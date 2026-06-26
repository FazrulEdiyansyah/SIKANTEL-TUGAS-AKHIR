@extends('layouts.superadmin')

@section('title', 'Laporan Pencairan Dana')
@section('breadcrumb', 'Pencairan Dana')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <form action="{{ route('superadmin.pencairan.index') }}" method="GET" class="flex flex-wrap gap-3 items-center w-full sm:w-auto">
        <!-- Search -->
        <div class="relative flex-1 min-w-[200px]">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tenant..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white outline-none transition-shadow hover:shadow-sm">
        </div>
        
        <!-- Status -->
        <div class="relative min-w-[160px]">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                <i class="fa-solid fa-filter"></i>
            </div>
            <select name="status" class="w-full pl-10 pr-8 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-shadow hover:shadow-sm" onchange="this.form.submit()">
                <option value="all">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="proposed" {{ request('status') === 'proposed' ? 'selected' : '' }}>Menunggu Kaur</option>
                <option value="approved_kaur" {{ request('status') === 'approved_kaur' ? 'selected' : '' }}>Menunggu Kabag</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="rejected_kaur" {{ request('status') === 'rejected_kaur' ? 'selected' : '' }}>Ditolak Kaur</option>
                <option value="rejected_kabag" {{ request('status') === 'rejected_kabag' ? 'selected' : '' }}>Ditolak Kabag</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </div>
        </div>

        <button type="submit" class="hidden">Filter</button>
    </form>
    
    <a href="{{ route('superadmin.pencairan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm shrink-0">
        <i class="fa-solid fa-plus mr-2"></i>
        Buat Laporan
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">Riwayat Pencairan Dana</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola laporan pencairan dana tenant.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">No</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Periode Laporan</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Nama Tenant</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Total Penjualan</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Dana Tenant 70%</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-50">
                @forelse($pencairan_danas as $index => $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">{{ $pencairan_danas->firstItem() + $index }}</td>
                    <td class="px-6 py-4 text-gray-700 font-medium">
                        {{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                                @if($item->tenant && $item->tenant->foto)
                                    <img src="{{ asset('storage/' . $item->tenant->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs"><i class="fa-solid fa-shop"></i></div>
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <span>{{ $item->tenant->nama_tenant ?? 'Unknown' }}</span>
                                <span class="text-xs text-gray-500 font-normal">{{ $item->tenant->kantin->nama_kantin ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900">
                        Rp{{ number_format($item->total_penjualan, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">
                        Rp{{ number_format($item->dana_tenant, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($item->status == 'draft')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Dibuat (Draft)</span>
                        @elseif($item->status == 'proposed')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Menunggu Kaur</span>
                        @elseif($item->status == 'approved_kaur')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Menunggu Kabag</span>
                        @elseif($item->status == 'approved' || $item->status == 'completed')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Selesai</span>
                        @elseif(in_array($item->status, ['rejected_kaur', 'rejected_kabag']))
                            <div class="flex flex-col gap-1">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 w-max">Ditolak</span>
                                <span class="text-[10px] text-red-500 line-clamp-1 max-w-[120px]" title="{{ $item->status == 'rejected_kaur' ? $item->catatan_kaur : $item->catatan_kabag }}">
                                    {{ $item->status == 'rejected_kaur' ? $item->catatan_kaur : $item->catatan_kabag }}
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('superadmin.pencairan.show', $item->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-circle-info mr-1.5"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fa-regular fa-file-lines text-4xl mb-3 text-gray-300"></i>
                        <p>Tidak ada laporan pencairan dana di status ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100">
        {{ $pencairan_danas->links() }}
    </div>
</div>
@endsection
