@extends('layouts.pelanggan')

@section('title', 'Kantin ' . $kantin->nama_kantin . ' - SIKANTEL')

@section('content')

    <!-- Hero Section -->
    <div class="relative max-w-[1400px] mx-auto mt-6 px-6 lg:px-16">
        <!-- Banner Container -->
        <div class="relative w-full h-[360px] bg-[#E31E24] rounded-[32px] overflow-hidden flex text-white z-0">
            
            @if($kantin->foto)
                <!-- Background Image Area (Right) -->
                <div class="absolute inset-y-0 right-0 w-2/3 md:w-3/4 pointer-events-none">
                    <!-- Gradient Masking for smooth transition -->
                    <div class="absolute inset-0 bg-gradient-to-r from-[#E31E24] via-[#E31E24]/80 to-transparent z-10"></div>
                    <img src="{{ asset('storage/' . $kantin->foto) }}" class="w-full h-full object-cover">
                </div>
            @endif

            <!-- Content Area (Left) -->
            <div class="relative z-20 w-full h-full p-8 md:p-12 flex flex-col justify-center">
                <!-- Breadcrumb -->
                <div class="flex items-center space-x-3 mb-4">
                    <a href="{{ route('pelanggan.dashboard') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white transition-colors shadow-sm border border-white/20">
                        <i class="ph-bold ph-arrow-left text-sm"></i>
                    </a>
                    <div class="flex items-center space-x-2 text-[13px] text-white/80 font-medium">
                        <a href="{{ route('pelanggan.dashboard') }}" class="hidden sm:inline hover:text-white transition-colors">Beranda</a>
                        <span class="hidden sm:inline">></span>
                        <a href="{{ route('pelanggan.dashboard') }}" class="hidden sm:inline hover:text-white transition-colors">Kantin</a>
                        <span class="hidden sm:inline">></span>
                        <span class="text-white font-bold">{{ $kantin->nama_kantin }}</span>
                    </div>
                </div>

                <!-- Titles -->
                <h1 class="text-4xl md:text-5xl font-bold mb-2 tracking-tight">{{ $kantin->nama_kantin }}</h1>
                <h2 class="text-lg md:text-xl font-medium mb-4 text-white/90 flex items-center">
                    <i class="ph ph-map-pin mr-2"></i> {{ $kantin->lokasi ?? 'Lokasi belum ditambahkan' }}
                </h2>
                <p class="text-sm md:text-base text-white/80 mb-8 max-w-lg">Pilih tenant untuk melihat menu yang tersedia.</p>

                <!-- Info Cards -->
                <div class="flex flex-col sm:flex-row gap-4 mt-auto">
                    <!-- Status Card -->
                    <div class="bg-white rounded-2xl p-4 flex items-center space-x-4 shrink-0 shadow-lg">
                        <div class="w-10 h-10 rounded-full bg-red-50 text-telkom-red flex items-center justify-center shrink-0">
                            <i class="ph ph-clock text-xl"></i>
                        </div>
                        <div class="text-left text-gray-800">
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Status</p>
                            <p class="text-[13px] font-bold"><span class="text-gray-900">Buka</span> <span class="font-medium text-gray-600">07.00 - 16.00 WIB</span></p>
                        </div>
                    </div>

                    <!-- Lokasi Card -->
                    <div class="bg-white rounded-2xl p-4 flex items-center space-x-4 shadow-lg flex-1 max-w-xs">
                        <div class="w-10 h-10 rounded-full bg-red-50 text-telkom-red flex items-center justify-center shrink-0">
                            <i class="ph ph-map-pin text-xl"></i>
                        </div>
                        <div class="text-left text-gray-800">
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Lokasi</p>
                            <p class="text-[13px] font-bold text-gray-900 leading-tight">{{ $kantin->lokasi ?? 'Kantin area Telkom University' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Floating Search Bar -->
        <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-[800px] bg-white rounded-2xl shadow-xl border border-gray-100 p-2.5 z-30 flex items-center">
            <form id="search-form" action="{{ route('pelanggan.kantin.show', $kantin->id) }}" method="GET" class="flex flex-col md:flex-row w-full items-center">
                
                <!-- Search Input -->
                <div class="flex items-center flex-1 w-full pl-4 py-2 border-b md:border-b-0 border-gray-100">
                    <div class="flex flex-col shrink-0 w-24">
                        <span class="text-[11px] font-bold text-gray-900 uppercase">Cari Tenant</span>
                    </div>
                    <div class="flex items-center flex-1">
                        <i class="ph ph-magnifying-glass text-gray-400 text-lg mx-3 shrink-0"></i>
                        <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Masukkan nama tenant" class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-700 placeholder-gray-400">
                    </div>
                </div>


                
                <!-- Submit Button for Mobile -->
                <button type="submit" class="md:hidden mt-2 w-full bg-[#E31E24] hover:bg-red-700 text-white font-bold text-sm px-6 py-2 rounded-xl transition-colors">
                    Cari
                </button>
                <!-- Hidden submit button for Enter key on Desktop -->
                <button type="submit" class="hidden"></button>
            </form>
        </div>
    </div>

    <!-- Main Content: Daftar Tenant -->
    <div class="max-w-[1400px] mx-auto px-6 lg:px-16 mt-24 mb-20">
        <!-- Section Header -->
        <div class="mb-8">
            <h2 class="text-[22px] font-bold text-gray-900 tracking-tight mb-1">Daftar Tenant</h2>
            <p class="text-sm font-medium text-gray-500">Pilih tenant yang ingin Anda lihat menu dan lakukan pemesanan.</p>
        </div>

        <!-- Grid Cards -->
        <div id="tenant-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($tenants as $tenant)
                <!-- Tenant Card -->
                <a href="{{ $tenant->is_open ? route('pelanggan.tenant.show', $tenant->id) : '#' }}" 
                   class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-row sm:flex-col hover:shadow-lg transition-all duration-300 group {{ !$tenant->is_open ? 'opacity-75 cursor-not-allowed' : '' }}"
                   @if(!$tenant->is_open) onclick="event.preventDefault()" @endif>
                    
                    <!-- Photo Area -->
                    <div class="relative w-[110px] sm:w-full shrink-0 sm:aspect-[4/3] h-auto bg-gray-100 overflow-hidden">
                        @if($tenant->foto)
                            <img src="{{ asset('storage/' . $tenant->foto) }}" 
                                 alt="{{ $tenant->nama_tenant }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ph ph-storefront text-4xl sm:text-5xl text-gray-300"></i>
                            </div>
                        @endif

                        <!-- Rating Badge -->
                        @if($tenant->reviews_count > 0)
                            <div class="absolute bottom-2 sm:bottom-3 right-2 sm:right-3 bg-white rounded-full px-2 sm:px-2.5 py-0.5 sm:py-1 flex items-center gap-1 shadow-sm">
                                <i class="ph-fill ph-star text-yellow-400 text-[10px] sm:text-xs"></i>
                                <span class="text-[11px] sm:text-xs font-bold text-gray-900">{{ number_format($tenant->reviews_avg_rating, 1) }}</span>
                            </div>
                        @endif

                        <!-- Status Badge (Tutup) -->
                        @if(!$tenant->is_open)
                            <div class="absolute inset-0 bg-white/50 backdrop-blur-[2px] flex items-center justify-center">
                                <span class="bg-white text-gray-800 font-bold text-[10px] sm:text-sm px-2 sm:px-4 py-1 sm:py-2 rounded-full shadow-sm border border-gray-100 text-center leading-tight">Tutup</span>
                            </div>
                        @endif
                    </div>

                    <!-- Content Area -->
                    <div class="p-3 sm:p-4 flex flex-col flex-1 justify-center sm:justify-start">
                        <h3 class="text-[15px] font-bold text-gray-900 leading-tight mb-1 line-clamp-2 group-hover:text-telkom-red transition-colors">{{ $tenant->nama_tenant }}</h3>
                        
                        <p class="text-[13px] text-gray-500 mb-3 line-clamp-1">{{ $tenant->jenis_makanan ?? 'Aneka makanan' }}</p>

                        <div class="mt-auto flex items-center text-[13px] font-medium text-gray-500 gap-2">
                            @if($tenant->is_open)
                                <span class="flex items-center gap-1 text-green-600 font-semibold">
                                    <i class="ph-fill ph-check-circle"></i> Buka
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-gray-400 font-semibold">
                                    <i class="ph-fill ph-x-circle"></i> Tutup
                                </span>
                            @endif

                            @if($tenant->reviews_count > 0)
                                <span>•</span>
                                <span>{{ $tenant->reviews_count }} ulasan</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-storefront text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada tenant</h3>
                    <p class="text-sm text-gray-500">Kantin ini belum memiliki tenant aktif yang terdaftar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const searchForm = document.getElementById('search-form');
            const gridContainer = document.getElementById('tenant-grid');

            const performSearch = () => {
                const url = new URL(searchForm.action);
                if (searchInput.value) {
                    url.searchParams.set('search', searchInput.value);
                }

                let loadingTimeout;
                if (gridContainer) {
                    loadingTimeout = setTimeout(() => {
                        gridContainer.style.transition = 'opacity 0.2s';
                        gridContainer.style.opacity = '0.5';
                    }, 250); // Muncul animasi jika loading lebih dari 250ms
                }

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    clearTimeout(loadingTimeout);
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newGrid = doc.getElementById('tenant-grid');
                    
                    if (newGrid && gridContainer) {
                        gridContainer.style.transition = 'none';
                        gridContainer.style.opacity = '1';
                        gridContainer.innerHTML = newGrid.innerHTML;
                    }
                    
                    // Update URL tanpa reload
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    clearTimeout(loadingTimeout);
                    if (gridContainer) {
                        gridContainer.style.transition = 'none';
                        gridContainer.style.opacity = '1';
                    }
                    console.error('Error:', error);
                });
            };

            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    performSearch();
                });
            }
        });
    </script>
@endsection
