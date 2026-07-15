@extends('layouts.dashboard')

@section('title', 'Dashboard Pengelola')

@section('sidebar_menu')
    <x-sidebar.pengelola active="dashboard" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Dashboard Pengelola</h1>
        <p class="text-[15px] text-gray-500 font-medium">Pantau ringkasan performa kantin dan tenant secara real-time.</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-6">
                <i class="ph ph-storefront text-[24px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Kantin</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalKantin }}</h3>
                <p class="text-[13px] font-medium text-gray-400">Kantin Aktif</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-6">
                <i class="ph ph-users text-[24px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Tenant Aktif</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalTenant }}</h3>
                <p class="text-[13px] font-medium text-gray-400">Tenant Aktif</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mb-6">
                <i class="ph ph-money text-[24px] text-green-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Penjualan Bulan Ini</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">Rp{{ number_format($penjualanBulanIni, 0, ',', '.') }}</h3>
                <p class="text-[13px] font-medium text-gray-400">Dari semua kantin</p>
            </div>
        </div>
    </div>

    <!-- Charts & Lists Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Grafik Penjualan -->
        <div class="lg:col-span-2 bg-white rounded-[20px] p-6 shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 space-y-4 sm:space-y-0">
                <h2 class="text-[17px] font-bold text-gray-900">Grafik Penjualan</h2>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center bg-gray-50 rounded-lg p-1">
                        <span class="text-xs font-medium text-gray-500 px-3 py-1.5">Pilih Kantin</span>
                        <select id="chartKantinSelect" onchange="fetchChartData(this.value)" class="text-xs font-semibold bg-white border border-gray-200 rounded-md px-3 py-1 outline-none focus:ring-2 focus:ring-telkom-red/20 cursor-pointer">
                            <option value="">Semua Kantin</option>
                            @foreach($allKantins as $k)
                                <option value="{{ $k->id }}" {{ request('kantin_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kantin }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex bg-gray-50 rounded-lg p-1" id="timeFilterButtons">
                        <button onclick="changeTimeFilter('hari', this)" class="time-btn text-[13px] font-medium text-gray-500 px-4 py-1.5 rounded-md hover:text-gray-900 transition-colors">Hari Ini</button>
                        <button onclick="changeTimeFilter('minggu', this)" class="time-btn text-[13px] font-medium text-gray-500 px-4 py-1.5 rounded-md hover:text-gray-900 transition-colors">Minggu Ini</button>
                        <button onclick="changeTimeFilter('bulan', this)" class="time-btn text-[13px] font-bold text-white bg-telkom-red px-4 py-1.5 rounded-md shadow-sm active-time-btn">Bulan Ini</button>
                    </div>
                </div>
            </div>
            
            <!-- Area Chart -->
            <div class="relative h-64 w-full">
                <canvas id="pengelolaSalesChart"></canvas>
            </div>
        </div>

        <!-- Kantin Paling Ramai -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-[17px] font-bold text-gray-900">Kantin Paling Ramai</h2>
                <form method="GET" action="{{ route('pengelola.dashboard') }}">
                    @if(request('kantin_id'))
                        <input type="hidden" name="kantin_id" value="{{ request('kantin_id') }}">
                    @endif
                    @if(request('filter_waktu_tenant'))
                        <input type="hidden" name="filter_waktu_tenant" value="{{ request('filter_waktu_tenant') }}">
                    @endif
                    <select name="filter_waktu_kantin" onchange="this.form.submit()" class="text-[12px] font-semibold bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 outline-none focus:ring-0 cursor-pointer text-gray-700">
                        <option value="bulan_ini" {{ request('filter_waktu_kantin', 'bulan_ini') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="semua_waktu" {{ request('filter_waktu_kantin') == 'semua_waktu' ? 'selected' : '' }}>Semua Waktu</option>
                    </select>
                </form>
            </div>
            
            <div class="space-y-5 flex-1 flex flex-col items-center justify-center text-center pb-8">
                @if($kantinTeramai)
                    <div class="w-20 h-20 rounded-2xl bg-gray-100 border-2 border-white shadow-md overflow-hidden mx-auto mb-2">
                        @if($kantinTeramai->foto)
                            <img src="{{ asset('storage/' . $kantinTeramai->foto) }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60">
                        @endif
                            <div class="w-full h-full flex items-center justify-center bg-red-50 text-telkom-red">
                                <i class="ph-bold ph-storefront text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $kantinTeramai->nama_kantin }}</h3>
                        <p class="text-sm font-medium text-gray-500 mb-2">{{ $kantinTeramai->lokasi }}</p>
                        <span class="inline-flex px-3 py-1 bg-green-50 text-green-600 rounded-lg text-xs font-bold">Rp{{ number_format($kantinTeramai->orders_sum_total_price, 0, ',', '.') }}</span>
                    </div>
                @else
                    <i class="ph ph-storefront text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm font-medium text-gray-400">Belum ada data kantin</p>
                @endif
            </div>

            <a href="{{ route('pengelola.kantin.index') }}" class="w-full mt-6 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-telkom-red hover:bg-red-50 transition-colors text-center block">
                Lihat Semua Kantin
            </a>
        </div>
    </div>

    <!-- Top 5 Tenant Table -->
    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-[17px] font-bold text-gray-900">Top 5 Tenant <span class="text-[13px] font-medium text-gray-400 font-normal ml-1">(Berdasarkan Penjualan)</span></h2>
            <form method="GET" action="{{ route('pengelola.dashboard') }}" class="flex items-center gap-2">
                @if(request('filter_waktu_kantin'))
                    <input type="hidden" name="filter_waktu_kantin" value="{{ request('filter_waktu_kantin') }}">
                @endif
                <div class="flex items-center bg-white border border-gray-200 rounded-lg p-1.5 px-3">
                    <select name="filter_waktu_tenant" onchange="this.form.submit()" class="text-[13px] font-semibold bg-transparent border-none outline-none focus:ring-0 cursor-pointer text-gray-700 py-0 pl-1 pr-6">
                        <option value="bulan_ini" {{ request('filter_waktu_tenant', 'bulan_ini') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="semua_waktu" {{ request('filter_waktu_tenant') == 'semua_waktu' ? 'selected' : '' }}>Semua Waktu</option>
                    </select>
                </div>
                <div class="flex items-center bg-white border border-gray-200 rounded-lg p-1.5 px-3">
                    <select name="kantin_id" onchange="this.form.submit()" class="text-[13px] font-semibold bg-transparent border-none outline-none focus:ring-0 cursor-pointer text-gray-700">
                        <option value="">Semua Kantin</option>
                        @foreach($allKantins as $k)
                            <option value="{{ $k->id }}" {{ request('kantin_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kantin }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Peringkat</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tenant</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kantin</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Pesanan Selesai</th>
                        <th class="py-4 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topTenants as $index => $tenantInfo)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-4">
                                <div class="w-8 h-8 rounded-full bg-{{ $index == 0 ? 'yellow' : ($index == 1 ? 'gray' : ($index == 2 ? 'orange' : 'red')) }}-50 text-{{ $index == 0 ? 'yellow' : ($index == 1 ? 'gray' : ($index == 2 ? 'orange' : 'red')) }}-600 flex items-center justify-center font-black text-sm">
                                    #{{ $index + 1 }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                        @if($tenantInfo->foto)
                                            <img src="{{ asset('storage/' . $tenantInfo->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60">
                                        @endif
                                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="ph-fill ph-storefront"></i></div>
                                        @endif
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">{{ $tenantInfo->nama_tenant }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-600">
                                {{ $tenantInfo->kantin->nama_kantin ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="text-sm font-bold text-gray-900">{{ $tenantInfo->orders_count }}</span> <span class="text-xs text-gray-500">pesanan</span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="text-[15px] font-black text-telkom-red">Rp{{ number_format($tenantInfo->orders_sum_total_price, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ph ph-users text-4xl text-gray-200 mb-3"></i>
                                    <p class="text-sm font-medium text-gray-400">Belum ada data tenant</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="w-full pt-6 mt-2 flex justify-center">
                <a href="{{ route('pengelola.tenant.index') }}" class="text-sm font-bold text-telkom-red hover:text-telkom-maroon transition-colors">
                    Lihat Semua Tenant
                </a>
            </div>
        </div>
    </div>
    
    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('pengelolaSalesChart');
            if (!ctx) return;
            
            const context = ctx.getContext('2d');
            
            // Gradient fill
            let gradient = context.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(237, 28, 36, 0.2)');   
            gradient.addColorStop(1, 'rgba(237, 28, 36, 0)');

            const dataLabels = {!! json_encode($labels ?? []) !!};
            const dataValues = {!! json_encode($data ?? []) !!};

            window.pengelolaChart = new Chart(context, {
                type: 'line',
                data: {
                    labels: dataLabels,
                    datasets: [{
                        label: 'Pendapatan Keseluruhan (Rp)',
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
                        legend: { display: false },
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
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { family: "'Poppins', sans-serif", size: 11 }, color: '#9ca3af' }
                        },
                        y: {
                            grid: { color: '#f3f4f6', drawBorder: false },
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

        let currentKantinId = '{{ request("kantin_id") }}';
        let currentTimeFilter = 'bulan';

        async function fetchChartData(kantinId) {
            currentKantinId = kantinId;
            updateChart();
        }
        
        function changeTimeFilter(time, btnElement) {
            currentTimeFilter = time;
            
            // Update button styles
            document.querySelectorAll('.time-btn').forEach(btn => {
                btn.className = 'time-btn text-[13px] font-medium text-gray-500 px-4 py-1.5 rounded-md hover:text-gray-900 transition-colors';
            });
            btnElement.className = 'time-btn text-[13px] font-bold text-white bg-telkom-red px-4 py-1.5 rounded-md shadow-sm active-time-btn';
            
            updateChart();
        }

        async function updateChart() {
            try {
                // Add loading indicator to select
                const select = document.getElementById('chartKantinSelect');
                const originalCursor = select.style.cursor;
                select.style.cursor = 'wait';
                
                const response = await fetch(`{{ route('pengelola.dashboard') }}?fetch_chart=1&kantin_id=${currentKantinId}&time_filter=${currentTimeFilter}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (window.pengelolaChart) {
                        window.pengelolaChart.data.labels = result.labels;
                        window.pengelolaChart.data.datasets[0].data = result.data;
                        window.pengelolaChart.update();
                    }
                }
                
                select.style.cursor = originalCursor;
            } catch (error) {
                console.error("Error fetching chart data:", error);
            }
        }
    </script>
@endsection

