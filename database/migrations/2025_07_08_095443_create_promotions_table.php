<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('promotion_id')->unique();
            $table->string('name_en');
            $table->string('name_km');
            $table->string('banner')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_km')->nullable();
            $table->enum('type', ['percent', 'flat']);
            $table->decimal('discount_value', 10, 2);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotions');
    }
};
