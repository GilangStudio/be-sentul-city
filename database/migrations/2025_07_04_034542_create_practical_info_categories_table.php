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
        Schema::create('practical_info_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Tempat Ibadah"
            $table->string('slug')->unique();
            $table->string('title'); // "Rumah Ibadah di Kawasan Sentul City"
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
        Schema::dropIfExists('practical_info_categories');
    }
};
