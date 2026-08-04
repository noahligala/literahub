<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGateway;
use App\Services\Payments\MpesaPaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, MpesaPaymentGateway::class);
    }

    public function boot(): void
    {
        // Add model observers, policies, and production hardening here.
    }
}
