@extends('layouts.dashboard')

@section('title', 'Data Kantin - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="kantin" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Data Kantin</h1>
            <p class="text-[15px] text-gray-500 font-medium">Kelola data kantin yang tersedia di lingkungan Universitas Telkom.</p>
        </div>
        <a href="{{ route('pengelola.kantin.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-telkom-red hover:bg-telkom-maroon text-white font-semibold rounded-xl shadow-sm transition-colors">
            <i class="ph ph-plus font-bold text-lg mr-2"></i>
            Tambah Kantin
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mr-5">
                <i class="ph ph-storefront text-[28px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Total Kantin</p>
                <div class="flex items-end">
                    <h3 class="text-3xl font-bold text-gray-900 leading-none mr-2">{{ $kantins->total() }}</h3>
                    <span class="text-[13px] font-medium text-gray-400 mb-0.5">Kantin</span>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mr-5">
                <div class="w-3 h-3 bg-green-500 rounded-full ring-4 ring-green-100"></div>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Kantin Aktif</p>
                <div class="flex items-end">
                    <h3 class="text-3xl font-bold text-gray-900 leading-none mr-2">{{ $kantins->where('status', 'aktif')->count() }}</h3>
                    <span class="text-[13px] font-medium text-gray-400 mb-0.5">Kantin</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Box (Table Section) -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('pengelola.kantin.index') }}" class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kantin..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all">
                <button type="submit" class="hidden"></button>
            </div>
            
            <div class="flex items-center space-x-3 w-full md:w-auto">
                <!-- Filter Status -->
                <div class="relative w-full md:w-48">
                    <select name="status" onchange="this.form.submit()" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 text-sm text-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                        <option value="">Filter Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    <span class="absolute -top-2 left-3 bg-white px-1 text-[11px] font-medium text-gray-400">Filter Status</span>
                </div>

                <!-- Refresh Button -->
                <a href="{{ route('pengelola.kantin.index') }}" class="w-12 h-12 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                    <i class="ph ph-arrows-clockwise text-xl"></i>
                </a>
            </div>
        </form>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Nama Kantin</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Jumlah Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kantins as $index => $kantin)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $kantins->firstItem() + $index }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 mr-4 overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center">
                                        @if($kantin->foto)
                                            <img src="{{ asset('storage/' . $kantin->foto) }}" alt="{{ $kantin->nama_kantin }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60" alt="no image">
                                        @endif
                                    </div>
                                    <span class="font-bold text-[14px] text-gray-900">{{ $kantin->nama_kantin }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $kantin->lokasi }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600 text-center">
                                @if($kantin->tenants_count > 0)
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[12px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ $kantin->tenants_count }} Tenant
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <x-status-badge :status="$kantin->status" />
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pengelola.kantin.show', $kantin->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 border border-telkom-red text-telkom-red hover:bg-red-50 text-[13px] font-bold rounded-lg transition-colors">
                                        <i class="ph ph-eye mr-1.5"></i>
                                        Detail
                                    </a>
                                    <a href="{{ route('pengelola.kantin.edit', $kantin->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-gray-900 border border-gray-200 rounded-lg transition-colors" title="Edit Kantin">
                                        <i class="ph ph-pencil-simple text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    <x-empty-state icon="ph ph-storefront" title="Belum ada data kantin" message="Silakan tambah data kantin baru." :colspan="6" />
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-5 border-t border-gray-100">
            {{ $kantins->links() }}
        </div>

    </div>
@endsection
