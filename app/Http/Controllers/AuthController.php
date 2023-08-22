<?php

namespace App\Http\Controllers;

use App\Models\guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RegistrationAcceptedNotification;
use App\Notifications\RegistrationRejectedNotification;
use App\Notifications\NewUserRegistrationNotification;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    public function index()
    {
        $masjids = User::all(); // Ambil semua data user dari model

        return response()->json(['data' => $masjids]);
    }

    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $user->id_masjid,
                'nama_masjid' => $user->nama_masjid,
                'email' => $user->email,
                'updated_at' => $user->updated_at,
                'created_at' => $user->created_at,
            ],
            'verifikasi' => $user->verifikasi,
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_masjid' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'alamat_masjid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $nama_masjid = $request->nama_masjid;
        $gambar = $request->gambar;
        $email = $request->email;
        $password = $request->password;
        $provinsi = $request->provinsi;
        $kabupaten = $request->kabupaten;
        $alamat_masjid = $request->alamat_masjid;

     // Simpan gambar ke dalam folder 'public/images/poto-masjid'
        $gambar = $request->file('gambar');
        $gambarPath = 'images/poto-masjid/' . $nama_masjid . '.' . $gambar->getClientOriginalExtension();
        $gambar->move(public_path('images/poto-masjid'), $gambarPath);

        // Check if the email uses a valid domain
        $allowedDomains = ['gmail.com', 'yahoo.com'];
        $domain = substr(strrchr($email, "@"), 1);
        if (!in_array($domain, $allowedDomains)) {
            return response()->json(['error' => 'Email must use @gmail or @yahoo domain'], 400);
        }
        // Check if the email already exists
        if (User::where('email', $email)->exists()) {
            return response()->json(['error' => 'Email already exists'], 400);
        }
   // Perform further actions for user registration here
        $user = User::create([
            'nama_masjid' => $nama_masjid,
            'gambar' => $gambar,
            'email' => $email,
            'password' => bcrypt($request->password),
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'alamat_masjid' => $alamat_masjid,
            'verifikasi' => false,
        ]);

 // Send notification to administrators
        // $adminEmail = 'admin@gmail.com'; // Ganti dengan alamat email administrator yang valid
        // Notification::route('mail', $adminEmail)
        //             ->notify(new NewUserRegistrationNotification($user));

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'data' => [
                'id' => $user->id_masjid,
                'nama_masjid' => $user->nama_masjid,
                'email' => $user->email,
                'updated_at' => $user->updated_at,
                'created_at' => $user->created_at,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

// Admin Pusat Kirim Verifikasi
    public function processUserVerification($id, Request $request)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        $user = User::findOrFail($id);

        if ($request->action === 'accept') {
            $user->verifikasi = true;
            $user->save();
            $user->notify(new RegistrationAcceptedNotification($user));
            $message = 'User accepted successfully';
        } elseif ($request->action === 'reject') {
            // Lakukan tindakan yang sesuai untuk penolakan
            $user->notify(new RegistrationRejectedNotification($user));
            $message = 'User rejected successfully';
        } else {
            return response()->json(['error' => 'Invalid action'], 400);
        }

        // Kirim notifikasi tergantung pada tindakan yang dilakukan
        if ($request->action === 'accept') {
            Notification::send($user, new RegistrationAcceptedNotification($user));
        } elseif ($request->action === 'reject') {
            Notification::send($user, new RegistrationRejectedNotification($user));
        }

        return response()->json(['message' => $message], 200);
    }


// Login
    // public function login(Request $request)
    // {
    //     $data = $request->validate([
    //         'email' => 'email|required',
    //         'password' => 'required'
    //     ]);

    //     if (Auth::attempt($data)) {
    //         $user = Auth::user();
    //         $token = $user->createToken('AuthToken')->accessToken;

    //     if ($user) {
    //         $role = $user->role;

    //         if ($role === 'admin_pondok' && !$user->verifikasi) {
    //             return response()->json(['error_message' => 'Akun admin pondok belum diverifikasi'], 401);
    //         }

    //         // Lanjutkan dengan pembuatan token sesuai role
    //         if ($role === 'admin_pusat') {
    //             $token = $user->createToken('Admin Pusat Token')->accessToken;
    //         } elseif ($role === 'staff_pusat') {
    //             $token = $user->createToken('Staff Pusat Token')->accessToken;
    //         } elseif ($role === 'ust_pondok') {
    //             $token = $user->createToken('Ustad Pondok Token')->accessToken;
    //         } elseif ($role === 'santri_pondok') {
    //             $token = $user->createToken('Santri Pondok Token')->accessToken;
    //         } elseif ($role === 'staff_pondok') {
    //             $token = $user->createToken('Staff Pondok Token')->accessToken;
    //         } else {
    //             return response()->json(['error_message' => 'Invalid role'], 401);
    //         }

    //         return response()->json(['user' => $user, 'token' => $token, 'role' => $role], 200);
    //     }

    //     return response()->json(['error_message' => 'Kombinasi email dan password salah atau akun belum di validasi'], 401);
    // }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (Auth::guard('guru')->attempt($data)) {
            $user = Auth::guard('guru')->user();
            $role = 'ust_pondok'; // Ganti dengan role yang sesuai

            $token = $user->createToken('Ustad Pondok Token')->accessToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'role' => $role
            ], 200);
        }

        return response()->json([
            'error_message' => 'Kombinasi email dan password salah atau akun belum di validasi'
        ], 401);
    }
}
