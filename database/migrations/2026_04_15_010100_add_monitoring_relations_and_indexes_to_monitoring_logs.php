<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            // Optional relation to the final monitoring record when a log has been promoted.
            $table->foreignId('monitoring_id')
                ->nullable()
                ->after('id')
                ->constrained('monitorings')
                ->nullOnDelete();

            $table->index('created_at', 'monitoring_logs_created_at_idx');
            $table->index(['monitoring_id', 'created_at'], 'monitoring_logs_monitoring_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropIndex('monitoring_logs_created_at_idx');
            $table->dropIndex('monitoring_logs_monitoring_created_at_idx');
            $table->dropConstrainedForeignId('monitoring_id');
        });
    }
};
