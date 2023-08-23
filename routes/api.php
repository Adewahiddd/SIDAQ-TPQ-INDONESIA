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


Route::middleware(['auth:api', 'role:admin_pusat'])->group(function () {
//    index user
    Route::get('index',[AuthController::class,'index']);
    // Verifikasi User Register
    Route::post('process-user-verification/{id}', [AuthController::class, 'processUserVerification']);
    // Get User Registar
    Route::post('UserById/{id}',[AuthController::class,'getUserById']);


});

Route::middleware(['auth:api', 'role:staff_pusat'])->group(function () {


});

Route::middleware(['auth:api', 'role:admin_pondok'])->group(function () {
// Registrasi ustadz
    Route::post('registerguru',[GuruController::class,'registerGuru']);

});

Route::middleware(['auth:api', 'role:ust_pondok'])->group(function () {
    // Add Santri
    Route::post('add-santri', [SantriController::class, 'AddSantri']);
});

Route::middleware(['auth:api', 'role:santri_pondok'])->group(function () {


});

Route::middleware(['auth:api', 'role:staff_pondok'])->group(function () {


});





// COBA KIRIM EMAIL MANUAL
// Route::post('/test-email', function () {
//     $user = User::find(3); // Ganti dengan ID user yang sesuai
//     Notification::send($user, new RegistrationRejectedNotification($user));
//     return "Email sent successfully.";
// });




