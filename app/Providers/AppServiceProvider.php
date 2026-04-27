<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Register icon helper globally for Blade templates
        \Blade::directive('icon', function ($expression) {
            $params = array_map('trim', explode(',', trim($expression, '\'"')));
            $iconName = trim(array_shift($params), '\'"');
            $attributes = trim(implode(',', $params), '\'"');
            
            return "<?php 
            \$iconPath = resource_path('icons/{$iconName}.svg');
            if (file_exists(\$iconPath)) {
                echo file_get_contents(\$iconPath);
            }
            ?>";
        });

        // SLOW QUERY LOGGING: Log queries slower than 100ms
        // This helps identify bottlenecks in database performance
        if (config('app.debug')) {
            DB::listen(function (QueryExecuted $query) {
                $slowThreshold = (int) env('DB_LOG_SLOW_MS', 100);

                // Log queries that exceed threshold
                if ($query->time > $slowThreshold) {
                    Log::warning('Slow Query Detected', [
                        'duration_ms' => $query->time,
                        'query' => $query->sql,
                        'bindings' => $query->bindings,
                        'threshold_ms' => $slowThreshold,
                    ]);
                }
            });
        }
    }
}

