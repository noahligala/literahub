<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_class', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('school_class_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('assigned_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table
                ->timestamp('available_from')
                ->nullable();

            $table
                ->timestamp('available_until')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'book_id',
                'school_class_id',
            ]);

            $table->index([
                'school_class_id',
                'available_from',
                'available_until',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_class');
    }
};