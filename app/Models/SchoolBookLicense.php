<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolBookLicense extends Model
{
    protected $fillable = [
        'school_id',
        'book_id',

        'publisher_id',
        'author_id',

        'license_number',
        'license_type',

        'starts_at',
        'expires_at',

        'seat_limit',
        'concurrent_reader_limit',

        'allow_student_reading',
        'allow_teacher_reading',
        'allow_teacher_assignment',
        'allow_student_borrowing',
        'allow_print',
        'allow_download',

        'status',

        'price_minor',
        'currency',

        'terms',
        'notes',

        'created_by',

        'revoked_at',
        'revoked_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' =>
                'datetime',

            'expires_at' =>
                'datetime',

            'revoked_at' =>
                'datetime',

            'seat_limit' =>
                'integer',

            'concurrent_reader_limit' =>
                'integer',

            'price_minor' =>
                'integer',

            'allow_student_reading' =>
                'boolean',

            'allow_teacher_reading' =>
                'boolean',

            'allow_teacher_assignment' =>
                'boolean',

            'allow_student_borrowing' =>
                'boolean',

            'allow_print' =>
                'boolean',

            'allow_download' =>
                'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | School
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Book
    |--------------------------------------------------------------------------
    */

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Publisher Licensor
    |--------------------------------------------------------------------------
    */

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(
            Publisher::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Author Licensor
    |--------------------------------------------------------------------------
    */

    public function author(): BelongsTo
    {
        return $this->belongsTo(
            Author::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Created By
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Revoked By
    |--------------------------------------------------------------------------
    */

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'revoked_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Licence Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (
            $this->starts_at
            && $this->starts_at->isFuture()
        ) {
            return false;
        }

        if (
            $this->expires_at
            && $this->expires_at->isPast()
        ) {
            return false;
        }

        if ($this->revoked_at) {
            return false;
        }

        return true;
    }

    public function allowsStudentReading(): bool
    {
        return $this->allow_student_reading;
    }

    public function allowsTeacherReading(): bool
    {
        return $this->allow_teacher_reading;
    }

    public function allowsTeacherAssignment(): bool
    {
        return $this->allow_teacher_assignment;
    }

    public function allowsStudentBorrowing(): bool
    {
        return $this->allow_student_borrowing;
    }

    public function allowsPrint(): bool
    {
        return $this->allow_print;
    }

    public function allowsDownload(): bool
    {
        return $this->allow_download;
    }
}