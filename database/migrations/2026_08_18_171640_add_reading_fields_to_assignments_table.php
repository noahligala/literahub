<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'assignments',
            function (Blueprint $table) {

                $table
                    ->timestamp('starts_at')
                    ->nullable()
                    ->after('instructions');

                $table
                    ->unsignedInteger('start_page')
                    ->nullable()
                    ->after('starts_at');

                $table
                    ->unsignedInteger('end_page')
                    ->nullable()
                    ->after('start_page');

                $table
                    ->unsignedInteger('total_marks')
                    ->nullable()
                    ->after('end_page');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'assignments',
            function (Blueprint $table) {

                $table->dropColumn([
                    'starts_at',
                    'start_page',
                    'end_page',
                    'total_marks',
                ]);
            }
        );
    }
};