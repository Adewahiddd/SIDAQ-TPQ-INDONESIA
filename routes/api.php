<?php

use App\Http\Controllers\AmalSholehController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\UstadzController;
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


Route::middleware(['auth:api', 'role:admin_pusat,ust_pondok,santri_pondok'])->group(function () {
    // Update inputan admin untuk admin_pondok
    Route::get('indexamalsholeh', [AmalSholehController::class, 'index']);

});



Route::middleware(['auth:api', 'role:admin_pusat'])->group(function () {
    // Verifikasi User Register
    Route::post('process-user-verification/{id}', [AuthController::class, 'processUserVerification']);
    // Get User Registar
    Route::get('getVerifiedUsers',[AuthController::class,'getVerifiedUsers']);
    Route::get('getNotVerifiedUsers',[AuthController::class,'getNotVerifiedUsers']);
// get profile masjid dan jumlah
    Route::get('indexprofile',[AuthController::class,'index']);
    Route::get('getVerifiedAdminMasjidCount',[AuthController::class,'getVerifiedAdminMasjidCount']);
// update
    Route::post('updateProfile/{id}',[AuthController::class,'updateProfile']);



});


Route::middleware(['auth:api', 'role:admin_pondok'])->group(function () {
// nge-registerin ustadz
    Route::post('register/ustadz',[UstadzController::class,'registerustadz']);
// update profile admin
    Route::post('updateProfile/{id}',[AuthController::class,'updateProfile']);
// get profile masjid/pondok
    Route::get('indexprofileadmin',[AuthController::class,'indexprofileadmin']);
// get semua Ustadz pondok
    Route::get('getUstadzByAdminId/{id}',[UstadzController::class,'getUstadzByAdminId']);
    Route::get('getTotalUstadzByAdminId/{id}',[UstadzController::class,'getTotalUstadzByAdminId']);
// get semua santri pondok
    Route::get('getSantriByAdminId/{id}',[SantriController::class,'getSantriByAdminId']);
    Route::get('getTotalSantriByAdminId/{id}',[UstadzController::class,'getTotalSantriByAdminId']);





});


Route::middleware(['auth:api', 'role:ust_pondok'])->group(function () {
// nge-registerin Santri
    Route::post('register/santri',[SantriController::class,'registersantri']);
// CRUD AMAL SHOLEH
    Route::post('createamalsisholeh/{id}',[AmalSholehController::class,'createAmalSholeh']);

    // Route::get('amalsholeh', [AmalSholehController::class, 'index']);
    Route::post('delete/{id}', [AmalSholehController::class, 'deleteAmalSholeh']);


});


Route::middleware(['auth:api', 'role:staff_ust'])->group(function () {


});


Route::middleware(['auth:api', 'role:santri_pondok'])->group(function () {


});


Route::middleware(['auth:api', 'role:staff_pondok'])->group(function () {


});


Route::middleware(['auth:api', 'role:staff_pusat'])->group(function () {


});








