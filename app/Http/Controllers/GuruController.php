<?php

namespace App\Http\Controllers;

use App\Models\guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    public function index()
    {

    }


    public function registerGuru(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'nama' => 'required|string',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        'email' => 'required|string|email|max:255|unique:gurus',
        'password' => 'required|string|min:8',
        'tgl_lahir' => 'required|date',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Verifikasi apakah pengguna yang mengakses memiliki peran "admin_pondok"
    $user = Auth::user(); // Ambil pengguna yang sedang terautentikasi
    if ($user->role !== 'admin_pondok') {
        return response()->json(['error' => 'Only admin_pondok can register a guru'], 403);
    }

    // Membuat guru baru
    $guru = $user->gurus()->create([
        'nama' => $request->nama,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'tgl_lahir' => $request->tgl_lahir,
        'role' => 'ust_pondok', // Tambahkan ini untuk mengisi role
    ]);

    // Pindahkan gambar yang diunggah ke penyimpanan
    if ($request->hasFile('gambar')) {
        $gambar = $request->file('gambar');
        $gambarPath = 'images/poto-ustadz/' . $guru->id_ust . '.' . $gambar->getClientOriginalExtension();
        Storage::disk('public')->put($gambarPath, file_get_contents($gambar));
        $guru->gambar = $gambarPath;
        $guru->save();
    }

    // Mengembalikan respons dengan data guru
    return response()->json([
        'data' => [
            'id' => $guru->id_ust,
            'nama' => $guru->nama,
            'role' => $guru->role,
            'gambar' => $guru->gambar,
            'email' => $guru->email,
            'updated_at' => $guru->updated_at,
            'created_at' => $guru->created_at,
        ],
        'message' => 'Guru berhasil terdaftar'
    ]);
}




}
