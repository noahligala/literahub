<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegisteredDevice extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_token_hash',
        'device_name',
        'device_type',
        'browser',
        'platform',
        'last_ip_address',
        'last_user_agent',
        'fingerprint_hash',
        'first_seen_at',
        'last_seen_at',
        'trusted_at',
        'revoked_at',
        'revocation_reason',
    ];


    protected $hidden = [
        'device_token_hash',
        'fingerprint_hash',
    ];


    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'trusted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }


    public function readerSessions(): HasMany
    {
        return $this->hasMany(
            ReaderSession::class
        );
    }


    public function readingActivities(): HasMany
    {
        return $this->hasMany(
            ReadingActivity::class
        );
    }


    public function securityEvents(): HasMany
    {
        return $this->hasMany(
            SecurityEvent::class
        );
    }


    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }


    public function isTrusted(): bool
    {
        return $this->trusted_at !== null
            && ! $this->isRevoked();
    }
}