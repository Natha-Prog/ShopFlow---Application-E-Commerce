<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AirtelMoneyService
{
    private string $apiKey;
    private string $apiSecret;
    private string $merchantNumber;
    private string $environment;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.airtel_money.api_key');
        $this->apiSecret = config('services.airtel_money.api_secret');
        $this->merchantNumber = config('services.airtel_money.merchant_number');
        $this->environment = config('services.airtel_money.environment', 'sandbox');
        
        $this->baseUrl = $this->environment === 'production'
            ? 'https://payments.airtel.com'
            : 'https://payments-preprod.airtel.com';
    }

    /**
     * Initiate a payment transaction
     */
    public function initiatePayment(array $data): array
    {
        $payload = [
            'amount' => $data['amount'],
            'currency' => 'MGA',
            'description' => $data['description'] ?? 'Payment',
            'merchant_number' => $this->merchantNumber,
            'customer_number' => $data['customer_phone'],
            'reference' => $data['reference'],
            'callback_url' => route('webhooks.airtel_money'),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/v1/payments", $payload);

        if ($response->failed()) {
            Log::error('Airtel Money payment initiation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to initiate Airtel Money payment');
        }

        return $response->json();
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}/v1/payments/{$transactionId}");

        if ($response->failed()) {
            Log::error('Airtel Money transaction status check failed', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
            ]);
            throw new \Exception('Failed to get Airtel Money transaction status');
        }

        return $response->json();
    }

    /**
     * Generate USSD code for payment initiation
     */
    public function generateUSSDCode(string $amount, string $reference): string
    {
        $formattedAmount = number_format($amount, 0, '', ' ');
        return "*151*2*{$this->merchantNumber}*{$formattedAmount}*{$reference}#";
    }

    /**
     * Generate QR code data for payment scan
     */
    public function generateQRCodeData(array $data): string
    {
        return json_encode([
            'provider' => 'airtel_money',
            'merchant' => $this->merchantNumber,
            'amount' => $data['amount'],
            'reference' => $data['reference'],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get access token from Airtel Money API
     */
    private function getAccessToken(): string
    {
        $response = Http::asForm()->post("{$this->baseUrl}/auth/oauth2/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiKey,
            'client_secret' => $this->apiSecret,
        ]);

        if ($response->failed()) {
            Log::error('Airtel Money token generation failed', [
                'status' => $response->status(),
            ]);
            throw new \Exception('Failed to get Airtel Money access token');
        }

        return $response->json()['access_token'];
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('services.airtel_money.webhook_secret');
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
