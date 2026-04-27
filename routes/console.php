<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Note: Database cleanup is now handled by CleanupExpiredData job
// triggered automatically via TriggerCleanupMiddleware.
// No scheduler tasks needed - cleanup runs async via queue system.

