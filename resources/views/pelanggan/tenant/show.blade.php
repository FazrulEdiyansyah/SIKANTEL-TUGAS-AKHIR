@extends('layouts.pelanggan')

@section('title', $tenant->nama_tenant . ' - SIKANTEL')

@section('content')

    <!-- Breadcrumb -->
    <div class="max-w-[1400px] mx-auto mt-6 px-6 lg:px-16 mb-4">
        <div class="flex items-center space-x-2 text-[13px] text-gray-500 font-medium">
            <a href="{{ route('pelanggan.dashboard') }}" class="hover:text-[#E31E24] transition-colors">Kantin</a>
            <span>></span>
            <a href="{{ route('pelanggan.kantin.show', $tenant->kantin_id) }}" class="hover:text-[#E31E24] transition-colors">{{ $tenant->kantin->nama_kantin ?? 'Kantin' }}</a>
            <span>></span>
            <span class="text-gray-900 font-bold">{{ $tenant->nama_tenant }}</span>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="relative max-w-[1400px] mx-auto px-6 lg:px-16">
        
        <!-- Banner Container -->
        <div class="relative w-full h-[280px] md:h-[320px] bg-[#E31E24] rounded-[32px] overflow-hidden flex text-white z-0">
            
            <!-- Background Image Area (Right) -->
            <div class="absolute inset-y-0 right-0 w-2/3 md:w-1/2 pointer-events-none">
                <!-- Gradient Masking for smooth transition -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#E31E24] via-[#E31E24]/90 to-transparent z-10"></div>
                
                @if($tenant->foto)
                    <img src="{{ asset('storage/' . $tenant->foto) }}" class="w-full h-full object-cover opacity-90">
                @else
                    <img src="{{ asset('images/makanan-1.png') }}" class="w-full h-full object-cover opacity-90">
                @endif
            </div>

            <!-- Content Area (Left) -->
            <div class="relative z-20 w-full h-full p-8 md:p-12 flex items-center">
                
                <!-- Initials Box -->
                @php
                    $words = explode(' ', $tenant->nama_tenant);
                    $initials = '';
                    foreach($words as $w) {
                        $initials .= strtoupper(substr($w, 0, 1));
                        if(strlen($initials) >= 2) break;
                    }
                @endphp
                <div class="w-24 h-24 md:w-32 md:h-32 bg-white text-[#E31E24] rounded-[24px] flex items-center justify-center text-3xl md:text-5xl font-black shadow-lg shrink-0 mr-6 md:mr-10">
                    {{ $initials }}
                </div>

                <!-- Text Info -->
                <div class="flex-1 flex flex-col justify-center">
                    <h1 class="text-3xl md:text-5xl font-bold mb-2 tracking-tight">{{ $tenant->nama_tenant }}</h1>
                    
                    <div class="flex items-center text-white/90 text-sm md:text-base font-medium mb-3">
                        <i class="ph ph-map-pin mr-2"></i>
                        {{ $tenant->kantin->nama_kantin ?? 'Lokasi tidak diketahui' }}
                    </div>

                    <p class="text-sm md:text-base text-white/80 mb-6 max-w-lg leading-relaxed line-clamp-2">
                        {{ $tenant->jenis_makanan ?? 'Menyediakan aneka makanan dan minuman yang lezat.' }}
                    </p>

                    <div class="flex items-center space-x-3">
                        @if($tenant->is_open)
                            <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-full px-5 py-1.5 flex items-center text-sm font-bold shadow-sm">
                                Buka
                            </div>
                        @else
                            <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-500/30 rounded-full px-5 py-1.5 flex items-center text-sm font-bold shadow-sm">
                                Tutup
                            </div>
                        @endif
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-5 py-1.5 flex items-center text-sm font-medium shadow-sm">
                            <i class="ph ph-clock mr-2"></i> 07.00 - 16.00 WIB
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Search Bar -->
        <div class="absolute -bottom-7 left-1/2 -translate-x-1/2 w-[90%] max-w-[800px] bg-white rounded-2xl shadow-xl border border-gray-100 p-2 z-30 flex items-center">
            <form action="{{ route('pelanggan.tenant.show', $tenant->id) }}" method="GET" class="flex flex-row w-full items-center">
                
                <div class="flex items-center shrink-0 w-24 pl-4 border-r border-gray-100">
                    <i class="ph ph-magnifying-glass text-gray-400 text-lg mr-2 shrink-0"></i>
                    <span class="text-[11px] font-bold text-gray-900 uppercase">Cari menu</span>
                </div>
                <div class="flex items-center flex-1 px-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan nama menu" class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-700 placeholder-gray-400">
                </div>
                
                <button type="submit" class="hidden"></button>
            </form>
        </div>
    </div>

    <!-- Main Content: Daftar Menu -->
    <div class="max-w-[1400px] mx-auto px-6 lg:px-16 mt-20 mb-20">
        <!-- Section Header -->
        <div class="mb-8">
            <h2 class="text-[22px] font-bold text-gray-900 tracking-tight mb-1">Daftar Menu</h2>
            <p class="text-sm font-medium text-gray-500">Pilih menu yang ingin Anda pesan.</p>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($menus as $menu)
                <!-- Menu Card -->
                <div class="bg-white rounded-[24px] shadow-sm hover:shadow-md border border-gray-100 overflow-hidden flex flex-col transition-all group">
                    <!-- Image -->
                    <div class="w-full h-48 bg-gray-100 relative overflow-hidden shrink-0">
                        @if($menu->foto)
                            <img src="{{ asset('storage/' . $menu->foto) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500">
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-[15px] font-bold text-gray-900 leading-tight mb-2">{{ $menu->nama_menu }}</h3>
                        <p class="text-[14px] text-gray-700 font-semibold mb-4 flex-1">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                        
                        <!-- Status Label -->
                        @if($menu->status === 'tersedia')
                            <span class="text-[11px] font-bold text-gray-900 mb-3 block">Tersedia</span>
                        @else
                            <span class="text-[11px] font-bold text-gray-400 mb-3 block flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-1.5"></span> Habis
                            </span>
                        @endif
                        
                        <!-- Action Area -->
                        <div class="flex items-center justify-between gap-3 mt-auto">
                            <!-- Quantity Control -->
                            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-2 py-1 flex-1 border border-gray-100">
                                @if($menu->status === 'tersedia' && $tenant->is_open)
                                    <button class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded-lg transition-colors">
                                        <i class="ph ph-minus text-sm"></i>
                                    </button>
                                    <span class="text-sm font-bold text-gray-900 mx-2">1</span>
                                    <button class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded-lg transition-colors">
                                        <i class="ph ph-plus text-sm"></i>
                                    </button>
                                @else
                                    <button disabled class="w-7 h-7 flex items-center justify-center text-gray-300 rounded-lg cursor-not-allowed">
                                        <i class="ph ph-minus text-sm"></i>
                                    </button>
                                    <span class="text-sm font-bold text-gray-300 mx-2">0</span>
                                    <button disabled class="w-7 h-7 flex items-center justify-center text-gray-300 rounded-lg cursor-not-allowed">
                                        <i class="ph ph-plus text-sm"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Add Button -->
                            @if($menu->status === 'tersedia' && $tenant->is_open)
                                <button class="bg-[#E31E24] hover:bg-red-700 text-white font-bold text-[13px] px-5 py-2.5 rounded-xl transition-colors shadow-sm shrink-0">
                                    Tambah
                                </button>
                            @else
                                <button disabled class="bg-gray-100 text-gray-400 font-bold text-[13px] px-5 py-2.5 rounded-xl cursor-not-allowed shrink-0 border border-gray-200">
                                    Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-hamburger text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada menu</h3>
                    <p class="text-sm text-gray-500">Tenant ini belum menambahkan menu apapun.</p>
                </div>
            @endforelse
        </div>
    </div>

@endsection
