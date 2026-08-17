<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_student', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('assignment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->string('status')
                ->default('pending');

            $table
                ->decimal('score', 6, 2)
                ->nullable();

            $table
                ->dateTime('submitted_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'assignment_id',
                'user_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_student');
    }
};