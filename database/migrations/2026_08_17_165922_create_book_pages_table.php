<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('page_number');

            /*
             * Private storage path.
             *
             * Example:
             * library/rendered/{book_uuid}/0001.webp
             */
            $table->string('image_path');

            $table->unsignedInteger('width')->nullable();

            $table->unsignedInteger('height')->nullable();

            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('mime_type', 100)
                ->default('image/webp');

            $table->string('checksum', 64)
                ->nullable();

            /*
             * Allows us to regenerate only outdated pages later.
             */
            $table->unsignedInteger('render_version')
                ->default(1);

            $table->timestamp('rendered_at')
                ->nullable();

            $table->timestamps();


            /*
             * A book may only have one record for each page.
             */
            $table->unique([
                'book_id',
                'page_number',
            ]);


            $table->index([
                'book_id',
                'render_version',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('book_pages');
    }
};