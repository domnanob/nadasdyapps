<?php
declare(strict_types=1);

require __DIR__ . '/db.php';
require __DIR__ . '/partials.php';

send_security_headers();
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
boot_session();

const ADMIN_SESSION_TTL = 7200;   // 2 óra tétlenség után kilépteti
const MAX_FAILED_LOGINS = 5;      // ennyi hibás próba / 15 perc / IP
const LOCK_WINDOW       = 900;

$pdo  = db();
$hash = (string) cfg('admin_password_hash');
$ip   = client_ip();

function admin_url(array $params = []): string
{
    $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return 'admin.php' . ($params ? '?' . http_build_query($params) : '');
}

$logged = !empty($_SESSION['admin']) && (int) ($_SESSION['admin_seen'] ?? 0) > time() - ADMIN_SESSION_TTL;
if ($logged) {
    $_SESSION['admin_seen'] = time();
} else {
    unset($_SESSION['admin'], $_SESSION['admin_seen']);
}

$loginError = '';
$editErrors = [];
$editPosted = null;

/* ---------- POST műveletek ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $back = admin_url([
        'q'   => (string) ($_POST['q'] ?? ''),
        'cls' => (string) ($_POST['cls'] ?? ''),
        'st'  => (string) ($_POST['st'] ?? ''),
    ]);

    if (!csrf_ok()) {
        if ($action === 'login') {
            $loginError = 'Az űrlap lejárt, próbáld újra.';
        } else {
            flash_set('error', 'Az űrlap lejárt, próbáld újra.');
            redirect($logged ? $back : admin_url());
        }
    } elseif ($action === 'login' && !$logged) {
        if ($hash === '') {
            $loginError = 'Az admin jelszó még nincs beállítva.';
        } else {
            $pdo->prepare('DELETE FROM login_attempts WHERE attempted_at < ?')->execute([time() - LOCK_WINDOW]);
            $st = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at >= ?');
            $st->execute([$ip, time() - LOCK_WINDOW]);

            if ((int) $st->fetchColumn() >= MAX_FAILED_LOGINS) {
                $loginError = 'Túl sok hibás próba. Várj 15 percet, és próbáld újra.';
            } elseif (password_verify((string) ($_POST['password'] ?? ''), $hash)) {
                session_regenerate_id(true);
                $_SESSION['admin'] = true;
                $_SESSION['admin_seen'] = time();
                $pdo->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([$ip]);
                redirect(admin_url());
            } else {
                $pdo->prepare('INSERT INTO login_attempts (ip, attempted_at) VALUES (?, ?)')->execute([$ip, time()]);
                usleep(700000);
                $loginError = 'Hibás jelszó.';
            }
        }
    } elseif ($logged) {
        $id = (int) ($_POST['id'] ?? 0);

        switch ($action) {
            case 'logout':
                $_SESSION = [];
                session_destroy();
                redirect(admin_url());

            case 'toggle_open':
                $open = !registration_open();
                set_setting('registration_open', $open ? '1' : '0');
                flash_set('ok', $open ? 'A jelentkezés megnyitva.' : 'A jelentkezés lezárva.');
                redirect($back);

            case 'toggle_confirm':
                $pdo->prepare('UPDATE registrations SET confirmed = 1 - confirmed WHERE id = ?')->execute([$id]);
                redirect($back);

            case 'delete':
                $pdo->prepare('DELETE FROM registrations WHERE id = ?')->execute([$id]);
                flash_set('ok', 'A jelentkezést töröltük.');
                redirect($back);

            case 'update':
                [$clean, $editErrors] = validate_registration($_POST, $id);
                if (!$editErrors) {
                    try {
                        $pdo->prepare(
                            'UPDATE registrations
                             SET full_name = :full_name, name_key = :name_key,
                                 class_name = :class_name, game_rank = :game_rank, confirmed = :confirmed
                             WHERE id = :id'
                        )->execute($clean + ['confirmed' => empty($_POST['confirmed']) ? 0 : 1, 'id' => $id]);
                        flash_set('ok', 'A módosítás mentve.');
                        redirect($back);
                    } catch (PDOException $ex) {
                        $editErrors[] = $ex->getCode() === '23000'
                            ? 'Ezzel a névvel és osztállyal már van jelentkezés.'
                            : 'A mentés nem sikerült: ' . $ex->getMessage();
                    }
                }
                $editPosted = $_POST;
                $_GET['edit'] = (string) $id;
                break;
        }
    }
}

$flash = flash_get();

/* ---------- Belépés oldal ---------- */
if (!$logged) {
    render_head('Belépés', true);
    render_streaks();
    ?>
    <main class="page page-narrow">
        <section class="panel">
            <h1 class="panel-title">Admin belépés</h1>
            <?php if ($hash === ''): ?>
                <div class="alert" role="alert">
                    <p>Az admin jelszó még nincs beállítva. Generálj hash-t a parancssorban, majd másold a <code>config.php</code> fájl <code>admin_password_hash</code> értékéhez:</p>
                    <p><code>php -r "echo password_hash('JELSZÓ', PASSWORD_DEFAULT);"</code></p>
                </div>
            <?php else: ?>
                <?php if ($loginError): ?><div class="alert" role="alert"><?= e($loginError) ?></div><?php endif; ?>
                <form method="post" action="admin.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="login">
                    <div class="field">
                        <label for="password">Jelszó</label>
                        <input type="password" id="password" name="password" required autofocus autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary">Belépés</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
    </body></html>
    <?php
    exit;
}

/* ---------- CSV export ---------- */
if (isset($_GET['export'])) {
    $rows = $pdo->query('SELECT id, full_name, class_name, game_rank, confirmed, created_at FROM registrations ORDER BY class_name, full_name')->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="nadasdy-cup-jelentkezok-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM, hogy az Excel jól olvassa az ékezeteket
    $safe = fn($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : (string) $v; // képletinjektálás ellen
    fputcsv($out, ['Sorszám', 'Név', 'Osztály', 'Rang', 'Megerősítve', 'Jelentkezés ideje'], ';', '"', '');
    foreach ($rows as $r) {
        fputcsv($out, [$r['id'], $safe($r['full_name']), $r['class_name'], $r['game_rank'] ?? '', $r['confirmed'] ? 'igen' : 'nem', $r['created_at']], ';', '"', '');
    }
    exit;
}

/* ---------- Szűrők, lista ---------- */
$q   = trim((string) ($_GET['q'] ?? ''));
$cls = (string) ($_GET['cls'] ?? '');
$stf = (string) ($_GET['st'] ?? '');

$where = [];
$args  = [];
if ($q !== '') {
    $where[] = "name_key LIKE ? ESCAPE '\\'";
    $args[]  = '%' . addcslashes(mb_strtolower($q), '%_\\') . '%';
}
if ($cls !== '') {
    $where[] = 'class_name = ?';
    $args[]  = $cls;
}
if ($stf === 'ok') {
    $where[] = 'confirmed = 1';
} elseif ($stf === 'new') {
    $where[] = 'confirmed = 0';
}

$st = $pdo->prepare('SELECT * FROM registrations' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC, id DESC');
$st->execute($args);
$rows = $st->fetchAll();

$total     = (int) $pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
$confirmed = (int) $pdo->query('SELECT COUNT(*) FROM registrations WHERE confirmed = 1')->fetchColumn();
$perClass  = $pdo->query('SELECT class_name, COUNT(*) AS c FROM registrations GROUP BY class_name')->fetchAll(PDO::FETCH_KEY_PAIR);
$open      = registration_open();

function ctx_fields(string $q, string $cls, string $stf): string
{
    return '<input type="hidden" name="q" value="' . e($q) . '">'
         . '<input type="hidden" name="cls" value="' . e($cls) . '">'
         . '<input type="hidden" name="st" value="' . e($stf) . '">';
}

$editId  = (int) ($_GET['edit'] ?? 0);
$editRow = null;
if ($editId > 0) {
    $s = $pdo->prepare('SELECT * FROM registrations WHERE id = ?');
    $s->execute([$editId]);
    $editRow = $s->fetch();
    if (!$editRow) {
        redirect(admin_url());
    }
}

render_head('Jelentkezők – Admin', true);
?>
<main class="admin">
    <header class="admin-top">
        <h1>Jelentkezők</h1>
        <div class="admin-actions">
            <form method="post" action="admin.php">
                <?= csrf_field() ?><?= ctx_fields($q, $cls, $stf) ?>
                <input type="hidden" name="action" value="toggle_open">
                <button class="btn btn-ghost" type="submit"><?= $open ? 'Jelentkezés lezárása' : 'Jelentkezés megnyitása' ?></button>
            </form>
            <a class="btn btn-ghost" href="admin.php?export=1">Letöltés CSV-ben</a>
            <form method="post" action="admin.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="logout">
                <button class="btn btn-ghost" type="submit">Kilépés</button>
            </form>
        </div>
    </header>

    <?php if ($flash): ?>
        <div class="alert <?= $flash['type'] === 'ok' ? 'alert-ok' : '' ?>" role="status"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <p class="stats">
        <span><strong><?= $total ?></strong> jelentkező</span>
        <span><strong><?= $confirmed ?></strong> megerősítve</span>
        <span><strong><?= $total - $confirmed ?></strong> megerősítésre vár</span>
        <span class="state <?= $open ? 'state-open' : 'state-closed' ?>"><?= $open ? 'A jelentkezés nyitva van' : 'A jelentkezés le van zárva' ?></span>
    </p>

    <?php
    /* ---------- Szerkesztő nézet ---------- */
    if ($editRow):
        $v = $editPosted ?? [
            'full_name' => $editRow['full_name'], 'class_name' => $editRow['class_name'],
            'game_rank' => (string) $editRow['game_rank'], 'confirmed' => $editRow['confirmed'],
        ];
    ?>
        <section class="panel panel-edit">
            <h2>Jelentkező szerkesztése</h2>
            <?php if ($editErrors): ?>
                <div class="alert" role="alert"><ul><?php foreach ($editErrors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>
            <form method="post" action="admin.php">
                <?= csrf_field() ?><?= ctx_fields($q, $cls, $stf) ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $editId ?>">
                <div class="field">
                    <label for="full_name">Teljes név</label>
                    <input type="text" id="full_name" name="full_name" maxlength="100" required value="<?= e($v['full_name']) ?>">
                </div>
                <div class="field">
                    <label for="class_name">Osztály</label>
                    <select id="class_name" name="class_name" required>
                        <?php foreach (cfg('classes') as $c): ?>
                            <option value="<?= e($c) ?>" <?= $v['class_name'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="game_rank">Rang <span class="opt">nem kötelező</span></label>
                    <select id="game_rank" name="game_rank">
                        <option value="">Nincs / nem tudom</option>
                        <?php foreach (cfg('ranks') as $r): ?>
                            <option value="<?= e($r) ?>" <?= (string) $v['game_rank'] === $r ? 'selected' : '' ?>><?= e($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <label class="check">
                    <input type="checkbox" name="confirmed" value="1" <?= !empty($v['confirmed']) ? 'checked' : '' ?>>
                    Megerősítve
                </label>
                <div class="row-actions">
                    <button type="submit" class="btn btn-primary">Mentés</button>
                    <a class="btn btn-ghost" href="<?= e($back = admin_url(['q' => $q, 'cls' => $cls, 'st' => $stf])) ?>">Mégse</a>
                </div>
            </form>
        </section>
    <?php else: ?>

        <form class="toolbar" method="get" action="admin.php">
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Keresés névre" aria-label="Keresés névre">
            <select name="cls" aria-label="Osztály szűrő">
                <option value="">Minden osztály</option>
                <?php foreach (cfg('classes') as $c): ?>
                    <option value="<?= e($c) ?>" <?= $cls === $c ? 'selected' : '' ?>><?= e($c) ?><?= isset($perClass[$c]) ? ' (' . $perClass[$c] . ')' : '' ?></option>
                <?php endforeach; ?>
            </select>
            <select name="st" aria-label="Állapot szűrő">
                <option value="">Minden állapot</option>
                <option value="new" <?= $stf === 'new' ? 'selected' : '' ?>>Megerősítésre vár</option>
                <option value="ok" <?= $stf === 'ok' ? 'selected' : '' ?>>Megerősítve</option>
            </select>
            <button class="btn btn-primary" type="submit">Szűrés</button>
            <?php if ($q !== '' || $cls !== '' || $stf !== ''): ?><a class="btn btn-ghost" href="admin.php">Szűrők törlése</a><?php endif; ?>
        </form>

        <?php if (!$rows): ?>
            <p class="empty"><?= $total === 0 ? 'Még nincs jelentkező. Az első jelentkezés itt jelenik meg.' : 'Nincs a szűrésnek megfelelő jelentkező.' ?></p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Név</th><th>Osztály</th><th>Rang</th><th>Jelentkezett</th><th>Állapot</th><th><span class="sr-only">Műveletek</span></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td class="name"><?= e($r['full_name']) ?></td>
                            <td><?= e($r['class_name']) ?></td>
                            <td><?= $r['game_rank'] ? e($r['game_rank']) : '<span class="muted">–</span>' ?></td>
                            <td class="muted"><?= e(str_replace('-', '.', substr($r['created_at'], 0, 16))) ?></td>
                            <td>
                                <form method="post" action="admin.php">
                                    <?= csrf_field() ?><?= ctx_fields($q, $cls, $stf) ?>
                                    <input type="hidden" name="action" value="toggle_confirm">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button type="submit" class="badge <?= $r['confirmed'] ? 'badge-ok' : 'badge-new' ?>"
                                            title="Kattints az állapot váltásához"><?= $r['confirmed'] ? 'Megerősítve' : 'Vár' ?></button>
                                </form>
                            </td>
                            <td class="row-actions">
                                <a class="btn btn-small btn-ghost" href="<?= e(admin_url(['edit' => $r['id'], 'q' => $q, 'cls' => $cls, 'st' => $stf])) ?>">Szerkesztés</a>
                                <form method="post" action="admin.php" data-confirm="Biztosan törlöd ezt a jelentkezést: <?= e($r['full_name']) ?>?">
                                    <?= csrf_field() ?><?= ctx_fields($q, $cls, $stf) ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Törlés</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="muted count-note"><?= count($rows) ?> találat</p>
        <?php endif; ?>
    <?php endif; ?>
</main>
<script src="assets/admin.js"></script>
</body>
</html>
