<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id', 'gateway', 'provider_reference', 'payer_reference',
        'amount_minor', 'currency', 'status', 'paid_at', 'payload',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'payload' => 'encrypted:array'];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
