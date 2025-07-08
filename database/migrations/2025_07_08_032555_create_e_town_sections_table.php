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
        Schema::create('e_town_sections', function (Blueprint $table) {
            $table->id();

            // App Images
            $table->string('app_mockup_image_path');
            $table->string('app_mockup_alt_text')->nullable();
            
            // Content
            $table->string('section_title')->default('E-Town, Feel Sentul City in Your Hand!');
            $table->text('description');
            
            // App Store Links
            $table->string('google_play_url')->nullable();
            $table->string('app_store_url')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_town_sections');
    }
};
