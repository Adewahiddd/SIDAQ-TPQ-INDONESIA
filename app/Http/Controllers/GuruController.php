<?php

namespace App\Http\Controllers;

use App\Models\guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuruController extends Controller
{
    public function index()
    {

    }

    public function registerguru(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'tgl_lahir' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $nama = $request->nama;
        $gambarPath = null;
        $gambar = $request->file('gambar');
        $email = $request->email;
        $password = $request->password;
        $tgl_lahir = $request->tgl_lahir;
        // $guruRole = 'ust_pondok';

        // Jika ada gambar diunggah, simpan ke folder 'public/images/poto-ustadz'
        if ($gambar) {
            $gambarPath = 'images/poto-ustadz/' . $nama . '.' . $gambar->getClientOriginalExtension();
            $gambar->move(public_path('images/poto-ustadz'), $gambarPath);
        }

        // Check if the email uses a valid domain
        $allowedDomains = ['gmail.com', 'yahoo.com'];
        $domain = substr(strrchr($email, "@"), 1);
        if (!in_array($domain, $allowedDomains)) {
            return response()->json(['error' => 'Email must use @gmail or @yahoo domain'], 400);
        }
        // Check if the email already exists
        if (Guru::where('email', $email)->exists()) {
            return response()->json(['error' => 'Email already exists'], 400);
        }

        $guru = Guru::create([
            'nama' => $nama,
            'gambar' => $gambarPath, // Gunakan path gambar yang sudah dibuat
            'role' => 'ust_pondok', // Set role sebagai 'admin_pondok'
            'email' => $email,
            'password' => bcrypt($password),
            'tgl_lahir' => $tgl_lahir,
        ]);

        $token = $guru->createToken('API Token')->accessToken;
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
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }


}
