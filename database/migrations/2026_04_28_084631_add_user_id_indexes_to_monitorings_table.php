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
        Schema::table('monitorings', function (Blueprint $table) {
            $table->index('user_id', 'idx_monitorings_user_id');
            $table->index(['user_id', 'tahun', 'bulan', 'tanggal', 'jam_mulai', 'id'], 'idx_monitorings_user_sort_composite');
            $table->index(['user_id', 'kategori', 'tahun', 'bulan', 'tanggal'], 'idx_monitorings_user_kategori_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropIndex('idx_monitorings_user_id');
            $table->dropIndex('idx_monitorings_user_sort_composite');
            $table->dropIndex('idx_monitorings_user_kategori_sort');
        });
    }
};
