<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookAccessRequest extends Model
{
    protected $fillable = [
        'book_id',
        'student_id',
        'school_id',
        'teacher_id',

        'reason',

        'status',

        'reviewed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' =>
                'datetime',

            'expires_at' =>
                'datetime',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'teacher_id'
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        if (
            $this->expires_at
            && $this->expires_at->isPast()
        ) {
            return false;
        }

        return true;
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}