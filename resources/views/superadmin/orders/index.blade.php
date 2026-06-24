@extends('layouts.superadmin')

@section('title', 'Orders Management')
@section('breadcrumb', 'Orders')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">All Orders Data</h2>
        <p class="text-sm text-gray-500 mt-1">Read-only view of all orders from across the platform.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Order ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Tenant</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Customer</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Total Price</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Date</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $order->order_id }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $order->tenant->nama_tenant ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $order->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'selesai' => 'bg-green-100 text-green-700',
                                'diproses' => 'bg-orange-100 text-orange-700',
                                'dibatalkan' => 'bg-red-100 text-red-700',
                            ];
                            $color = $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No Orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
</div>
@endsection
