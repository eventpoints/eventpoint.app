<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ticketing;

use PHPUnit\Framework\TestCase;
use Stripe\Exception\SignatureVerificationException;
use Stripe\WebhookSignature;

final class WebhookVerificationTest extends TestCase
{
    private const string WEBHOOK_SECRET = 'whsec_test_secret_for_unit_tests_only';

    public function testValidSignaturePasses(): void
    {
        $payload = json_encode([
            'id' => 'evt_test_123',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [],
            ],
        ]);

        $timestamp = time();
        $signature = $this->computeStripeSignature($payload, $timestamp, self::WEBHOOK_SECRET);
        $sigHeader = 't=' . $timestamp . ',v1=' . $signature;

        // Should not throw
        try {
            WebhookSignature::verifyHeader($payload, $sigHeader, self::WEBHOOK_SECRET, 300);
            self::assertTrue(true);
        } catch (SignatureVerificationException $e) {
            self::fail('Valid signature should not throw: ' . $e->getMessage());
        }
    }

    public function testTamperedPayloadThrows(): void
    {
        $originalPayload = json_encode([
            'id' => 'evt_test_456',
            'type' => 'checkout.session.completed',
        ]);

        $tamperedPayload = json_encode([
            'id' => 'evt_test_456',
            'type' => 'charge.refunded',
        ]);

        $timestamp = time();
        $signature = $this->computeStripeSignature($originalPayload, $timestamp, self::WEBHOOK_SECRET);
        $sigHeader = 't=' . $timestamp . ',v1=' . $signature;

        $this->expectException(SignatureVerificationException::class);
        WebhookSignature::verifyHeader($tamperedPayload, $sigHeader, self::WEBHOOK_SECRET, 300);
    }

    public function testWrongSecretThrows(): void
    {
        $payload = json_encode([
            'id' => 'evt_test_789',
            'type' => 'checkout.session.completed',
        ]);

        $timestamp = time();
        $signature = $this->computeStripeSignature($payload, $timestamp, 'whsec_wrong_secret');
        $sigHeader = 't=' . $timestamp . ',v1=' . $signature;

        $this->expectException(SignatureVerificationException::class);
        WebhookSignature::verifyHeader($payload, $sigHeader, self::WEBHOOK_SECRET, 300);
    }

    public function testExpiredTimestampThrows(): void
    {
        $payload = json_encode([
            'id' => 'evt_test_expired',
            'type' => 'checkout.session.completed',
        ]);

        $oldTimestamp = time() - 400; // 400 seconds ago, past 300s tolerance
        $signature = $this->computeStripeSignature($payload, $oldTimestamp, self::WEBHOOK_SECRET);
        $sigHeader = 't=' . $oldTimestamp . ',v1=' . $signature;

        $this->expectException(SignatureVerificationException::class);
        WebhookSignature::verifyHeader($payload, $sigHeader, self::WEBHOOK_SECRET, 300);
    }

    private function computeStripeSignature(string $payload, int $timestamp, string $secret): string
    {
        // Stripe uses the full secret string (including whsec_ prefix) as the hmac key
        return hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
    }
}
