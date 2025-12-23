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
        Schema::create('cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payment_method_id');
            $table->string('name')->unique();
            $table->string('code')->unique()->nullable();
            $table->string('merchant_account_name'); 
            $table->string('merchant_account_number'); 
            $table->string('description')->nullable(); 
            $table->string('logo')->nullable();
            $table->json('additional_info')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
