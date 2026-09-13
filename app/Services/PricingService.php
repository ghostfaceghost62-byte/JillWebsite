<?php
declare(strict_types=1);

namespace App\Services;

class PricingService
{
    private const VAT_RATE = 0.12;

    /**
     * Calculate the full financial breakdown for a reservation.
     * 
     * @param float $pricePerNight The nightly rate of the room.
     * @param int $nights The number of nights for the stay.
     * @return array Returns an associative array containing all financial totals.
     */
    public function calculateReservationTotals(float $pricePerNight, int $nights): array
    {
        // Enforce basic minimums
        if ($pricePerNight < 0) {
            $pricePerNight = 0.0;
        }
        if ($nights < 1) {
            $nights = 1;
        }

        $subtotal = $pricePerNight * $nights;
        
        // VAT is calculated on the subtotal.
        $taxAmount = $subtotal * self::VAT_RATE;
        
        // Grand total is Subtotal + Taxes.
        $grandTotal = $subtotal + $taxAmount;

        return [
            'price_per_night' => round($pricePerNight, 2),
            'nights' => $nights,
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'tax_rate_percent' => self::VAT_RATE * 100,
            'grand_total' => round($grandTotal, 2)
        ];
    }
}

