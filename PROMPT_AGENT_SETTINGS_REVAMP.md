PROMPT UNTUK AI AGENT - UI/UX REVAMP HALAMAN PENGATURAN
========================================================

Role: Senior Laravel + Alpine.js Developer

Tugas: Merombak halaman Pengaturan (resources/views/settings.blade.php) dari tampilan tabel langsung menjadi Card Grid + Drill-down Menu

---


## 📋 CURRENT STATE (Sekarang)

File: resources/views/settings.blade.php

Struktur sekarang:
```
┌─────────────────────────────────────────┐
│ Pengaturan (halaman)                    │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ Card Header: Log Aktivitas Sistem   │ │
│ │ (ikon + judul + deskripsi)          │ │
│ ├─────────────────────────────────────┤ │
│ │ Tabel Log Aktivitas (show always)   │ │
│ │ - Waktu                             │ │
│ │ - Aksi                              │ │
│ │ - Deskripsi                         │ │
│ │ - Platform                          │ │
│ │ - IP Address                        │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

**Masalah**: 
- Tabel selalu visible, tidak ada interaksi drill-down
- Belum scalable untuk tambah pengaturan lain nanti
- Tampilan kurang modern (bukan card grid)

---

## 🎯 DESIRED STATE (Target)

Tampilan yang diinginkan menggunakan 2 mode interaksi:

### Mode 1: MENU GRID (Default - Saat Halaman Dibuka)
```
┌──────────────────────────────────────────┐
│ Pengaturan                               │
├──────────────────────────────────────────┤
│                                          │
│  ┌────────────────────────────────────┐ │
│  │ [ikon]  Log Aktivitas Sistem       │ │
│  │         Lihat rekaman aktivitas    │ │
│  │         pengguna di sistem         │ │
│  │                                    │ │
│  │      [Klik untuk membuka] →        │ │
│  └────────────────────────────────────┘ │
│                                          │
│  (Space siap untuk card pengaturan      │
│   lain di masa depan)                   │
│                                          │
└──────────────────────────────────────────┘
```

### Mode 2: DETAIL VIEW (Saat Card Diklik)
```
┌──────────────────────────────────────────┐
│ Pengaturan                               │
├──────────────────────────────────────────┤
│ [← Kembali] atau [X Tutup]              │
├──────────────────────────────────────────┤
│ Tabel Log Aktivitas (full view)         │
│ - Headers: Waktu, Aksi, etc             │
│ - Data rows (loop @forelse)             │ 
│                                          │
│ (Tabel sekarang fully visible)          │
│                                          │
└──────────────────────────────────────────┘
```

**Transisi**: Instan (no page reload), hide card grid → show table

---

## 🛠️ TECHNICAL IMPLEMENTATION

**Tools yang digunakan**:
- Alpine.js (x-data, x-show) untuk state management
- Bootstrap/Tailwind classes (styling, sudah ada)
- Lucide Icons (sudah ada @lucide)

**State Management**:
```javascript
x-data="{
    showMenuGrid: true,      // Default: tampilkan card grid
    showLogActivityDetail: false  // Default: sembunyikan tabel
}"
```

**Logika Transisi**:
1. Click Card "Log Aktivitas" → `showMenuGrid = false; showLogActivityDetail = true`
2. Click Tombol "Kembali/X" → `showMenuGrid = true; showLogActivityDetail = false`

**CSS Transition** (optional, tapi recommended):
```html
<div x-show="showMenuGrid" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:leave="transition ease-in duration-150">
    <!-- Card Grid -->
</div>

<div x-show="showLogActivityDetail"
     x-transition:enter="transition ease-out duration-200"
     x-transition:leave="transition ease-in duration-150">
    <!-- Table View -->
</div>
```

---

## 📝 EXPECTED OUTPUT

### 1. Struktur HTML Overview
```html
<!-- Container -->
<div class="container-fluid p-0" x-data="{ 
    showMenuGrid: true, 
    showLogActivityDetail: false 
}">

    <!-- VIEW 1: MENU GRID (Card Grid) -->
    <div x-show="showMenuGrid">
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card-clickable" 
                     @click="showMenuGrid = false; showLogActivityDetail = true">
                    <!-- Card: Log Aktivitas -->
                </div>
            </div>
            <!-- More cards siap untuk ditambah nanti -->
        </div>
    </div>

    <!-- VIEW 2: DETAIL (Log Activity Table) -->
    <div x-show="showLogActivityDetail">
        <div class="mb-3">
            <button @click="showMenuGrid = true; showLogActivityDetail = false">
                ← Kembali
            </button>
            <!-- OR -->
            <button @click="showMenuGrid = true; showLogActivityDetail = false">
                × Tutup
            </button>
        </div>
        
        <!-- Original Table (from settings.blade.php) -->
        <div class="card">
            <table>...</table>
        </div>
    </div>
</div>
```

### 2. Card Design (Menu Grid)
```html
<div class="card border-0 shadow-sm cursor-pointer 
            hover:shadow-lg transition-shadow"
     style="background: #ffffff; border-radius: 1.25rem; 
            cursor: pointer; min-height: 200px;"
     @click="showMenuGrid = false; showLogActivityDetail = true">
    
    <div class="card-body p-4 d-flex flex-column">
        <!-- Icon Container -->
        <div class="mb-3">
            <div class="rounded-3 bg-blue-50 p-3 d-inline-flex" 
                 style="width: fit-content;">
                <i data-lucide="history" size="32" class="text-blue-600"></i>
            </div>
        </div>
        
        <!-- Title & Description -->
        <h5 class="fw-bold text-dark mb-2">Log Aktivitas Sistem</h5>
        <p class="text-slate-500 mb-0 small">
            Lihat rekaman lengkap aktivitas pengguna di dalam sistem
        </p>
        
        <!-- CTA (optional) -->
        <div class="mt-auto">
            <small class="text-blue-600 fw-bold">
                Buka →
            </small>
        </div>
    </div>
</div>
```

### 3. Back Button (Detail View Header)
```html
<div class="mb-3 d-flex gap-2">
    <button class="btn btn-sm btn-outline-secondary" 
            @click="showMenuGrid = true; showLogActivityDetail = false">
        <i data-lucide="arrow-left" size="16" class="me-1"></i>
        Kembali
    </button>
    
    <!-- OR Icon Button -->
    <button class="btn btn-sm btn-outline-danger"
            @click="showMenuGrid = true; showLogActivityDetail = false"
            title="Tutup">
        <i data-lucide="x" size="16"></i>
    </button>
</div>
```

---

## ✅ REQUIREMENTS CHECKLIST

- [ ] Setiap Card di Menu Grid harus clickable (cursor: pointer)
- [ ] Saat Card diklik → sembunyikan Grid, tampilkan Table (instan)
- [ ] Table header tetap ada (Waktu, Aksi, Deskripsi, Platform, IP)
- [ ] Table rows (@forelse loop) tetap sama (no data structure change)
- [ ] Tombol "Kembali"/"X" di atas table
- [ ] Klik tombol Back → sembunyikan table, tampilkan grid (instan)
- [ ] Gunakan Alpine.js x-show (bukan x-if) untuk smooth transition
- [ ] Styling harus sesuai dengan design system yang sudah ada (Bootstrap + custom colors)
- [ ] **PENTING**: Preserve semua data logic (activity logs @forelse loop)
- [ ] Icon "history" tetap pada card
- [ ] Responsive design (col-md-6 col-lg-4 untuk grid layout yang scalable)

---

## 🎨 STYLING NOTES

- **Card Grid Spacing**: Gunakan `row` dengan gap
- **Card Hover Effect**: Subtle shadow increase saat hover
- **Table Container**: Dalam card, sama seperti sekarang
- **Colors**: 
  - Primary accent: Blue (#3b82f6 atau Bootstrap primary)
  - Background: White (#ffffff)
  - Text: Dark slate (#1e293b)
  - Hover: Light shadow + subtle color change
  
- **Icons**: Lucide Icons (sudah di project)
- **Transitions**: 200ms ease-out untuk smooth feel

---

## 🔄 FUTURE-READY DESIGN

Nanti, untuk tambah card pengaturan lain (misal "Backup", "Security", "Roles"):

```html
<!-- Same structure, just duplicate the card div -->
<div class="col-md-6 col-lg-4 mb-3">
    <div class="card-clickable" @click="showMenuGrid = false; showSecurityDetail = true">
        <!-- Card: Security Settings -->
    </div>
</div>

<div class="col-md-6 col-lg-4 mb-3">
    <div class="card-clickable" @click="showMenuGrid = false; showBackupDetail = true">
        <!-- Card: Backup Management -->
    </div>
</div>
```

---

## 📦 DELIVERABLE

Silakan ubah file `resources/views/settings.blade.php` sesuai dengan spesifikasi di atas.

**Output yang diharapkan**:
1. File `settings.blade.php` yang sudah di-update
2. Verifikasi:
   - ✅ Saat halaman dibuka → tampil Card Grid
   - ✅ Klik Card → tampil Table, sembunyikan Grid
   - ✅ Klik "Kembali" → tampil Grid, sembunyikan Table
   - ✅ Table masih menampilkan semua activity logs dengan benar
   - ✅ Styling konsisten dengan design system project
   - ✅ Responsive (mobile, tablet, desktop)

---

## 💡 BONUS (Optional)

Jika sempat, tambahkan:
- [ ] Smooth fade-in/fade-out transition (x-transition)
- [ ] Hover effect pada card (scale slight, shadow increase)
- [ ] Loading skeleton saat table data loading (jika ada delay)
- [ ] Accessibility: aria-labels, keyboard navigation

---

**Good luck! Gas buat kodenya! 🚀**
