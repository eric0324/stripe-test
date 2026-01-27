<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->string('email');
            $table->string('payment_type'); // one_time, installment
            $table->foreignId('tier_id')->nullable()->constrained('pricing_tiers')->nullOnDelete();
            $table->string('tier_name')->nullable();
            $table->unsignedInteger('original_amount'); // 原價
            $table->unsignedInteger('discount_amount')->default(0); // 折扣金額
            $table->unsignedInteger('final_amount'); // 實付金額
            $table->string('coupon_code')->nullable(); // 折價券代碼
            $table->string('currency', 10)->default('KRW');
            $table->string('status')->default('succeeded'); // succeeded, failed, refunded
            $table->timestamps();

            $table->index('email');
            $table->index('coupon_code');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
