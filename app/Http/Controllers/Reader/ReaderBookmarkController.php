<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Jobs\RecordReadingActivity;
use App\Models\Book;
use App\Models\BookBookmark;
use App\Services\Reader\ReaderAuthorizationService;
use App\Services\Reader\ReaderSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReaderBookmarkController extends Controller
{
    public function __construct(
        private readonly ReaderSessionService $sessions,
        private readonly ReaderAuthorizationService $authorization,
    ) {
    }


    /**
     * Create or update a bookmark.
     */
    public function store(
        Request $request,
        Book $book
    ): JsonResponse|RedirectResponse {
        $user = $request->user();

        abort_unless(
            $user,
            401
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Input
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'page' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'label' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        $page =
            (int) $validated['page'];


        abort_if(
            $book->processing_status !== 'processed',
            409,
            'Book processing is incomplete.'
        );


        abort_if(
            $page >
            $book->processed_page_count,
            422,
            'Bookmark page is outside the book.'
        );


        /*
        |--------------------------------------------------------------------------
        | Resolve Reader Session
        |--------------------------------------------------------------------------
        */

        $readerSession =
            $this->sessions
                ->resolveFromRequest(
                    request: $request,
                    book: $book
                );


        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
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
        | Save Bookmark
        |--------------------------------------------------------------------------
        */

        $bookmark =
            BookBookmark::query()
                ->updateOrCreate(
                    [
                        'user_id' =>
                            $user->id,

                        'book_id' =>
                            $book->id,

                        'page' =>
                            $page,
                    ],
                    [
                        'label' =>
                            $validated['label']
                            ?? 'Page ' . $page,

                        'note' =>
                            $validated['note']
                            ?? null,
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | Update Current Reading Position
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
        | Audit
        |--------------------------------------------------------------------------
        */

        RecordReadingActivity::dispatch(
            userId: $user->id,
            bookId: $book->id,
            eventType: 'bookmark',
            readerSessionId:
                $readerSession->id,
            schoolId:
                $readerSession->school_id,
            registeredDeviceId:
                $readerSession->registered_device_id,
            pageNumber:
                $page,
            ipAddress:
                $request->ip(),
            metadata: [
                'bookmark_id' =>
                    $bookmark->id,
            ],
        );


        /*
        |--------------------------------------------------------------------------
        | API Response
        |--------------------------------------------------------------------------
        */

        if (
            $request->expectsJson()
            ||
            $request->ajax()
        ) {
            return response()->json([
                'message' =>
                    'Bookmark saved.',

                'bookmark' => [
                    'id' =>
                        $bookmark->id,

                    'page' =>
                        $bookmark->page,

                    'label' =>
                        $bookmark->label,

                    'note' =>
                        $bookmark->note,
                ],
            ]);
        }


        return back()->with(
            'status',
            'Bookmark saved.'
        );
    }
}