@extends('layouts.dashboard')

@section('title', 'Data Tenant - Approver')

@section('sidebar_menu')
    <x-sidebar.approver active="tenant" />
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Data Tenant</h1>
        <p class="text-[15px] text-gray-500 font-medium">Read-only view of all tenants and their parent kantin.</p>
    </div>

    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tenant Name</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Owner (User)</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kantin</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-4 px-4 text-sm font-semibold text-gray-600">#{{ $tenant->id }}</td>
                        <td class="py-4 px-4 text-sm font-bold text-gray-900">{{ $tenant->nama_tenant }}</td>
                        <td class="py-4 px-4 text-sm font-medium text-gray-700">{{ $tenant->user->name ?? '-' }}</td>
                        <td class="py-4 px-4 text-sm font-medium text-gray-700">{{ $tenant->kantin->nama_kantin ?? '-' }}</td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $tenant->status == 'aktif' ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $tenant->status == 'aktif' ? 'bg-green-500' : 'bg-red-500' }} mr-1.5"></span> {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <p class="text-base font-bold text-gray-900 mb-1">Belum ada data Tenant</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $tenants->links() }}
        </div>
    </div>
@endsection
