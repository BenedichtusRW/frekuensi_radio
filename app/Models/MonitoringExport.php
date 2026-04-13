<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringExport extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
}
