<?php

namespace App\Http\Controllers;

use App\Models\AmalSholeh;
use App\Models\CategoriAmanah;
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

            // Buat respons JSON untuk entri AmalSholeh dengan menambahkan nama santri
            $responseData = [];
            foreach ($amalSholeh as $entry) {
                $santri = User::where('id_santri', $entry->id_santri)->first(); // Ambil data santri berdasarkan id_santri

                $responseData[] = [
                    'name' => $santri ? $santri->name : null, // Tambahkan nama santri
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


// Create amal sholeh
    public function createAmalSholeh(Request $request, $id_santri)
    {
        $validator = Validator::make($request->all(), [
            'hafalan' => 'required|string',
            'mutqin' => 'required|string',
            'fundraising' => 'required|string',
            'name_amanah' => 'required|exists:categori_amanahs,name_amanah',
            'kedisiplinan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        // Pastikan pengguna adalah 'ustadz'
        if ($user && $user->role === 'ust_pondok') {
            // Periksa apakah santri dengan 'id_santri' yang ditentukan terdaftar oleh ustadz
            $santri = User::where('id_santri', $id_santri)
                ->where('id_ustadz', $user->id_ustadz)
                ->first();

            if (!$santri) {
                return response()->json(['error' => 'Santri tidak ditemukan atau tidak diotorisasi'], 401);
            }

            $nameAmanah = $request->input('name_amanah');
            $amanahEntry = CategoriAmanah::where('name_amanah', $nameAmanah)->first();

            if (!$amanahEntry) {
                return response()->json(['error' => 'Amanah not found'], 404);
            }


            // Buat catatan 'AmalSholeh' baru dan hubungkan dengan 'amanah' menggunakan ID-nya
            $amalSholeh = AmalSholeh::create([
                'id_ustadz' => $user->id_ustadz,
                'id_santri' => $id_santri,
                'hafalan' => $request->hafalan,
                'mutqin' => $request->mutqin,
                'fundraising' => $request->fundraising,
                'name_amanah' => $nameAmanah,
                'kedisiplinan' => $request->kedisiplinan,
            ]);

            return response()->json(['message' => 'Amal Sholeh untuk santri berhasil dibuat', 'santri' => $santri->name, 'user' => $amalSholeh], 201);
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }
    }

// PUT amal sholeh
    public function updateAmalSholeh(Request $request, $id_amal)
    {
        $validator = Validator::make($request->all(), [
            'hafalan' => 'required|string',
            'mutqin' => 'required|string',
            'fundraising' => 'required|string',
            'name_amanah' => 'required|exists:categori_amanahs,name_amanah',
            'kedisiplinan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        // Pastikan pengguna adalah 'ustadz'
        if ($user && $user->role === 'ust_pondok') {
            // Temukan 'AmalSholeh' berdasarkan 'id_amal'
            $amalSholeh = AmalSholeh::find($id_amal)->first();

            if (!$amalSholeh) {
                return response()->json(['error' => 'Amal Sholeh tidak ditemukan'], 404);
            }

            // Update data AmalSholeh
            $amalSholeh->hafalan = $request->hafalan;
            $amalSholeh->mutqin = $request->mutqin;
            $amalSholeh->fundraising = $request->fundraising;
            $amalSholeh->name_amanah = $request->input('name_amanah');
            $amalSholeh->kedisiplinan = $request->kedisiplinan;

            $amalSholeh->save();

            return response()->json(['message' => 'Amal Sholeh berhasil diperbarui', 'user' => $amalSholeh], 200);
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }
    }




// Delete amal sholeh
    public function deleteAmalSholeh($id_amal)
    {
        $user = Auth::user();

        // Ensure the user is an 'ust_pondok'
        if (!$user || $user->role !== 'ust_pondok') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        // Find and delete the AmalSholeh based on id_amal
        $amalSholeh = AmalSholeh::where('id_amal', $id_amal)
            ->where('id_ustadz', $user->id_ustadz)
            ->first();

        if ($amalSholeh) {
            // Delete the AmalSholeh
            $amalSholeh->delete();
            return response()->json(['message' => 'Amal Sholeh santri successfully deleted'], 200);
        } else {
            return response()->json(['error' => 'Amal Sholeh not found or unauthorized'], 404);
        }
    }










}
