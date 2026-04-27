# Dokumen Arsitektur Sistem (Ringkas 1 Halaman)

## 1. Gambaran Umum Sistem
Website Pemilah Pita Frekuensi adalah aplikasi internal untuk pencatatan, pengelolaan, pemantauan, dan pelaporan data monitoring frekuensi radio di lingkungan Komdigi Balmon Lampung. Sistem dirancang untuk mendukung input data operasional harian, pemantauan statistik, serta ekspor laporan secara cepat dan aman.

## 2. Alur Proses Bisnis
1. Pengguna melakukan login menggunakan akun terdaftar.
2. Sistem menampilkan dashboard berisi ringkasan statistik dan visualisasi data monitoring.
3. Pengguna melakukan input data monitoring melalui formulir terstruktur.
4. Data tersimpan ke database dan langsung tersedia di daftar laporan.
5. Pengguna dapat melakukan pencarian, filter, edit, atau hapus data sesuai hak akses.
6. Pengguna mengekspor data laporan ke file XLSX (langsung unduh ke perangkat pengguna).
7. Sistem mencatat aktivitas penting pengguna (audit trail) dan melakukan cleanup data otomatis di background.

## 3. Arsitektur Teknologi
### Frontend
- Blade Template Engine
- Livewire (interaksi tanpa full page reload)
- Alpine.js (interaksi UI ringan)
- Chart.js (visualisasi dashboard)

### Backend
- Laravel 13
- PHP 8.3
- MySQL (XAMPP)
- Laravel Queue (database driver) untuk pekerjaan background

## 4. Keamanan Sistem
### Keamanan Login dan Sesi
- Autentikasi berbasis session Laravel.
- Rate limiting login untuk mengurangi brute-force.
- Session regenerate setelah login berhasil.
- Session invalidate + token regenerate saat logout.

### Keamanan Request dan Input
- Proteksi CSRF pada route form.
- Validasi server-side pada semua input utama.
- Sanitasi input string untuk mengurangi risiko XSS.
- Header no-cache pada halaman sensitif untuk mencegah akses ulang via tombol Back setelah logout.

### Audit dan Monitoring
- Aktivitas pengguna dicatat pada tabel user_activity_logs.
- Slow query logging aktif untuk deteksi query lambat.

## 5. Efisiensi Database dan Server
- Query listing dan filter monitorings dioptimasi dengan index.
- Dashboard menggunakan cache untuk mengurangi query berulang.
- Cleanup data otomatis berjalan melalui queue job:
  - Hapus session idle lama.
  - Hapus cache expired.
  - Hapus log aktivitas lama sesuai retention.
- Operasi OPTIMIZE TABLE dijalankan kondisional saat penghapusan data besar.
- File hasil ekspor tidak disimpan sebagai binary di database, melainkan langsung dikirim ke client.

## 6. Data Flow (Ringkas)
1. Input Form -> validasi -> simpan ke tabel monitorings.
2. Dashboard/Laporan -> baca monitorings (+ cache) -> tampilkan data.
3. Ekspor XLSX -> generate file in-memory -> kirim file ke browser pengguna.
4. Aktivitas pengguna -> simpan metadata ke user_activity_logs.
5. Queue worker -> jalankan job cleanup untuk menjaga performa jangka panjang.

## 7. Struktur Tabel MySQL (Aktif)
- users
- sessions
- password_reset_tokens
- cache
- cache_locks
- jobs
- failed_jobs
- job_batches
- migrations
- monitorings
- user_activity_logs
- monitoring_export_histories

## 8. Kesimpulan
Arsitektur sistem saat ini sudah sesuai untuk kebutuhan operasional internal: sederhana, cepat, aman, dan mudah dirawat. Fokus utama sistem berada pada input manual + pelaporan + audit aktivitas, dengan optimasi performa database yang cukup baik untuk pertumbuhan data bertahap.

## 9. Checklist Go-Live Security (Rencana Eksekusi)
Gunakan checklist ini saat transisi dari lokal ke online. Urutan disusun agar tim server bisa menjalankan deployment dengan aman dan terukur.

### A. Persiapan Environment (H-7 s.d H-3)
- [ ] Siapkan server production terpisah (jangan gunakan database lokal XAMPP produksi).
- [ ] Pastikan versi runtime sesuai target: PHP 8.3+, ekstensi wajib aktif, dan MySQL stabil.
- [ ] Siapkan akun deployment dengan hak terbatas (bukan administrator OS harian).
- [ ] Konfigurasi `.env` production khusus (jangan menyalin `.env` lokal apa adanya).

### B. Hardening Aplikasi Laravel (H-3)
- [ ] Set `APP_ENV=production`.
- [ ] Set `APP_DEBUG=false`.
- [ ] Set `APP_URL` ke domain production HTTPS.
- [ ] Jalankan `php artisan config:cache`.
- [ ] Jalankan `php artisan route:cache`.
- [ ] Jalankan `php artisan view:cache`.
- [ ] Pastikan `LOG_LEVEL` tidak terlalu verbose untuk production.

### C. HTTPS dan Session Security (H-3)
- [ ] Instal sertifikat SSL/TLS valid pada domain production.
- [ ] Paksa redirect HTTP -> HTTPS di web server/reverse proxy.
- [ ] Aktifkan secure cookie session untuk production.
- [ ] Pastikan atribut cookie aman: secure dan httpOnly.
- [ ] Verifikasi tidak ada mixed content (asset HTTP pada halaman HTTPS).

### D. Database Security (H-2)
- [ ] Batasi akses port MySQL hanya dari host aplikasi.
- [ ] Nonaktifkan akses publik langsung ke MySQL dari internet.
- [ ] Gunakan user database khusus aplikasi (least privilege).
- [ ] Ganti password DB yang kuat dan simpan di secret manager/`.env` server.
- [ ] Verifikasi backup credentials tidak hardcoded di repository.

### E. Backup dan Disaster Recovery (H-2)
- [ ] Buat backup harian otomatis database (dump terjadwal).
- [ ] Simpan backup di lokasi terpisah (offsite/NAS/object storage).
- [ ] Terapkan retention backup (misal 7/14/30 hari).
- [ ] Uji restore minimal 1 kali sebelum go-live.
- [ ] Dokumentasikan SOP restore (siapa, langkah, dan SLA pemulihan).

### F. Deployment dan Migrasi (H-1)
- [ ] Pull kode versi rilis final ke server production.
- [ ] Jalankan `composer install --no-dev --optimize-autoloader`.
- [ ] Jalankan `php artisan migrate --force`.
- [ ] Jalankan clear + cache ulang konfigurasi artisan.
- [ ] Verifikasi tabel utama dan jumlah data baseline pasca-migrasi.

### G. Queue dan Job Operasional (H-1)
- [ ] Jalankan worker queue sebagai service/background process.
- [ ] Pastikan auto-restart worker saat server reboot.
- [ ] Pantau tabel `jobs` dan `failed_jobs` setelah deploy.
- [ ] Verifikasi cleanup job berjalan sesuai retention policy.

### H. Uji Keamanan Fungsional (Hari-H)
- [ ] Uji login normal, login gagal berulang (rate limit), dan logout.
- [ ] Uji CSRF protection pada form input.
- [ ] Uji akses halaman sensitif setelah logout (harus tertolak/no-cache).
- [ ] Uji validasi input kosong/format salah/XSS payload sederhana.
- [ ] Uji export XLSX berjalan normal via HTTPS.

### I. Uji Performa dan Stabilitas (Hari-H)
- [ ] Uji dashboard, input, laporan, edit, delete, export pada jam sibuk simulasi.
- [ ] Pantau slow query log dan perbaiki query outlier.
- [ ] Pantau CPU/RAM/disk server setelah traffic awal.
- [ ] Verifikasi cache berjalan dan tidak menimbulkan stale data kritis.

### J. Kontrol Akses dan Audit (H+1)
- [ ] Definisikan role minimum: admin, operator, viewer (jika user bertambah).
- [ ] Terapkan policy/authorization di backend (bukan hanya sembunyikan tombol UI).
- [ ] Verifikasi log aktivitas user tercatat dengan baik.
- [ ] Tetapkan prosedur audit bulanan untuk aktivitas sensitif (edit/delete/export).

### K. Operasional Pasca Go-Live (Mingguan/Bulanan)
- [ ] Review error log dan failed jobs secara rutin.
- [ ] Patch dependency keamanan (Laravel/PHP/package) terjadwal.
- [ ] Uji restore backup berkala (drill recovery).
- [ ] Review ulang konfigurasi firewall dan akses DB.
- [ ] Update dokumentasi perubahan sistem setiap rilis.

### L. Definition of Done Go-Live
Checklist dianggap selesai jika:
- [ ] Semua checklist A-K telah centang.
- [ ] Tidak ada error kritis di 24 jam pertama.
- [ ] Backup harian terverifikasi berjalan.
- [ ] Tim operasional menerima SOP incident dan restore.

---

## 6. Single Page Application (SPA) Navigation - Konsistensi Implementasi

### Latar Belakang
Sejak Q1 2026, sistem menggunakan **Livewire Navigate** (`wire:navigate`) untuk menghilangkan full-page reload saat pengguna berpindah halaman. Ini meningkatkan pengalaman pengguna (user experience) dengan transisi halaman yang smooth dan mempertahankan state UI seperti scroll position atau form focus.

### Strategi Navigasi SPA
1. **Primary Navigation (Menu & Link Internal)**: Gunakan `wire:navigate` pada href untuk semua link navigasi ke halaman lain sistem (dashboard, laporan, settings, input).
2. **AJAX Pagination**: Gunakan manual fetch + `history.pushState` untuk paginasi laporan tanpa reload.
3. **Browser Back/Forward Button**: Gunakan `Livewire.navigate()` dengan fallback ke reload jika Livewire tidak tersedia.
4. **Direct Downloads**: Link download file (XLSX export) TIDAK menggunakan `wire:navigate` karena merupakan direct file download, bukan navigasi halaman.

### Implementasi di Kode
#### File yang sudah dikonfigurasi:
- **[resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php)**
  - Baris 438, 444, 450, 525, 530, 535: Menu sidebar & topbar menggunakan `wire:navigate`.
  - Baris 654-675: Handler `popstate` (back/forward button) menggunakan `Livewire.navigate()` dengan fallback reload.
  - Baris 587-649: AJAX pagination engine untuk tabel laporan (fetch + pushState).
  - Baris 695-700: Event listener `livewire:navigated` untuk re-inisialisasi komponen UI (Lucide icons, Bootstrap tooltips, pagination links).

- **[resources/views/input.blade.php](resources/views/input.blade.php)**
  - Baris 244: Link "Kembali" menggunakan `wire:navigate`.

- **[resources/views/laporan.blade.php](resources/views/laporan.blade.php)**
  - Baris 429: Link "Reset" (reset filter) menggunakan `wire:navigate`.

- **[resources/views/dashboard.blade.php](resources/views/dashboard.blade.php)**
  - Baris 134: Link "Lihat Semua" menggunakan `wire:navigate`.

#### File yang TIDAK menggunakan SPA (by design):
- **[resources/views/downloads.blade.php](resources/views/downloads.blade.php)**
  - Baris 43: Link download file XLSX langsung (tanpa `wire:navigate`).

### Workflow Navigasi SPA
```plaintext
User klik link internal (wire:navigate)
    ↓
Livewire intercept, fetch halaman baru via AJAX
    ↓
Swap <main> content tanpa reload
    ↓
Emit event 'livewire:navigated'
    ↓
Jalankan re-inisialisasi JS (Lucide, Bootstrap, pagination setup)
    ↓
Halaman siap interaksi normal
```

### Workflow AJAX Pagination Laporan
```plaintext
User masuk ke /laporan (tanpa parameter)
    ↓
Tabel menampilkan halaman 1 data (10 per halaman)
    ↓
User klik tombol halaman "2", "3", dst
    ↓
JavaScript listener (event delegation) intercept click
    ↓
Fetch AJAX ke backend dengan ?page=N parameter
    ↓
Backend return ONLY table HTML (partial view)
    ↓
JavaScript replace table DOM tanpa URL change
    ↓
history.pushState() DISABLED (URL tetap /laporan)
    ↓
Scroll position restore, JS components re-init
    ↓
Halaman tetap di /laporan, tapi data tabel berubah
```

### Sinkronisasi Lifecycle Events
Setiap kali halaman berpindah (via `wire:navigate` atau AJAX pagination), function berikut dipanggil otomatis:

1. **`window.reInitializePageComponents()`**
   - Render ulang Lucide icons.
   - Sanitasi pagination links (ubah href jadi AJAX-safe).
   - Setup double-submit protection pada form.

2. **`window.initPagination()`**
   - Attach document-level click handler untuk pagination links.
   - Guard: handler hanya attach SATU kali (flag `window._paginationClickBound`).
   - Setup `popstate` handler untuk back/forward button (flag `window._popstateBound`).

3. **`window.initClock()`**
   - Render jam & tanggal di topbar, update setiap 1 detik.
   - Hanya jalankan SATU kali (flag `clockEl.dataset.clockRunning`).

### Pencegahan Konflik Navigasi
✅ **Tidak ada duplikasi handler**:
- Manual fetch hanya untuk AJAX pagination (tabel laporan).
- Link navigasi umum 100% menggunakan `wire:navigate` (Livewire handle).
- `popstate` handler menggunakan Livewire.navigate, bukan reload langsung.

✅ **Guard flags**:
- `window._paginationClickBound`: Prevent re-attach pagination handler.
- `window._popstateBound`: Prevent re-attach popstate handler.
- `clockEl.dataset.clockRunning`: Prevent multiple clock intervals.

### Testing & Validation
```bash
# Skenario 1: Navigasi menu
- Klik menu "Daftar Laporan" → halaman berubah tanpa reload.
- Scroll position kembali ke atas.
- Pagination, filter, chart icons, tooltip siap.

# Skenario 2: Klik pagination
- Klik halaman 2 → tabel update via AJAX tanpa reload.
- URL berubah via pushState (browser URL bar update).
- Klik back/forward → navigate via Livewire (bukan reload).

# Skenario 3: Back/Forward button
- Di halaman Laporan, klik browser back.
- Navigate ke halaman sebelumnya via Livewire.navigate().
- Fallback: reload jika Livewire gagal.
```

### Maintenance Notes
- Setiap kali menambah link internal baru, pastikan pakai `wire:navigate`.
- Jika menambah AJAX endpoint baru, pastikan setup `history.pushState` + lifecycle re-init.
- Jika menambah JS component yang perlu render ulang, tambahkan init function ke `reInitializePageComponents()`.
- Hindari `window.location.href = ...` untuk navigasi halaman (gunakan `wire:navigate` atau `Livewire.navigate()`).
- **URL Clean Feature**: `history.pushState()` di-disable untuk pagination (line 628 di app.blade.php). Jika di masa depan dibutuhkan browser back button navigation history untuk pagination, uncomment line tersebut + refactor ke Livewire Component dengan `WithPagination` trait (Phase 2 improvement, post-launch).

---

## 7. AJAX Pagination Laporan - Implementasi Details

### Latar Belakang
Komponen Tabel Laporan menampilkan data dengan pagination. Saat pengguna mengklik tombol halaman (1, 2, 3, ...), sistem melakukan AJAX request tanpa reload halaman, sehingga pengalaman tetap smooth dan responsif.

### Arsitektur Pagination
```plaintext
User klik tombol halaman (page 2, 3, dll)
    ↓
JavaScript listener (app.blade.php line 595) intercept
    ↓
Fetch AJAX ke route monitoring.index?page=N dengan header XMLHttpRequest
    ↓
Laravel Controller index() detects ajax via request->ajax()
    ↓
Return ONLY table partial (partials/laporan-table.blade.php)
    ↓
JavaScript replace table DOM via replaceWith()
    ↓
history.pushState() update URL bar (no browser reload)
    ↓
Trigger lifecycle re-init (Lucide icons, Bootstrap tooltips, pagination setup)
    ↓
Halaman siap dengan data page baru
```

### File Konfigurasi
1. **Controller: [app/Http/Controllers/MonitoringController.php](app/Http/Controllers/MonitoringController.php#L310)**
   - Method `index(Request $request)`:
     - Line 310-325: Extract filter parameters dari request
     - Line 329: `$monitorings = $this->monitoringFilteredQuery($filters)->paginate(10)->withQueryString();`
     - Line 334-339: **AJAX Detection**: `if ($request->ajax()) return view('partials.laporan-table', ...);`
     - Line 341-350: Normal full page view return
   
   **Key Point**: `withQueryString()` menjaga filter params (tanggal, bulan, kategori, pencarian) saat paginasi, sehingga pengguna tidak "hilang" filternya saat ganti halaman.

2. **View Main: [resources/views/laporan.blade.php](resources/views/laporan.blade.php)**
   - **Line 6**: Single Root Element: `<div wire:key="laporan-daftar-{{ request()->query('page', 1) }}">`
   - **Penting**: Key ini **dinamis per halaman** (includes page number), memungkinkan Livewire Navigate track setiap state page secara unique.
   - Line 7-950: Seluruh konten halaman (filter, tabel, modals) dibungkus di dalam div ini.
   - Line 951: Closing tag `</div>` sebelum `@endsection`.
   
   **Benefit**: Livewire Navigate bisa mengganti content halaman secara **atomik** (seluruh content sekaligus), menjaga konsistensi DOM state.

3. **View Partial: [resources/views/partials/laporan-table.blade.php](resources/views/partials/laporan-table.blade.php)**
   - Line 1-4: Table card header dengan filter entries & action buttons.
   - Line 5-150+: Struktur tabel lengkap (thead, tbody dengan data).
   - **Line 335-340**: Pagination links footer:
     ```blade
     @if ($monitorings->hasPages())
         <div class="card-footer">
             {{ $monitorings->links() }}
         </div>
     @endif
     ```
   - **Bootstrap 5 Pagination**: Links render dengan Bootstrap 5 styling.

4. **HTML/CSS Protection**
   - ✅ Table structure (colgroup, thead, tbody) TIDAK DIUBAH
   - ✅ CSS classes (mosfet-table, badge-kategori, action-pill, dll) TIDAK DIUBAH
   - ✅ Hanya menambah wrapper element (tidak ada modifikasi tampilan)

### JavaScript Handler (app.blade.php)

**Document-level Click Listener** (Line 595-649):
```javascript
document.addEventListener('click', function (e) {
    const link = e.target.closest('.pagination a, .page-link');
    if (!link) return;
    
    const mainContainer = document.getElementById('tabel-frekuensi');
    if (!mainContainer) return;
    
    const url = link.dataset.ajaxUrl || link.href;
    // ... [URL validation & scroll lock]
    
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newTable = doc.getElementById('tabel-frekuensi');
            
            if (newTable && currentContainer) {
                currentContainer.replaceWith(newTable);  // ← Ganti div tabel lama dengan baru
                history.pushState(null, '', url);        // ← Update URL
                window.scrollTo(0, savedScrollPos);      // ← Restore scroll position
                window.reInitializePageComponents();     // ← Re-init JS components
            }
        })
        .catch(err => { 
            console.error(err); 
            window.location.href = url;  // Fallback ke full page load
        });
});
```

**Guard: Single Attach** (Line 645-657):
```javascript
if (!window._paginationClickBound) {
    window._paginationClickBound = true;
    // ... [attach listener]
}
```
Memastikan listener di-attach **hanya sekali**, mencegah duplikasi event.

### Flow Diagram
```
Halaman Laporan (page 1)
├─ Filter Form (stays intact)
├─ Tabel Frekuensi
│  ├─ Header (entries selector, buttons)
│  ├─ Tabel data (page 1 rows)
│  └─ Pagination links (1 [current] 2 3 ... Next)
└─ Modals (detail view, edit form)

User klik "2"
    ↓
fetch() ke /monitoring?page=2&filters=...
    ↓
Controller returns <div id="tabel-frekuensi">...</div>
    ↓
JavaScript replaceWith() tabel lama dengan tabel baru (page 2)
    ↓
history.pushState() update URL: /monitoring?page=2&filters=...
    ↓
Re-init Lucide icons, pagination setup, tooltips
    ↓
Page 2 siap, scroll ke posisi sebelumnya, modals tetap hidup
```

### Testing Checklist
- [ ] Buka halaman Daftar Laporan di `/laporan`, pastikan tabel tampil (page 1)
- [ ] **Verifikasi URL** di address bar = `/laporan` (tanpa `?page=1`)
- [ ] Klik pagination "2" → tabel berubah ke page 2 **tanpa URL change**
- [ ] URL tetap `/laporan` (kritical test!)
- [ ] Klik pagination beberapa kali (halaman 3, 4, dll) → URL selalu `/laporan`
- [ ] Filter tetap aktif saat paginasi (tanggal, kategori, pencarian tidak hilang)
- [ ] Lucide icons di tabel tetap render dengan benar setelah pagination
- [ ] Tombol "Edit" dan "Hapus" tetap berfungsi di halaman baru
- [ ] Scroll position kembali ke posisi sebelumnya saat page berubah
- [ ] **Reload browser** saat di page 2 → kembali ke page 1 `/laporan` (behavior expected)
- [ ] Tombol browser back dari `/laporan` → tidak trigger, tetap di halaman

### Fitur: Clean URL pada AJAX Pagination
**Implementasi khusus untuk Balmon**: 
- URL tetap `/laporan` meskipun data tabel berubah saat pagination
- `history.pushState()` di-disable (line 628 di app.blade.php di-comment)
- **Alasan**: Agar pengalaman pengguna tetap "dalam satu halaman" saat membaca data monitoring
- **Trade-off**: Back button tidak bisa recall page history (acceptable untuk use case ini)
- **Future**: Jika dibutuhkan back button state preservation, dapat di-enable kembali via uncomment `history.pushState()` + refactor ke Livewire Component

### Compatibility dengan Livewire Navigate
- **wire:key="laporan-daftar-{{ request()->query('page', 1) }}"**: 
  - Setiap halaman punya key unik
  - Memungkinkan Livewire Navigate untuk cache & restore page state dengan akurat
  - Jika user kembali ke page 2 via back button, Livewire tahu harus load page 2 (bukan page 1)

### Performance Notes
- **AJAX vs Full Reload**: Hemat 60-70% bandwidth (hanya render tabel, bukan layout/CSS/JS)
- **history.pushState**: Browser history tetap akurat, back/forward button berfungsi normal
- **Scroll Lock**: Viewport height tetap stable saat loading tabel baru (prevent layout shift)
- **Fallback**: Jika fetch gagal, fallback ke `window.location.href` (full reload)

### Troubleshooting
| Gejala | Penyebab | Solusi |
|--------|---------|--------|
| Tabel tidak update saat klik pagination | Listener tidak attach atau selector `#tabel-frekuensi` salah | Cek browser console untuk error, pastikan ID match |
| Lucide icons tidak render | Re-init function tidak dipanggil | Pastikan `window.reInitializePageComponents()` di line 635 |
| URL berubah ke ?page=2 saat pagination | `history.pushState()` tidak di-comment | Pastikan line 628 sudah di-comment: `// history.pushState(null, '', url);` |
| Filter hilang saat ganti halaman | Form filter tidak di-include dalam AJAX partial | Pastikan filter form di-retain (hanya tabel yang di-swap) |
| Modals di bawah tabel tidak bisa dibuka | Modal masih ada tapi div-nya tertembus ganti | By design: Modals di laporan.blade (index), bukan di partial (tidak ikut di-swap) |
| Page tidak scroll ke atas saat pagination | `window.scrollTo()` gagal atau viewport lock issue | Check if `mainContainer.style` reset properly di catch block |
