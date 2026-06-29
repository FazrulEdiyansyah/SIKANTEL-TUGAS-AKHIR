<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengelolaController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\RekapPenjualanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Pengelola\PencairanDanaController;
use App\Http\Controllers\ApproverController;
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

// Superadmin Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', function () {
        return redirect()->route('superadmin.dashboard');
    });
    Route::get('/superadmin/dashboard', [\App\Http\Controllers\Superadmin\DashboardController::class, 'index'])->name('superadmin.dashboard');
    
    // Users & Roles
    Route::resource('/superadmin/users', \App\Http\Controllers\Superadmin\UserController::class, ['as' => 'superadmin']);
    
    // View other data (Orders only for now, the rest are full CRUD)
    Route::get('/superadmin/orders', [\App\Http\Controllers\Superadmin\DataController::class, 'orders'])->name('superadmin.orders.index');
    Route::get('/superadmin/orders/{order}', [\App\Http\Controllers\Superadmin\DataController::class, 'showOrder'])->name('superadmin.orders.show');
    
    Route::resource('/superadmin/kantin', \App\Http\Controllers\Superadmin\KantinController::class, ['as' => 'superadmin']);
    Route::resource('/superadmin/tenant', \App\Http\Controllers\Superadmin\TenantController::class, ['as' => 'superadmin']);
    
    // Pencairan Dana Superadmin
    Route::prefix('superadmin/pencairan')->name('superadmin.pencairan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'store'])->name('store');
        Route::post('/calculate', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'calculateSales'])->name('calculate');
        Route::get('/preview-pdf', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'generatePdf'])->name('preview_pdf');
        
        Route::get('/{id}', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'show'])->name('show');
        Route::post('/{id}/propose', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'propose'])->name('propose');
        Route::post('/{id}/approve', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'destroy'])->name('destroy');
    });
});

// Rute Kaur
Route::middleware(['auth', 'role:kaur'])->prefix('kaur')->name('kaur.')->group(function () {
    Route::get('/dashboard', [ApproverController::class, 'dashboardKaur'])->name('dashboard');
    Route::get('/pencairan/{id}', [ApproverController::class, 'showPencairan'])->name('pencairan.show');
    Route::get('/pencairan/{id}/pdf', [ApproverController::class, 'generatePdf'])->name('pencairan.pdf');
    Route::post('/pencairan/{id}/approve', [ApproverController::class, 'approveKaur'])->name('pencairan.approve');
    Route::post('/pencairan/{id}/reject', [ApproverController::class, 'rejectKaur'])->name('pencairan.reject');
    
    Route::get('/kantin', [ApproverController::class, 'kantin'])->name('kantin.index');
    Route::get('/tenant', [ApproverController::class, 'tenant'])->name('tenant.index');
    Route::get('/orders', [ApproverController::class, 'orders'])->name('orders.index');
});

// Rute Kabag
Route::middleware(['auth', 'role:kabag'])->prefix('kabag')->name('kabag.')->group(function () {
    Route::get('/dashboard', [ApproverController::class, 'dashboardKabag'])->name('dashboard');
    Route::get('/pencairan/{id}', [ApproverController::class, 'showPencairan'])->name('pencairan.show');
    Route::get('/pencairan/{id}/pdf', [ApproverController::class, 'generatePdf'])->name('pencairan.pdf');
    Route::post('/pencairan/{id}/approve', [ApproverController::class, 'approveKabag'])->name('pencairan.approve');
    Route::post('/pencairan/{id}/reject', [ApproverController::class, 'rejectKabag'])->name('pencairan.reject');

    Route::get('/kantin', [ApproverController::class, 'kantin'])->name('kantin.index');
    Route::get('/tenant', [ApproverController::class, 'tenant'])->name('tenant.index');
    Route::get('/orders', [ApproverController::class, 'orders'])->name('orders.index');
});

Route::middleware(['auth', 'role:pengelola'])->group(function () {
    Route::get('/pengelola/dashboard', [PengelolaController::class, 'dashboard'])->name('pengelola.dashboard');
    Route::get('/pengelola/kantin', [KantinController::class, 'index'])->name('pengelola.kantin.index');
    Route::get('/pengelola/kantin/create', [KantinController::class, 'create'])->name('pengelola.kantin.create');
    Route::post('/pengelola/kantin', [KantinController::class, 'store'])->name('pengelola.kantin.store');
    Route::get('/pengelola/kantin/{kantin}', [KantinController::class, 'show'])->name('pengelola.kantin.show');
    
    // Laporan Pencairan Dana
    Route::prefix('pengelola/pencairan-dana')->name('pengelola.pencairan_dana.')->group(function () {
        Route::get('/', [PencairanDanaController::class, 'index'])->name('index');
        Route::get('/create', [PencairanDanaController::class, 'create'])->name('create');
        Route::post('/', [PencairanDanaController::class, 'store'])->name('store');
        Route::post('/calculate', [PencairanDanaController::class, 'calculateSales'])->name('calculate');
        Route::post('/{id}/propose', [PencairanDanaController::class, 'propose'])->name('propose');
        Route::get('/preview-pdf/{id}', [PencairanDanaController::class, 'generatePdf'])->name('preview_pdf');
        Route::get('/batch/{batch_id}', [PencairanDanaController::class, 'show'])->name('show');
        Route::get('/batch/{batch_id}/pdf', [PencairanDanaController::class, 'generateBatchPdf'])->name('batch_pdf');
    });
    
    // Rute Pengelola Tenant
    Route::get('/pengelola/tenant', [TenantController::class, 'index'])->name('pengelola.tenant.index');
    Route::get('/pengelola/tenant/create', [TenantController::class, 'create'])->name('pengelola.tenant.create');
    Route::post('/pengelola/tenant', [TenantController::class, 'store'])->name('pengelola.tenant.store');
    Route::get('/pengelola/tenant/{tenant}', [TenantController::class, 'show'])->name('pengelola.tenant.show');
    Route::get('/pengelola/tenant/{tenant}/edit', [TenantController::class, 'edit'])->name('pengelola.tenant.edit');
    Route::put('/pengelola/tenant/{tenant}', [TenantController::class, 'update'])->name('pengelola.tenant.update');

    // Rute Rekap Penjualan
    Route::get('/pengelola/rekap-penjualan', [RekapPenjualanController::class, 'index'])->name('pengelola.rekap.index');
    Route::get('/pengelola/rekap-penjualan/{kantin}', [RekapPenjualanController::class, 'show'])->name('pengelola.rekap.show');
});

Route::middleware(['auth', 'role:tenant'])->group(function () {
    // Dashboard & Status Toko
    Route::get('/tenant/dashboard', [\App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('tenant.dashboard');
    Route::patch('/tenant/toggle-status', [\App\Http\Controllers\Tenant\DashboardController::class, 'toggleStoreStatus'])->name('tenant.toggle-status');

    // Menu Management untuk Tenant
    Route::resource('/tenant/menu', \App\Http\Controllers\Tenant\MenuController::class, [
        'as' => 'tenant'
    ]);
    Route::patch('/tenant/menu/{menu}/toggle-status', [\App\Http\Controllers\Tenant\MenuController::class, 'toggleStatus'])->name('tenant.menu.toggle-status');

    // Pesanan Masuk untuk Tenant
    Route::get('/tenant/orders', [\App\Http\Controllers\Tenant\OrderController::class, 'index'])->name('tenant.orders.index');
    Route::get('/tenant/orders/{order}', [\App\Http\Controllers\Tenant\OrderController::class, 'show'])->name('tenant.orders.show');
    Route::patch('/tenant/orders/{order}/status', [\App\Http\Controllers\Tenant\OrderController::class, 'updateStatus'])->name('tenant.orders.update-status');

    // Rekap Penjualan untuk Tenant
    Route::get('/tenant/rekap-penjualan', [\App\Http\Controllers\Tenant\ReportController::class, 'index'])->name('tenant.reports.index');
    Route::get('/tenant/rekap-penjualan/export/excel', [\App\Http\Controllers\Tenant\ReportController::class, 'exportExcel'])->name('tenant.reports.export-excel');
    Route::get('/tenant/rekap-penjualan/export/pdf', [\App\Http\Controllers\Tenant\ReportController::class, 'exportPdf'])->name('tenant.reports.export-pdf');
});

Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pelanggan/dashboard', [\App\Http\Controllers\PelangganController::class, 'dashboard'])->name('pelanggan.dashboard');
    Route::get('/pelanggan/kantin/{kantin}', [\App\Http\Controllers\PelangganController::class, 'showKantin'])->name('pelanggan.kantin.show');
    Route::get('/pelanggan/tenant/{tenant}', [\App\Http\Controllers\PelangganController::class, 'showTenant'])->name('pelanggan.tenant.show');
    
    // Rute Keranjang & Checkout
    Route::get('/pelanggan/checkout', [CartController::class, 'index'])->name('pelanggan.checkout');
    Route::post('/pelanggan/cart/add', [CartController::class, 'add'])->name('pelanggan.cart.add');
    Route::post('/pelanggan/cart/decrease', [CartController::class, 'decrease'])->name('pelanggan.cart.decrease');
    Route::post('/pelanggan/cart/update', [CartController::class, 'updateQuantity'])->name('pelanggan.cart.update');
    Route::post('/pelanggan/cart/remove', [CartController::class, 'remove'])->name('pelanggan.cart.remove');
    
    // Checkout & Midtrans
    Route::post('/pelanggan/checkout/process', [CheckoutController::class, 'process'])->name('pelanggan.checkout.process');
    Route::post('/pelanggan/checkout/success-local', [CheckoutController::class, 'successLocal'])->name('pelanggan.checkout.success-local');
    
    // Pesanan Saya
    Route::get('/pelanggan/orders', [\App\Http\Controllers\Pelanggan\OrderController::class, 'index'])->name('pelanggan.orders.index');
    Route::get('/pelanggan/orders/{order}', [\App\Http\Controllers\Pelanggan\OrderController::class, 'show'])->name('pelanggan.orders.show');
    Route::post('/pelanggan/orders/{order}/cancel', [\App\Http\Controllers\Pelanggan\OrderController::class, 'cancel'])->name('pelanggan.orders.cancel');
});
