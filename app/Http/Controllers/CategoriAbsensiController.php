<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CategoriAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriAbsensiController extends Controller
{
    public function index()
    {
        // Mengambil semua kategori absen dari tabel CategoriAbsensi
        $categories = CategoriAbsensi::all();

        return response()->json(['categories' => $categories], 200);
    }

    public function Createcategori(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_kategori' => 'required|string',
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

        $categoriAbsen = CategoriAbsensi::create([
            'id_admin' => $user->id_user, // Menggunakan id_user dari pengguna yang terautentikasi
            'name_kategori' => $request->name_kategori,
        ]);

        return response()->json(['user' => $categoriAbsen], 200);
    }


    public function update(Request $request, $id_categoriabsen)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name_kategori' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Cari kategori absen berdasarkan ID
        $categoriAbsen = CategoriAbsensi::find($id_categoriabsen);

        if (!$categoriAbsen) {
            return response()->json(['error' => 'Kategori absen not found'], 404);
        }

        // Update kategori absen
        $categoriAbsen->name_kategori = $request->name_kategori;
        $categoriAbsen->save();

        return response()->json(['user' => $categoriAbsen], 200);
    }

    public function destroy($id_categoriabsen)
    {
        // Cari kategori absen berdasarkan ID
        $categoriAbsen = CategoriAbsensi::find($id_categoriabsen);

        if (!$categoriAbsen) {
            return response()->json(['error' => 'Kategori absen not found'], 404);
        }

        // Hapus kategori absen
        $categoriAbsen->delete();

        return response()->json(['message' => 'Kategori absen successfully deleted'], 200);
    }

}
