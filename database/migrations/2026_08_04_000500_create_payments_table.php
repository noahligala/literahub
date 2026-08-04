<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gateway')->index(); $table->string('provider_reference')->nullable()->unique();
            $table->string('payer_reference')->nullable()->index(); $table->unsignedBigInteger('amount_minor'); $table->char('currency', 3)->default('KES');
            $table->string('status')->default('pending')->index(); $table->timestamp('paid_at')->nullable(); $table->longText('payload')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
