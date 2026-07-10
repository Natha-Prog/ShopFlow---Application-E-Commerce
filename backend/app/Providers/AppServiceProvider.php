<?php

namespace App\Providers;

use App\Services\NullPaymentService;
use App\Services\PaymentServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentServiceInterface::class, NullPaymentService::class);
    }

    public function boot(): void
    {
        //
    }
}
