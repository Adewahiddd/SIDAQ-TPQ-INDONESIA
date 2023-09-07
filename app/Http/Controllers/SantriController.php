<?php

namespace App\Http\Controllers;

use App\Models\AmalSholeh;
use App\Models\ProfileSantri;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SantriController extends Controller
{
// get jumlah santri by provinsi
    public function index()
    {
        $inputanPerProvinsi = ProfileSantri::select('provinsi')
            ->selectRaw('count(*) as total')
            ->groupBy('provinsi')
            ->get();

        return view('inputan.index', compact('inputanPerProvinsi'));
    }


    public function registersantri(Request $request)
    {
        $ustadz = Auth::user();
    // Check if the ustadz has reached the limit of adding santri
        $santriCount = User::where('id_ustadz', $ustadz->id_ustadz)
        ->where('role', 'santri_pondok')
        ->count();

        if ($santriCount >= 10) {
            return response()->json(['error' => 'You have reached the limit of adding santri'], 400);
        }

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

// DELETE SANTRI DARI DATA BASE
    // public function deleteSantri($id_santri)
    // {
    //     $user = User::where('id_santri', $id_santri)->first();

    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     // Check if the authenticated user is the same ustadz who registered the santri
    //     $authenticatedUser = auth()->user();

    //     // Pastikan bahwa yang ingin menghapus adalah ustadz dan id_ustadz-nya sesuai dengan yang mendaftarkan santri
    //     if ($authenticatedUser->role !== 'ust_pondok' || $authenticatedUser->id_ustadz !== $user->id_ustadz) {
    //         return response()->json(['error' => 'Only the ustadz who registered the santri can delete it'], 403);
    //     }

    //     // Delete the associated profile and user
    //     ProfileSantri::where('id_santri', $id_santri)->delete();
    //     $user->delete();

    //     return response()->json(['message' => 'Santri deleted successfully'], 200);
    // }

// HAPUS SANTRI DARI JUMLAH LIMIT USTADZ
    public function deleteSantri($id_santri)
    {
        $santri = User::where('id_santri', $id_santri)->first();

        if (!$santri) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if the authenticated user is the same ustadz who registered the santri
        $authenticatedUser = auth()->user();

        // Pastikan bahwa yang ingin menghapus adalah ustadz dan id_ustadz-nya sesuai dengan yang mendaftarkan santri
        if ($authenticatedUser->role !== 'ust_pondok' || $authenticatedUser->id_ustadz !== $santri->id_ustadz) {
            return response()->json(['error' => 'Only the ustadz who registered the santri can delete it'], 403);
        }

        // Hapus santri dari batasan limit ustadz
        $ustadz = User::where('id_ustadz', $santri->id_ustadz)->first();

        if ($ustadz) {
            // Hitung jumlah santri yang dimiliki oleh ustadz
            $santriCount = User::where('id_ustadz', $ustadz->id_ustadz)
                ->where('role', 'santri_pondok')
                ->count();

            // Kurangi jumlah santri_count ustadz jika lebih dari 0
            if ($santriCount > 10) {
                $ustadz->santri_count = $santriCount - 1;
                $ustadz->save();
            }
        }

        return response()->json(['message' => 'Santri removed from the ustadz\'s limit'], 200);
    }

// get santri berdasarkan id admin atau atau admin_pondok
    public function getSantriByAdminId($id_admin)
    {
        // Periksa apakah id_admin adalah angka positif
        if (!is_numeric($id_admin) || $id_admin <= 0) {
            return response()->json(['error' => 'Invalid id_admin'], 400);
        }

        // Cari admin dengan id_admin yang sesuai
        $admin = User::where('id_admin', $id_admin)
            ->where('role', 'admin_pondok')
            ->first();

        if (!$admin) {
            return response()->json(['error' => 'Admin not found for this id_admin'], 404);
        }

        // Cari santri dengan id_admin yang sesuai
        $santri = User::where('id_admin', $id_admin)
            ->where('role', 'santri_pondok')
            ->get();

        if ($santri->isEmpty()) {
            return response()->json(['error' => 'No santri found for this id_admin'], 404);
        }

        // Tampilkan data santri
        $santri_user_details = $santri->map(function ($item) {
            return [
                'id_santri' => $item->id_santri,
                'name' => $item->name,
                'email' => $item->email,
                'role' => $item->role,
            ];
        });

        $santri_profile_details = $santri->map(function ($item) {
            $profile = $item->profileSantri;

            // Menghapus properti yang tidak ingin ditampilkan
            unset($profile->provinsi);
            unset($profile->kabupaten);
            unset($profile->alamat_masjid);
            unset($profile->verifikasi);
            unset($profile->id_profile);
            unset($profile->id_user);

            return $profile;
        });

        return response()->json([
            'santri_user_details' => $santri_user_details,
            'santri_profile_details' => $santri_profile_details,
        ], 200);

    }

    // 'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
// Update Fundraising (Santri)
    public function updateFundraising(Request $request, $id_amal)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'fundraising' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        // Pastikan pengguna adalah ustadz
        if (!$user || $user->role !== 'santri_pondok') {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }

        // Temukan rekaman AmalSholeh berdasarkan id_amal
        $amalSholeh = AmalSholeh::find($id_amal);
            // dd($id_amal);
        if (!$amalSholeh) {
            return response()->json(['error' => 'Amal Sholeh tidak ditemukan'], 404);
        }

        // Update kolom 'fundraising'
        $amalSholeh->fundraising = $request->fundraising;
        $amalSholeh->save();

        // Mengelola gambar jika disediakan
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarPath = 'images/poto-fundraising/' . $user->id_santri . '.' . $gambar->getClientOriginalExtension();

            // Hapus gambar sebelumnya jika ada
            if ($amalSholeh->gambar && file_exists(public_path($amalSholeh->gambar))) {
                unlink(public_path($amalSholeh->gambar));
            }

            $gambar->move(public_path('images/poto-fundraising'), $gambarPath);
            $amalSholeh->gambar = $gambarPath;
            $amalSholeh->save();
        }

        return response()->json(['message' => 'Amal Sholeh santri berhasil diupdate', 'santri' => $user->name, 'amalSholeh' => $amalSholeh], 200);
    }



    // public function registersantrii(Request $request)
    // {
    //     // Membungkus seluruh operasi dalam transaksi
    //     DB::beginTransaction();

    //     try {
    //         $ustadz = Auth::user();

    //         // Check if the ustadz has reached the limit of adding santri
    //         $santriCount = User::where('id_ustadz', $ustadz->id_ustadz)
    //             ->where('role', 'santri_pondok')
    //             ->count();

    //         if ($santriCount >= 10) {
    //             return response()->json(['error' => 'You have reached the limit of adding santri'], 400);
    //         }

    //         // Generate a unique ID for the new santri based on existing data
    //         $maxIdSantriPondok = User::max('id_santri') ?? 0;
    //         $newIdSantriPondok = $maxIdSantriPondok + 1;

    //         // Validasi input
    //         $validator = Validator::make($request->all(), [
    //             'name' => 'required|max:255',
    //             'email' => 'required|email|unique:users',
    //             'password' => 'required|min:8',
    //             'role' => 'required|string',
    //             'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
    //             'tgl_lahir' => 'required|date_format:Y/m/d',
    //             'gender' => 'required|string',
    //             'angkatan' => 'required|string',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['errors' => $validator->errors()], 400);
    //         }

    //         $email = $request->email;

    //         // Check if the email uses a valid domain
    //         $allowedDomains = ['gmail.com', 'yahoo.com'];
    //         $domain = substr(strrchr($email, "@"), 1);
    //         if (!in_array($domain, $allowedDomains)) {
    //             return response()->json(['error' => 'Email must use @gmail or @yahoo domain'], 400);
    //         }

    //         // Create a new user
    //         $user = User::create([
    //             'name' => $request->name,
    //             'email' => $email,
    //             'password' => bcrypt($request->password),
    //             'role' => $request->role,
    //             'id_admin' => $ustadz->id_admin,
    //             'id_ustadz' => $ustadz->id_ustadz,
    //             'id_santri' => $newIdSantriPondok,
    //         ]);

    //         // Mendapatkan ID user yang baru saja dibuat
    //         $id_user = $user->id;

    //         // Handle image upload
    //         $gambar = $request->file('gambar');
    //         if (!$gambar->isValid()) {
    //             return response()->json(['error' => 'Invalid image file'], 400);
    //         }

    //         // Upload and save gambar
    //         $gambarPath = 'images/poto-santri/' . $id_user . '.' . $gambar->getClientOriginalExtension();
    //         $gambar->move(public_path('images/poto-santri'), $gambarPath);

    //         // Create a new ProfileSantri
    //         $profileSantri = ProfileSantri::create([
    //             'id_ustadz' => $ustadz->id_ustadz,
    //             'id_santri' => $newIdSantriPondok,
    //             'id_user' => $id_user, // Menggunakan ID user yang baru saja dibuat
    //             'gambar' => $gambarPath,
    //             'tgl_lahir' => $request->tgl_lahir,
    //             'gender' => $request->gender,
    //             'angkatan' => $request->angkatan,
    //         ]);

    //         // Commit transaksi jika semua operasi berhasil
    //         DB::commit();

    //         // Create and return access token
    //         $token = $user->createToken('API Token')->accessToken;

    //         return response()->json(['user' => $user, 'token' => $token], 200);
    //     } catch (\Exception $e) {
    //         // Rollback transaksi jika terjadi kesalahan
    //         DB::rollback();

    //         // Handle kesalahan dengan mengembalikan pesan error
    //         return response()->json(['error' => 'Failed to register santri. ' . $e->getMessage()], 500);
    //     }
    // }










}
