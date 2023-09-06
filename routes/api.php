<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\AmalSholehController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HafalanController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\UstadzController;
use App\Models\CategoriAbsensi;
use App\Models\CategoriAmanah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::post('logout',[AuthController::class,'logout'])->middleware('auth:api');


Route::middleware(['auth:api', 'role:admin_pondok,ust_pondok,santri_pondok'])->group(function () {
// Update inputan admin untuk admin_pondok
    Route::get('indexamalsholeh', [AmalSholehController::class, 'index']);

    Route::get('indexHafalan', [HafalanController::class, 'indexHafalan']);

    Route::get('IndexAbsen', [CategoriAbsensiController::class, 'index']);
    Route::get('IndexAmanah', [CategoriAmanahController::class, 'index']);
    Route::get('IndexDevisi', [CategoriDivisiController::class, 'index']);
    Route::get('Indexkegiatan', [CategoriKegiatanController::class, 'index']);

    Route::get('countSantriByIndex', [AbsenController::class, 'countSantriByIndex']);

});


Route::middleware(['auth:api', 'role:admin_pondok,admin_pusat'])->group(function () {
// admin_pusat dan admin_pondok bisa update
    Route::put('updateProfile/{id}',[AuthController::class,'updateProfile']);

});



Route::middleware(['auth:api', 'role:admin_pusat'])->group(function () {
    // Verifikasi User Register
    Route::post('process-user-verification/{id_admin}', [AuthController::class, 'processUserVerification']);
    // Get User Registar
    Route::get('getVerifiedUsers',[AuthController::class,'getVerifiedUsers']);
    Route::get('getNotVerifiedUsers',[AuthController::class,'getNotVerifiedUsers']);

    Route::get('getVerifiedAdminMasjidCount',[AuthController::class,'getVerifiedAdminMasjidCount']);

// yang bisa melihat hanya admin_pusat get semua nya
    Route::get('indexprofileadminPusat',[AuthController::class,'indexprofileadminPusat']);
// get role admin_pondok
    Route::get('indexprofileadminPondok',[AuthController::class,'indexprofileadminPondok']);

});


Route::middleware(['auth:api', 'role:admin_pondok'])->group(function () {
// nge-registerin ustadz
    Route::post('register/ustadz',[UstadzController::class,'registerustadz']);
    Route::delete('deleteUstadz/{id_ustadz}', [UstadzController::class, 'deleteUstadz']);

// get semua Ustadz pondok
    Route::get('getUstadzByAdminId/{id}',[UstadzController::class,'getUstadzByAdminId']);
    Route::get('getTotalUstadzByAdminId/{id}',[UstadzController::class,'getTotalUstadzByAdminId']);
// get semua santri pondok
    Route::get('getSantriByAdminId/{id}',[SantriController::class,'getSantriByAdminId']);

    Route::get('getTotalSantriByAdminId/{id}',[UstadzController::class,'getTotalSantriByAdminId']);
// Categori Absen
    Route::post('Createcategori', [CategoriAbsensiController::class, 'create']);
    Route::put('Updatecategori', [CategoriAbsensiController::class, 'update']);
    Route::delete('Deletecategori', [CategoriAbsensiController::class, 'destroy']);
// Categori Amanah
    Route::post('Createcategori', [CategoriAmanahController::class, 'create']);
    Route::put('Updatecategori', [CategoriAmanahController::class, 'update']);
    Route::delete('Deletecategori', [CategoriAmanahController::class, 'destroy']);
// Categori Kegiatan
    Route::post('Createkegiatan', [CategoriKegiatanController::class, 'create']);
    Route::put('Updatekegiatan', [CategoriKegiatanController::class, 'update']);
    Route::delete('Deletekegiatan', [CategoriKegiatanController::class, 'destroy']);
// Categori Devisi
    Route::post('CreateDevisi', [CategoriDivisiController::class, 'create']);
    Route::put('UpdateDevisi', [CategoriDivisiController::class, 'update']);
    Route::delete('DeleteDevisi', [CategoriDivisiController::class, 'destroy']);





});


Route::middleware(['auth:api', 'role:ust_pondok'])->group(function () {
// nge-registerin Santri
    Route::post('register/santri',[SantriController::class,'registersantri']);
    Route::delete('deleteSantri/{id_santri}', [SantriController::class, 'deleteSantri']);

// Index Profile ustadz
    Route::get('indexProfileUstadz',[UstadzController::class,'indexProfileUstadz']);
// CRUD AMAL SHOLEH
    Route::post('createamalsisholeh/{id_santri}',[AmalSholehController::class,'createAmalSholeh']);
    Route::put('updateAmalSholeh/{id_amal}',[AmalSholehController::class,'updateAmalSholeh']);
    Route::delete('deleteamal/{id_amal}', [AmalSholehController::class, 'deleteAmalSholeh']);
// CRUD HAFALAN
    Route::post('createHafalan/{id_santri}',[HafalanController::class,'createHafalan']);
    Route::put('updateHafalan/{id_hafalan}',[HafalanController::class,'updateHafalan']);
    Route::delete('deletehafalan/{id_hafalan}', [HafalanController::class, 'deleteHafalan']);
// CRUD ABSEN
    Route::post('createAbsen/{id_santri}', [AbsenController::class, 'createAbsen']);





});


Route::middleware(['auth:api', 'role:santri_pondok'])->group(function () {
    // BELIM DI COBA
    Route::put('updateFundraising/{id_amal}', [SantriController::class, 'updateFundraising']);


});


Route::middleware(['auth:api', 'role:staff_ust'])->group(function () {


});


// Route::middleware(['auth:api', 'role:staff_pondok'])->group(function () {


// });


// Route::middleware(['auth:api', 'role:staff_pusat'])->group(function () {


// });








