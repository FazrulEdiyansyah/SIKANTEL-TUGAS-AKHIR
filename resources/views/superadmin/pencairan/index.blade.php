@extends('layouts.superadmin')

@section('title', 'Laporan Pencairan Dana')
@section('breadcrumb', 'Pencairan Dana')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Riwayat Pencairan Dana</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola laporan pencairan dana tenant.</p>
        </div>
        <a href="{{ route('superadmin.pencairan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center">
            <i class="ph ph-plus font-bold mr-2"></i> Buat Laporan Pencairan
        </a>
    </div>
    
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('superadmin.pencairan.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Batch ID atau Judul..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white outline-none transition-all">
            </div>
            
            <!-- Status -->
            <div class="relative min-w-[160px]">
                <select name="status" class="w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-all" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="proposed" {{ request('status') === 'proposed' ? 'selected' : '' }}>Diproses (Menunggu Kaur)</option>
                    <option value="approved_kaur" {{ request('status') === 'approved_kaur' ? 'selected' : '' }}>Diproses (Menunggu Kabag)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>
            
            <!-- Start Date -->
            <div class="relative min-w-[140px]">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white outline-none transition-all cursor-pointer" onchange="this.form.submit()" title="Dari Tanggal">
            </div>
            
            <!-- End Date -->
            <div class="relative min-w-[140px]">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white outline-none transition-all cursor-pointer" onchange="this.form.submit()" title="Sampai Tanggal">
            </div>

            <!-- Reset -->
            <a href="{{ route('superadmin.pencairan.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors shrink-0" title="Reset Filter">
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
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Referensi / Batch ID</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Kantin</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pencairan_danas as $index => $item)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $pencairan_danas->firstItem() + $index }}</td>
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($item->start_date)->format('d M y') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d M y') }}
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex flex-col">
                            <span class="font-bold text-[14px] text-gray-800">{{ $item->batch_id }}</span>
                            <span class="text-xs text-gray-500 font-medium">{{ $item->judul ?? '-' }} ({{ $item->tenant_count }} Tenant)</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $item->keterangan_kantin }}</td>
                    <td class="py-4 px-6 font-bold text-[14px] text-gray-800">
                        Rp{{ number_format($item->total_penjualan, 0, ',', '.') }}
                        <span class="block text-[11px] text-gray-500 font-normal mt-0.5">Dana Tenant: Rp{{ number_format($item->dana_tenant, 0, ',', '.') }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <x-status-badge :status="$item->status" />
                    </td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('superadmin.pencairan.show', $item->batch_id) }}" class="inline-flex items-center px-3 py-1.5 text-[13px] font-bold text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="ph ph-eye mr-1.5"></i> Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <x-empty-state icon="ph ph-file-text" title="Belum ada data" message="Tidak ada laporan pencairan dana di status ini." :colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-5 border-t border-gray-100">
        {{ $pencairan_danas->links() }}
    </div>
</div>
@endsection
