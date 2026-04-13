<?php

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
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('classification_rule');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->index('is_archived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropIndex(['is_archived']);
            $table->dropColumn(['is_archived', 'archived_at']);
        });
    }
};
