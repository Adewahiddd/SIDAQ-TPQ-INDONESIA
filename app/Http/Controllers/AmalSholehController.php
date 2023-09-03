<?php

namespace App\Http\Controllers;

use App\Models\AmalSholeh;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AmalSholehController extends Controller
{
// Get Amal Sholeh santri  {MASIH BROKEN}
   public function index()
{
    $user = Auth::user();

    // Pastikan pengguna sudah masuk
    if ($user) {
        // Ambil semua data AmalSholeh
        $amalSholeh = AmalSholeh::all();

        // Buat respons JSON untuk entri AmalSholeh
        $responseData = [];
        foreach ($amalSholeh as $entry) {
            $responseData[] = [
                'id_amal' => $entry->id_amal,
                'id_ustadz' => $entry->id_ustadz,
                'id_santri' => $entry->id_santri,
                'hafalan' => $entry->hafalan,
                'mutqin' => $entry->mutqin,
                'fundraising' => $entry->fundraising,
                'amanah' => $entry->amanah,
                'kedisiplinan' => $entry->kedisiplinan,
            ];
        }

        return response()->json(['message' => 'kamu hebat bang', 'data' => $responseData], 200);
    }

    return response()->json(['error' => 'Tidak Diotorisasi'], 401);
}





// Create dan Update
    public function createAmalSholeh(Request $request, $id_santri)
    {
        $validator = Validator::make($request->all(), [
            'hafalan' => 'required|string',
            'mutqin' => 'required|string',
            'fundraising' => 'required|string',
            'amanah' => 'required|string',
            'kedisiplinan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        // Pastikan pengguna adalah ustadz
        if ($user && $user->role === 'ust_pondok') {
            // Cek apakah santri dengan id_santri yang dimaksud sudah terdaftar oleh ustadz
            $santri = User::where('id_santri', $id_santri)
                ->where('id_ustadz', $user->id_ustadz)
                ->first();

                $idSantri = $santri->name;

            if ($santri) {
                // Temukan atau buat rekaman AmalSholeh
                $amalSholeh = AmalSholeh::updateOrCreate(
                    ['id_ustadz' => $user->id_ustadz, 'id_santri' => $id_santri],
                    [
                        'hafalan' => $request->hafalan,
                        'mutqin' => $request->mutqin,
                        'fundraising' => $request->fundraising,
                        'amanah' => $request->amanah,
                        'kedisiplinan' => $request->kedisiplinan,
                    ]
                );

                return response()->json(['message' => 'Amal Sholeh santri berhasil dibuat atau diupdate', 'santri' => $idSantri, 'user' => $amalSholeh], 200);
            } else {
                return response()->json(['error' => 'Tidak Diotorisasi'], 401);
            }
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }
    }


// BROKEN CUKKK
    public function deleteAmalSholeh($id_amal)
    {
        $user = Auth::user();

        // Pastikan pengguna adalah ustadz dan memiliki peran 'ust_pondok'
        if ($user && $user->role === 'ust_pondok') {
            // Temukan dan hapus AmalSholeh berdasarkan id_amal
            $amalSholeh = AmalSholeh::find($id_amal);

            if ($amalSholeh) {
                // Periksa apakah id_ustadz yang terkait dengan AmalSholeh sesuai dengan id pengguna saat ini
                if ($amalSholeh->id_ustadz === $user->id_ustadz) {
                    // Hapus AmalSholeh
                    $amalSholeh->delete();
                    return response()->json(['message' => 'Amal Sholeh santri berhasil dihapus'], 200);
                } else {
                    return response()->json(['error' => 'Tidak Diotorisasi'], 403); // 403 artinya terlarang
                }
            } else {
                return response()->json(['error' => 'Amal Sholeh tidak ditemukan'], 404);
            }
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }
    }






}
