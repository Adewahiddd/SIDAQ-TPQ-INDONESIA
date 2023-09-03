<?php

namespace App\Http\Controllers;

use App\Models\Masjid;
use App\Models\ProfileSantri;
use App\Models\User;
use App\Notifications\RegistrationAcceptedNotification;
use App\Notifications\RegistrationRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'alamat_masjid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Determine verifikasi value
        $verifikasi = ($request->role === 'admin_pondok') ? 0 : null;

        // $adminUser = Auth::user(); // Assuming you are using Laravel's built-in authentication
        $maxIdAdminPondok = User::max('id_admin') ?? 0;
        $newIdAdmin = $maxIdAdminPondok + 1;
        // Create a new user without specifying the ID
        $user = User::create([
            'id_admin' => $newIdAdmin,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,

        ]);
        // Determine the values for id_ustadz and id_santri based on role
        $idUstadz = ($request->role === 'admin_pusat' || $request->role === 'admin_pondok') ? null : $user->id_user;
        $idSantri = ($request->role === 'admin_pusat' || $request->role === 'admin_pondok') ? null : $user->id_user;


        // Upload and save gambar
        $gambar = $request->file('gambar');
        $gambarPath = 'images/poto-masjid/' . $user->id_user . '.' . $gambar->getClientOriginalExtension();
        $gambar->move(public_path('images/poto-masjid'), $gambarPath);

        // Create a new ProfileSantri with the user's ID
        $profileSantri = ProfileSantri::create([
            'id_user' => $user->id_user,
            'id_admin' => $user->id_admin,
            'gambar' => $gambarPath,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'alamat_masjid' => $request->alamat_masjid,
            'verifikasi' => $verifikasi,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        // Generate API token
        $token = $user->createToken('API Token')->accessToken;

        // Return response
        return response()->json(['user' => $user, 'token' => $token], 200);
    }


// login
   public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $role = $user->role;

            if ($role === 'admin_pusat') {
                $tokenName = 'Admin Pusat Token';
                $token = $user->createToken($tokenName)->accessToken;
            } elseif ($role === 'admin_pondok') {
                $tokenName = 'Admin Pondok Token';
                $ProfileSantri = ProfileSantri::where('id_user', $user->id_user)->first();

                if ($ProfileSantri && ($ProfileSantri->verifikasi === true || $ProfileSantri->verifikasi === 1)) {
                    $token = $user->createToken($tokenName)->accessToken;
                } else {
                    return response()->json(['message' => 'Maaf, anda belum di verifikasi'], 403);
                }
            } elseif ($role === 'staff_pusat') {
                $tokenName = 'Staff Pusat Token';
                $token = $user->createToken($tokenName)->accessToken;
            } elseif ($role === 'santri_pondok') {
                $tokenName = 'Santri Pondok Token';
                $token = $user->createToken($tokenName)->accessToken;
            } elseif ($role === 'ust_pondok') {
                $tokenName = 'Ustadz Pondok Token';
                $token = $user->createToken($tokenName)->accessToken;
            } elseif ($role === 'staff_pondok') {
                $tokenName = 'Staff Pondok Token';
                $token = $user->createToken($tokenName)->accessToken;
            } elseif ($role === 'staff_ust') {
                $tokenName = 'Staff Ust Token';
                $token = $user->createToken($tokenName)->accessToken;
            } else {
                $tokenName = 'User Token';
            }

            if (isset($token)) {
                return response()->json([
                    'user' => [
                        'id_user' => $user->id_user,
                        'name' => $user->name,
                        'role' => $user->role,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]);
            }

        }

    }


// Update Profile Admin Pusat & Poondok
public function updateProfile(Request $request, $id_admin)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|max:255',
        'email' => 'required|email|unique:users,email,' . $id_admin . ',id_admin',
        'role' => 'required|string',
        'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        'provinsi' => 'required|string',
        'kabupaten' => 'required|string',
        'alamat_masjid' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $user = User::where('id_admin', $id_admin)->firstOrFail();

    // Update user attributes
    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role;
    $user->save();

    $profileSantri = ProfileSantri::where('id_admin', $id_admin)->firstOrFail();

    // Update profile attributes
    $profileSantri->provinsi = $request->provinsi;
    $profileSantri->kabupaten = $request->kabupaten;
    $profileSantri->alamat_masjid = $request->alamat_masjid;

    // Update gambar if provided
    if ($request->hasFile('gambar')) {
        // Cek apakah ada gambar sebelumnya
        if ($user->profileSantri && !empty($user->profileSantri->gambar)) {
            // Hapus gambar sebelumnya
            if (file_exists(public_path($user->profileSantri->gambar))) {
                unlink(public_path($user->profileSantri->gambar));
            }
        }

        $gambar = $request->file('gambar');
        $gambarPath = 'images/poto-masjid/' . $user->id_admin . '.' . $gambar->getClientOriginalExtension();
        $gambar->move(public_path('images/poto-masjid'), $gambarPath);
        $profileSantri->gambar = $gambarPath;
    }

    // Save the updated profile
    $profileSantri->save();

    // Return response
    return response()->json(['user' => $user, 'profile' => $profileSantri], 200);
}



// verifikas
    public function processUserVerification($id_profile, Request $request)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        $profileSantri = ProfileSantri::findOrFail($id_profile);

        if ($request->action === 'accept') {
            $profileSantri->verifikasi = true;
            $profileSantri->save();
            $profileSantri->user->notify(new RegistrationAcceptedNotification($profileSantri->user));
            $message = 'User accepted successfully';
        } elseif ($request->action === 'reject') {
            $profileSantri->verifikasi = false;
            $profileSantri->save();
            $profileSantri->user->notify(new RegistrationRejectedNotification($profileSantri->user));
            $message = 'User rejected successfully';
        } else {
            return response()->json(['error' => 'Invalid action'], 400);
        }

        return response()->json(['message' => $message], 200);
    }



    // public function processUserVerification($id_user, Request $request)
    // {
    //     $request->validate([
    //         'action' => 'required|in:accept,reject',
    //     ]);

    //     $user = User::find($id_user);

    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     $profileSantri = $user->profileSantri;

    //     if (!$profileSantri) {
    //         return response()->json(['error' => 'Profile not found'], 404);
    //     }

    //     if ($request->action === 'accept') {
    //         $profileSantri->verifikasi = 'true'; // Ubah menjadi string 'true'
    //         $profileSantri->save();
    //         // Lakukan notifikasi atau tindakan lainnya
    //         $message = 'User accepted successfully';
    //     } elseif ($request->action === 'reject') {
    //         $profileSantri->verifikasi = 'false'; // Ubah menjadi string 'false'
    //         $profileSantri->save();
    //         // Lakukan notifikasi atau tindakan lainnya
    //         $message = 'User rejected successfully';
    //     } else {
    //         return response()->json(['error' => 'Invalid action'], 400);
    //     }

    //     return response()->json(['message' => $message], 200);
    // }


// get Admin pondok
    public function indexprofileadmin()
    {
        $loggedInUser = Auth::user(); // Mengambil informasi pengguna yang masuk

        if ($loggedInUser->role === 'admin_pondok') {
            $adminProfile = ProfileSantri::where('id_admin', $loggedInUser->id_admin)
                ->select('id_admin', 'provinsi', 'kabupaten', 'alamat_masjid')
                ->first();

            if ($adminProfile) {
                return response()->json([
                    'admin_profile' => $adminProfile,
                    'user_details' => [
                        'name' => $loggedInUser->name,
                        'email' => $loggedInUser->email,
                        'role' => $loggedInUser->role,
                    ],
                ], 200);
            } else {
                return response()->json(['message' => 'Profil admin tidak ditemukan'], 404);
            }
        } else {
            return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengakses profil ini'], 403);
        }
    }

// get jumlah admin pondok yang sudah di verifikasi
    public function getVerifiedAdminMasjidCount()
    {
        $count = User::where('role', 'admin_pondok')
            ->whereHas('profileSantri', function ($query) {
                $query->where('verifikasi', '=', true);
            })
            ->count();

        return response()->json(['verified_admin_Pondok_count' => $count], 200);
    }

// get user yang sudah verifikasi
    public function getVerifiedUsers()
    {
        $verifiedAdminPondokUsers = User::where('role', 'admin_pondok')
                                        ->whereHas('profileSantri', function ($query) {
                                            $query->where('verifikasi', true);
                                        })
                                        ->get();

        return response()->json(['data' => $verifiedAdminPondokUsers]);
    }

// get user yang belum verifikasi
    public function getNotVerifiedUsers()
    {
        $notVerifiedAdminPondokUsers = User::where('role', 'admin_pondok')
                                        ->whereHas('profileSantri', function ($query) {
                                            $query->where('verifikasi', false);
                                        })
                                        ->get();

        return response()->json(['data' => $notVerifiedAdminPondokUsers]);
    }









}
