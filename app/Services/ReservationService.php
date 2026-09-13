<?php
declare(strict_types=1);

namespace App\Services;

use PDO;
use Exception;

class ReservationService
{
    private PDO $pdo;
    private PricingService $pricing;

    public function __construct(PDO $pdo, PricingService $pricing)
    {
        $this->pdo = $pdo;
        $this->pricing = $pricing;
    }

    /**
     * Generate a unique, professional reservation number (e.g. JHM-2026-012345)
     */
    public function generateReservationNumber(): string
    {
        $year = date('Y');
        // Generate a random 6-digit hex string and uppercase it
        $suffix = strtoupper(bin2hex(random_bytes(3)));
        return "JHM-{$year}-{$suffix}";
    }

    /**
     * Check if a room is available for the given dates.
     */
    public function isRoomAvailable(int $roomId, string $checkIn, string $checkOut): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT 1 FROM reservations 
            WHERE room_id = ? 
            AND status IN ("PENDING", "CONFIRMED", "CHECKED_IN")
            AND check_in < ? 
            AND check_out > ?
        ');
        $stmt->execute([$roomId, $checkOut, $checkIn]);
        return (bool) !$stmt->fetchColumn();
    }

    /**
     * Validates if a state transition is legal.
     */
    public function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        $transitions = [
            'PENDING' => ['CONFIRMED', 'CANCELLED', 'REJECTED'],
            'CONFIRMED' => ['CHECKED_IN', 'CANCELLED'],
            'CHECKED_IN' => ['CHECKED_OUT'],
            'CHECKED_OUT' => [],
            'CANCELLED' => [],
            'REJECTED' => []
        ];

        if (!isset($transitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, $transitions[$currentStatus], true);
    }
}

