<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SantriController;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RegistrationRejectedNotification;
use App\Models\User;

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


Route::middleware(['auth:api'])->group(function () {
    // Update inputan admin untuk admin_pondok
    Route::middleware('role:admin_pondok')->post('updateAdmin/{id}',[AuthController::class,'updateAdmin']);
    
    // Update inputan admin untuk admin_pusat
    Route::middleware('role:admin_pusat')->post('updateAdmin/{id}',[AuthController::class,'updateAdmin']);
});


Route::middleware(['auth:api', 'role:admin_pusat'])->group(function () {
// update role untuk menjadikannya admin_pusat
    Route::post('updateRole/{id}',[AuthController::class,'updateRole']);
// Verifikasi User Register
    Route::post('process-user-verification/{id}', [AuthController::class, 'processUserVerification']);
// Get User Registar
    Route::get('getVerifiedUsers',[AuthController::class,'getVerifiedUsers']);
    Route::get('getNotVerifiedUsers',[AuthController::class,'getNotVerifiedUsers']);



});

Route::middleware(['auth:api', 'role:staff_pusat'])->group(function () {

});

Route::middleware(['auth:api', 'role:admin_pondok'])->group(function () {
// register guru oleh admin pondok
    Route::post('registerguru',[GuruController::class,'registerGuru']);
    Route::get('index-guru',[GuruController::class,'index']);



});


Route::middleware(['auth:api-guru', 'role:ust_pondok'])->group(function () {
    // registrasi santri oleh ustadz 
    Route::post('registersantri/{id}', [SantriController::class, 'AddSantri']);
    // get santri by id_ust
    Route::get('index-santri',[SantriController::class,'index']);

});

Route::middleware(['auth:api-santri', 'role:santri_pondok'])->group(function () {
    // upadte Fundraising
    Route::post('updatesantri/{id}', [SantriController::class, 'updateSantri']);
    Route::post('updateprofile/{id}', [SantriController::class, 'updateProfile']);
    Route::get('getsantri/{id}', [SantriController::class, 'getProfile']);

});

Route::middleware(['auth:api', 'role:staff_pondok'])->group(function () {


});





// COBA KIRIM EMAIL MANUAL
// Route::post('/test-email', function () {
//     $user = User::find(3); // Ganti dengan ID user yang sesuai
//     Notification::send($user, new RegistrationRejectedNotification($user));
//     return "Email sent successfully.";
// });




