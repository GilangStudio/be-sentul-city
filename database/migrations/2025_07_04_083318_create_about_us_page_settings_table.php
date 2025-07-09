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
        Schema::create('about_us_page_settings', function (Blueprint $table) {
            $table->id();

            // Banner Section
            $table->string('banner_image_path');
            $table->string('banner_alt_text')->nullable();

            // Home Section Thumbnail
            $table->string('home_thumbnail_image_path')->nullable();
            $table->string('home_thumbnail_alt_text')->nullable();

            // Company Logo
            $table->string('company_logo_path')->nullable();
            $table->string('company_logo_alt_text')->nullable();
            
            // Company Info
            $table->string('company_name')->default('PT SENTUL CITY Tbk.');
            $table->text('company_description');
            $table->text('vision');
            $table->text('mission');
            
            // Statistics
            $table->integer('total_houses')->default(7800);
            $table->string('houses_label')->default('Hunian');
            $table->integer('daily_visitors')->default(40000);
            $table->string('visitors_label')->default('Pengunjung/hari');
            $table->integer('commercial_areas')->default(100);
            $table->string('commercial_label')->default('Area Komersial');
            
            // Main Section 1 - Building as Comfortable City
            $table->string('main_section1_image_path');
            $table->string('main_section1_image_alt_text')->nullable();
            $table->string('main_section1_title')->default('Building Sentul City as a Comfortable City');
            $table->text('main_section1_description');
            
            // Main Section 2 - More than Just Resident Services
            $table->string('main_section2_image_path');
            $table->string('main_section2_image_alt_text')->nullable();
            $table->string('main_section2_title')->default('More than Just Resident Services');
            $table->text('main_section2_description');
            
            // Contact Info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('website_url')->nullable();

            // Social Media Links
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us_page_settings');
    }
};
