@extends('layouts.superadmin')

@section('title', 'Pencairan Dana')
@section('breadcrumb', 'Pencairan Dana')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">All Pencairan Dana Data</h2>
        <p class="text-sm text-gray-500 mt-1">Read-only view of all fund disbursements.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Tenant</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Amount</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Period</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Date</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($pencairans as $p)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $p->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $p->tenant->nama_tenant ?? '-' }}</td>
                    <td class="px-6 py-4 font-medium text-green-600">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $p->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $p->periode_start }} - {{ $p->periode_end }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $p->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No Pencairan Dana records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $pencairans->links() }}
    </div>
</div>
@endsection
