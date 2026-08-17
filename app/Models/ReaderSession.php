<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReaderSession extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'book_id',
        'school_id',
        'registered_device_id',
        'session_token_hash',
        'public_id',
        'forensic_id',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'current_page',
        'started_at',
        'last_activity_at',
        'expires_at',
        'absolute_expires_at',
        'revoked_at',
        'revocation_reason',
        'page_requests',
        'denied_requests',
    ];


    protected $hidden = [
        'session_token_hash',
        'device_fingerprint',
    ];


    protected function casts(): array
    {
        return [
            'current_page' => 'integer',
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'expires_at' => 'datetime',
            'absolute_expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'page_requests' => 'integer',
            'denied_requests' => 'integer',
        ];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }


    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class
        );
    }


    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class
        );
    }


    public function device(): BelongsTo
    {
        return $this->belongsTo(
            RegisteredDevice::class,
            'registered_device_id'
        );
    }


    public function activities(): HasMany
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


    public function isExpired(): bool
    {
        if ($this->isRevoked()) {
            return true;
        }


        if (
            $this->expires_at
            &&
            $this->expires_at->isPast()
        ) {
            return true;
        }


        if (
            $this->absolute_expires_at
            &&
            $this->absolute_expires_at->isPast()
        ) {
            return true;
        }


        return false;
    }


    public function isActive(): bool
    {
        return ! $this->isExpired();
    }
}