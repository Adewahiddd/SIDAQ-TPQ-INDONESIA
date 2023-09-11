<?php

namespace App\Http\Controllers;

use App\Models\Kemampuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KemampuanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pastikan pengguna adalah admin_pondok, ust_pondok, atau santri_pondok
        if ($user && in_array($user->role, ['admin_pondok', 'ust_pondok', 'santri_pondok'])) {
            // Jika pengguna adalah admin_pondok, mereka bisa melihat semua kemampuan santri
            if ($user->role === 'admin_pondok') {
                $kemampuans = Kemampuan::all();
            } else {
                // Jika pengguna adalah ust_pondok atau santri_pondok, mereka hanya bisa melihat kemampuan mereka sendiri
                $kemampuans = Kemampuan::where('id_santri', $user->id_santri)->get();
            }

            return response()->json(['kemampuans' => $kemampuans], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    public function createKemampuan(Request $request, $id_santri)
    {
        $validator = Validator::make($request->all(), [
            'khidmat' => 'required|integer',
            'leadership' => 'required|integer',
            'enterpreneur' => 'required|integer',
            'speaking' => 'required|integer',
            'operation' => 'required|integer',
            'mengajar' => 'required|integer',
            'admiristation' => 'required|integer',
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
                $kemampuan = Kemampuan::create([
                    'id_ustadz' => $user->id_ustadz,
                    'id_santri' => $id_santri,
                    'khidmat' => $request->khidmat,
                    'leadership' => $request->leadership,
                    'enterpreneur' => $request->enterpreneur,
                    'speaking' => $request->speaking,
                    'operation' => $request->operation,
                    'mengajar' => $request->mengajar,
                    'admiristation' => $request->admiristation,
                ]);

                return response()->json(['user' => $kemampuan], 200);
            } else {
                return response()->json(['error' => 'Santri not found or not registered by the Ustadz'], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }


    public function updateKemampuan(Request $request, $id_santri)
    {
        $validator = Validator::make($request->all(), [
            'khidmat' => 'required|integer',
            'leadership' => 'required|integer',
            'enterpreneur' => 'required|integer',
            'speaking' => 'required|integer',
            'operation' => 'required|integer',
            'mengajar' => 'required|integer',
            'admiristation' => 'required|integer',
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
                // Temukan atau buat data kemampuan yang sudah ada untuk santri ini
                $kemampuan = Kemampuan::firstOrNew([
                    'id_ustadz' => $user->id_ustadz,
                    'id_santri' => $id_santri,
                ]);

                // Update nilai kemampuan dengan yang baru
                $kemampuan->khidmat = $request->khidmat;
                $kemampuan->leadership = $request->leadership;
                $kemampuan->enterpreneur = $request->enterpreneur;
                $kemampuan->speaking = $request->speaking;
                $kemampuan->operation = $request->operation;
                $kemampuan->mengajar = $request->mengajar;
                $kemampuan->admiristation = $request->admiristation;

                // Simpan perubahan
                $kemampuan->save();

                return response()->json(['user' => $kemampuan], 200);
            } else {
                return response()->json(['error' => 'Santri not found or not registered by the Ustadz'], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }


    public function delete($id_kemampuan)
    {
        $user = Auth::user();

        // Pastikan pengguna adalah ustadz
        if ($user && $user->role === 'ust_pondok') {
            // Temukan kemampuan yang akan dihapus
            $kemampuan = Kemampuan::find($id_kemampuan);

            if ($kemampuan) {
                // Hapus kemampuan jika ditemukan dan sesuai dengan ustadz
                if ($kemampuan->id_ustadz === $user->id_ustadz) {
                    $kemampuan->delete();

                    return response()->json(['message' => 'Kemampuan santri deleted successfully'], 200);
                } else {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            } else {
                return response()->json(['error' => 'Kemampuan not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }



}
