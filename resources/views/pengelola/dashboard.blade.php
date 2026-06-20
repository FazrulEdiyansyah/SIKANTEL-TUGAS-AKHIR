@extends('layouts.dashboard')

@section('title', 'Dashboard Pengelola')

@section('sidebar_menu')
    <x-sidebar.pengelola active="dashboard" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Dashboard Pengelola</h1>
        <p class="text-[15px] text-gray-500 font-medium">Pantau ringkasan performa kantin dan tenant secara real-time.</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-6">
                <i class="ph ph-storefront text-[24px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Kantin</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">0</h3>
                <p class="text-[13px] font-medium text-gray-400">Kantin Aktif</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-6">
                <i class="ph ph-users text-[24px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Tenant Aktif</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">0</h3>
                <p class="text-[13px] font-medium text-gray-400">Tenant Aktif</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mb-6">
                <i class="ph ph-money text-[24px] text-green-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Penjualan Bulan Ini</p>
                <h3 class="text-3xl font-bold text-gray-400 mb-1">Rp0</h3>
                <p class="text-[13px] font-medium text-gray-400">Dari semua kantin</p>
            </div>
        </div>
    </div>

    <!-- Charts & Lists Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Grafik Penjualan (Placeholder) -->
        <div class="lg:col-span-2 bg-white rounded-[20px] p-6 shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 space-y-4 sm:space-y-0">
                <h2 class="text-[17px] font-bold text-gray-900">Grafik Penjualan</h2>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center bg-gray-50 rounded-lg p-1">
                        <span class="text-xs font-medium text-gray-500 px-3 py-1.5">Pilih Kantin</span>
                        <select class="text-xs font-semibold bg-white border border-gray-200 rounded-md px-3 py-1 outline-none focus:ring-2 focus:ring-telkom-red/20 cursor-pointer">
                            <option>Semua Kantin</option>
                        </select>
                    </div>
                    <div class="flex bg-gray-50 rounded-lg p-1">
                        <button class="text-[13px] font-medium text-gray-500 px-4 py-1.5 rounded-md hover:text-gray-900 transition-colors">Hari Ini</button>
                        <button class="text-[13px] font-medium text-gray-500 px-4 py-1.5 rounded-md hover:text-gray-900 transition-colors">Minggu Ini</button>
                        <button class="text-[13px] font-bold text-white bg-telkom-red px-4 py-1.5 rounded-md shadow-sm">Bulan Ini</button>
                    </div>
                </div>
            </div>
            
            <!-- Area Chart Placeholder (CSS Visual) -->
            <div class="relative h-64 w-full flex items-end">
                <!-- Y-Axis Grid Lines -->
                <div class="absolute inset-0 flex flex-col justify-between text-[12px] text-gray-400 font-medium pb-8 pr-12">
                    <div class="flex items-center justify-between w-full border-b border-gray-100 border-dashed pb-1"><span>8 jt</span></div>
                    <div class="flex items-center justify-between w-full border-b border-gray-100 border-dashed pb-1"><span>6 jt</span></div>
                    <div class="flex items-center justify-between w-full border-b border-gray-100 border-dashed pb-1"><span>4 jt</span></div>
                    <div class="flex items-center justify-between w-full border-b border-gray-100 border-dashed pb-1"><span>2 jt</span></div>
                    <div class="flex items-center justify-between w-full border-b border-gray-100 border-dashed pb-1"><span>0 jt</span></div>
                </div>
                
                <!-- X-Axis Labels -->
                <div class="absolute bottom-0 left-10 right-0 flex justify-between text-[12px] text-gray-400 font-medium pt-3">
                    <span>1 Jun</span><span>4 Jun</span><span>6 Jun</span><span>9 Jun</span><span>11 Jun</span><span>14 Jun</span><span>18 Jun</span><span>21 Jun</span><span>26 Jun</span><span>30 Jun</span>
                </div>

                <!-- Fake Line Chart SVG Placeholder (Empty State) -->
                <div class="absolute inset-0 w-full h-full pt-8 pb-10 pl-10 flex items-center justify-center">
                    <p class="text-sm font-medium text-gray-400 bg-white px-4 py-1 rounded-md">Belum ada data penjualan</p>
                </div>
            </div>
        </div>

        <!-- Kantin Paling Ramai -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col">
            <h2 class="text-[17px] font-bold text-gray-900 mb-6">Kantin Paling Ramai</h2>
            
            <div class="space-y-5 flex-1 flex flex-col items-center justify-center text-center pb-8">
                <i class="ph ph-storefront text-4xl text-gray-200 mb-3"></i>
                <p class="text-sm font-medium text-gray-400">Belum ada data kantin</p>
            </div>

            <button class="w-full mt-6 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-telkom-red hover:bg-red-50 transition-colors">
                Lihat Semua Kantin
            </button>
        </div>
    </div>

    <!-- Top 5 Tenant Table -->
    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-[17px] font-bold text-gray-900">Top 5 Tenant <span class="text-[13px] font-medium text-gray-400 font-normal ml-1">(Berdasarkan Penjualan)</span></h2>
            <div class="flex items-center bg-white border border-gray-200 rounded-lg p-1.5 px-3">
                <select class="text-[13px] font-semibold bg-transparent border-none outline-none focus:ring-0 cursor-pointer text-gray-700">
                    <option>Semua Kantin</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Peringkat</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tenant</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kantin</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Pesanan Selesai</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!-- Empty State -->
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="ph ph-users text-4xl text-gray-200 mb-3"></i>
                                <p class="text-sm font-medium text-gray-400">Belum ada data tenant</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="w-full pt-6 mt-2 flex justify-center">
                <button class="text-sm font-bold text-telkom-red hover:text-telkom-maroon transition-colors">
                    Lihat Semua Tenant
                </button>
            </div>
        </div>
    </div>
@endsection
