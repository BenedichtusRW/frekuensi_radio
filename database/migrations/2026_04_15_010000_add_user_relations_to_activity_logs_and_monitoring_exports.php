<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['user_id', 'created_at'], 'activity_logs_user_created_at_idx');
        });

        Schema::table('monitoring_exports', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['user_id', 'exported_at'], 'monitoring_exports_user_exported_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_exports', function (Blueprint $table) {
            $table->dropIndex('monitoring_exports_user_exported_at_idx');
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_user_created_at_idx');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
