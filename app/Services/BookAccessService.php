<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookAccessRequest;
use App\Models\School;
use App\Models\SchoolBookLicense;
use App\Models\User;
use Carbon\CarbonInterface;

class BookAccessService
{
    /**
     * Determine whether a user can see a book record.
     *
     * Platform staff and the book's author/uploader may see catalogue
     * records without a school licence.
     *
     * School users must belong to a school with an active licence.
     */
    public function canView(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        if ($this->isPlatformStaff($user)) {
            return true;
        }

        if ($this->isBookOwner($user, $book)) {
            return true;
        }

        if (! $school) {
            return $this->isIndividualSubscriber($user)
                && $this->individualEntitlementAllows($user, $book);
        }

        return $this->activeLicense(
            $school,
            $book
        ) !== null;
    }


    /**
     * Determine whether a user may read a book online.
     */
    public function canRead(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        /*
        |--------------------------------------------------------------------------
        | Platform review/admin access
        |--------------------------------------------------------------------------
        */

        if ($this->isPlatformStaff($user)) {
            return $this->bookAllowsReading($book);
        }


        /*
        |--------------------------------------------------------------------------
        | Author / uploader preview
        |--------------------------------------------------------------------------
        */

        if ($this->isBookOwner($user, $book)) {
            return $this->bookAllowsReading($book);
        }


        /*
        |--------------------------------------------------------------------------
        | Individual subscriber
        |--------------------------------------------------------------------------
        */

        if (! $school) {
            return $this->isIndividualSubscriber($user)
                && $this->individualEntitlementAllows($user, $book)
                && $this->bookAllowsReading($book);
        }


        /*
        |--------------------------------------------------------------------------
        | School users MUST have a valid licence
        |--------------------------------------------------------------------------
        */

        $license = $this->activeLicense(
            $school,
            $book
        );

        if (! $license) {
            return false;
        }


        if (! $this->bookAllowsReading($book)) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | School Admin
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('school_admin')) {
            return (bool) (
                $license->allow_teacher_reading
                || $license->allow_student_reading
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('teacher')) {
            return (bool)
                $license->allow_teacher_reading;
        }


        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('student')) {
            if (! $license->allow_student_reading) {
                return false;
            }


            /*
             * Student may read if:
             *
             * 1. the book is assigned to one of their classes; OR
             * 2. they have an approved, non-expired access request; OR
             * 3. they currently have an active borrowing.
             */

            if (
                $this->studentHasClassAccess(
                    $user,
                    $book
                )
            ) {
                return true;
            }


            if (
                $this->studentHasApprovedRequest(
                    $user,
                    $book,
                    $school
                )
            ) {
                return true;
            }


            if (
                $this->studentHasActiveBorrowing(
                    $user,
                    $book,
                    $school
                )
            ) {
                return true;
            }


            return false;
        }


        return false;
    }


    /**
     * Determine whether the user may borrow the book.
     */
    public function canBorrow(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        if (! $user->hasRole('student')) {
            return false;
        }

        if (! $school) {
            return false;
        }

        if (! $book->allow_student_borrowing) {
            return false;
        }

        $license = $this->activeLicense(
            $school,
            $book
        );

        if (! $license) {
            return false;
        }

        if (! $license->allow_student_borrowing) {
            return false;
        }


        /*
         * A student should still require normal entitlement:
         * class assignment or approved access request.
         */

        return $this->studentHasClassAccess(
            $user,
            $book
        )
            ||
            $this->studentHasApprovedRequest(
                $user,
                $book,
                $school
            );
    }


    /**
     * Determine whether a teacher may assign a book to a class.
     */
    public function canAssign(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        if (
            ! $user->hasAnyRole([
                'teacher',
                'school_admin',
            ])
        ) {
            return false;
        }

        if (! $school) {
            return false;
        }

        if (! $book->allow_teacher_assignment) {
            return false;
        }

        if ($book->status !== 'published') {
            return false;
        }

        $license = $this->activeLicense(
            $school,
            $book
        );

        if (! $license) {
            return false;
        }

        return (bool)
            $license->allow_teacher_assignment;
    }


    /**
     * Determine whether downloading is permitted.
     */
    public function canDownload(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        if (! $book->allow_download) {
            return false;
        }


        /*
         * Platform staff may download only if the rights holder
         * explicitly allows downloading.
         */

        if ($this->isPlatformStaff($user)) {
            return true;
        }


        if ($this->isBookOwner($user, $book)) {
            return true;
        }


        if (! $school) {
            return false;
        }


        $license = $this->activeLicense(
            $school,
            $book
        );

        if (! $license) {
            return false;
        }


        if (! $license->allow_download) {
            return false;
        }


        return $this->canRead(
            $user,
            $book,
            $school
        );
    }


    /**
     * Determine whether printing is permitted.
     */
    public function canPrint(
        User $user,
        Book $book,
        ?School $school = null
    ): bool {
        if (! $book->allow_print) {
            return false;
        }


        if ($this->isPlatformStaff($user)) {
            return true;
        }


        if ($this->isBookOwner($user, $book)) {
            return true;
        }


        if (! $school) {
            return false;
        }


        $license = $this->activeLicense(
            $school,
            $book
        );

        if (! $license) {
            return false;
        }


        if (! $license->allow_print) {
            return false;
        }


        return $this->canRead(
            $user,
            $book,
            $school
        );
    }


    /**
     * Return the active licence for a school/book pair.
     */
    public function activeLicense(
        School $school,
        Book $book
    ): ?SchoolBookLicense {
        return SchoolBookLicense::query()
            ->where(
                'school_id',
                $school->id
            )
            ->where(
                'book_id',
                $book->id
            )
            ->whereIn(
                'status',
                [
                    'active',
                    'trial',
                ]
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull('starts_at')
                        ->orWhere(
                            'starts_at',
                            '<=',
                            now()
                        );
                }
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull('expires_at')
                        ->orWhere(
                            'expires_at',
                            '>',
                            now()
                        );
                }
            )
            ->latest('id')
            ->first();
    }


    /**
     * Check whether a student belongs to a class assigned this book.
     */
    private function studentHasClassAccess(
        User $user,
        Book $book
    ): bool {
        /*
         * Expected relationship:
         *
         * Book::classes()
         * Student User::studentClasses()
         *
         * We compare using the class_student relationship already
         * present in the User model.
         */

        $studentClassIds =
            $user
                ->studentClasses()
                ->pluck('school_classes.id');


        if ($studentClassIds->isEmpty()) {
            return false;
        }


        return $book
            ->classes()
            ->whereIn(
                'school_classes.id',
                $studentClassIds
            )
            ->exists();
    }


    /**
     * Check approved out-of-scope access.
     */
    private function studentHasApprovedRequest(
        User $user,
        Book $book,
        School $school
    ): bool {
        return BookAccessRequest::query()
            ->where(
                'student_id',
                $user->id
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
                        ->whereNull('expires_at')
                        ->orWhere(
                            'expires_at',
                            '>',
                            now()
                        );
                }
            )
            ->exists();
    }


    /**
     * Check active digital borrowing.
     */
    private function studentHasActiveBorrowing(
        User $user,
        Book $book,
        School $school
    ): bool {
        return $book
            ->borrowings()
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'school_id',
                $school->id
            )
            ->where(
                'status',
                'active'
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull('due_at')
                        ->orWhere(
                            'due_at',
                            '>',
                            now()
                        );
                }
            )
            ->exists();
    }


    /**
     * Platform-level catalogue staff.
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


    /**
     * Determine whether this user owns/uploaded the book.
     */
    private function isBookOwner(
        User $user,
        Book $book
    ): bool {
        /*
         * Uploader is always permitted to preview their own work.
         */

        if (
            (int) $book->uploaded_by
            ===
            (int) $user->id
        ) {
            return true;
        }


        /*
         * If the user has an Author profile, check the book's
         * author relationship.
         */

        if (
            $user->relationLoaded('authorProfile')
            ? $user->authorProfile
            : $user->authorProfile()->exists()
        ) {
            $authorId =
                $user->authorProfile?->id
                ??
                $user
                    ->authorProfile()
                    ->value('id');


            if (
                $authorId
                &&
                $book
                    ->authors()
                    ->where(
                        'authors.id',
                        $authorId
                    )
                    ->exists()
            ) {
                return true;
            }
        }


        return false;
    }


    /**
     * Book-level online reading right.
     */
    private function bookAllowsReading(
        Book $book
    ): bool {
        return (bool)
            $book->allow_online_reading;
    }


    /**
     * Individual subscription role.
     */
    private function isIndividualSubscriber(
        User $user
    ): bool {
        return $user->hasRole(
            'individual_subscriber'
        );
    }


    /**
     * Individual subscriber entitlements are not yet fully implemented.
     *
     * For now, only published books are potentially eligible.
     * This prevents us from accidentally granting unrestricted catalogue
     * access while the subscription module is still unfinished.
     */
    private function individualEntitlementAllows(
        User $user,
        Book $book
    ): bool {
        return false;
    }
}