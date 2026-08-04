<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('schools', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique();
            $table->string('registration_number')->nullable()->unique(); $table->string('type')->default('secondary');
            $table->string('county')->nullable(); $table->string('town')->nullable();
            $table->string('email')->nullable(); $table->string('phone')->nullable();
            $table->string('status')->default('pending')->index(); $table->unsignedInteger('student_limit')->nullable(); $table->timestamps();
        });
        Schema::create('school_user', function (Blueprint $table) {
            $table->id(); $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->index(); $table->string('status')->default('active'); $table->timestamps();
            $table->unique(['school_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('school_user'); Schema::dropIfExists('schools'); }
};
