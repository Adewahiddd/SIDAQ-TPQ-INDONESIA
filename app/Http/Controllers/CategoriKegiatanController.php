<?php

namespace App\Http\Controllers;

use App\Models\CategoriKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriKegiatanController extends Controller
{
    public function index()
    {
        // Mengambil semua kategori kegiatan dari tabel CategoriKegiatan
        $kegiatan = CategoriKegiatan::all();

        return response()->json(['categories' => $kegiatan], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_kegiatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = auth()->user();

        if (!$user || $user->role !== 'admin_pondok') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $kegiatan = CategoriKegiatan::create([
            'id_admin' => $user->id_user,
            'name_kegiatan' => $request->name_kegiatan,
        ]);

        $kegiatan->save();

        return response()->json(['user' => $kegiatan], 200);

    }

    public function update(Request $request, $id_kegiatan)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name_kegiatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Cari kegiatan$kegiatan Kegiatan berdasarkan ID
        $kegiatan = CategoriKegiatan::find($id_kegiatan);

        if (!$kegiatan) {
            return response()->json(['error' => 'Kategori Kegiatan not found'], 404);
        }

        // Update kategori Kegiatan
        $kegiatan->name_kegiatan = $request->name_kegiatan;
        $kegiatan->save();

        return response()->json(['user' => $kegiatan], 200);
    }

    public function destroy($id_kegiatan)
    {
        // Cari kategori Kegiatan berdasarkan ID
        $kegiatan = CategoriKegiatan::find($id_kegiatan);

        if (!$kegiatan) {
            return response()->json(['error' => 'Kategori Kegiatan not found'], 404);
        }

        // Hapus kategori Kegiatan
        $kegiatan->delete();

        return response()->json(['message' => 'Kategori Kegiatan successfully deleted'], 200);
    }
}
