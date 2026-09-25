<?php
try {
    $options = [
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_SSL_CA => ''
    ];
    $pdo = new PDO('sqlite::memory:', null, null, $options);
    echo "OK";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}

