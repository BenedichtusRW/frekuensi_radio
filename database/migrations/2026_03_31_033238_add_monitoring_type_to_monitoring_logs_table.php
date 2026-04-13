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
            $table->string('monitoring_type', 20)->default('other')->after('source_row');
            $table->index('monitoring_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropIndex(['monitoring_type']);
            $table->dropColumn('monitoring_type');
        });
    }
};
