@extends('layouts.superadmin')

@section('title', 'Tenant Management')
@section('breadcrumb', 'Tenant')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-gray-800">All Tenants</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua data tenant.</p>
        </div>
        <a href="{{ route('superadmin.tenant.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Add New Tenant
        </a>
    </div>
    
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('superadmin.tenant.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tenant atau owner..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white outline-none transition-shadow hover:shadow-sm">
            </div>
            
            <!-- Kantin -->
            <div class="relative min-w-[150px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-store"></i>
                </div>
                <select name="kantin_id" class="w-full pl-10 pr-8 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-shadow hover:shadow-sm" onchange="this.form.submit()">
                    <option value="all">Semua Kantin</option>
                    @foreach($kantins as $k)
                        <option value="{{ $k->id }}" {{ request('kantin_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kantin }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
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
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Tenant Name</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Owner (User)</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Email</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">No Telp</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Kantin</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $tenant->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $tenant->nama_tenant }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $tenant->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $tenant->user->email ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $tenant->no_telepon ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $tenant->kantin->nama_kantin ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $tenant->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('superadmin.tenant.edit', $tenant->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-circle-info mr-1.5"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No Tenant data found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
