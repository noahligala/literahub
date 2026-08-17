<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table
                ->string('code')
                ->nullable();

            $table
                ->string('level')
                ->nullable();

            $table
                ->string('academic_year')
                ->nullable();

            $table
                ->string('status')
                ->default('active');

            $table->timestamps();

            $table->unique([
                'school_id',
                'name',
                'academic_year',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};