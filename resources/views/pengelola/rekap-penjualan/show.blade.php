@extends('layouts.dashboard')

@section('title', 'Detail Rekap Penjualan - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="rekap" />
@endsection

@section('content')
    <!-- Back Button -->
    <a href="{{ route('pengelola.rekap.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors mb-6">
        <i class="ph ph-arrow-left text-lg mr-2"></i>
        Kembali
    </a>

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Detail Rekap Penjualan – {{ $kantin->nama_kantin }}</h1>
            <p class="text-[15px] text-gray-500 font-medium">Ringkasan penjualan seluruh tenant pada {{ $kantin->nama_kantin }}.</p>
        </div>
        <!-- Export Dropdown Menu -->
        <div class="relative" x-data="{ openExport: false }" @click.away="openExport = false">
            <button @click="openExport = !openExport" class="inline-flex items-center justify-center px-5 py-2.5 border border-telkom-red text-telkom-red hover:bg-red-50 font-semibold rounded-xl transition-colors">
                <i class="ph ph-download-simple font-bold text-lg mr-2"></i>
                Export Laporan
                <i class="ph ph-caret-down ml-2"></i>
            </button>
            
            <div x-show="openExport" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden" 
                 style="display: none;">
                <div class="py-1">
                    <a href="{{ route('pengelola.rekap.export-pdf', ['kantin' => $kantin->id] + request()->query()) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-telkom-red transition-colors">
                        <i class="ph ph-file-pdf text-lg mr-2"></i>
                        Export sebagai PDF
                    </a>
                    <a href="{{ route('pengelola.rekap.export-excel', ['kantin' => $kantin->id] + request()->query()) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-telkom-red transition-colors">
                        <i class="ph ph-file-xls text-lg mr-2"></i>
                        Export sebagai Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Box (Table Section) -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        
        <!-- Filter & Search Bar -->
        <!-- Filter & Search Bar -->
        <div class="p-6 border-b border-gray-100">
            <form action="{{ route('pengelola.rekap.show', $kantin->id) }}" method="GET" class="flex flex-col xl:flex-row gap-4 w-full items-start xl:items-center justify-between">
                
                <!-- Left: Search -->
                <div class="relative flex-1 w-full xl:max-w-xs">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tenant..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all">
                </div>
                
                <!-- Right: Filters -->
                <div class="flex flex-col md:flex-row items-center gap-3 w-full xl:w-auto">
                    
                    <div class="relative w-full md:w-40">
                        <select name="period" onchange="if(this.value !== 'custom') { this.form.start_date.value=''; this.form.end_date.value=''; }" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all appearance-none cursor-pointer">
                            <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Pilih Tanggal</option>
                        </select>
                        <i class="ph ph-calendar-blank absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    <!-- Custom Date Range -->
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.period.value='custom'" class="w-full md:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all" title="Tanggal Mulai">
                        <span class="text-gray-400">-</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.period.value='custom'" class="w-full md:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all" title="Tanggal Akhir">
                    </div>

                    <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-telkom-red hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center justify-center">
                        Terapkan
                    </button>
                    
                    @if(request('search') || request('period') && request('period') !== 'all')
                        <a href="{{ route('pengelola.rekap.show', $kantin->id) }}" class="w-full md:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-colors flex items-center justify-center" title="Reset Filter">
                            <i class="ph-bold ph-arrow-counter-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-100">
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">No</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Nama Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Pesanan Selesai</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Total Penjualan</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tenants as $index => $tenant)
                        @php
                            $pesananSelesai = $tenant->pesanan_selesai ?? 0;
                            $totalPenjualan = $tenant->total_penjualan ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 mr-4 overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center">
                                        @if($tenant->foto)
                                            <img src="{{ asset('storage/' . $tenant->foto) }}" alt="{{ $tenant->nama_tenant }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60" alt="no image">
                                        @endif
                                    </div>
                                    <span class="font-bold text-[14px] text-gray-900">{{ $tenant->nama_tenant }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $pesananSelesai }} Pesanan</td>
                            <td class="py-4 px-6 text-[14px] font-bold text-gray-900">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-center">
                                <a href="#" class="inline-flex items-center justify-center px-4 py-1.5 border border-telkom-red text-telkom-red hover:bg-red-50 text-[13px] font-bold rounded-lg transition-colors">
                                    <i class="ph ph-eye mr-1.5"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 mb-4 opacity-60">
                                        <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-contain" alt="no image">
                                    </div>
                                    <p class="text-sm font-medium text-gray-400">Belum ada data tenant</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-5 border-t border-gray-100">
            {{ $tenants->links() }}
        </div>

    </div>
@endsection
