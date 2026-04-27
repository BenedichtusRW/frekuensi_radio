<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        // Fitur Auto-Clear Cache: Setiap ada data baru/diubah, hapus memori sementara dashboard
        static::saved(function ($monitoring) {
            $monitoring->clearDashboardCache();
        });

        static::deleted(function ($monitoring) {
            $monitoring->clearDashboardCache();
        });
    }

    public function clearDashboardCache()
    {
        $keys = [
            'dashboard_summary_stats',
            'dashboard_barchart_stats_v2',
            'dashboard_monthly_stats_v2',
        ];

        foreach ($keys as $key) {
            // Hapus cache untuk keseluruhan (Super Admin default)
            \Illuminate\Support\Facades\Cache::forget("{$key}_all");
            
            // Hapus cache untuk spesifik petugas ini (Admin / Super Admin yang memfilter)
            if ($this->user_id) {
                \Illuminate\Support\Facades\Cache::forget("{$key}_user_{$this->user_id}");
            }
        }
    }
}
