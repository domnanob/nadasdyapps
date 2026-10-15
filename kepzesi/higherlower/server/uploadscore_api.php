<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../includes/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Érvénytelen kérési metódus.']);
    exit;
}

if (empty($_SESSION['user_id']) || !empty($_SESSION['is_guest'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Bejelentkezés szükséges a pontszám mentéséhez.']);
    exit;
}

$score = filter_var($_POST['score'] ?? null, FILTER_VALIDATE_INT);
$mode = $_POST['mode'] ?? '';
if ($score === false || $score < 0 || !in_array($mode, ['spotify', 'countries'], true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Érvénytelen pontszám vagy játékmód.']);
    exit;
}

try {
    $conn = kepzesi_database_connection('higherorlowergame');
    $result = uploadScore((string) $_SESSION['user_id'], $score, $mode);
    if (!$result) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'A pontszám mentése nem sikerült.']);
        exit;
    }

    echo json_encode(['success' => true]);
} catch (PDOException $error) {
    error_log('Could not save Higher/Lower score: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'A pontszám mentése adatbázishiba miatt nem sikerült.']);
}
