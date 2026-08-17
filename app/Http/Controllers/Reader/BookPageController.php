<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Jobs\RecordReadingActivity;
use App\Models\Book;
use App\Models\BookPage;
use App\Services\Reader\ReaderAuthorizationService;
use App\Services\Reader\ReaderSecurityService;
use App\Services\Reader\ReaderSessionService;
use App\Services\Reader\ReaderWatermarkService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookPageController extends Controller
{
    public function __construct(
        private readonly ReaderSessionService $sessions,
        private readonly ReaderAuthorizationService $authorization,
        private readonly ReaderSecurityService $security,
        private readonly ReaderWatermarkService $watermarks,
    ) {
    }


    /**
     * Return one protected, watermarked book page.
     *
     * The original PDF is NEVER returned here.
     */
    public function show(
        Request $request,
        Book $book,
        int $page
    ): Response {
        $user = $request->user();

        abort_unless(
            $user,
            401,
            'Authentication required.'
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Page Number
        |--------------------------------------------------------------------------
        */

        abort_if(
            $page < 1,
            404,
            'Invalid page.'
        );


        abort_if(
            $book->processing_status !== 'processed',
            409,
            'This book has not completed secure page processing.'
        );


        abort_if(
            $book->processed_page_count < 1,
            409,
            'This book has no processed pages.'
        );


        abort_if(
            $page > $book->processed_page_count,
            404,
            'Page not found.'
        );


        /*
        |--------------------------------------------------------------------------
        | Resolve Secure Reader Session
        |--------------------------------------------------------------------------
        |
        | ReaderSessionService should validate:
        |
        | X-Reader-Session
        | X-Reader-Token
        |
        | and ensure the session belongs to:
        |
        | - current authenticated user
        | - requested book
        |
        */

        $readerSession =
            $this->sessions
                ->resolveFromRequest(
                    request: $request,
                    book: $book
                );


        /*
        |--------------------------------------------------------------------------
        | Authorize Page Access
        |--------------------------------------------------------------------------
        |
        | Checks:
        |
        | - account
        | - book access
        | - school/subscription
        | - licence
        | - role
        | - class/access request
        | - reader session
        | - session expiry
        | - device
        |
        */

        $this->authorization
            ->authorizePage(
                user: $user,
                book: $book,
                readerSession: $readerSession,
                page: $page,
                request: $request
            );


        /*
        |--------------------------------------------------------------------------
        | Security / Behaviour Check
        |--------------------------------------------------------------------------
        |
        | This service will later inspect:
        |
        | - request rate
        | - sequential scraping
        | - session concurrency
        | - device legitimacy
        | - suspicious access patterns
        |
        */

        $this->security
            ->assertPageRequestAllowed(
                user: $user,
                book: $book,
                readerSession: $readerSession,
                page: $page,
                request: $request
            );


        /*
        |--------------------------------------------------------------------------
        | Locate Rendered Page
        |--------------------------------------------------------------------------
        */

        $bookPage =
            BookPage::query()
                ->where(
                    'book_id',
                    $book->id
                )
                ->where(
                    'page_number',
                    $page
                )
                ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Server-side Watermark
        |--------------------------------------------------------------------------
        |
        | Expected return:
        |
        | [
        |     'content' => binary image,
        |     'mime_type' => 'image/webp',
        | ]
        |
        */

        $rendered =
            $this->watermarks
                ->render(
                    page: $bookPage,
                    user: $user,
                    readerSession: $readerSession
                );


        abort_unless(
            isset($rendered['content']),
            500,
            'Unable to render protected page.'
        );


        /*
        |--------------------------------------------------------------------------
        | Update Session
        |--------------------------------------------------------------------------
        */

        $this->sessions
            ->touch(
                session: $readerSession,
                page: $page,
                request: $request
            );


        /*
        |--------------------------------------------------------------------------
        | Record Security Behaviour
        |--------------------------------------------------------------------------
        */

        $this->security
            ->recordSuccessfulPageRequest(
                readerSession: $readerSession,
                page: $page,
                request: $request
            );


        /*
        |--------------------------------------------------------------------------
        | Reading Activity
        |--------------------------------------------------------------------------
        |
        | Queue the audit event instead of slowing the page response.
        |
        */

        RecordReadingActivity::dispatch(
            userId: $user->id,
            bookId: $book->id,
            eventType: 'page_view',
            readerSessionId: $readerSession->id,
            schoolId: $readerSession->school_id,
            registeredDeviceId:
                $readerSession->registered_device_id,
            pageNumber: $page,
            ipAddress: $request->ip(),
            metadata: [
                'reader' => 'secure',
            ],
        );


        /*
        |--------------------------------------------------------------------------
        | Protected Image Response
        |--------------------------------------------------------------------------
        */

        return response(
            $rendered['content'],
            200,
            [
                'Content-Type' =>
                    $rendered['mime_type']
                    ?? 'image/webp',

                'Cache-Control' =>
                    'private, no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',

                'X-Content-Type-Options' =>
                    'nosniff',

                'Content-Disposition' =>
                    'inline',

                'X-Robots-Tag' =>
                    'noindex, nofollow, noarchive',

                /*
                 * Prevent pages from being embedded
                 * by unrelated websites.
                 */
                'Content-Security-Policy' =>
                    "default-src 'none'; frame-ancestors 'self';",
            ]
        );
    }
}