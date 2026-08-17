<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityEvent extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'book_id',
        'school_id',
        'reader_session_id',
        'registered_device_id',
        'event_type',
        'severity',
        'ip_address',
        'user_agent',
        'description',
        'context',
        'detected_at',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];


    protected function casts(): array
    {
        return [
            'context' => 'array',
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
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


    public function readerSession(): BelongsTo
    {
        return $this->belongsTo(
            ReaderSession::class
        );
    }


    public function device(): BelongsTo
    {
        return $this->belongsTo(
            RegisteredDevice::class,
            'registered_device_id'
        );
    }


    public function resolver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'resolved_by'
        );
    }


    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }
}