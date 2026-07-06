@extends('layouts.dashboard')

@section('title', 'Data Tenant - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="tenant" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Data Tenant</h1>
            <p class="text-[15px] text-gray-500 font-medium">Kelola data tenant yang bergabung di kantin.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="#" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-telkom-red text-telkom-red hover:bg-red-50 font-semibold rounded-xl shadow-sm transition-colors">
                <i class="ph ph-trophy font-bold text-lg mr-2"></i>
                Ranking Tenant
            </a>
            <a href="{{ route('pengelola.tenant.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-telkom-red hover:bg-telkom-maroon text-white font-semibold rounded-xl shadow-sm transition-colors">
                <i class="ph ph-plus font-bold text-lg mr-2"></i>
                Tambah Tenant
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="w-16 h-16 rounded-[14px] bg-red-50 flex items-center justify-center mr-5">
                <i class="ph ph-users text-[32px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Total Tenant</p>
                <h3 class="text-[34px] font-bold text-gray-900 leading-none mb-1">{{ $tenants->count() }}</h3>
                <p class="text-[13px] font-medium text-gray-400">Tenant</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="w-16 h-16 rounded-[14px] bg-gray-50 flex items-center justify-center mr-5">
                <div class="w-3 h-3 bg-green-500 rounded-full ring-4 ring-green-100"></div>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Tenant Aktif</p>
                <h3 class="text-[34px] font-bold text-gray-900 leading-none mb-1">{{ $tenants->where('status', 'aktif')->count() }}</h3>
                <p class="text-[13px] font-medium text-gray-400">Tenant</p>
            </div>
        </div>
    </div>

    <!-- Main Content Box (Table Section) -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        
        <!-- Filter & Search Bar -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" placeholder="Cari nama tenant..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all">
            </div>
            
            <div class="flex items-center space-x-3 w-full md:w-auto">
                <!-- Filter Kantin -->
                <div class="relative w-full md:w-48">
                    <select class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 text-sm text-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                        <option value="">Filter Kantin</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    <span class="absolute -top-2 left-3 bg-white px-1 text-[11px] font-medium text-gray-400">Filter Kantin</span>
                </div>

                <!-- Filter Status -->
                <div class="relative w-full md:w-48">
                    <select class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 text-sm text-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                        <option value="">Filter Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    <span class="absolute -top-2 left-3 bg-white px-1 text-[11px] font-medium text-gray-400">Filter Status</span>
                </div>

                <!-- Refresh Button -->
                <button class="w-12 h-12 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                    <i class="ph ph-arrows-clockwise text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">No</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Nama Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Nama Kantin</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Jenis Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">No. Telepon</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Status</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tenants as $index => $tenant)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 mr-3 overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center">
                                        @if($tenant->foto)
                                            <img src="{{ asset('storage/' . $tenant->foto) }}" alt="{{ $tenant->nama_tenant }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60" alt="no image">
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-[14px] text-gray-900">{{ $tenant->nama_tenant }}</span>
                                        <span class="text-[12px] text-gray-500 mt-0.5">{{ $tenant->user->email ?? 'Belum ada email' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $tenant->kantin->nama_kantin ?? '-' }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $tenant->jenis_makanan }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $tenant->no_telepon }}</td>
                            <td class="py-4 px-6">
                                @if($tenant->status === 'aktif')
                                    <span class="inline-flex items-center justify-center w-24 py-1.5 rounded-full text-[12px] font-bold bg-green-50 text-green-600 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-24 py-1.5 rounded-full text-[12px] font-bold bg-gray-50 text-gray-500 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 flex items-center space-x-2">
                                <a href="{{ route('pengelola.tenant.show', $tenant->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 border border-telkom-red text-telkom-red hover:bg-red-50 text-[13px] font-bold rounded-lg transition-colors">
                                    <i class="ph ph-eye mr-1.5"></i>
                                    Detail
                                </a>
                                <a href="{{ route('pengelola.tenant.edit', $tenant->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-gray-900 border border-gray-200 rounded-lg transition-colors" title="Edit Tenant">
                                    <i class="ph ph-pencil-simple text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ph ph-users text-4xl text-gray-200 mb-3"></i>
                                    <p class="text-sm font-medium text-gray-400">Belum ada data tenant</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-[13px] font-medium text-gray-500">Menampilkan {{ $tenants->count() }} data tenant</span>
            <div class="flex items-center space-x-1.5">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition-colors cursor-not-allowed" disabled>
                    <i class="ph ph-caret-left font-bold"></i>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-telkom-red text-white font-bold text-[13px] shadow-sm">
                    1
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition-colors cursor-not-allowed" disabled>
                    <i class="ph ph-caret-right font-bold"></i>
                </button>
            </div>
        </div>

    </div>
@endsection
