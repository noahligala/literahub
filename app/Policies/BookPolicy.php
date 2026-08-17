<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\School;
use App\Models\User;
use App\Services\Library\BookAccessService;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    public function __construct(
        private readonly BookAccessService $access
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Global Override
    |--------------------------------------------------------------------------
    */

    public function before(
        User $user,
        string $ability
    ): ?bool {
        if (
            $user->hasAnyRole([
                'super_admin',
                'platform_admin',
            ])
        ) {
            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View Any
    |--------------------------------------------------------------------------
    */

    public function viewAny(
        User $user
    ): bool {
        return $user->hasAnyRole([
            'content_manager',
            'author',
            'school_admin',
            'teacher',
            'student',
            'individual_subscriber',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        Book $book
    ): bool {
        /*
         * Content managers may inspect all submitted books.
         */
        if ($user->hasRole('content_manager')) {
            return true;
        }

        /*
         * Authors may always inspect their own works.
         */
        if (
            $user->hasRole('author')
            && $book
                ->authors()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists()
        ) {
            return true;
        }

        $school = $this->schoolFor(
            $user
        );

        return $this->access->canView(
            $user,
            $book,
            $school
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {
        return $user->hasAnyRole([
            'content_manager',
            'author',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        User $user,
        Book $book
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        if (!$user->hasRole('author')) {
            return false;
        }

        $ownsBook = $book
            ->authors()
            ->where(
                'user_id',
                $user->id
            )
            ->exists();

        if (!$ownsBook) {
            return false;
        }

        /*
         * Authors should not edit an already published
         * work directly. Changes should normally trigger
         * a new review cycle.
         */
        return in_array(
            $book->status,
            [
                'draft',
                'changes_requested',
                'rejected',
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        User $user,
        Book $book
    ): bool {
        if ($user->hasRole('content_manager')) {
            return !in_array(
                $book->status,
                [
                    'published',
                    'approved',
                ],
                true
            );
        }

        if (!$user->hasRole('author')) {
            return false;
        }

        $ownsBook = $book
            ->authors()
            ->where(
                'user_id',
                $user->id
            )
            ->exists();

        return
            $ownsBook
            && in_array(
                $book->status,
                [
                    'draft',
                    'rejected',
                ],
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Submit for Review
    |--------------------------------------------------------------------------
    */

    public function submitForReview(
        User $user,
        Book $book
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        if (!$user->hasRole('author')) {
            return false;
        }

        $ownsBook = $book
            ->authors()
            ->where(
                'user_id',
                $user->id
            )
            ->exists();

        return
            $ownsBook
            && in_array(
                $book->status,
                [
                    'draft',
                    'changes_requested',
                ],
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Review
    |--------------------------------------------------------------------------
    */

    public function review(
        User $user,
        Book $book
    ): bool {
        return $user->hasRole(
            'content_manager'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Publish
    |--------------------------------------------------------------------------
    */

    public function publish(
        User $user,
        Book $book
    ): bool {
        if (!$user->hasRole('content_manager')) {
            return false;
        }

        return in_array(
            $book->status,
            [
                'approved',
                'under_review',
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Read
    |--------------------------------------------------------------------------
    */

    public function read(
        User $user,
        Book $book
    ): bool {
        $school = $this->schoolFor(
            $user
        );

        return $this->access->canRead(
            $user,
            $book,
            $school
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Borrow
    |--------------------------------------------------------------------------
    */

    public function borrow(
        User $user,
        Book $book
    ): bool {
        $school = $this->schoolFor(
            $user
        );

        if (!$school) {
            return false;
        }

        return $this->access->canBorrow(
            $user,
            $book,
            $school
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Download
    |--------------------------------------------------------------------------
    */

    public function download(
        User $user,
        Book $book
    ): bool {
        $school = $this->schoolFor(
            $user
        );

        if (!$school) {
            return false;
        }

        return $this->access->canDownload(
            $user,
            $book,
            $school
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Print
    |--------------------------------------------------------------------------
    */

    public function print(
        User $user,
        Book $book
    ): bool {
        $school = $this->schoolFor(
            $user
        );

        if (!$school) {
            return false;
        }

        return $this->access->canPrint(
            $user,
            $book,
            $school
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assign to Class
    |--------------------------------------------------------------------------
    */

    public function assign(
        User $user,
        Book $book
    ): bool {
        $school = $this->schoolFor(
            $user
        );

        if (!$school) {
            return false;
        }

        return $this->access->canAssign(
            $user,
            $book,
            $school
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve School
    |--------------------------------------------------------------------------
    */

    private function schoolFor(
        User $user
    ): ?School {
        return $user
            ->schools()
            ->first();
    }
}