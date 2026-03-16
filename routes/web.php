<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Client\LandingController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\PackageManagementController;
use App\Http\Controllers\Client\BookingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::inertia('/', function(){
//     return Inertia::render('admin/auth/Login');
// })->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });


Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/booking/{package}', [BookingController::class, 'index'])->name('booking');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{booking}/success', [BookingController::class, 'success'])->name('booking.success');



Route::prefix('admin')->group(function () {
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [LoginController::class, 'index'])->name('admin.login');
        Route::post('/login', [LoginController::class, 'store'])->name('admin.login.store');
    });

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        //
        Route::get('/packages', [PackageManagementController::class, 'index'])->name('admin.packages');
        Route::get('/packages/create', [PackageManagementController::class, 'create'])->name('admin.packages.create');
        Route::post('/packages/store', [PackageManagementController::class, 'store'])->name('admin.packages.store');
        Route::get('/packages/{package}/show', [PackageManagementController::class, 'show'])->name('admin.packages.show');
        Route::get('/packages/{package}/edit', [PackageManagementController::class, 'edit'])->name('admin.packages.edit');
        Route::put('/packages/{package}/update', [PackageManagementController::class, 'update'])->name('admin.packages.update');
        Route::delete('/packages/{package}/destroy', [PackageManagementController::class, 'destroy'])->name('admin.packages.destroy');
        //
        Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    });
});


require __DIR__ . '/settings.php';
