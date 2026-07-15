@extends('layouts.pelanggan')

@section('title', 'Beranda Pelanggan - SIKANTEL')

@section('content')

    <!-- Hero Section -->
    <div class="relative max-w-[1400px] mx-auto mt-6 px-6 lg:px-16">
        <!-- The Red Banner -->
        <div class="relative w-full h-[320px] rounded-[32px] overflow-visible flex items-center justify-center text-center text-white z-0">
            <!-- Background Image Banner -->
            <div class="absolute inset-0 w-full h-full rounded-[32px] shadow-sm overflow-hidden z-0 bg-[#E31E24]">
                <img src="{{ asset('images/FOTO BANNER KANTIN.png') }}" alt="Banner Kantin" class="w-full h-full object-cover object-center">
                <!-- Overlay tipis untuk memastikan teks tetap terbaca -->
                <div class="absolute inset-0 bg-black/10"></div>
            </div>

            <!-- Text Content -->
            <div class="relative z-20 max-w-2xl px-4 drop-shadow-lg">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 tracking-tight text-white drop-shadow-md">Pilih Kantin</h1>
                <p class="text-white/95 text-sm md:text-base font-medium leading-relaxed drop-shadow-sm">
                    Temukan tenant dan menu makanan yang<br class="hidden md:block">
                    tersedia di kantin Universitas Telkom.
                </p>
            </div>

            <!-- Floating Search Bar -->
            <div class="absolute -bottom-10 left-1/2 bg-white rounded-2xl shadow-xl border border-gray-100 p-2 sm:p-2.5 z-30 flex items-center" style="width: 92%; max-width: 640px; transform: translateX(-50%);">
                <form id="search-form" action="{{ route('pelanggan.search') }}" method="GET" class="flex w-full items-center">
                    <div class="hidden sm:flex pl-4 items-center shrink-0">
                        <span class="text-gray-800 font-bold text-sm whitespace-nowrap">Mau Makan Apa?</span>
                    </div>
                    <div class="hidden sm:block h-8 w-px bg-gray-200 mx-4"></div>
                    <i class="ph ph-magnifying-glass text-gray-400 text-lg mr-2 sm:ml-0 ml-2 shrink-0"></i>
                    <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Cari menu, tenant, atau kantin..." class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-700 placeholder-gray-400 min-w-0">
                    <button type="submit" class="shrink-0 ml-2 bg-[#E31E24] hover:bg-red-700 text-white font-bold text-sm px-5 sm:px-8 py-3 sm:py-3.5 rounded-xl transition-colors shadow-sm">
                        Cari
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content: Daftar Kantin -->
    <div class="max-w-[1400px] mx-auto px-6 lg:px-16 mt-24 mb-20">
        <!-- Section Header -->
        <div class="mb-8">
            <h2 class="text-[22px] font-bold text-gray-900 tracking-tight mb-1">Daftar Kantin</h2>
            <p class="text-sm font-medium text-gray-500">Pilih kantin yang ingin Anda kunjungi.</p>
        </div>

        <!-- Grid Cards -->
        <div id="kantin-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-8">
            @forelse($kantins as $kantin)
                <!-- Kantin Card -->
                <a href="{{ route('pelanggan.kantin.show', $kantin->id) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 overflow-hidden flex flex-row sm:flex-col transition-all group">
                    <!-- Image -->
                    <div class="w-32 sm:w-full h-auto sm:h-48 bg-gray-100 relative overflow-hidden shrink-0">
                        @if($kantin->foto)
                            <img src="{{ asset('storage/' . $kantin->foto) }}" alt="{{ $kantin->nama_kantin }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" loading="lazy" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="no image">
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4 sm:p-6 flex-1 flex flex-col justify-center">
                        <h3 class="text-[15px] sm:text-lg font-bold text-gray-900 mb-1 leading-tight group-hover:text-telkom-red transition-colors">{{ $kantin->nama_kantin }}</h3>
                        <p class="text-[12px] sm:text-[13px] text-gray-500 mb-0 sm:mb-6 flex-1 line-clamp-2">{{ $kantin->lokasi ?? 'Kantin area Telkom University' }}</p>
                        
                        <!-- Action Button (Desktop Only) -->
                        <div class="hidden sm:block w-full text-center bg-[#E31E24] hover:bg-red-700 text-white font-bold text-sm py-3 rounded-xl transition-colors shadow-sm mt-auto">
                            Lihat Tenant
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-storefront text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada kantin</h3>
                    <p class="text-sm text-gray-500">Kantin belum ditambahkan atau tidak ditemukan.</p>
                </div>
            @endforelse
        </div>
    </div>


@endsection
