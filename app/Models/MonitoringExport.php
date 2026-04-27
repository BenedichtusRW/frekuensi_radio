<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringExport extends Model
{
    use SoftDeletes;

    protected $table = 'monitoring_export_histories';

    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'file_size',
        'filter_kategori',
        'filter_tahun',
        'filter_bulan',
        'filter_tanggal',
        'filter_tanggal_lengkap',
        'row_count',
        'exported_at',
    ];

    protected $casts = [
        'filter_tanggal_lengkap' => 'date',
        'exported_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
