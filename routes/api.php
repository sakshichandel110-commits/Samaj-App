<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello, World!'
    ]);
});

Route::post('/auth/login', [RegistrationController::class, 'login']);
Route::post('/auth/verify-otp', [RegistrationController::class, 'verifyOtp']);
Route::post('/auth/google/exchange', [RegistrationController::class, 'exchangeGoogleCode']);
Route::post('/auth/profile', [RegistrationController::class, 'updateProfile'])->middleware('auth:sanctum');

