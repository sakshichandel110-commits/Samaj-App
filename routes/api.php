<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\AdminController;

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

// Members API (authenticated)
Route::post('/verify-members', [MemberController::class, 'store'])->middleware('auth:sanctum');

// Admin: create community
Route::post('/communities', [AdminController::class, 'storeCommunity'])->middleware('auth:sanctum');
// Join community by code
Route::post('/communities/join', [MemberController::class, 'joinCommunity'])->middleware('auth:sanctum');

// Matrimony posts
Route::post('/posts', [MemberController::class, 'createPost'])->middleware('auth:sanctum');
Route::post('/posts/react', [MemberController::class, 'reactToPost'])->middleware('auth:sanctum');
Route::post('/announcements', [MemberController::class, 'createAnnouncement'])->middleware('auth:sanctum');
Route::post('/announcements/react', [MemberController::class, 'reactToAnnouncement'])->middleware('auth:sanctum');
Route::get('/posts/list', [MemberController::class, 'listPosts'])->middleware('auth:sanctum');
Route::get('/announcements/list', [MemberController::class, 'listAnnouncements'])->middleware('auth:sanctum');

// Admin verification for members
Route::post('/admin/verify-member', [AdminController::class, 'verifyMember'])->middleware(['auth:sanctum','admin']);

