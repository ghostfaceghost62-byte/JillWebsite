<?php
declare(strict_types=1);

class CurrencyService {
    public const BASE_CURRENCY = 'PHP';
    public const SUPPORTED = ['PHP', 'USD', 'EUR', 'JPY', 'SGD', 'AUD', 'GBP'];

    public static function getRates(): array {
        $pdo = db();
        
        // 1. Fetch from DB
        $stmt = $pdo->query('SELECT currency, rate, fetched_at FROM currency_rates');
        $rates = [];
        $needsRefresh = false;
        
        while ($row = $stmt->fetch()) {
            $rates[$row['currency']] = (float)$row['rate'];
            if (strtotime($row['fetched_at']) < time() - 86400) {
                $needsRefresh = true;
            }
        }
        
        // 2. Refresh if needed (or if empty)
        if (empty($rates) || $needsRefresh) {
            $newRates = self::fetchFromApi();
            if ($newRates) {
                $pdo->beginTransaction();
                $insert = $pdo->prepare('INSERT INTO currency_rates (currency, rate, fetched_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE rate = VALUES(rate), fetched_at = NOW()');
                foreach ($newRates as $curr => $rate) {
                    if (in_array($curr, self::SUPPORTED)) {
                        $insert->execute([$curr, $rate]);
                        $rates[$curr] = (float)$rate;
                    }
                }
                $pdo->commit();
            }
        }
        
        return $rates;
    }

    private static function fetchFromApi(): ?array {
        // Fallback or open.er-api.com
        try {
            $json = @file_get_contents('https://open.er-api.com/v6/latest/' . self::BASE_CURRENCY);
            if ($json) {
                $data = json_decode($json, true);
                if (isset($data['rates'])) {
                    return $data['rates'];
                }
            }
        } catch (Exception $e) {
            // Ignore, use stale rates
        }
        return null;
    }

    public static function convert(float $amount, string $targetCurrency): float {
        if ($targetCurrency === self::BASE_CURRENCY) return $amount;
        $rates = self::getRates();
        $rate = $rates[$targetCurrency] ?? 1.0;
        return $amount * $rate;
    }

    public static function format(float $amount, string $currency): string {
        $symbols = [
            'PHP' => '₱',
            'USD' => '$',
            'EUR' => '€',
            'JPY' => '¥',
            'SGD' => 'S$',
            'AUD' => 'A$',
            'GBP' => '£'
        ];
        $sym = $symbols[$currency] ?? $currency . ' ';
        return $sym . number_format($amount, 2);
    }
}
