@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')
@section('breadcrumb', 'Overview')

@section('content')
<!-- Header Page -->
<div class="mb-8">
    <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Dashboard Superadmin</h1>
    <p class="text-[15px] text-gray-500 font-medium">Pantau aktivitas seluruh pengguna dan ringkasan sistem secara real-time.</p>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Card 1 -->
    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
            <i class="ph-fill ph-users text-[24px] text-blue-500"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Users</p>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalUsers }}</h3>
            <p class="text-[13px] font-medium text-gray-400">Pengguna Terdaftar</p>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center mb-4">
            <i class="ph-fill ph-shopping-cart text-[24px] text-purple-500"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Orders</p>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalOrders }}</h3>
            <p class="text-[13px] font-medium text-gray-400">Seluruh Transaksi</p>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center mb-4">
            <i class="ph-fill ph-money text-[24px] text-orange-500"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Pencairan</p>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalPencairan }}</h3>
            <p class="text-[13px] font-medium text-gray-400">Laporan Diajukan</p>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mb-4">
            <i class="ph-fill ph-storefront text-[24px] text-green-500"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Tenant & Kantin</p>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalTenant }} / {{ $totalKantin }}</h3>
            <p class="text-[13px] font-medium text-gray-400">Total Terdaftar</p>
        </div>
    </div>

</div>

<!-- Main Content Split -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- User Baru -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Pengguna Baru</h3>
            <a href="{{ route('superadmin.users.index') }}" class="text-[13px] font-bold text-blue-600 hover:text-blue-800 flex items-center transition-colors">
                Lihat Semua <i class="ph ph-caret-right ml-1"></i>
            </a>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentUsers as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-bold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-6">
                                <span class="inline-flex px-2 py-1 bg-gray-100 text-gray-700 border border-gray-200 rounded-md text-[10px] font-bold uppercase tracking-wider">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <p class="text-[12px] font-medium text-gray-500">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 text-sm">Belum ada pengguna baru</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Transaksi Terbaru</h3>
            <a href="{{ route('superadmin.orders.index') }}" class="text-[13px] font-bold text-blue-600 hover:text-blue-800 flex items-center transition-colors">
                Lihat Semua <i class="ph ph-caret-right ml-1"></i>
            </a>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Order ID</th>
                        <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-6">
                                <p class="text-[13px] font-bold text-gray-900">{{ $order->order_id }}</p>
                                <p class="text-[11px] text-gray-500">{{ $order->tenant->nama_tenant ?? 'Tenant Dihapus' }}</p>
                            </td>
                            <td class="py-3 px-6">
                                <p class="text-[13px] font-bold text-blue-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                @if($order->order_status == 'selesai')
                                    <span class="inline-flex px-2 py-1 bg-green-50 text-green-600 border border-green-100 rounded-md text-[10px] font-bold uppercase tracking-wider">Selesai</span>
                                @elseif($order->order_status == 'diproses')
                                    <span class="inline-flex px-2 py-1 bg-yellow-50 text-yellow-600 border border-yellow-100 rounded-md text-[10px] font-bold uppercase tracking-wider">Proses</span>
                                @elseif($order->order_status == 'dibatalkan')
                                    <span class="inline-flex px-2 py-1 bg-red-50 text-red-600 border border-red-100 rounded-md text-[10px] font-bold uppercase tracking-wider">Batal</span>
                                @else
                                    <span class="inline-flex px-2 py-1 bg-gray-50 text-gray-600 border border-gray-100 rounded-md text-[10px] font-bold uppercase tracking-wider">{{ $order->order_status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 text-sm">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
