<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MvolaService
{
    private string $apiKey;
    private string $apiSecret;
    private string $merchantNumber;
    private string $partnerName;
    private string $environment;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.mvola.api_key');
        $this->apiSecret = config('services.mvola.api_secret');
        $this->merchantNumber = config('services.mvola.merchant_number');
        $this->partnerName = config('services.mvola.partner_name', config('app.name'));
        $this->environment = config('services.mvola.environment', 'sandbox');
        
        $this->baseUrl = $this->environment === 'production'
            ? 'https://api.mvola.mg/mvola/mm/transactions/type/merchantpay/1.0.0'
            : 'https://pre-api.mvola.mg/mvola/mm/transactions/type/merchantpay/1.0.0';
    }

    /**
     * Initiate a payment transaction
     */
    public function initiatePayment(array $data): array
    {
        $correlationId = Str::uuid()->toString();
        $requestDate = now()->toISOString();

        $payload = [
            'amount' => (string) $data['amount'],
            'currency' => 'MGA',
            'descriptionText' => $data['description'] ?? 'Payment',
            'requestingOrganisationTransactionReference' => $data['reference'],
            'requestDate' => $requestDate,
            'debitParty' => [
                [
                    'key' => 'msisdn',
                    'value' => $data['customer_phone'],
                ],
            ],
            'creditParty' => [
                [
                    'key' => 'msisdn',
                    'value' => $this->merchantNumber,
                ],
            ],
            'metadata' => [
                [
                    'key' => 'partnerName',
                    'value' => $this->partnerName,
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Version' => '1.0',
            'X-CorrelationID' => $correlationId,
            'X-Callback-URL' => route('webhooks.mvola'),
            'Cache-Control' => 'no-cache',
            'UserAccountIdentifier' => $this->merchantNumber,
        ])->post($this->baseUrl, $payload);

        if ($response->failed()) {
            Log::error('MVola payment initiation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'correlation_id' => $correlationId,
            ]);
            throw new \Exception('Failed to initiate MVola payment');
        }

        $responseData = $response->json();
        
        // Store server correlation ID for status checking
        $responseData['server_correlation_id'] = $responseData['serverCorrelationId'] ?? null;
        $responseData['correlation_id'] = $correlationId;

        return $responseData;
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $serverCorrelationId): array
    {
        $correlationId = Str::uuid()->toString();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Accept' => 'application/json',
            'Version' => '1.0',
            'X-CorrelationID' => $correlationId,
            'UserAccountIdentifier' => $this->merchantNumber,
            'partnerName' => $this->partnerName,
            'Cache-Control' => 'no-cache',
        ])->get("{$this->baseUrl}/status/{$serverCorrelationId}");

        if ($response->failed()) {
            Log::error('MVola transaction status check failed', [
                'server_correlation_id' => $serverCorrelationId,
                'status' => $response->status(),
            ]);
            throw new \Exception('Failed to get MVola transaction status');
        }

        return $response->json();
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetails(string $transactionReference): array
    {
        $correlationId = Str::uuid()->toString();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Accept' => 'application/json',
            'Version' => '1.0',
            'X-CorrelationID' => $correlationId,
            'UserAccountIdentifier' => $this->merchantNumber,
            'Cache-Control' => 'no-cache',
        ])->get("{$this->baseUrl}/{$transactionReference}");

        if ($response->failed()) {
            Log::error('MVola transaction details check failed', [
                'transaction_reference' => $transactionReference,
                'status' => $response->status(),
            ]);
            throw new \Exception('Failed to get MVola transaction details');
        }

        return $response->json();
    }

    /**
     * Generate USSD code for payment initiation
     */
    public function generateUSSDCode(string $amount, string $reference): string
    {
        $formattedAmount = number_format($amount, 0, '', ' ');
        return "*111*1*{$this->merchantNumber}*{$formattedAmount}*{$reference}#";
    }

    /**
     * Generate QR code data for payment scan
     */
    public function generateQRCodeData(array $data): string
    {
        return json_encode([
            'provider' => 'mvola',
            'merchant' => $this->merchantNumber,
            'amount' => $data['amount'],
            'reference' => $data['reference'],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get access token from MVola API
     */
    private function getAccessToken(): string
    {
        $tokenUrl = $this->environment === 'production'
            ? 'https://api.mvola.mg/mvola/mm/authorization/token'
            : 'https://pre-api.mvola.mg/mvola/mm/authorization/token';

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiKey,
            'client_secret' => $this->apiSecret,
        ]);

        if ($response->failed()) {
            Log::error('MVola token generation failed', [
                'status' => $response->status(),
            ]);
            throw new \Exception('Failed to get MVola access token');
        }

        return $response->json()['access_token'];
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('services.mvola.webhook_secret');
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
