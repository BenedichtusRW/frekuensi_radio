<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitoring_exports') && !Schema::hasTable('monitoring_export_histories')) {
            Schema::rename('monitoring_exports', 'monitoring_export_histories');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('monitoring_export_histories') && !Schema::hasTable('monitoring_exports')) {
            Schema::rename('monitoring_export_histories', 'monitoring_exports');
        }
    }
};
