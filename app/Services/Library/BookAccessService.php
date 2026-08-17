<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\BookAccessRequest;
use App\Models\BookBorrowing;
use App\Models\School;
use App\Models\SchoolBookLicense;
use App\Models\User;

class BookAccessService
{
    public function __construct(
        private readonly BookLicenseService $licenses,
        private readonly BookRightsService $rights
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Can View Metadata
    |--------------------------------------------------------------------------
    */

    public function canView(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        if ($this->isPlatformStaff($user)) {
            return true;
        }

        if ($this->isBookAuthor(
            $user,
            $book
        )) {
            return true;
        }

        /*
         * Teachers can preview review-state material.
         */
        if (
            $user->hasRole('teacher')
            && in_array(
                $book->status,
                [
                    'under_review',
                    'approved',
                    'published',
                ],
                true
            )
        ) {
            return true;
        }

        if (!$school) {
            return false;
        }

        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $book
                );

        if (!$license) {
            return false;
        }

        if ($user->hasRole('school_admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $book->status === 'published';
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Can Read Book
    |--------------------------------------------------------------------------
    */

    public function canRead(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        /*
         * Platform review staff.
         */
        if ($this->isPlatformStaff($user)) {
            return true;
        }

        /*
         * Author always has preview access to own book.
         */
        if ($this->isBookAuthor(
            $user,
            $book
        )) {
            return true;
        }

        /*
         * Teachers may preview content during review.
         */
        if (
            $user->hasRole('teacher')
            && in_array(
                $book->status,
                [
                    'under_review',
                    'approved',
                ],
                true
            )
        ) {
            return true;
        }

        if (!$school) {
            return false;
        }

        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $book
                );

        if (!$license) {
            return false;
        }

        /*
         * School admins may inspect licensed content.
         */
        if ($user->hasRole('school_admin')) {
            return $book->allow_online_reading;
        }

        /*
         * Teacher reading.
         */
        if ($user->hasRole('teacher')) {
            if (
                !in_array(
                    $book->status,
                    [
                        'under_review',
                        'approved',
                        'published',
                    ],
                    true
                )
            ) {
                return false;
            }

            return
                $book->allow_online_reading
                && $license->allow_teacher_reading;
        }

        /*
         * Student reading.
         */
        if ($user->hasRole('student')) {
            return $this->studentCanRead(
                $user,
                $book,
                $school,
                $license
            );
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Student Access
    |--------------------------------------------------------------------------
    */

    private function studentCanRead(
        User $student,
        Book $book,
        School $school,
        SchoolBookLicense $license
    ): bool {
        if ($book->status !== 'published') {
            return false;
        }

        if (!$book->allow_online_reading) {
            return false;
        }

        if (!$license->allow_student_reading) {
            return false;
        }

        /*
         * Class entitlement.
         */
        if (
            $this->studentHasClassAccess(
                $student,
                $book
            )
        ) {
            return true;
        }

        /*
         * Explicit teacher approval.
         */
        return $this->hasApprovedAccessRequest(
            $student,
            $book,
            $school
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Class Entitlement
    |--------------------------------------------------------------------------
    */

    public function studentHasClassAccess(
        User $student,
        Book $book
    ): bool {
        $classIds =
            $student
                ->studentClasses()
                ->pluck(
                    'school_classes.id'
                );

        if ($classIds->isEmpty()) {
            return false;
        }

        return $book
            ->classes()
            ->whereIn(
                'school_classes.id',
                $classIds
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull(
                            'book_class.available_from'
                        )
                        ->orWhere(
                            'book_class.available_from',
                            '<=',
                            now()
                        );
                }
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull(
                            'book_class.available_until'
                        )
                        ->orWhere(
                            'book_class.available_until',
                            '>',
                            now()
                        );
                }
            )
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Approved Access Request
    |--------------------------------------------------------------------------
    */

    public function hasApprovedAccessRequest(
        User $student,
        Book $book,
        School $school
    ): bool {
        return BookAccessRequest::query()
            ->where(
                'student_id',
                $student->id
            )
            ->where(
                'book_id',
                $book->id
            )
            ->where(
                'school_id',
                $school->id
            )
            ->where(
                'status',
                'approved'
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull(
                            'expires_at'
                        )
                        ->orWhere(
                            'expires_at',
                            '>',
                            now()
                        );
                }
            )
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Can Borrow
    |--------------------------------------------------------------------------
    */

    public function canBorrow(
        User $user,
        Book $book,
        School $school
    ): bool {
        if (!$user->hasRole('student')) {
            return false;
        }

        if (!$this->canRead(
            $user,
            $book,
            $school
        )) {
            return false;
        }

        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $book
                );

        if (!$license) {
            return false;
        }

        if (!$this->rights->canBorrow(
            $book,
            $license
        )) {
            return false;
        }

        return !BookBorrowing::query()
            ->where(
                'book_id',
                $book->id
            )
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                'borrowed'
            )
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Can Download
    |--------------------------------------------------------------------------
    */

    public function canDownload(
        User $user,
        Book $book,
        School $school
    ): bool {
        if (!$this->canRead(
            $user,
            $book,
            $school
        )) {
            return false;
        }

        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $book
                );

        return $this->rights->canDownload(
            $book,
            $license
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Can Print
    |--------------------------------------------------------------------------
    */

    public function canPrint(
        User $user,
        Book $book,
        School $school
    ): bool {
        if (!$this->canRead(
            $user,
            $book,
            $school
        )) {
            return false;
        }

        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $book
                );

        return $this->rights->canPrint(
            $book,
            $license
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Can Assign
    |--------------------------------------------------------------------------
    */

    public function canAssign(
        User $user,
        Book $book,
        School $school
    ): bool {
        if (
            !$user->hasAnyRole([
                'teacher',
                'school_admin',
            ])
        ) {
            return false;
        }

        if ($book->status !== 'published') {
            return false;
        }

        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $book
                );

        return $this->rights
            ->canTeacherAssign(
                $book,
                $license
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function isPlatformStaff(
        User $user
    ): bool {
        return $user->hasAnyRole([
            'super_admin',
            'platform_admin',
            'content_manager',
        ]);
    }

    private function isBookAuthor(
        User $user,
        Book $book
    ): bool {
        if (!$user->hasRole('author')) {
            return false;
        }

        return $book
            ->authors()
            ->where(
                'user_id',
                $user->id
            )
            ->exists();
    }
}