<?php

namespace App\Services;

/**
 * Payment service interface — prepared for future Stripe integration.
 *
 * TODO: Implement StripePaymentService when payment integration is required.
 */
interface PaymentServiceInterface
{
    public function createPaymentIntent(float $amount, string $currency, array $metadata = []): array;

    public function confirmPayment(string $paymentIntentId): array;

    public function refundPayment(string $paymentIntentId, ?float $amount = null): array;
}
