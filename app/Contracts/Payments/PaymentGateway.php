<?php

namespace App\Contracts\Payments;

use App\Data\Payments\PaymentRequest;
use App\Data\Payments\PaymentResponse;

interface PaymentGateway
{
    public function initiate(PaymentRequest $request): PaymentResponse;

    public function verify(string $providerReference): PaymentResponse;

    public function handleCallback(array $payload): PaymentResponse;
}
