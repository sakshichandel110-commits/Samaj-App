<?php

use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Err404;
use App\Http\Livewire\Err500;
use App\Http\Livewire\ForgotPassword;
use App\Http\Livewire\ResetPassword;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\CommunityController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\MemberController;
use App\Http\Controllers\Web\AnnouncementController;
use App\Http\Controllers\Web\MatrimonyPostController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

Route::get('/register', Register::class)->name('register');
Route::get('/login', Login::class)->name('login');
Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
Route::get('/reset-password/{id}', ResetPassword::class)->name('reset-password')->middleware('signed');

Route::get('/404', Err404::class)->name('404');
Route::get('/500', Err500::class)->name('500');

// Profile (auth only)
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ─── Admin CRUD Panel ────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // ── Communities ──────────────────────────────────────────────────────────
    Route::get('/communities',              [CommunityController::class, 'index'])  ->name('communities.index');
    Route::get('/communities/create',       [CommunityController::class, 'create']) ->name('communities.create');
    Route::post('/communities',             [CommunityController::class, 'store'])  ->name('communities.store');
    Route::get('/communities/{id}',         [CommunityController::class, 'show'])   ->name('communities.show');
    Route::get('/communities/{id}/edit',    [CommunityController::class, 'edit'])   ->name('communities.edit');
    Route::put('/communities/{id}',         [CommunityController::class, 'update']) ->name('communities.update');
    Route::delete('/communities/{id}',      [CommunityController::class, 'destroy'])->name('communities.destroy');

    // ── Users ────────────────────────────────────────────────────────────────
    Route::get('/users',                 [UserController::class, 'index'])        ->name('users.index');
    Route::get('/users/{id}',            [UserController::class, 'show'])         ->name('users.show');
    Route::get('/users/{id}/edit',       [UserController::class, 'edit'])         ->name('users.edit');
    Route::put('/users/{id}',            [UserController::class, 'update'])       ->name('users.update');
    Route::post('/users/{id}/status',    [UserController::class, 'updateStatus']) ->name('users.update-status');
    Route::delete('/users/{id}',         [UserController::class, 'destroy'])      ->name('users.destroy');

    // ── Members ──────────────────────────────────────────────────────────────
    Route::get('/members',              [MemberController::class, 'index'])       ->name('members.index');
    Route::get('/members/create',       [MemberController::class, 'create'])      ->name('members.create');
    Route::post('/members',             [MemberController::class, 'store'])       ->name('members.store');
    Route::get('/members/{id}',         [MemberController::class, 'show'])        ->name('members.show');
    Route::get('/members/{id}/edit',    [MemberController::class, 'edit'])        ->name('members.edit');
    Route::put('/members/{id}',         [MemberController::class, 'update'])      ->name('members.update');
    Route::post('/members/{id}/status', [MemberController::class, 'updateStatus'])->name('members.update-status');
    Route::delete('/members/{id}',      [MemberController::class, 'destroy'])     ->name('members.destroy');

    // ── Announcements ────────────────────────────────────────────────────────
    Route::get('/announcements',             [AnnouncementController::class, 'index'])  ->name('announcements.index');
    Route::get('/announcements/create',      [AnnouncementController::class, 'create']) ->name('announcements.create');
    Route::post('/announcements',            [AnnouncementController::class, 'store'])  ->name('announcements.store');
    Route::get('/announcements/{id}',        [AnnouncementController::class, 'show'])   ->name('announcements.show');
    Route::get('/announcements/{id}/edit',   [AnnouncementController::class, 'edit'])   ->name('announcements.edit');
    Route::put('/announcements/{id}',        [AnnouncementController::class, 'update']) ->name('announcements.update');
    Route::delete('/announcements/{id}',     [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // ── Matrimony Posts ──────────────────────────────────────────────────────
    Route::get('/matrimony-posts',             [MatrimonyPostController::class, 'index'])  ->name('matrimony-posts.index');
    Route::get('/matrimony-posts/create',      [MatrimonyPostController::class, 'create']) ->name('matrimony-posts.create');
    Route::post('/matrimony-posts',            [MatrimonyPostController::class, 'store'])  ->name('matrimony-posts.store');
    Route::get('/matrimony-posts/{id}',        [MatrimonyPostController::class, 'show'])   ->name('matrimony-posts.show');
    Route::get('/matrimony-posts/{id}/edit',   [MatrimonyPostController::class, 'edit'])   ->name('matrimony-posts.edit');
    Route::put('/matrimony-posts/{id}',        [MatrimonyPostController::class, 'update']) ->name('matrimony-posts.update');
    Route::delete('/matrimony-posts/{id}',     [MatrimonyPostController::class, 'destroy'])->name('matrimony-posts.destroy');
});

Route::get('/google-callback', function (Request $request) {
    return response()->json([
        'code'  => $request->query('code'),
        'error' => $request->query('error'),
    ]);
});