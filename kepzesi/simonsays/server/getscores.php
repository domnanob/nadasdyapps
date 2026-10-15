<?php

require_once __DIR__ . '/../../includes/database.php';
$conn = kepzesi_database_connection('simon_says');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $scores = $conn->query(
        'SELECT username, score FROM simonsays_leaderboard ORDER BY score DESC LIMIT 10'
    )->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($scores);
} else {
    echo "Invalid request method.";
}