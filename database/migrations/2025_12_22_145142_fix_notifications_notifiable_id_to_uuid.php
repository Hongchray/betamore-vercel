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

        // Change notifiable_id to UUID (Postgres syntax)
        DB::statement("
            ALTER TABLE notifications
            ALTER COLUMN notifiable_id TYPE CHAR(36) USING notifiable_id::CHAR(36)
        ");

        // Recreate index
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        // Rollback to BIGINT
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_type_notifiable_id_index');
        });

        DB::statement("
            ALTER TABLE notifications
            ALTER COLUMN notifiable_id TYPE BIGINT USING notifiable_id::BIGINT
        ");

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }
};
