<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('school_class_id')
                ->constrained('school_classes')
                ->cascadeOnDelete();

            $table
                ->foreignId('teacher_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name');

            $table
                ->string('status')
                ->default('active');

            $table->timestamps();

            $table->unique([
                'school_class_id',
                'name',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streams');
    }
};