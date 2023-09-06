<?php

namespace App\Http\Controllers;

use App\Models\Hafalan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HafalanController extends Controller
{
    public function createHafalan(Request $request, $id_santri)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'surah' => 'required|string',
            'jumlah_ayat' => 'required|integer',
            'nilai' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        // Pastikan pengguna adalah ustadz
        if ($user && $user->role === 'ust_pondok') {
            // Cek apakah santri dengan `id_santri` yang dimaksud sudah terdaftar oleh ustadz
            $santri = User::where('id_santri', $id_santri)
                ->where('id_ustadz', $user->id_ustadz)
                ->first();

            if ($santri) {
                // Buat rekaman Hafalan baru untuk santri ini
                $hafalan = Hafalan::create([
                    'id_ustadz' => $user->id_ustadz,
                    'id_santri' => $id_santri,
                    'tanggal' => $request->tanggal,
                    'surah' => $request->surah,
                    'jumlah_ayat' => $request->jumlah_ayat,
                    'nilai' => $request->nilai,
                ]);

                return response()->json(['message' => 'Hafalan santri berhasil dibuat', 'santri' => $santri->name, 'hafalan' => $hafalan], 201);
            } else {
                return response()->json(['error' => 'Santri tidak ditemukan atau tidak diotorisasi'], 401);
            }
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }
    }


    public function updateHafalan(Request $request, $id_hafalan)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'surah' => 'required|string',
            'jumlah_ayat' => 'required|integer',
            'nilai' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        // Pastikan pengguna adalah ustadz
        if ($user && $user->role === 'ust_pondok') {
            // Cek apakah Hafalan dengan `id_hafalan` yang dimaksud ada
            $hafalan = Hafalan::find($id_hafalan);
            // $santri = User::where('id', $id_santri)->first();
            // dd($namaSantri);

            if ($hafalan) {
                // $namaSantri = $santri->name;
                // Periksa apakah `id_ustadz` dalam permintaan cocok dengan `id_ustadz` dari pengguna yang masuk
                if ($user->id_ustadz == $hafalan->id_ustadz) {
                    $hafalan->tanggal = $request->tanggal;
                    $hafalan->surah = $request->surah;
                    $hafalan->jumlah_ayat = $request->jumlah_ayat;
                    $hafalan->nilai = $request->nilai;
                    $hafalan->save();

                    $santri = User::where('id_santri', $hafalan->id_santri)->first();
                    if ($santri) {
                        $namaSantri = $santri->name;
                    } else {
                        $namaSantri = 'Santri tidak ditemukan'; // Atau apa yang sesuai dengan logika aplikasi Anda
                    }


                    // Dapatkan nama santri
                    return response()->json(['message' => 'Hafalan santri berhasil diperbarui', 'santri' => $namaSantri, 'hafalan' => $hafalan], 200);
                } else {
                    return response()->json(['error' => 'Tidak Diotorisasi'], 401);
                }
            } else {
                return response()->json(['error' => 'Data Hafalan tidak ditemukan'], 404);
            }
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }
    }



    public function deleteHafalan($id_hafalan)
    {
        $user = Auth::user();

        // Ensure the user is an 'ust_pondok'
        if (!$user || $user->role !== 'ust_pondok') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        // Find and delete the AmalSholeh based on id_amal
        $amalSholeh = Hafalan::where('id_hafalan', $id_hafalan)
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


    public function indexHafalan()
    {
        $user = Auth::user();

        // Pastikan pengguna adalah ustadz
        if ($user && $user->role === 'ust_pondok') {
            // Ambil semua Hafalan yang dimiliki oleh ustadz ini
            $hafalan = Hafalan::where('id_ustadz', $user->id_ustadz)->get();
            return response()->json(['hafalan' => $hafalan], 200);
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }
    }



}
