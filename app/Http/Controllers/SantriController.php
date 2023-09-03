<?php

namespace App\Http\Controllers;

use App\Models\ProfileSantri;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SantriController extends Controller
{
    public function registersantri(Request $request)
    {
        $ustadz = Auth::user();
        // Check if the ustadz has reached the limit of adding santri
        // $santriCount = User::where('id_ustadz', $ustadz->id_ustadz)
        // ->where('role', 'santri_pondok')
        // ->count();

        // if ($santriCount >= 10) {
        //     return response()->json(['error' => 'You have reached the limit of adding santri'], 400);
        // }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tgl_lahir' => 'required|date_format:Y/m/d',
            'gender' => 'required|string',
            'angkatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $email = $request->email;

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

        // Get the user who is registering the 'ust_pondok'
        $IdUstadz = Auth::user(); // Assuming you are using Laravel's built-in authentication
        $maxIdSantriPondok = User::max('id_santri') ?? 0;
        $newIdSantriPondok = $maxIdSantriPondok + 1;

        // Get the id_ust_pondok value from the admin user
        $idUstPondok = $IdUstadz->id_ustadz;
        $IdAdmin = $IdUstadz->id_admin;

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'id_admin' => $IdAdmin, // Use the id_ust_pondok from the admin user
            'id_ustadz' => $idUstPondok, // Use the id_ust_pondok from the admin user
            'id_santri' => $newIdSantriPondok,
        ]);

        // Handle image upload
        $gambar = $request->file('gambar');
        if (!$gambar->isValid()) {
            return response()->json(['error' => 'Invalid image file'], 400);
        }

         // Upload and save gambar
         $gambar = $request->file('gambar');
         $gambarPath = 'images/poto-santri/' . $user->id_user . '.' . $gambar->getClientOriginalExtension();
         $gambar->move(public_path('images/poto-santri'), $gambarPath);

        // Create a new ProfileSantri
        $ustadz = ProfileSantri::create([
            'id_admin' => $IdAdmin,
            'id_ustadz' => $idUstPondok,
            'id_santri' => $newIdSantriPondok,
            'id_user' => $user->id_user,
            'gambar' => $gambarPath,
            'tgl_lahir' => $request->tgl_lahir,
            'gender' => $request->gender,
            'angkatan' => $request->angkatan,
        ]);

        $ustadz->save();

        // Create and return access token
        $token = $user->createToken('API Token')->accessToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }


    public function getSantriByAdminId(Request $request)
    {
        $adminUser = Auth::user();

        // Ambil ID admin dari user yang terautentikasi
        $idAdmin = $adminUser->id_admin;

        // Ambil semua data santri yang memiliki role "santri_pondok" dan id_admin yang sama dengan id_admin admin yang terautentikasi
        $santriUserIds = User::join('profile_santris', 'users.id_santri', '=', 'profile_santris.id_santri')
                            ->where('users.id_admin', $idAdmin)
                            ->where('users.role', 'santri_pondok')
                            ->pluck('users.id_santri');

        $santriUserDetails = User::whereIn('id_santri', $santriUserIds)
                            ->get(['id_santri', 'name', 'email', 'role']);

        $santriProfileDetails = ProfileSantri::whereIn('id_santri', $santriUserIds)
                            ->get(['id_santri', 'gambar', 'tgl_lahir', 'gender', 'angkatan']);

        return response()->json([
            'santri_user_details' => $santriUserDetails,
            'santri_profile_details' => $santriProfileDetails
            ], 200);
    }








}
