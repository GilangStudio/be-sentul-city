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
        Schema::create('transportation_items', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // "Sinar Jaya"
            $table->text('description'); // "Arah Rawamangun, Grogol, Blok M"
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
        Schema::dropIfExists('transportation_items');
    }
};
