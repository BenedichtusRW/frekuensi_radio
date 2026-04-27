@extends('layouts.app')

@section('title', 'Lengkapi Profil - Balmon Lampung')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="col-12 col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <div class="d-inline-flex p-3 bg-white shadow-sm rounded-circle mb-3 border border-light">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <h4 class="fw-800 text-slate-900 mb-2">Verifikasi Identitas</h4>
            <p class="text-slate-500 mx-auto px-2" style="font-size: 0.8rem; line-height: 1.5; max-width: 320px;">
                Silakan lengkapi identitas Anda untuk melanjutkan ke menu berikutnya.
            </p>
        </div>

        <div class="bg-white p-4 rounded-4 shadow-sm border-0">
            <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label text-slate-400 fw-bold small text-uppercase mb-2" style="font-size: 0.6rem; letter-spacing: 1.5px;">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control border-0 bg-light rounded-3 py-2 px-3 @error('name') is-invalid @enderror" 
                           style="font-size: 0.85rem; height: 42px;"
                           placeholder="Masukkan nama asli Anda" value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')
                        <div class="text-danger mt-1 fw-medium" style="font-size: 0.65rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-slate-400 fw-bold small text-uppercase mb-2" style="font-size: 0.6rem; letter-spacing: 1.5px;">Foto Profil</label>
                    <div id="dropZone" class="bg-light rounded-3 p-3 text-center transition-all border-0" style="cursor: pointer; min-height: 110px; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                        <div id="previewContainer" class="d-none">
                            <img id="imagePreview" src="#" alt="Preview" class="rounded-circle shadow-sm" style="width: 70px; height: 70px; object-fit: cover; border: 2px solid white;">
                        </div>
                        <div id="uploadHint">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a5b4fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <p class="text-slate-400 mb-0" style="font-size: 0.7rem;">Pilih foto</p>
                            <p class="text-indigo-300 mt-1" style="font-size: 0.6rem; font-weight: 600;">Maksimal Kapasitas: 1 MB</p>
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*" required>
                    </div>
                    @error('profile_photo')
                        <div class="text-danger mt-1 fw-medium" style="font-size: 0.65rem;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-indigo w-100 rounded-3 py-2 fw-bold shadow-sm mt-2" style="background: #6366f1; color: white; font-size: 0.85rem; border: none; height: 42px;">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="text-center mt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-slate-300 text-decoration-none small p-0" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    Keluaran Sistem
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .bg-light { background-color: #f1f5f9 !important; }
    .rounded-4 { border-radius: 1.5rem !important; }
    #dropZone:hover { background-color: #e2e8f0 !important; }
    .transition-all { transition: all 0.25s ease; }
    .fw-800 { font-weight: 800; }
    .btn-indigo:hover { background: #4f46e5 !important; transform: translateY(-1px); }
    .text-indigo-300 { color: #a5b4fc !important; }
</style>

<script>
    const fileInput = document.getElementById('profile_photo');
    const dropZone = document.getElementById('dropZone');

    dropZone.onclick = () => fileInput.click();

    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran foto maksimal adalah 1 MB.',
                    background: '#ffffff',
                    color: '#0f172a',
                    iconColor: '#f59e0b'
                });
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('previewContainer').classList.remove('d-none');
                document.getElementById('uploadHint').classList.add('d-none');
            }
            reader.readAsDataURL(file);
        }
    };
</script>
@endsection
