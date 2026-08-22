<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'late_submission_policy',
        'late_penalty_type',
        'late_penalty_value',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'due_at' => 'datetime',

            'start_page' => 'integer',
            'end_page' => 'integer',
            'total_marks' => 'integer',
            'late_penalty_value' => 'decimal:2',
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

    public function submissions(): HasMany
    {
        return $this->hasMany(
            AssignmentSubmission::class
        );
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isOverdue(): bool
    {
        return $this->due_at
            && now()->greaterThan(
                $this->due_at
            );
    }

    public function hasStarted(): bool
    {
        return ! $this->starts_at
            || now()->greaterThanOrEqualTo(
                $this->starts_at
            );
    }

    public function acceptsSubmissions(): bool
    {
        return $this->isPublished()
            && ! $this->isClosed()
            && $this->hasStarted();
    }

    public function allowsLateSubmissions(): bool
    {
        return $this->late_submission_policy !== 'reject';
    }


    public function rejectsLateSubmissions(): bool
    {
        return $this->late_submission_policy === 'reject';
    }


    public function penalizesLateSubmissions(): bool
    {
        return $this->late_submission_policy === 'allow_with_penalty';
    }


    public function isPastDeadline(): bool
    {
        return $this->due_at
            && now()->greaterThan($this->due_at);
    }


    public function acceptsStudentSubmission(): bool
    {
        if (! $this->isPublished()) {
            return false;
        }

        if (! $this->hasStarted()) {
            return false;
        }

        if (
            $this->isPastDeadline()
            && $this->rejectsLateSubmissions()
        ) {
            return false;
        }

        return true;
    }
}