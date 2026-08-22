<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;


    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',

        'education_level',
        'institution_name',

        'county',
        'town',

        'date_of_birth',

        'admission_number',
    ];


    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'user_id' =>
                'integer',

            'date_of_birth' =>
                'date',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasAdmissionNumber(): bool
    {
        return filled(
            $this->admission_number
        );
    }


    public function hasInstitution(): bool
    {
        return filled(
            $this->institution_name
        );
    }


    public function location(): ?string
    {
        $parts = array_filter([
            $this->town,
            $this->county,
        ]);


        if (empty($parts)) {
            return null;
        }


        return implode(
            ', ',
            $parts
        );
    }


    public function academicIdentity(): string
    {
        if (
            filled(
                $this->admission_number
            )
        ) {
            return $this->admission_number;
        }


        return (string) $this->user_id;
    }
}