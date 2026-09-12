<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = db();
    $pdo->query('SELECT 1');
    http_response_code(200);
    echo "OK";
} catch (Throwable $e) {
    http_response_code(503);
    echo "FAIL";
}
