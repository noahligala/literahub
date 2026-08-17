<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Jobs\RecordReadingActivity;
use App\Models\Book;
use App\Models\ReaderSession;
use App\Services\Reader\ReaderAuthorizationService;
use App\Services\Reader\ReaderDeviceService;
use App\Services\Reader\ReaderSecurityService;
use App\Services\Reader\ReaderSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReaderSessionController extends Controller
{
    public function __construct(
        private readonly ReaderAuthorizationService $authorization,
        private readonly ReaderDeviceService $devices,
        private readonly ReaderSessionService $sessions,
        private readonly ReaderSecurityService $security,
    ) {
    }


    /**
     * Create a secure reader session.
     */
    public function store(
        Request $request,
        Book $book
    ): JsonResponse {
        $user =
            $request->user();


        abort_unless(
            $user,
            401,
            'Authentication required.'
        );


        /*
        |--------------------------------------------------------------------------
        | Book Processing State
        |--------------------------------------------------------------------------
        */

        abort_if(
            $book->processing_status !== 'processed',
            409,
            'This book is not ready for secure reading.'
        );


        abort_if(
            $book->processed_page_count < 1,
            409,
            'This book has no readable pages.'
        );


        /*
        |--------------------------------------------------------------------------
        | Authorize Book
        |--------------------------------------------------------------------------
        |
        | Expected result:
        |
        | [
        |     'school'  => ?School,
        |     'license' => ?SchoolBookLicense,
        | ]
        |
        */

        $access =
            $this->authorization
                ->authorizeBook(
                    user: $user,
                    book: $book,
                    request: $request
                );


        /*
        |--------------------------------------------------------------------------
        | Register / Resolve Device
        |--------------------------------------------------------------------------
        */

        $device =
            $this->devices
                ->resolveOrRegister(
                    user: $user,
                    request: $request
                );


        /*
        |--------------------------------------------------------------------------
        | Device Security
        |--------------------------------------------------------------------------
        */

        $this->devices
            ->assertDeviceAllowed(
                user: $user,
                device: $device
            );


        /*
        |--------------------------------------------------------------------------
        | Session Security
        |--------------------------------------------------------------------------
        */

        $this->security
            ->assertSessionCanStart(
                user: $user,
                book: $book,
                school: $access['school'] ?? null,
                device: $device,
                request: $request
            );


        /*
        |--------------------------------------------------------------------------
        | Create Reader Session
        |--------------------------------------------------------------------------
        |
        | Expected:
        |
        | [
        |     'session' => ReaderSession,
        |     'token'   => raw one-time reader token
        | ]
        |
        | Only the token HASH is stored in the database.
        |
        */

        $result =
            $this->sessions
                ->start(
                    user: $user,
                    book: $book,
                    school: $access['school'] ?? null,
                    device: $device,
                    request: $request
                );


        $readerSession =
            $result['session'];

        $rawToken =
            $result['token'];


        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        RecordReadingActivity::dispatch(
            userId:
                $user->id,

            bookId:
                $book->id,

            eventType:
                'reader_started',

            readerSessionId:
                $readerSession->id,

            schoolId:
                $readerSession->school_id,

            registeredDeviceId:
                $readerSession->registered_device_id,

            pageNumber:
                $readerSession->current_page,

            ipAddress:
                $request->ip(),

            metadata: [
                'reader' =>
                    'secure',

                'device_uuid' =>
                    $device->device_uuid,
            ],
        );


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        |
        | The frontend stores this token in MEMORY only.
        |
        | Do not put the reader token into:
        |
        | - localStorage
        | - sessionStorage
        | - URL
        |
        */

        return response()->json([
            'message' =>
                'Reader session started.',

            'session' => [
                'id' =>
                    $readerSession->public_id,

                'token' =>
                    $rawToken,

                'expires_at' =>
                    $readerSession
                        ->expires_at
                        ?->toISOString(),

                'absolute_expires_at' =>
                    $readerSession
                        ->absolute_expires_at
                        ?->toISOString(),

                'current_page' =>
                    $readerSession->current_page,
            ],

            'book' => [
                'id' =>
                    $book->id,

                'title' =>
                    $book->title,

                'pages' =>
                    $book->processed_page_count,
            ],
        ]);
    }


    /**
     * End / revoke a reader session.
     */
    public function destroy(
        Request $request,
        ReaderSession $readerSession
    ): JsonResponse {
        $user =
            $request->user();


        abort_unless(
            $user,
            401
        );


        /*
        |--------------------------------------------------------------------------
        | Ownership
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $readerSession->user_id
            ===
            (int) $user->id,
            403,
            'You cannot terminate this reader session.'
        );


        if (! $readerSession->isRevoked()) {

            $this->sessions
                ->revoke(
                    session: $readerSession,
                    reason: 'user_closed_reader'
                );


            RecordReadingActivity::dispatch(
                userId:
                    $user->id,

                bookId:
                    $readerSession->book_id,

                eventType:
                    'reader_ended',

                readerSessionId:
                    $readerSession->id,

                schoolId:
                    $readerSession->school_id,

                registeredDeviceId:
                    $readerSession->registered_device_id,

                pageNumber:
                    $readerSession->current_page,

                ipAddress:
                    $request->ip(),
            );
        }


        return response()->json([
            'message' =>
                'Reader session ended.',
        ]);
    }
}