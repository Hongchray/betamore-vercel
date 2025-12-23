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
        // Drop existing table if it exists
        Schema::dropIfExists('banners');
        
        // Create new banners table with proper structure
        Schema::create('banners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('banner_id', 50)->unique();
            $table->string('name');
            $table->text('banner_image');
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('banner_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
