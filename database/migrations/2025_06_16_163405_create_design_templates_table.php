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
        Schema::create('design_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('blade_template');
            $table->string('category')->default('general'); // e.g., 'malaysian', 'modern', 'traditional'
            $table->boolean('is_malaysian_design')->default(false);
            $table->string('preview_image')->nullable();
            $table->json('default_variables')->nullable(); // Store default template variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_templates');
    }
};
