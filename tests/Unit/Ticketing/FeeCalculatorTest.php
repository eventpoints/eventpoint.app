<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ticketing;

use App\Service\Ticketing\FeeCalculator;
use PHPUnit\Framework\TestCase;

final class FeeCalculatorTest extends TestCase
{
    private FeeCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new FeeCalculator();
    }

    public function testZeroOrderReturnsZeroFee(): void
    {
        self::assertSame(0, $this->calculator->calculate(0));
    }

    public function testMinimumFeeClampingForSmallOrder(): void
    {
        // €0.05 = 5 cents → 1% = 0.05 cents → rounds to 0, clamped to min 20
        self::assertSame(20, $this->calculator->calculate(5));
    }

    public function testMinimumFeeClampingForTwoCentOrder(): void
    {
        // €1.99 = 199 cents → 1% = 1.99 cents → rounds to 2, clamped to min 20
        self::assertSame(20, $this->calculator->calculate(199));
    }

    public function testNormalFeeCalculation(): void
    {
        // €25.00 = 2500 cents → 1% = 25 cents (within min/max range)
        self::assertSame(25, $this->calculator->calculate(2500));
    }

    public function testMaximumFeeClampingForLargeOrder(): void
    {
        // €300.00 = 30000 cents → 1% = 300 cents → clamped to max 200
        self::assertSame(200, $this->calculator->calculate(30000));
    }

    public function testFeeAtExactMinimumBoundary(): void
    {
        // 2000 cents = €20 → 1% = 20 cents → exactly min
        self::assertSame(20, $this->calculator->calculate(2000));
    }

    public function testFeeAtExactMaximumBoundary(): void
    {
        // 20000 cents = €200 → 1% = 200 cents → exactly max
        self::assertSame(200, $this->calculator->calculate(20000));
    }

    public function testFeeJustBelowMinimum(): void
    {
        // 1999 cents → 1% = 19.99 → rounds to 20, just hits the minimum
        self::assertSame(20, $this->calculator->calculate(1999));
    }

    public function testFeeJustAboveMaximum(): void
    {
        // 20001 cents → 1% = 200.01 → rounds to 200, capped at max
        self::assertSame(200, $this->calculator->calculate(20001));
    }
}
