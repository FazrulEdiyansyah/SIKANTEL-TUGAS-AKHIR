@extends('layouts.dashboard')

@section('title', 'Rekap Penjualan - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="rekap" />
@endsection

@section('content')
<div class="font-sans">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Rekap Penjualan</h1>
            <p class="text-[15px] text-gray-500 font-medium">Pantau performa penjualan dan unduh laporan transaksi Anda.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('tenant.reports.export-excel', ['filter' => $filter]) }}" class="px-4 py-2 bg-white border border-green-200 text-green-700 hover:bg-green-50 rounded-xl font-bold text-sm transition-colors flex items-center shadow-sm">
                <i class="ph-bold ph-microsoft-excel mr-2 text-lg"></i> Export Excel
            </a>
            <a href="{{ route('tenant.reports.export-pdf', ['filter' => $filter]) }}" class="px-4 py-2 bg-white border border-red-200 text-telkom-red hover:bg-red-50 rounded-xl font-bold text-sm transition-colors flex items-center shadow-sm">
                <i class="ph-bold ph-file-pdf mr-2 text-lg"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-red-50 to-white rounded-[20px] p-6 border border-red-100 shadow-sm flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-5">
                <i class="ph-fill ph-money text-8xl"></i>
            </div>
            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm text-telkom-red shrink-0 z-10">
                <i class="ph-fill ph-money text-3xl"></i>
            </div>
            <div class="z-10">
                <p class="text-sm font-bold text-gray-500 mb-1">Pendapatan Bersih (70%)</p>
                <h3 class="text-3xl font-black text-gray-900 mb-1">Rp{{ number_format($totalPendapatanBersih, 0, ',', '.') }}</h3>
                <p class="text-[11px] font-semibold text-gray-400">Total Kotor: Rp{{ number_format($totalPendapatanKotor, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-[20px] p-6 border border-blue-100 shadow-sm flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-5">
                <i class="ph-fill ph-check-circle text-8xl"></i>
            </div>
            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-600 shrink-0 z-10">
                <i class="ph-fill ph-check-circle text-3xl"></i>
            </div>
            <div class="z-10">
                <p class="text-sm font-bold text-gray-500 mb-1">Total Pesanan Selesai</p>
                <h3 class="text-3xl font-black text-gray-900">{{ $pesananSelesai }} <span class="text-base font-bold text-gray-500">pesanan</span></h3>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-[20px] p-6 border border-gray-100 shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h3 class="text-lg font-bold text-gray-900">Grafik Pendapatan</h3>
            
            <div class="flex flex-wrap gap-1 bg-gray-50 p-1 rounded-xl border border-gray-200">
                <a href="{{ route('tenant.reports.index', ['filter' => 'hari_ini']) }}" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ $filter == 'hari_ini' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Hari Ini</a>
                <a href="{{ route('tenant.reports.index', ['filter' => 'minggu_ini']) }}" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ $filter == 'minggu_ini' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">7 Hari</a>
                <a href="{{ route('tenant.reports.index', ['filter' => 'bulan_ini']) }}" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ $filter == 'bulan_ini' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">30 Hari</a>
                <a href="{{ route('tenant.reports.index', ['filter' => 'semua']) }}" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ $filter == 'semua' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Semua Waktu</a>
            </div>
        </div>
        
        <div class="h-[300px] w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Rincian Transaksi</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-[12px] font-bold text-gray-500 uppercase tracking-wider">ID & Waktu</th>
                        <th class="py-4 px-6 text-[12px] font-bold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="py-4 px-6 text-[12px] font-bold text-gray-500 uppercase tracking-wider">Menu</th>
                        <th class="py-4 px-6 text-[12px] font-bold text-gray-500 uppercase tracking-wider text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <p class="font-bold text-gray-900 text-sm">#{{ substr($order->order_id, -8) }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-bold text-gray-900 text-sm">{{ $order->user->name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->order_type == 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-600 line-clamp-2 w-48">
                                    {{ $order->items->map(function($i) { return $i->nama_menu . ' (x'.$i->quantity.')'; })->join(', ') }}
                                </p>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <p class="font-black text-telkom-red">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mx-auto mb-4">
                                    <i class="ph ph-receipt text-3xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Belum Ada Transaksi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- Tambahkan Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Gradient fill
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(237, 28, 36, 0.2)');   
        gradient.addColorStop(1, 'rgba(237, 28, 36, 0)');

        const dataLabels = {!! json_encode($labels) !!};
        const dataValues = {!! json_encode($data) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: dataValues,
                    borderColor: '#ed1c24',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#ed1c24',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 13, family: "'Poppins', sans-serif" },
                        bodyFont: { size: 14, weight: 'bold', family: "'Poppins', sans-serif" },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: "'Poppins', sans-serif", size: 11 },
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: "'Poppins', sans-serif", size: 11 },
                            color: '#9ca3af',
                            callback: function(value) {
                                if (value === 0) return '0';
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
