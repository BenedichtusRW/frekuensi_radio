# 🔧 Quick Security Fixes - Ready-to-Use Code

This file contains copy-paste ready code snippets to fix the identified vulnerabilities.

---

## 1. FIX: Critical Issues

### Fix 1.1 - Update .env Configuration

Replace your `.env` file with these critical settings:

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:GENERATE_THIS_WITH_ARTISAN  # See command below
APP_DEBUG=false
APP_URL=https://your-domain.com

BCRYPT_ROUNDS=12

# Session Security - CRITICAL
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Database
DB_CONNECTION=sqlite
CACHE_STORE=database
QUEUE_CONNECTION=database

# Additional Security
FORCE_HTTPS=true
MAIL_MAILER=log
LOG_CHANNEL=stack
LOG_LEVEL=warning
```

**Generate APP_KEY** (run in terminal):
```bash
php artisan key:generate
# Copy the generated key and paste in .env as APP_KEY=
```

---

### Fix 1.2 - .gitignore Update

```bash
# Add to .gitignore - NEVER commit .env files
.env
.env.*.local
.env.production
```

---

## 2. FIX: Authorization/IDOR Issues

### Fix 2.1 - Create Authorization Policy

```bash
php artisan make:policy MonitoringPolicy --model=Monitoring
```

```php
// app/Policies/MonitoringPolicy.php
<?php

namespace App\Policies;

use App\Models\Monitoring;
use App\Models\User;

class MonitoringPolicy
{
    /**
     * Determine if user can view the record
     */
    public function view(User $user, Monitoring $monitoring): bool
    {
        return true; // All authenticated users can view
    }

    /**
     * Determine if user can update the record
     */
    public function update(User $user, Monitoring $monitoring): bool
    {
        // Allow if user created it OR is admin
        return $user->id === $monitoring->user_id || $user->hasRole('admin');
    }

    /**
     * Determine if user can delete the record
     */
    public function delete(User $user, Monitoring $monitoring): bool
    {
        return $user->id === $monitoring->user_id || $user->hasRole('admin');
    }

    /**
     * Determine if user can restore the record
     */
    public function restore(User $user, Monitoring $monitoring): bool
    {
        return $user->id === $monitoring->user_id || $user->hasRole('admin');
    }

    /**
     * Determine if user can force delete the record
     */
    public function forceDelete(User $user, Monitoring $monitoring): bool
    {
        return $user->hasRole('admin');
    }
}
```

### Fix 2.2 - Update Monitoring Model

```php
// app/Models/Monitoring.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Monitoring extends Model
{
    protected $fillable = [
        'user_id',  // ADD THIS
        'kategori',
        'kode_negara',
        // ... rest of fields
    ];

    // ADD THIS RELATIONSHIP
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### Fix 2.3 - Add user_id to Migration

```bash
php artisan make:migration add_user_id_to_monitoring_logs_table
```

```php
// database/migrations/2024_04_20_add_user_id_to_monitoring_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
        });
    }
};
```

### Fix 2.4 - Update MonitoringController

```php
// app/Http/Controllers/MonitoringController.php
<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    use AuthorizesRequests;

    // EXISTING METHODS...

    // FIX: Add authorization to edit
    public function edit(Request $request, int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        
        // ✅ ADD AUTHORIZATION CHECK
        $this->authorize('update', $monitoring);
        
        return redirect()
            ->route('monitoring.index', [
                'edit_id' => $id,
                'no' => $this->toNullableInt((string) $request->query('no', '')),
            ]);
    }

    // FIX: Add authorization and rate limiting to update
    public function update(Request $request, int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        
        // ✅ ADD AUTHORIZATION CHECK
        $this->authorize('update', $monitoring);
        
        // ✅ ADD RATE LIMITING
        $throttleKey = 'monitoring.update.' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 30, 60)) {
            abort(429, 'Too many update attempts. Try again in ' . 
                RateLimiter::availableIn($throttleKey) . ' seconds.');
        }
        RateLimiter::hit($throttleKey, 60);

        // EXISTING VALIDATION AND UPDATE CODE...
        $validated = $request->validate($this->monitoringValidationRules());
        
        foreach ($validated as $key => $value) {
            if (is_string($value)) {
                $validated[$key] = htmlspecialchars(
                    strip_tags(trim($value)), 
                    ENT_QUOTES, 
                    'UTF-8'
                );
            }
        }

        $validated = $this->normalizeNumericFields($validated);
        $monitoring->update($validated);
        $this->clearDashboardCache();

        $this->logActivity($request, 'edit_data', 'Edit data ID #' . $id);

        return redirect()
            ->route('monitoring.index')
            ->with('success', 'Data monitoring berhasil diperbarui.');
    }

    // FIX: Add authorization and rate limiting to destroy
    public function destroy(int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        
        // ✅ ADD AUTHORIZATION CHECK
        $this->authorize('delete', $monitoring);
        
        // ✅ ADD RATE LIMITING
        $throttleKey = 'monitoring.delete.' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 10, 60)) {
            abort(429, 'Too many delete attempts. Try again in ' . 
                RateLimiter::availableIn($throttleKey) . ' seconds.');
        }
        RateLimiter::hit($throttleKey, 60);

        $monitoring->delete();
        $this->clearDashboardCache();

        return redirect()
            ->route('monitoring.index')
            ->with('success', 'Data monitoring berhasil dihapus.');
    }

    // FIX: Add rate limiting to store
    public function store(Request $request)
    {
        // ✅ ADD RATE LIMITING
        $throttleKey = 'monitoring.create.' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 50, 60)) {
            return back()->withErrors([
                'error' => 'Too many records created in the last minute. Try again in ' . 
                    RateLimiter::availableIn($throttleKey) . ' seconds.'
            ])->withInput();
        }
        RateLimiter::hit($throttleKey, 60);

        // EXISTING VALIDATION AND CREATION CODE...
    }
}
```

---

## 3. FIX: Insecure ISP Lookup

### Fix 3.1 - Replace ISP Lookup with Secure HTTPS

```php
// app/Http/Controllers/MonitoringController.php
// Replace the logActivity() method ISP lookup section:

private function logActivity(Request $request, string $action, string $description): void
{
    $ip = $request->ip();
    $ua = $request->userAgent() ?? '';

    app()->terminating(function () use ($ip, $ua, $action, $description) {
        try {
            // Browser detection
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

            // OS detection
            $platform = 'Unknown';
            if (str_contains($ua, 'Windows'))     $platform = 'Windows';
            elseif (str_contains($ua, 'Android')) $platform = 'Android';
            elseif (str_contains($ua, 'iPhone'))  $platform = 'iOS (iPhone)';
            elseif (str_contains($ua, 'iPad'))    $platform = 'iOS (iPad)';
            elseif (str_contains($ua, 'Mac'))     $platform = 'macOS';
            elseif (str_contains($ua, 'Linux'))   $platform = 'Linux';

            // Device detection
            $device = 'Desktop';
            if (preg_match('/iPhone|Android.*Mobile/i', $ua)) $device = 'Mobile';
            elseif (preg_match('/iPad|Tablet/i', $ua))         $device = 'Tablet';

            // ✅ SECURE ISP LOOKUP: Use HTTPS only, with proper error handling
            $isp = $this->getISPInfo($ip);

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
        } catch (\Throwable $e) {
            Log::warning('Activity logging failed', ['error' => $e->getMessage()]);
            // Fail silently
        }
    });
}

// ✅ NEW SECURE METHOD
private function getISPInfo(string $ip): string
{
    if (in_array($ip, ['127.0.0.1', '::1'], true)) {
        return 'Loopback/Localhost';
    }

    return Cache::remember('isp_' . md5($ip), now()->addHours(1), function () use ($ip) {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'ignore_errors' => false,
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                ]
            ]);

            // ✅ Use HTTPS and API key if available
            $apiKey = env('IP_API_KEY', '');
            $url = $apiKey
                ? "https://ip-api.com/json/{$ip}?fields=isp,org&key={$apiKey}"
                : "https://ip-api.com/json/{$ip}?fields=isp,org";

            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return 'Unknown';
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('ISP API invalid response', ['ip' => $ip]);
                return 'Unknown';
            }

            return $data['isp'] ?? $data['org'] ?? 'Unknown';
        } catch (\Throwable $e) {
            Log::warning('ISP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return 'Unknown';
        }
    });
}
```

---

## 4. FIX: Add Audit Trail for Data Changes

### Fix 4.1 - Create Audit Log Model

```bash
php artisan make:model AuditLog --migration
```

```php
// app/Models/AuditLog.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getModelAttribute()
    {
        return app($this->model_type);
    }
}
```

### Fix 4.2 - Create Migration

```php
// database/migrations/2024_04_20_create_audit_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->enum('action', ['CREATE', 'UPDATE', 'DELETE', 'RESTORE']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
```

### Fix 4.3 - Create Observer

```bash
php artisan make:observer MonitoringObserver --model=Monitoring
```

```php
// app/Observers/MonitoringObserver.php
<?php

namespace App\Observers;

use App\Models\Monitoring;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class MonitoringObserver
{
    /**
     * Handle the Monitoring "created" event.
     */
    public function created(Monitoring $monitoring): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => Monitoring::class,
            'model_id' => $monitoring->id,
            'action' => 'CREATE',
            'old_values' => null,
            'new_values' => $monitoring->toArray(),
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Handle the Monitoring "updated" event.
     */
    public function updated(Monitoring $monitoring): void
    {
        // Only log if actual changes occurred
        $changes = $monitoring->getChanges();
        if (empty($changes)) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => Monitoring::class,
            'model_id' => $monitoring->id,
            'action' => 'UPDATE',
            'old_values' => $monitoring->getOriginal(),
            'new_values' => $monitoring->getAttributes(),
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Handle the Monitoring "deleted" event.
     */
    public function deleted(Monitoring $monitoring): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => Monitoring::class,
            'model_id' => $monitoring->id,
            'action' => 'DELETE',
            'old_values' => $monitoring->getAttributes(),
            'new_values' => null,
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Handle the Monitoring "restored" event.
     */
    public function restored(Monitoring $monitoring): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => Monitoring::class,
            'model_id' => $monitoring->id,
            'action' => 'RESTORE',
            'old_values' => null,
            'new_values' => $monitoring->getAttributes(),
            'ip_address' => Request::ip(),
        ]);
    }
}
```

### Fix 4.4 - Register Observer in Model

```php
// app/Models/Monitoring.php
<?php

namespace App\Models;

use App\Observers\MonitoringObserver;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    // ... existing code ...

    protected static function booted(): void
    {
        static::observe(MonitoringObserver::class);
    }
}
```

---

## 5. FIX: Search Query Length Validation

### Fix 5.1 - Update extractMonitoringFilters()

```php
// app/Http/Controllers/MonitoringController.php

private function extractMonitoringFilters(Request $request): array
{
    // ... existing code ...

    // ✅ ADD length validation to search query
    $q = trim((string) $request->query('q', ''));
    
    if (strlen($q) > 500) {
        $q = substr($q, 0, 500);
    }

    return [
        'kategori' => $request->query('kategori'),
        'bulan' => $bulan,
        'tanggal' => $tanggal,
        'tahun' => $tahun,
        'tanggal_lengkap' => $tanggalLengkap,
        'search_in' => $searchIn,
        'q' => $q,  // Now length-validated
    ];
}
```

---

## 6. FIX: Make HTTPS Enforcement Global

### Fix 6.1 - Update bootstrap/app.php

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        // ... existing providers ...
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            // ✅ Add security headers middleware globally
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withRouting(
        web: base_path('routes/web.php'),
    )
    ->withExceptions(
        // ... existing exceptions ...
    )
    ->create();
```

---

## 7. FIX: Remove Error Suppression

### Fix 7.1 - Update ActivityLog pruneOldLogs()

```php
// app/Models/ActivityLog.php

public static function pruneOldLogs(int $retentionDays = 30): void
{
    try {
        $cutoff = now()->subDays($retentionDays);
        $deleted = static::where('created_at', '<', $cutoff)->delete();

        // ✅ Only optimize if deletion occurred
        if ($deleted > 0) {
            try {
                DB::statement('OPTIMIZE TABLE user_activity_logs');
            } catch (\Throwable $e) {
                Log::warning('Failed to optimize activity logs table', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    } catch (\Throwable $e) {
        // Log the error instead of silently failing
        Log::warning('Activity log pruning failed', [
            'error' => $e->getMessage(),
            'retention_days' => $retentionDays
        ]);
    }
}
```

---

## 8. DEPLOYMENT COMMANDS

Run these commands before deploying:

```bash
# 1. Generate encryption key
php artisan key:generate

# 2. Run database migrations
php artisan migrate --force

# 3. Create symbolic link (if using file storage)
php artisan storage:link

# 4. Cache configuration (production optimization)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Clear any existing cache
php artisan cache:clear

# 6. Optimize autoloader
composer install --optimize-autoloader --no-dev

# 7. Run security checks
composer require --dev laravel/pint
./vendor/bin/pint --test

# 8. Test the application
php artisan tinker
# > \App\Models\User::count()  # Should work
```

---

## 9. ENVIRONMENT FILES

Create `.env.production` (never commit this):

```env
APP_NAME="Balmon Lampung"
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=https://monitoring.balmon.id

DB_CONNECTION=mysql
DB_HOST=db.example.com
DB_PORT=3306
DB_DATABASE=balmon_monitoring
DB_USERNAME=balmon_user
DB_PASSWORD=your_strong_password_here

SESSION_ENCRYPT=true
SESSION_DRIVER=database

CACHE_STORE=redis
REDIS_HOST=redis.example.com
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

FORCE_HTTPS=true
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

LOG_CHANNEL=stack
LOG_LEVEL=warning
```

---

## SUMMARY OF FIXES

| Issue | Status | Difficulty |
|-------|--------|-----------|
| APP_DEBUG=true | ✅ Fixed | Easy |
| APP_KEY missing | ✅ Fixed | Easy |
| SESSION_ENCRYPT=false | ✅ Fixed | Easy |
| IDOR vulnerabilities | ✅ Fixed | Medium |
| Missing rate limiting | ✅ Fixed | Medium |
| Insecure ISP lookup | ✅ Fixed | Easy |
| No audit trail | ✅ Fixed | Medium |
| Search query validation | ✅ Fixed | Easy |
| HTTPS enforcement | ✅ Fixed | Easy |
| Error suppression | ✅ Fixed | Easy |

**Total Time to Implement**: ~4-6 hours  
**Critical Fixes Time**: ~30 minutes

