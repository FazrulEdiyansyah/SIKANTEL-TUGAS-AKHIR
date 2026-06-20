@extends('layouts.pelanggan')

@section('title', 'Beranda Pelanggan - SIKANTEL')

@section('content')

    <!-- Hero Section -->
    <div class="relative max-w-[1400px] mx-auto mt-6 px-6 lg:px-16">
        <!-- The Red Banner -->
        <div class="relative w-full h-[320px] bg-[#E31E24] rounded-[32px] overflow-visible flex items-center justify-center text-center text-white z-0">
            <!-- Background Elements & Food Images -->
            <!-- Left Shrimp Bowl -->
            <div class="absolute -left-12 -top-10 w-[380px] h-[380px] rounded-full border-8 border-white bg-white shadow-2xl overflow-hidden z-10 hidden md:block">
                <img src="{{ asset('images/makanan-1.png') }}" class="w-full h-full object-cover">
            </div>

            <!-- Right Top Small Bowl (Chili) -->
            <div class="absolute right-32 top-6 w-24 h-24 rounded-full border-4 border-white bg-white shadow-xl overflow-hidden z-10 hidden lg:block">
                <img src="{{ asset('images/makanan-1.png') }}" class="w-full h-full object-cover scale-150 origin-center">
            </div>

            <!-- Right Bottom Meatball Bowl -->
            <div class="absolute -right-16 top-28 w-[340px] h-[340px] rounded-full border-8 border-white bg-white shadow-2xl overflow-hidden z-10 hidden md:block">
                <img src="{{ asset('images/makanan-1.png') }}" class="w-full h-full object-cover">
            </div>

            <!-- Text Content -->
            <div class="relative z-20 max-w-2xl px-4">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 tracking-tight">Pilih Kantin</h1>
                <p class="text-white/90 text-sm md:text-base font-medium leading-relaxed">
                    Temukan tenant dan menu makanan yang<br class="hidden md:block">
                    tersedia di kantin Universitas Telkom.
                </p>
            </div>

            <!-- Floating Search Bar -->
            <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-[640px] bg-white rounded-2xl shadow-xl border border-gray-100 p-2.5 z-30 flex items-center">
                <form action="{{ route('pelanggan.dashboard') }}" method="GET" class="flex w-full items-center">
                    <div class="pl-4 flex items-center shrink-0">
                        <span class="text-gray-800 font-bold text-sm whitespace-nowrap">Cari Kantin</span>
                    </div>
                    <div class="h-8 w-px bg-gray-200 mx-4"></div>
                    <i class="ph ph-magnifying-glass text-gray-400 text-lg mr-2 shrink-0"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan nama kantin" class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-700 placeholder-gray-400">
                    <button type="submit" class="shrink-0 ml-2 bg-[#E31E24] hover:bg-red-700 text-white font-bold text-sm px-8 py-3.5 rounded-xl transition-colors shadow-sm">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($kantins as $kantin)
                <!-- Kantin Card -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 overflow-hidden flex flex-col transition-all group">
                    <!-- Image -->
                    <div class="w-full h-48 bg-gray-100 relative overflow-hidden">
                        @if($kantin->foto)
                            <img src="{{ asset('storage/' . $kantin->foto) }}" alt="{{ $kantin->nama_kantin }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="no image">
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $kantin->nama_kantin }}</h3>
                        <p class="text-[13px] text-gray-500 mb-6 flex-1">{{ $kantin->lokasi ?? 'Kantin area Telkom University' }}</p>
                        
                        <!-- Action Button -->
                        <a href="{{ route('pelanggan.kantin.show', $kantin->id) }}" class="w-full block text-center bg-[#E31E24] hover:bg-red-700 text-white font-bold text-sm py-3 rounded-xl transition-colors shadow-sm">
                            Lihat Tenant
                        </a>
                    </div>
                </div>
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
