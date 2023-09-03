<?php

namespace App\Http\Controllers;

use App\Models\Kemampuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KemampuanController extends Controller
{
    public function createamalsisholeh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hafalan' => 'required|string',
            'mutqin' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'fundraising' => 'required|string',
            'amanah' => 'required|string',
            'kedisiplinan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();
        $ustadzId = $user->id_ustadz;

        $amalSholeh = Kemampuan::create([
            'id_ustadz' => $ustadzId,
            'id_santri' => $user->id_santri,
            'hafalan' => $request->hafalan,
            'mutqin' => $request->mutqin,
            'fundraising' => $request->fundraising,
            'amanah' => $request->amanah,
            'kedisiplinan' => $request->kedisiplinan,
        ]);

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');

            if (!$gambar->isValid()) {
                return response()->json(['error' => 'Invalid image file'], 400);
            }

            $gambarPath = 'images/poto-fundraising/' . $amalSholeh->id_amal . '.' . $gambar->getClientOriginalExtension();
            $gambar->move(public_path('images/poto-fundraising'), $gambarPath);
            $amalSholeh->gambar = $gambarPath;
        }

        $amalSholeh->save();

        return response()->json(['user' => $amalSholeh], 200);

    }
}
