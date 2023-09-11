<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\CategoriAbsensi;
use App\Models\CategoriKegiatan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AbsenController extends Controller
{
    public function createAbsen(Request $request, $id_santri)
    {
        $validator = Validator::make($request->all(), [
            'name_kegiatan' => 'required|exists:categori_kegiatans,name_kegiatan',
            'name_kategori' => 'required|exists:categori_absensis,name_kategori',
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

            $nameKegiatan = $request->input('name_kegiatan');
            $kegiatanEntry = CategoriKegiatan::where('name_kegiatan', $nameKegiatan)->first();
            $nameKategori = $request->input('name_kategori');
            $kategoriEntry = CategoriAbsensi::where('name_kategori', $nameKategori)->first();

            if (!$kegiatanEntry || !$kategoriEntry) {
                return response()->json(['error' => 'CategoriKegiatan or CategoriAbsensi not found'], 404);
            }

            // Buat catatan 'Absen' baru
            $absen = new Absen;
            $absen->id_ustadz = $user->id_ustadz;
            $absen->id_santri = $id_santri;
            $absen->name_kegiatan = $nameKegiatan;
            $absen->name_kategori = $nameKategori;
            $absen->save();

            $message = 'Absen santri successfully created';
        } else {
            return response()->json(['error' => 'Tidak Diotorisasi'], 401);
        }

        return response()->json(['message' => $message, 'absen' => $absen], 200);
    }

    public function updateAbsen(Request $request, $id_absen)
    {
        $validator = Validator::make($request->all(), [
            'name_kegiatan' => 'required|exists:categori_kegiatans,name_kegiatan',
            'name_kategori' => 'required|exists:categori_absensis,name_kategori',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Cari catatan 'Absen' berdasarkan 'id_absen'
        $absen = Absen::find($id_absen);

        if (!$absen) {
            return response()->json(['error' => 'Absen not found'], 404);
        }

        $nameKegiatan = $request->input('name_kegiatan');
        $nameKategori = $request->input('name_kategori');

        // Temukan kategori kegiatan dan kategori absensi berdasarkan nama
        $kegiatanEntry = CategoriKegiatan::where('name_kegiatan', $nameKegiatan)->first();
        $kategoriEntry = CategoriAbsensi::where('name_kategori', $nameKategori)->first();

        if (!$kegiatanEntry || !$kategoriEntry) {
            return response()->json(['error' => 'CategoriKegiatan or CategoriAbsen not found'], 404);
        }

        // Update data 'Absen'
        $absen->name_kegiatan = $nameKegiatan;
        $absen->name_kategori = $nameKategori;
        $absen->save();

        $message = 'Absen santri successfully updated';

        return response()->json(['message' => $message, 'absen' => $absen], 200);
    }


    public function deleteAbsen(Request $request, $id_absen)
    {
        // Cari catatan 'Absen' berdasarkan 'id_absen'
        $absen = Absen::find($id_absen);

        if (!$absen) {
            return response()->json(['error' => 'Absen not found'], 404);
        }

        // Hapus catatan 'Absen'
        $absen->delete();

        $message = 'Absen santri successfully deleted';

        return response()->json(['message' => $message], 200);
    }


//
    public function countCategoriesByActivityAndDate($id_santri, $date, $interval)
    {
        // Inisialisasi Carbon berdasarkan tanggal yang diberikan
        $carbonDate = Carbon::parse($date);

        // Atur interval berdasarkan parameter yang diberikan
        if ($interval === 'day') {
            // Hitung berapa kali 'name_kategori' muncul pada tanggal tersebut
            $result = Absen::where('id_santri', $id_santri)
                ->whereDate('created_at', $carbonDate)
                ->groupBy('name_kategori')
                ->select('name_kategori', DB::raw('count(*) as total'))
                ->get();
        } elseif ($interval === 'week') {
            // Hitung berapa kali 'name_kategori' muncul dalam seminggu
            $result = Absen::where('id_santri', $id_santri)
                ->whereBetween('created_at', [$carbonDate->startOfWeek(), $carbonDate->endOfWeek()])
                ->groupBy('name_kategori')
                ->select('name_kategori', DB::raw('count(*) as total'))
                ->get();
        } elseif ($interval === 'month') {
            // Hitung berapa kali 'name_kategori' muncul dalam bulan ini
            $result = Absen::where('id_santri', $id_santri)
                ->whereYear('created_at', $carbonDate->year)
                ->whereMonth('created_at', $carbonDate->month)
                ->groupBy('name_kategori')
                ->select('name_kategori', DB::raw('count(*) as total'))
                ->get();
        } elseif ($interval === 'year') {
            // Hitung berapa kali 'name_kategori' muncul dalam tahun ini
            $result = Absen::where('id_santri', $id_santri)
                ->whereYear('created_at', $carbonDate->year)
                ->groupBy('name_kategori')
                ->select('name_kategori', DB::raw('count(*) as total'))
                ->get();
        } else {
            return response()->json(['error' => 'Interval tidak valid'], 400);
        }

        // Memastikan semua kategori yang diharapkan termasuk dalam hasil query
        $expectedCategories = ['sakit', 'izin', 'hadir', 'lain-lain']; // Sesuaikan dengan kategori yang Anda miliki
        $resultCategories = $result->pluck('name_kategori')->toArray();

        // Cek setiap kategori yang diharapkan
        foreach ($expectedCategories as $category) {
            if (!in_array($category, $resultCategories)) {
                // Jika kategori tidak ada dalam hasil, tambahkan dengan total 0
                $result->push(['name_kategori' => $category, 'total' => 0]);
            }
        }

        return response()->json(['result' => $result]);
    }













}
