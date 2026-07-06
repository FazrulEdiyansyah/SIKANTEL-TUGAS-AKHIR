@extends('layouts.dashboard')

@section('title', 'Data Tenant - Approver')

@section('sidebar_menu')
    <x-sidebar.approver active="tenant" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Data Tenant</h1>
        <p class="text-[15px] text-gray-500 font-medium">Read-only view of all tenants and their parent kantin.</p>
    </div>

    <!-- Main Content Box (Table Section) -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('kaur.tenant.index') }}" class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tenant..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all">
                <button type="submit" class="hidden"></button>
            </div>
            
            <div class="flex items-center space-x-3 w-full md:w-auto">
                <!-- Filter Status -->
                <div class="relative w-full md:w-48">
                    <select name="status" onchange="this.form.submit()" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 text-sm text-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                        <option value="all">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    <span class="absolute -top-2 left-3 bg-white px-1 text-[11px] font-medium text-gray-400">Filter Status</span>
                </div>

                <!-- Refresh Button -->
                <a href="{{ url()->current() }}" class="w-12 h-12 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors">
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
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Pemilik</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Kantin</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tenants as $index => $tenant)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $tenants->firstItem() + $index }}</td>
                            <td class="py-4 px-6 text-[14px] font-bold text-gray-900">{{ $tenant->nama_tenant }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-700">{{ $tenant->user->name ?? '-' }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-700">{{ $tenant->kantin->nama_kantin ?? '-' }}</td>
                            <td class="py-4 px-6 text-center">
                                <x-status-badge :status="$tenant->status" />
                            </td>
                        </tr>
                    @empty
                    <x-empty-state icon="ph ph-users" title="Belum ada data tenant" message="Tidak ada data tenant yang ditemukan." :colspan="5" />
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
