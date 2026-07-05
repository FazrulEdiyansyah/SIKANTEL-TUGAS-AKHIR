@extends('layouts.dashboard')

@section('title', 'Dashboard Tenant - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="dashboard" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Dashboard Tenant</h1>
            <p class="text-[15px] text-gray-500 font-medium">Pantau pesanan masuk, status pesanan, dan ringkasan penjualan tenant Anda.</p>
        </div>

        <!-- Tenant Profile Summary Card -->
        <div class="bg-white rounded-[16px] shadow-sm border border-gray-100 p-4 flex items-center gap-4 shrink-0 min-w-[300px]">
            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 border border-gray-100 shrink-0">
                @if($tenant->foto)
                    <img src="{{ asset('storage/' . $tenant->foto) }}" alt="{{ $tenant->nama_tenant }}" class="w-full h-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant->nama_tenant) }}&background=fee2e2&color=dc2626&bold=true" class="w-full h-full object-cover">
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 text-sm">{{ $tenant->nama_tenant }}</h3>
                <p class="text-xs text-gray-500 font-medium">{{ $tenant->kantin->nama_kantin ?? 'Belum ada kantin' }}</p>
            </div>
            <div class="pl-4 border-l border-gray-100 flex flex-col items-center justify-center">
                <p class="text-[10px] text-gray-400 font-medium uppercase mb-1">Status Toko</p>
                <button onclick="document.getElementById('toggleStatusModal').classList.remove('hidden')" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ $tenant->is_open ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                    {{ $tenant->is_open ? 'BUKA' : 'TUTUP' }}
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-blue-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-receipt text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 cursor-pointer hover:bg-gray-100 transition-colors">
                    <i class="ph-fill ph-file-text"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Pesanan Hari Ini</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">{{ $pesananHariIni }}</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">pesanan</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-yellow-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-clock text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center text-gray-300">
                    <i class="ph ph-caret-right font-bold"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Menunggu Diproses</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">{{ $menungguDiproses }}</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">pesanan</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-orange-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-fork-knife text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center text-gray-300">
                    <i class="ph ph-caret-right font-bold"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Sedang Disiapkan</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">{{ $sedangDisiapkan }}</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">pesanan</p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:border-green-200 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill ph-trend-up text-2xl"></i>
                </div>
                <div class="w-8 h-8 flex items-center justify-center text-gray-300">
                    <i class="ph ph-caret-right font-bold"></i>
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-500 mb-1">Penjualan Hari Ini</p>
                <h3 class="text-[28px] font-bold text-gray-900 leading-tight">Rp{{ number_format($penjualanHariIni, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 font-medium mt-1">total penjualan</p>
            </div>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Pesanan Terbaru (span 2) -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h3>
                    <a href="#" class="text-[13px] font-bold text-telkom-red hover:text-telkom-maroon flex items-center transition-colors">
                        Lihat Semua <i class="ph ph-caret-right ml-1"></i>
                    </a>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Layanan</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Menu</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                                <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pesananTerbaru as $pesanan)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-red-50 text-telkom-red flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ substr($pesanan->user->name ?? 'G', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-[13px] font-bold text-gray-900">{{ $pesanan->user->name ?? 'Guest' }}</p>
                                                <p class="text-[11px] text-gray-500">{{ \Carbon\Carbon::parse($pesanan->created_at)->format('H:i') }} WIB</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6">
                                        <div class="flex items-center gap-1.5 text-[12px] font-medium text-gray-600">
                                            <i class="ph-fill {{ $pesanan->order_type == 'dine-in' ? 'ph-armchair text-orange-500' : 'ph-bag text-blue-500' }}"></i>
                                            {{ $pesanan->order_type == 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang' }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-6">
                                        <p class="text-[12px] font-semibold text-gray-900">{{ count($pesanan->items) }} menu</p>
                                        <p class="text-[11px] text-gray-500 line-clamp-1 w-32" title="{{ $pesanan->items->pluck('nama_menu')->join(', ') }}">
                                            {{ $pesanan->items->pluck('nama_menu')->join(', ') }}
                                        </p>
                                    </td>
                                    <td class="py-3 px-6">
                                        <p class="text-[13px] font-bold text-telkom-red">Rp{{ number_format($pesanan->total_price, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        @if($pesanan->status == 'success')
                                            <span class="inline-flex px-2 py-1 bg-red-50 text-telkom-red border border-red-100 rounded-md text-[10px] font-bold uppercase tracking-wider">Baru Masuk</span>
                                        @elseif($pesanan->status == 'preparing')
                                            <span class="inline-flex px-2 py-1 bg-yellow-50 text-yellow-600 border border-yellow-100 rounded-md text-[10px] font-bold uppercase tracking-wider">Disiapkan</span>
                                        @elseif($pesanan->status == 'ready')
                                            <span class="inline-flex px-2 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-md text-[10px] font-bold uppercase tracking-wider">Siap Ambil</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 bg-green-50 text-green-600 border border-green-100 rounded-md text-[10px] font-bold uppercase tracking-wider">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('tenant.orders.show', $pesanan->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:text-telkom-red hover:bg-red-50 transition-colors" title="Lihat Detail">
                                            <i class="ph-bold ph-caret-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                                                <i class="ph ph-receipt text-3xl"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Pesanan</p>
                                            <p class="text-xs font-medium text-gray-500">Pesanan yang masuk akan otomatis muncul di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 text-center">
                    <a href="#" class="text-[13px] font-bold text-telkom-red hover:text-telkom-maroon inline-flex items-center transition-colors">Lihat Semua Pesanan <i class="ph ph-caret-right ml-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Right Column (span 1) -->
        <div class="flex flex-col gap-6">
            <!-- Ringkasan Menu -->
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Ringkasan Menu</h3>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                <i class="ph-fill ph-clipboard-text text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Total Menu</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">{{ $totalMenu }}</span>
                            <p class="text-[11px] text-gray-400 font-medium">menu</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-500">
                                <i class="ph-fill ph-check-circle text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Menu Tersedia</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">{{ $menuTersedia }}</span>
                            <p class="text-[11px] text-gray-400 font-medium">menu</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-telkom-red">
                                <i class="ph-fill ph-x-circle text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Menu Habis</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">{{ $menuHabis }}</span>
                            <p class="text-[11px] text-gray-400 font-medium">menu</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('tenant.menu.index') }}" class="w-full flex items-center justify-center py-2.5 rounded-xl border border-telkom-red text-telkom-red font-bold text-[13px] hover:bg-red-50 transition-colors">
                    Kelola Menu
                </a>
            </div>

            <!-- Ringkasan Penjualan -->
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan Penjualan</h3>
                    <button class="flex items-center gap-1 text-[11px] font-bold text-gray-600 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1.5 hover:bg-gray-100 transition-colors">
                        Hari Ini <i class="ph ph-caret-down ml-0.5"></i>
                    </button>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-500">
                                <i class="ph-fill ph-money text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Total Penjualan</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-[15px]">Rp{{ number_format($penjualanHariIni, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                                <i class="ph-fill ph-check-square text-xl"></i>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-700">Pesanan Selesai</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 text-lg leading-none">{{ $pesananSelesaiHariIni }}</span>
                            <p class="text-[11px] text-gray-400 font-medium">pesanan</p>
                        </div>
                    </div>
                </div>

                <button class="w-full py-2.5 rounded-xl border border-telkom-red text-telkom-red font-bold text-[13px] hover:bg-red-50 transition-colors">
                    Lihat Rekap Penjualan
                </button>
            </div>
        </div>

    </div>

    <!-- Toggle Status Modal -->
    <div id="toggleStatusModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden animate-fade-in-up">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-{{ $tenant->is_open ? 'red' : 'green' }}-100 flex items-center justify-center mx-auto mb-4 text-{{ $tenant->is_open ? 'red' : 'green' }}-600">
                    <i class="ph-fill ph-storefront text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ $tenant->is_open ? 'Tutup Toko?' : 'Buka Toko?' }}
                </h3>
                <p class="text-sm text-gray-500">
                    {{ $tenant->is_open ? 'Pelanggan tidak akan bisa memesan menu Anda sampai Anda membukanya kembali.' : 'Toko Anda akan terlihat oleh pelanggan dan mereka dapat mulai memesan.' }}
                </p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('toggleStatusModal').classList.add('hidden')" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    Batal
                </button>
                <form action="{{ route('tenant.toggle-status') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white {{ $tenant->is_open ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} rounded-xl shadow-sm transition-colors">
                        Ya, {{ $tenant->is_open ? 'Tutup Toko' : 'Buka Toko' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
