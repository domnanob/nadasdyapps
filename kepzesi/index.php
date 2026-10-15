<?php
$permissionData = json_decode(
    file_get_contents(__DIR__ . '/api/permission/permission.json'),
    true
);
$games = [];
$catalogError = false;

try {
    require_once __DIR__ . '/includes/database.php';
    $conn = kepzesi_database_connection('kepzesi_games');
    $games = $conn->query('SELECT id, name, link, type, image_path FROM games ORDER BY sort_order, id')->fetchAll();
} catch (PDOException | RuntimeException $exception) {
    error_log('Game catalog database error: ' . $exception->getMessage());
    $catalogError = true;
}

$gamesJson = json_encode(
    array_column($games, 'link'),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
$gamesJson = $gamesJson === false ? '[]' : $gamesJson;
$gamesAllowed = is_array($permissionData) && ($permissionData['allow_games'] ?? false) === true;
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#17152d">
    <meta name="description" content="Válassz egy játékot a Nádasdy játékgyűjteményéből!">
    <link rel="icon" type="image/x-icon" href="./assets/brand/nadasdy-games.svg">
    <link rel="stylesheet" href="./assets/dist/css/app.css?v=20261015-1">
    <title>Nádasdy Játékok</title>
</head>

<body>
    <div class="page-shell">
        <header class="site-header">
            <a class="brand" href="./" aria-label="Nádasdy Játékok – főoldal">
                <img class="brand-logo" src="./assets/brand/nadasdy-games.svg" alt="">
                <span>Nádasdy<span class="brand-light"> Játékok</span></span>
            </a>
        </header>
        <main id="main" data-games-allowed="<?= $catalogError || $gamesAllowed ? 'true' : 'false' ?>">
            <?php if ($catalogError): ?>
                <section class="welcome-panel waiting-panel" aria-labelledby="welcome-title">
                    <div class="welcome-copy">
                        <p class="eyebrow"><span class="sparkle" aria-hidden="true">✦</span> NÁDASDY JÁTÉKELLOSZTÓ</p>
                        <h1 id="welcome-title">Még egy kis <span class="waiting-highlight">beállítás!</span></h1>
                        <p class="welcome-description">A játékkatalógus adatbázisa nem érhető el. Ellenőrizd a SQLite-adatbázis fájljának és mappájának jogosultságait, valamint hogy elérhető-e a PHP PDO SQLite bővítménye.</p>
                        <a class="play-hint setup-link" href="./admin/">Adminisztráció megnyitása <span aria-hidden="true">↗</span></a>
                    </div>
                    <div class="welcome-doodle doodle-one" aria-hidden="true">✳</div>
                    <div class="welcome-doodle doodle-two" aria-hidden="true">✦</div>
                </section>
            <?php elseif (!$gamesAllowed): ?>
                <section class="welcome-panel waiting-panel" aria-labelledby="welcome-title">
                    <div class="welcome-copy">
                        <p class="eyebrow"><span class="sparkle" aria-hidden="true">✦</span> NÁDASDY JÁTÉKELLOSZTÓ</p>
                        <h1 id="welcome-title">Hamarosan <span class="waiting-highlight">kezdünk!</span></h1>
                        <p class="welcome-description">Figyelj az előadásra, a játékok mindjárt elérhetőek lesznek.</p>
                        <div class="play-hint" id="permission-status"><span class="status-dot" aria-hidden="true"></span> Várjuk, hogy indulhasson a játék...</div>
                    </div>
                    <div class="welcome-doodle doodle-one" aria-hidden="true">✳</div>
                    <div class="welcome-doodle doodle-two" aria-hidden="true">✦</div>
                </section>
            <?php else: ?>
                <section class="welcome-panel" aria-labelledby="welcome-title">
                    <button id="fsBtn" class="fullscreen-button" type="button" aria-label="Teljes képernyő" title="Teljes képernyő">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3m13-5h3a2 2 0 0 1 2 2v3M3 16v3a2 2 0 0 0 2 2h3m13-5v3a2 2 0 0 1-2 2h-3"/>
                        </svg>
                    </button>
                    <div class="welcome-copy">
                        <p class="eyebrow"><span class="sparkle" aria-hidden="true">✦</span> NÁDASDY JÁTÉKELLOSZTÓ</p>
                        <h1 id="welcome-title">Válassz egy játékot<span class="title-dot">.</span></h1>
                        <p class="welcome-description">Próbáld ki a diákjaink által készített játékokat!</p>
                    </div>
                    <div class="welcome-doodle doodle-one" aria-hidden="true">✳</div>
                    <div class="welcome-doodle doodle-two" aria-hidden="true">✦</div>
                </section>

                <section class="games-section" aria-labelledby="games-title">
                    <div class="section-heading">
                        <div>
                            <p class="eyebrow section-eyebrow">Legyél te a legjobb a játékainkban</p>
                            <h2 id="games-title">Játékok <span>neked</span></h2>
                        </div>
                        <span class="game-count"><?= str_pad((string) count($games), 2, '0', STR_PAD_LEFT) ?> JÁTÉK</span>
                    </div>
                    <?php if ($games): ?>
                        <div class="game-grid">
                            <?php foreach ($games as $index => $game): ?>
                                <a class="game-card" href="<?= $escape($game['link']) ?>">
                                    <span class="game-art"><img src="<?= $escape($game['image_path']) ?>" alt="" loading="lazy"></span>
                                    <span class="game-info">
                                        <span class="game-category"><?= $escape($game['type']) ?></span>
                                        <span class="game-title"><?= $escape($game['name']) ?></span>
                                        <span class="game-action">Játék indítása <span aria-hidden="true">↗</span></span>
                                    </span>
                                    <span class="game-number" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="empty-games">Még nincs játék hozzáadva. Nyisd meg az adminisztrációt, és adj hozzá egyet!</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </main>
        <footer class="site-footer">
            <span>Jó játékot kívánunk!</span>
            <span>© 2026 NTSZKI</span>
        </footer>
    </div>
    <script id="game-links" type="application/json"><?= $gamesJson ?></script>
    <script src="./assets/dist/js/script.js" defer></script>
</body>

</html>
