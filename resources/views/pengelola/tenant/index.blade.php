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
                <div class="flex items-end">
                    <h3 class="text-[34px] font-bold text-gray-900 leading-none mr-2">{{ $totalTenant }}</h3>
                    <span class="text-[13px] font-medium text-gray-400 mb-0.5">Tenant</span>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="w-16 h-16 rounded-[14px] bg-gray-50 flex items-center justify-center mr-5">
                <div class="w-3 h-3 bg-green-500 rounded-full ring-4 ring-green-100"></div>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Tenant Aktif</p>
                <div class="flex items-end">
                    <h3 class="text-[34px] font-bold text-gray-900 leading-none mr-2">{{ $totalTenantAktif }}</h3>
                    <span class="text-[13px] font-medium text-gray-400 mb-0.5">Tenant</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Box (Table Section) -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col" id="tenant-container">
        
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('pengelola.tenant.index') }}" class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tenant..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all">
                <button type="submit" class="hidden"></button>
            </div>
            
            <div class="flex items-center space-x-3 w-full md:w-auto">
                <!-- Filter Kantin -->
                <div class="relative w-full md:w-48">
                    <select name="kantin_id" onchange="this.form.submit()" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 text-sm text-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                        <option value="">Filter Kantin</option>
                        @foreach($kantins as $kantin)
                            <option value="{{ $kantin->id }}" {{ request('kantin_id') == $kantin->id ? 'selected' : '' }}>{{ $kantin->nama_kantin }}</option>
                        @endforeach
                    </select>
                    <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    <span class="absolute -top-2 left-3 bg-white px-1 text-[11px] font-medium text-gray-400">Filter Kantin</span>
                </div>

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
                <a href="{{ route('pengelola.tenant.index') }}" class="w-12 h-12 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors">
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
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Nama Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Nama Kantin</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Jenis Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">No. Telepon</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tenants as $index => $tenant)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $tenants->firstItem() + $index }}</td>
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
                                <x-status-badge :status="$tenant->status" />
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pengelola.tenant.show', $tenant->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 border border-telkom-red text-telkom-red hover:bg-red-50 text-[13px] font-bold rounded-lg transition-colors">
                                        <i class="ph ph-eye mr-1.5"></i>
                                        Detail
                                    </a>
                                    <a href="{{ route('pengelola.tenant.edit', $tenant->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-gray-900 border border-gray-200 rounded-lg transition-colors" title="Edit Tenant">
                                        <i class="ph ph-pencil-simple text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    <x-empty-state icon="ph ph-users" title="Belum ada data tenant" message="Silakan tambah data tenant baru." :colspan="7" />
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
