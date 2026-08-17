<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class)
            ->withPivot([
                'role',
                'status',
                'reference_number',
            ])
            ->withTimestamps();
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function isPlatformUser(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'platform_admin',
            'content_manager',
            'author',
            'finance',
            'support',
        ]);
    }

    public function isSchoolAdmin(): bool
    {
        return $this->hasRole('school_admin');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function isLearner(): bool
    {
        return $this->hasAnyRole([
            'student',
            'individual_subscriber',
        ]);
    }

    public function studentClasses(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                SchoolClass::class,
                'class_student',
                'user_id',
                'school_class_id'
            )
            ->withPivot('stream_id')
            ->withTimestamps();
    }

    public function teachingClasses(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                SchoolClass::class,
                'class_teacher',
                'user_id',
                'school_class_id'
            )
            ->withTimestamps();
    }

    public function authorProfile(): HasOne
    {
        return $this->hasOne(
            Author::class
        );
    }

    public function uploadedBooks(): HasMany
    {
        return $this->hasMany(
            Book::class,
            'uploaded_by'
        );
    }

    public function bookBorrowings(): HasMany
    {
        return $this->hasMany(
            BookBorrowing::class
        );
    }

    public function bookBookmarks(): HasMany
    {
        return $this->hasMany(
            BookBookmark::class
        );
    }

    public function bookAccessRequests(): HasMany
    {
        return $this->hasMany(
            BookAccessRequest::class,
            'student_id'
        );
    }

    public function bookAccessRequestsToReview(): HasMany
    {
        return $this->hasMany(
            BookAccessRequest::class,
            'teacher_id'
        );
    }

    public function reviewedBooks(): HasMany
    {
        return $this->hasMany(
            Book::class,
            'reviewed_by'
        );
    }
    public function registeredDevices(): HasMany
    {
        return $this->hasMany(
            RegisteredDevice::class
        );
    }


    public function readerSessions(): HasMany
    {
        return $this->hasMany(
            ReaderSession::class
        );
    }


    public function readingActivities(): HasMany
    {
        return $this->hasMany(
            ReadingActivity::class
        );
    }


    public function securityEvents(): HasMany
    {
        return $this->hasMany(
            SecurityEvent::class
        );
    }
}