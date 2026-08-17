<?php

namespace App\Services\Reader;

use App\Models\Book;
use App\Models\ReaderSession;
use App\Models\RegisteredDevice;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReaderSessionService
{
    public function __construct(
        private readonly ReaderDeviceService $devices
    ) {
    }


    /**
     * Start a secure reading session.
     *
     * @return array{
     *     session:ReaderSession,
     *     token:string
     * }
     */
    public function start(
        User $user,
        Book $book,
        ?School $school,
        RegisteredDevice $device,
        Request $request
    ): array {
        $rawToken =
            Str::random(96);


        $tokenHash =
            hash(
                'sha256',
                $rawToken
            );


        $publicId =
            (string) Str::uuid();


        $forensicId =
            $this->forensicId(
                publicId: $publicId,
                userId: $user->id,
                bookId: $book->id
            );


        $idleMinutes =
            max(
                5,
                (int) config(
                    'reader.session.idle_minutes',
                    15
                )
            );


        $maximumMinutes =
            max(
                $idleMinutes,
                (int) config(
                    'reader.session.max_minutes',
                    240
                )
            );


        $session =
            DB::transaction(
                function () use (
                    $user,
                    $book,
                    $school,
                    $device,
                    $request,
                    $publicId,
                    $rawToken,
                    $tokenHash,
                    $forensicId,
                    $idleMinutes,
                    $maximumMinutes
                ) {
                    return ReaderSession::query()
                        ->create([
                            'user_id' =>
                                $user->id,

                            'book_id' =>
                                $book->id,

                            'school_id' =>
                                $school?->id,

                            'registered_device_id' =>
                                $device->id,

                            'session_token_hash' =>
                                $tokenHash,

                            'public_id' =>
                                $publicId,

                            'forensic_id' =>
                                $forensicId,

                            'ip_address' =>
                                $request->ip(),

                            'user_agent' =>
                                $request->userAgent(),

                            'device_fingerprint' =>
                                $device->fingerprint_hash,

                            'current_page' =>
                                1,

                            'started_at' =>
                                now(),

                            'last_activity_at' =>
                                now(),

                            'expires_at' =>
                                now()->addMinutes(
                                    $idleMinutes
                                ),

                            'absolute_expires_at' =>
                                now()->addMinutes(
                                    $maximumMinutes
                                ),

                            'page_requests' =>
                                0,

                            'denied_requests' =>
                                0,
                        ]);
                }
            );


        return [
            'session' =>
                $session,

            'token' =>
                $rawToken,
        ];
    }


    /**
     * Resolve and validate a reader session from request headers.
     */
    public function resolveFromRequest(
        Request $request,
        Book $book
    ): ReaderSession {
        $publicId =
            $request->header(
                'X-Reader-Session'
            );


        $rawToken =
            $request->header(
                'X-Reader-Token'
            );


        if (
            ! filled($publicId)
            ||
            ! filled($rawToken)
        ) {
            throw new HttpException(
                401,
                'Reader session credentials are missing.'
            );
        }


        $session =
            ReaderSession::query()
                ->with([
                    'device',
                    'school',
                ])
                ->where(
                    'public_id',
                    $publicId
                )
                ->where(
                    'book_id',
                    $book->id
                )
                ->first();


        if (! $session) {
            throw new HttpException(
                401,
                'Reader session is invalid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | User Binding
        |--------------------------------------------------------------------------
        */

        if (
            ! $request->user()
            ||
            (int) $session->user_id
                !==
                (int) $request->user()->id
        ) {
            throw new HttpException(
                403,
                'Reader session does not belong to this account.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Token Verification
        |--------------------------------------------------------------------------
        */

        $incomingHash =
            hash(
                'sha256',
                $rawToken
            );


        if (
            ! hash_equals(
                $session->session_token_hash,
                $incomingHash
            )
        ) {
            $session->increment(
                'denied_requests'
            );


            throw new HttpException(
                401,
                'Reader token is invalid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Expiry
        |--------------------------------------------------------------------------
        */

        if (! $session->isActive()) {
            throw new HttpException(
                401,
                'Reader session has expired.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Device Binding
        |--------------------------------------------------------------------------
        */

        if (
            ! $session->device
            ||
            ! $this->devices
                ->requestMatchesDevice(
                    $session->device,
                    $request
                )
        ) {
            $session->increment(
                'denied_requests'
            );


            throw new HttpException(
                403,
                'Reader session is not valid for this device.'
            );
        }


        return $session;
    }


    /**
     * Refresh inactivity expiry and reading position.
     */
    public function touch(
        ReaderSession $session,
        int $page,
        Request $request
    ): void {
        if (! $session->isActive()) {
            return;
        }


        $idleMinutes =
            max(
                5,
                (int) config(
                    'reader.session.idle_minutes',
                    15
                )
            );


        $rollingExpiry =
            now()->addMinutes(
                $idleMinutes
            );


        /*
         * Rolling expiry must never exceed absolute expiry.
         */
        if (
            $session->absolute_expires_at
            &&
            $rollingExpiry->greaterThan(
                $session->absolute_expires_at
            )
        ) {
            $rollingExpiry =
                $session->absolute_expires_at;
        }


        $session->forceFill([
            'current_page' =>
                $page,

            'last_activity_at' =>
                now(),

            'expires_at' =>
                $rollingExpiry,

            'ip_address' =>
                $request->ip(),

            'user_agent' =>
                $request->userAgent(),
        ])->save();
    }


    /**
     * Revoke a reader session immediately.
     */
    public function revoke(
        ReaderSession $session,
        string $reason
    ): void {
        if ($session->revoked_at) {
            return;
        }


        $session->forceFill([
            'revoked_at' =>
                now(),

            'revocation_reason' =>
                Str::limit(
                    $reason,
                    255
                ),
        ])->save();
    }


    /**
     * Forensic watermark identifier.
     */
    private function forensicId(
        string $publicId,
        int $userId,
        int $bookId
    ): string {
        $secret =
            (string) config(
                'app.key'
            );


        $hash =
            hash_hmac(
                'sha256',
                $publicId
                . '|'
                . $userId
                . '|'
                . $bookId,
                $secret
            );


        return 'LH-'
            . strtoupper(
                substr(
                    $hash,
                    0,
                    16
                )
            );
    }
}