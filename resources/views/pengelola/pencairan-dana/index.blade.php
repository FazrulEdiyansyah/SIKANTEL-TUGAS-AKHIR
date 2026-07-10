@extends('layouts.dashboard')

@section('title', 'Laporan Pencairan Dana')

@section('sidebar_menu')
    <x-sidebar.pengelola active="laporan" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Laporan Pencairan Dana</h1>
        <p class="text-[15px] text-gray-500 font-medium">Kelola riwayat laporan pencairan dana tenant.</p>
    </div>

    <!-- Tabs and Action Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div class="flex flex-wrap items-center bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            @php
                $tabs = [
                    'all' => ['label' => 'Semua', 'active_bg' => 'bg-white text-gray-900'],
                    'draft' => ['label' => 'Draft', 'active_bg' => 'bg-[#FFFBF0] text-[#8B5E34]'],
                    'proposed' => ['label' => 'Diproses', 'active_bg' => 'bg-indigo-50 text-indigo-700'],
                    'approved' => ['label' => 'Selesai', 'active_bg' => 'bg-emerald-50 text-emerald-800'],
                    'rejected' => ['label' => 'Ditolak', 'active_bg' => 'bg-red-50 text-red-800'],
                ];
            @endphp
            @foreach($tabs as $tabKey => $tabConfig)
                <a href="{{ route('pengelola.pencairan_dana.index', array_merge(request()->except('status', 'page'), ['status' => $tabKey])) }}" 
                   class="px-5 py-2.5 text-sm font-bold transition-colors border-r border-gray-200 last:border-r-0 {{ $status === $tabKey ? $tabConfig['active_bg'] : 'bg-gray-50/50 text-gray-500 hover:bg-white' }}">
                    {{ $tabConfig['label'] }}
                    <span class="ml-1 text-[11px] font-bold opacity-70">({{ $statusCounts[$tabKey] ?? 0 }})</span>
                </a>
            @endforeach
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('pengelola.pencairan_dana.create') }}" class="px-5 py-3 bg-telkom-red hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center shrink-0">
                <i class="ph-bold ph-plus mr-2"></i> Buat Laporan Pencairan
            </a>
        </div>
    </div>

    <!-- Filters (Server-side) -->
    <form method="GET" action="{{ route('pengelola.pencairan_dana.index') }}" class="bg-white p-6 rounded-[20px] shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-6 items-end">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="w-full md:w-1/3">
            <label class="block text-xs font-bold text-gray-700 mb-2">Pencarian Laporan</label>
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Batch ID atau judul..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium transition-all">
            </div>
        </div>
        
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-bold text-gray-700 mb-2">Dari Tanggal</label>
            <div class="relative">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium transition-all cursor-pointer" onchange="this.form.submit()">
            </div>
        </div>
        
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-bold text-gray-700 mb-2">Sampai Tanggal</label>
            <div class="relative">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium transition-all cursor-pointer" onchange="this.form.submit()">
            </div>
        </div>

        <div class="w-full md:w-auto">
            <a href="{{ route('pengelola.pencairan_dana.index') }}" class="w-full md:w-11 h-[42px] shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors" title="Reset Filter">
                <i class="ph-bold ph-arrows-clockwise text-lg"></i>
            </a>
        </div>
    </form>

    <!-- Main Content Table -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Riwayat Laporan Pencairan Dana</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Periode Laporan</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Judul Laporan</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Referensi / Batch ID</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Keterangan Kantin</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Dana Tenant 70%</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-4 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pencairan_danas as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-4 px-4 text-sm font-semibold text-gray-600">{{ $pencairan_danas->firstItem() + $index }}</td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-700">
                                {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm font-bold text-gray-900">{{ $item->judul ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-50 text-telkom-red flex items-center justify-center shrink-0 border border-red-100">
                                        <i class="ph-bold ph-files"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-bold text-gray-900 block">{{ $item->batch_id }}</span>
                                        <span class="text-[11px] text-gray-500 font-medium">{{ $item->tenant_count }} Tenant</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-600">{{ $item->keterangan_kantin }}</td>
                            <td class="py-4 px-4 text-sm font-semibold text-gray-900">Rp{{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 text-[14px] font-black text-gray-900">Rp{{ number_format($item->dana_tenant, 0, ',', '.') }}</td>
                            <td class="py-4 px-4">
                                <x-status-badge :status="$item->status" />
                                @if($item->status === 'approved')
                                    <span class="block text-[10px] text-gray-400 font-medium mt-1">Approv. by Kaur & Kabag</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pengelola.pencairan_dana.show', $item->batch_id) }}" class="px-3 py-1.5 text-xs font-bold text-telkom-red border border-red-200 rounded-lg hover:bg-red-50 transition-colors flex items-center justify-center">
                                        <i class="ph ph-eye mr-1.5 text-sm"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    <x-empty-state icon="ph ph-file-text" title="Belum ada data" message="Tidak ada laporan pencairan dana di status ini." :colspan="9" />
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-2">
            {{ $pencairan_danas->links() }}
        </div>
    </div>
@endsection
