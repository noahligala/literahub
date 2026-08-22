<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'assignment_submissions',
            function (Blueprint $table) {

                $table->string('attachment_path')
                    ->nullable()
                    ->after('response');

                $table->string('attachment_original_name')
                    ->nullable()
                    ->after('attachment_path');

                $table->string('attachment_mime_type', 120)
                    ->nullable()
                    ->after('attachment_original_name');

                $table->unsignedBigInteger('attachment_size')
                    ->nullable()
                    ->after('attachment_mime_type');

                /*
                 * Teacher's score before a late penalty.
                 */
                $table->unsignedInteger('raw_score')
                    ->nullable()
                    ->after('score');

                /*
                 * Marks deducted after applying late policy.
                 */
                $table->decimal('late_penalty', 8, 2)
                    ->default(0)
                    ->after('raw_score');

                $table->text('late_penalty_note')
                    ->nullable()
                    ->after('late_penalty');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'assignment_submissions',
            function (Blueprint $table) {
                $table->dropColumn([
                    'attachment_path',
                    'attachment_original_name',
                    'attachment_mime_type',
                    'attachment_size',
                    'raw_score',
                    'late_penalty',
                    'late_penalty_note',
                ]);
            }
        );
    }
};