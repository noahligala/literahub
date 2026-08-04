<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Data\Payments\PaymentRequest;
use App\Data\Payments\PaymentResponse;
use LogicException;

class MpesaPaymentGateway implements PaymentGateway
{
    public function initiate(PaymentRequest $request): PaymentResponse
    {
        throw new LogicException('M-Pesa integration is not configured yet. Implement OAuth, STK Push, idempotency, and callback verification.');
    }

    public function verify(string $providerReference): PaymentResponse
    {
        throw new LogicException('M-Pesa transaction verification is not configured yet.');
    }

    public function handleCallback(array $payload): PaymentResponse
    {
        throw new LogicException('M-Pesa callback handling is not configured yet.');
    }
}
