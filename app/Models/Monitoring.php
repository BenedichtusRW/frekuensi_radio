<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $fillable = [
        'kategori',
        'kode_negara',
        'stasiun_monitor',
        'frekuensi_khz',
        'tanggal',
        'bulan',
        'tahun',
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
}
