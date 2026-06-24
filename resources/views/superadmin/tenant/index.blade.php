@extends('layouts.superadmin')

@section('title', 'Tenant Management')
@section('breadcrumb', 'Tenant')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">All Tenants</h2>
        <p class="text-sm text-gray-500 mt-1">Read-only view of all tenants and their parent kantin.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Tenant Name</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Owner (User)</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Kantin</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $tenant->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $tenant->nama_tenant }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $tenant->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $tenant->kantin->nama_kantin ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $tenant->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No Tenant data found.</td>
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
