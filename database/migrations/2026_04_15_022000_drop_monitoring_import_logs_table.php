<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitoring_import_logs')) {
            Schema::drop('monitoring_import_logs');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('monitoring_import_logs')) {
            return;
        }

        Schema::create('monitoring_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_id')
                ->nullable()
                ->constrained('monitorings')
                ->nullOnDelete();

            $table->string('source_file');
            $table->string('sheet_name')->default('Monitoring Harian');
            $table->unsignedInteger('source_row');
            $table->string('monitoring_type', 20)->default('other');
            $table->boolean('is_forced_classification')->default(false);
            $table->string('classification_rule', 30)->default('range');
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();

            $table->string('kode_negara', 10)->nullable();
            $table->string('stasiun_monitor')->nullable();
            $table->decimal('frekuensi_khz', 12, 3)->nullable();
            $table->unsignedTinyInteger('tanggal')->nullable();
            $table->unsignedTinyInteger('bulan')->nullable();
            $table->string('jam_mulai', 10)->nullable();
            $table->string('jam_akhir', 10)->nullable();
            $table->decimal('kuat_medan_dbuvm', 8, 2)->nullable();
            $table->string('identifikasi')->nullable();
            $table->string('administrasi_termonitor', 10)->nullable();
            $table->string('kelas_stasiun', 20)->nullable();
            $table->string('lebar_band', 30)->nullable();
            $table->string('kelas_emisi', 30)->nullable();
            $table->string('perkiraan_lokasi_sumber_pancaran')->nullable();
            $table->string('longitude_derajat', 10)->nullable();
            $table->string('longitude_arah', 10)->nullable();
            $table->string('longitude_menit', 10)->nullable();
            $table->string('latitude_derajat', 10)->nullable();
            $table->string('latitude_arah', 10)->nullable();
            $table->string('latitude_menit', 10)->nullable();
            $table->string('north_bearing', 20)->nullable();
            $table->string('akurasi', 30)->nullable();
            $table->string('tidak_sesuai_rr', 30)->nullable();
            $table->text('informasi_tambahan')->nullable();

            $table->timestamps();

            $table->index(['bulan', 'tanggal']);
            $table->index('frekuensi_khz');
            $table->index('monitoring_type');
            $table->index('is_forced_classification');
            $table->index('is_archived');
            $table->index('created_at', 'monitoring_logs_created_at_idx');
            $table->index(['monitoring_id', 'created_at'], 'monitoring_logs_monitoring_created_at_idx');
            $table->unique(['source_file', 'sheet_name', 'source_row'], 'monitoring_logs_source_unique');
        });
    }
};
