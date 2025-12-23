<?php

use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->uuid('user_id')->nullable();
            $table->uuid('delivery_id')->nullable();
            $table->decimal('delivery_fee', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default(OrderStatus::PENDING);           // order status only
            $table->uuid('payment_method_id')->nullable();           // Add this field
            $table->uuid('address_id')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('note')->nullable();   // Add this field

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('set null');
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
