<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Webhook endpoints for Mobile Money providers
Route::post('/webhooks/mvola', [WebhookController::class, 'mvola']);
Route::post('/webhooks/orange-money', [WebhookController::class, 'orangeMoney']);
Route::post('/webhooks/airtel-money', [WebhookController::class, 'airtelMoney']);
