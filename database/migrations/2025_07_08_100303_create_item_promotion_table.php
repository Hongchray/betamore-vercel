<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('item_promotion', function (Blueprint $table) {
            $table->uuid('item_id');
            $table->uuid('promotion_id');
            $table->timestamps();
            
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            
            $table->primary(['item_id', 'promotion_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_promotion');
    }
};
