<?php
require_once __DIR__ . '/../../includes/database.php';
$conn = kepzesi_database_connection('simon_says');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (string) ($_POST['username'] ?? '');
    $score = filter_var($_POST['score'] ?? null, FILTER_VALIDATE_INT);
    if ($username === '' || $score === false || $score < 0) {
        http_response_code(422);
        exit('Invalid username or score.');
    }

    $statement = $conn->prepare(
        'INSERT INTO simonsays_leaderboard (username, score) VALUES (?, ?)
         ON CONFLICT(username) DO UPDATE SET score = MAX(score, excluded.score)'
    );
    $statement->execute([$username, $score]);

    $sql_trim = "
        DELETE FROM simonsays_leaderboard
        WHERE id NOT IN (
            SELECT id FROM (
                SELECT id FROM simonsays_leaderboard
                ORDER BY score DESC
                LIMIT 10
            ) AS t
        )";
    $conn->exec($sql_trim);
    echo "Leaderboard trimmed to top 10.";
} else {
    echo "Invalid request method.";
}
