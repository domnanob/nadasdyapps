<?php
declare(strict_types=1);

foreach (['mbstring', 'pdo_sqlite'] as $ext) {
    if (!extension_loaded($ext)) {
        http_response_code(500);
        exit('Hiányzó PHP bővítmény: ' . $ext . '. Kapcsold be a tárhely PHP beállításaiban.');
    }
}

$GLOBALS['config'] = require __DIR__ . '/config.php';
date_default_timezone_set((string) cfg('timezone'));

set_exception_handler(function (Throwable $ex) {
    error_log((string) $ex);
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    if ($ex instanceof RuntimeException && $ex->getMessage() === 'DB_NOT_WRITABLE') {
        exit('Az adatbázis nem írható. A data/ mappának ÉS a nadasdy_cup.sqlite fájlnak is írhatónak kell lennie a webszervernek (775/664, vagy 777/666).');
    }
    exit('Szerverhiba történt. Nézd meg a tárhely hibanaplóját.');
});

function cfg(string $key)
{
    return $GLOBALS['config'][$key];
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $path = (string) cfg('db_path');
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }

    if (!is_writable($dir) || (is_file($path) && !is_writable($path))) {
        throw new RuntimeException('DB_NOT_WRITABLE');
    }

    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5,
    ]);

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS registrations (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name  TEXT    NOT NULL,
            name_key   TEXT    NOT NULL,
            class_name TEXT    NOT NULL,
            game_rank  TEXT,
            confirmed  INTEGER NOT NULL DEFAULT 0,
            created_at TEXT    NOT NULL DEFAULT (datetime(\'now\', \'localtime\'))
        );
        CREATE UNIQUE INDEX IF NOT EXISTS ux_registrations_person
            ON registrations (name_key, class_name);
        CREATE TABLE IF NOT EXISTS settings (
            key   TEXT PRIMARY KEY,
            value TEXT NOT NULL
        );
        CREATE TABLE IF NOT EXISTS login_attempts (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            ip           TEXT    NOT NULL,
            attempted_at INTEGER NOT NULL
        );
    ');

    return $pdo;
}

/* ---------- Beállítások (pl. jelentkezés nyitva/zárva) ---------- */

function get_setting(string $key, string $default = ''): string
{
    $st = db()->prepare('SELECT value FROM settings WHERE key = ?');
    $st->execute([$key]);
    $v = $st->fetchColumn();
    return $v === false ? $default : (string) $v;
}

function set_setting(string $key, string $value): void
{
    $st = db()->prepare(
        'INSERT INTO settings (key, value) VALUES (?, ?)
         ON CONFLICT(key) DO UPDATE SET value = excluded.value'
    );
    $st->execute([$key, $value]);
}

function registration_open(): bool
{
    return get_setting('registration_open', '1') === '1';
}

/* ---------- Munkamenet, CSRF, flash ---------- */

function boot_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    session_name('nadasdycup');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_ok(): bool
{
    $sent = (string) ($_POST['csrf'] ?? '');
    return $sent !== '' && hash_equals(csrf_token(), $sent);
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function send_security_headers(): void
{
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header(
        "Content-Security-Policy: default-src 'self'; "
        . "style-src 'self' https://fonts.googleapis.com; "
        . "font-src https://fonts.gstatic.com; "
        . "img-src 'self' data:; script-src 'self'; "
        . "form-action 'self'; frame-ancestors 'none'; base-uri 'none'"
    );
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------- Jelentkezés validálása és mentése ---------- */

/**
 * @return array{0: array, 1: string[]} [tiszta adatok, hibák]
 */
function validate_registration(array $in, ?int $ignoreId = null): array
{
    $name  = trim((string) preg_replace('/\s+/u', ' ', (string) ($in['full_name'] ?? '')));
    $class = (string) ($in['class_name'] ?? '');
    $rank  = (string) ($in['game_rank'] ?? '');
    $errors = [];

    $len = mb_strlen($name);
    if ($name === '') {
        $errors[] = 'Add meg a teljes neved.';
    } elseif ($len < 3 || $len > 100 || strpos($name, ' ') === false) {
        $errors[] = 'A teljes nevedet írd be: vezetéknév és keresztnév is kell.';
    } elseif (!preg_match('/^[\p{L}][\p{L}\p{M} .\'’-]*$/u', $name)) {
        $errors[] = 'A név csak betűt, szóközt, pontot, kötőjelet és aposztrófot tartalmazhat.';
    }

    if (!in_array($class, cfg('classes'), true)) {
        $errors[] = 'Válaszd ki az osztályodat a listából.';
    }

    if ($rank !== '' && !in_array($rank, cfg('ranks'), true)) {
        $errors[] = 'A megadott rang nem szerepel a listában.';
    }

    $key = mb_strtolower($name);

    if (!$errors) {
        $st = db()->prepare(
            'SELECT 1 FROM registrations WHERE name_key = ? AND class_name = ? AND id != ?'
        );
        $st->execute([$key, $class, $ignoreId ?? 0]);
        if ($st->fetchColumn()) {
            $errors[] = 'Ezzel a névvel és osztállyal már van jelentkezés.';
        }
    }

    return [[
        'full_name'  => $name,
        'name_key'   => $key,
        'class_name' => $class,
        'game_rank'  => $rank === '' ? null : $rank,
    ], $errors];
}

function insert_registration(array $d): void
{
    $st = db()->prepare(
        'INSERT INTO registrations (full_name, name_key, class_name, game_rank)
         VALUES (:full_name, :name_key, :class_name, :game_rank)'
    );
    $st->execute($d);
}
