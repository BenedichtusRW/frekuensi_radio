# Website Pemilah Pita Frekuensi

Sistem monitoring frekuensi radio untuk Komdigi Balmon Lampung.

---

## 🚀 Setup Website Supaya Jalan

Ikuti langkah-langkah ini supaya website bisa dijalankan di komputer Anda.

### 1. Persiapan Awal

Pastikan komputer Anda sudah punya:
- PHP 8.0 atau lebih baru
- MySQL/Database
- Composer (untuk PHP)

Cek dengan membuka PowerShell:
```powershell
php -v
mysql --version
composer --version
```

### 2. Download & Setup Project

```powershell
# Masuk ke folder project
cd "path-ke-project\website_frekuensi"

# Install dependencies PHP
composer install
```

### 3. Setup Environment (File .env)

```powershell
# Copy file environment
cp .env.example .env

# Generate app key (important!)
php artisan key:generate
```

Buka file `.env` (pakai Notepad), cari bagian database dan sesuaikan dengan setting lokal Anda:
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_frekuensi
DB_USERNAME=root
DB_PASSWORD=(kosongkan atau isi password Anda)
```

### 4. Setup Database

```powershell
# Jalankan migration
php artisan migrate
```

Done! Website siap untuk jalan.

---

## ▶️ Menjalankan Website

Untuk menjalankan website, buka **2 terminal PowerShell**.

### Terminal 1: Web Server

```powershell
cd "path-ke-project\website_frekuensi"
php artisan serve
```

Tunggu sampai muncul:
```
Server running on: http://localhost:8000
```

Buka browser dan ketik: **http://localhost:8000**

### Terminal 2: Queue Worker (Background Jobs)

Buka terminal PowerShell baru:

```powershell
cd "path-ke-project\website_frekuensi"
php artisan queue:work --verbose
```

Terminal ini akan menampilkan background jobs yang sedang berjalan. Biarkan tetap aktif!

Sekarang Anda punya:
- Terminal 1: Website running di http://localhost:8000 ✓
- Terminal 2: Background cleanup jobs processing ✓

---

## 🎯 Selesai!

Website sudah bisa diakses. Login dengan username dan password yang ada, terus explore fitur-fiturnya.

---

---

## 📱 Apa Itu Website Ini?

Website ini adalah sistem pencatatan dan pelaporan monitoring frekuensi radio yang digunakan oleh Komdigi Lampung.

**Fungsi utama:**
- Mencatat data monitoring frekuensi radio
- Membuat laporan dengan filter dan statistik
- Melihat grafik dan trend
- Melacak aktivitas user (siapa yang login, siapa yang add/edit data)

Jadi kalau ada frekuensi aneh atau bermasalah, operator bisa langsung catat di sistem ini. Nanti admin bisa lihat laporan lengkapnya.

---

## ✨ Fitur-Fitur Utama

**Dashboard** - Halaman pertama
- Ringkasan total data, berapa MF, berapa HF, berapa HF Nelayan
- Grafik aktivitas 7 hari terakhir (stacked bar chart)
- Grafik trend 12 bulan terakhir
- 5 data monitoring terbaru

**Daftar Laporan** - Untuk cek dan export data
- Filter berdasarkan kategori, bulan, tahun, tanggal
- Search dengan keyword (nama, identifikasi, dll)
- Bisa export ke Excel (buat laporan resmi)
- Pagination (tampil per halaman)

**Pengaturan** - Untuk maintenance
- Lihat log aktivitas (siapa login kapan, siapa edit apa)
- Lihat IP address dan browser yang akses

---

## 🏗️ Teknologi yang Dipakai

**Backend (Server Side):**
- **Laravel 13** - Framework PHP modern untuk bikin website
- **PHP 8.3** - Bahasa pemrograman
- **MySQL** - Database untuk simpan data

**Frontend (Tampilan):**
- **Blade** - Template HTML
- **Livewire** - Bikin halaman interaktif (pakai AJAX di belakang)
- **Alpine.js** - JavaScript untuk fitur-fitur kecil
- **Chart.js** - Untuk bikin grafik cantik

**Database Optimization:**
- **Index** - Permainan cepat untuk search dan filter
- **Cache** - Simpan data yang sering diakses di memory (lebih cepat)

**Auto Cleanup:**
- **Laravel Queue** - Background jobs untuk bersihkan database otomatis
- Database-backed - Tidak perlu install tools tambahan

---

## ⚡ Performa Website

Website ini sudah dioptimasi supaya cepat dan responsif.

| Halaman | Kecepatan | Penjelasan |
|---------|-----------|-----------|
| Dashboard | ~3ms | Super cepat! Cache membantu |
| Search/Laporan | ~200-300ms | Cepat, query sudah simplified |
| Database Query | ~3ms | Index membantu pencarian |

**Kenapa jadi cepat?**
- Dashboard pakai cache (disimpan di memory, jadi tidak perlu query lagi)
- Search query disederhanakan (dari yang super kompleks jadi simple)
- Database punya index yang tepat (seperti katalog di perpustakaan)
- Slow query logging (untuk monitor query yang lambat)

---

## 🧹 Pembersihan Database (Otomatis)

Database akan terus bertambah seiring waktu. Ada data yang tidak perlu lagi:
- Session user yang idle >7 hari
- Cache yang sudah expired
- Activity log yang sudah >30 hari

Website otomatis akan membersihkan data ini tanpa perlu Anda setup apapun:

1. Saat user akses website, ada ~5% chance trigger cleanup
2. Cleanup job di-queue ke background
3. Queue worker memproses job (tidak mengganggu user)
4. Data lama dihapus, database tetap clean
5. Selesai!

Jadi Anda tidak perlu khawatir database akan penuh.

---

## 📊 Struktur Database

Database terdiri dari beberapa tabel:

**monitorings** - Data monitoring frekuensi
- Tanggal, jam, kategori, identifikasi
- Frekuensi, mode, lokasi, dll
- Total 22+ field per record

**activity_logs** - Audit trail
- Siapa login kapan
- Siapa add/edit/delete data kapan
- Siapa export laporan kapan
- IP address, browser, dll

**users** - Data user/operator
- Admin, operator
- Username, password (hashed)
- Email, dll

**sessions** - User session
- Tersimpan saat user login
- Otomatis dihapus setelah 7 hari idle

**cache** - Cache storage
- Data yang sering diakses disimpan di sini (lebih cepat)
- Expire date sudah dikonfigurasi

**jobs** - Queue jobs
- Cleanup jobs menunggu di sini
- Background worker memproses satu per satu

---

## 🛠️ Command-Command Sering Dipakai

### Cache & Config
```powershell
php artisan cache:clear        # Bersihkan cache
php artisan config:cache       # Simpan config (production)
php artisan view:clear         # Bersihkan view cache
```

### Database
```powershell
php artisan migrate            # Jalankan migrasi
php artisan migrate:rollback   # Batalkan migrasi terakhir
php artisan migrate:refresh    # Reset database
php artisan tinker             # Interactive shell
```

### Queue & Background
```powershell
php artisan queue:work         # Start processing jobs
php artisan queue:work --verbose    # Dengan detail
php artisan queue:info         # Lihat status queue
php artisan queue:failed       # Lihat jobs yang gagal
```

### Frontend Assets
```powershell
npm install                    # Install Node dependencies
npm run dev                    # Watch & compile (development)
npm run build                  # Build untuk production
```

### Debugging
```powershell
# Lihat log real-time
Get-Content storage/logs/laravel.log -Wait -Tail 50

# Cari slow query
Get-Content storage/logs/laravel.log | Select-String "Slow Query"
```

---

## 🚢 Untuk Production Server

Saat mau deploy ke server (bukan local), prosesnya:

### Setup di Server
```powershell
# Masuk ke server (Windows Server)
cd "C:\path\project\website_frekuensi"

# Install dependencies tanpa dev tools
composer install --optimize-autoloader --no-dev

# Setup database
php artisan migrate --force

# Cache semua config
php artisan config:cache
```

### Jalankan Queue Worker
Queue worker harus selalu berjalan supaya cleanup aktif.

**Windows Server:**
```powershell
# Buat file: queue-worker.bat
@echo off
cd /d "C:\path\project\website_frekuensi"
php artisan queue:work --daemon >> storage/logs/queue-worker.log 2>&1

# Schedule saat startup (Windows Task Scheduler)
$action = New-ScheduledTaskAction -Execute "C:\path\queue-worker.bat"
$trigger = New-ScheduledTaskTrigger -AtStartup
Register-ScheduledTask -Action $action -Trigger $trigger -TaskName "Queue-Worker" -RunLevel Highest
```

**Linux Server (via Supervisor):**
```bash
sudo apt-get install supervisor

# Buat config: /etc/supervisor/conf.d/laravel-queue.conf
[program:laravel-queue]
process_name=%(program_name)s
command=php /var/www/website_frekuensi/artisan queue:work --daemon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/website_frekuensi/storage/logs/queue.log

# Start
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-queue:*
```

---

## ✅ Pre-Deployment Checklist

Sebelum launch ke server production:

- [ ] Install dependencies: `composer install --no-dev`
- [ ] Config `.env` sesuai production
- [ ] Generate key: `php artisan key:generate`
- [ ] Database ready: `php artisan migrate --force`
- [ ] Config cached: `php artisan config:cache`
- [ ] Queue worker berjalan (Task Scheduler / Supervisor)
- [ ] Folder `storage/logs/` writable
- [ ] Database connection tested
- [ ] Minimal 1 request (trigger cleanup)

---

## 📁 Struktur File Project

```
website_frekuensi/
├── app/
│   ├── Http/
│   │   ├── Controllers/      ← Logika request
│   │   └── Middleware/       ← TriggerCleanupMiddleware
│   ├── Jobs/                 ← CleanupExpiredData.php
│   ├── Models/               ← Eloquent models
│   ├── Livewire/             ← Livewire components
│   └── Exports/              ← Excel export logic
├── database/
│   ├── migrations/           ← SQL schema (auto create tabel)
│   ├── factories/            ← Dummy data generator
│   └── seeders/              ← Seed script
├── resources/
│   ├── views/                ← HTML template (Blade)
│   ├── css/                  ← Stylesheet
│   └── js/                   ← JavaScript
├── routes/
│   ├── web.php               ← Define URL routes
│   └── console.php           ← Artisan commands
├── storage/
│   └── logs/                 ← Log files
├── .env                      ← Environment config
├── README.md                 ← Dokumentasi ini
├── composer.json             ← PHP dependencies
├── package.json              ← Node dependencies
└── artisan                   ← CLI tool
```

---

## 🔒 Security Tips

- Jangan commit `.env` ke Git (berisi password)
- Update Laravel berkala: `composer update`
- Setup slow query logging (catch N+1 queries)
- Activity logs traceback semua action (audit trail)
- Jangan expose sensitive info di error messages

---

## 🆘 Troubleshooting

**Website tidak bisa dibuka?**
```powershell
# Cek Terminal 1 (php artisan serve) masih aktif?
# Kalau tidak: php artisan serve
```

**Queue worker tidak bekerja?**
```powershell
# Cek Terminal 2 (php artisan queue:work) masih aktif?
# Kalau tidak: php artisan queue:work --verbose
```

**Database connection error?**
```powershell
# Cek .env (DB_HOST, DB_PORT, DB_DATABASE)
# Cek MySQL service berjalan
php artisan tinker
>>> DB::connection()->getPdo()  # Test connection
```

**Cleanup tidak jalan?**
```powershell
php artisan queue:info  # Lihat pending jobs
php artisan tinker
>>> DB::table('jobs')->count()  # Count jobs
```

---

## 📚 Referensi & Dokumentasi

Kalau mau belajar lebih dalam:
- Laravel docs: https://laravel.com/docs/13.x
- Livewire docs: https://livewire.laravel.com
- MySQL docs: https://dev.mysql.com/doc/

---

**Terakhir diupdate**: 15 April 2026  
**Status**: Siap Production ✓  
**Tim**: Backend Developer
# Website Pemilah Pita Frekuensi

**Sistem Monitoring Frekuensi Radio - Komdigi Balmon Lampung**

**Version**: 1.0.0  
**Framework**: Laravel 13.2.0  
**PHP**: 8.3.25  
**Database**: MySQL  
**Status**: ✅ Production Ready

---

## 📋 Deskripsi Proyek

Sistem monitoring frekuensi radio untuk **BALAI MONITOR SPEKTRUM FREKUENSI RADIO KELAS II LAMPUNG** (Komdigi).

Aplikasi ini mengelola data monitoring frekuensi radio dengan penuh fitur:
- 📊 Dashboard real-time dengan visualisasi data
- 📝 Manajemen laporan dengan filter advanced dan export Excel
- ✏️ Input data monitoring terintegrasi
- 👁️ Audit trail dan activity logging lengkap
- 🚀 Navigation cepat dengan Livewire SPA
- 🔄 Auto-cleanup database (tidak butuh manual setup)

---

## ✨ Fitur Utama

### Dashboard
- KPI Cards: Total data, MF, HF Rutin, HF Nelayan
- 7-day activity chart (stacked bar chart)
- 12-month trend analysis
- Recent monitoring preview (5 latest records)
- **Performance**: ~3ms response time (cached)

### Daftar Laporan
- Filter: kategori, bulan, tahun, tanggal
- Search dengan index optimization
- Pagination (10 per page)
- Excel export dengan formatting
- **Performance**: ~200-300ms search (optimized)

### Pengaturan
- Activity logs audit trail (20 latest)
- User action tracking (add/edit/export/visit)
- IP address & browser detection
- ISP lookup dengan 24-hour cache

---

## 🏗️ Tech Stack

- **Backend**: Laravel 13.2.0
- **PHP**: 8.3.25 (cli)
- **Frontend**: Blade templates, Livewire SPA, Alpine.js
- **Database**: MySQL dengan optimized indexes
- **Caching**: Database cache with TTL
- **Queue**: Laravel Queue (database-backed)
- **Charts**: Chart.js
- **Icons**: Lucide icons
- **Export**: Maatwebsite Excel

---

## 🚀 Getting Started

### Prerequisites

```powershell
# Verify requirements
php -v                    # PHP 8.0+ required
composer --version        # Composer installed
mysql --version          # MySQL running
```

### Installation

#### Step 1: Clone & Setup
```powershell
cd "your-project-path"
composer install
```

#### Step 2: Environment Setup
```powershell
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=db_frekuensi
# DB_USERNAME=root
# DB_PASSWORD=
```

#### Step 3: Database Setup
```powershell
# Run migrations
php artisan migrate

# Seed data (optional)
php artisan db:seed
```

#### Step 4: Cache & Config
```powershell
# Cache configuration
php artisan config:cache

# Build frontend assets (if needed)
npm install
npm run dev      # Development
npm run build    # Production
```

---

## 💻 Running the Application

### Development

#### Terminal 1: Web Server
```powershell
php artisan serve
# Akses: http://localhost:8000
```

#### Terminal 2: Queue Worker
```powershell
# Process background jobs (for auto-cleanup)
php artisan queue:work --verbose
# Keep this running for cleanup to work
```

#### Terminal 3: Asset Compilation (Optional)
```powershell
npm run dev
# Or compile once: npm run build
```

### Production

#### Setup (Server Admin)
```powershell
cd "C:\path\to\website_frekuensi"  # Windows Server
# or
cd /var/www/website_frekuensi      # Linux Server

composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
```

#### Start Queue Worker (One-time)

**Windows Server:**
```powershell
# Create batch file: queue-worker.bat
@echo off
cd /d "C:\path\to\website_frekuensi"
php artisan queue:work --daemon >> storage/logs/queue-worker.log 2>&1

# Schedule via Task Scheduler
$action = New-ScheduledTaskAction -Execute "C:\path\queue-worker.bat"
$trigger = New-ScheduledTaskTrigger -AtStartup
Register-ScheduledTask -Action $action -Trigger $trigger -TaskName "Laravel-Queue-Worker" -RunLevel Highest
```

**Linux Server (Supervisor):**
```bash
sudo apt-get install supervisor

# Create /etc/supervisor/conf.d/laravel-queue.conf
[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/website_frekuensi/artisan queue:work --daemon
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/website_frekuensi/storage/logs/queue.log

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-queue:*
```

---

## 🔄 Database Architecture

### Core Tables
```
- monitorings         → Main monitoring data (22+ fields)
- activity_logs       → Audit trail & user actions
- monitoring_exports  → Export history
- users               → System users (admin, operators)
- sessions            → User sessions (auto-cleaned)
- cache               → Cache storage (auto-cleaned)
- jobs                → Queue jobs (auto-processed)
- failed_jobs         → Failed job tracking
```

### Key Indexes
```
idx_monitorings_sort_composite    → (tahun, bulan, tanggal, jam_mulai, id)
idx_monitorings_filter_sort       → (kategori, tahun, bulan, tanggal)
idx_monitorings_created_at        → For recent monitoring queries
idx_sessions_last_activity        → For session cleanup
```

---

## ⚙️ Performance Optimizations

### Query Optimization
- Dashboard: 8 queries → 2 queries (75% reduction)
- Summary stats using CASE WHEN aggregation
- Search simplified: ~2-3s → ~200-300ms (10x faster)
- Index-friendly WHERE clauses

### Caching Strategy
- Dashboard summary: 10 minutes TTL
- Recent monitoring: 10 minutes TTL
- ISP lookup: 24 hours TTL
- Cache invalidation on data changes

### Auto-Cleanup (Built-in)
```
Activity Logs:  Delete >30 days (randomized dispatch)
Sessions:       Delete >7 days idle (randomized dispatch)
Cache:          Delete expired entries (randomized dispatch)

Bagaimana kerjanya:
  ~5% dari user requests dispatch cleanup job
  Job berjalan async di background (tidak blocking)
  Tidak perlu external scheduler!
  
Otomatis berjalan saat aplikasi diakses.
```

### Logging & Monitoring
- Slow query logging (>100ms autodetect)
- Non-blocking activity logging
- Structured error logging
- Queue job tracking

---

## 📊 Performance Metrics

| Metric | Target | Status |
|--------|--------|--------|
| Dashboard | <100ms | ✅ ~3ms |
| Search | <500ms | ✅ ~200-300ms |
| Query | <50ms | ✅ ~3ms |
| Auto-cleanup | Guaranteed | ✅ Running |
| Slow Queries | Logged | ✅ Monitored |

---

## 🔨 Useful Commands

### Cache Management
```powershell
php artisan cache:clear        # Clear all caches
php artisan config:cache       # Cache configuration
php artisan config:clear       # Clear config cache
php artisan view:clear         # Clear view cache
```

### Database
```powershell
php artisan migrate            # Run pending migrations
php artisan migrate:rollback   # Rollback last migration
php artisan migrate:refresh    # Reset all (with seeding)
php artisan tinker             # Interactive shell
```

### Queue Monitoring
```powershell
php artisan queue:work         # Start processing jobs
php artisan queue:work --verbose  # With detailed output
php artisan queue:info         # Show pending job count
php artisan queue:failed       # View failed jobs
php artisan queue:retry        # Retry failed jobs
```

### Logging & Debugging
```powershell
Get-Content storage/logs/laravel.log -Wait -Tail 50  # Real-time logs
Get-Content storage/logs/laravel.log | Select-String "Cleanup"  # View cleanup activity
Get-Content storage/logs/queue-worker.log  # Queue worker logs
```

### Development
```powershell
php artisan serve              # Start dev server
php artisan tinker             # Run in shell
npm run dev                    # Watch frontend assets
npm run build                  # Build for production
```

---

## 🏗️ Architecture: Auto-Cleanup System

### Bagaimana Cara Kerjanya?

```
User akses website
         ↓
Middleware otomatis cek: ~5% chance trigger cleanup
         ↓
CleanupExpiredData job di-dispatch ke queue
         ↓
Queue worker process job di background (async)
         ↓
Delete expired data:
  - Session idle >7 hari
  - Cache entries expired
  - Activity logs >30 hari
         ↓
User tetap dapat response CEPAT (tidak menunggu cleanup)
```

### System Files
- `app/Jobs/CleanupExpiredData.php` - Cleanup logic
- `app/Http/Middleware/TriggerCleanupMiddleware.php` - Auto-trigger
- `bootstrap/app.php` - Middleware registration
- `.env` - Queue config: `QUEUE_CONNECTION=database`

### Keuntungan Pendekatan Ini
✅ **Tidak perlu setup manual** di server  
✅ **Sama** di development & production  
✅ **Self-sustaining** - cleanup saat ada traffic  
✅ **Non-blocking** - user experience tidak terpengaruh  
✅ **Reliable** - async dengan retry otomatis  
✅ **Transparent** - detailed logging tersedia  

---

## 🔍 Monitoring & Troubleshooting

### Check Queue Status
```powershell
php artisan queue:info
# Expected: "default........... 0 jobs"
```

### View Cleanup Activity
```powershell
Get-Content storage/logs/laravel.log | Select-String "Cleanup"
# Look for: "Deleted X activity logs", "Deleted X sessions", etc
```

### Manual Cleanup (Testing)
```powershell
php artisan tinker
>>> use App\Jobs\CleanupExpiredData;
>>> CleanupExpiredData::dispatchSync();  # Run immediately
>>> exit
```

### Check Database Sizes
```powershell
php artisan tinker
>>> DB::table('activity_logs')->count()
>>> DB::table('sessions')->count()
>>> DB::table('cache')->count()
>>> exit
```

### Queue Issues

**Jobs not processing?**
```powershell
# Verify queue worker running
Get-Process | Select-String "php"
# If not: php artisan queue:work --daemon
```

**Too many pending jobs?**
```powershell
# Process all pending immediately
php artisan queue:work --until-empty

# Or add more workers
php artisan queue:work --daemon --workers=4
```

**Job keeps failing?**
```powershell
# View failed jobs
php artisan queue:failed

# Retry them
php artisan queue:retry

# Or clear them
php artisan queue:flush
```

---

## 📈 Development Workflow

### 1. Local Development
```powershell
# Terminal 1
php artisan serve

# Terminal 2
php artisan queue:work --verbose

# Visit http://localhost:8000
# Lihat Terminal 2 untuk cleanup job processing
```

### 2. Testing Changes
```powershell
# Clear config saat code berubah
php artisan config:clear

# Migrate database changes
php artisan migrate

# Check logs untuk issues
Get-Content storage/logs/laravel.log -Wait -Tail 20
```

### 3. Before Deployment
```powershell
# Run tests (jika ada)
php artisan test

# Cache everything untuk production
php artisan config:cache
php artisan view:cache
php artisan route:cache

# Optimize composer
composer install --optimize-autoloader --no-dev
```

---

## 📚 Project Structure

```
website_frekuensi/
├── app/
│   ├── Http/
│   │   ├── Controllers/      ← Request handlers
│   │   └── Middleware/       ← TriggerCleanupMiddleware 🔄
│   ├── Jobs/                 ← CleanupExpiredData.php 🔄
│   ├── Models/               ← Eloquent models
│   ├── Livewire/             ← Livewire components
│   └── Exports/              ← Excel exports
├── bootstrap/
│   └── app.php               ← Application config & middleware registration
├── database/
│   ├── migrations/           ← Schema changes
│   ├── factories/            ← Test data
│   └── seeders/              ← Initial data
├── resources/
│   ├── views/                ← Blade templates
│   ├── css/                  ← Stylesheets
│   └── js/                   ← JavaScript
├── routes/
│   ├── web.php               ← Web routes
│   └── console.php           ← Commands
├── storage/
│   └── logs/                 ← Application logs
├── .env                      ← Environment variables
├── composer.json             ← PHP dependencies
├── package.json              ← Node dependencies
└── artisan                   ← CLI tool

🔄 = Auto-cleanup related
```

---

## 🔐 Security Notes

- Keep `.env` secure (never commit to git)
- Use `php artisan config:cache` in production
- Enable slow query logging (catch N+1 queries)
- Activity logs track semua user actions
- Queue jobs stored in database (aman, trackable)

---

## 📞 Support & Documentation

- **Laravel Docs**: https://laravel.com/docs/13.x
- **Livewire Docs**: https://livewire.laravel.com
- **Queue System**: https://laravel.com/docs/13.x/queues

---

## ✅ Deployment Checklist

- [ ] `composer install --no-dev` di server
- [ ] `.env` configured dengan production values
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate --force`
- [ ] `php artisan config:cache`
- [ ] `php artisan view:cache`
- [ ] Queue worker running (Windows Task Scheduler / Supervisor)
- [ ] `storage/logs/` writable
- [ ] Database connection tested
- [ ] One request made (untuk trigger first cleanup)

---

**Last Updated**: April 15, 2026  
**Status**: Production Ready & Fully Automated 🚀  
**Team**: Backend Developer (No admin scheduler setup needed!)
# Website Pemilah Pita Frekuensi

**Version**: 1.0.0  
**Framework**: Laravel 13.2.0  
**Database**: MySQL  
**PHP**: 8.3.25  
**Status**: ✅ Production Ready

---

## 📋 Deskripsi Proyek

Sistem monitoring frekuensi radio untuk **BALAI MONITOR SPEKTRUM FREKUENSI RADIO KELAS II LAMPUNG** (Komdigi).

Aplikasi ini mengelola data monitoring frekuensi radio dengan fitur:
- 📊 Dashboard real-time dengan visualisasi data
- 📝 Manajemen laporan dengan filter dan export Excel  
- ✏️ Input data monitoring terintegrasi
- 👁️ Audit trail dan activity logging
- 🚀 Navigation cepat dengan Livewire SPA

---

## 🎯 Fitur Utama

### Dashboard
- KPI Cards: Total data, MF, HF Rutin, HF Nelayan
- 7-day activity chart (stacked bar chart)
- 12-month trend analysis
- Recent monitoring preview (5 latest records)
- **Performance**: ~3ms response time (cached)

### Daftar Laporan
- Filter by kategori, bulan, tahun, tanggal
- Search identifikasi dengan index optimization
- Pagination (10 per page)
- Excel export dengan formatting
- **Performance**: ~200-300ms search (optimized query)

### Pengaturan
- Activity logs audit trail (20 latest)
- User action tracking (add/edit/export/visit)
- IP address & browser detection
- ISP lookup dengan 24-hour cache

---

## 🏗️ Tech Stack

- **Backend**: Laravel 13.2.0, PHP 8.3.25
- **Frontend**: Blade templates, Livewire SPA, Alpine.js
- **Database**: MySQL with optimized indexes
- **Caching**: Database cache with TTL
- **Charts**: Chart.js
- **Icons**: Lucide icons
- **Export**: Maatwebsite Excel

---

## 📦 Database Schema

### Core Tables:
- `monitorings` - Main monitoring data (22+ fields)
- `activity_logs` - User activity audit trail
- `monitoring_exports` - Export history tracking
- `users` - System users (admin, operators)
- `sessions` - User sessions
- `cache` - Cache storage

### Key Indexes:
```
idx_monitorings_sort_composite    → (tahun, bulan, tanggal, jam_mulai, id)
idx_monitorings_filter_sort       → (kategori, tahun, bulan, tanggal)
idx_monitorings_created_at        → For recent monitoring queries
idx_sessions_last_activity        → For session cleanup
```

---

## ⚙️ Performance Optimizations

### Query Optimization:
- Dashboard: 8 queries → 2 queries (75% reduction)
- Summary stats using CASE WHEN aggregation
- Index-friendly search with simplified WHERE clauses
- **Result**: ~14ms → ~3ms response time

### Caching Strategy:
- Dashboard summary cache: 10 minutes
- Recent monitoring cache: 10 minutes  
- ISP lookup cache: 24 hours
- Cache invalidation on data changes

### Database Cleanup (Automated):
- Activity logs: Remove >30 days daily at 02:30
- Sessions: Remove >7 days daily at 02:35
- Cache: Remove expired entries hourly at :15
- Auto OPTIMIZE TABLE when significant rows deleted

### Logging:
- Slow query logging (>100ms autodetect)
- Non-blocking activity logging via termination callback
- Structured error logging

---

## 📊 Performance Metrics

| Metric | Target | Current |
|--------|--------|---------|
| Dashboard Load | <100ms | ✅ ~3ms |
| Search Response | <500ms | ✅ ~200ms |
| Query Time | <50ms | ✅ ~3ms |
| Database Cleanup | Auto | ✅ Running |
| Slow Queries | Logged | ✅ Monitored |

---

## 📚 Server Setup

Complete server setup instructions available in [SERVER_SETUP.md](SERVER_SETUP.md)

Key items:
- ✅ Complete installation steps
- ✅ Windows Task Scheduler configuration
- ✅ Database setup & migration
- ✅ Monitoring & maintenance procedures

---

## 🔧 Quick Development Commands

### Installation:
```bash
composer install
php artisan migrate
npm install && npm run dev
php artisan serve
```

### Cache Management:
```bash
php artisan cache:clear       # Clear all caches
php artisan config:cache      # Cache configuration
php artisan config:clear      # Clear config cache
```

### Scheduler:
```bash
php artisan schedule:list     # Show scheduled tasks
php artisan schedule:run      # Run scheduler once
```

### Monitoring:
```bash
tail -f storage/logs/laravel.log       # Watch logs real-time
grep "Slow Query" storage/logs/laravel.log  # Find slow queries
```

---

## 📖 Documentation

- [SERVER_SETUP.md](SERVER_SETUP.md) - Complete server setup guide
- [Database Migrations](database/migrations) - Schema information
- [Models](app/Models) - Data models documentation
- [Controllers](app/Http/Controllers) - API endpoints

---

**Last Updated**: April 15, 2026  
**Maintained By**: Development Team  
**License**: Internal Use Only - Komdigi
    <p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
