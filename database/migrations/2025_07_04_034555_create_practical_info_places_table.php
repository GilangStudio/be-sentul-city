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
        Schema::create('practical_info_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('practical_info_categories')->onDelete('cascade');
            $table->string('name'); // "Masjid Taqha Islamic Center"
            $table->text('address');
            $table->string('image_path');
            $table->string('image_alt_text')->nullable();
            $table->text('tags')->nullable(); // JSON array
            $table->string('map_url')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('practical_info_places');
    }
};
