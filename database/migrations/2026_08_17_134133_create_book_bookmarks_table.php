<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_bookmarks', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->unsignedInteger('page');

            $table
                ->string('label')
                ->nullable();

            $table
                ->text('note')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'book_id',
                'user_id',
                'page',
            ]);

            $table->index([
                'user_id',
                'book_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_bookmarks');
    }
};