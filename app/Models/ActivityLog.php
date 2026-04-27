<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ActivityLog extends Model
{
    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id',
        'ip_address',
        'browser',
        'platform',
        'device',
        'isp',
        'action',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label warna badge per aksi (untuk tampilan dashboard).
     */
    public function getActionBadgeColor(): string
    {
        return match ($this->action) {
            'add_data'       => 'success',
            'edit_data'      => 'warning',
            'export'         => 'info',
            'visit_laporan'  => 'secondary',
            'visit_input'    => 'secondary',
            'visit_dashboard'=> 'light',
            default          => 'dark',
        };
    }

    /**
     * Label aksi yang ramah pengguna.
     */
    public function getActionLabel(): string
    {
        return match ($this->action) {
            'add_data'        => 'Tambah Data',
            'edit_data'       => 'Edit Data',
            'export'          => 'Export',
            'visit_laporan'   => 'Buka Laporan',
            'visit_input'     => 'Buka Form Input',
            'visit_dashboard' => 'Buka Dashboard',
            default           => $this->action,
        };
    }

    /**
     * Hapus log yang lebih tua dari 30 hari dan optimalkan tabel.
     * Dipanggil secara probabilistik (tidak setiap request).
     */
    public static function pruneOldLogs(int $retentionDays = 30): void
    {
        try {
            $cutoff = now()->subDays($retentionDays);

            $deleted = static::where('created_at', '<', $cutoff)->delete();

            // Jalankan OPTIMIZE TABLE hanya jika ada baris yang benar-benar dihapus
            if ($deleted > 0) {
                DB::statement('OPTIMIZE TABLE user_activity_logs');
            }
        } catch (\Throwable) {
            // Fail silently — cleanup tidak boleh merusak fitur utama
        }
    }
}
