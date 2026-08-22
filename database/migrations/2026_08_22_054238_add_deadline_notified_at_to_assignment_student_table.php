<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'assignment_student',
            function (Blueprint $table) {
                $table->timestamp('deadline_notified_at')
                    ->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'assignment_student',
            function (Blueprint $table) {
                $table->dropColumn(
                    'deadline_notified_at'
                );
            }
        );
    }
};