<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'school_class_id',
        'creator_id',
        'resource_id',

        'title',
        'instructions',

        'starts_at',
        'due_at',

        'start_page',
        'end_page',

        'total_marks',

        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' =>
                'datetime',

            'due_at' =>
                'datetime',

            'start_page' =>
                'integer',

            'end_page' =>
                'integer',

            'total_marks' =>
                'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(
            SchoolClass::class
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'creator_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assigned Book
    |--------------------------------------------------------------------------
    |
    | resource_id currently stores the Book ID.
    |
    | Later we can rename the database column to book_id.
    |
    */

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class,
            'resource_id'
        );
    }

    public function students(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                User::class,
                'assignment_student'
            )
            ->withPivot([
                'status',
                'score',
                'submitted_at',
            ])
            ->withTimestamps();
    }

    public function isPublished(): bool
    {
        return $this->status
            === 'published';
    }

    public function isClosed(): bool
    {
        return $this->status
            === 'closed';
    }

    public function isOverdue(): bool
    {
        return $this->due_at
            && now()->greaterThan(
                $this->due_at
            );
    }
}