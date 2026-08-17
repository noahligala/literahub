<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('school_class_id')
                ->constrained('school_classes')
                ->cascadeOnDelete();

            $table
                ->foreignId('creator_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Will later reference the LiteraHub digital
             * resource/library model.
             */
            $table
                ->unsignedBigInteger('resource_id')
                ->nullable();

            $table->string('title');

            $table
                ->text('instructions')
                ->nullable();

            $table
                ->dateTime('due_at')
                ->nullable();

            $table
                ->string('status')
                ->default('draft');

            $table->timestamps();

            $table->index([
                'school_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};