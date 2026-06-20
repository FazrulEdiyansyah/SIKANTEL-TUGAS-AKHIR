@extends('layouts.dashboard')

@section('title', 'Dashboard Tenant - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="dashboard" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Dashboard Tenant</h1>
            <p class="text-[15px] text-gray-500 font-medium">Pantau pesanan masuk, status pesanan, dan ringkasan penjualan tenant Anda.</p>
        </div>

        <!-- Tenant Profile Summary Card -->
        <div class="bg-white rounded-[16px] shadow-sm border border-gray-100 p-4 flex items-center gap-4 shrink-0 min-w-[280px]">
            <div class="w-12 h-12 rounded-full overflow-hidden bg-red-50 border border-gray-100 shrink-0">
                <img src="https://ui-avatars.com/api/?name=Ayam+Geprek&background=fee2e2&color=dc2626&bold=true" alt="Ayam Geprek" class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 text-sm">Ayam Geprek</h3>
                <p class="text-xs text-gray-500 font-medium">Kantin GKU</p>
            </div>
            <div class="pl-4 border-l border-gray-100 flex flex-col items-center justify-center">
                <p class="text-[10px] text-gray-400 font-medium uppercase mb-0.5">Status</p>
                <span class="text-green-600 font-bold text-xs">Buka</span>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-blue-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-receipt text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 cursor-pointer hover:bg-gray-100 transition-colors">
                    <i class="ph-fill ph-file-text"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Pesanan Hari Ini</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">0</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">pesanan</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-yellow-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-clock text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center text-gray-300">
                    <i class="ph ph-caret-right font-bold"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Menunggu Diproses</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">0</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">pesanan</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-orange-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-fork-knife text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center text-gray-300">
                    <i class="ph ph-caret-right font-bold"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Sedang Disiapkan</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">0</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">pesanan</p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-green-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-trend-up text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center text-gray-300">
                    <i class="ph ph-caret-right font-bold"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Penjualan Hari Ini</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">Rp0</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">total penjualan</p>
            </div>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Pesanan Terbaru (span 2) -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h3>
                    <a href="#" class="text-[13px] font-bold text-telkom-red hover:text-telkom-maroon flex items-center transition-colors">
                        Lihat Semua <i class="ph ph-caret-right ml-1"></i>
                    </a>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Layanan</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Menu</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                                            <i class="ph ph-receipt text-3xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Pesanan</p>
                                        <p class="text-xs font-medium text-gray-500">Pesanan yang masuk akan otomatis muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 text-center">
                    <a href="#" class="text-[13px] font-bold text-telkom-red hover:text-telkom-maroon inline-flex items-center transition-colors">Lihat Semua Pesanan <i class="ph ph-caret-right ml-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Right Column (span 1) -->
        <div class="flex flex-col gap-6">
            <!-- Ringkasan Menu -->
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Ringkasan Menu</h3>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                <i class="ph-fill ph-clipboard-text text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Total Menu</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">0</span>
                            <p class="text-[11px] text-gray-400 font-medium">menu</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-500">
                                <i class="ph-fill ph-check-circle text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Menu Tersedia</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">0</span>
                            <p class="text-[11px] text-gray-400 font-medium">menu</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-telkom-red">
                                <i class="ph-fill ph-x-circle text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Menu Habis</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">0</span>
                            <p class="text-[11px] text-gray-400 font-medium">menu</p>
                        </div>
                    </div>
                </div>

                <button class="w-full py-2.5 rounded-xl border border-telkom-red text-telkom-red font-bold text-[13px] hover:bg-red-50 transition-colors">
                    Kelola Menu
                </button>
            </div>

            <!-- Ringkasan Penjualan -->
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan Penjualan</h3>
                    <button class="flex items-center gap-1 text-[11px] font-bold text-gray-600 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1.5 hover:bg-gray-100 transition-colors">
                        Hari Ini <i class="ph ph-caret-down ml-0.5"></i>
                    </button>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-500">
                                <i class="ph-fill ph-money text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Total Penjualan</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-[15px]">Rp0</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                                <i class="ph-fill ph-check-square text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Pesanan Selesai</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">0</span>
                            <p class="text-[11px] text-gray-400 font-medium">pesanan</p>
                        </div>
                    </div>
                </div>

                <button class="w-full py-2.5 rounded-xl border border-telkom-red text-telkom-red font-bold text-[13px] hover:bg-red-50 transition-colors">
                    Lihat Rekap Penjualan
                </button>
            </div>
        </div>

    </div>
@endsection
