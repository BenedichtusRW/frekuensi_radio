# 🛰️ Portal Monitoring Balmon Lampung
**Sistem Administrasi Spektrum Frekuensi Radio Kelas II Lampung**

---

## 🔒 Keamanan Berlapis (Defense in Depth)
Sistem ini menggunakan pendekatan keamanan berlapis untuk menjamin integritas data:

### 🛡️ Pertahanan Depan (Anti-Inspect System)
*   **Zero-Tolerance Detection**: Mengunci dan melempar akses ilegal ke halaman 404 saat terdeteksi upaya *Inspect Element*.
*   **Input Lockdown**: Mematikan Klik Kanan dan Shortcut keyboard berbahaya (`F12`, `Ctrl+U`, dll).
*   **Debugger Trap**: Jebakan script untuk menghentikan upaya *debugging* ilegal.

### 🧱 Pertahanan Dalam (Backend Security)
*   **SQL Injection Protection**: Menggunakan *Prepared Statements* (Eloquent ORM) untuk mencegah manipulasi database.
*   **CSRF Shield**: Proteksi penuh terhadap serangan *Cross-Site Request Forgery* di setiap form.
*   **XSS Protection**: Pembersihan otomatis untuk mencegah injeksi script jahat.
*   **Bcrypt Encryption**: Enkripsi password tingkat tinggi yang tidak dapat didekripsi.
*   **Role-Gate Access**: Sistem penguncian akses menu berdasarkan jabatan (Super Admin vs Petugas).

---

## ✨ Fitur Utama
*   **Dashboard Real-time**: Monitoring grafik aktivitas mingguan dan tren bulanan.
*   **Manajemen Laporan**: Filter canggih berdasarkan kategori dan tanggal, serta Export ke Excel.
*   **Audit Trail**: Mencatat setiap aksi (Login, Edit, Export) lengkap dengan IP Address dan Browser.
*   **Keamanan 2FA**: Integrasi Google Authenticator untuk login yang lebih aman.
*   **UI Dinamis**: Pembaruan profil instan tanpa reload halaman.
*   **Auto-Cleanup Database**: Sistem pembersihan otomatis untuk data sampah (Session/Log lama) agar performa tetap kencang.

---

## ⚙️ Panduan Instalasi (Lokal)

### 1. Persiapan Awal
Pastikan komputer memiliki: **PHP 8.2+**, **Composer**, dan **MySQL**.

### 2. Setup Project
```powershell
# Install Dependensi
composer install

# Setup Environment
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Database & Email
Buka file `.env` dan sesuaikan bagian berikut:
```env
# Database
DB_DATABASE=db_frekuensi
DB_USERNAME=root
DB_PASSWORD=

# Email (SMTP Gmail)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### 4. Jalankan Migrasi & Link Storage
```powershell
# Migrasi Database & Isi Akun Default
php artisan migrate --seed

# Hubungkan Storage (Agar Foto Profil Muncul)
php artisan storage:link
```

**Detail Login Default (Setelah Seeding):**
*   **Super Admin**: `adminsuper@balmon.go.id` | Pass: `BalmonLampung25`
*   **Petugas Admin**: `adminaja@balmon.go.id` | Pass: `BalmonLampung23`

---

## ▶️ Menjalankan Website

Buka **2 Terminal** untuk performa maksimal:

**Terminal 1 (Web Server):**
```powershell
php artisan serve
```
Akses di: `http://127.0.0.1:8000`

**Terminal 2 (Queue Worker - WAJIB):**
```powershell
php artisan queue:work
```
*Gunakan terminal ini agar fitur pembersihan database otomatis (Auto-Cleanup) berjalan.*

---

**Monitoring Dev Team**  
*Sistem ini dikembangkan secara profesional untuk Balmon Lampung.*
