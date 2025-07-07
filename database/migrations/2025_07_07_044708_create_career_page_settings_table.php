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
        Schema::create('career_page_settings', function (Blueprint $table) {
            $table->id();

            // Banner Section
            $table->string('banner_image_path');
            $table->string('banner_alt_text')->nullable();
            $table->string('banner_title')->default('Career Opportunities');
            // $table->text('banner_subtitle')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Contact Information
            $table->string('hr_email')->nullable();
            $table->string('hr_phone')->nullable();
            
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
        Schema::dropIfExists('career_page_settings');
    }
};
