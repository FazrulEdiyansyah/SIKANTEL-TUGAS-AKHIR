@extends('layouts.dashboard')

@section('title', 'Data Kantin - Approver')

@section('sidebar_menu')
    <x-sidebar.approver active="kantin" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Data Kantin</h1>
        <p class="text-[15px] text-gray-500 font-medium">Read-only view of all Kantin data.</p>
    </div>

    <!-- Main Content Box (Table Section) -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('kaur.kantin.index') }}" class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kantin..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red focus:bg-white transition-all">
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
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Nama Kantin</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Total Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kantins as $index => $kantin)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $kantins->firstItem() + $index }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 mr-4 overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center">
                                        @if($kantin->foto)
                                            <img src="{{ asset('storage/' . $kantin->foto) }}" alt="{{ $kantin->nama_kantin }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60" alt="no image">
                                        @endif
                                    </div>
                                    <span class="font-bold text-[14px] text-gray-900">{{ $kantin->nama_kantin }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $kantin->lokasi }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600 text-center">
                                @if($kantin->tenants_count > 0)
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[12px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ $kantin->tenants_count }} Tenant
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <x-status-badge :status="$kantin->status" />
                            </td>
                        </tr>
                    @empty
                    <x-empty-state icon="ph ph-storefront" title="Belum ada data kantin" message="Silakan tambah data kantin baru." :colspan="5" />
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-5 border-t border-gray-100">
            {{ $kantins->links() }}
        </div>
    </div>
@endsection
