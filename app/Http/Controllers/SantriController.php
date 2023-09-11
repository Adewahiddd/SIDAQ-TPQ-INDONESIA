<?php

namespace App\Http\Controllers;

use App\Models\AmalSholeh;
use App\Models\CategoriDivisi;
use App\Models\ProfileSantri;
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
            'nomorwa' => ['required', 'regex:/^(08|\+628)\d{8,12}$/'],
            'status' => 'required|string',
            'aktivitas' => 'required|in:aktif,tidak aktif',
            'tgl_lahir' => 'required|date_format:Y/m/d',
            'gender' => 'required|string',
            'angkatan' => 'required|string',
            'name_divisi' => 'required|exists:categori_divisis,name_divisi',
            'provinsi' => 'required|string',
        ]);
        // dd($validator);

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

        // Generate a random 'idcard'
        $idcard = mt_rand(100000000000, 999999999999);
        $aktivitas = $request->aktivitas === 'aktif' ? true : false;

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

            $nameDivisi = $request->input('name_divisi');
            $divisiEntry = CategoriDivisi::where('name_divisi', $nameDivisi)->first();

            if (!$divisiEntry) {
                return response()->json(['error' => 'Divisi not found'], 404);
            }

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
                'idcard' => $idcard,
                'nomorwa' => $request->nomorwa,
                'status' => $request->status,
                'aktivitas' => $aktivitas,
                'tgl_lahir' => $request->tgl_lahir,
                'gender' => $request->gender,
                'angkatan' => $request->angkatan,
                'name_divisi' => $nameDivisi,
                'provinsi' => $request->provinsi,
            ]);

        $ustadz->save();

        // Create and return access token
        $token = $user->createToken('API Token')->accessToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    public function updateSantri(Request $request, $id_santri)
    {
        $user = Auth::user(); // Mengambil user yang sedang login

        // Periksa apakah user adalah admin_pondok atau memiliki id_admin tertentu
        if (!$user || ($user->role != 'admin_pondok' && !$user->id_admin)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Temukan santri yang akan diperbarui
        $santri = ProfileSantri::where('id_santri', $id_santri)->first();

        // Periksa apakah santri ditemukan
        if (!$santri) {
            return response()->json(['error' => 'Santri not found'], 404);
        }

        // Periksa apakah santri yang akan diperbarui adalah santri_pondok
        if ($santri->user->role != 'santri_pondok') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'role' => 'required|string',
            'status' => 'required|string',
            'aktivitas' => 'required|in:aktif,tidak aktif',
            'angkatan' => 'required|string',
            'name_divisi' => 'required|exists:categori_divisis,name_divisi',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $santri->user->role = $request->role;
        $santri->user->save();

        $santri->status = $request->status;
        $santri->aktivitas = $request->aktivitas === 'aktif' ? true : false;
        $santri->angkatan = $request->angkatan;

    // Ambil nilai name_divisi dari kategori divisi
        $nameDivisi = $request->input('name_divisi');
        $divisiEntry = CategoriDivisi::where('name_divisi', $nameDivisi)->first();

        if (!$divisiEntry) {
            return response()->json(['error' => 'Divisi not found'], 404);
        }

        $santri->name_divisi = $divisiEntry->name_divisi;
        $santri->save();

        return response()->json(['message' => 'Santri updated successfully'], 200);
    }


// DELETE SANTRI DARI DATA BASE
    // public function deleteSantr($id_santri)
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

    public function getSantriByProvinsi(Request $request)
    {
        $user = Auth::user(); // Mengambil user yang sedang login

        // Periksa apakah user adalah admin_pondok atau memiliki id_admin tertentu
        if (!$user || ($user->role != 'admin_pondok' && !$user->id_admin)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Filter berdasarkan id_admin jika user adalah admin_pondok
        $query = ProfileSantri::query();
        if ($user->role == 'admin_pondok') {
            $query->where('id_admin', $user->id_admin);
        } elseif ($user->id_admin) {
            $query->where('id_admin', $user->id_admin);
        }

        // Filter hanya santri dengan role 'santri_pondok' (jika 'role' ada dalam tabel 'users')
        $query->whereHas('user', function ($q) {
            $q->where('role', 'santri_pondok');
        });

        // Query untuk mengambil jumlah santri berdasarkan provinsi
        $santriByProvinsi = $query->select('provinsi', DB::raw('count(*) as total'))
            ->groupBy('provinsi')
            ->get();

        // Membuat hasil dalam bentuk array asosiatif
        $result = [];
        foreach ($santriByProvinsi as $santri) {
            $result[$santri->provinsi] = $santri->total;
        }

        return response()->json(['district' => $result], 200);
    }


    public function getSantriByAngkatan(Request $request)
    {
        $user = Auth::user(); // Mengambil user yang sedang login

        // Periksa apakah user adalah admin_pondok atau memiliki id_admin tertentu
        if (!$user || ($user->role != 'admin_pondok' && !$user->id_admin)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Filter berdasarkan id_admin jika user adalah admin_pondok
        $query = ProfileSantri::query();
        if ($user->role == 'admin_pondok') {
            $query->where('id_admin', $user->id_admin);
        } elseif ($user->id_admin) {
            $query->where('id_admin', $user->id_admin);
        }

        // Filter hanya santri dengan role 'santri_pondok'
        $query->whereHas('user', function ($q) {
            $q->where('role', 'santri_pondok');
        });

        // Query untuk mengambil jumlah santri berdasarkan angkatan
        $santriByAngkatan = $query->select('angkatan', DB::raw('count(*) as total'))
            ->groupBy('angkatan')
            ->get();

        // Membuat hasil dalam bentuk array
        $result = [];
        foreach ($santriByAngkatan as $santri) {
            $result[$santri->angkatan] = $santri->total;
        }

        return response()->json(['santriByAngkatan' => $result], 200);
    }



    public function updateProfileSantri(Request $request, $id_santri)
    {
        $user = Auth::user(); // Mengambil user yang sedang login

        // Temukan santri yang akan diperbarui
        $santri = ProfileSantri::where('id_santri', $id_santri)->first();

        // Periksa apakah santri ditemukan
        if (!$santri) {
            return response()->json(['error' => 'Santri not found'], 404);
        }

        // Periksa apakah santri yang akan diperbarui adalah santri_pondok
        if ($santri->user->role != 'santri_pondok') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $santri->user->id_santri . ',id_santri',
            'password' => 'nullable|min:8', // Ubah menjadi nullable jika tidak ingin mengubah kata sandi
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Ubah menjadi nullable jika tidak ingin mengubah gambar
            'nomorwa' => ['required', 'regex:/^(08|\+628)\d{8,12}$/'],
            'tgl_lahir' => 'required|date_format:Y/m/d',
            'gender' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Update data santri
        $santri->user->name = $request->name;
        $santri->user->email = $request->email;
        if ($request->has('password')) {
            $santri->user->password = bcrypt($request->password);
        }

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            if (!$gambar->isValid()) {
                return response()->json(['error' => 'Invalid image file'], 400);
            }

            $gambarPath = 'images/poto-santri/' . $santri->user->id_user . '.' . $gambar->getClientOriginalExtension();
            $gambar->move(public_path('images/poto-santri'), $gambarPath);
            $santri->gambar = $gambarPath;
        }
        $santri->nomorwa = $request->nomorwa;
        $santri->tgl_lahir = $request->tgl_lahir;
        $santri->gender = $request->gender;

        $santri->user->save();
        $santri->save();

        return response()->json(['message' => 'Santri updated successfully'], 200);
    }





}
