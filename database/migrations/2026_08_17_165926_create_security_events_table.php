<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('book_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('school_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('reader_session_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('registered_device_id')
                ->nullable()
                ->constrained('registered_devices')
                ->nullOnDelete();

            /*
             * Examples:
             *
             * rate_limit_exceeded
             * invalid_session
             * device_limit_exceeded
             * concurrent_session_limit
             * suspicious_page_sequence
             * forbidden_page_access
             * expired_license
             */
            $table->string('event_type', 80);

            /*
             * info
             * low
             * medium
             * high
             * critical
             */
            $table->string('severity', 20)
                ->default('info');

            $table->string('ip_address', 45)
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->json('context')
                ->nullable();

            $table->timestamp('detected_at');

            /*
             * Admin review / security workflow.
             */
            $table->timestamp('resolved_at')
                ->nullable();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('resolution_notes')
                ->nullable();

            $table->timestamps();


            $table->index([
                'severity',
                'detected_at',
            ]);


            $table->index([
                'user_id',
                'detected_at',
            ]);


            $table->index([
                'reader_session_id',
                'detected_at',
            ]);


            $table->index([
                'event_type',
                'detected_at',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};