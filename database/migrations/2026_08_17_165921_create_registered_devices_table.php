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
        | Create table if it does not exist
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasTable('registered_devices')) {

            Schema::create(
                'registered_devices',
                function (Blueprint $table) {
                    $table->id();

                    $table->foreignId('user_id')
                        ->constrained()
                        ->cascadeOnDelete();

                    $table->uuid('device_uuid');

                    $table->string(
                        'device_token_hash',
                        64
                    )->nullable();

                    $table->string('device_name')
                        ->nullable();

                    $table->string(
                        'device_type',
                        50
                    )->nullable();

                    $table->string(
                        'browser',
                        100
                    )->nullable();

                    $table->string(
                        'platform',
                        100
                    )->nullable();

                    $table->string(
                        'last_ip_address',
                        45
                    )->nullable();

                    $table->text(
                        'last_user_agent'
                    )->nullable();

                    $table->string(
                        'fingerprint_hash',
                        128
                    )->nullable();

                    $table->timestamp(
                        'first_seen_at'
                    )->nullable();

                    $table->timestamp(
                        'last_seen_at'
                    )->nullable();

                    $table->timestamp(
                        'trusted_at'
                    )->nullable();

                    $table->timestamp(
                        'revoked_at'
                    )->nullable();

                    $table->string(
                        'revocation_reason'
                    )->nullable();

                    $table->timestamps();

                    $table->unique([
                        'user_id',
                        'device_uuid',
                    ]);

                    $table->index([
                        'user_id',
                        'revoked_at',
                    ]);

                    $table->index(
                        'last_seen_at'
                    );
                }
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Existing table
        |--------------------------------------------------------------------------
        |
        | Add only columns that are missing.
        |
        */

        if (
            ! Schema::hasColumn(
                'registered_devices',
                'user_id'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->foreignId('user_id')
                        ->nullable()
                        ->constrained()
                        ->cascadeOnDelete();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'device_uuid'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->uuid('device_uuid')
                        ->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'device_token_hash'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'device_token_hash',
                        64
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'device_name'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'device_name'
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'device_type'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'device_type',
                        50
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'browser'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'browser',
                        100
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'platform'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'platform',
                        100
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'last_ip_address'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'last_ip_address',
                        45
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'last_user_agent'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->text(
                        'last_user_agent'
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'fingerprint_hash'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'fingerprint_hash',
                        128
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'first_seen_at'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->timestamp(
                        'first_seen_at'
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'last_seen_at'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->timestamp(
                        'last_seen_at'
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'trusted_at'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->timestamp(
                        'trusted_at'
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'revoked_at'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->timestamp(
                        'revoked_at'
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'revocation_reason'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->string(
                        'revocation_reason'
                    )->nullable();
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Laravel timestamps
        |--------------------------------------------------------------------------
        */

        if (
            ! Schema::hasColumn(
                'registered_devices',
                'created_at'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->timestamp(
                        'created_at'
                    )->nullable();
                }
            );
        }


        if (
            ! Schema::hasColumn(
                'registered_devices',
                'updated_at'
            )
        ) {
            Schema::table(
                'registered_devices',
                function (Blueprint $table) {
                    $table->timestamp(
                        'updated_at'
                    )->nullable();
                }
            );
        }
    }


    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Development-safe rollback
        |--------------------------------------------------------------------------
        |
        | Because this migration may be upgrading a table that existed before
        | the secure-reader work, we should NOT automatically drop the entire
        | table during rollback.
        |
        | A destructive rollback could remove pre-existing device data.
        |
        */

        if (! Schema::hasTable('registered_devices')) {
            return;
        }


        /*
         * Only remove secure-reader-specific columns.
         *
         * Keep:
         * id
         * user_id
         * device_uuid
         * created_at
         * updated_at
         *
         * because those may have existed before this migration.
         */

        $columns = [
            'device_token_hash',
            'device_name',
            'device_type',
            'browser',
            'platform',
            'last_ip_address',
            'last_user_agent',
            'fingerprint_hash',
            'first_seen_at',
            'last_seen_at',
            'trusted_at',
            'revoked_at',
            'revocation_reason',
        ];


        foreach ($columns as $column) {

            if (
                Schema::hasColumn(
                    'registered_devices',
                    $column
                )
            ) {
                Schema::table(
                    'registered_devices',
                    function (
                        Blueprint $table
                    ) use ($column) {

                        $table->dropColumn(
                            $column
                        );

                    }
                );
            }

        }
    }
};