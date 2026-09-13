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

$dbHost = config_value('DB_HOST');
$dbPort = config_value('DB_PORT');
$dbName = config_value('DB_NAME');
$dbUser = config_value('DB_USER');
$dbPass = config_value('DB_PASS');

if (!$dbHost || !$dbName || !$dbUser || $dbPass === null) {
    // Fail fast if required configuration is missing, instead of silently connecting to root/admin12345
    header('HTTP/1.1 500 Internal Server Error');
    die('Critical Error: Database configuration is incomplete. Please check environment variables.');
}

define('DB_HOST', (string) $dbHost);
define('DB_PORT', (string) ($dbPort ?: '3306'));
define('DB_NAME', (string) $dbName);
define('DB_USER', (string) $dbUser);
define('DB_PASS', (string) $dbPass);

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
        
        // Disable strict mode completely to allow complex aggregation queries on MySQL 8
        try {
            $pdo->exec("SET SESSION sql_mode = ''");
        } catch (Throwable $e) {
            // Ignore if driver doesn't support session variables
        }
    }
    return $pdo;
}
