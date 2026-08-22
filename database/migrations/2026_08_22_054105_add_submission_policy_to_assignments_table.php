<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {

            /*
             * allow
             * allow_with_penalty
             * reject
             */
            $table->string('late_submission_policy', 30)
                ->default('allow')
                ->after('status');

            /*
             * fixed
             * percentage
             */
            $table->string('late_penalty_type', 20)
                ->nullable()
                ->after('late_submission_policy');

            $table->decimal('late_penalty_value', 8, 2)
                ->nullable()
                ->after('late_penalty_type');
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn([
                'late_submission_policy',
                'late_penalty_type',
                'late_penalty_value',
            ]);
        });
    }
};