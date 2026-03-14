<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\PackageManagementController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Route::inertia('/', 'Welcome', [
//     'canRegister' => Features::enabled(Features::registration()),
// ])->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });



Route::prefix('admin')->group(function () {
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [LoginController::class, 'index'])->name('admin.login');
        Route::post('/login', [LoginController::class, 'store'])->name('admin.login.store');
    });

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        //
        Route::get('/packages', [PackageManagementController::class, 'index'])->name('admin.packages');
        //
        Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    });
});


require __DIR__ . '/settings.php';
