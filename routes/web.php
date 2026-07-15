<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match ($role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'kaur' => redirect()->route('kaur.dashboard'),
            'kabag' => redirect()->route('kabag.dashboard'),
            'pengelola' => redirect()->route('pengelola.dashboard'),
            'tenant' => redirect()->route('tenant.dashboard'),
            'pelanggan' => redirect()->route('pelanggan.dashboard'),
            default => redirect()->route('login'),
        };
    }
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
        
        Route::get('/{batch_id}', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'show'])->name('show');
        Route::get('/{batch_id}/batch-pdf', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'generateBatchPdf'])->name('batch_pdf');
        Route::post('/{batch_id}/propose', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'propose'])->name('propose');
        Route::post('/{batch_id}/approve', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'approve'])->name('approve');
        Route::post('/{batch_id}/reject', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'reject'])->name('reject');
        Route::delete('/{batch_id}', [\App\Http\Controllers\Superadmin\PencairanDanaController::class, 'destroy'])->name('destroy');
    });
});

// Rute Kaur
Route::middleware(['auth', 'role:kaur'])->prefix('kaur')->name('kaur.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Approver\ApproverController::class, 'dashboardKaur'])->name('dashboard');
    Route::get('/riwayat', [\App\Http\Controllers\Approver\ApproverController::class, 'riwayatKaur'])->name('riwayat');
    Route::get('/pencairan/{batch_id}', [\App\Http\Controllers\Approver\ApproverController::class, 'showPencairan'])->name('pencairan.show');
    Route::get('/pencairan/{batch_id}/pdf', [\App\Http\Controllers\Approver\ApproverController::class, 'generatePdf'])->name('pencairan.pdf');
    Route::post('/pencairan/{batch_id}/approve', [\App\Http\Controllers\Approver\ApproverController::class, 'approveKaur'])->name('pencairan.approve');
    Route::post('/pencairan/{batch_id}/reject', [\App\Http\Controllers\Approver\ApproverController::class, 'rejectKaur'])->name('pencairan.reject');
    
    Route::get('/kantin', [\App\Http\Controllers\Approver\ApproverController::class, 'kantin'])->name('kantin.index');
    Route::get('/tenant', [\App\Http\Controllers\Approver\ApproverController::class, 'tenant'])->name('tenant.index');
    Route::get('/orders', [\App\Http\Controllers\Approver\ApproverController::class, 'orders'])->name('orders.index');
    
    // Profil
    Route::get('/profile', [\App\Http\Controllers\Approver\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Approver\ProfileController::class, 'update'])->name('profile.update');
});

// Rute Kabag
Route::middleware(['auth', 'role:kabag'])->prefix('kabag')->name('kabag.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Approver\ApproverController::class, 'dashboardKabag'])->name('dashboard');
    Route::get('/riwayat', [\App\Http\Controllers\Approver\ApproverController::class, 'riwayatKabag'])->name('riwayat');
    Route::get('/pencairan/{batch_id}', [\App\Http\Controllers\Approver\ApproverController::class, 'showPencairan'])->name('pencairan.show');
    Route::get('/pencairan/{batch_id}/pdf', [\App\Http\Controllers\Approver\ApproverController::class, 'generatePdf'])->name('pencairan.pdf');
    Route::post('/pencairan/{batch_id}/approve', [\App\Http\Controllers\Approver\ApproverController::class, 'approveKabag'])->name('pencairan.approve');
    Route::post('/pencairan/{batch_id}/reject', [\App\Http\Controllers\Approver\ApproverController::class, 'rejectKabag'])->name('pencairan.reject');

    Route::get('/kantin', [\App\Http\Controllers\Approver\ApproverController::class, 'kantin'])->name('kantin.index');
    Route::get('/tenant', [\App\Http\Controllers\Approver\ApproverController::class, 'tenant'])->name('tenant.index');
    Route::get('/orders', [\App\Http\Controllers\Approver\ApproverController::class, 'orders'])->name('orders.index');
    
    // Profil
    Route::get('/profile', [\App\Http\Controllers\Approver\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Approver\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:pengelola'])->group(function () {
    Route::get('/pengelola/dashboard', [\App\Http\Controllers\Pengelola\DashboardController::class, 'dashboard'])->name('pengelola.dashboard');
    Route::get('/pengelola/kantin', [\App\Http\Controllers\Pengelola\KantinController::class, 'index'])->name('pengelola.kantin.index');
    Route::get('/pengelola/kantin/create', [\App\Http\Controllers\Pengelola\KantinController::class, 'create'])->name('pengelola.kantin.create');
    Route::post('/pengelola/kantin', [\App\Http\Controllers\Pengelola\KantinController::class, 'store'])->name('pengelola.kantin.store');
    Route::get('/pengelola/kantin/{kantin}', [\App\Http\Controllers\Pengelola\KantinController::class, 'show'])->name('pengelola.kantin.show');
    Route::get('/pengelola/kantin/{kantin}/edit', [\App\Http\Controllers\Pengelola\KantinController::class, 'edit'])->name('pengelola.kantin.edit');
    Route::put('/pengelola/kantin/{kantin}', [\App\Http\Controllers\Pengelola\KantinController::class, 'update'])->name('pengelola.kantin.update');
    
    // Laporan Pencairan Dana
    Route::prefix('pengelola/pencairan-dana')->name('pengelola.pencairan_dana.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'store'])->name('store');
        Route::post('/calculate', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'calculateSales'])->name('calculate');
        Route::post('/{id}/propose', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'propose'])->name('propose');
        Route::post('/batch/{batch_id}/duplicate', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'duplicateBatch'])->name('duplicate');
        Route::get('/preview-pdf/{id}', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'generatePdf'])->name('preview_pdf');
        Route::get('/batch/{batch_id}', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'show'])->name('show');
        Route::get('/batch/{batch_id}/pdf', [\App\Http\Controllers\Pengelola\PencairanDanaController::class, 'generateBatchPdf'])->name('batch_pdf');
    });
    
    // Rute Pengelola Tenant
    Route::get('/pengelola/tenant', [\App\Http\Controllers\Pengelola\TenantController::class, 'index'])->name('pengelola.tenant.index');
    Route::get('/pengelola/tenant/create', [\App\Http\Controllers\Pengelola\TenantController::class, 'create'])->name('pengelola.tenant.create');
    Route::post('/pengelola/tenant', [\App\Http\Controllers\Pengelola\TenantController::class, 'store'])->name('pengelola.tenant.store');
    Route::get('/pengelola/tenant/{tenant}', [\App\Http\Controllers\Pengelola\TenantController::class, 'show'])->name('pengelola.tenant.show');
    Route::get('/pengelola/tenant/{tenant}/edit', [\App\Http\Controllers\Pengelola\TenantController::class, 'edit'])->name('pengelola.tenant.edit');
    Route::put('/pengelola/tenant/{tenant}', [\App\Http\Controllers\Pengelola\TenantController::class, 'update'])->name('pengelola.tenant.update');

    // Rute Rekap Penjualan
    Route::get('/pengelola/rekap-penjualan', [\App\Http\Controllers\Pengelola\RekapPenjualanController::class, 'index'])->name('pengelola.rekap.index');
    Route::get('/pengelola/rekap-penjualan/{kantin}', [\App\Http\Controllers\Pengelola\RekapPenjualanController::class, 'show'])->name('pengelola.rekap.show');
    Route::get('/pengelola/rekap-penjualan/{kantin}/export/excel', [\App\Http\Controllers\Pengelola\RekapPenjualanController::class, 'exportExcel'])->name('pengelola.rekap.export-excel');
    Route::get('/pengelola/rekap-penjualan/{kantin}/export/pdf', [\App\Http\Controllers\Pengelola\RekapPenjualanController::class, 'exportPdf'])->name('pengelola.rekap.export-pdf');
    Route::get('/pengelola/rekap-penjualan/{kantin}/tenant/{tenant}', [\App\Http\Controllers\Pengelola\RekapPenjualanController::class, 'showTenant'])->name('pengelola.rekap.show-tenant');
    Route::get('/pengelola/rekap-penjualan/{kantin}/tenant/{tenant}/export/excel', [\App\Http\Controllers\Pengelola\RekapPenjualanController::class, 'exportExcelTenant'])->name('pengelola.rekap.export-excel-tenant');
    Route::get('/pengelola/rekap-penjualan/{kantin}/tenant/{tenant}/export/pdf', [\App\Http\Controllers\Pengelola\RekapPenjualanController::class, 'exportPdfTenant'])->name('pengelola.rekap.export-pdf-tenant');
    
    // Profil
    Route::get('/pengelola/profile', [\App\Http\Controllers\Pengelola\ProfileController::class, 'index'])->name('pengelola.profile.index');
    Route::put('/pengelola/profile', [\App\Http\Controllers\Pengelola\ProfileController::class, 'update'])->name('pengelola.profile.update');
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

    // Profil Tenant
    Route::get('/tenant/profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'index'])->name('tenant.profile.index');
    Route::put('/tenant/profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'update'])->name('tenant.profile.update');
});

Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pelanggan/dashboard', [\App\Http\Controllers\Pelanggan\DashboardController::class, 'dashboard'])->name('pelanggan.dashboard');
    Route::get('/pelanggan/search', [\App\Http\Controllers\Pelanggan\SearchController::class, 'index'])->name('pelanggan.search');
    Route::get('/pelanggan/search/autocomplete', [\App\Http\Controllers\Pelanggan\SearchController::class, 'autocomplete'])->name('pelanggan.search.autocomplete');
    Route::post('/pelanggan/search/remove-recent', [\App\Http\Controllers\Pelanggan\SearchController::class, 'removeRecent'])->name('pelanggan.search.remove_recent');
    Route::get('/pelanggan/kantin/{kantin}', [\App\Http\Controllers\Pelanggan\DashboardController::class, 'showKantin'])->name('pelanggan.kantin.show');
    Route::get('/pelanggan/tenant/{tenant}', [\App\Http\Controllers\Pelanggan\DashboardController::class, 'showTenant'])->name('pelanggan.tenant.show');
    
    // Rute Keranjang & Checkout
    Route::get('/pelanggan/checkout', [\App\Http\Controllers\Pelanggan\CartController::class, 'index'])->name('pelanggan.checkout');
    Route::post('/pelanggan/cart/add', [\App\Http\Controllers\Pelanggan\CartController::class, 'add'])->name('pelanggan.cart.add');
    Route::post('/pelanggan/cart/decrease', [\App\Http\Controllers\Pelanggan\CartController::class, 'decrease'])->name('pelanggan.cart.decrease');
    Route::post('/pelanggan/cart/update', [\App\Http\Controllers\Pelanggan\CartController::class, 'updateQuantity'])->name('pelanggan.cart.update');
    Route::post('/pelanggan/cart/update-note', [\App\Http\Controllers\Pelanggan\CartController::class, 'updateNote'])->name('pelanggan.cart.update-note');
    Route::post('/pelanggan/cart/remove', [\App\Http\Controllers\Pelanggan\CartController::class, 'remove'])->name('pelanggan.cart.remove');
    
    // Checkout & Midtrans
    Route::post('/pelanggan/checkout/process', [\App\Http\Controllers\Pelanggan\CheckoutController::class, 'process'])->name('pelanggan.checkout.process');
    Route::post('/pelanggan/checkout/success-local', [\App\Http\Controllers\Pelanggan\CheckoutController::class, 'successLocal'])->name('pelanggan.checkout.success-local');
    
    // Pesanan Saya
    Route::get('/pelanggan/orders', [\App\Http\Controllers\Pelanggan\OrderController::class, 'index'])->name('pelanggan.orders.index');
    Route::get('/pelanggan/orders/{order}', [\App\Http\Controllers\Pelanggan\OrderController::class, 'show'])->name('pelanggan.orders.show');
    Route::patch('/pelanggan/orders/{order}/table', [\App\Http\Controllers\Pelanggan\OrderController::class, 'updateTable'])->name('pelanggan.orders.update-table');
    Route::post('/pelanggan/orders/{order}/cancel', [\App\Http\Controllers\Pelanggan\OrderController::class, 'cancel'])->name('pelanggan.orders.cancel');
    
    // Fitur Baru: Polling Status, Reorder, Review
    Route::get('/pelanggan/orders/{order}/status', [\App\Http\Controllers\Pelanggan\OrderController::class, 'statusAPI'])->name('pelanggan.orders.status-api');
    Route::post('/pelanggan/orders/{order}/pay', [\App\Http\Controllers\Pelanggan\OrderController::class, 'pay'])->name('pelanggan.orders.pay');
    Route::post('/pelanggan/orders/{order}/reorder', [\App\Http\Controllers\Pelanggan\OrderController::class, 'reorder'])->name('pelanggan.orders.reorder');
    Route::post('/pelanggan/orders/{order}/review', [\App\Http\Controllers\Pelanggan\ReviewController::class, 'store'])->name('pelanggan.orders.review');
    
    // Profil
    Route::get('/pelanggan/profile', [\App\Http\Controllers\Pelanggan\ProfileController::class, 'index'])->name('pelanggan.profile.index');
    Route::put('/pelanggan/profile', [\App\Http\Controllers\Pelanggan\ProfileController::class, 'update'])->name('pelanggan.profile.update');
});
