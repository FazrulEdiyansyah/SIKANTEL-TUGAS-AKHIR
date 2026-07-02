@extends('layouts.dashboard')

@section('title', 'Detail Pesanan - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="pesanan" />
@endsection

@section('content')
<div class="font-sans">
    <div class="pb-8 max-w-[1000px] mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('tenant.orders.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">
                <i class="ph-bold ph-arrow-left"></i>
            </a>
            <h2 class="text-xl font-bold text-gray-900">Detail Pesanan #{{ substr($order->order_id, -8) }}</h2>
        </div>
            <div class="flex flex-col lg:flex-row gap-6">
                
                <!-- Left: Order Items & Customer -->
                <div class="flex-1 space-y-6">
                    
                    <!-- Customer Info -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-6">Informasi Pelanggan</h3>
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-red-50 text-telkom-red flex items-center justify-center text-xl font-black">
                                {{ substr($order->user->name ?? 'G', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $order->user->name ?? 'Guest' }}</h4>
                                <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                                    <div class="flex items-center gap-1.5">
                                        <i class="ph-fill {{ $order->order_type == 'dine-in' ? 'ph-armchair' : 'ph-bag' }}"></i>
                                        {{ $order->order_type == 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang' }}
                                    </div>
                                    @if($order->order_type == 'dine-in')
                                        <span>•</span>
                                        <div class="flex items-center gap-1.5">
                                            Meja {{ $order->table_number ?? '-' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-6">Daftar Menu</h3>
                        
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                            <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-100">
                                <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($item->menu && $item->menu->foto)
                                        <img src="{{ asset('storage/' . $item->menu->foto) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ph ph-image text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-bold text-gray-900">{{ $item->nama_menu }}</h4>
                                        <span class="font-black text-gray-900">x{{ $item->quantity }}</span>
                                    </div>
                                    <p class="text-sm text-telkom-red font-bold mb-2">Rp{{ number_format($item->harga, 0, ',', '.') }}</p>
                                    
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
                                        <div class="bg-yellow-50 p-2.5 rounded-lg border border-yellow-100 flex items-start gap-2">
                                            <i class="ph-fill ph-warning-circle text-yellow-600 mt-0.5"></i>
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
                <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                    
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-6">Ringkasan</h3>
                        
                        <div class="flex items-center justify-between mb-3 text-sm">
                            <span class="text-gray-500">Waktu Pesan</span>
                            <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100 text-sm">
                            <span class="text-gray-500">Tanggal</span>
                            <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <span class="font-bold text-gray-900">Total Pembayaran</span>
                            <span class="text-xl font-black text-telkom-red">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>

                        @if($order->order_status == 'siap_diambil' || $order->order_status == 'selesai')
                        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-xl text-center">
                            <p class="text-xs text-gray-500 mb-1 font-bold">PIN PENGAMBILAN PELANGGAN</p>
                            <p class="text-4xl font-black text-gray-900 tracking-[0.2em]">{{ $order->pickup_pin ?? '---' }}</p>
                            <p class="text-[11px] text-gray-400 mt-2">Pastikan cocok sebelum menyerahkan pesanan.</p>
                        </div>
                        @endif

                        <!-- Update Status Form -->
                        @if($order->order_status !== 'selesai')
                            @php
                                $nextStatus = '';
                                $btnText = '';
                                if ($order->order_status == 'belum_diproses') {
                                    $nextStatus = 'diproses';
                                    $btnText = 'Proses Pesanan';
                                } else if ($order->order_status == 'diproses') {
                                    if ($order->order_type == 'dine-in') {
                                        $nextStatus = !empty($order->table_number) ? 'selesai' : 'siap_diambil';
                                        $btnText = !empty($order->table_number) ? 'Selesaikan Pesanan' : 'Tandai Ambil Sendiri';
                                    } else {
                                        $nextStatus = 'siap_diambil';
                                        $btnText = 'Tandai Siap Diambil';
                                    }
                                } else if ($order->order_status == 'siap_diambil') {
                                    $nextStatus = 'selesai';
                                    $btnText = 'Selesaikan Pesanan';
                                }
                            @endphp
                            
                            <form action="{{ route('tenant.orders.update-status', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="order_status" value="{{ $nextStatus }}">
                                <button type="submit" class="w-full py-3.5 bg-telkom-red hover:bg-red-700 text-white font-bold rounded-xl transition-colors shadow-lg">
                                    {{ $btnText }}
                                </button>
                            </form>
                        @else
                            <div class="w-full py-3.5 bg-green-50 border border-green-100 text-green-600 text-center font-bold rounded-xl">
                                <i class="ph-bold ph-check-circle mr-1"></i> Pesanan Telah Selesai
                            </div>
                        @endif
                    </div>
                    
                </div>
            </div>
    </div>
</div>
@endsection
