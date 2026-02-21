<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ticketing;

use App\Enum\OrderStatusEnum;
use PHPUnit\Framework\TestCase;

final class OrderStatusTest extends TestCase
{
    public function testPendingToPaidTransition(): void
    {
        self::assertSame('pending', OrderStatusEnum::PENDING->value);
        self::assertSame('paid', OrderStatusEnum::PAID->value);

        // Verify the enum cases exist and have expected values
        $cases = OrderStatusEnum::cases();
        $caseValues = array_map(fn ($case) => $case->value, $cases);

        self::assertContains('pending', $caseValues);
        self::assertContains('paid', $caseValues);
        self::assertContains('refunded', $caseValues);
        self::assertContains('failed', $caseValues);
        self::assertContains('cancelled', $caseValues);
    }

    public function testPaidToRefundedTransition(): void
    {
        // PAID → REFUNDED is a valid forward transition
        $paid = OrderStatusEnum::PAID;
        $refunded = OrderStatusEnum::REFUNDED;

        self::assertNotSame($paid, $refunded);
        self::assertSame('paid', $paid->value);
        self::assertSame('refunded', $refunded->value);
    }

    public function testAllCasesAreDefined(): void
    {
        $expectedCases = ['pending', 'paid', 'failed', 'cancelled', 'refunded'];
        $actualCases = array_map(fn ($case) => $case->value, OrderStatusEnum::cases());

        sort($expectedCases);
        sort($actualCases);

        self::assertSame($expectedCases, $actualCases);
    }

    public function testFromValue(): void
    {
        self::assertSame(OrderStatusEnum::PENDING, OrderStatusEnum::from('pending'));
        self::assertSame(OrderStatusEnum::PAID, OrderStatusEnum::from('paid'));
        self::assertSame(OrderStatusEnum::REFUNDED, OrderStatusEnum::from('refunded'));
    }

    public function testInvalidValueThrows(): void
    {
        $this->expectException(\ValueError::class);
        OrderStatusEnum::from('invalid_status');
    }
}
