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
        Schema::create('card_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_card_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // 'view', 'share', 'rsvp_yes', 'rsvp_no'
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->json('metadata')->nullable(); // Additional data like device type, etc.
            $table->timestamps();

            $table->index(['wedding_card_id', 'event_type']);
            $table->index(['wedding_card_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_analytics');
    }
};
