@extends('layouts.superadmin')

@section('title', 'Detail Pesanan')
@section('breadcrumb', 'Orders / Detail')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-5xl">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Detail Pesanan #{{ substr($order->order_id, -8) }}</h2>
            <p class="text-sm text-gray-500 mt-1">Tenant: <span class="font-semibold">{{ $order->tenant->nama_tenant ?? '-' }}</span></p>
        </div>
        <a href="{{ route('superadmin.orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="flex flex-col lg:flex-row">
        <!-- Left: Order Items & Customer -->
        <div class="flex-1 p-6 border-b lg:border-b-0 lg:border-r border-gray-100">
            
            <!-- Customer Info -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-gray-900 mb-4 border-b pb-2">Informasi Pelanggan</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                        {{ substr($order->user->name ?? 'G', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $order->user->name ?? 'Guest' }}</h4>
                        <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid {{ $order->order_type == 'dine-in' ? 'fa-chair' : 'fa-bag-shopping' }}"></i>
                                {{ $order->order_type == 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang' }}
                            </div>
                            @if($order->order_type == 'dine-in')
                                <span>•</span>
                                <div class="flex items-center gap-1.5 font-medium">
                                    Meja {{ $order->table_number ?? '-' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-4 border-b pb-2">Daftar Menu</h3>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                        <div class="w-16 h-16 rounded-xl bg-white border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                            @if($item->menu && $item->menu->foto)
                                <img src="{{ asset('storage/' . $item->menu->foto) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-image text-gray-300 text-2xl"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-gray-800">{{ $item->nama_menu }}</h4>
                                <span class="font-black text-gray-700 bg-gray-200 px-2 py-0.5 rounded text-xs">x{{ $item->quantity }}</span>
                            </div>
                            <p class="text-sm text-blue-600 font-bold mb-2">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                            
                            @if($item->selected_options)
                                @php 
                                    $opts = is_array($item->selected_options) ? $item->selected_options : json_decode($item->selected_options, true);
                                @endphp
                                @if(is_array($opts))
                                    <div class="flex flex-col gap-0.5 mb-2">
                                        @foreach($opts as $opt)
                                            <p class="text-xs text-gray-500"><span class="font-semibold">{{ $opt['label'] ?? '' }}:</span> {{ $opt['value'] ?? '' }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                            
                            @if($item->catatan)
                                <div class="bg-yellow-50 p-2.5 rounded-lg border border-yellow-100 flex items-start gap-2 mt-2">
                                    <i class="fa-solid fa-circle-exclamation text-yellow-500 mt-0.5"></i>
                                    <p class="text-xs text-yellow-800"><span class="font-bold">Catatan:</span> {{ $item->catatan }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right: Summary & Actions -->
        <div class="w-full lg:w-[320px] p-6 bg-gray-50/30">
            
            <h3 class="text-sm font-bold text-gray-900 mb-4 border-b pb-2">Ringkasan Pesanan</h3>
            
            <div class="space-y-3 text-sm mb-6 pb-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Waktu Pesan</span>
                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Status</span>
                    @php
                        $statusColors = [
                            'selesai' => 'bg-green-100 text-green-700',
                            'diproses' => 'bg-orange-100 text-orange-700',
                            'dibatalkan' => 'bg-red-100 text-red-700',
                        ];
                        $color = $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase {{ $color }}">
                        {{ $order->order_status }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-6">
                <span class="font-bold text-gray-900">Total Pembayaran</span>
                <span class="text-lg font-black text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>

            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm text-blue-800">
                <i class="fa-solid fa-circle-info mr-2"></i> Laporan pesanan ini bersifat read-only untuk Superadmin. Hanya Tenant atau Pelanggan yang bisa membatalkan atau mengubah status pesanan.
            </div>
            
        </div>
    </div>
</div>
@endsection
