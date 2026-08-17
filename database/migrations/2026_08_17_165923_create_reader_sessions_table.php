<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reader_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('school_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('registered_device_id')
                ->nullable()
                ->constrained('registered_devices')
                ->nullOnDelete();

            /*
             * Never store the raw reader session token.
             * Store only a SHA-256 hash.
             */
            $table->string('session_token_hash', 64)
                ->unique();

            /*
             * Random public identifier used by the reader.
             * This must not expose the database ID.
             */
            $table->uuid('public_id')
                ->unique();

            /*
             * Server-derived forensic identifier used in watermarks.
             */
            $table->string('forensic_id', 32)
                ->unique();

            $table->string('ip_address', 45)
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            /*
             * Optional hash of the browser/device characteristics.
             */
            $table->string('device_fingerprint', 128)
                ->nullable();

            /*
             * Where the reader currently is.
             */
            $table->unsignedInteger('current_page')
                ->default(1);

            /*
             * Session timestamps.
             */
            $table->timestamp('started_at');

            $table->timestamp('last_activity_at');

            $table->timestamp('expires_at');

            /*
             * Absolute expiry avoids indefinitely extended sessions.
             */
            $table->timestamp('absolute_expires_at')
                ->nullable();

            $table->timestamp('revoked_at')
                ->nullable();

            $table->string('revocation_reason')
                ->nullable();

            /*
             * Useful for future security decisions.
             */
            $table->unsignedInteger('page_requests')
                ->default(0);

            $table->unsignedInteger('denied_requests')
                ->default(0);

            $table->timestamps();


            $table->index([
                'user_id',
                'book_id',
            ]);

            $table->index([
                'school_id',
                'book_id',
            ]);

            $table->index([
                'user_id',
                'revoked_at',
                'expires_at',
            ]);

            $table->index(
                'last_activity_at'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('reader_sessions');
    }
};