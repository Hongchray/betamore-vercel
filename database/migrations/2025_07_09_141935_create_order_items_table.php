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
            Schema::create('order_items', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('order_id');
                $table->uuid('item_modification_id');
                $table->string('name');
                $table->string('image')->nullable();
                $table->integer('quantity');
                $table->decimal('unit_price', 8, 2);
                $table->decimal('total_price', 10, 2);
                $table->text('notes')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('item_modification_id')->references('id')->on('item_modifications')->onDelete('cascade');

                // Indexes
                $table->index('order_id');
                $table->index('item_modification_id');
            });
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
