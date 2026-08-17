<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookBorrowing extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'school_id',

        'borrowed_at',
        'due_at',
        'returned_at',

        'status',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' =>
                'datetime',

            'due_at' =>
                'datetime',

            'returned_at' =>
                'datetime',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function isActive(): bool
    {
        return $this->status === 'borrowed';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        return $this->due_at
            && $this->due_at->isPast()
            && !$this->returned_at;
    }
}