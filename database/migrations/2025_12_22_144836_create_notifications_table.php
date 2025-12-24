<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('type');
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();

            // ✅ ALWAYS boolean
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent')->default(false);

            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(columns: ['user_id', 'created_at']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};