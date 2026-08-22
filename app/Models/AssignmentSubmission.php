<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',

        'response',

        'status',
        'submitted_at',

        'score',
        'feedback',

        'graded_at',
        'graded_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(
            Assignment::class
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'graded_by'
        );
    }

    public function isSubmitted(): bool
    {
        return in_array(
            $this->status,
            [
                'submitted',
                'late',
                'graded',
            ],
            true
        );
    }

    public function isGraded(): bool
    {
        return $this->status
            === 'graded';
    }
}