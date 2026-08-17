<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'school_book_licenses',
            function (Blueprint $table) {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | School
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('school_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Book
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('book_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Licensor
                |--------------------------------------------------------------------------
                |
                | A licence may be issued either by the publisher or directly
                | by an author who retains the relevant distribution rights.
                |
                */

                $table
                    ->foreignId('publisher_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table
                    ->foreignId('author_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Licence Identity
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('license_number')
                    ->unique();

                $table
                    ->enum(
                        'license_type',
                        [
                            'lease',
                            'subscription',
                            'perpetual',
                            'trial',
                        ]
                    )
                    ->default('lease');

                /*
                |--------------------------------------------------------------------------
                | Licence Period
                |--------------------------------------------------------------------------
                */

                $table
                    ->timestamp('starts_at')
                    ->useCurrent();

                $table
                    ->timestamp('expires_at')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Usage Limits
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedInteger('seat_limit')
                    ->nullable();

                $table
                    ->unsignedInteger(
                        'concurrent_reader_limit'
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Rights Granted to School
                |--------------------------------------------------------------------------
                |
                | These rights may restrict the Book-level rights but should
                | never expand beyond what the publisher/author permits.
                |
                */

                $table
                    ->boolean('allow_student_reading')
                    ->default(true);

                $table
                    ->boolean('allow_teacher_reading')
                    ->default(true);

                $table
                    ->boolean('allow_teacher_assignment')
                    ->default(true);

                $table
                    ->boolean('allow_student_borrowing')
                    ->default(true);

                $table
                    ->boolean('allow_print')
                    ->default(false);

                $table
                    ->boolean('allow_download')
                    ->default(false);

                /*
                |--------------------------------------------------------------------------
                | Licence Status
                |--------------------------------------------------------------------------
                */

                $table
                    ->enum(
                        'status',
                        [
                            'pending',
                            'active',
                            'expired',
                            'suspended',
                            'revoked',
                        ]
                    )
                    ->default('pending');

                /*
                |--------------------------------------------------------------------------
                | Commercial Information
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedBigInteger('price_minor')
                    ->nullable();

                $table
                    ->string('currency', 3)
                    ->default('KES');

                /*
                |--------------------------------------------------------------------------
                | Terms
                |--------------------------------------------------------------------------
                */

                $table
                    ->text('terms')
                    ->nullable();

                $table
                    ->text('notes')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Creation / Audit
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->timestamp('revoked_at')
                    ->nullable();

                $table
                    ->foreignId('revoked_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Indexes
                |--------------------------------------------------------------------------
                */

                $table->index([
                    'school_id',
                    'status',
                ]);

                $table->index([
                    'book_id',
                    'status',
                ]);

                $table->index([
                    'school_id',
                    'book_id',
                ]);

                $table->index([
                    'publisher_id',
                    'status',
                ]);

                $table->index([
                    'author_id',
                    'status',
                ]);

                $table->index([
                    'starts_at',
                    'expires_at',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'school_book_licenses'
        );
    }
};