<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Assignment
            |--------------------------------------------------------------------------
            */

            $table->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Student Response
            |--------------------------------------------------------------------------
            */

            $table->longText('response')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Submission Status
            |--------------------------------------------------------------------------
            |
            | Supported values:
            |
            | draft
            | submitted
            | late
            | graded
            |
            */

            $table->string('status', 30)
                ->default('draft');


            /*
            |--------------------------------------------------------------------------
            | Submission Timestamp
            |--------------------------------------------------------------------------
            */

            $table->timestamp('submitted_at')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Grading
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('score')
                ->nullable();

            $table->text('feedback')
                ->nullable();

            $table->timestamp('graded_at')
                ->nullable();

            $table->foreignId('graded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Laravel Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Constraints
            |--------------------------------------------------------------------------
            |
            | A learner can have only one submission record per assignment.
            | Draft → submitted → graded all operate on this same row.
            |
            */

            $table->unique([
                'assignment_id',
                'student_id',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'assignment_id',
                'status',
            ]);

            $table->index([
                'student_id',
                'status',
            ]);

            $table->index([
                'graded_by',
                'graded_at',
            ]);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'assignment_submissions'
        );
    }
};
