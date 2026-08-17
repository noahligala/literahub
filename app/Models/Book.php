<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'publisher_id',
        'uploaded_by',

        'title',
        'slug',
        'isbn',
        'edition',
        'publication_year',
        'language',
        'category',
        'description',

        'cover_path',
        'pdf_path',
        'page_count',
        'file_size',
        'file_hash',

        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'review_notes',

        'allow_online_reading',
        'allow_download',
        'allow_print',
        'allow_teacher_assignment',
        'allow_student_borrowing',

        'loan_days',
        'max_concurrent_loans',

        'rights_statement',

        'storage_uuid',
        'original_pdf_path',
        'processing_status',
        'processed_page_count',
        'render_version',
        'processing_started_at',
        'processing_completed_at',
        'processing_failed_at',
        'processing_error',
        'source_checksum',
    ];

    protected function casts(): array
    {
        return [
            'publication_year' =>
                'integer',

            'page_count' =>
                'integer',

            'file_size' =>
                'integer',

            'submitted_at' =>
                'datetime',

            'reviewed_at' =>
                'datetime',

            'allow_online_reading' =>
                'boolean',

            'allow_download' =>
                'boolean',

            'allow_print' =>
                'boolean',

            'allow_teacher_assignment' =>
                'boolean',

            'allow_student_borrowing' =>
                'boolean',

            'loan_days' =>
                'integer',

            'max_concurrent_loans' =>
                'integer',
                'processed_page_count' => 'integer',
                'render_version' => 'integer',
                'processing_started_at' => 'datetime',
                'processing_completed_at' => 'datetime',
                'processing_failed_at' => 'datetime',
                        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Publisher
    |--------------------------------------------------------------------------
    */

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(
            Publisher::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Uploader
    |--------------------------------------------------------------------------
    */

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reviewer
    |--------------------------------------------------------------------------
    */

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Authors
    |--------------------------------------------------------------------------
    */

    public function authors(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Author::class,
                'author_book'
            )
            ->withPivot([
                'contribution',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Classes
    |--------------------------------------------------------------------------
    */

    public function classes(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                SchoolClass::class,
                'book_class'
            )
            ->withPivot([
                'assigned_by',
                'available_from',
                'available_until',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Licences
    |--------------------------------------------------------------------------
    */

    public function licenses(): HasMany
    {
        return $this->hasMany(
            SchoolBookLicense::class
        );
    }

    public function licensesForSchool(
        int $schoolId
    ): HasMany {
        return $this
            ->licenses()
            ->where(
                'school_id',
                $schoolId
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Borrowings
    |--------------------------------------------------------------------------
    */

    public function borrowings(): HasMany
    {
        return $this->hasMany(
            BookBorrowing::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bookmarks
    |--------------------------------------------------------------------------
    */

    public function bookmarks(): HasMany
    {
        return $this->hasMany(
            BookBookmark::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Access Requests
    |--------------------------------------------------------------------------
    */

    public function accessRequests(): HasMany
    {
        return $this->hasMany(
            BookAccessRequest::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function allowsOnlineReading(): bool
    {
        return $this->allow_online_reading;
    }

    public function allowsDownload(): bool
    {
        return $this->allow_download;
    }

    public function allowsPrint(): bool
    {
        return $this->allow_print;
    }

    public function allowsTeacherAssignment(): bool
    {
        return $this->allow_teacher_assignment;
    }

    public function allowsStudentBorrowing(): bool
    {
        return $this->allow_student_borrowing;
    }

    public function pages(): HasMany
{
    return $this->hasMany(
        BookPage::class
    )->orderBy(
        'page_number'
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

// public function isProcessed(): bool
// {
//     return $this->processing_status === 'processed'
//         &&
//         $this->processed_page_count > 0;
// }


// public function isProcessing(): bool
// {
//     return $this->processing_status === 'processing';
// }


// public function hasProcessingFailed(): bool
// {
//     return $this->processing_status === 'failed';
// }

}