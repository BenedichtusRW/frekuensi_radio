<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

use App\Traits\LogsActivity;

class UserController extends Controller
{
    use LogsActivity;

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $role = $request->role;
        $name = $request->name;

        // Auto-generate name for super_admin
        if ($role === 'super_admin') {
            $saCount = User::where('role', 'super_admin')->count();
            $name = 'Super Admin ' . ($saCount + 1);
        }

        $request->validate([
            'name' => $role === 'super_admin' ? 'nullable' : 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['super_admin', 'admin'])],
        ]);

        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $this->logActivity($request, 'add_user', 'Menambah anggota tim baru: ' . $user->name . ' (' . $user->role . ')');

        return response()->json([
            'success' => true,
            'message' => 'Anggota tim berhasil ditambahkan.'
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Prevent changing other Super Admin's sensitive data
        if ($user->role === 'super_admin' && $user->id !== auth()->id()) {
            // Only allow self-update or block everything for others
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat memodifikasi akun sesama Super Admin.'
            ], 403);
        }

        $request->validate([
            'name' => $request->role === 'super_admin' ? 'nullable' : 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['super_admin', 'admin'])],
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $this->logActivity($request, 'edit_user', 'Memperbarui data anggota tim: ' . $user->name);

        return response()->json([
            'success' => true,
            'message' => 'Data anggota tim berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent deleting other Super Admin
        if ($user->role === 'super_admin' && $user->id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus sesama akun Super Admin.'
            ], 403);
        }

        // JIKA HAPUS AKUN SENDIRI: WAJIB VERIFIKASI PASSWORD
        if ($user->id === auth()->id()) {
            $request->validate([
                'password' => ['required', 'string'],
            ]);

            if (!\Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password yang Anda masukkan salah.'
                ], 422);
            }
        }

        $userName = $user->name;
        $user->delete();

        $this->logActivity($request, 'delete_user', 'Menghapus akun anggota tim: ' . $userName);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dihapus.'
        ]);
    }

    /**
     * Toggle the active status of a user.
     */
    public function toggleStatus(Request $request, User $user)
    {
        // Proteksi: Jangan bisa menonaktifkan diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
            ], 403);
        }

        // Proteksi: Jangan bisa menonaktifkan sesama Super Admin
        if ($user->role === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Status akun Super Admin tidak dapat diubah.'
            ], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'Mengaktifkan' : 'Menonaktifkan';
        $this->logActivity($request, 'toggle_user_status', $statusText . ' akun: ' . $user->name);

        return response()->json([
            'success' => true,
            'message' => 'Status akun ' . $user->name . ' berhasil diubah menjadi ' . ($user->is_active ? 'Aktif' : 'Nonaktif') . '.'
        ]);
    }

    /**
     * Send password reset link to the user.
     */
    public function sendResetLink(Request $request, User $user)
    {
        // Only Super Admin can trigger this
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        // We use Laravel's built-in password broker to send the link
        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->logActivity($request, 'send_reset_link', 'Mengirim tautan reset password ke email: ' . $user->email);
            
            return response()->json([
                'success' => true,
                'message' => 'Tautan reset password telah dikirim ke email ' . $user->email
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim email reset. Pastikan konfigurasi SMTP benar.'
        ], 500);
    }
}
