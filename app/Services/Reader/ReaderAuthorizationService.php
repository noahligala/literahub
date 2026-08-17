<?php

namespace App\Services\Reader;

use App\Models\Book;
use App\Models\ReaderSession;
use App\Models\School;
use App\Models\User;
use App\Services\BookAccessService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReaderAuthorizationService
{
    public function __construct(
        private readonly BookAccessService $bookAccess
    ) {
    }


    /**
     * Authorize access to the book itself.
     *
     * @return array{
     *     school:?School,
     *     license:mixed
     * }
     */
    public function authorizeBook(
        User $user,
        Book $book,
        Request $request
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Active Account
        |--------------------------------------------------------------------------
        */

        if (
            isset($user->status)
            &&
            $user->status !== null
            &&
            $user->status !== 'active'
        ) {
            throw new HttpException(
                403,
                'Your account is not active.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Platform / Author Preview
        |--------------------------------------------------------------------------
        */

        if (
            $this->mayPreviewUnpublished(
                user: $user,
                book: $book
            )
        ) {
            if (
                ! $book->allow_online_reading
            ) {
                throw new HttpException(
                    403,
                    'Online reading is not permitted for this title.'
                );
            }


            return [
                'school' =>
                    null,

                'license' =>
                    null,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Ordinary readers require published content
        |--------------------------------------------------------------------------
        */

        if (
            $book->status !== 'published'
        ) {
            throw new HttpException(
                404,
                'This book is not available.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Resolve School
        |--------------------------------------------------------------------------
        */

        $school =
            $this->resolveSchool(
                $user
            );


        /*
        |--------------------------------------------------------------------------
        | Institutional User
        |--------------------------------------------------------------------------
        */

        if ($school) {
            $license =
                $this->bookAccess
                    ->activeLicense(
                        $school,
                        $book
                    );


            if (! $license) {
                throw new HttpException(
                    403,
                    'Your institution does not have an active licence for this title.'
                );
            }


            if (
                ! $this->bookAccess
                    ->canRead(
                        $user,
                        $book,
                        $school
                    )
            ) {
                throw new HttpException(
                    403,
                    'You do not currently have reading access to this title.'
                );
            }


            return [
                'school' =>
                    $school,

                'license' =>
                    $license,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Individual Subscriber
        |--------------------------------------------------------------------------
        |
        | BookAccessService currently denies this until the individual
        | subscription entitlement module is completed.
        |
        */

        if (
            $this->bookAccess
                ->canRead(
                    $user,
                    $book,
                    null
                )
        ) {
            return [
                'school' =>
                    null,

                'license' =>
                    null,
            ];
        }


        throw new HttpException(
            403,
            'You do not have an active entitlement for this title.'
        );
    }


    /**
     * Authorize a specific page request.
     */
    public function authorizePage(
        User $user,
        Book $book,
        ReaderSession $readerSession,
        int $page,
        Request $request
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Session Ownership
        |--------------------------------------------------------------------------
        */

        if (
            (int) $readerSession->user_id
            !==
            (int) $user->id
        ) {
            throw new HttpException(
                403,
                'Reader session does not belong to this user.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Book Binding
        |--------------------------------------------------------------------------
        */

        if (
            (int) $readerSession->book_id
            !==
            (int) $book->id
        ) {
            throw new HttpException(
                403,
                'Reader session is not valid for this book.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Session Status
        |--------------------------------------------------------------------------
        */

        if (! $readerSession->isActive()) {
            throw new HttpException(
                401,
                'Reader session has expired.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Page Range
        |--------------------------------------------------------------------------
        */

        if (
            $page < 1
            ||
            $page > $book->processed_page_count
        ) {
            throw new HttpException(
                404,
                'Page not found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Re-check entitlement on every page request
        |--------------------------------------------------------------------------
        |
        | This means revoking a school licence can stop subsequent
        | page retrieval instead of waiting for a browser session to end.
        |
        */

        $access =
            $this->authorizeBook(
                user: $user,
                book: $book,
                request: $request
            );


        $school =
            $access['school'];


        if (
            $readerSession->school_id !== null
            &&
            (
                ! $school
                ||
                (int) $readerSession->school_id
                    !==
                    (int) $school->id
            )
        ) {
            throw new HttpException(
                403,
                'Institutional reader entitlement has changed.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Device must remain valid
        |--------------------------------------------------------------------------
        */

        if (
            $readerSession->device
            &&
            $readerSession->device->revoked_at
        ) {
            throw new HttpException(
                403,
                'This device has been revoked.'
            );
        }
    }


    /**
     * Platform staff and the rights-owning author may preview
     * content before publication.
     */
    public function mayPreviewUnpublished(
        User $user,
        Book $book
    ): bool {
        if (
            $user->hasAnyRole([
                'super_admin',
                'platform_admin',
                'content_manager',
            ])
        ) {
            return true;
        }


        /*
         * Uploader.
         */
        if (
            (int) $book->uploaded_by
            ===
            (int) $user->id
        ) {
            return true;
        }


        /*
         * Linked Author profile.
         */
        if (
            method_exists(
                $user,
                'authorProfile'
            )
        ) {
            $authorId =
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
     * Resolve the user's current school.
     *
     * This remains first-active-school behaviour until LiteraHub
     * introduces explicit active-school context for multi-school users.
     */
    private function resolveSchool(
        User $user
    ): ?School {
        if (
            ! method_exists(
                $user,
                'schools'
            )
        ) {
            return null;
        }


        return $user
            ->schools()
            ->wherePivot(
                'status',
                'active'
            )
            ->first();
    }
}