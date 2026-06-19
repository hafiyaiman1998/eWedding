<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wedding_card_id');
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->string('bill_code')->nullable(); // toyyibPay bill code
            $table->string('bill_payment_id')->nullable(); // toyyibPay payment ID
            $table->string('external_reference_no')->unique(); // Our unique reference
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_url')->nullable(); // toyyibPay payment URL
            $table->text('message')->nullable(); // Gift message from guest
            $table->json('toyyibpay_response')->nullable(); // Store toyyibPay responses
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('wedding_card_id')->references('id')->on('wedding_cards')->onDelete('cascade');
            $table->index(['status', 'created_at']);
            $table->index('external_reference_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
