<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('publisher_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table
                ->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            $table->string('title');

            $table->string('slug')->unique();

            $table->string('isbn', 20)->unique();

            $table->string('edition')->nullable();

            $table
                ->unsignedSmallInteger('publication_year')
                ->nullable();

            $table
                ->string('language', 50)
                ->default('English');

            $table->string('category')->nullable();

            $table->text('description')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Files
            |--------------------------------------------------------------------------
            */

            $table->string('cover_path')->nullable();

            $table->string('pdf_path');

            $table
                ->unsignedInteger('page_count')
                ->nullable();

            $table
                ->unsignedBigInteger('file_size')
                ->nullable();

            $table
                ->string('file_hash', 64)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Review Workflow
            |--------------------------------------------------------------------------
            */

            $table
                ->enum('status', [
                    'draft',
                    'under_review',
                    'changes_requested',
                    'approved',
                    'published',
                    'rejected',
                    'archived',
                ])
                ->default('under_review');

            $table
                ->timestamp('submitted_at')
                ->nullable();

            $table
                ->timestamp('reviewed_at')
                ->nullable();

            $table
                ->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table
                ->text('review_notes')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Rights
            |--------------------------------------------------------------------------
            */

            $table
                ->boolean('allow_online_reading')
                ->default(true);

            $table
                ->boolean('allow_download')
                ->default(false);

            $table
                ->boolean('allow_print')
                ->default(false);

            $table
                ->boolean('allow_teacher_assignment')
                ->default(true);

            $table
                ->boolean('allow_student_borrowing')
                ->default(true);


            /*
            |--------------------------------------------------------------------------
            | Lending
            |--------------------------------------------------------------------------
            */

            $table
                ->unsignedSmallInteger('loan_days')
                ->default(14);

            $table
                ->unsignedSmallInteger('max_concurrent_loans')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Intellectual Property
            |--------------------------------------------------------------------------
            */

            $table
                ->text('rights_statement')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('status');
            $table->index('category');
            $table->index('title');
            $table->index('publisher_id');
            $table->index('file_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};