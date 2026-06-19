<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'max_cards_per_user',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Maximum number of wedding cards each user can create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_card_expiry_days',
                'value' => '365',
                'type' => 'integer',
                'description' => 'Default number of days cards remain active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'allow_custom_domains',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow users to use custom domains',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'enable_analytics_tracking',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable analytics tracking for cards',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'auto_approve_cards',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Automatically approve new cards without admin review',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
}; 