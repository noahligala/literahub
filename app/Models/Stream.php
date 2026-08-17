<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id',
        'teacher_id',
        'name',
        'status',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(
            SchoolClass::class
        );
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'teacher_id'
        );
    }
}