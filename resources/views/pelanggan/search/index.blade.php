@extends('layouts.pelanggan')

@section('title', 'Hasil Pencarian "' . $query . '" - SIKANTEL')

@section('content')
    <div class="max-w-[1400px] mx-auto mt-6 px-6 lg:px-16 mb-20" x-data="{ activeTab: {{ $menus->count() > 0 ? "'menu'" : ($tenants->count() > 0 ? "'tenant'" : "'kantin'") }} }">
        
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
            <form action="{{ route('pelanggan.search') }}" method="GET" class="w-full" x-data="{ searchQuery: '{{ addslashes($query) }}' }">
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

        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Hasil pencarian untuk "{{ $query }}"</h1>
        </div>

        <!-- Tabs -->
        <div class="flex space-x-2 border-b border-gray-200 mb-8 overflow-x-auto scrollbar-hide">
            <!-- Tab Menu -->
            <button @click="activeTab = 'menu'" 
                    :class="{ 'border-telkom-red text-telkom-red': activeTab === 'menu', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'menu' }"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition-colors flex items-center space-x-2">
                <i class="ph-fill ph-fork-knife text-lg"></i>
                <span>Menu</span>
                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs" :class="{ 'bg-red-50 text-telkom-red': activeTab === 'menu' }">{{ $menus->count() }}</span>
            </button>

            <!-- Tab Tenant -->
            <button @click="activeTab = 'tenant'" 
                    :class="{ 'border-telkom-red text-telkom-red': activeTab === 'tenant', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tenant' }"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition-colors flex items-center space-x-2">
                <i class="ph-fill ph-storefront text-lg"></i>
                <span>Tenant</span>
                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs" :class="{ 'bg-red-50 text-telkom-red': activeTab === 'tenant' }">{{ $tenants->count() }}</span>
            </button>

            <!-- Tab Kantin -->
            <button @click="activeTab = 'kantin'" 
                    :class="{ 'border-telkom-red text-telkom-red': activeTab === 'kantin', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'kantin' }"
                    class="whitespace-nowrap py-4 px-6 border-b-2 font-bold text-sm transition-colors flex items-center space-x-2">
                <i class="ph-fill ph-map-pin text-lg"></i>
                <span>Kantin</span>
                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs" :class="{ 'bg-red-50 text-telkom-red': activeTab === 'kantin' }">{{ $kantins->count() }}</span>
            </button>
        </div>

        <!-- Tab Content: Menu -->
        <div x-show="activeTab === 'menu'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($menus->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($menus as $menu)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all group overflow-hidden flex flex-row sm:flex-col">
                            <!-- Image -->
                            <div class="w-28 sm:w-full h-28 sm:h-48 bg-gray-100 relative overflow-hidden shrink-0">
                                @if($menu->foto)
                                    <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="ph ph-image text-4xl text-gray-300"></i>
                                    </div>
                                @endif
                                @if($menu->status !== 'tersedia')
                                    <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center">
                                        <span class="bg-white text-gray-800 font-bold text-xs px-3 py-1 rounded-full shadow-sm">Habis</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="text-base font-bold text-gray-900 mb-1 leading-tight group-hover:text-telkom-red transition-colors">{{ $menu->nama_menu }}</h3>
                                <p class="text-sm font-bold text-telkom-red mb-3">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                                
                                <div class="mt-auto space-y-2 bg-gray-50 rounded-xl p-3 border border-gray-100">
                                    <div class="flex items-start text-xs text-gray-600">
                                        <i class="ph ph-storefront text-gray-400 mt-0.5 mr-1.5 shrink-0"></i>
                                        <span class="font-medium truncate" title="{{ $menu->tenant->nama_tenant }}">{{ $menu->tenant->nama_tenant }}</span>
                                    </div>
                                    <div class="flex items-start text-xs text-gray-600">
                                        <i class="ph ph-map-pin text-gray-400 mt-0.5 mr-1.5 shrink-0"></i>
                                        <span class="truncate" title="{{ $menu->tenant->kantin->nama_kantin }}">{{ $menu->tenant->kantin->nama_kantin }}</span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('pelanggan.tenant.show', $menu->tenant_id) }}" class="mt-3 w-full block text-center bg-white border border-gray-200 hover:border-telkom-red hover:text-telkom-red text-gray-700 font-bold text-xs py-2 rounded-lg transition-colors">
                                    Kunjungi Tenant
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-fork-knife text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Menu tidak ditemukan</h3>
                    <p class="text-sm text-gray-500">Tidak ada menu yang sesuai dengan pencarian Anda.</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Tenant -->
        <div x-show="activeTab === 'tenant'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($tenants->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($tenants as $tenant)
                        <a href="{{ $tenant->is_open ? route('pelanggan.tenant.show', $tenant->id) : '#' }}" 
                           class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-row sm:flex-col hover:shadow-lg transition-all duration-300 group {{ !$tenant->is_open ? 'opacity-75 cursor-not-allowed' : '' }}"
                           @if(!$tenant->is_open) onclick="event.preventDefault()" @endif>
                            
                            <!-- Photo Area -->
                            <div class="relative w-[110px] sm:w-full shrink-0 sm:aspect-[4/3] h-auto bg-gray-100 overflow-hidden">
                                @if($tenant->foto)
                                    <img src="{{ asset('storage/' . $tenant->foto) }}" alt="{{ $tenant->nama_tenant }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="ph ph-storefront text-4xl sm:text-5xl text-gray-300"></i>
                                    </div>
                                @endif

                                @if($tenant->reviews_count > 0)
                                    <div class="absolute bottom-2 sm:bottom-3 right-2 sm:right-3 bg-white rounded-full px-2 sm:px-2.5 py-0.5 sm:py-1 flex items-center gap-1 shadow-sm">
                                        <i class="ph-fill ph-star text-yellow-400 text-[10px] sm:text-xs"></i>
                                        <span class="text-[11px] sm:text-xs font-bold text-gray-900">{{ number_format($tenant->reviews_avg_rating, 1) }}</span>
                                    </div>
                                @endif

                                @if(!$tenant->is_open)
                                    <div class="absolute inset-0 bg-white/50 backdrop-blur-[2px] flex items-center justify-center">
                                        <span class="bg-white text-gray-800 font-bold text-[10px] sm:text-sm px-2 sm:px-4 py-1 sm:py-2 rounded-full shadow-sm border border-gray-100 text-center leading-tight">Tutup</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Content Area -->
                            <div class="p-3 sm:p-4 flex flex-col flex-1 justify-center sm:justify-start">
                                <h3 class="text-[15px] font-bold text-gray-900 leading-tight mb-1 group-hover:text-telkom-red transition-colors">{{ $tenant->nama_tenant }}</h3>
                                
                                <div class="flex items-center text-xs text-gray-500 mb-3 mt-1">
                                    <i class="ph-fill ph-map-pin text-gray-400 mr-1"></i>
                                    {{ $tenant->kantin->nama_kantin }}
                                </div>

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
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-storefront text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Tenant tidak ditemukan</h3>
                    <p class="text-sm text-gray-500">Tidak ada tenant yang sesuai dengan pencarian Anda.</p>
                </div>
            @endif
        </div>

        <!-- Tab Content: Kantin -->
        <div x-show="activeTab === 'kantin'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($kantins->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-8">
                    @foreach($kantins as $kantin)
                        <a href="{{ route('pelanggan.kantin.show', $kantin->id) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 overflow-hidden flex flex-row sm:flex-col transition-all group">
                            <div class="w-32 sm:w-full h-auto sm:h-48 bg-gray-100 relative overflow-hidden shrink-0">
                                @if($kantin->foto)
                                    <img src="{{ asset('storage/' . $kantin->foto) }}" alt="{{ $kantin->nama_kantin }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="no image">
                                @endif
                            </div>
                            
                            <div class="p-4 sm:p-6 flex-1 flex flex-col justify-center">
                                <h3 class="text-[15px] sm:text-lg font-bold text-gray-900 mb-1 leading-tight group-hover:text-telkom-red transition-colors">{{ $kantin->nama_kantin }}</h3>
                                <p class="text-[12px] sm:text-[13px] text-gray-500 mb-0 sm:mb-6 flex-1 line-clamp-2">{{ $kantin->lokasi ?? 'Kantin area Telkom University' }}</p>
                                
                                <div class="hidden sm:block w-full text-center bg-white border border-gray-200 group-hover:border-telkom-red group-hover:text-telkom-red text-gray-700 font-bold text-sm py-2.5 rounded-xl transition-colors mt-auto">
                                    Lihat Kantin
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-map-pin text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Kantin tidak ditemukan</h3>
                    <p class="text-sm text-gray-500">Tidak ada kantin yang sesuai dengan pencarian Anda.</p>
                </div>
            @endif
        </div>

    </div>
@endsection
