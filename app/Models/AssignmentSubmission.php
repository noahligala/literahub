<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;


    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

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
        'attachment_path',
        'attachment_original_name',
        'attachment_mime_type',
        'attachment_size',
        'raw_score',
        'late_penalty',
        'late_penalty_note',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'assignment_id' =>
                'integer',

            'student_id' =>
                'integer',

            'submitted_at' =>
                'datetime',

            'score' =>
                'integer',

            'graded_at' =>
                'datetime',

            'graded_by' =>
                'integer',
                           
            'attachment_size' => 'integer',
            'raw_score' => 'integer',
            'late_penalty' => 'decimal:2',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Assignment
    |--------------------------------------------------------------------------
    */

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(
            Assignment::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Grader
    |--------------------------------------------------------------------------
    */

    public function grader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'graded_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === 'draft';
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


    public function isLate(): bool
    {
        return $this->status === 'late';
    }


    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }


    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }
}