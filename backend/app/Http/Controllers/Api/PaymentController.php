<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentConfigService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentConfigService $paymentConfig,
    ) {}

    public function methods(): JsonResponse
    {
        return response()->json([
            'currency' => config('payments.currency', 'MGA'),
            'methods' => $this->paymentConfig->getEnabledMethods(),
        ]);
    }
}
