<?php
require_once __DIR__ . '/../includes/bootstrap.php';

try {
    $pdo = db();
    
    echo "=== ROOM IMAGES ===\n";
    $images = $pdo->query("SELECT * FROM room_images")->fetchAll();
    print_r($images);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

