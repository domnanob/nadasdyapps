<?php

function check_login(PDO $conn): array
{
    if (isset($_SESSION['is_guest']) && $_SESSION['is_guest'] === true && isset($_SESSION['user_id'])) {
        return [
            'user_id' => $_SESSION['user_id'],
            'username' => 'Guest',
            'email' => '',
            'country' => 'HU',
            'is_guest' => true,
        ];
    }

    if (isset($_SESSION['user_id'])) {

        $id = $_SESSION['user_id'];
        $statement = $conn->prepare('SELECT * FROM higherorlower_users WHERE user_id = ? LIMIT 1');
        $statement->execute([$id]);
        $user_data = $statement->fetch();
        if ($user_data) {
            $user_data['is_guest'] = false;
            return $user_data;
        }

    }

    header("Location: login.php");
    die;
}

function generate__uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function uploadScore(string $user_id, int $score, string $mode): bool
{
    $statement = $GLOBALS['conn']->prepare(
        'INSERT INTO higherorlower_leaderboard (user_id, score, mode) VALUES (?, ?, ?)'
    );
    return $statement->execute([$user_id, $score, $mode]);
}

function changeLeaderboard(string $mode): PDOStatement
{
    global $conn;
    $sql = 'SELECT u.username, u.country, l.score, l.mode
            FROM higherorlower_leaderboard l
            JOIN higherorlower_users u ON l.user_id = u.user_id';
    if ($mode !== 'mixed') {
        $sql .= ' WHERE l.mode = ?';
    }
    $sql .= ' ORDER BY l.score DESC LIMIT 20';
    $statement = $conn->prepare($sql);
    $statement->execute($mode !== 'mixed' ? [$mode] : []);
    return $statement;
}