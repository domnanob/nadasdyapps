<?php

function kepzesi_database_connection(string $databaseName): PDO
{
    $databaseNames = [
        'kepzesi_games',
        'higherorlowergame',
        'simon_says',
        'ujjkoptato_jatek',
        'findtheball',
    ];

    if (!in_array($databaseName, $databaseNames, true)) {
        throw new InvalidArgumentException('Unknown application database: ' . $databaseName);
    }

    $databasePath = getenv('KEPZESI_SQLITE_PATH')
        ?: __DIR__ . '/../database/kepzesi.sqlite';
    $databaseDirectory = dirname($databasePath);
    if (!is_dir($databaseDirectory) || !is_writable($databaseDirectory)) {
        throw new RuntimeException('The SQLite database directory must exist and be writable.');
    }

    $connection = new PDO('sqlite:' . $databasePath);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $connection->exec('PRAGMA busy_timeout = 5000');
    $connection->exec('PRAGMA foreign_keys = ON');

    $schemaPath = __DIR__ . '/../database/setup.sql';
    $schema = file_get_contents($schemaPath);
    if ($schema === false) {
        throw new RuntimeException('Unable to read the SQLite database schema.');
    }
    $connection->exec($schema);

    return $connection;
}
