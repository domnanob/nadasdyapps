<?php
session_start();

if (!isset($_SESSION['game_admin_csrf'])) {
    $_SESSION['game_admin_csrf'] = bin2hex(random_bytes(32));
}

$games = [];
$errors = [];
$success = isset($_GET['added']);
$formName = '';
$formLink = '';
$formType = '';
$conn = null;
$databaseUnavailable = false;

try {
    require_once __DIR__ . '/../includes/database.php';
    $conn = kepzesi_database_connection('kepzesi_games');
    $games = $conn->query('SELECT id, name, link, type, image_path FROM games ORDER BY sort_order, id')->fetchAll();
} catch (PDOException | RuntimeException $exception) {
    error_log('Game admin database error: ' . $exception->getMessage());
    $databaseUnavailable = true;
    $errors[] = 'Nem érhető el a játékkatalógus. Ellenőrizd a SQLite-adatbázis fájljának és mappájának jogosultságait, valamint hogy elérhető-e a PHP PDO SQLite bővítménye.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$databaseUnavailable) {
    $formName = trim((string) ($_POST['name'] ?? ''));
    $formLink = trim((string) ($_POST['link'] ?? ''));
    $formType = trim((string) ($_POST['type'] ?? ''));
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['game_admin_csrf'], $csrfToken)) {
        $errors[] = 'A beküldött űrlap lejárt. Frissítsd az oldalt, majd próbáld újra.';
    }
    if ($formName === '' || mb_strlen($formName) > 100) {
        $errors[] = 'A játék neve kötelező, és legfeljebb 100 karakter lehet.';
    }
    if ($formType === '' || mb_strlen($formType) > 60) {
        $errors[] = 'A játéktípus kötelező, és legfeljebb 60 karakter lehet.';
    }

    $isExternalLink = filter_var($formLink, FILTER_VALIDATE_URL) !== false;
    if ($isExternalLink) {
        $linkScheme = strtolower((string) parse_url($formLink, PHP_URL_SCHEME));
        if (!in_array($linkScheme, ['http', 'https'], true)) {
            $errors[] = 'A külső hivatkozás csak HTTP vagy HTTPS cím lehet.';
        }
    } elseif (
        $formLink === ''
        || preg_match('/[\x00-\x20\\\\]/', $formLink)
        || !preg_match('/^(?:\.\/|\/(?!\/))/', $formLink)
        || preg_match('~(?:^|/)\.\.(?:/|$)~', (string) parse_url($formLink, PHP_URL_PATH))
    ) {
        $errors[] = 'Adj meg egy ./ kezdetű, webhelyen belüli útvonalat vagy teljes HTTP(S) URL-t.';
    }

    $image = $_FILES['image'] ?? null;
    if (!$image || $image['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'A játék képének feltöltése kötelező.';
    } elseif ($image['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'A képfeltöltés nem sikerült. Próbáld újra egy legfeljebb 5 MB-os képpel.';
    } elseif ($image['size'] > 5 * 1024 * 1024) {
        $errors[] = 'A kép legfeljebb 5 MB méretű lehet.';
    } elseif (!class_exists('finfo')) {
        error_log('Game image upload requires the PHP Fileinfo extension.');
        $errors[] = 'A képfeltöltéshez engedélyezni kell a PHP Fileinfo bővítményt.';
    } else {
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($image['tmp_name']);
        $allowedImageTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $imageDimensions = @getimagesize($image['tmp_name']);

        if (
            !isset($allowedImageTypes[$mimeType])
            || $imageDimensions === false
            || $imageDimensions[0] > 8000
            || $imageDimensions[1] > 8000
        ) {
            $errors[] = 'Csak érvényes JPG, PNG vagy WebP kép tölthető fel (legfeljebb 8000 × 8000 képpont).';
        }
    }

    if (!$errors) {
        $uploadDirectory = __DIR__ . '/../assets/uploads/games';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            $errors[] = 'A képek feltöltési mappája nem hozható létre. Ellenőrizd a mappa jogosultságait.';
        } else {
            $filename = bin2hex(random_bytes(16)) . '.' . $allowedImageTypes[$mimeType];
            $destination = $uploadDirectory . '/' . $filename;
            $imagePath = './assets/uploads/games/' . $filename;

            if (!move_uploaded_file($image['tmp_name'], $destination)) {
                $errors[] = 'A feltöltött kép nem menthető el. Ellenőrizd a mappa jogosultságait.';
            } else {
                try {
                    $statement = $conn->prepare('INSERT INTO games (name, link, type, image_path, sort_order) VALUES (?, ?, ?, ?, ?)');
                    $nextOrder = count($games) * 10 + 10;
                    $statement->execute([$formName, $formLink, $formType, $imagePath, $nextOrder]);
                    header('Location: ./?added=1', true, 303);
                    exit;
                } catch (PDOException $exception) {
                    unlink($destination);
                    error_log('Game insert failed: ' . $exception->getMessage());
                    $errors[] = 'A játék nem menthető az adatbázisba. Ellenőrizd az adatbázis fájljának jogosultságait, majd próbáld újra.';
                }
            }
        }
    }
}

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#17152d">
    <link rel="stylesheet" href="../assets/dist/css/app.css">
    <title>Játékok kezelése · Nádasdy Játékok</title>
</head>

<body>
    <div class="page-shell admin-shell">
        <header class="site-header">
            <a class="brand" href="../" aria-label="Vissza a Nádasdy Játékok főoldalára">
                <span class="brand-mark" aria-hidden="true">N</span>
                <span>Nádasdy<span class="brand-light"> Játékok</span></span>
            </a>
            <a class="admin-link" href="../">Vissza a játékokhoz <span aria-hidden="true">↗</span></a>
        </header>

        <main class="admin-main">
            <section class="admin-intro">
                <p class="eyebrow"><span class="sparkle" aria-hidden="true">✦</span> JÁTÉKELLOSZTÓ</p>
                <h1>Játékok <span>kezelése</span></h1>
                <p>Adj hozzá új játékot, és rögtön megjelenik a főoldal választékában.</p>
            </section>

            <div class="admin-columns">
                <section class="admin-panel" aria-labelledby="add-game-title">
                    <div class="admin-panel-heading">
                        <span class="admin-panel-icon" aria-hidden="true">＋</span>
                        <div>
                            <p class="eyebrow section-eyebrow">ÚJ KEDVENC</p>
                            <h2 id="add-game-title">Játék hozzáadása</h2>
                        </div>
                    </div>

                    <?php if ($success): ?>
                        <div class="form-message form-success" role="status">A játék sikeresen hozzáadva a főoldalhoz.</div>
                    <?php endif; ?>
                    <?php if ($errors): ?>
                        <div class="form-message form-error" role="alert">
                            <?php foreach ($errors as $error): ?>
                                <p><?= $escape($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!$databaseUnavailable): ?>
                        <form class="game-form" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['game_admin_csrf']) ?>">
                            <div class="form-field">
                                <label for="game-name">Játék neve</label>
                                <input id="game-name" name="name" type="text" maxlength="100" required value="<?= $escape($formName) ?>" placeholder="Például: Memória mester">
                            </div>
                            <div class="form-field">
                                <label for="game-link">Játék hivatkozása</label>
                                <input id="game-link" name="link" type="text" maxlength="2048" required value="<?= $escape($formLink) ?>" placeholder="./uj-jatek/ vagy https://pelda.hu">
                                <small>Helyi játékhoz használd a ./mappa/ formátumot.</small>
                            </div>
                            <div class="form-field">
                                <label for="game-type">Típus / kategória</label>
                                <input id="game-type" name="type" type="text" maxlength="60" required value="<?= $escape($formType) ?>" placeholder="Például: Logika · Fejtörő">
                            </div>
                            <div class="form-field">
                                <label for="game-image">Borítókép</label>
                                <input id="game-image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required>
                                <small>JPG, PNG vagy WebP · legfeljebb 5 MB és 8000 × 8000 képpont.</small>
                            </div>
                            <button class="admin-submit" type="submit">Játék hozzáadása <span aria-hidden="true">↗</span></button>
                        </form>
                    <?php endif; ?>
                </section>

                <section class="admin-panel catalog-panel" aria-labelledby="catalog-title">
                    <div class="admin-panel-heading">
                        <span class="admin-panel-icon catalog-icon" aria-hidden="true"><?= count($games) ?></span>
                        <div>
                            <p class="eyebrow section-eyebrow">JELENLEGI VÁLASZTÉK</p>
                            <h2 id="catalog-title">A játéklistád</h2>
                        </div>
                    </div>
                    <?php if ($games): ?>
                        <ul class="admin-game-list">
                            <?php foreach ($games as $game): ?>
                                <li class="admin-game-item">
                                    <img src="../<?= $escape(ltrim($game['image_path'], './')) ?>" alt="" loading="lazy">
                                    <span class="admin-game-details">
                                        <span class="admin-game-name"><?= $escape($game['name']) ?></span>
                                        <span class="admin-game-type"><?= $escape($game['type']) ?></span>
                                        <span class="admin-game-link"><?= $escape($game['link']) ?></span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="empty-games">Még üres a listád. Töltsd ki az űrlapot egy játék hozzáadásához.</p>
                    <?php endif; ?>
                    <p class="admin-security-note"><span aria-hidden="true">ⓘ</span> Ez a kezelőoldal nem kér bejelentkezést. Csak olyan hálózaton használd, ahol a webhely és ez az oldal megbízható felhasználók számára érhető el.</p>
                </section>
            </div>
        </main>

        <footer class="site-footer">
            <span>Jó játékot kívánunk!</span>
            <span>© 2026 NTSZKI</span>
        </footer>
    </div>
</body>

</html>
