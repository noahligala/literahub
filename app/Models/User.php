<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;


    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];


    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];


    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Schools
    |--------------------------------------------------------------------------
    */

    public function schools(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                School::class
            )
            ->withPivot([
                'role',
                'status',
                'reference_number',
            ])
            ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Student Profile
    |--------------------------------------------------------------------------
    */

    public function studentProfile(): HasOne
    {
        return $this->hasOne(
            StudentProfile::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Classes
    |--------------------------------------------------------------------------
    */

    public function studentClasses(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                SchoolClass::class,
                'class_student',
                'user_id',
                'school_class_id'
            )
            ->withPivot([
                'stream_id',
            ])
            ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Teacher Classes
    |--------------------------------------------------------------------------
    |
    | Standardized as teacherClasses().
    |
    | Controllers should use this relationship when determining
    | which classes a teacher is permitted to manage or assign work to.
    |
    */

    public function teacherClasses(): BelongsToMany
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


    /*
    |--------------------------------------------------------------------------
    | Assigned Student Assignments
    |--------------------------------------------------------------------------
    |
    | assignment_student represents distribution:
    | which assignments have been assigned to this learner.
    |
    */

    public function assignments(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Assignment::class,
                'assignment_student',
                'user_id',
                'assignment_id'
            )
            ->withPivot([
                'status',
                'score',
                'submitted_at',
            ])
            ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Assignment Submissions
    |--------------------------------------------------------------------------
    */

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(
            AssignmentSubmission::class,
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Assignment Submissions Graded By User
    |--------------------------------------------------------------------------
    */

    public function gradedAssignmentSubmissions(): HasMany
    {
        return $this->hasMany(
            AssignmentSubmission::class,
            'graded_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Author Profile
    |--------------------------------------------------------------------------
    */

    public function authorProfile(): HasOne
    {
        return $this->hasOne(
            Author::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Uploaded Books
    |--------------------------------------------------------------------------
    */

    public function uploadedBooks(): HasMany
    {
        return $this->hasMany(
            Book::class,
            'uploaded_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reviewed Books
    |--------------------------------------------------------------------------
    */

    public function reviewedBooks(): HasMany
    {
        return $this->hasMany(
            Book::class,
            'reviewed_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Book Borrowings
    |--------------------------------------------------------------------------
    */

    public function bookBorrowings(): HasMany
    {
        return $this->hasMany(
            BookBorrowing::class,
            'user_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Book Bookmarks
    |--------------------------------------------------------------------------
    */

    public function bookBookmarks(): HasMany
    {
        return $this->hasMany(
            BookBookmark::class,
            'user_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Book Access Requests
    |--------------------------------------------------------------------------
    */

    public function bookAccessRequests(): HasMany
    {
        return $this->hasMany(
            BookAccessRequest::class,
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Book Access Requests Reviewed By Teacher
    |--------------------------------------------------------------------------
    */

    public function bookAccessRequestsToReview(): HasMany
    {
        return $this->hasMany(
            BookAccessRequest::class,
            'teacher_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Registered Reader Devices
    |--------------------------------------------------------------------------
    */

    public function registeredDevices(): HasMany
    {
        return $this->hasMany(
            RegisteredDevice::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reader Sessions
    |--------------------------------------------------------------------------
    */

    public function readerSessions(): HasMany
    {
        return $this->hasMany(
            ReaderSession::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reading Activities
    |--------------------------------------------------------------------------
    */

    public function readingActivities(): HasMany
    {
        return $this->hasMany(
            ReadingActivity::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Security Events
    |--------------------------------------------------------------------------
    */

    public function securityEvents(): HasMany
    {
        return $this->hasMany(
            SecurityEvent::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

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
        return $this->hasRole(
            'school_admin'
        );
    }


    public function isTeacher(): bool
    {
        return $this->hasRole(
            'teacher'
        );
    }


    public function isStudent(): bool
    {
        return $this->hasRole(
            'student'
        );
    }


    public function isIndividualSubscriber(): bool
    {
        return $this->hasRole(
            'individual_subscriber'
        );
    }


    public function isLearner(): bool
    {
        return $this->hasAnyRole([
            'student',
            'individual_subscriber',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | School Membership Helpers
    |--------------------------------------------------------------------------
    */

    public function activeSchools(): BelongsToMany
    {
        return $this
            ->schools()
            ->wherePivot(
                'status',
                'active'
            );
    }


    public function belongsToSchool(
        School|int $school
    ): bool {
        $schoolId = $school instanceof School
            ? $school->id
            : $school;


        return $this
            ->schools()
            ->where(
                'schools.id',
                $schoolId
            )
            ->wherePivot(
                'status',
                'active'
            )
            ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | Class Membership Helpers
    |--------------------------------------------------------------------------
    */

    public function belongsToStudentClass(
        SchoolClass|int $class
    ): bool {
        $classId = $class instanceof SchoolClass
            ? $class->id
            : $class;


        return $this
            ->studentClasses()
            ->where(
                'school_classes.id',
                $classId
            )
            ->exists();
    }


    public function teachesClass(
        SchoolClass|int $class
    ): bool {
        $classId = $class instanceof SchoolClass
            ? $class->id
            : $class;


        return $this
            ->teacherClasses()
            ->where(
                'school_classes.id',
                $classId
            )
            ->exists();
    }
}