<?php

namespace App\Http\Controllers;

use App\Models\guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SantriController extends Controller
{
    public function AddSantri(Request $request, $idguru)
    {
        $guru = Auth::user(); // Mengambil guru yang terautentikasi
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'email' => 'required|string|email|max:255|unique:santris',
            'password' => 'required|string|min:8',
            'tgl_lahir' => 'required|date',
            'ustadz' => 'required|string',
            'amanah' => 'required|string',
            'kedisiplinan' => 'required|integer|min:0|max:100|multiple_of:10', // Menambahkan aturan multiple_of:10,
            'hafalans' => 'required|string',
            'mutqin' => 'required|string',
            'fundraising' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'tanggal' => 'required|date',
            'alpha' => 'required|integer',
            'sakit' => 'required|integer',
            'izin' => 'required|integer',
            'tahajjud' => 'required|string',
            'odoj' => 'required|string',
            'stw' => 'required|string',
            'majelis' => 'required|string',
            'khidmat' => 'required|integer|multiple_of:10',
            'leadership' => 'required|integer|multiple_of:10',
            'entrepreneur' => 'required|integer|multiple_of:10',
            'speaking' => 'required|integer|multiple_of:10',
            'operation' => 'required|integer|multiple_of:10',
            'mengajar' => 'required|integer|multiple_of:10',
            'administration' => 'required|integer|multiple_of:10',
            'hafalan' => 'required|integer|multiple_of:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Verifikasi apakah pengguna yang mengakses memiliki peran "admin_pondok"
        // $guru = Auth::user(); // Ambil pengguna yang sedang terautentikasi
        if ($guru->role !== 'admin_pondok') {
            return response()->json(['error' => 'Only admin_pondok can add a santri'], 403);
        }

        $guru = guru::find($idguru);

        if (!$guru) {
            return response()->json(['error' => 'Guru not found'], 404);
        }

      // Membuat santri baru
    $santri = $guru->santris()->create([
        'nama' => $request->nama,
        'gambar' => $request->gambar,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'tgl_lahir' => $request->tgl_lahir,
        'ustadz' => $request->ustadz,
        'amanah' => $request->amanah,
        'kedisiplinan' => $request->kedisiplinan,
        'hafalans' => $request->hafalans,
        'mutqin' => $request->mutqin,
        'fundraising' => $request->fundraising,
        'image' => $request->image,
        'tanggal' => $request->tanggal,
        'alpha' => $request->alpha,
        'sakit' => $request->sakit,
        'izin' => $request->izin,
        'tahajjud' => 'tidak',
        'odoj' => 'tidak',
        'stw' => 'tidak',
        'majelis' => $request->majelis,
        'khidmat' => $request->khidmat,
        'leadership' => $request->leadership,
        'entrepreneur' => $request->enterepreneur,
        'speaking' => $request->speaking,
        'operation' => $request->operation,
        'mengajar' => $request->mengajar,
        'administration' => $request->administration,
        'hafalan' => $request->hafalan,
    ]);

        // Pindahkan gambar yang diunggah ke penyimpanan
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarPath = 'images/poto-santri/' . $santri->id_santri . '.' . $gambar->getClientOriginalExtension();
            Storage::disk('public')->put($gambarPath, file_get_contents($gambar));
            $santri->gambar = $gambarPath;
            $santri->save();
        }

        // Pindahkan gambar yang diunggah ke penyimpanan
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'images/poto-fundraising/' . $guru->id_ust . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put($imagePath, file_get_contents($image));
            $guru->image = $imagePath;
            $guru->save();
        }

        return response()->json([
            'data' => $santri,
            'message' => 'Santri berhasil ditambahkan oleh guru'
        ], 201);
    }


}
