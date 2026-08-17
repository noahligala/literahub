<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookBookmark;
use App\Services\Reader\ReaderAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecureReaderController extends Controller
{
    public function __construct(
        private readonly ReaderAuthorizationService $authorization,
    ) {
    }


    /**
     * Display the secure reader shell.
     *
     * No book page and no PDF is returned by this request.
     *
     * JavaScript creates a ReaderSession after the shell loads,
     * then requests individual protected pages.
     */
    public function show(
        Request $request,
        Book $book
    ): View {
        $user =
            $request->user();


        abort_unless(
            $user,
            401,
            'Authentication required.'
        );


        /*
        |--------------------------------------------------------------------------
        | Book State
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $book->status === 'published'
            ||
            $this->authorization
                ->mayPreviewUnpublished(
                    user: $user,
                    book: $book
                ),
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Processing State
        |--------------------------------------------------------------------------
        */

        abort_if(
            $book->processing_status === 'processing',
            409,
            'This book is still being prepared for secure reading.'
        );


        abort_if(
            $book->processing_status === 'failed',
            409,
            'Secure book processing failed. Please contact platform support.'
        );


        abort_unless(
            $book->processing_status === 'processed',
            409,
            'This book is not yet available in the secure reader.'
        );


        abort_if(
            $book->processed_page_count < 1,
            409,
            'No secure pages are available for this title.'
        );


        /*
        |--------------------------------------------------------------------------
        | Access Authorization
        |--------------------------------------------------------------------------
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
        | Book Metadata
        |--------------------------------------------------------------------------
        */

        $book->load([
            'authors',
            'publisher',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Resume Position
        |--------------------------------------------------------------------------
        |
        | We don't need to load all bookmarks.
        |
        */

        $bookmark =
            BookBookmark::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'book_id',
                    $book->id
                )
                ->latest('updated_at')
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Previous Reading Position
        |--------------------------------------------------------------------------
        */

        $previousSession =
            $book
                ->readerSessions()
                ->where(
                    'user_id',
                    $user->id
                )
                ->orderByDesc(
                    'last_activity_at'
                )
                ->first();


        $initialPage =
            $bookmark?->page
            ??
            $previousSession?->current_page
            ??
            1;


        $initialPage =
            max(
                1,
                min(
                    $initialPage,
                    $book->processed_page_count
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Reader Configuration
        |--------------------------------------------------------------------------
        |
        | Only non-sensitive frontend configuration is exposed.
        |
        | The original PDF path is NEVER included.
        |
        */

        $readerConfig = [
            'bookId' =>
                $book->id,

            'totalPages' =>
                $book->processed_page_count,

            'initialPage' =>
                $initialPage,

            'pageWindow' =>
                (int) config(
                    'reader.page_window',
                    1
                ),

            'sessionUrl' =>
                route(
                    'secure-reader.sessions.store',
                    $book
                ),

            /*
             * JavaScript replaces PAGE_NUMBER.
             */
            'pageUrlTemplate' =>
                route(
                    'secure-reader.pages.show',
                    [
                        'book' =>
                            $book,

                        'page' =>
                            'PAGE_NUMBER',
                    ]
                ),

            'bookmarkUrl' =>
                route(
                    'secure-reader.bookmarks.store',
                    $book
                ),

            'sessionDestroyUrl' =>
                null,
        ];


        return view(
            'reader.secure-show',
            [
                'book' =>
                    $book,

                'school' =>
                    $access['school']
                    ?? null,

                'license' =>
                    $access['license']
                    ?? null,

                'bookmark' =>
                    $bookmark,

                'initialPage' =>
                    $initialPage,

                'readerConfig' =>
                    $readerConfig,
            ]
        );
    }
}