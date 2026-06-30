@extends('layouts.dashboard')

@section('title', 'Kabag Dashboard - Approval Pencairan')

@section('sidebar_menu')
    <x-sidebar.approver active="riwayat" />
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Riwayat Persetujuan (Kabag)</h1>
        <p class="text-[15px] text-gray-500 font-medium">Daftar riwayat laporan pencairan dana yang telah diproses oleh Kabag.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Batch ID</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Dana Tenant</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Jml Tenant</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pencairans as $p)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-4 px-4 text-sm font-semibold text-gray-600">{{ $p->batch_id }}</td>
                        <td class="py-4 px-4 text-sm font-medium text-gray-700">{{ \Carbon\Carbon::parse($p->start_date)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($p->end_date)->format('d/m/y') }}</td>
                        <td class="py-4 px-4 text-sm font-semibold text-gray-900">Rp {{ number_format($p->total_penjualan, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-[14px] font-black text-gray-900">Rp {{ number_format($p->dana_tenant, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-[14px] font-bold text-gray-700">{{ $p->tenant_count }} Tenant</td>
                        <td class="py-4 px-4">
                            @if($p->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            <a href="{{ route('kabag.pencairan.show', $p->batch_id) }}" class="px-3 py-1.5 text-xs font-bold text-telkom-red border border-red-200 rounded-lg hover:bg-red-50 transition-colors inline-flex items-center justify-center">
                                <i class="ph ph-eye mr-1.5 text-sm"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <p class="text-base font-bold text-gray-900 mb-1">Belum ada antrean</p>
                            </div>
                        </td>
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
