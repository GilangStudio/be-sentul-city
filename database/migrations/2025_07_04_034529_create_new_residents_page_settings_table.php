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
        Schema::create('new_residents_page_settings', function (Blueprint $table) {
            $table->id();

            // Banner Section
            $table->string('banner_image_path');
            $table->string('banner_alt_text')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Neighborhood Guide Section
            $table->string('neighborhood_title')->default('Neighborhood Guide');
            $table->longText('neighborhood_description');
            $table->string('neighborhood_image_path')->nullable();
            $table->string('neighborhood_image_alt_text')->nullable();
            
            // Meta
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_residents_page_settings');
    }
};
