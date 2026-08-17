<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'registration_number',
        'type',
        'county',
        'town',
        'email',
        'phone',
        'status',
        'student_limit',
    ];

    protected function casts(): array
    {
        return [
            'student_limit' => 'integer',
        ];
    }

     public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'role',
                'status',
                'reference_number',
            ])
            ->withTimestamps();
        }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function administrators(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('role', 'school_admin')
            ->wherePivot('status', 'active');
    }

    public function teachers(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('role', 'teacher')
            ->wherePivot('status', 'active');
    }

    public function students(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('role', 'student')
            ->wherePivot('status', 'active');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function bookLicenses(): HasMany
    {
        return $this->hasMany(
            SchoolBookLicense::class
        );
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(
            BookBorrowing::class
        );
    }

    public function bookAccessRequests(): HasMany
    {
        return $this->hasMany(
            BookAccessRequest::class
        );
    }

    // public function licensedBooks()
    // {
    //     return Book::query()
    //         ->whereHas(
    //             'licenses',
    //             function ($query) {
    //                 $query
    //                     ->where(
    //                         'school_id',
    //                         $this->id
    //                     )
    //                     ->where(
    //                         'status',
    //                         'active'
    //                     )
    //                     ->where(
    //                         'starts_at',
    //                         '<=',
    //                         now()
    //                     )
    //                     ->where(
    //                         function ($query) {
    //                             $query
    //                                 ->whereNull(
    //                                     'expires_at'
    //                                 )
    //                                 ->orWhere(
    //                                     'expires_at',
    //                                     '>',
    //                                     now()
    //                                 );
    //                         }
    //                     );
    //             }
    //         );
    // }
}