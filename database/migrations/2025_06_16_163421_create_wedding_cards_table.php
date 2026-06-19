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
        Schema::create('wedding_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('design_template_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->json('card_details'); // Store bride_name, groom_name, wedding_date, venue, etc.
            $table->text('custom_message')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('unique_url')->unique()->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_cards');
    }
};
