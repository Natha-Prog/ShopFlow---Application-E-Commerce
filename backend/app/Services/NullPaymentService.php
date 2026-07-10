<?php

namespace App\Services;

/**
 * Stub payment service — orders are created with payment_status: pending.
 */
class NullPaymentService implements PaymentServiceInterface
{
    public function createPaymentIntent(float $amount, string $currency, array $metadata = []): array
    {
        return [
            'id' => null,
            'status' => 'pending',
            'message' => 'Payment integration not yet implemented',
        ];
    }

    public function confirmPayment(string $paymentIntentId): array
    {
        return ['status' => 'pending'];
    }

    public function refundPayment(string $paymentIntentId, ?float $amount = null): array
    {
        return ['status' => 'not_implemented'];
    }
}
