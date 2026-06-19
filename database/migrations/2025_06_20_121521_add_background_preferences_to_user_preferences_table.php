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
        // Skip if columns already exist (from create_user_preferences_table migration)
        if (Schema::hasColumn('user_preferences', 'background_theme')) {
            return;
        }

        Schema::table('user_preferences', function (Blueprint $table) {
            // Background preferences
            $table->enum('background_theme', ['romantic', 'elegant', 'modern', 'nature', 'sunset', 'ocean', 'royal', 'minimal'])->default('romantic')->after('animation_speed');
            $table->boolean('background_animation_enabled')->default(true)->after('background_theme');
            $table->enum('background_opacity', ['light', 'medium', 'bold'])->default('medium')->after('background_animation_enabled');
            $table->boolean('background_blur_enabled')->default(false)->after('background_opacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'background_theme',
                'background_animation_enabled', 
                'background_opacity',
                'background_blur_enabled'
            ]);
        });
    }
};
