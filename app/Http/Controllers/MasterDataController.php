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
        
        if ($request->has('category')) {
            $query->where('category', $request->query('category'));
        }
        
        $data = $query->orderBy('value')->get();
        return response()->json($data);
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

        return response()->json([
            'message' => 'Opsi master data berhasil diperbarui!',
            'data' => $masterData
        ]);
    }

    public function destroy($id)
    {
        $masterData = MasterData::findOrFail($id);
        $masterData->delete();

        return response()->json(['message' => 'Opsi berhasil dihapus.']);
    }

    public function toggleStatus($id)
    {
        $masterData = MasterData::findOrFail($id);
        $masterData->is_active = !$masterData->is_active;
        $masterData->save();

        return response()->json([
            'message' => $masterData->is_active ? 'Opsi diaktifkan.' : 'Opsi dinonaktifkan.',
            'is_active' => $masterData->is_active
        ]);
    }
}
