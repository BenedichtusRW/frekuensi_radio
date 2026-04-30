<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData;

class MasterDataController extends Controller
{
    // Middleware sudah ditangani di web.php (super_admin group)

    public function index(Request $request)
    {
        $query = MasterData::query();
        
        // Jangan tampilkan kategori konfigurasi sistem di tabel master data biasa
        $query->where('category', '!=', 'system_config');

        if ($request->has('category')) {
            $query->where('category', $request->query('category'));
        }
        
        $data = $query->orderBy('value')->get();
        return response()->json($data);
    }

    public function getConfig(Request $request)
    {
        $key = $request->query('key');
        if (!$key) return response()->json(['message' => 'Key required'], 400);

        $config = MasterData::where('category', 'system_config')
                            ->where('value', $key)
                            ->first();
        
        // Jika belum ada, buat defaultnya (is_active = false berarti Dropdown Mode)
        if (!$config) {
            $config = MasterData::create([
                'category' => 'system_config',
                'value' => $key,
                'is_active' => false 
            ]);
        }

        return response()->json($config);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'value' => 'required|string|max:255',
        ]);

        $normalizedValue = strtoupper(trim((string) $validated['value']));

        // Check if exists (case-insensitive)
        $exists = MasterData::where('category', $validated['category'])
                            ->where('value', $normalizedValue)
                            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Opsi ini sudah ada di dalam sistem.'], 422);
        }

        $masterData = MasterData::create([
            'category' => $validated['category'],
            'value' => $normalizedValue,
            'is_active' => true,
        ]);

        \Illuminate\Support\Facades\Cache::forget('laporan_dropdown_options');

        return response()->json([
            'message' => 'Opsi master data berhasil ditambahkan!',
            'data' => $masterData
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $masterData = MasterData::findOrFail($id);
        $normalizedValue = strtoupper(trim((string) $validated['value']));
        
        // Check if exists with another ID
        $exists = MasterData::where('category', $masterData->category)
                            ->where('value', $normalizedValue)
                            ->where('id', '!=', $id)
                            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Opsi dengan nama ini sudah ada.'], 422);
        }

        $masterData->update([
            'value' => $normalizedValue
        ]);

        \Illuminate\Support\Facades\Cache::forget('laporan_dropdown_options');

        return response()->json([
            'message' => 'Opsi master data berhasil diperbarui!',
            'data' => $masterData
        ]);
    }

    public function destroy($id)
    {
        $masterData = MasterData::findOrFail($id);
        $masterData->delete();

        \Illuminate\Support\Facades\Cache::forget('laporan_dropdown_options');

        return response()->json(['message' => 'Opsi berhasil dihapus.']);
    }

    public function toggleStatus($id)
    {
        $masterData = MasterData::findOrFail($id);
        $masterData->is_active = !$masterData->is_active;
        $masterData->save();

        \Illuminate\Support\Facades\Cache::forget('laporan_dropdown_options');

        return response()->json([
            'message' => $masterData->is_active ? 'Opsi diaktifkan.' : 'Opsi dinonaktifkan.',
            'is_active' => $masterData->is_active
        ]);
    }
}
