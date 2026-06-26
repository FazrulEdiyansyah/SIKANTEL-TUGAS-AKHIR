@extends('layouts.superadmin')

@section('title', 'Orders Management')
@section('breadcrumb', 'Orders')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">All Orders Data</h2>
        <p class="text-sm text-gray-500 mt-1">Read-only view of all orders from across the platform.</p>
    </div>
    
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('superadmin.orders.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID Pesanan, Nama Tenant, atau Customer..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white outline-none transition-shadow hover:shadow-sm">
            </div>
            
            <!-- Status -->
            <div class="relative min-w-[150px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-award"></i>
                </div>
                <select name="status" class="w-full pl-10 pr-8 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-shadow hover:shadow-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <button type="submit" class="hidden">Filter</button>
        </form>
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
                    <th class="px-6 py-4 font-medium border-b border-gray-100 text-right">Actions</th>
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
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('superadmin.orders.show', $order->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-circle-info mr-1.5"></i> Detail
                        </a>
                    </td>
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
