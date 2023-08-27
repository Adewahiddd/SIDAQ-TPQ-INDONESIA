<?php

namespace App\Http\Controllers;

use App\Models\guru;
use App\Models\santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SantriController extends Controller
{
// get santri berdasarkan id_ustadz nya
    public function index()
    {
        $guru = auth('api-guru')->user(); // Mendapatkan informasi Guru (Ustad) yang sedang login
            
        if (!$guru) {
            return response()->json(['error_message' => 'Anda belum terautentikasi'], 401);
        }

        $santris = Santri::where('id_ust', $guru->id_ust)->get();

        if ($santris->isEmpty()) {
            return response()->json(['error_message' => 'Maaf, Anda bukan ustad dari santri ini'], 403);
        }

        return response()->json(['santris' => $santris], 200);
    }

    

    public function AddSantri(Request $request, $id)
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

         // Verifikasi apakah pengguna yang mengakses memiliki peran "ust_pondok"
         if (!$guru || $guru->role !== 'ust_pondok') {
            return response()->json(['error' => 'Only ustadz can add a santri'], 403);
        }        

        // Temukan guru berdasarkan id
        $guru = guru::find($id);

        if (!$guru) {
            return response()->json(['error' => 'Guru not found'], 404);
        }

      // Membuat santri baru
      $santri = santri::create([
        'id_ust' => $guru->id_ust,
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
        'entrepreneur' => $request->entrepreneur,
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
            $santri->image = $imagePath;
            $santri->save();
        }

        return response()->json([
            'data' => $santri,
            'message' => 'Santri berhasil ditambahkan oleh guru'
        ], 201);
    }



    public function updateSantri(Request $request, $id)
    {
        $guru = auth('api-santri')->user(); // Mengambil guru yang terautentikasi

        // Verifikasi apakah pengguna yang mengakses memiliki peran "ust_pondok"
        if (!$guru || $guru->role !== 'santri_pondok') {
            return response()->json(['error' => 'Only santri can update a fundraising'], 403);
        }

        // Temukan santri berdasarkan id
        $santri = santri::find($id);

        if (!$santri) {
            return response()->json(['error' => 'Santri not found'], 404);
        }

        // Memperbarui fundraising, image, dan tanggal
        $santri->fundraising = $request->fundraising ?? $santri->fundraising;
        
        if ($request->hasFile('image')) {
            // Mengambil gambar yang sudah ada
            $existingImage = $santri->image;
            // Hapus gambar yang sudah ada
            if ($existingImage && Storage::disk('public')->exists($existingImage)) {
                Storage::disk('public')->delete($existingImage);
            }
            $image = $request->file('image');
            $imagePath = 'images/poto-fundraising/' . $guru->id_ust . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put($imagePath, file_get_contents($image));
            $santri->image = $imagePath;
        }
        
        $santri->save();
        

        $santri->tanggal = $request->tanggal ?? $santri->tanggal;
        $santri->save();

        return response()->json([
            'data' => $santri,
            'message' => 'Santri berhasil diperbarui oleh guru'
        ], 200);
    }


    public function getProfile()
    {
        $santri = auth('api-santri')->user(); // Mendapatkan informasi Santri yang sedang login
        
        if (!$santri) {
            return response()->json(['error_message' => 'Anda belum terautentikasi'], 401);
        }

        return response()->json([
            'nama' => $santri->nama,
            'gambar' => $santri->gambar,
            'email' => $santri->email,
            'tgl_lahir' => $santri->tgl_lahir,
        ], 200);
    }


    public function updateProfile(Request $request)
    {
        $santri = auth('api-santri')->user(); // Mendapatkan informasi Santri yang sedang login
        
        if (!$santri) {
            return response()->json(['error_message' => 'Anda belum terautentikasi'], 401);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'email' => 'required|string|email|max:255|unique:santris,email,' . $santri->id_santri,
            'password' => 'nullable|string|min:8',
            'tgl_lahir' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Update informasi profil
        $santri->nama = $request->nama;
        $santri->email = $request->email;
        $santri->tgl_lahir = $request->tgl_lahir;
        
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarPath = 'images/poto-santri/' . $santri->id_santri . '.' . $gambar->getClientOriginalExtension();
            Storage::disk('public')->put($gambarPath, file_get_contents($gambar));
            $santri->gambar = $gambarPath;
        }

        if ($request->has('password')) {
            $santri->password = bcrypt($request->password);
        }

        $santri->save();

        return response()->json([
            'data' => $santri,
            'message' => 'Profil berhasil diperbarui'
        ], 200);
    }




}
