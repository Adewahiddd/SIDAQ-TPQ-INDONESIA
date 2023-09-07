<?php

namespace App\Http\Controllers;

use App\Models\CategoriAmanah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriAmanahController extends Controller
{
    public function index()
    {
        // Mengambil semua kategori amanah dari tabel CategoriAmanah
        $amanah = CategoriAmanah::all();

        return response()->json(['categories' => $amanah], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amanah' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        // Ambil data user yang terautentikasi (pengguna yang login)
        $user = auth()->user();

        if (!$user || $user->role !== 'admin_pondok') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $amanah = CategoriAmanah::create([
            'id_admin' => $user->id_user, // Menggunakan id_user dari pengguna yang terautentikasi
            'amanah' => $request->amanah,
        ]);

        $amanah->save();

        return response()->json(['user' => $amanah], 200);

    }

    public function update(Request $request, $id_amanah)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'amanah' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Cari amanah absen berdasarkan ID
        $amanah = CategoriAmanah::find($id_amanah);

        if (!$amanah) {
            return response()->json(['error' => 'Kategori absen not found'], 404);
        }

        // Update kategori absen
        $amanah->amanah = $request->amanah;
        $amanah->save();

        return response()->json(['user' => $amanah], 200);
    }

    public function destroy($id_amanah)
    {
        // Cari kategori absen berdasarkan ID
        $amanah = CategoriAmanah::find($id_amanah);

        if (!$amanah) {
            return response()->json(['error' => 'Kategori absen not found'], 404);
        }

        // Hapus kategori absen
        $amanah->delete();

        return response()->json(['message' => 'Kategori absen successfully deleted'], 200);
    }
}
