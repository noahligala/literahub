<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\Reader\BookPageRenderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProcessBookPdf implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;


    /**
     * Maximum number of attempts.
     */
    public int $tries = 3;


    /**
     * Maximum execution time per attempt.
     *
     * Large books may require several minutes.
     */
    public int $timeout = 900;


    /**
     * Stop retrying after this many unhandled exceptions.
     */
    public int $maxExceptions = 3;


    /**
     * Delete the job if its models disappear.
     */
    public bool $deleteWhenMissingModels = true;


    public function __construct(
        public int $bookId
    ) {
        $this->onQueue('book-processing');
    }


    /**
     * Prevent the same book from being processed
     * by multiple workers simultaneously.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping(
                'process-book-' . $this->bookId
            ))
                ->expireAfter(1200)
                ->releaseAfter(30),
        ];
    }


    /**
     * Execute the job.
     */
    public function handle(
        BookPageRenderService $renderer
    ): void {
        $book = Book::query()
            ->findOrFail(
                $this->bookId
            );


        /*
        |--------------------------------------------------------------------------
        | Prevent unnecessary duplicate processing
        |--------------------------------------------------------------------------
        */

        if (
            $book->processing_status === 'processed'
            &&
            $book->processed_page_count > 0
            &&
            $book->pages()->exists()
        ) {
            Log::info(
                'LiteraHub book processing skipped: already processed.',
                [
                    'book_id' => $book->id,
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Resolve Private Source PDF
        |--------------------------------------------------------------------------
        */

        $sourcePath =
            $book->original_pdf_path
            ?: $book->pdf_path;


        if (! filled($sourcePath)) {
            throw new \RuntimeException(
                "Book {$book->id} does not have a source PDF path."
            );
        }


        $disk = Storage::disk('local');


        if (! $disk->exists($sourcePath)) {
            throw new \RuntimeException(
                "Private source PDF does not exist for book {$book->id}: {$sourcePath}"
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ensure Storage UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($book->storage_uuid)) {
            $book->storage_uuid =
                (string) Str::uuid();
        }


        /*
        |--------------------------------------------------------------------------
        | Mark Processing Started
        |--------------------------------------------------------------------------
        */

        $book->forceFill([
            'processing_status' =>
                'processing',

            'processing_started_at' =>
                now(),

            'processing_completed_at' =>
                null,

            'processing_failed_at' =>
                null,

            'processing_error' =>
                null,

            'processed_page_count' =>
                0,
        ])->save();


        Log::info(
            'LiteraHub PDF processing started.',
            [
                'book_id' =>
                    $book->id,

                'title' =>
                    $book->title,

                'storage_uuid' =>
                    $book->storage_uuid,

                'source_path' =>
                    $sourcePath,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Calculate Source Checksum
        |--------------------------------------------------------------------------
        */

        $absoluteSourcePath =
            $disk->path(
                $sourcePath
            );


        $sourceChecksum =
            hash_file(
                'sha256',
                $absoluteSourcePath
            );


        if ($sourceChecksum === false) {
            throw new \RuntimeException(
                "Unable to calculate checksum for book {$book->id}."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Clear Stale Render Records
        |--------------------------------------------------------------------------
        |
        | BookPageRenderService will regenerate the pages.
        |
        | The service should delete/recreate actual image files safely.
        |
        */

        $book->pages()->delete();


        /*
        |--------------------------------------------------------------------------
        | Render PDF
        |--------------------------------------------------------------------------
        |
        | The renderer returns:
        |
        | [
        |     'page_count' => 250,
        |     'pages' => [...],
        | ]
        |
        */

        $result =
            $renderer->process(
                book: $book,
                sourcePath: $sourcePath
            );


        $pageCount =
            (int) (
                $result['page_count']
                ?? 0
            );


        if ($pageCount < 1) {
            throw new \RuntimeException(
                "PDF renderer produced no pages for book {$book->id}."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Database Page Count
        |--------------------------------------------------------------------------
        */

        $storedPageCount =
            $book->pages()
                ->count();


        if (
            $storedPageCount
            !==
            $pageCount
        ) {
            throw new \RuntimeException(
                "Rendered page count mismatch for book {$book->id}. "
                . "Renderer reported {$pageCount}; database contains {$storedPageCount}."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Mark Processing Successful
        |--------------------------------------------------------------------------
        */

        $book->forceFill([
            'processing_status' =>
                'processed',

            'processed_page_count' =>
                $pageCount,

            'page_count' =>
                $pageCount,

            'source_checksum' =>
                $sourceChecksum,

            'processing_completed_at' =>
                now(),

            'processing_failed_at' =>
                null,

            'processing_error' =>
                null,
        ])->save();


        Log::info(
            'LiteraHub PDF processing completed.',
            [
                'book_id' =>
                    $book->id,

                'pages' =>
                    $pageCount,

                'storage_uuid' =>
                    $book->storage_uuid,
            ]
        );
    }


    /**
     * Handle a permanently failed job.
     */
    public function failed(
        ?Throwable $exception
    ): void {
        $book =
            Book::query()
                ->find(
                    $this->bookId
                );


        if ($book) {
            $book->forceFill([
                'processing_status' =>
                    'failed',

                'processing_failed_at' =>
                    now(),

                'processing_completed_at' =>
                    null,

                'processing_error' =>
                    Str::limit(
                        $exception?->getMessage()
                        ?? 'Unknown PDF processing error.',
                        5000
                    ),
            ])->save();
        }


        Log::error(
            'LiteraHub PDF processing permanently failed.',
            [
                'book_id' =>
                    $this->bookId,

                'message' =>
                    $exception?->getMessage(),

                'exception' =>
                    $exception
                        ? get_class($exception)
                        : null,
            ]
        );
    }


    /**
     * Retry delays.
     *
     * 1st retry: 30 seconds
     * 2nd retry: 2 minutes
     * 3rd retry: 5 minutes
     */
    public function backoff(): array
    {
        return [
            30,
            120,
            300,
        ];
    }
}