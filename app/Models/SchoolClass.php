<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_teacher_id',
        'name',
        'code',
        'level',
        'academic_year',
        'status',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function students(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                User::class,
                'class_student',
                'school_class_id',
                'user_id'
            )
            ->withPivot('stream_id')
            ->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                User::class,
                'class_teacher',
                'school_class_id',
                'user_id'
            )
            ->withTimestamps();
    }

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'class_teacher_id'
        );
    }
}