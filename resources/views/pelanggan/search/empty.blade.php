@extends('layouts.pelanggan')

@section('title', 'Pencarian - SIKANTEL')

@section('content')
    <div class="max-w-[1400px] mx-auto mt-6 px-6 lg:px-16 mb-20">
        
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-3 mb-6">
            <a href="{{ route('pelanggan.dashboard') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white hover:bg-gray-50 text-gray-700 transition-colors shadow-sm border border-gray-200">
                <i class="ph-bold ph-arrow-left text-sm"></i>
            </a>
            <div class="flex items-center space-x-2 text-[13px] text-gray-500 font-medium">
                <a href="{{ route('pelanggan.dashboard') }}" class="hover:text-gray-900 transition-colors">Beranda</a>
                <span>></span>
                <span class="text-gray-900 font-bold">Hasil Pencarian</span>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <form action="{{ route('pelanggan.search') }}" method="GET" class="w-full" x-data="{ searchQuery: '' }">
                <div class="relative flex items-center w-full h-12 md:h-14 rounded-full bg-white border border-gray-300 hover:border-gray-400 focus-within:border-telkom-red focus-within:ring-1 focus-within:ring-telkom-red transition-all duration-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-center w-12 md:w-14 h-full text-gray-400 shrink-0">
                        <i class="ph-bold ph-magnifying-glass text-lg md:text-xl"></i>
                    </div>

                    <input
                        x-ref="searchInput"
                        class="h-full w-full outline-none text-gray-900 bg-transparent text-sm md:text-base placeholder-gray-400 border-none focus:ring-0 px-0 font-medium"
                        type="text"
                        name="search"
                        x-model="searchQuery"
                        placeholder="Cari menu, tenant, atau kantin..."
                        autocomplete="off"
                    />

                    <!-- Clear Button -->
                    <button type="button" 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''; $refs.searchInput.focus()"
                            class="flex items-center justify-center w-12 md:w-14 h-full text-gray-400 hover:text-gray-700 transition-colors shrink-0"
                            style="display: none;">
                        <i class="ph-bold ph-x text-base md:text-lg"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
