<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('resources', function (Blueprint $table) {
            $table->id(); $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title'); $table->string('slug')->unique(); $table->text('description')->nullable();
            $table->string('resource_type')->index(); $table->string('genre')->nullable()->index(); $table->string('education_level')->nullable()->index();
            $table->string('language')->default('English'); $table->string('isbn')->nullable()->unique(); $table->string('edition')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable(); $table->string('cover_path')->nullable(); $table->string('file_path');
            $table->string('filesystem_disk')->default('resources'); $table->string('status')->default('draft')->index();
            $table->boolean('is_downloadable')->default(false); $table->unsignedInteger('preview_pages')->default(0); $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('resources'); }
};
