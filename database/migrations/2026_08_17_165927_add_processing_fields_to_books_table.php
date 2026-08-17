<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {

            /*
             * Public UUID used for private storage directory names.
             */
            $table->uuid('storage_uuid')
                ->nullable()
                ->unique();

            /*
             * Original PDF remains private.
             */
            $table->string('original_pdf_path')
                ->nullable();

            /*
             * Processing state is deliberately separate
             * from editorial publication status.
             */
            $table->string(
                'processing_status',
                30
            )
                ->default('pending')
                ->index();

            $table->unsignedInteger(
                'processed_page_count'
            )
                ->default(0);

            $table->unsignedInteger(
                'render_version'
            )
                ->default(1);

            $table->timestamp(
                'processing_started_at'
            )
                ->nullable();

            $table->timestamp(
                'processing_completed_at'
            )
                ->nullable();

            $table->timestamp(
                'processing_failed_at'
            )
                ->nullable();

            $table->text(
                'processing_error'
            )
                ->nullable();

            /*
             * Useful if the original source is replaced.
             */
            $table->string(
                'source_checksum',
                64
            )
                ->nullable();
        });
    }


    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'storage_uuid',
                'original_pdf_path',
                'processing_status',
                'processed_page_count',
                'render_version',
                'processing_started_at',
                'processing_completed_at',
                'processing_failed_at',
                'processing_error',
                'source_checksum',
            ]);
        });
    }
};