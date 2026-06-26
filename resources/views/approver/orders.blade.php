@extends('layouts.dashboard')

@section('title', 'Data Pesanan - Approver')

@section('sidebar_menu')
    <x-sidebar.approver active="orders" />
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Data Pesanan</h1>
        <p class="text-[15px] text-gray-500 font-medium">Read-only view of all orders from across the platform.</p>
    </div>

    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tenant</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total Price</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-4 px-4 text-sm font-semibold text-gray-600">{{ $order->order_id }}</td>
                        <td class="py-4 px-4 text-sm font-medium text-gray-700">{{ $order->tenant->nama_tenant ?? '-' }}</td>
                        <td class="py-4 px-4 text-sm font-medium text-gray-700">{{ $order->user->name ?? '-' }}</td>
                        <td class="py-4 px-4 text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="py-4 px-4">
                            @php
                                $statusColors = [
                                    'selesai' => 'bg-green-50 text-green-600 border-green-200',
                                    'diproses' => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'dibatalkan' => 'bg-red-50 text-red-600 border-red-200',
                                ];
                                $color = $statusColors[$order->order_status] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $color }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm font-medium text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <p class="text-base font-bold text-gray-900 mb-1">Belum ada data Pesanan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
