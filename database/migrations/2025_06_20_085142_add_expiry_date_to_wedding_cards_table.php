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
        if (!Schema::hasColumn('wedding_cards', 'expiry_date')) {
            Schema::table('wedding_cards', function (Blueprint $table) {
                $table->timestamp('expiry_date')->nullable()->after('unique_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wedding_cards', function (Blueprint $table) {
            $table->dropColumn('expiry_date');
        });
    }
};
