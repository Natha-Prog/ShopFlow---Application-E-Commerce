<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MvolaService;
use App\Services\OrangeMoneyService;
use App\Services\AirtelMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private MvolaService $mvolaService,
        private OrangeMoneyService $orangeMoneyService,
        private AirtelMoneyService $airtelMoneyService,
    ) {}

    /**
     * Handle MVola webhook
     */
    public function mvola(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-MVola-Signature');

        if (!$this->mvolaService->validateWebhookSignature($payload, $signature)) {
            Log::warning('Invalid MVola webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();
        $transactionId = $data['transaction_id'] ?? null;

        if (!$transactionId) {
            Log::error('MVola webhook missing transaction_id');
            return response()->json(['error' => 'Missing transaction_id'], 400);
        }

        $transaction = Transaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            Log::error('MVola webhook transaction not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $status = $data['status'] ?? 'pending';

        switch ($status) {
            case 'completed':
            case 'success':
                if ($transaction->isPending() || $transaction->status === 'processing') {
                    $transaction->markAsCompleted();
                    $transaction->order()->update(['status' => 'paid']);
                }
                break;
            case 'failed':
                if ($transaction->isPending() || $transaction->status === 'processing') {
                    $transaction->markAsFailed();
                }
                break;
            case 'processing':
                $transaction->markAsProcessing();
                break;
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Handle Orange Money webhook
     */
    public function orangeMoney(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Orange-Signature');

        if (!$this->orangeMoneyService->validateWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Orange Money webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();
        $transactionId = $data['transaction_id'] ?? null;

        if (!$transactionId) {
            Log::error('Orange Money webhook missing transaction_id');
            return response()->json(['error' => 'Missing transaction_id'], 400);
        }

        $transaction = Transaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            Log::error('Orange Money webhook transaction not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $status = $data['status'] ?? 'pending';

        switch ($status) {
            case 'completed':
            case 'success':
                if ($transaction->isPending() || $transaction->status === 'processing') {
                    $transaction->markAsCompleted();
                    $transaction->order()->update(['status' => 'paid']);
                }
                break;
            case 'failed':
                if ($transaction->isPending() || $transaction->status === 'processing') {
                    $transaction->markAsFailed();
                }
                break;
            case 'processing':
                $transaction->markAsProcessing();
                break;
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Handle Airtel Money webhook
     */
    public function airtelMoney(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Airtel-Signature');

        if (!$this->airtelMoneyService->validateWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Airtel Money webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();
        $transactionId = $data['transaction_id'] ?? null;

        if (!$transactionId) {
            Log::error('Airtel Money webhook missing transaction_id');
            return response()->json(['error' => 'Missing transaction_id'], 400);
        }

        $transaction = Transaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            Log::error('Airtel Money webhook transaction not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $status = $data['status'] ?? 'pending';

        switch ($status) {
            case 'completed':
            case 'success':
                if ($transaction->isPending() || $transaction->status === 'processing') {
                    $transaction->markAsCompleted();
                    $transaction->order()->update(['status' => 'paid']);
                }
                break;
            case 'failed':
                if ($transaction->isPending() || $transaction->status === 'processing') {
                    $transaction->markAsFailed();
                }
                break;
            case 'processing':
                $transaction->markAsProcessing();
                break;
        }

        return response()->json(['status' => 'received']);
    }
}
