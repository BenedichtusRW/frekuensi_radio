<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_exports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path')->unique();
            $table->unsignedBigInteger('file_size')->default(0);

            $table->string('filter_kategori', 50)->nullable();
            $table->unsignedSmallInteger('filter_tahun')->nullable();
            $table->unsignedTinyInteger('filter_bulan')->nullable();
            $table->unsignedTinyInteger('filter_tanggal')->nullable();
            $table->date('filter_tanggal_lengkap')->nullable();
            $table->unsignedInteger('row_count')->default(0);

            $table->timestamp('exported_at')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['filter_tahun', 'filter_bulan', 'filter_tanggal'], 'monitoring_exports_filter_date_index');
            $table->index('filter_kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_exports');
    }
};
