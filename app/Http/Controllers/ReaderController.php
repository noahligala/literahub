<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\School;
use App\Services\Library\BookAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReaderController extends Controller
{
    public function __construct(
        private readonly BookAccessService $access
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Reader UI
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Book $book
    ): View {
        $school =
            $this->schoolFor(
                $request
            );

        abort_unless(
            $this->access->canRead(
                $request->user(),
                $book,
                $school
            ),
            403,
            'You are not permitted to read this book.'
        );

        $book->load([
            'authors',
            'publisher',
        ]);

        $canPrint =
            $school
                ? $this->access
                    ->canPrint(
                        $request->user(),
                        $book,
                        $school
                    )
                : false;

        $canDownload =
            $school
                ? $this->access
                    ->canDownload(
                        $request->user(),
                        $book,
                        $school
                    )
                : false;

        return view(
            'reader.show',
            compact(
                'book',
                'school',
                'canPrint',
                'canDownload'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Protected PDF Stream
    |--------------------------------------------------------------------------
    |
    | Used by PDF.js.
    |
    */

    public function stream(
        Request $request,
        Book $book
    ): StreamedResponse {
        $school =
            $this->schoolFor(
                $request
            );

        abort_unless(
            $this->access->canRead(
                $request->user(),
                $book,
                $school
            ),
            403
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

        return response()->stream(
            function () use ($path) {
                $handle =
                    fopen(
                        $path,
                        'rb'
                    );

                while (
                    !feof($handle)
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

                'Content-Disposition' =>
                    'inline; filename="reader.pdf"',

                'Cache-Control' =>
                    'private, no-store, no-cache, must-revalidate',

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
    ): BinaryFileResponse {
        $school =
            $this->schoolFor(
                $request
            );

        abort_unless(
            $school
            && $this->access
                ->canDownload(
                    $request->user(),
                    $book,
                    $school
                ),
            403,
            'Downloading is not permitted for this title.'
        );

        $path =
            Storage::disk('local')
                ->path(
                    $book->pdf_path
                );

        abort_unless(
            is_file($path),
            404
        );

        return response()->download(
            $path,
            $this->filename($book)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Print Source
    |--------------------------------------------------------------------------
    |
    | Only exposed where both book rights and school licence
    | explicitly permit printing.
    |
    */

    public function printSource(
        Request $request,
        Book $book
    ): BinaryFileResponse {
        $school =
            $this->schoolFor(
                $request
            );

        abort_unless(
            $school
            && $this->access
                ->canPrint(
                    $request->user(),
                    $book,
                    $school
                ),
            403,
            'Printing is not permitted for this title.'
        );

        $path =
            Storage::disk('local')
                ->path(
                    $book->pdf_path
                );

        abort_unless(
            is_file($path),
            404
        );

        return response()->file(
            $path,
            [
                'Content-Type' =>
                    'application/pdf',

                'Content-Disposition' =>
                    'inline; filename="'
                    . $this->filename($book)
                    . '"',

                'Cache-Control' =>
                    'private, no-store',
            ]
        );
    }

    private function schoolFor(
        Request $request
    ): ?School {
        return $request
            ->user()
            ->schools()
            ->first();
    }

    private function filename(
        Book $book
    ): string {
        $name =
            preg_replace(
                '/[^A-Za-z0-9\-_]+/',
                '-',
                $book->title
            );

        return trim(
            $name,
            '-'
        ) . '.pdf';
    }
}