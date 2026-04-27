<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Composite indexes to accelerate the primary ORDER BY clause used in
     * monitoringFilteredQuery(): tahun DESC, bulan DESC, tanggal DESC, jam_mulai DESC, id DESC.
     *
     * Without these indexes, MySQL must perform a full filesort on every paginated
     * request — which becomes a critical bottleneck at 10k+ rows.
     */
    public function up(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            // Covers the main ORDER BY clause for the laporan listing page.
            // MySQL can use this index to serve sorted results directly
            // without an expensive filesort operation.
            $table->index(
                ['tahun', 'bulan', 'tanggal', 'jam_mulai', 'id'],
                'idx_monitorings_sort_composite'
            );

            // Covers the most common filter + sort pattern:
            // WHERE kategori = ? ORDER BY tahun DESC, bulan DESC, tanggal DESC
            // This allows MySQL to filter and sort in a single index scan.
            $table->index(
                ['kategori', 'tahun', 'bulan', 'tanggal'],
                'idx_monitorings_filter_sort'
            );
        });
    }

    public function down(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->dropIndex('idx_monitorings_sort_composite');
            $table->dropIndex('idx_monitorings_filter_sort');
        });
    }
};
