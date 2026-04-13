<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            // Index kolom tahun berdiri sendiri untuk query filter tahun tunggal
            // dan query GROUP BY bulan dalam satu tahun (grafik tren 12 bulan)
            $table->index('tahun', 'idx_monitorings_tahun');

            // Composite index (tahun, bulan) untuk query tren bulanan
            $table->index(['tahun', 'bulan'], 'idx_monitorings_tahun_bulan');
        });
    }

    public function down(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropIndex('idx_monitorings_tahun');
            $table->dropIndex('idx_monitorings_tahun_bulan');
        });
    }
};
