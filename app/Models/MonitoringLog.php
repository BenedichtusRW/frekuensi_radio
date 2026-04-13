<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringLog extends Model
{
    protected $fillable = [
        'source_file',
        'sheet_name',
        'source_row',
        'monitoring_type',
        'is_forced_classification',
        'classification_rule',
        'is_archived',
        'archived_at',
        'kode_negara',
        'stasiun_monitor',
        'frekuensi_khz',
        'tanggal',
        'bulan',
        'jam_mulai',
        'jam_akhir',
        'kuat_medan_dbuvm',
        'identifikasi',
        'administrasi_termonitor',
        'kelas_stasiun',
        'lebar_band',
        'kelas_emisi',
        'perkiraan_lokasi_sumber_pancaran',
        'longitude_derajat',
        'longitude_arah',
        'longitude_menit',
        'latitude_derajat',
        'latitude_arah',
        'latitude_menit',
        'north_bearing',
        'akurasi',
        'tidak_sesuai_rr',
        'informasi_tambahan',
    ];

    protected $casts = [
        'is_forced_classification' => 'boolean',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];
}
