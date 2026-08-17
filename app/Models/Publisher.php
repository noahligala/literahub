<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'registration_number',
        'email',
        'phone',
        'website',
        'address',
        'description',
        'logo_path',
        'status',
    ];

    public function authors(): HasMany
    {
        return $this->hasMany(
            Author::class
        );
    }

    public function books(): HasMany
    {
        return $this->hasMany(
            Book::class
        );
    }

    public function schoolBookLicenses(): HasMany
    {
        return $this->hasMany(
            SchoolBookLicense::class
        );
    }
}