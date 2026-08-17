<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Reading Activity
        |--------------------------------------------------------------------------
        |
        | Stores auditable reader activity such as:
        |
        | - reader_started
        | - reader_ended
        | - page_view
        | - bookmark
        | - borrow
        | - return
        | - access_denied
        |
        */

        if (! Schema::hasTable('reading_activities')) {

            Schema::create(
                'reading_activities',
                function (Blueprint $table) {

                    $table->id();


                    /*
                    |--------------------------------------------------------------------------
                    | Related Reader Session
                    |--------------------------------------------------------------------------
                    */

                    $table->foreignId(
                        'reader_session_id'
                    )
                        ->nullable()
                        ->constrained(
                            'reader_sessions'
                        )
                        ->nullOnDelete();


                    /*
                    |--------------------------------------------------------------------------
                    | User
                    |--------------------------------------------------------------------------
                    */

                    $table->foreignId(
                        'user_id'
                    )
                        ->constrained()
                        ->cascadeOnDelete();


                    /*
                    |--------------------------------------------------------------------------
                    | Book
                    |--------------------------------------------------------------------------
                    */

                    $table->foreignId(
                        'book_id'
                    )
                        ->constrained()
                        ->cascadeOnDelete();


                    /*
                    |--------------------------------------------------------------------------
                    | School
                    |--------------------------------------------------------------------------
                    */

                    $table->foreignId(
                        'school_id'
                    )
                        ->nullable()
                        ->constrained()
                        ->nullOnDelete();


                    /*
                    |--------------------------------------------------------------------------
                    | Device
                    |--------------------------------------------------------------------------
                    */

                    $table->foreignId(
                        'registered_device_id'
                    )
                        ->nullable()
                        ->constrained(
                            'registered_devices'
                        )
                        ->nullOnDelete();


                    /*
                    |--------------------------------------------------------------------------
                    | Reading Position
                    |--------------------------------------------------------------------------
                    */

                    $table->unsignedInteger(
                        'page_number'
                    )
                        ->nullable();


                    /*
                    |--------------------------------------------------------------------------
                    | Event
                    |--------------------------------------------------------------------------
                    |
                    | Example values:
                    |
                    | reader_started
                    | reader_ended
                    | page_view
                    | bookmark
                    | borrow
                    | return
                    | access_denied
                    |
                    */

                    $table->string(
                        'event_type',
                        60
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Request Context
                    |--------------------------------------------------------------------------
                    */

                    $table->string(
                        'ip_address',
                        45
                    )
                        ->nullable();


                    /*
                    |--------------------------------------------------------------------------
                    | Extra Metadata
                    |--------------------------------------------------------------------------
                    |
                    | Example:
                    |
                    | {
                    |     "previous_page": 17,
                    |     "source": "reader",
                    |     "reason": "navigation"
                    | }
                    |
                    */

                    $table->json(
                        'metadata'
                    )
                        ->nullable();


                    /*
                    |--------------------------------------------------------------------------
                    | Actual Event Time
                    |--------------------------------------------------------------------------
                    |
                    | This is deliberately separate from created_at because
                    | activity may later be written asynchronously by a queue.
                    |
                    */

                    $table->timestamp(
                        'occurred_at'
                    );


                    $table->timestamps();


                    /*
                    |--------------------------------------------------------------------------
                    | Indexes
                    |--------------------------------------------------------------------------
                    */

                    $table->index([
                        'user_id',
                        'book_id',
                        'occurred_at',
                    ]);


                    $table->index([
                        'reader_session_id',
                        'event_type',
                    ]);


                    $table->index([
                        'school_id',
                        'occurred_at',
                    ]);


                    $table->index([
                        'book_id',
                        'page_number',
                    ]);

                }
            );
        }
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'reading_activities'
        );
    }
};