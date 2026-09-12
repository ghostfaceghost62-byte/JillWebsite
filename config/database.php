<?php
declare(strict_types=1);

function load_env_file(): void {
    $envPath = __DIR__.'/../.env';
    if (!is_file($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
        $key = trim($key);
        $value = trim((string) $value, " \t\n\r\0\x0B\"'");

        if ($key === '') {
            continue;
        }

        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key.'='.$value);
    }
}

function config_value(string $key, mixed $fallback = null): mixed {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value !== false && $value !== null && $value !== '') {
        return $value;
    }
    return $fallback;
}

load_env_file();

// APP_ROOT: on Render this should be empty (app IS the doc root).
// On XAMPP it's /hotelreservation. We check explicitly for the env var
// being set (even if empty) before falling back to the XAMPP default.
$appRootEnv = $_ENV['APP_ROOT'] ?? $_SERVER['APP_ROOT'] ?? getenv('APP_ROOT');
if ($appRootEnv !== false && $appRootEnv !== null) {
    // Env var is explicitly set (even if empty string) — use it
    define('APP_ROOT', $appRootEnv);
} else {
    // No env var at all — fall back to XAMPP default
    define('APP_ROOT', '/hotelreservation');
}

define('DB_HOST', (string) config_value('DB_HOST', '127.0.0.1'));
define('DB_PORT', (string) config_value('DB_PORT', '3307'));
define('DB_NAME', (string) config_value('DB_NAME', 'hotelreservation_db'));
define('DB_USER', (string) config_value('DB_USER', 'root'));
$dbPass = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? getenv('DB_PASS');
define('DB_PASS', $dbPass !== false && $dbPass !== null ? (string) $dbPass : 'admin12345');

// SSL flag for Aiven MySQL (or any remote MySQL requiring SSL)
$dbSsl = config_value('DB_SSL', 'false');
define('DB_SSL', $dbSsl === 'true' || $dbSsl === '1' || $dbSsl === true);

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $dsn = config_value('DB_DSN');
        if ($dsn === null || $dsn === false || $dsn === '') {
            $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4';
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,
        ];

        // Enable SSL for remote MySQL (Aiven, PlanetScale, etc.)
        if (DB_SSL) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Disable ONLY_FULL_GROUP_BY to allow complex aggregation queries on MySQL 8
        try {
            $pdo->exec("SET SESSION sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
        } catch (Throwable $e) {
            // Ignore if driver doesn't support session variables
        }
    }
    return $pdo;
}
