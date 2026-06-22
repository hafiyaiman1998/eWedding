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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Theme preferences
            $table->enum('theme', ['light', 'dark', 'auto'])->default('light');
            $table->enum('color_scheme', ['default', 'pink', 'purple', 'blue', 'green', 'orange'])->default('default');

            // Layout preferences
            $table->boolean('sidebar_collapsed')->default(false);
            $table->enum('layout_density', ['comfortable', 'compact', 'spacious'])->default('comfortable');
            $table->enum('font_size', ['small', 'medium', 'large'])->default('medium');

            // Animation preferences
            $table->boolean('floating_hearts_enabled')->default(true);
            $table->boolean('animations_enabled')->default(true);
            $table->enum('animation_speed', ['slow', 'normal', 'fast'])->default('normal');

            // Background preferences
            $table->enum('background_theme', ['romantic', 'elegant', 'modern', 'nature', 'sunset', 'ocean', 'royal', 'minimal'])->default('romantic');
            $table->boolean('background_animation_enabled')->default(true);
            $table->enum('background_opacity', ['light', 'medium', 'bold'])->default('medium');
            $table->boolean('background_blur_enabled')->default(false);

            // Notification preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('browser_notifications')->default(false);
            $table->boolean('marketing_emails')->default(false);

            // Dashboard preferences
            $table->json('dashboard_widgets')->nullable(); // Store which widgets to show/hide
            $table->enum('card_view_mode', ['grid', 'list'])->default('grid');
            $table->integer('items_per_page')->default(12);

            // Language and locale
            $table->string('language', 10)->default('en');
            $table->string('timezone', 50)->default('UTC');
            $table->enum('date_format', ['Y-m-d', 'd/m/Y', 'm/d/Y', 'F j Y'])->default('Y-m-d');

            $table->timestamps();

            // Ensure one preference record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
