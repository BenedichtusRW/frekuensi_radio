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
            $table->boolean('is_forced_classification')->default(false)->after('monitoring_type');
            $table->string('classification_rule', 30)->default('range')->after('is_forced_classification');
            $table->index('is_forced_classification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropIndex(['is_forced_classification']);
            $table->dropColumn(['is_forced_classification', 'classification_rule']);
        });
    }
};
