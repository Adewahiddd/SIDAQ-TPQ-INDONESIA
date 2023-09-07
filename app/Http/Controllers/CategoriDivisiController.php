<?php

namespace App\Http\Controllers;

use App\Models\CategoriDivisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriDivisiController extends Controller
{
    public function index()
    {
        // Mengambil semua kategori Divisi dari tabel CategoriDivisi
        $divisi = CategoriDivisi::all();

        return response()->json(['divisi' => $divisi], 200);
    }

    public function create(Request $request)
    {
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'divisi' => 'required|string',
        ]);

        // Jika validasi gagal, kembalikan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Dapatkan pengguna yang terautentikasi
        $user = auth()->user();

        // Periksa izin pengguna
        if (!$user || $user->role !== 'admin_pondok') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Buat kategori divisi
        $categoriDivisi = CategoriDivisi::create([
            'id_admin' => $user->id_user,
            'divisi' => $request->input('divisi'), // Perbaikan di sini
        ]);

        // Simpan data kategori divisi
        $categoriDivisi->save();

        // Kembalikan respons sukses
        return response()->json(['categori_divisi' => $categoriDivisi], 200);
    }

    public function update(Request $request, $id_divisi)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'divisi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Cari kategori Divisi berdasarkan ID
        $categoriDivisi = CategoriDivisi::find($id_divisi);

        if (!$categoriDivisi) {
            return response()->json(['error' => 'Kategori Divisi not found'], 404);
        }

        // Update kategori Divisi
        $categoriDivisi->divisi = $request->divisi;
        $categoriDivisi->save();

        return response()->json(['user' => $categoriDivisi], 200);
    }

    public function destroy($id_divisi)
    {
        // Cari kategori Divisi berdasarkan ID
        $categoriDivisi = CategoriDivisi::find($id_divisi);

        if (!$categoriDivisi) {
            return response()->json(['error' => 'Kategori Divisi not found'], 404);
        }

        // Hapus kategori Divisi
        $categoriDivisi->delete();

        return response()->json(['message' => 'Kategori Divisi successfully deleted'], 200);
    }

}
