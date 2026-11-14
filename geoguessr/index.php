<?php
declare(strict_types=1);

require __DIR__ . '/db.php';
require __DIR__ . '/partials.php';

send_security_headers();
boot_session();

$errors = [];
$old = ['full_name' => '', 'class_name' => '', 'game_rank' => ''];
$open = registration_open();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'full_name'  => (string) ($_POST['full_name'] ?? ''),
        'class_name' => (string) ($_POST['class_name'] ?? ''),
        'game_rank'  => (string) ($_POST['game_rank'] ?? ''),
    ];

    if (!csrf_ok()) {
        $errors[] = 'Az űrlap lejárt. Töltsd újra az oldalt, és küldd el még egyszer.';
    } elseif (!empty($_POST['website'])) {
        // Honeypot: a robotok kitöltik, az emberek nem látják. Csendben eldobjuk.
        redirect('index.php');
    } elseif (!$open) {
        $errors[] = 'A jelentkezés jelenleg le van zárva.';
    } else {
        [$clean, $errors] = validate_registration($old);
        if (!$errors) {
            try {
                insert_registration($clean);
                $_SESSION['done'] = ['name' => $clean['full_name'], 'class' => $clean['class_name']];
                redirect('index.php');
            } catch (PDOException $ex) {
                if ($ex->getCode() === '23000') {
                    // Két egyszerre érkező azonos jelentkezés: az egyedi index elkapja.
                    $errors[] = 'Ezzel a névvel és osztállyal már van jelentkezés.';
                } else {
                    error_log((string) $ex);
                    $errors[] = 'A mentés nem sikerült. Szólj a szervezőknek.';
                }
            }
        }
    }
}

$done = $_SESSION['done'] ?? null;
unset($_SESSION['done']);

render_head('Nádasdy GeoGuessr Cup ’26 – Jelentkezés');
render_streaks();
?>
<main class="page">
    <header class="hero">
        <p class="brand">
            <img class="brand-crest" src="assets/school-crest.png" width="104" height="104" alt="Nádasdy Tamás Technikum és Kollégium">
            <img class="brand-x" src="assets/x.png" width="47" height="40" alt="×">
            <img class="brand-gg" src="assets/geoguessr.png" width="253" height="50" alt="GeoGuessr">
        </p>

        <?php render_crest(); ?>

        <h1 class="slogan">Felismered<br>hol vagy?</h1>
        <p class="lead">Jelentkezz az első Nádasdy GeoGuessr kupára!</p>
        <p class="when"><?= e(cfg('event_date')) ?> • <?= e(cfg('event_time')) ?></p>
    </header>

    <?php if ($done): ?>
        <section class="panel done" aria-live="polite">
            <h2>Köszönjük, <?= e($done['name']) ?>!</h2>
            <p>A jelentkezésedet rögzítettük (<?= e($done['class']) ?>). Találkozunk <?= e(cfg('event_date')) ?>-án <?= e(cfg('event_time')) ?>-kor.</p>
            <a class="btn btn-ghost" href="index.php">Másik jelentkezés</a>
        </section>
    <?php elseif (!$open): ?>
        <section class="panel done">
            <h2>A jelentkezés lezárult</h2>
            <p>Új jelentkezést már nem fogadunk. Ha lemaradtál, kérdezd a szervezőket.</p>
        </section>
    <?php else: ?>
        <section class="panel" id="jelentkezes">
            <h2>Jelentkezés</h2>

            <?php if ($errors): ?>
                <div class="alert" role="alert">
                    <ul>
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php" novalidate>
                <?= csrf_field() ?>
                <div class="hp" aria-hidden="true">
                    <label>Ezt hagyd üresen <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="field">
                    <label for="full_name">Teljes neved</label>
                    <input type="text" id="full_name" name="full_name" required maxlength="100"
                           autocomplete="name" placeholder="pl. Kovács Anna"
                           value="<?= e($old['full_name']) ?>">
                </div>

                <div class="field">
                    <label for="class_name">Osztályod</label>
                    <select id="class_name" name="class_name" required>
                        <option value="" disabled <?= $old['class_name'] === '' ? 'selected' : '' ?>>Válassz osztályt…</option>
                        <?php foreach (cfg('classes') as $c): ?>
                            <option value="<?= e($c) ?>" <?= $old['class_name'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="game_rank">Rangod a GeoGuessrben <span class="opt">nem kötelező</span></label>
                    <select id="game_rank" name="game_rank">
                        <option value="">Nincs / nem tudom</option>
                        <?php foreach (cfg('ranks') as $r): ?>
                            <option value="<?= e($r) ?>" <?= $old['game_rank'] === $r ? 'selected' : '' ?>><?= e($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Jelentkezem</button>
            </form>
        </section>
    <?php endif; ?>

    <footer class="foot">Nádasdy GeoGuessr Cup ’26</footer>
</main>
</body>
</html>
