<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

trait LogsActivity
{
    /**
     * Catat aktivitas pengguna ke tabel activity_logs.
     * Hanya mencatat aksi penting terkait data sistem (Laporan & Kelola User).
     */
    protected function logActivity(Request $request, string $action, string $description): void
    {
        // Filter: Hanya simpan log yang berhubungan dengan manipulasi data sistem
        $allowedActions = [
            'add_data', 'edit_data', 'delete_data', 'bulk_delete', 'delete_all', 'export_xlsx',
            'add_user', 'edit_user', 'delete_user', 'send_reset_link',
            'failed_login', 'failed_2fa', 'brute_force_detected', 'suspicious_access', 'unauthorized_access',
            'database_backup', 'restore_sql'
        ];

        if (!in_array($action, $allowedActions)) {
            return;
        }

        $ip = $request->ip();
        $ua = $request->userAgent() ?? '';

        // Jalankan logging setelah response terkirim agar render halaman tetap cepat.
        app()->terminating(function () use ($ip, $ua, $action, $description) {
            try {
                // Deteksi browser
                $browser = 'Unknown';
                $browserMap = [
                    'Edg'     => 'Microsoft Edge',
                    'OPR'     => 'Opera',
                    'Chrome'  => 'Chrome',
                    'Firefox' => 'Firefox',
                    'Safari'  => 'Safari',
                    'MSIE'    => 'IE',
                ];
                foreach ($browserMap as $key => $name) {
                    if (str_contains($ua, $key)) { $browser = $name; break; }
                }

                // Deteksi OS
                $platform = 'Unknown';
                if (str_contains($ua, 'Windows'))     $platform = 'Windows';
                elseif (str_contains($ua, 'Android')) $platform = 'Android';
                elseif (str_contains($ua, 'iPhone'))  $platform = 'iOS (iPhone)';
                elseif (str_contains($ua, 'iPad'))    $platform = 'iOS (iPad)';
                elseif (str_contains($ua, 'Mac'))     $platform = 'macOS';
                elseif (str_contains($ua, 'Linux'))   $platform = 'Linux';

                // Deteksi tipe device
                $device = 'Desktop';
                if (preg_match('/iPhone|Android.*Mobile/i', $ua)) $device = 'Mobile';
                elseif (preg_match('/iPad|Tablet/i', $ua))         $device = 'Tablet';

                // Lookup ISP dari IP (di-cache 24 jam per IP)
                $isp = 'N/A';
                if ($ip && !in_array($ip, ['127.0.0.1', '::1'])) {
                    $isp = Cache::remember('isp_' . md5($ip), now()->addHours(24), function () use ($ip) {
                        try {
                            $ctx = stream_context_create(['http' => ['timeout' => 3]]);
                            $res = @file_get_contents("http://ip-api.com/json/{$ip}?fields=isp,org", false, $ctx);
                            if ($res) {
                                $d = json_decode($res, true);
                                return $d['isp'] ?? $d['org'] ?? 'Unknown';
                            }
                        } catch (\Throwable) {}
                        return 'Unknown';
                    });
                } else {
                    $isp = 'Loopback/Localhost';
                }

                ActivityLog::create([
                    'user_id'     => Auth::id(),
                    'ip_address'  => $ip,
                    'browser'     => $browser,
                    'platform'    => $platform,
                    'device'      => $device,
                    'isp'         => $isp,
                    'action'      => $action,
                    'description' => $description,
                ]);
            } catch (\Throwable) {
                // Fail silently agar tidak merusak fitur utama
            }
        });
    }
}
