<?php
require_once __DIR__ . '/../includes/bootstrap.php';

try {
    $pdo = db();
    echo "Successfully connected to Aiven DB!\n";

    // Update hotel_name setting
    $stmt = $pdo->prepare("INSERT INTO hotel_settings (setting_key, setting_value) VALUES ('hotel_name', 'Lido De Paris Hotel & Entertainment Center') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->execute();
    echo "Updated hotel_name setting in Aiven DB.\n";

    // Check hotel_settings table
    $settings = $pdo->query("SELECT * FROM hotel_settings")->fetchAll();
    echo "Current Aiven Hotel Settings:\n";
    print_r($settings);

} catch (Exception $e) {
    echo "Error updating Aiven DB: " . $e->getMessage() . "\n";
}

