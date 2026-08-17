<?php

namespace App\Services\Reader;

use App\Models\Book;
use App\Models\BookPage;
use Imagick;
use ImagickException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class BookPageRenderService
{
    /**
     * Convert a private PDF into private WebP pages.
     *
     * @return array{
     *     page_count:int,
     *     pages:array<int,array<string,mixed>>
     * }
     */
    public function process(
        Book $book,
        string $sourcePath
    ): array {
        $this->assertRuntimeAvailable();

        $disk = Storage::disk('local');


        if (! $disk->exists($sourcePath)) {
            throw new RuntimeException(
                "Source PDF does not exist: {$sourcePath}"
            );
        }


        if (! filled($book->storage_uuid)) {
            throw new RuntimeException(
                'The book must have a storage UUID before rendering.'
            );
        }


        $absoluteSourcePath =
            $disk->path($sourcePath);


        /*
        |--------------------------------------------------------------------------
        | Private Output Directory
        |--------------------------------------------------------------------------
        */

        $outputDirectory =
            'library/rendered/'
            . $book->storage_uuid
            . '/pages';


        /*
         * Remove stale rendered files before regeneration.
         */
        if ($disk->exists($outputDirectory)) {
            $disk->deleteDirectory(
                $outputDirectory
            );
        }


        $disk->makeDirectory(
            $outputDirectory
        );


        /*
        |--------------------------------------------------------------------------
        | Determine PDF Page Count
        |--------------------------------------------------------------------------
        */

        $pageCount =
            $this->determinePageCount(
                $absoluteSourcePath
            );


        if ($pageCount < 1) {
            throw new RuntimeException(
                'The PDF does not contain any readable pages.'
            );
        }


        $pages = [];


        /*
        |--------------------------------------------------------------------------
        | Render Pages Individually
        |--------------------------------------------------------------------------
        |
        | PDF page indexes are zero-based in Imagick.
        |
        */

        for (
            $index = 0;
            $index < $pageCount;
            $index++
        ) {
            $pageNumber =
                $index + 1;


            $pages[] =
                $this->renderPage(
                    book: $book,
                    absoluteSourcePath:
                        $absoluteSourcePath,
                    outputDirectory:
                        $outputDirectory,
                    pageIndex:
                        $index,
                    pageNumber:
                        $pageNumber
                );
        }


        return [
            'page_count' =>
                $pageCount,

            'pages' =>
                $pages,
        ];
    }


    /**
     * Determine the number of pages in the PDF.
     */
    private function determinePageCount(
        string $absoluteSourcePath
    ): int {
        $pdf = new Imagick();


        try {
            /*
             * pingImage reads metadata without fully rendering
             * each PDF page.
             */
            $pdf->pingImage(
                $absoluteSourcePath
            );

            return $pdf->getNumberImages();
        }
        catch (ImagickException $exception) {
            throw new RuntimeException(
                'Unable to inspect the PDF. '
                . 'Verify that ImageMagick/Imagick has PDF support. '
                . $exception->getMessage(),
                previous: $exception
            );
        }
        finally {
            $pdf->clear();
            $pdf->destroy();
        }
    }


    /**
     * Render one PDF page.
     *
     * @return array<string,mixed>
     */
    private function renderPage(
        Book $book,
        string $absoluteSourcePath,
        string $outputDirectory,
        int $pageIndex,
        int $pageNumber
    ): array {
        $dpi =
            (int) config(
                'reader.render.dpi',
                150
            );


        $quality =
            (int) config(
                'reader.render.quality',
                82
            );


        $quality =
            max(
                40,
                min(
                    $quality,
                    95
                )
            );


        $page = new Imagick();


        try {
            /*
             * Resolution MUST be set before readImage().
             */
            $page->setResolution(
                $dpi,
                $dpi
            );


            /*
             * Read only ONE PDF page.
             */
            $page->readImage(
                $absoluteSourcePath
                . '['
                . $pageIndex
                . ']'
            );


            $page->setIteratorIndex(0);


            /*
            |--------------------------------------------------------------------------
            | Normalize transparency onto white background
            |--------------------------------------------------------------------------
            */

            $width =
                $page->getImageWidth();

            $height =
                $page->getImageHeight();


            $canvas =
                new Imagick();


            $canvas->newImage(
                $width,
                $height,
                'white'
            );


            $canvas->setImageFormat(
                'webp'
            );


            $canvas->compositeImage(
                $page,
                Imagick::COMPOSITE_OVER,
                0,
                0
            );


            /*
            |--------------------------------------------------------------------------
            | Remove source metadata
            |--------------------------------------------------------------------------
            */

            $canvas->stripImage();


            $canvas->setImageFormat(
                'webp'
            );


            $canvas->setImageCompressionQuality(
                $quality
            );


            $blob =
                $canvas->getImageBlob();


            if (
                $blob === false
                ||
                $blob === ''
            ) {
                throw new RuntimeException(
                    "Unable to generate page {$pageNumber}."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Private Page Path
            |--------------------------------------------------------------------------
            */

            $filename =
                str_pad(
                    (string) $pageNumber,
                    5,
                    '0',
                    STR_PAD_LEFT
                )
                . '.webp';


            $imagePath =
                $outputDirectory
                . '/'
                . $filename;


            $disk =
                Storage::disk('local');


            $written =
                $disk->put(
                    $imagePath,
                    $blob
                );


            if (! $written) {
                throw new RuntimeException(
                    "Unable to store rendered page {$pageNumber}."
                );
            }


            $fileSize =
                strlen($blob);


            $checksum =
                hash(
                    'sha256',
                    $blob
                );


            /*
            |--------------------------------------------------------------------------
            | BookPage Record
            |--------------------------------------------------------------------------
            */

            $bookPage =
                DB::transaction(
                    function () use (
                        $book,
                        $pageNumber,
                        $imagePath,
                        $width,
                        $height,
                        $fileSize,
                        $checksum
                    ) {
                        return BookPage::query()
                            ->updateOrCreate(
                                [
                                    'book_id' =>
                                        $book->id,

                                    'page_number' =>
                                        $pageNumber,
                                ],
                                [
                                    'image_path' =>
                                        $imagePath,

                                    'width' =>
                                        $width,

                                    'height' =>
                                        $height,

                                    'file_size' =>
                                        $fileSize,

                                    'mime_type' =>
                                        'image/webp',

                                    'checksum' =>
                                        $checksum,

                                    'render_version' =>
                                        $book->render_version
                                        ?: 1,

                                    'rendered_at' =>
                                        now(),
                                ]
                            );
                    }
                );


            return [
                'id' =>
                    $bookPage->id,

                'page_number' =>
                    $pageNumber,

                'image_path' =>
                    $imagePath,

                'width' =>
                    $width,

                'height' =>
                    $height,

                'file_size' =>
                    $fileSize,

                'checksum' =>
                    $checksum,
            ];
        }
        catch (Throwable $exception) {
            Log::error(
                'LiteraHub page rendering failed.',
                [
                    'book_id' =>
                        $book->id,

                    'page' =>
                        $pageNumber,

                    'message' =>
                        $exception->getMessage(),
                ]
            );


            throw new RuntimeException(
                "Unable to render page {$pageNumber}: "
                . $exception->getMessage(),
                previous: $exception
            );
        }
        finally {
            $page->clear();
            $page->destroy();

            if (isset($canvas)) {
                $canvas->clear();
                $canvas->destroy();
            }
        }
    }


    /**
     * Validate server rendering support.
     */
    private function assertRuntimeAvailable(): void
    {
        if (! class_exists(Imagick::class)) {
            throw new RuntimeException(
                'The PHP Imagick extension is not installed. '
                . 'LiteraHub secure book processing requires '
                . 'ImageMagick with the PHP Imagick extension.'
            );
        }


        $formats =
            Imagick::queryFormats();


        $formats =
            array_map(
                'strtoupper',
                $formats
            );


        if (
            ! in_array(
                'PDF',
                $formats,
                true
            )
        ) {
            throw new RuntimeException(
                'ImageMagick is installed but PDF decoding '
                . 'is not available.'
            );
        }


        if (
            ! in_array(
                'WEBP',
                $formats,
                true
            )
        ) {
            throw new RuntimeException(
                'ImageMagick is installed but WebP output '
                . 'is not available.'
            );
        }
    }
}