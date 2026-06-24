@extends('layouts.superadmin')

@section('title', 'Kantin Management')
@section('breadcrumb', 'Kantin')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">All Kantin Data</h2>
        <p class="text-sm text-gray-500 mt-1">Read-only view of all Kantin.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Nama Kantin</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Lokasi</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Dibuat</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($kantins as $kantin)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $kantin->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $kantin->nama_kantin }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $kantin->lokasi }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $kantin->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($kantin->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $kantin->created_at->format('d M Y') }}</td>
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
