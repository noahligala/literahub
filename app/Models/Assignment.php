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
        'due_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
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
}