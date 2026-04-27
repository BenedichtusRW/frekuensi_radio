<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupExpiredData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $failOnTimeout = false;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->cleanupActivityLogs();
            $this->cleanupSessions();
            $this->cleanupCache();
            
            Log::info('Cleanup expired data completed successfully', [
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Cleanup expired data failed', [
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * Delete activity logs older than 30 days.
     */
    private function cleanupActivityLogs(): void
    {
        $retentionDays = (int) env('ACTIVITY_LOG_RETENTION_DAYS', 30);

        $deleted = DB::table('user_activity_logs')
            ->where('created_at', '<', now()->subDays($retentionDays))
            ->delete();

        if ($deleted > 0) {
            Log::info("Deleted {$deleted} activity logs older than {$retentionDays} days");
        }
    }

    /**
     * Delete sessions with last activity older than 7 days.
     */
    private function cleanupSessions(): void
    {
        $retentionDays = (int) env('SESSION_RETENTION_DAYS', 7);

        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays($retentionDays)->timestamp)
            ->delete();

        if ($deleted > 0) {
            Log::info("Deleted {$deleted} idle sessions older than {$retentionDays} days");
            
            // Auto OPTIMIZE if significant rows were deleted
            if ($deleted > 100) {
                DB::statement('OPTIMIZE TABLE sessions');
                Log::info('Optimized sessions table');
            }
        }
    }

    /**
     * Delete expired cache entries.
     */
    private function cleanupCache(): void
    {
        $deleted = DB::table('cache')
            ->where('expiration', '<', now()->timestamp)
            ->delete();

        if ($deleted > 0) {
            Log::info("Deleted {$deleted} expired cache entries");
            
            // Auto OPTIMIZE if significant rows were deleted
            if ($deleted > 100) {
                DB::statement('OPTIMIZE TABLE cache');
                Log::info('Optimized cache table');
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupExpiredData job failed', [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
