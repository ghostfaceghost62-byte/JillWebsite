<?php
require_once __DIR__ . '/../includes/bootstrap.php';

try {
    $pdo = db();
    echo "Connecting to Aiven DB for payments table migration...\n";

    // Add online_ref_code if not exists
    try {
        $pdo->exec("ALTER TABLE payments ADD COLUMN online_ref_code VARCHAR(255) NULL AFTER payment_method");
        echo "Added column online_ref_code.\n";
    } catch (Exception $e) {
        echo "online_ref_code already exists or note: " . $e->getMessage() . "\n";
    }

    // Add payment_proof_img if not exists
    try {
        $pdo->exec("ALTER TABLE payments ADD COLUMN payment_proof_img VARCHAR(500) NULL AFTER online_ref_code");
        echo "Added column payment_proof_img.\n";
    } catch (Exception $e) {
        echo "payment_proof_img already exists or note: " . $e->getMessage() . "\n";
    }

    // Add admin_notes if not exists
    try {
        $pdo->exec("ALTER TABLE payments ADD COLUMN admin_notes TEXT NULL AFTER payment_proof_img");
        echo "Added column admin_notes.\n";
    } catch (Exception $e) {
        echo "admin_notes already exists or note: " . $e->getMessage() . "\n";
    }

    // Add submitted_at if not exists
    try {
        $pdo->exec("ALTER TABLE payments ADD COLUMN submitted_at DATETIME NULL AFTER admin_notes");
        echo "Added column submitted_at.\n";
    } catch (Exception $e) {
        echo "submitted_at already exists or note: " . $e->getMessage() . "\n";
    }

    // Modify payment_status column to VARCHAR(50) or ENUM
    try {
        $pdo->exec("ALTER TABLE payments MODIFY COLUMN payment_status VARCHAR(50) NOT NULL DEFAULT 'PENDING'");
        echo "Modified payment_status to VARCHAR(50).\n";
    } catch (Exception $e) {
        echo "payment_status modify note: " . $e->getMessage() . "\n";
    }

    echo "Aiven DB Payments table migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
}

