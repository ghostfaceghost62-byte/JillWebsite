<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/currency.php';

header('Content-Type: application/json');

// Only allow supported target conversions
$target = strtoupper($_GET['target'] ?? 'PHP');
if (!in_array($target, CurrencyService::SUPPORTED)) {
    $target = 'PHP';
}

echo json_encode([
    'base' => CurrencyService::BASE_CURRENCY,
    'target' => $target,
    'rates' => CurrencyService::getRates()
]);
