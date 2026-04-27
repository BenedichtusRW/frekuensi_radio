# 🔒 Security Audit Report: Laravel Monitoring Website
**Date**: April 20, 2026  
**Project**: Balmon Lampung Monitoring Frekuensi  
**Framework**: Laravel 11

---

## Executive Summary

The Laravel monitoring website has **implemented several robust security practices**, particularly around input validation, CSRF protection, and security headers. However, **three critical vulnerabilities** and several medium-level issues require immediate attention before production deployment.

| Severity | Count | Status |
|----------|-------|--------|
| 🔴 Critical | 3 | Action Required |
| 🟠 High | 4 | Action Required |
| 🟡 Medium | 5 | Review Recommended |
| 🟢 Low | 2 | Consider Fixing |
| ✅ Strengths | 8+ | Well Implemented |

---

## 1. CRITICAL VULNERABILITIES

### ⚠️ 1.1 Debug Mode Enabled in .env.example
**Severity**: CRITICAL | **File**: [.env.example](.env.example#L5)  
**Issue**: `APP_DEBUG=true` will expose complete stack traces, database queries, and sensitive configuration to attackers.

```env
APP_DEBUG=true  # ❌ CRITICAL: Disables error hiding
```

**Impact**:
- Full stack traces with file paths and line numbers visible
- SQL queries exposed in debug output
- Environment variables potentially revealed in error pages
- Attackers can enumerate application structure

**Fix**:
```env
APP_DEBUG=false  # ✅ Production safe
```

**Additional Mitigation**:
```php
// config/app.php - already has good fallback
'debug' => (bool) env('APP_DEBUG', false),  // Defaults to false
```

---

### ⚠️ 1.2 Missing APP_KEY in .env.example
**Severity**: CRITICAL | **File**: [.env.example](.env.example#L3)  
**Issue**: `APP_KEY=` is empty, which is required for encryption and session handling.

```env
APP_KEY=  # ❌ CRITICAL: No encryption key
```

**Impact**:
- Session encryption disabled - compromises session security
- Cookie encryption uses empty key
- Application may use insecure fallback encryption
- User sessions vulnerable to tampering

**Fix**:
Generate a 32-character base64 key:
```bash
php artisan key:generate
```

Then add to .env:
```env
APP_KEY=base64:your_generated_key_here
```

---

### ⚠️ 1.3 Session Encryption Disabled
**Severity**: CRITICAL | **File**: [.env.example](.env.example#L32)  
**Issue**: Sessions not encrypted; combined with missing APP_KEY, creates serious session security risk.

```env
SESSION_ENCRYPT=false  # ❌ CRITICAL: Sessions stored in plaintext
DB_CONNECTION=sqlite   # Stores sessions unencrypted
```

**Impact**:
- Session data stored in plaintext in database
- Attacker with database access can hijack sessions
- User activity logs exposed
- Authentication tokens visible

**Fix**:
```env
SESSION_ENCRYPT=true  # ✅ Encrypt all session data
SESSION_DRIVER=database  # Keep database, but encrypt contents
```

**Recommended for Higher Security**:
```env
SESSION_DRIVER=array  # Or use Redis for production
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_strong_password
REDIS_PORT=6379
```

---

## 2. HIGH SEVERITY VULNERABILITIES

### 🔴 2.1 Missing Insecure Direct Object Reference (IDOR) Checks
**Severity**: HIGH | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php)  
**Issue**: Routes `edit/{id}`, `update/{id}`, `destroy/{id}` don't verify user permissions before accessing.

```php
// ❌ VULNERABLE: No authorization check
public function edit(Request $request, int $id)
{
    return redirect()->route('monitoring.index', ['edit_id' => $id]);
}

public function update(Request $request, int $id)
{
    $monitoring = Monitoring::findOrFail($id);  // Only checks existence, not ownership
    $monitoring->update($validated);  // Any authenticated user can modify ANY record
}

public function destroy(int $id)
{
    $monitoring = Monitoring::findOrFail($id);  // No permission check
    $monitoring->delete();  // Can delete any record
}
```

**Attack Scenario**:
1. Attacker logs in as regular user
2. Guesses monitoring record ID (1, 2, 3, etc.)
3. Directly updates/deletes records they don't own
4. No audit trail prevents this

**Fix**:
```php
// ✅ SECURE: With authorization
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MonitoringController extends Controller
{
    use AuthorizesRequests;
    
    public function update(Request $request, int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        
        // Option 1: Policy-based authorization
        $this->authorize('update', $monitoring);
        
        // Or Option 2: Explicit permission check
        if ($monitoring->created_by_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        // ... rest of method
    }
    
    public function destroy(int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        $this->authorize('delete', $monitoring);
        $monitoring->delete();
        return redirect()->back()->with('success', 'Record deleted.');
    }
}
```

**Create Authorization Policy**:
```bash
php artisan make:policy MonitoringPolicy --model=Monitoring
```

```php
// app/Policies/MonitoringPolicy.php
namespace App\Policies;

use App\Models\Monitoring;
use App\Models\User;

class MonitoringPolicy
{
    public function update(User $user, Monitoring $monitoring): bool
    {
        return $user->id === $monitoring->created_by_id || $user->is_admin;
    }

    public function delete(User $user, Monitoring $monitoring): bool
    {
        return $user->id === $monitoring->created_by_id || $user->is_admin;
    }
}
```

---

### 🔴 2.2 No Rate Limiting on Sensitive Operations
**Severity**: HIGH | **Files**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php) (create, update, destroy routes)  
**Issue**: While login has rate limiting (good!), data modification endpoints allow unlimited requests.

```php
// ✅ Good: Login has rate limiting
if (RateLimiter::tooManyAttempts($throttleKey, 5)) { ... }

// ❌ Bad: No rate limiting on data operations
public function store(Request $request) { ... }  // Can spam create unlimited records
public function update(Request $request, int $id) { ... }  // Can spam updates
public function destroy(int $id) { ... }  // Can spam deletes
```

**Attack Scenario**:
- Attacker with valid credentials rapidly creates/updates thousands of records
- Database bloats, disk space fills
- Application performance degrades
- Audit logs filled with spam

**Fix**:
```php
// Add rate limiting to sensitive operations
use Illuminate\Support\Facades\RateLimiter;

class MonitoringController extends Controller
{
    public function store(Request $request)
    {
        $throttleKey = 'monitoring.create.' . Auth::id();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 30, 60)) {
            return back()->withErrors([
                'error' => 'Too many records created. Try again in ' . 
                    RateLimiter::availableIn($throttleKey) . ' seconds.'
            ]);
        }
        
        RateLimiter::hit($throttleKey, 60);
        
        // ... rest of method
    }
    
    public function destroy(int $id)
    {
        $throttleKey = 'monitoring.delete.' . Auth::id();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 10, 60)) {
            abort(429, 'Too many delete attempts. Try again later.');
        }
        
        RateLimiter::hit($throttleKey, 60);
        $monitoring = Monitoring::findOrFail($id);
        $monitoring->delete();
        
        return redirect()->route('monitoring.index')
            ->with('success', 'Record deleted.');
    }
}
```

**Or use Middleware** (recommended):
```php
// routes/web.php
Route::middleware(['auth', 'throttle:30,60'])->group(function () {
    Route::post('/input', [MonitoringController::class, 'store']);
    Route::put('/input/{id}', [MonitoringController::class, 'update']);
    Route::delete('/input/{id}', [MonitoringController::class, 'destroy']);
});
```

---

### 🔴 2.3 Hardcoded ISP Lookup Over HTTP in logActivity()
**Severity**: HIGH | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L283)  
**Issue**: Activity logging makes external HTTP request to ip-api.com without HTTPS.

```php
// ❌ Insecure: Plain HTTP external API call
$res = @file_get_contents("http://ip-api.com/json/{$ip}?fields=isp,org", false, $ctx);
```

**Risks**:
- Man-in-the-middle (MITM) attack can intercept ISP data
- Relies on untrusted external service
- IP address exposed in logs if service is compromised
- Rate limiting against ip-api.com causes service degradation
- `@` suppresses actual errors, hiding problems

**Fix**:
```php
// ✅ Secure: Use HTTPS and add error handling
private function getISPInfo(string $ip): string
{
    if (in_array($ip, ['127.0.0.1', '::1'], true)) {
        return 'Loopback/Localhost';
    }
    
    return Cache::remember('isp_' . md5($ip), now()->addHours(24), function () use ($ip) {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'ignore_errors' => false,
                ],
                'ssl' => [
                    'verify_peer' => true,  // Verify SSL certificates
                    'verify_peer_name' => true,
                ]
            ]);
            
            // Use HTTPS instead of HTTP
            $response = file_get_contents(
                "https://ip-api.com/json/{$ip}?fields=isp,org",
                false,
                $context
            );
            
            if ($response === false) {
                return 'Unknown (Service Unavailable)';
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'Unknown (Invalid Response)';
            }
            
            return $data['isp'] ?? $data['org'] ?? 'Unknown';
        } catch (Throwable $e) {
            // Log actual error for debugging, but don't expose to users
            Log::warning('ISP Lookup Failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return 'Unknown';
        }
    });
}
```

**Better Alternative** - Use caching only:
```php
// Simplest approach: don't rely on external service
private function logActivity(...) {
    // ... omit ISP lookup entirely or use local geo-IP database
    ActivityLog::create([
        // ... other fields
        'isp' => 'Unknown',  // Don't call external service
    ]);
}
```

---

### 🔴 2.4 No Row-Level Audit Trail for Sensitive Changes
**Severity**: HIGH | **Files**: All modification endpoints  
**Issue**: When records are updated/deleted, no before/after audit trail is maintained.

```php
// ❌ Current: Update happens without tracking what changed
$monitoring->update($validated);  // No history of what was modified

// ❌ Delete leaves no trace
$monitoring->delete();  // It's gone forever - no audit trail
```

**Impact**:
- Compliance issues (regulatory body needs audit trail)
- Cannot detect unauthorized changes
- Cannot restore accidentally deleted records
- Investigation difficult if data is corrupted

**Fix**:
```bash
php artisan make:model AuditLog --migration
```

```php
// app/Models/AuditLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'model_type', 'model_id', 'action', 'old_values', 'new_values', 'ip_address'];
    protected $casts = ['old_values' => 'json', 'new_values' => 'json'];
}
```

```php
// app/Models/Monitoring.php - Add observer
namespace App\Models;

use App\Observers\MonitoringObserver;

class Monitoring extends Model
{
    // ...
    
    protected static function booted(): void
    {
        static::observe(MonitoringObserver::class);
    }
}
```

```php
// app/Observers/MonitoringObserver.php
namespace App\Observers;

use App\Models\Monitoring;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class MonitoringObserver
{
    public function updating(Monitoring $monitoring): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => Monitoring::class,
            'model_id' => $monitoring->id,
            'action' => 'UPDATE',
            'old_values' => $monitoring->getOriginal(),
            'new_values' => $monitoring->getAttributes(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function deleting(Monitoring $monitoring): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => Monitoring::class,
            'model_id' => $monitoring->id,
            'action' => 'DELETE',
            'old_values' => $monitoring->getAttributes(),
            'new_values' => null,
            'ip_address' => request()->ip(),
        ]);
    }
}
```

---

## 3. MEDIUM SEVERITY ISSUES

### 🟡 3.1 XSS in data via selectRaw (Though Unlikely)
**Severity**: MEDIUM | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L32-L44)  
**Issue**: The hardcoded category names in `selectRaw` are safe, but approach is brittle.

```php
// ⚠️ Current approach (actually safe, but not ideal):
$monthlySummary = Monitoring::query()
    ->selectRaw("SUM(CASE WHEN kategori = 'MF' THEN 1 ELSE 0 END) as mf")
    ->selectRaw("SUM(CASE WHEN kategori = 'HF Rutin' THEN 1 ELSE 0 END) as rutin")
    ->first();
```

**Why It's Medium (Not Critical)**:
- Values are hardcoded, not from user input
- If user modifies database, then XSS is their own fault
- Query Builder would parameterize if using `where()` conditions

**Fix (More Robust)**:
```php
$monthlySummary = Monitoring::query()
    ->selectRaw('COUNT(*) as total')
    ->selectRaw("SUM(CASE WHEN kategori = ? THEN 1 ELSE 0 END) as mf", ['MF'])
    ->selectRaw("SUM(CASE WHEN kategori = ? THEN 1 ELSE 0 END) as rutin", ['HF Rutin'])
    ->first();
```

Or better (using collections):
```php
$monitorings = Monitoring::query()->get();
$summary = [
    'total' => $monitorings->count(),
    'mf' => $monitorings->where('kategori', 'MF')->count(),
    'rutin' => $monitorings->where('kategori', 'HF Rutin')->count(),
    'nelayan' => $monitorings->where('kategori', 'HF Nelayan')->count(),
];
```

---

### 🟡 3.2 Query Injection via Filter Search
**Severity**: MEDIUM | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L680-L710)  
**Issue**: `search_in` parameter is whitelist-validated (good), but `q` search term should have length limits.

```php
// Good: Whitelist validation
$allowedSearchIn = ['identifikasi', 'frekuensi_khz', 'stasiun_monitor', 'administrasi_termonitor'];
if (!in_array($searchIn, $allowedSearchIn, true)) {
    $searchIn = 'identifikasi';
}

// ⚠️ Potential issue: No length limit on search query
'q' => trim((string) $request->query('q', '')),  // Could be gigabytes long
```

**Attack**:
- Send extremely long search string
- Causes database to process massive string
- Potential for ReDoS (Regular Expression Denial of Service)
- Memory exhaustion

**Fix**:
```php
private function extractMonitoringFilters(Request $request): array
{
    // ... existing code ...
    
    // ✅ Add length validation
    $q = trim((string) $request->query('q', ''));
    
    if (strlen($q) > 500) {
        $q = substr($q, 0, 500);  // Truncate to reasonable length
    }
    
    return [
        // ... other filters ...
        'q' => $q,
    ];
}
```

---

### 🟡 3.3 Cache Poisoning Vulnerability in ISP Lookup
**Severity**: MEDIUM | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L283-L300)  
**Issue**: External ISP data cached indefinitely per IP; poisoned cache hard to clear.

```php
// ⚠️ Vulnerable to cache poisoning
$isp = Cache::remember('isp_' . md5($ip), now()->addHours(24), function () use ($ip) {
    $res = @file_get_contents("http://ip-api.com/json/{$ip}?...");  // External service
    // If external service compromised or MITM, bad data gets cached for 24 hours
});
```

**Risk**:
- External service compromised
- ISP data in cache is corrupted/malicious
- Remains in cache for 24 hours
- Difficult to invalidate

**Fix**:
```php
// Option 1: Shorter cache duration
$isp = Cache::remember('isp_' . md5($ip), now()->addMinutes(60), function () use ($ip) { ... });

// Option 2: Don't cache if service is unreliable
$isp = $this->lookupISP($ip);  // Don't cache external API response

// Option 3: Use local GeoIP database instead
use GeoIp2\Database\Reader;

private function getISPInfo(string $ip): string
{
    try {
        $reader = new Reader(storage_path('geoip/GeoLite2-ASN.mmdb'));
        $result = $reader->asn($ip);
        return $result->autonomousSystemOrganization;
    } catch (\Exception) {
        return 'Unknown';
    }
}
```

---

### 🟡 3.4 Verbose Error Responses in Validation
**Severity**: MEDIUM | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L378-L384)  
**Issue**: Validation errors may expose internal structure to attackers.

```php
// ⚠️ Reveals table names and field names
return back()->withErrors(['frekuensi_khz' => 'Data serupa sudah diinput sebelumnya...'])
    ->withInput();
```

**Not Critical Because**:
- Field names are implied by form labels already
- Field names visible in HTML form anyway
- But should minimize information leakage

**Fix**:
```php
// Existing implementation is actually acceptable
// But can be more defensive:

private function store(Request $request)
{
    try {
        $validated = $request->validate($this->monitoringValidationRules());
        // ... validation passed
    } catch (\Illuminate\Validation\ValidationException $e) {
        // ✅ Custom error handling if needed
        return back()
            ->withErrors($e->errors())
            ->withInput()
            ->with('validation_error_count', count($e->errors()));
    }
    
    // ... rest of method
}

// In .env for production:
APP_ENV=production  # Hides validation details from JSON responses
APP_DEBUG=false     # Hides stack traces
```

---

### 🟡 3.5 No HTTPS Enforcement in Production  
**Severity**: MEDIUM | **File**: [SecurityHeadersMiddleware.php](app/Http/Middleware/SecurityHeadersMiddleware.php#L14-L16)  
**Issue**: While HTTPS redirect exists for production, it's not enforced widely enough.

```php
// ✅ Good: HTTPS redirect for production
if (app()->environment('production') && !$request->secure()) {
    return redirect()->secure($request->getRequestUri(), 301);
}

// ⚠️ But not for all routes (only in SecurityHeadersMiddleware)
// Need to ensure ALL routes use HTTPS
```

**Fix** (ensure middleware is applied globally):
```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            // ... other middleware
        ]);
    })
    ->withRouting(
        web: base_path('routes/web.php'),
    );
```

**Or add to config/app.php**:
```php
// config/app.php - ensure force HTTPS
'force_https' => env('FORCE_HTTPS', (bool) env('APP_ENV') === 'production'),
```

**Even Better - Use .htaccess for Apache**:
```apache
# public/.htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## 4. LOW SEVERITY ISSUES

### 🟢 4.1 Credentials in IP-API Service
**Severity**: LOW | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L283)  
**Issue**: The ip-api.com service doesn't use authentication key (free tier).

```php
// Free tier (no key):
"http://ip-api.com/json/{$ip}?fields=isp,org"  // ⚠️ Rate limited, unreliable

// ✅ Better: Use API key for reliability (paid plan)
"http://ip-api.com/json/{$ip}?fields=isp,org&key=" . env('IP_API_KEY')
```

**Fix**:
```env
IP_API_KEY=your_api_key_here
```

```php
$apiKey = env('IP_API_KEY', '');
$apiUrl = $apiKey 
    ? "https://ip-api.com/json/{$ip}?fields=isp,org&key={$apiKey}"
    : "https://ip-api.com/json/{$ip}?fields=isp,org";

$response = file_get_contents($apiUrl, false, $context);
```

---

### 🟢 4.2 Silent Error Suppression in ActivityLog
**Severity**: LOW | **File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L188-L200)  
**Issue**: Using `@` operator to suppress errors makes debugging difficult.

```php
// ⚠️ Hides actual errors
$res = @file_get_contents("http://ip-api.com/json/{$ip}?...");

// Also in ActivityLog::pruneOldLogs()
try { ... } catch (\Throwable) {
    // Fail silently — cleanup tidak boleh merusak fitur utama
}
```

**Fix**:
```php
// Replace @ with proper error handling
try {
    $response = file_get_contents(
        "https://ip-api.com/json/{$ip}?fields=isp,org",
        false,
        $context
    );
    
    if ($response === false) {
        Log::warning('ISP lookup failed', ['ip' => $ip]);
        return 'Unknown';
    }
    
    return json_decode($response, true)['isp'] ?? 'Unknown';
} catch (\Throwable $e) {
    // Log actual error for debugging
    Log::warning('ISP API Error', ['ip' => $ip, 'error' => $e->getMessage()]);
    return 'Unknown';
}
```

---

## 5. WHAT'S DONE WELL (SECURITY STRENGTHS) ✅

### ✅ 5.1 Strong Input Validation
**File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L625-L657)

```php
private function monitoringValidationRules(): array
{
    return [
        'kategori' => ['required', 'string', 'in:MF,HF Rutin,HF Nelayan'],  // ✅ Enum validation
        'frekuensi_khz' => ['nullable', 'numeric'],  // ✅ Type validation
        'tanggal' => ['nullable', 'integer', 'between:1,31'],  // ✅ Range validation
        'tahun' => ['nullable', 'integer', 'between:2000,2100'],  // ✅ Year validation
        // ... more strict rules
    ];
}
```

**Why Good**:
- Uses Laravel's built-in validation engine
- Defines exact allowed values (enum)
- Type-safe (numeric, integer, string)
- Range validation prevents unreasonable values

---

### ✅ 5.2 Aggressive XSS Protection
**File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L431)

```php
// ✅ Multiple layers of XSS protection
foreach ($validated as $key => $value) {
    if (is_string($value)) {
        $validated[$key] = htmlspecialchars(
            strip_tags(trim($value)),  // Remove HTML tags
            ENT_QUOTES,  // Convert both " and ' to HTML entities
            'UTF-8'      // Specify encoding
        );
    }
}
```

**Why Good**:
- `strip_tags()` removes any HTML markup
- `htmlspecialchars()` escapes remaining characters
- `ENT_QUOTES` handles both single and double quotes
- Applied to all user input before storage

---

### ✅ 5.3 CSRF Token Protection
**Files**: [auth/login.blade.php](resources/views/auth/login.blade.php#L238), [all forms]

```blade
<!-- ✅ CSRF token in all forms -->
<form method="POST" action="/login">
    @csrf
    <!-- form fields -->
</form>
```

**Why Good**:
- Laravel middleware automatically validates CSRF tokens
- Tokens bound to user session
- Prevents cross-site request forgery

---

### ✅ 5.4 Comprehensive Security Headers
**File**: [SecurityHeadersMiddleware.php](app/Http/Middleware/SecurityHeadersMiddleware.php)

```php
// ✅ Multiple security headers implemented:
$response->headers->set('X-Frame-Options', 'SAMEORIGIN');  // Prevents clickjacking
$response->headers->set('X-Content-Type-Options', 'nosniff');  // Prevents MIME sniffing
$response->headers->set('X-XSS-Protection', '1; mode=block');  // XSS filter
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');  // Privacy
$response->headers->set('Strict-Transport-Security', 'max-age=31536000; ...');  // Force HTTPS

// ✅ Content Security Policy to prevent XSS
$response->headers->set('Content-Security-Policy', $csp);

// ✅ Hide server information
$response->headers->remove('Server');
$response->headers->remove('X-Powered-By');
```

**Why Good**:
- Defense-in-depth approach
- Modern browser protection mechanisms enabled
- Server fingerprinting prevented

---

### ✅ 5.5 Rate Limiting on Authentication
**File**: [AuthController.php](app/Http/Controllers/AuthController.php#L38-L51)

```php
// ✅ Login attempt rate limiting (5 attempts per minute)
$throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
    $seconds = RateLimiter::availableIn($throttleKey);
    return back()->withErrors(['email' => 'Terlalu banyak percobaan login...']);
}

RateLimiter::hit($throttleKey, 60);  // Lock for 60 seconds
```

**Why Good**:
- Prevents brute-force attacks on login
- Uses both email AND IP address for uniqueness
- Clear feedback to user about cooldown

---

### ✅ 5.6 Session Security Configuration
**File**: [.env.example, config/auth.php]

```env
SESSION_DRIVER=database  # Store in database, not files
SESSION_LIFETIME=120     # 2-hour timeout, reasonable for web app
BCRYPT_ROUNDS=12         # Good cost factor for password hashing
```

**Why Good**:
- Database sessions can be queried/audited
- 2-hour lifetime prevents indefinite session hijacking
- Bcrypt rounds=12 takes ~250ms to hash (good security/performance balance)

---

### ✅ 5.7 Password Hashing with Bcrypt
**File**: [User.php](app/Models/User.php#L18)

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // ✅ Automatic bcrypt hashing
    ];
}
```

**Why Good**:
- Passwords are automatically hashed with Bcrypt when set
- Bcrypt is slow by design (resistant to brute-force)
- No plaintext passwords ever stored

---

### ✅ 5.8 Cache Busting for Security-Critical Data
**File**: [MonitoringController.php](app/Http/Controllers/MonitoringController.php#L470-L476)

```php
// ✅ Clear dashboard cache when data changes
private function clearDashboardCache(): void
{
    Cache::forget('dashboard_summary_stats');
    Cache::forget('dashboard_barchart_stats_v2');
    Cache::forget('dashboard_monthly_stats_v2');
    Cache::forget('dashboard_recent_monitoring');
}
```

**Why Good**:
- Prevents stale data from being served to users
- Critical after mutations (create, update, delete)
- Ensures consistency

---

### ✅ 5.9 No Back-Button History Access
**File**: [PreventBackHistory.php](app/Http/Middleware/PreventBackHistory.php)

```php
// ✅ Prevents back-button cache access after logout
$response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
$response->headers->set('Pragma', 'no-cache');
$response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
```

**Why Good**:
- User logs out, can't go back in browser to see authenticated pages
- Prevents accidental exposure of data in shared computers

---

## 6. RECOMMENDATIONS SUMMARY

### 🔴 CRITICAL (Fix Before Production)
| # | Issue | Priority | Effort |  |
|---|-------|----------|--------|--|
| 1 | APP_DEBUG=true enabled | Hours | 15 min |
| 2 | Missing APP_KEY in .env | Hours | 5 min |
| 3 | SESSION_ENCRYPT=false | Hours | 15 min |

### 🔴 HIGH (Fix Within Days)
| # | Issue | Priority | Effort |
|---|-------|----------|--------|
| 1 | Missing IDOR checks (authorization) | Days | 2 hours |
| 2 | No rate limiting on mutations | Days | 1 hour |
| 3 | Insecure ISP lookup (HTTP) | Days | 30 min |
| 4 | No audit trail for changes | Days | 4 hours |

### 🟡 MEDIUM (Fix Within Weeks)
| # | Issue | Priority | Effort |
|---|-------|----------|--------|
| 1 | selectRaw approach (low risk) | Weeks | 1 hour |
| 2 | Search query length validation | Weeks | 15 min |
| 3 | Cache poisoning risk | Weeks | 30 min |
| 4 | Verbose error responses | Weeks | 30 min |
| 5 | HTTPS enforcement gaps | Weeks | 30 min |

### 🟢 LOW (Nice to Have)
| # | Issue | Priority | Effort |
|---|-------|----------|--------|
| 1 | IP-API authentication | Later | 15 min |
| 2 | Error suppression cleanup | Later | 1 hour |

---

## 7. DEPLOYMENT CHECKLIST

Before deploying to production:

### Environment Setup
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate and set `APP_KEY` via `php artisan key:generate`
- [ ] Set `SESSION_ENCRYPT=true` in `.env`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure `FORCE_HTTPS=true` for production
- [ ] Set strong `DB_PASSWORD` for database
- [ ] Use `.env.production` (never commit `.env`)

### Code Changes
- [ ] Add authorization checks (IDOR) to all PUT/DELETE routes
- [ ] Add rate limiting to create/update/delete endpoints
- [ ] Fix ISP lookup to use HTTPS and API key
- [ ] Implement audit trail for data modifications
- [ ] Add search query length validation
- [ ] Replace error suppression (`@`) with proper logging

### Testing
- [ ] Test login with rate limiting
- [ ] Verify CSRF tokens work
- [ ] Check HTTPS redirect in production
- [ ] Verify audit logs are recorded
- [ ] Test authorization (try accessing others' records)

### Infrastructure
- [ ] Enable HTTPS/TLS certificate (Let's Encrypt)
- [ ] Configure database backups
- [ ] Set up monitoring/alerting
- [ ] Configure firewall rules
- [ ] Set up log aggregation

---

## 8. TOOLS FOR ONGOING SECURITY

### PHPStan (Static Analysis)
```bash
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse app
```

### Laravel Pint (Code Formatting + Security)
```bash
composer require --dev laravel/pint
./vendor/bin/pint
```

### Psalm (Type Checking)
```bash
composer require --dev vimeo/psalm
./vendor/bin/psalm
```

### Snyk (Dependency Vulnerability Scanning)
```bash
npm install -g snyk
snyk test
```

---

## CONCLUSION

The Laravel monitoring website has implemented **many security best practices**. However, **critical issues with debug mode, encryption, and session security** must be fixed before any production deployment. After addressing the critical and high-severity issues, the application will be significantly more secure.

**Overall Security Rating**: 🟠 **6.5/10** (before fixes) → 🟢 **8.5/10** (after fixes)

---

**Report Generated**: April 20, 2026  
**Auditor**: Security Analysis Team  
**Confidence**: High

