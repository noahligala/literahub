<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->string('audience')->index();
            $table->string('billing_period'); $table->unsignedBigInteger('price_minor'); $table->char('currency', 3)->default('KES');
            $table->unsignedInteger('user_limit')->nullable(); $table->json('features')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id(); $table->foreignId('subscription_plan_id')->constrained(); $table->morphs('subscriber');
            $table->string('status')->index(); $table->timestamp('starts_at')->nullable(); $table->timestamp('ends_at')->nullable()->index();
            $table->timestamp('trial_ends_at')->nullable(); $table->timestamp('cancelled_at')->nullable(); $table->boolean('auto_renew')->default(false); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subscriptions'); Schema::dropIfExists('subscription_plans'); }
};
