<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OtpController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/verify', [AuthController::class, 'verifyOTP']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/otp/send', [OtpController::class, 'sendOTP']);
Route::post('/otp/verify', [OTPController::class, 'verifyOTP']);
Route::post('/otp/change', [OTPController::class, 'changePassword']);
Route::post('/otp/resend', [OTPController::class, 'resendOTP']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
});
