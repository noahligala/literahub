<?php

namespace App\Services\Reader;

use App\Models\Book;
use App\Models\ReaderSession;
use App\Models\RegisteredDevice;
use App\Models\School;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Services\BookAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReaderSecurityService
{
    public function __construct(
        private readonly BookAccessService $bookAccess
    ) {
    }


    /**
     * Security checks before a new reader session starts.
     */
    public function assertSessionCanStart(
        User $user,
        Book $book,
        ?School $school,
        RegisteredDevice $device,
        Request $request
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Reader Session Start Rate
        |--------------------------------------------------------------------------
        */

        $startKey =
            'reader-start:'
            . $user->id;


        if (
            RateLimiter::tooManyAttempts(
                $startKey,
                10
            )
        ) {
            $this->securityEvent(
                user: $user,
                book: $book,
                school: $school,
                device: $device,
                request: $request,
                type: 'reader_start_rate_limit',
                severity: 'medium',
                description:
                    'Too many reader sessions were started in a short period.'
            );


            throw new HttpException(
                429,
                'Too many reader session attempts. Please try again shortly.'
            );
        }


        RateLimiter::hit(
            $startKey,
            60
        );


        /*
        |--------------------------------------------------------------------------
        | Per-user Concurrent Reader Limit
        |--------------------------------------------------------------------------
        */

        $userLimit =
            $this->concurrentUserLimit(
                $user
            );


        $activeUserSessions =
            $this->activeSessions()
                ->where(
                    'user_id',
                    $user->id
                )
                ->count();


        if (
            $userLimit > 0
            &&
            $activeUserSessions >= $userLimit
        ) {
            $this->securityEvent(
                user: $user,
                book: $book,
                school: $school,
                device: $device,
                request: $request,
                type: 'concurrent_session_limit',
                severity: 'medium',
                description:
                    'The account reached its concurrent reader-session limit.'
            );


            throw new HttpException(
                403,
                'Your account already has the maximum number of active reader sessions.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | School Licence Concurrent Reader Limit
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
                    'No active institutional licence exists.'
                );
            }


            $licenseLimit =
                $license->concurrent_reader_limit;


            if (
                $licenseLimit !== null
                &&
                $licenseLimit > 0
            ) {
                $activeSchoolReaders =
                    $this->activeSessions()
                        ->where(
                            'school_id',
                            $school->id
                        )
                        ->where(
                            'book_id',
                            $book->id
                        )
                        ->count();


                if (
                    $activeSchoolReaders
                    >=
                    $licenseLimit
                ) {
                    $this->securityEvent(
                        user: $user,
                        book: $book,
                        school: $school,
                        device: $device,
                        request: $request,
                        type: 'license_concurrency_limit',
                        severity: 'low',
                        description:
                            'The book licence concurrent-reader limit was reached.'
                    );


                    throw new HttpException(
                        429,
                        'This title has reached its institutional concurrent-reader limit. Please try again later.'
                    );
                }
            }
        }
    }


    /**
     * Security checks performed for every page retrieval.
     */
    public function assertPageRequestAllowed(
        User $user,
        Book $book,
        ReaderSession $readerSession,
        int $page,
        Request $request
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Per Reader Session Rate Limit
        |--------------------------------------------------------------------------
        */

        $limit =
            max(
                10,
                (int) config(
                    'reader.page_rate_limit_per_minute',
                    60
                )
            );


        $key =
            'reader-page:'
            . $readerSession->public_id;


        if (
            RateLimiter::tooManyAttempts(
                $key,
                $limit
            )
        ) {
            $readerSession->increment(
                'denied_requests'
            );


            $this->securityEvent(
                user: $user,
                book: $book,
                school: $readerSession->school,
                device: $readerSession->device,
                readerSession: $readerSession,
                request: $request,
                type: 'page_rate_limit_exceeded',
                severity: 'high',
                description:
                    'Reader page retrieval exceeded the configured request rate.',
                context: [
                    'page' =>
                        $page,

                    'limit_per_minute' =>
                        $limit,
                ]
            );


            throw new HttpException(
                429,
                'Page requests are occurring too quickly.'
            );
        }


        RateLimiter::hit(
            $key,
            60
        );


        /*
        |--------------------------------------------------------------------------
        | Device Revocation
        |--------------------------------------------------------------------------
        */

        if (
            ! $readerSession->device
            ||
            $readerSession->device->revoked_at
        ) {
            throw new HttpException(
                403,
                'Reader device is no longer authorised.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Basic Automated Extraction Detection
        |--------------------------------------------------------------------------
        |
        | This is deliberately conservative.
        |
        | We do not assume that fast navigation automatically means abuse.
        |
        */

        if (
            $readerSession->page_requests >= 120
            &&
            $readerSession->started_at
            &&
            $readerSession
                ->started_at
                ->diffInMinutes(
                    now()
                ) < 2
        ) {
            $this->securityEvent(
                user: $user,
                book: $book,
                school: $readerSession->school,
                device: $readerSession->device,
                readerSession: $readerSession,
                request: $request,
                type: 'suspicious_page_sequence',
                severity: 'high',
                description:
                    'A reader session requested an unusually large number of pages shortly after opening.',
                context: [
                    'page_requests' =>
                        $readerSession->page_requests,

                    'current_page' =>
                        $page,
                ]
            );


            throw new HttpException(
                429,
                'Reader activity has been temporarily restricted.'
            );
        }
    }


    /**
     * Record successful page delivery.
     */
    public function recordSuccessfulPageRequest(
        ReaderSession $readerSession,
        int $page,
        Request $request
    ): void {
        ReaderSession::query()
            ->whereKey(
                $readerSession->id
            )
            ->increment(
                'page_requests'
            );
    }


    /**
     * Query active reader sessions.
     */
    private function activeSessions()
    {
        return ReaderSession::query()
            ->whereNull(
                'revoked_at'
            )
            ->where(
                'expires_at',
                '>',
                now()
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull(
                            'absolute_expires_at'
                        )
                        ->orWhere(
                            'absolute_expires_at',
                            '>',
                            now()
                        );
                }
            );
    }


    private function concurrentUserLimit(
        User $user
    ): int {
        foreach (
            [
                'school_admin',
                'teacher',
                'student',
                'individual_subscriber',
            ]
            as $role
        ) {
            if (
                $user->hasRole(
                    $role
                )
            ) {
                return (int) config(
                    "reader.concurrent_sessions.{$role}",
                    1
                );
            }
        }


        /*
         * Platform staff.
         */
        return 5;
    }


    /**
     * Persist an auditable security event.
     *
     * @param array<string,mixed>|null $context
     */
    private function securityEvent(
        User $user,
        Book $book,
        ?School $school,
        ?RegisteredDevice $device,
        Request $request,
        string $type,
        string $severity,
        string $description,
        ?ReaderSession $readerSession = null,
        ?array $context = null
    ): void {
        SecurityEvent::query()
            ->create([
                'user_id' =>
                    $user->id,

                'book_id' =>
                    $book->id,

                'school_id' =>
                    $school?->id,

                'reader_session_id' =>
                    $readerSession?->id,

                'registered_device_id' =>
                    $device?->id,

                'event_type' =>
                    $type,

                'severity' =>
                    $severity,

                'ip_address' =>
                    $request->ip(),

                'user_agent' =>
                    $request->userAgent(),

                'description' =>
                    $description,

                'context' =>
                    $context,

                'detected_at' =>
                    now(),
            ]);
    }
}