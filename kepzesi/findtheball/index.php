<?php
require_once __DIR__ . '/../includes/database.php';

function send_json(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function get_top_scores(PDO $connection): array
{
    return $connection->query(
        'SELECT player_name, score FROM findtheball_scores ORDER BY score DESC, id ASC LIMIT 10'
    )->fetchAll();
}

if (isset($_GET['action'])) {
    $connection = null;
    $transactionStarted = false;
    try {
        $connection = kepzesi_database_connection('findtheball');

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'scores') {
            send_json(['scores' => get_top_scores($connection)]);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'submit-score') {
            $input = json_decode(file_get_contents('php://input'), true);
            $playerName = trim((string) ($input['name'] ?? ''));
            $score = filter_var($input['score'] ?? null, FILTER_VALIDATE_INT);

            if ($playerName === '' || mb_strlen($playerName) > 50 || preg_match('/[\x00-\x1F\x7F]/u', $playerName)) {
                send_json(['error' => 'Adj meg egy nevet (legfeljebb 50 karakter).'], 422);
            }
            if ($score === false || $score < 1 || $score > 100000) {
                send_json(['error' => 'Érvénytelen pontszám.'], 422);
            }

            $connection->begin_transaction();
            $transactionStarted = true;
            $lowestScore = $connection->query(
                'SELECT score FROM findtheball_scores ORDER BY score ASC, id DESC LIMIT 1'
            )->fetchColumn();
            if ($lowestScore !== false && $score <= (int) $lowestScore) {
                $connection->rollback();
                $transactionStarted = false;
                send_json([
                    'saved' => false,
                    'scores' => get_top_scores($connection),
                ]);
            }

            $statement = $connection->prepare('INSERT INTO findtheball_scores (player_name, score) VALUES (?, ?)');
            $statement->execute([$playerName, $score]);

            $connection->query(
                'DELETE FROM findtheball_scores WHERE id NOT IN ' .
                '(SELECT id FROM (SELECT id FROM findtheball_scores ORDER BY score DESC, id ASC LIMIT 10) AS top_scores)'
            );
            $connection->commit();
            $transactionStarted = false;

            send_json([
                'saved' => true,
                'scores' => get_top_scores($connection),
            ]);
        }

        send_json(['error' => 'Ismeretlen kérés.'], 404);
    } catch (Throwable $exception) {
        if ($transactionStarted && $connection instanceof PDO && $connection->inTransaction()) {
            $connection->rollBack();
        }
        error_log('Find the Ball request failed: ' . $exception->getMessage());
        send_json(['error' => 'Az eredménytábla most nem érhető el. Indítsd újra a játékot, vagy próbáld később.'], 500);
    }
}

$databaseError = null;
$scores = [];
try {
    $connection = kepzesi_database_connection('findtheball');
    $scores = get_top_scores($connection);
} catch (Throwable $exception) {
    error_log('Find the Ball leaderboard unavailable: ' . $exception->getMessage());
    $databaseError = 'A ranglista-adatbázis nem érhető el. Ellenőrizd a SQLite-adatbázis fájljának és mappájának jogosultságait, majd próbáld újra.';
}

$scoresJson = json_encode($scores, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10211e">
    <title>Hol a labda? · Nádasdy Játékok</title>
    <link rel="stylesheet" href="./style.css?v=20261015-2">
</head>
<body>
    <div class="game-page">
        <header class="game-header">
            <a class="back-link" href="../" aria-label="Vissza a játékokhoz">← <span>Játékok</span></a>
            <a class="game-brand" href="../">
                <span>Nádasdy <strong>Játékok</strong></span>
            </a>
            <span class="header-tag">FIGYELJ JÓL <span aria-hidden="true">✦</span></span>
        </header>

        <main class="game-layout" id="game" data-database-ready="<?= $databaseError === null ? 'true' : 'false' ?>">
            <section class="play-panel" aria-labelledby="game-title">
                <div class="game-intro">
                    <p class="eyebrow">SZEMFÜLESSÉG · MEMÓRIA</p>
                    <h1 id="game-title">Hol a <span>labda?</span></h1>
                    <p class="game-copy">Kövesd a labdás poharat, aztán bökj rá, amikor megáll a keverés!</p>
                </div>

                <div class="game-stats" aria-live="polite">
                    <div class="stat-box">
                        <span class="stat-label">PONTSZÁM</span>
                        <strong id="score">0</strong>
                    </div>
                    <div class="stat-box stat-level">
                        <span class="stat-label">POHARAK</span>
                        <strong id="cup-count">3</strong>
                    </div>
                    <div class="stat-box stat-best">
                        <span class="stat-label">LEGJOBB KÖR</span>
                        <strong id="best-score"><?= isset($scores[0]['score']) ? (int) $scores[0]['score'] : 0 ?></strong>
                    </div>
                </div>

                <div class="stage-shell">
                    <div class="stage-message" id="stage-message" role="status">
                        <?php if ($databaseError !== null): ?>
                            <?= htmlspecialchars($databaseError, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        <?php else: ?>
                            Nyomd meg az indítást, és figyeld a labdát!
                        <?php endif; ?>
                    </div>
                    <div class="cup-stage" id="cup-stage" aria-label="Poharak"></div>
                    <div class="stage-caption" id="stage-caption">Kezdéskor mutatjuk, hová kerül a labda.</div>
                </div>

                <div class="game-controls">
                    <button class="primary-button" id="start-button" type="button" <?= $databaseError !== null ? 'disabled' : '' ?>>
                        <span aria-hidden="true">▶</span> <span>Játék indítása</span>
                    </button>
                    <p class="control-hint" id="control-hint">Minden jó tipp után egy újabb pohár kerül a játékba!</p>
                </div>
            </section>

            <aside class="leaderboard-panel" aria-labelledby="leaderboard-title">
                <div class="leaderboard-heading">
                    <span class="trophy" aria-hidden="true">🏆</span>
                    <div>
                        <p class="eyebrow">A LEGJOBBAK</p>
                        <h2 id="leaderboard-title">Top 10</h2>
                    </div>
                </div>
                <p class="leaderboard-subtitle">Ki követi legjobban a labdát?</p>
                <ol class="score-list" id="score-list">
                    <?php foreach ($scores as $index => $entry): ?>
                        <li>
                            <span class="rank"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                            <span class="score-name"><?= htmlspecialchars($entry['player_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                            <strong class="score-value"><?= (int) $entry['score'] ?></strong>
                        </li>
                    <?php endforeach; ?>
                    <?php if (!$scores && $databaseError === null): ?>
                        <li class="empty-score">Még nincs eredmény — te lehetsz az első!</li>
                    <?php endif; ?>
                </ol>
                <div class="leaderboard-note"><span aria-hidden="true">✦</span> Minden pont egy új poharat jelent.</div>
            </aside>
        </main>

        <footer class="game-footer">Kövesd a poharat. Bízz a szemedben. ✦</footer>
    </div>

    <dialog class="result-dialog" id="result-dialog" aria-labelledby="result-title">
        <form method="dialog" class="dialog-close-wrap"><button class="dialog-close" aria-label="Bezárás">×</button></form>
        <div class="result-icon" id="result-icon" aria-hidden="true">🎯</div>
        <p class="eyebrow" id="result-eyebrow">SZÉP VOLT!</p>
        <h2 id="result-title">Jól követted!</h2>
        <p class="result-description" id="result-description"></p>
        <form id="score-form" class="score-form" hidden>
            <label for="player-name">A neved felkerül a ranglistára</label>
            <input id="player-name" name="name" maxlength="50" autocomplete="nickname" required placeholder="Írd be a neved">
            <button class="primary-button" type="submit">Eredmény mentése <span aria-hidden="true">↗</span></button>
        </form>
        <p class="save-message" id="save-message" role="status"></p>
        <button class="secondary-button" id="play-again" type="button">Új kör</button>
    </dialog>

    <script id="initial-scores" type="application/json"><?= $scoresJson ?: '[]' ?></script>
    <script src="./game.js" defer></script>
</body>
</html>
