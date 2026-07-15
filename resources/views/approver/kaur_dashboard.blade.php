@extends('layouts.dashboard')

@section('title', 'Kaur Dashboard - Approval Pencairan')

@section('sidebar_menu')
    <x-sidebar.approver active="dashboard" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Antrean Persetujuan (Kaur)</h1>
        <p class="text-[15px] text-gray-500 font-medium">Daftar usulan pencairan dana yang menunggu persetujuan tingkat 1 (Kaur).</p>
    </div>

    <!-- Main Content Box (Table Section) -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('kaur.dashboard') }}" class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Batch ID..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all">
                <button type="submit" class="hidden"></button>
            </div>
            
            <div class="flex items-center space-x-3 w-full md:w-auto">
                <!-- Refresh Button -->
                <a href="{{ url()->current() }}" class="w-12 h-12 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                    <i class="ph ph-arrows-clockwise text-xl"></i>
                </a>
            </div>
        </form>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Periode Laporan</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Judul Laporan</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Batch ID</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Keterangan Kantin</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Dana Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pencairans as $index => $p)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $pencairans->firstItem() + $index }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-700">{{ \Carbon\Carbon::parse($p->start_date)->format('d M y') }} - {{ \Carbon\Carbon::parse($p->end_date)->format('d M y') }}</td>
                            <td class="py-4 px-6"><span class="text-[14px] font-bold text-gray-900">{{ $p->judul ?? '-' }}</span></td>
                            <td class="py-4 px-6">
                                <span class="text-[14px] font-bold text-gray-900 block">{{ $p->batch_id }}</span>
                                <span class="text-[11px] text-gray-500 font-medium">{{ $p->tenant_count }} Tenant</span>
                            </td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $p->keterangan_kantin }}</td>
                            <td class="py-4 px-6 text-[14px] font-semibold text-gray-900">Rp{{ number_format($p->total_penjualan, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-[14px] font-black text-gray-900">Rp{{ number_format($p->dana_tenant, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-center">
                                <x-status-badge :status="$p->status" />
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('kaur.pencairan.show', $p->batch_id) }}" class="px-3 py-1.5 text-xs font-bold text-telkom-red border border-red-200 rounded-lg hover:bg-red-50 transition-colors inline-flex items-center justify-center">
                                        <i class="ph ph-eye mr-1.5 text-sm"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    <x-empty-state icon="ph ph-file-text" title="Belum ada antrean" message="Tidak ada laporan pencairan dana yang perlu diproses saat ini." :colspan="9" />
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-5 border-t border-gray-100">
            {{ $pencairans->links() }}
        </div>
    </div>
@endsection
