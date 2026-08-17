<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\School;
use App\Services\BookAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReaderController extends Controller
{
    public function __construct(
        private readonly BookAccessService $access
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Reader
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Book $book
    ): View {
        $user = $request->user();

        $school =
            $this->schoolFor($user);

        abort_unless(
            $this->access->canRead(
                $user,
                $book,
                $school
            ),
            403,
            'You do not have permission to read this book.'
        );


        $book->load([
            'authors',
            'publisher',
        ]);


        $canDownload =
            $school
                ? $this->access->canDownload(
                    $user,
                    $book,
                    $school
                )
                : false;


        $canPrint =
            $school
                ? $this->access->canPrint(
                    $user,
                    $book,
                    $school
                )
                : false;


        $bookmark =
            $book
                ->bookmarks()
                ->where(
                    'user_id',
                    $user->id
                )
                ->latest()
                ->first();


        $canBookmark =
            $school
            &&
            $user->hasAnyRole([
                'student',
                'teacher',
                'school_admin',
            ]);


        return view(
            'reader.show',
            compact(
                'book',
                'school',
                'bookmark',
                'canBookmark',
                'canDownload',
                'canPrint'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Protected PDF Stream
    |--------------------------------------------------------------------------
    |
    | Supports standard HTTP byte ranges so PDF.js can request only the
    | sections of a large document that it currently needs.
    |
    */

    public function stream(
        Request $request,
        Book $book
    ): StreamedResponse {
        $user = $request->user();

        $school =
            $this->schoolFor($user);


        abort_unless(
            $this->access->canRead(
                $user,
                $book,
                $school
            ),
            403
        );


        abort_unless(
            filled($book->pdf_path),
            404,
            'Book file not found.'
        );


        $disk =
            Storage::disk('local');


        abort_unless(
            $disk->exists(
                $book->pdf_path
            ),
            404,
            'Book file not found.'
        );


        $path =
            $disk->path(
                $book->pdf_path
            );


        $size =
            filesize($path);


        abort_if(
            $size === false,
            404
        );


        $range =
            $request->header('Range');


        /*
        |--------------------------------------------------------------------------
        | Full file
        |--------------------------------------------------------------------------
        */

        if (
            ! $range
            ||
            ! preg_match(
                '/bytes=(\d*)-(\d*)/',
                $range,
                $matches
            )
        ) {
            return response()
                ->stream(
                    function () use ($path) {
                        $handle =
                            fopen(
                                $path,
                                'rb'
                            );

                        if (! $handle) {
                            return;
                        }

                        while (
                            ! feof($handle)
                        ) {
                            echo fread(
                                $handle,
                                1024 * 1024
                            );

                            flush();
                        }

                        fclose($handle);
                    },
                    200,
                    [
                        'Content-Type' =>
                            'application/pdf',

                        'Content-Length' =>
                            (string) $size,

                        'Accept-Ranges' =>
                            'bytes',

                        'Content-Disposition' =>
                            'inline; filename="'
                            . $this->safeFilename(
                                $book
                            )
                            . '"',

                        'Cache-Control' =>
                            'private, no-store, max-age=0',

                        'Pragma' =>
                            'no-cache',

                        'X-Content-Type-Options' =>
                            'nosniff',
                    ]
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Partial Content
        |--------------------------------------------------------------------------
        */

        $start =
            $matches[1] !== ''
                ? (int) $matches[1]
                : null;


        $end =
            $matches[2] !== ''
                ? (int) $matches[2]
                : null;


        /*
         * Suffix range:
         * bytes=-500
         */

        if (
            $start === null
            &&
            $end !== null
        ) {
            $length =
                min(
                    $end,
                    $size
                );

            $start =
                $size - $length;

            $end =
                $size - 1;
        }


        if (
            $start === null
        ) {
            $start = 0;
        }


        if (
            $end === null
            ||
            $end >= $size
        ) {
            $end =
                $size - 1;
        }


        /*
         * Invalid range.
         */

        if (
            $start > $end
            ||
            $start >= $size
        ) {
            abort(
                416,
                'Requested range is not satisfiable.',
                [
                    'Content-Range' =>
                        "bytes */{$size}",
                ]
            );
        }


        $length =
            $end
            - $start
            + 1;


        return response()
            ->stream(
                function () use (
                    $path,
                    $start,
                    $length
                ) {
                    $handle =
                        fopen(
                            $path,
                            'rb'
                        );

                    if (! $handle) {
                        return;
                    }


                    fseek(
                        $handle,
                        $start
                    );


                    $remaining =
                        $length;


                    while (
                        $remaining > 0
                        &&
                        ! feof($handle)
                    ) {
                        $chunkSize =
                            min(
                                1024 * 1024,
                                $remaining
                            );


                        $buffer =
                            fread(
                                $handle,
                                $chunkSize
                            );


                        if (
                            $buffer === false
                            ||
                            $buffer === ''
                        ) {
                            break;
                        }


                        echo $buffer;

                        flush();


                        $remaining -=
                            strlen(
                                $buffer
                            );
                    }


                    fclose($handle);
                },
                206,
                [
                    'Content-Type' =>
                        'application/pdf',

                    'Content-Length' =>
                        (string) $length,

                    'Content-Range' =>
                        "bytes {$start}-{$end}/{$size}",

                    'Accept-Ranges' =>
                        'bytes',

                    'Content-Disposition' =>
                        'inline; filename="'
                        . $this->safeFilename(
                            $book
                        )
                        . '"',

                    'Cache-Control' =>
                        'private, no-store, max-age=0',

                    'Pragma' =>
                        'no-cache',

                    'X-Content-Type-Options' =>
                        'nosniff',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Download
    |--------------------------------------------------------------------------
    */

    public function download(
        Request $request,
        Book $book
    ) {
        $user =
            $request->user();

        $school =
            $this->schoolFor($user);


        abort_unless(
            $school
            &&
            $this->access->canDownload(
                $user,
                $book,
                $school
            ),
            403,
            'Downloading this title is not permitted.'
        );


        abort_unless(
            Storage::disk('local')
                ->exists(
                    $book->pdf_path
                ),
            404
        );


        return Storage::disk('local')
            ->download(
                $book->pdf_path,
                $this->safeFilename(
                    $book
                ),
                [
                    'Content-Type' =>
                        'application/pdf',

                    'Cache-Control' =>
                        'private, no-store',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Print Source
    |--------------------------------------------------------------------------
    */

    public function printSource(
        Request $request,
        Book $book
    ): StreamedResponse {
        $user =
            $request->user();

        $school =
            $this->schoolFor($user);


        abort_unless(
            $school
            &&
            $this->access->canPrint(
                $user,
                $book,
                $school
            ),
            403,
            'Printing this title is not permitted.'
        );


        abort_unless(
            Storage::disk('local')
                ->exists(
                    $book->pdf_path
                ),
            404
        );


        $path =
            Storage::disk('local')
                ->path(
                    $book->pdf_path
                );


        $size =
            filesize($path);


        return response()
            ->stream(
                function () use ($path) {
                    readfile($path);
                },
                200,
                [
                    'Content-Type' =>
                        'application/pdf',

                    'Content-Length' =>
                        (string) $size,

                    'Content-Disposition' =>
                        'inline; filename="'
                        . $this->safeFilename(
                            $book
                        )
                        . '"',

                    'Cache-Control' =>
                        'private, no-store',

                    'X-Content-Type-Options' =>
                        'nosniff',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve User School
    |--------------------------------------------------------------------------
    */

    private function schoolFor(
        $user
    ): ?School {
        if (! $user) {
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


    /*
    |--------------------------------------------------------------------------
    | Safe Filename
    |--------------------------------------------------------------------------
    */

    private function safeFilename(
        Book $book
    ): string {
        $name =
            preg_replace(
                '/[^A-Za-z0-9\-_ ]/',
                '',
                $book->title
            );


        $name =
            trim(
                $name ?: 'book'
            );


        return $name
            . '.pdf';
    }
}