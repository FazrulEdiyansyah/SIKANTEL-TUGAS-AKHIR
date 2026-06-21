@extends('layouts.pelanggan')

@section('title', 'Kantin ' . $kantin->nama_kantin . ' - SIKANTEL')

@section('content')

    <!-- Hero Section -->
    <div class="relative max-w-[1400px] mx-auto mt-6 px-6 lg:px-16">
        <!-- Banner Container -->
        <div class="relative w-full h-[360px] bg-[#E31E24] rounded-[32px] overflow-hidden flex text-white z-0">
            
            <!-- Background Image Area (Right) -->
            <div class="absolute inset-y-0 right-0 w-2/3 md:w-3/4 pointer-events-none">
                <!-- Gradient Masking for smooth transition -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#E31E24] via-[#E31E24]/80 to-transparent z-10"></div>
                
                @if($kantin->foto)
                    <img src="{{ asset('storage/' . $kantin->foto) }}" class="w-full h-full object-cover">
                @else
                    <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60">
                @endif
            </div>

            <!-- Content Area (Left) -->
            <div class="relative z-20 w-full h-full p-8 md:p-12 flex flex-col justify-center">
                <!-- Breadcrumb -->
                <div class="flex items-center space-x-2 text-[13px] text-white/80 font-medium mb-4">
                    <a href="{{ route('pelanggan.dashboard') }}" class="hover:text-white transition-colors">Beranda</a>
                    <span>></span>
                    <a href="{{ route('pelanggan.dashboard') }}" class="hover:text-white transition-colors">Kantin</a>
                    <span>></span>
                    <span class="text-white">{{ $kantin->nama_kantin }}</span>
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
            <form action="{{ route('pelanggan.kantin.show', $kantin->id) }}" method="GET" class="flex flex-col md:flex-row w-full items-center">
                
                <!-- Search Input -->
                <div class="flex items-center flex-1 w-full pl-4 py-2 border-b md:border-b-0 border-gray-100">
                    <div class="flex flex-col shrink-0 w-24">
                        <span class="text-[11px] font-bold text-gray-900 uppercase">Cari Tenant</span>
                    </div>
                    <div class="flex items-center flex-1">
                        <i class="ph ph-magnifying-glass text-gray-400 text-lg mx-3 shrink-0"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan nama tenant" class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-700 placeholder-gray-400">
                    </div>
                </div>

                <!-- Divider -->
                <div class="hidden md:block w-px h-10 bg-gray-200 mx-4 shrink-0"></div>

                <!-- Status Dropdown -->
                <div class="flex items-center w-full md:w-auto px-4 py-2 shrink-0">
                    <div class="flex flex-col shrink-0 mr-4">
                        <span class="text-[11px] font-bold text-gray-900 uppercase">Status</span>
                    </div>
                    <div class="relative w-full md:w-40">
                        <select name="status" onchange="this.form.submit()" class="w-full bg-transparent border-none focus:outline-none text-sm font-semibold text-gray-700 appearance-none cursor-pointer pr-6">
                            <option value="">Semua Tenant</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Buka</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tutup</option>
                        </select>
                        <i class="ph ph-caret-down absolute right-0 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($tenants as $tenant)
                <!-- Tenant Card -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 p-6 flex flex-col transition-all group">
                    <div class="flex items-start gap-4 mb-6">
                        <!-- Initials Box -->
                        @php
                            // Generate initials (2 characters max)
                            $words = explode(' ', $tenant->nama_tenant);
                            $initials = '';
                            foreach($words as $w) {
                                $initials .= strtoupper(substr($w, 0, 1));
                                if(strlen($initials) >= 2) break;
                            }
                            // Generate random consistent color based on ID
                            $colors = ['bg-red-100 text-red-600', 'bg-yellow-100 text-yellow-600', 'bg-orange-100 text-orange-600', 'bg-green-100 text-green-600', 'bg-blue-100 text-blue-600', 'bg-purple-100 text-purple-600', 'bg-pink-100 text-pink-600'];
                            $colorClass = $colors[$tenant->id % count($colors)];
                        @endphp
                        
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-black shrink-0 {{ $colorClass }}">
                            {{ $initials }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-h-[64px] flex flex-col justify-center">
                            <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1">{{ $tenant->nama_tenant }}</h3>
                            <p class="text-[12px] font-medium text-gray-500 leading-snug line-clamp-2 mb-2">{{ $tenant->jenis_makanan ?? 'Aneka makanan dan minuman' }}</p>
                            
                            @if($tenant->is_open)
                                <span class="text-[13px] font-bold text-gray-900 mt-auto">Buka</span>
                            @else
                                <span class="text-[13px] font-bold text-gray-500 flex items-center mt-auto">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span> Tutup
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="mt-auto pt-2">
                        @if($tenant->is_open)
                            <a href="{{ route('pelanggan.tenant.show', $tenant->id) }}" class="w-full block text-center bg-[#E31E24] hover:bg-red-700 text-white font-bold text-sm py-3 rounded-xl transition-colors shadow-sm">
                                Lihat Menu
                            </a>
                        @else
                            <button disabled class="w-full block text-center bg-white border border-red-200 text-[#E31E24] font-bold text-sm py-3 rounded-xl cursor-not-allowed opacity-80">
                                Tutup
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-storefront text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada tenant</h3>
                    <p class="text-sm text-gray-500">Kantin ini belum memiliki tenant aktif yang terdaftar.</p>
                </div>
            @endforelse
        </div>
    </div>

@endsection
