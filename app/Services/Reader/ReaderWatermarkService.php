<?php

namespace App\Services\Reader;

use App\Models\BookPage;
use App\Models\ReaderSession;
use App\Models\User;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ReaderWatermarkService
{
    /**
     * Render a page with personalised forensic watermarking.
     *
     * @return array{
     *     content:string,
     *     mime_type:string
     * }
     */
    public function render(
        BookPage $page,
        User $user,
        ReaderSession $readerSession
    ): array {
        $this->assertRuntimeAvailable();


        $disk =
            Storage::disk('local');


        if (
            ! $disk->exists(
                $page->image_path
            )
        ) {
            throw new RuntimeException(
                "Rendered page file is missing: {$page->image_path}"
            );
        }


        $absolutePath =
            $disk->path(
                $page->image_path
            );


        $image =
            new Imagick();


        try {
            $image->readImage(
                $absolutePath
            );


            $image->setIteratorIndex(0);


            /*
            |--------------------------------------------------------------------------
            | If watermarking is disabled
            |--------------------------------------------------------------------------
            */

            if (
                ! config(
                    'reader.watermark.enabled',
                    true
                )
            ) {
                $image->setImageFormat(
                    'webp'
                );


                return [
                    'content' =>
                        $image->getImageBlob(),

                    'mime_type' =>
                        'image/webp',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Watermark Text
            |--------------------------------------------------------------------------
            */

            $lines =
                $this->watermarkLines(
                    user: $user,
                    readerSession:
                        $readerSession
                );


            $label =
                implode(
                    ' • ',
                    array_filter(
                        $lines
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | Watermark Appearance
            |--------------------------------------------------------------------------
            */

            $width =
                $image->getImageWidth();

            $height =
                $image->getImageHeight();


            $fontSize =
                max(
                    13,
                    min(
                        30,
                        (int) round(
                            $width / 42
                        )
                    )
                );


            $opacity =
                (int) config(
                    'reader.watermark.opacity',
                    12
                );


            $opacity =
                max(
                    5,
                    min(
                        $opacity,
                        25
                    )
                );


            $alpha =
                $opacity / 100;


            $draw =
                new ImagickDraw();


            $draw->setFontSize(
                $fontSize
            );


            $draw->setFillColor(
                new ImagickPixel(
                    sprintf(
                        'rgba(40,40,40,%.3f)',
                        $alpha
                    )
                )
            );


            $draw->setTextAntialias(
                true
            );


            /*
            |--------------------------------------------------------------------------
            | Repeated Diagonal Watermarks
            |--------------------------------------------------------------------------
            */

            $horizontalSpacing =
                max(
                    360,
                    (int) round(
                        $width * 0.48
                    )
                );


            $verticalSpacing =
                max(
                    180,
                    (int) round(
                        $height * 0.18
                    )
                );


            for (
                $y = 80;
                $y < $height + 150;
                $y += $verticalSpacing
            ) {
                /*
                 * Stagger alternate rows.
                 */
                $offset =
                    (
                        intdiv(
                            $y,
                            $verticalSpacing
                        )
                        % 2
                    )
                        ? -150
                        : 20;


                for (
                    $x = $offset;
                    $x < $width + 300;
                    $x += $horizontalSpacing
                ) {
                    $image->annotateImage(
                        $draw,
                        $x,
                        $y,
                        -28,
                        $label
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Small Forensic Footer
            |--------------------------------------------------------------------------
            */

            $footer =
                new ImagickDraw();


            $footer->setFontSize(
                max(
                    10,
                    (int) round(
                        $fontSize * .65
                    )
                )
            );


            $footer->setFillColor(
                new ImagickPixel(
                    'rgba(30,30,30,0.28)'
                )
            );


            $footerText =
                $readerSession->forensic_id
                . '  |  '
                . now()->format(
                    'Y-m-d H:i'
                );


            $image->annotateImage(
                $footer,
                15,
                max(
                    20,
                    $height - 15
                ),
                0,
                $footerText
            );


            /*
            |--------------------------------------------------------------------------
            | Final WebP
            |--------------------------------------------------------------------------
            */

            $image->stripImage();


            $image->setImageFormat(
                'webp'
            );


            $image->setImageCompressionQuality(
                (int) config(
                    'reader.render.quality',
                    82
                )
            );


            $content =
                $image->getImageBlob();


            if (
                $content === false
                ||
                $content === ''
            ) {
                throw new RuntimeException(
                    'Unable to produce watermarked page.'
                );
            }


            return [
                'content' =>
                    $content,

                'mime_type' =>
                    'image/webp',
            ];
        }
        catch (Throwable $exception) {
            throw new RuntimeException(
                'Unable to watermark page '
                . $page->page_number
                . ': '
                . $exception->getMessage(),
                previous: $exception
            );
        }
        finally {
            $image->clear();
            $image->destroy();

            if (isset($draw)) {
                $draw->clear();
            }

            if (isset($footer)) {
                $footer->clear();
            }
        }
    }


    /**
     * @return array<int,string>
     */
    private function watermarkLines(
        User $user,
        ReaderSession $readerSession
    ): array {
        $items = [];


        if (
            config(
                'reader.watermark.include_name',
                true
            )
        ) {
            $items[] =
                $this->safeText(
                    $user->name
                );
        }


        if (
            config(
                'reader.watermark.include_email',
                true
            )
            &&
            filled($user->email)
        ) {
            $items[] =
                $this->safeText(
                    $user->email
                );
        }


        if (
            config(
                'reader.watermark.include_school',
                true
            )
            &&
            $readerSession->school
        ) {
            $items[] =
                $this->safeText(
                    $readerSession
                        ->school
                        ->name
                );
        }


        if (
            config(
                'reader.watermark.include_forensic_id',
                true
            )
        ) {
            $items[] =
                $readerSession->forensic_id;
        }


        if (
            config(
                'reader.watermark.include_timestamp',
                true
            )
        ) {
            $items[] =
                now()->format(
                    'd M Y'
                );
        }


        return array_values(
            array_filter(
                $items
            )
        );
    }


    /**
     * Prevent control characters or unbounded text
     * from entering image rendering.
     */
    private function safeText(
        ?string $value
    ): string {
        $value =
            trim(
                preg_replace(
                    '/[\x00-\x1F\x7F]/u',
                    '',
                    (string) $value
                )
                ?? ''
            );


        return mb_substr(
            $value,
            0,
            80
        );
    }


    private function assertRuntimeAvailable(): void
    {
        if (! class_exists(Imagick::class)) {
            throw new RuntimeException(
                'The PHP Imagick extension is required for secure reader watermarking.'
            );
        }


        if (! class_exists(ImagickDraw::class)) {
            throw new RuntimeException(
                'Imagick drawing support is unavailable.'
            );
        }


        $formats =
            array_map(
                'strtoupper',
                Imagick::queryFormats()
            );


        if (
            ! in_array(
                'WEBP',
                $formats,
                true
            )
        ) {
            throw new RuntimeException(
                'ImageMagick does not currently support WebP output.'
            );
        }
    }
}