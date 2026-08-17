<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingActivity extends Model
{
    use HasFactory;


    protected $fillable = [
        'reader_session_id',
        'user_id',
        'book_id',
        'school_id',
        'registered_device_id',
        'page_number',
        'event_type',
        'ip_address',
        'metadata',
        'occurred_at',
    ];


    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }


    public function readerSession(): BelongsTo
    {
        return $this->belongsTo(
            ReaderSession::class
        );
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
}