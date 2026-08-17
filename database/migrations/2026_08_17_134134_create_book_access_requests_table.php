<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_access_requests', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table
                ->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('teacher_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table
                ->text('reason')
                ->nullable();

            $table
                ->enum('status', [
                    'pending',
                    'approved',
                    'rejected',
                    'expired',
                ])
                ->default('pending');

            $table
                ->timestamp('reviewed_at')
                ->nullable();

            $table
                ->timestamp('expires_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'student_id',
                'status',
            ]);

            $table->index([
                'teacher_id',
                'status',
            ]);

            $table->index([
                'school_id',
                'status',
            ]);

            $table->index([
                'book_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'book_access_requests'
        );
    }
};