<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\AmalSholehController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriAbsensiController;
use App\Http\Controllers\CategoriAmanahController;
use App\Http\Controllers\CategoriDivisiController;
use App\Http\Controllers\CategoriKegiatanController;
use App\Http\Controllers\HafalanController;
use App\Http\Controllers\KemampuanController;
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

Route::middleware(['auth:api', 'role:admin_pondok,ust_pondok'])->group(function () {
// get berdasarkan Provinsi
    Route::get('getSantriByProvinsi', [SantriController::class, 'getSantriByProvinsi']);
// Update Sebagian penting dari santri
    Route::put('updateSantri/{id_santri}', [SantriController::class, 'updateSantri']);

});

Route::middleware(['auth:api', 'role:admin_pondok,ust_pondok,ust_pondok'])->group(function () {
// update role
    Route::put('updateRole', [AuthController::class, 'updateRole']);

});


Route::middleware(['auth:api', 'role:admin_pondok,ust_pondok,santri_pondok'])->group(function () {
// Update inputan admin untuk admin_pondok
    Route::get('indexamalsholeh', [AmalSholehController::class, 'index']);
// get Hafalan
    Route::get('indexHafalan', [HafalanController::class, 'indexHafalan']);
// index Categori
    Route::get('IndexAbsen', [CategoriAbsensiController::class, 'index']);
    Route::get('IndexAmanah', [CategoriAmanahController::class, 'index']);
    Route::get('IndexDevisi', [CategoriDivisiController::class, 'index']);
    Route::get('Indexkegiatan', [CategoriKegiatanController::class, 'index']);
// get absen berdasarkan tgl Broken
    Route::get('/count-categories/{id_santri}/{date}/{interval}', [AbsenController::class, 'countCategoriesByActivityAndDate']);
// get santri berdasarkan angkatan
    Route::get('getSantriByAngkatan', [SantriController::class, 'getSantriByAngkatan']);






});


Route::middleware(['auth:api', 'role:admin_pondok,admin_pusat'])->group(function () {
// admin_pusat dan admin_pondok bisa update
    Route::put('updateProfile/{id}',[AuthController::class,'updateProfile']);
    Route::get('indexprofileadminPondok',[AuthController::class,'indexprofileadminPondok']);

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
    Route::post('Create/kategori', [CategoriAbsensiController::class, 'Createcategori']);
    Route::put('Updatecategori/{id}', [CategoriAbsensiController::class, 'update']);
    Route::delete('Deletecategori/{id}', [CategoriAbsensiController::class, 'destroy']);

// Categori Amanah
    Route::post('CreateAmanah', [CategoriAmanahController::class, 'create']);
    Route::put('UpdateAmanah/{id}', [CategoriAmanahController::class, 'update']);
    Route::delete('DeleteAmanah/{id}', [CategoriAmanahController::class, 'destroy']);

// Categori Kegiatan
    Route::post('Createkegiatan', [CategoriKegiatanController::class, 'create']);
    Route::put('Updatekegiatan/{id}', [CategoriKegiatanController::class, 'update']);
    Route::delete('Deletekegiatan/{id}', [CategoriKegiatanController::class, 'destroy']);

// Categori Devisi
    Route::post('CreateDevisi', [CategoriDivisiController::class, 'create']);
    Route::put('UpdateDevisi/{id}', [CategoriDivisiController::class, 'update']);
    Route::delete('DeleteDevisi/{id}', [CategoriDivisiController::class, 'destroy']);





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
    Route::put('updateAbsen/{id_absen}', [AbsenController::class, 'updateAbsen']);
    Route::delete('deleteAbsen/{id_absen}', [AbsenController::class, 'deleteAbsen']);



// CRUD KEMAMPUAN
    Route::post('createKemampuan/{id_santri}', [KemampuanController::class, 'createKemampuan']);
    Route::put('updateKemampuan/{id_santri}', [KemampuanController::class, 'updateKemampuan']);





});


Route::middleware(['auth:api', 'role:santri_pondok'])->group(function () {
// BELIM DI COBA
    Route::put('updateFundraising/{id_amal}', [SantriController::class, 'updateFundraising']);
// Update Profile
    Route::put('updateProfileSantri/{id_santri}', [SantriController::class, 'updateProfileSantri']);


});


Route::middleware(['auth:api', 'role:staff_ust'])->group(function () {


});


// Route::middleware(['auth:api', 'role:staff_pondok'])->group(function () {


// });


// Route::middleware(['auth:api', 'role:staff_pusat'])->group(function () {


// });








