<?php

declare(strict_types=1);

namespace App\Service\Ticketing;

final class FeeCalculator
{
    private const int MIN_FEE_CENTS = 20;
    private const int MAX_FEE_CENTS = 200;
    private const float FEE_RATE = 0.01;

    public function calculate(int $orderTotalCents): int
    {
        if ($orderTotalCents === 0) {
            return 0;
        }

        $fee = (int) round($orderTotalCents * self::FEE_RATE);

        return max(self::MIN_FEE_CENTS, min(self::MAX_FEE_CENTS, $fee));
    }
}
