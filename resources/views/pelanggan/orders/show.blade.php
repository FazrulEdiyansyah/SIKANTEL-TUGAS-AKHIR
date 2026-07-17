@extends('layouts.pelanggan')

@section('title', 'Detail Pesanan - SIKANTEL')

@section('content')

@php
    $firstOrder = $orders->first();
    $totalPayment = $orders->sum('total_price');
@endphp

<div class="pt-24 pb-20 bg-gray-50 min-h-screen">
    <div class="max-w-[1200px] mx-auto px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-6">
            <a href="{{ route('pelanggan.orders.index') }}" class="hover:text-telkom-red transition-colors">Pesanan Saya</a>
            <span>&gt;</span>
            <span class="font-bold text-gray-900">Detail Pesanan</span>
        </div>

        <!-- Back Button & Title -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('pelanggan.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-telkom-red text-telkom-red hover:bg-red-50 text-sm font-bold rounded-xl transition-colors mb-6">
                    <i class="ph-bold ph-arrow-left"></i> Kembali ke Pesanan Saya
                </a>
                <h1 class="text-3xl font-black text-gray-900 mb-2">Detail Pesanan</h1>
                <p class="text-gray-500 text-sm">Transaksi ID: <span class="font-bold text-gray-900">{{ $orderId }}</span></p>
            </div>
            <div class="flex items-center gap-8 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-1">
                        <i class="ph ph-calendar-blank"></i> Tanggal
                    </div>
                    <p class="font-bold text-gray-900 text-sm">{{ \Carbon\Carbon::parse($firstOrder->created_at)->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-1">
                        <i class="ph ph-clock"></i> Waktu
                    </div>
                    <p class="font-bold text-gray-900 text-sm">{{ \Carbon\Carbon::parse($firstOrder->created_at)->format('H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Left Column (Tenant Blocks) -->
            <div class="flex-1 space-y-6">
                
                @foreach($orders as $order)
                <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
                    <!-- Header Tenant & Status -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-gray-100 border border-gray-50 flex items-center justify-center overflow-hidden shrink-0">
                                @if($order->tenant && $order->tenant->foto)
                                    <img src="{{ asset('storage/' . $order->tenant->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ph ph-storefront text-2xl text-gray-400"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $order->tenant->nama_tenant ?? 'Tenant Tidak Diketahui' }}</h4>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-1">
                                    <i class="ph-bold ph-fork-knife text-telkom-red"></i>
                                    <span>{{ $order->order_type == 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-left sm:text-right">
                            @php
                                $statusText = '';
                                $statusClass = '';
                                $statusIcon = '';
                                
                                if ($order->payment_status == 'pending') {
                                    $statusText = 'Menunggu Pembayaran';
                                    $statusClass = 'bg-orange-50 text-orange-600';
                                    $statusIcon = 'ph-clock';
                                } else if ($order->payment_status == 'success') {
                                    if ($order->order_status == 'belum_diproses') {
                                        $statusText = 'Pesanan Diterima';
                                        $statusClass = 'bg-blue-50 text-blue-600';
                                        $statusIcon = 'ph-check-circle';
                                    } else if ($order->order_status == 'diproses') {
                                        $statusText = 'Sedang Disiapkan';
                                        $statusClass = 'bg-yellow-50 text-yellow-600';
                                        $statusIcon = 'ph-cooking-pot';
                                    } else if ($order->order_status == 'siap_diambil') {
                                        $statusText = 'Siap Diambil';
                                        $statusIcon = 'ph-shopping-bag';
                                        $statusClass = 'bg-teal-50 text-teal-600';
                                    } else if ($order->order_status == 'selesai') {                                
                                        $statusText = 'Selesai';
                                        $statusClass = 'bg-green-50 text-green-600';
                                        $statusIcon = 'ph-check-circle';
                                    }
                                } else {
                                    $statusText = 'Dibatalkan';
                                    $statusClass = 'bg-red-50 text-red-600';
                                    $statusIcon = 'ph-x-circle';
                                }
                            @endphp
                            
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold {{ $statusClass }}">
                                <i class="ph-bold {{ $statusIcon }}"></i>
                                <span>{{ $statusText }}</span>
                            </div>
                            
                            @if($order->order_status == 'siap_diambil' && $order->order_type != 'dine-in')
                            <div class="mt-2 text-xs font-bold text-gray-500">
                                PIN: <span class="text-base text-[#E31E24] tracking-widest ml-1">{{ $order->pickup_pin ?? '---' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ringkasan Menu -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[400px]">
                            <thead>
                                <tr class="text-xs text-gray-500 border-b border-gray-100">
                                    <th class="pb-3 font-normal">Menu</th>
                                    <th class="pb-3 font-normal text-center">Harga</th>
                                    <th class="pb-3 font-normal text-center">Jumlah</th>
                                    <th class="pb-3 font-normal text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr class="border-b border-gray-50 last:border-0">
                                    <td class="py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                                @if($item->menu && $item->menu->foto)
                                                    <img src="{{ asset('storage/' . $item->menu->foto) }}" class="w-full h-full object-cover">
                                                @else
                                                    <i class="ph ph-image text-gray-400"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 text-sm mb-0.5">{{ $item->nama_menu }}</h4>
                                                
                                                @if($item->selected_options)
                                                    @php 
                                                        $opts = is_array($item->selected_options) ? $item->selected_options : json_decode($item->selected_options, true);
                                                        $grouped = [];
                                                        if (is_array($opts)) {
                                                            foreach($opts as $o) {
                                                                $lbl = $o['label'] ?? 'Pilihan';
                                                                $val = $o['value'] ?? '';
                                                                if (isset($o['qty']) && $o['qty'] > 1) {
                                                                    $val .= " ({$o['qty']}x)";
                                                                }
                                                                $grouped[$lbl][] = $val;
                                                            }
                                                        }
                                                    @endphp
                                                    @if(!empty($grouped))
                                                        <div class="flex flex-col gap-0.5 mt-1">
                                                            @foreach($grouped as $label => $values)
                                                                <p class="text-[11px] text-gray-500">
                                                                    <span class="font-semibold">{{ $label }}:</span> 
                                                                    {{ implode(', ', $values) }}
                                                                </p>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif
                                                
                                                @if($item->catatan)
                                                    <div class="flex items-start gap-1 text-[11px] text-gray-500 mt-1">
                                                        <i class="ph ph-pencil-simple mt-0.5"></i>
                                                        <p><span class="font-semibold">Catatan:</span> {{ $item->catatan }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center font-bold text-gray-900 text-sm">
                                        Rp{{ number_format($item->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 text-center font-bold text-gray-900 text-sm">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-4 text-right font-bold text-telkom-red text-sm">
                                        Rp{{ number_format($item->harga * $item->quantity, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($order->order_status == 'selesai')
                        @if(!$order->review)
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <h4 class="text-sm font-bold text-gray-900 mb-4">Beri Ulasan untuk {{ $order->tenant->nama_tenant }}</h4>
                            <form action="{{ route('pelanggan.orders.review', $order->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <select name="rating" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-telkom-red font-medium">
                                        <option value="5">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                        <option value="4">⭐⭐⭐⭐ Baik</option>
                                        <option value="3">⭐⭐⭐ Cukup</option>
                                        <option value="2">⭐⭐ Kurang</option>
                                        <option value="1">⭐ Sangat Kurang</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full py-3 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-colors shadow-md">
                                    Kirim Ulasan
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <h4 class="text-sm font-bold text-gray-900 mb-3">Ulasan Anda untuk {{ $order->tenant->nama_tenant }}</h4>
                            <div class="flex text-yellow-400 text-sm mb-2 gap-1">
                                @for($i=1; $i<=5; $i++)
                                    <i class="{{ $i <= $order->review->rating ? 'ph-fill' : 'ph' }} ph-star text-lg"></i>
                                @endfor
                            </div>

                        </div>
                        @endif
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Right Column (Ringkasan Pembayaran) -->
            <div class="w-full lg:w-[350px] shrink-0">
                <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-sm font-bold text-gray-900 mb-6">Ringkasan Transaksi</h3>
                    
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500">Subtotal</span>
                        <span class="text-sm font-bold text-gray-900">Rp{{ number_format($totalPayment, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
                        <span class="text-base font-bold text-gray-900">Total Pembayaran</span>
                        <span class="text-xl font-black text-telkom-red">Rp{{ number_format($totalPayment, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500">Metode Pembayaran</span>
                        <span class="text-sm font-bold text-gray-900">{{ strtoupper($firstOrder->payment_type ?? 'Menunggu') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Status Pembayaran</span>
                        @if($firstOrder->payment_status == 'success')
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-green-600">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div> Lunas
                            </span>
                        @elseif($firstOrder->payment_status == 'pending')
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-orange-600">
                                <div class="w-1.5 h-1.5 rounded-full bg-orange-600 animate-pulse"></div> Menunggu
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-600">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-600"></div> Gagal
                            </span>
                        @endif
                    </div>
                    
                    @if($firstOrder->payment_status == 'pending')
                        @if($firstOrder->snap_token)
                            <button type="button" onclick="window.snap.pay('{{ $firstOrder->snap_token }}')" class="w-full mt-8 py-3.5 bg-[#E31E24] hover:bg-red-700 text-white font-bold rounded-xl transition-colors shadow-lg flex items-center justify-center gap-2">
                                Lanjutkan Pembayaran
                            </button>
                        @else
                            <form action="{{ route('pelanggan.orders.pay', $orderId) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full mt-8 py-3.5 bg-[#E31E24] hover:bg-red-700 text-white font-bold rounded-xl transition-colors shadow-lg flex items-center justify-center gap-2">
                                    Lanjutkan Pembayaran
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('pelanggan.orders.cancel', $orderId) }}" method="POST" class="mt-3" onsubmit="confirmFormSubmit(event, 'Apakah Anda yakin ingin membatalkan transaksi ini?')">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-white border-2 border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-500 font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                                Batalkan Transaksi
                            </button>
                        </form>
                    @endif

                    @php
                        $allSelesai = $orders->every(fn($o) => $o->order_status == 'selesai');
                        $anyReviewable = $orders->some(fn($o) => $o->order_status == 'selesai' && !$o->review);
                    @endphp

                    @if($allSelesai || $firstOrder->payment_status == 'success')
                        <!-- We only allow reorder if at least payment was successful, meaning they actually ordered it -->
                        <form action="{{ route('pelanggan.orders.reorder', $orderId) }}" method="POST" class="mt-8">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-white border-2 border-[#E31E24] text-[#E31E24] hover:bg-red-50 font-bold rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="ph-bold ph-arrows-clockwise text-lg"></i> Pesan Ulang (Reorder Semua)
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    // Polling API every 5 seconds to auto-refresh status
    setInterval(function() {
        fetch('{{ route("pelanggan.orders.status-api", $orderId) }}')
            .then(res => res.json())
            .then(data => {
                // If payment status changed for the transaction
                if (data.payment_status !== '{{ $firstOrder->payment_status }}') {
                    window.location.reload();
                    return;
                }
                
                // If any tenant status changed
                let currentTenantStatuses = {
                    @foreach($orders as $o)
                        '{{ $o->id }}': '{{ $o->order_status }}',
                    @endforeach
                };

                let needsReload = false;
                for (let tId in currentTenantStatuses) {
                    if (data.tenant_statuses[tId] && data.tenant_statuses[tId].order_status !== currentTenantStatuses[tId]) {
                        needsReload = true;
                        break;
                    }
                }

                if (needsReload) window.location.reload();
            })
            .catch(err => console.error('Polling error:', err));
    }, 5000);
</script>

@if(session('auto_trigger_snap'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.snap !== 'undefined') {
            window.snap.pay('{{ session('auto_trigger_snap') }}');
        }
    });
</script>
@endif
@endsection
