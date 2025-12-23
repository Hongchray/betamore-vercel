<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Drop the existing morph index first
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_type_notifiable_id_index');
        });

        // Change notifiable_id to UUID
        DB::statement("
            ALTER TABLE notifications
            MODIFY notifiable_id CHAR(36) NOT NULL
        ");

        // Recreate index
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        // Rollback to BIGINT (only if you ever need it)
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_type_notifiable_id_index');
        });

        DB::statement("
            ALTER TABLE notifications
            MODIFY notifiable_id BIGINT UNSIGNED NOT NULL
        ");

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }
};
