<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_borrowings', function (Blueprint $table) {
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
                ->foreignId('school_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->timestamp('borrowed_at');

            $table
                ->timestamp('due_at')
                ->nullable();

            $table
                ->timestamp('returned_at')
                ->nullable();

            $table
                ->enum('status', [
                    'borrowed',
                    'returned',
                    'expired',
                ])
                ->default('borrowed');

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'book_id',
                'status',
            ]);

            $table->index([
                'school_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_borrowings');
    }
};