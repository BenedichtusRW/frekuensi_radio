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
        // 1. Optimasi Tabel Master Data (Mencegah duplikasi data)
        Schema::table('master_data', function (Blueprint $table) {
            // Drop existing index if it exists to replace with unique
            $table->dropIndex(['category', 'is_active']);
            $table->unique(['category', 'value'], 'idx_master_data_unique_val');
            $table->index(['category', 'is_active'], 'idx_master_data_lookup');
        });

        // 2. Optimasi Tabel Monitorings (Mempercepat fitur Filter & Pencarian)
        Schema::table('monitorings', function (Blueprint $table) {
            // Indeks untuk kolom yang sering dicari melalui dropdown "Kata Kunci"
            $table->index('stasiun_monitor', 'idx_monitorings_stasiun');
            $table->index('administrasi_termonitor', 'idx_monitorings_admin');
            
            // Indeks komposit untuk filter Kategori + Waktu (Sangat sering dipakai)
            // Cek apakah sudah ada yang mirip, jika belum tambahkan yang paling efisien
            $table->index(['kategori', 'tahun', 'bulan', 'tanggal'], 'idx_monitorings_filter_full');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_data', function (Blueprint $table) {
            $table->dropUnique('idx_master_data_unique_val');
            $table->dropIndex('idx_master_data_lookup');
            $table->index(['category', 'is_active']);
        });

        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropIndex('idx_monitorings_stasiun');
            $table->dropIndex('idx_monitorings_admin');
            $table->dropIndex('idx_monitorings_filter_full');
        });
    }
};
