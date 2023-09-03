<?php

namespace App\Http\Controllers;

use App\Models\ProfileSantri;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UstadzController extends Controller
{
    public function registerustadz(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tgl_lahir' => 'required|date_format:Y/m/d',
            'gender' => 'required|string',
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

        $adminUser = Auth::user(); // Assuming you are using Laravel's built-in authentication
        // Determine the new IDs for id_ustadz and id_admin
        $maxIdUstPondok = User::max('id_ustadz') ?? 0;
        $newIdUstPondok = $maxIdUstPondok + 1;

        $IdAdmin = $adminUser->id_admin;

        $user = User::create([
            'id_admin' => $IdAdmin,
            'id_ustadz' => $newIdUstPondok,
            'name' => $request->name,
            'email' => $email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        $user->save();

        // Handle image upload
        $gambar = $request->file('gambar');
        if (!$gambar->isValid()) {
            return response()->json(['error' => 'Invalid image file'], 400);
        }

        $gambarPath = 'images/poto-ustadz/' . $user->id_ustsadz . '.' . $gambar->getClientOriginalExtension();
        $gambar->move(public_path('images/poto-ustadz'), $gambarPath);

        $ustadz = ProfileSantri::create([
            'id_admin' => $IdAdmin, // Menggunakan id_ust_pondok yang baru dibuat
            'id_ustadz' => $newIdUstPondok, // Menggunakan id_ust_pondok yang baru dibuat
            'id_user' => $user->id_user,
            'gambar' => $gambarPath,
            'tgl_lahir' => $request->tgl_lahir,
            'gender' => $request->gender,
        ]);

        $ustadz->save();

        // Create and return access token
        $token = $user->createToken('API Token')->accessToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }
// get Ustadz
    public function getUstadzByAdminId(Request $request)
        {
            $adminUser = Auth::user();

            // Get the admin's ID from the authenticated user
            $idAdmin = $adminUser->id_admin;

            // Retrieve all "ust_pondok" users' IDs with the same admin ID and role
            $ustadzUserIds = User::where('id_admin', $idAdmin)
                                ->where('role', 'ust_pondok')
                                ->pluck('id_ustadz');

            // Retrieve the user details of the "ust_pondok" users
            $ustadzUserDetails = User::whereIn('id_ustadz', $ustadzUserIds)
                                    ->where('role', 'ust_pondok')
                                    ->get(['id_admin', 'id_ustadz', 'name', 'email', 'role']);

            // Retrieve the profile details of the "ust_pondok" users
            $ustadzProfileDetails = ProfileSantri::whereIn('id_ustadz', $ustadzUserIds)
                                                ->whereNull('id_santri') // Filter out profiles with id_santri not null
                                                ->get(['id_admin', 'id_ustadz',
                                                        'gambar','tgl_lahir','gender']);

            return response()->json([
                'ustadz_user_details' => $ustadzUserDetails,
                'ustadz_profile_details' => $ustadzProfileDetails
            ], 200);
        }

// get jumlah ustadz
    public function getTotalUstadzByAdminId(Request $request)
    {
        $adminUser = Auth::user();

        // Get the admin's ID from the authenticated user
        $idAdmin = $adminUser->id_admin;

        // Get the total count of "ust_pondok" users with the same admin ID and role
        $totalUstadz = User::where('id_admin', $idAdmin)
                        ->where('role', 'ust_pondok')
                        ->count();

        return response()->json([
            'total_ustadz' => $totalUstadz
        ], 200);
    }

// get jumlah Santri
    public function getTotalSantriByAdminId(Request $request)
    {
        $adminUser = Auth::user();

        // Get the admin's ID from the authenticated user
        $idAdmin = $adminUser->id_admin;

        // Get the total count of "ust_pondok" users with the same admin ID and role
        $totalUstadz = User::where('id_admin', $idAdmin)
                        ->where('role', 'santri_pondok')
                        ->count();

        return response()->json([
            'total_ustadz' => $totalUstadz
        ], 200);
    }


}
