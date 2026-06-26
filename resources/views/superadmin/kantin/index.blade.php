@extends('layouts.superadmin')

@section('title', 'Kantin Management')
@section('breadcrumb', 'Kantin')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-gray-800">All Kantin Data</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua data Kantin.</p>
        </div>
        <a href="{{ route('superadmin.kantin.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Add New Kantin
        </a>
    </div>
    
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('superadmin.kantin.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau lokasi kantin..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white outline-none transition-shadow hover:shadow-sm">
            </div>
            
            <!-- Status -->
            <div class="relative min-w-[150px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-award"></i>
                </div>
                <select name="status" class="w-full pl-10 pr-8 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-shadow hover:shadow-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <button type="submit" class="hidden">Filter</button>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Nama Kantin</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Lokasi</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100 text-center">Total Tenant</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Dibuat</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($kantins as $kantin)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $kantin->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $kantin->nama_kantin }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $kantin->lokasi }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-bold text-xs">
                            {{ $kantin->tenants_count }} Tenant
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $kantin->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($kantin->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $kantin->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('superadmin.kantin.edit', $kantin->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-circle-info mr-1.5"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No Kantin data found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $kantins->links() }}
    </div>
</div>
@endsection
