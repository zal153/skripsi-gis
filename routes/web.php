<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DesaController;
use App\Http\Controllers\Admin\JalanController;
use App\Http\Controllers\Admin\PosyanduController;
use App\Http\Controllers\Admin\TitikJalanController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }

    return app(LandingPageController::class)();
});

Route::prefix('api')->group(function () {
    Route::get('/route', [RouteController::class, 'calculate'])->name('api.route');

    Route::prefix('v1')->name('api.v1.')->group(function () {
        Route::get('/posyandu', [App\Http\Controllers\Api\V1\PosyanduController::class, 'index'])->name('posyandu.index');
    });
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengujian-mae', [DashboardController::class, 'maeTest'])->name('mae-test');
});

Route::middleware('auth')->get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('posyandu', PosyanduController::class);
    Route::resource('desa', DesaController::class);
    Route::resource('jalan', JalanController::class);
    Route::resource('titik-jalan', TitikJalanController::class);
    Route::resource('akun', AkunController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
