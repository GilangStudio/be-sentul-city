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
        Schema::create('home_page_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Optional banner title
            $table->text('subtitle')->nullable(); // Optional banner subtitle  
            $table->string('button_text')->nullable(); // Optional button text
            $table->string('button_url')->nullable(); // Optional button link
            $table->string('image_path'); // Required banner image
            $table->string('image_alt_text')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_banners');
    }
};
