<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->unsignedSmallInteger('tahun')->nullable()->after('bulan');
            $table->index(['tahun', 'bulan', 'tanggal'], 'monitorings_tahun_bulan_tanggal_index');
        });
    }

    public function down(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropIndex('monitorings_tahun_bulan_tanggal_index');
            $table->dropColumn('tahun');
        });
    }
};
