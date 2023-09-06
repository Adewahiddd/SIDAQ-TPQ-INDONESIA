<?php

namespace App\Http\Controllers;

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

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $categoriAbsen = CategoriAbsensi::create([
            'kategori' => $request->kategori,
        ]);

        $categoriAbsen->save();

        return response()->json(['user' => $categoriAbsen], 200);

    }

    public function update(Request $request, $id_categoriabsen)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kategori' => 'required',
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
        $categoriAbsen->kategori = $request->kategori;
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
