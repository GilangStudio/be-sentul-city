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
        Schema::create('career_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // "Marketing Executive"
            $table->string('slug')->unique();
            $table->enum('type', ['full-time', 'part-time', 'contract', 'internship'])->default('full-time');
            $table->string('location'); // Work location
            $table->longText('responsibilities'); // Rich text content - Tanggung Jawab
            $table->longText('requirements'); // Rich text content - Requirements  
            $table->longText('benefits')->nullable(); // Rich text content - Benefits (Optional)
            $table->date('posted_at'); // Job posting date
            $table->date('closing_date')->nullable(); // Application deadline
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
        Schema::dropIfExists('career_positions');
    }
};
