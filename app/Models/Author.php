<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = [
        'user_id',
        'publisher_id',
        'name',
        'slug',
        'biography',
        'photo_path',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(
            Publisher::class
        );
    }

    public function books(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Book::class,
                'author_book'
            )
            ->withPivot([
                'contribution',
            ])
            ->withTimestamps();
    }

    public function schoolBookLicenses(): HasMany
    {
        return $this->hasMany(
            SchoolBookLicense::class
        );
    }
}