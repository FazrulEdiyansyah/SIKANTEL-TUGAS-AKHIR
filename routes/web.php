<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:pengelola'])->group(function () {
    Route::get('/pengelola/dashboard', [\App\Http\Controllers\PengelolaController::class, 'dashboard'])->name('pengelola.dashboard');
    Route::get('/pengelola/kantin', [\App\Http\Controllers\KantinController::class, 'index'])->name('pengelola.kantin.index');
    Route::get('/pengelola/kantin/create', [\App\Http\Controllers\KantinController::class, 'create'])->name('pengelola.kantin.create');
    Route::post('/pengelola/kantin', [\App\Http\Controllers\KantinController::class, 'store'])->name('pengelola.kantin.store');
    Route::get('/pengelola/kantin/{kantin}', [\App\Http\Controllers\KantinController::class, 'show'])->name('pengelola.kantin.show');
    
    // Rute Pengelola Tenant
    Route::get('/pengelola/tenant', [\App\Http\Controllers\TenantController::class, 'index'])->name('pengelola.tenant.index');
    Route::get('/pengelola/tenant/create', [\App\Http\Controllers\TenantController::class, 'create'])->name('pengelola.tenant.create');
    Route::post('/pengelola/tenant', [\App\Http\Controllers\TenantController::class, 'store'])->name('pengelola.tenant.store');
    Route::get('/pengelola/tenant/{tenant}', [\App\Http\Controllers\TenantController::class, 'show'])->name('pengelola.tenant.show');
    Route::get('/pengelola/tenant/{tenant}/edit', [\App\Http\Controllers\TenantController::class, 'edit'])->name('pengelola.tenant.edit');
    Route::put('/pengelola/tenant/{tenant}', [\App\Http\Controllers\TenantController::class, 'update'])->name('pengelola.tenant.update');
});

Route::middleware(['auth', 'role:tenant'])->group(function () {
    Route::get('/tenant/dashboard', function () {
        return view('tenant.dashboard');
    });

    // Menu Management untuk Tenant
    Route::resource('/tenant/menu', \App\Http\Controllers\Tenant\MenuController::class, [
        'as' => 'tenant'
    ]);
    Route::patch('/tenant/menu/{menu}/toggle-status', [\App\Http\Controllers\Tenant\MenuController::class, 'toggleStatus'])->name('tenant.menu.toggle-status');
});

Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pelanggan/dashboard', [\App\Http\Controllers\PelangganController::class, 'dashboard'])->name('pelanggan.dashboard');
    Route::get('/pelanggan/kantin/{kantin}', [\App\Http\Controllers\PelangganController::class, 'showKantin'])->name('pelanggan.kantin.show');
    Route::get('/pelanggan/tenant/{tenant}', [\App\Http\Controllers\PelangganController::class, 'showTenant'])->name('pelanggan.tenant.show');
});
