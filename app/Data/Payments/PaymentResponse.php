<?php

namespace App\Data\Payments;

final readonly class PaymentResponse
{
    public function __construct(
        public bool $successful,
        public string $status,
        public ?string $providerReference = null,
        public ?string $message = null,
        public array $payload = [],
    ) {
    }
}
