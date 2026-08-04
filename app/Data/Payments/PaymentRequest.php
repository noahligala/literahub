<?php

namespace App\Data\Payments;

final readonly class PaymentRequest
{
    public function __construct(
        public int $amountMinor,
        public string $currency,
        public string $payerReference,
        public string $description,
        public string $callbackUrl,
        public array $metadata = [],
    ) {
    }
}
