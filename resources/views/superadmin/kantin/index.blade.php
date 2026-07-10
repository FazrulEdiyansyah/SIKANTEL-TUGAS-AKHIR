@extends('layouts.superadmin')

@section('title', 'Manajemen Kantin')
@section('breadcrumb', 'Kantin')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Data Kantin</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua data kantin.</p>
        </div>
        <a href="{{ route('superadmin.kantin.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center">
            <i class="ph ph-plus font-bold mr-2"></i> Tambah Kantin
        </a>
    </div>
    
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('superadmin.kantin.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau lokasi kantin..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white outline-none transition-all">
            </div>
            
            <!-- Status -->
            <div class="relative min-w-[150px]">
                <select name="status" class="w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-all" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>

            <!-- Reset -->
            <a href="{{ route('superadmin.kantin.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors shrink-0" title="Reset Filter">
                <i class="ph ph-arrows-clockwise text-lg"></i>
            </a>

            <button type="submit" class="hidden">Filter</button>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Nama Kantin</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Total Tenant</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Dibuat</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kantins as $index => $kantin)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $kantins->firstItem() + $index }}</td>
                    <td class="py-4 px-6 text-[14px] font-bold text-gray-800">{{ $kantin->nama_kantin }}</td>
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $kantin->lokasi }}</td>
                    <td class="py-4 px-6 text-center">
                        <span class="inline-flex items-center justify-center bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-bold text-xs border border-blue-100">
                            {{ $kantin->tenants_count }} Tenant
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <x-status-badge :status="$kantin->status" />
                    </td>
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $kantin->created_at->format('d M Y') }}</td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('superadmin.kantin.edit', $kantin->id) }}" class="inline-flex items-center px-3 py-1.5 text-[13px] font-bold text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="ph ph-eye mr-1.5"></i> Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <x-empty-state icon="ph ph-storefront" title="Belum ada data kantin" message="Silakan tambah data kantin baru." :colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-5 border-t border-gray-100">
        {{ $kantins->links() }}
    </div>
</div>
@endsection
