<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('activity_logs') && !Schema::hasTable('user_activity_logs')) {
            Schema::rename('activity_logs', 'user_activity_logs');
        }

        if (Schema::hasTable('monitoring_logs') && !Schema::hasTable('monitoring_import_logs')) {
            Schema::rename('monitoring_logs', 'monitoring_import_logs');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('monitoring_import_logs') && !Schema::hasTable('monitoring_logs')) {
            Schema::rename('monitoring_import_logs', 'monitoring_logs');
        }

        if (Schema::hasTable('user_activity_logs') && !Schema::hasTable('activity_logs')) {
            Schema::rename('user_activity_logs', 'activity_logs');
        }
    }
};
