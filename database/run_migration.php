<?php
$sql = file_get_contents(__DIR__ . '/upgrade_v2.sql');
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=hotelreservation_db;charset=utf8mb4','root','');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Split on statement terminator and execute individually
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $ok = 0;
    foreach ($statements as $stmt) {
        if ($stmt === '') continue;
        try {
            $pdo->exec($stmt);
            $ok++;
        } catch (PDOException $e) {
            echo "SKIP: " . substr($stmt, 0, 60) . "\n  -> " . $e->getMessage() . "\n";
        }
    }
    echo "\nDone — $ok statements executed.\n";
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage() . "\n";
}
