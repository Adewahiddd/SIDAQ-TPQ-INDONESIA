<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\CategoriAbsensi;
use App\Models\CategoriKegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AbsenController extends Controller
{
//BROKEN
    public function createAbsen(Request $request, $id_santri)
    {
        $validator = Validator::make($request->all(), [
            'waktu' => 'required',
            'keterangan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $id_ustadz = auth()->user()->id_ustadz;
        // Cari santri pondok dengan $id_santri
        $santri = User::where('id_santri', $id_santri)->first();

        // Pastikan hanya peran 'ust_pondok' yang dapat melakukan pengisian absen
        if (auth()->user()->role !== 'ust_pondok') {
            return response()->json(['error' => 'Only ust_pondok role is allowed to create absen'], 403);
        }

        if ($santri) {
            // Dapatkan kategori kegiatan berdasarkan nama kegiatan yang dikirim dalam permintaan
            $categoriKegiatan = CategoriKegiatan::where('kegiatan', $request->input('waktu'))->first();
            // Ambil data dari tabel CategoriAbsen berdasarkan kategori
            $categoriAbsen = CategoriAbsensi::where('kategori', $request->input('keterangan'))->first();

            if ($categoriKegiatan && $categoriAbsen) {
                // Membuat entri absen baru
                $absen = Absen::create([
                    'id_ustadz' => $id_ustadz,
                    'id_santri' => $id_santri,
                    'waktu' => $categoriKegiatan->kegiatan, // Perbaikan di sini
                    'keterangan' => $categoriAbsen->keterangan,
                ]);

                $absen->save();

                $message = 'Absen santri successfully created';
            } else {
                return response()->json(['error' => 'CategoriKegiatan or CategoriAbsen not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Santri not found'], 404);
        }

        return response()->json(['message' => $message, 'absen' => $absen], 200);
    }


    public function countSantriByIndex(Request $request)
    {
        // Ambil index waktu dan keterangan dari request
        $indexWaktu = $request->input('waktu');
        $indexKeterangan = $request->input('keterangan');

        // Hitung jumlah keterangan persantri berdasarkan waktu
        $jumlahKeterangan = Absen::join('categori_kegiatan', 'absen.waktu', '=', 'categori_kegiatan.kegiatan')
            ->join('categori_absensi', 'absen.keterangan', '=', 'categori_absensi.keterangan')
            ->where('categori_kegiatan.waktu', $indexWaktu)
            ->count();

        return response()->json(['jumlah Keterangan' => $jumlahKeterangan], 200);
    }





}
