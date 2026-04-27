<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function showCompleteProfile()
    {
        return view('profile.complete');
    }

    public function storeCompleteProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^(?!Admin\s*\d*$).+/i'],
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:1024',
        ], [
            'name.regex' => 'Mohon gunakan nama asli Anda (bukan nama generik Admin).',
            'profile_photo.required' => 'Foto profil wajib diunggah.',
            'profile_photo.image' => 'File harus berupa gambar.',
            'profile_photo.max' => 'Ukuran foto maksimal 1MB agar sistem tetap ringan.',
        ]);

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update([
            'name' => $request->name,
            'profile_photo' => $path,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profil berhasil diperbarui. Selamat bekerja!');
    }
}
