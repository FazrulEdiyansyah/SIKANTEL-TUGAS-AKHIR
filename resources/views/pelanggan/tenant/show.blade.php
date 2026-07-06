@extends('layouts.pelanggan')

@section('title', $tenant->nama_tenant . ' - SIKANTEL')

@section('content')
<div x-data="tenantMenu()">

    <!-- Breadcrumb -->
    <div class="max-w-[1400px] mx-auto mt-6 px-6 lg:px-16 mb-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('pelanggan.kantin.show', $tenant->kantin_id) }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
                <i class="ph-bold ph-arrow-left text-sm"></i>
            </a>
            <div class="flex items-center space-x-2 text-[13px] text-gray-500 font-medium">
                <a href="{{ route('pelanggan.dashboard') }}" class="hidden sm:inline hover:text-[#E31E24] transition-colors">Kantin</a>
                <span class="hidden sm:inline">></span>
                <a href="{{ route('pelanggan.kantin.show', $tenant->kantin_id) }}" class="hidden sm:inline hover:text-[#E31E24] transition-colors">{{ $tenant->kantin->nama_kantin ?? 'Kantin' }}</a>
                <span class="hidden sm:inline">></span>
                <span class="text-gray-900 font-bold truncate">{{ $tenant->nama_tenant }}</span>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="relative max-w-[1400px] mx-auto px-6 lg:px-16">
        
        <!-- Banner Container -->
        <div class="relative w-full h-[280px] md:h-[320px] bg-[#E31E24] rounded-[32px] overflow-hidden flex text-white z-0">
            
            @if($tenant->foto)
                <!-- Background Image Area (Right) -->
                <div class="absolute inset-y-0 right-0 w-full md:w-2/3 pointer-events-none">
                    <!-- Gradient Masking for smooth transition -->
                    <div class="absolute inset-0 bg-gradient-to-r from-[#E31E24] via-[#E31E24]/80 to-transparent z-10"></div>
                    <img src="{{ asset('storage/' . $tenant->foto) }}" class="w-full h-full object-cover object-center">
                </div>
            @endif

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
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h1 class="text-3xl md:text-5xl font-bold tracking-tight">{{ $tenant->nama_tenant }}</h1>
                        @if($tenant->reviews_count > 0)
                            <div class="flex items-center bg-white/20 backdrop-blur-sm border border-white/30 rounded-full px-4 py-1 text-yellow-400 text-sm font-bold shadow-sm">
                                <i class="ph-fill ph-star mr-1.5"></i> {{ number_format($tenant->reviews_avg_rating, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center text-white/90 text-sm md:text-base font-medium mb-5">
                        <i class="ph ph-map-pin mr-2"></i>
                        {{ $tenant->kantin->nama_kantin ?? 'Lokasi tidak diketahui' }}
                    </div>

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
            <form id="search-form" action="{{ route('pelanggan.tenant.show', $tenant->id) }}" method="GET" class="flex flex-row w-full items-center">
                
                <div class="flex items-center shrink-0 w-24 pl-4 border-r border-gray-100">
                    <i class="ph ph-magnifying-glass text-gray-400 text-lg mr-2 shrink-0"></i>
                    <span class="text-[11px] font-bold text-gray-900 uppercase">Cari menu</span>
                </div>
                <div class="flex items-center flex-1 px-4">
                    <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Masukkan nama menu" class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-700 placeholder-gray-400">
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
        <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($menus as $menu)
                <!-- Menu Card -->
                <div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm hover:shadow-md overflow-hidden flex flex-row sm:flex-col transition-all group" :class="(cart.menuQty[{{ $menu->id }}] || 0) > 0 ? 'border-y border-r border-gray-100 border-l-[4px] sm:border-l-[6px] border-l-[#E31E24]' : 'border border-gray-100'">
                    <!-- Image -->
                    <div class="w-[110px] h-[110px] sm:w-full sm:h-48 bg-gray-100 relative overflow-hidden shrink-0 m-2 sm:m-0 rounded-xl sm:rounded-none">
                        @if($menu->foto)
                            <img src="{{ asset('storage/' . $menu->foto) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500">
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="p-3 sm:p-5 flex-1 flex flex-col justify-center sm:justify-start">
                        <h3 class="text-[14px] sm:text-[15px] font-bold text-gray-900 leading-tight mb-1 sm:mb-2 line-clamp-2">{{ $menu->nama_menu }}</h3>
                        <p class="text-[13px] sm:text-[14px] text-gray-700 font-semibold mb-2 sm:mb-4 flex-1">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                        
                        <!-- Status Label -->
                        @if($menu->status === 'tersedia')
                            <span class="text-[11px] font-bold text-gray-900 mb-2 sm:mb-3 hidden sm:block">Tersedia</span>
                        @else
                            <span class="text-[11px] font-bold text-gray-400 mb-2 sm:mb-3 block flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-1.5"></span> Habis
                            </span>
                        @endif
                        
                        <!-- Action Area -->
                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center" :class="(cart.menuQty[{{ $menu->id }}] || 0) > 0 ? 'justify-between' : 'justify-center'">
                            @if($menu->status === 'tersedia' && $tenant->is_open)
                                
                                <!-- In Cart State -->
                                <template x-if="(cart.menuQty[{{ $menu->id }}] || 0) > 0">
                                    <div class="w-full flex items-center justify-between">
                                        <button type="button" @click="activeModal = {{ $menu->id }}; document.body.style.overflow = 'hidden';" class="text-telkom-red font-bold flex items-center text-[13px] px-2 py-1 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="ph-fill ph-note-pencil mr-1 text-lg"></i> Catatan
                                        </button>
                                        <div class="flex items-center space-x-3">
                                            <button type="button" @click.prevent="decreaseCart({{ $menu->id }})" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                                <i class="ph-bold ph-minus"></i>
                                            </button>
                                            
                                            <span class="font-medium text-gray-900" x-text="cart.menuQty[{{ $menu->id }}]"></span>
                                            
                                            @if($menu->is_customizable && !empty($menu->customizations))
                                                <button type="button" @click="activeModal = {{ $menu->id }}; document.body.style.overflow = 'hidden';" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                                    <i class="ph-bold ph-plus"></i>
                                                </button>
                                            @else
                                                <button type="button" @click.prevent="addToCart({{ $menu->id }}, {{ $menu->harga }})" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                                    <i class="ph-bold ph-plus"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </template>

                                <!-- Not In Cart State -->
                                <template x-if="(cart.menuQty[{{ $menu->id }}] || 0) === 0">
                                    <div class="w-full">
                                        @if($menu->is_customizable && !empty($menu->customizations))
                                            <button type="button" @click="if(canAddMenu()) { activeModal = {{ $menu->id }}; document.body.style.overflow = 'hidden'; }" class="w-full block text-center bg-[#E31E24] hover:bg-red-700 text-white font-bold text-[13px] px-5 py-2.5 rounded-xl transition-colors shadow-sm cursor-pointer">
                                                Tambah
                                            </button>
                                        @else
                                            <button type="button" @click.prevent="addToCart({{ $menu->id }}, {{ $menu->harga }})" class="w-full block text-center bg-[#E31E24] hover:bg-red-700 text-white font-bold text-[13px] px-5 py-2.5 rounded-xl transition-colors shadow-sm cursor-pointer">
                                                Tambah
                                            </button>
                                        @endif
                                    </div>
                                </template>

                            @else
                                <button disabled class="w-full bg-gray-100 text-gray-400 font-bold text-[13px] px-5 py-2.5 rounded-xl cursor-not-allowed border border-gray-200">
                                    Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Alpine.js Modal Implementation -->
                @if($menu->status === 'tersedia' && $tenant->is_open)
                    <template x-teleport="body">
                        <div x-show="activeModal === {{ $menu->id }}" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                            <!-- Backdrop (No blur to fix lag) -->
                            <div x-show="activeModal === {{ $menu->id }}" 
                                 x-transition.opacity.duration.300ms
                                 class="absolute inset-0 bg-black/60 cursor-pointer" 
                                 @click="activeModal = null; document.body.style.overflow = '';"></div>
                            
                            <!-- Modal Content -->
                            <div x-show="activeModal === {{ $menu->id }}" 
                                 x-transition:enter="transition ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-8 scale-95" 
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                                 class="relative bg-white rounded-3xl shadow-2xl w-full sm:w-[420px] max-w-full overflow-hidden flex flex-col max-h-[85vh]">
                                
                                <!-- Close Button -->
                                <button type="button" @click="activeModal = null; document.body.style.overflow = '';" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:text-gray-900 transition-colors z-10">
                                    <i class="ph-bold ph-x text-sm"></i>
                                </button>
                                
                                <!-- Form with Alpine Scope for dynamic price -->
                                <form action="{{ route('pelanggan.cart.add') }}" method="POST" class="flex flex-col flex-1 overflow-hidden" @submit.prevent="submitModalForm($event, {{ $menu->id }}, {{ $menu->harga }})" x-data="{ modalQty: 1, basePrice: {{ $menu->harga }} }">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                    
                                    <div class="p-6 pt-10 space-y-6 overflow-y-auto custom-scrollbar">
                                        <div class="flex flex-col">
                                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $menu->nama_menu }}</h3>
                                            @if($menu->deskripsi)
                                                <p class="text-sm text-gray-500 mb-3">{{ $menu->deskripsi }}</p>
                                            @endif
                                            <span class="text-lg font-bold text-gray-900">{{ number_format($menu->harga, 0, ',', '.') }}</span>
                                        </div>
                                    
                                        <div class="border-t border-gray-100 pt-6">
                                            <div class="flex items-center gap-2 mb-3">
                                                <h4 class="font-bold text-gray-900">Catatan</h4>
                                            </div>
                                            <p class="text-sm text-gray-500 mb-2">Opsional</p>
                                            <textarea name="catatan" rows="3" maxlength="200" placeholder="Contoh: banyakin porsinya, ya" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-telkom-red focus:bg-white transition-all resize-none"></textarea>
                                        </div>
                                    
                                        @if(!empty($menu->customizations))
                                            @foreach($menu->customizations as $sIndex => $section)
                                                <div class="border-t border-gray-100 pt-6">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <div>
                                                            <h4 class="font-bold text-gray-900">{{ $section['name'] }}</h4>
                                                            @if($section['is_required'])
                                                                <p class="text-xs text-telkom-red font-semibold">Wajib <span class="text-gray-400 font-normal">Pilih 1</span></p>
                                                            @else
                                                                <p class="text-xs text-gray-400 font-semibold">Opsional</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="space-y-3">
                                                        @foreach($section['options'] as $oIndex => $option)
                                                            <label class="flex items-center justify-between p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-red-50 hover:border-telkom-red transition-colors group">
                                                                <div>
                                                                    <span class="text-sm font-semibold text-gray-700 group-hover:text-telkom-red">{{ $option['name'] }}</span>
                                                                    @if(isset($option['price_adjustment']) && $option['price_adjustment'] > 0)
                                                                        <span class="ml-2 text-xs text-gray-500 font-medium">+Rp {{ number_format($option['price_adjustment'], 0, ',', '.') }}</span>
                                                                    @endif
                                                                </div>
                                                                <input type="radio" name="custom_options[{{$sIndex}}]" value="{{ $oIndex }}" {{ $section['is_required'] ? 'required' : '' }} class="w-5 h-5 text-telkom-red focus:ring-telkom-red border-gray-300">
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    <!-- Footer Actions -->
                                    <div class="p-6 border-t border-gray-100 bg-white shrink-0">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="font-medium text-gray-900 text-base">Mau berapa?</span>
                                            <div class="flex items-center space-x-4">
                                                <button type="button" @click="if(modalQty > 1) modalQty--" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                                    <i class="ph-bold ph-minus"></i>
                                                </button>
                                                <input type="hidden" name="quantity" x-model="modalQty">
                                                <span class="font-medium text-lg text-gray-900" x-text="modalQty"></span>
                                                <button type="button" @click="modalQty++" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                                    <i class="ph-bold ph-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full py-3.5 bg-[#E31E24] hover:bg-red-700 text-white font-bold rounded-xl transition-colors shadow-lg text-[15px]">
                                            Perbaharui keranjang - <span x-text="formatPrice(basePrice * modalQty)"></span>
                                        </button>
                                    </div>
                                </form>
                        </div>
                    </div>
                </template>
                @endif
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <i class="ph ph-hamburger text-5xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada menu</h3>
                    <p class="text-sm text-gray-500">Tenant ini belum menambahkan menu apapun.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const searchForm = document.getElementById('search-form');
            const gridContainer = document.getElementById('menu-grid');

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
                    const newGrid = doc.getElementById('menu-grid');
                    
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tenantMenu', () => ({
                activeModal: null,
                requestSequence: 0,
                tenantKantinId: {{ $tenant->kantin_id }},
                canAddMenu() {
                    if (this.cart.totalQty > 0 && this.cart.kantinId !== null && this.cart.kantinId !== this.tenantKantinId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Beda Kantin',
                            text: 'Maaf, Anda hanya bisa memesan dari satu kantin dalam satu pesanan. Selesaikan atau kosongkan keranjang Anda terlebih dahulu.',
                            confirmButtonColor: '#E31E24'
                        });
                        return false;
                    }
                    return true;
                },
                get cart() {
                    return this.$store.cart;
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },
                async addToCart(menuId, price) {
                    if (!this.canAddMenu()) return;
                    
                    if (this.cart.totalQty === 0) {
                        this.cart.kantinId = this.tenantKantinId;
                    }
                    
                    // Optimistic UI Update for instant feedback
                    this.cart.totalQty++;
                    this.cart.menuQty[menuId] = (this.cart.menuQty[menuId] || 0) + 1;
                    if(price) this.cart.totalPrice += price;
                    
                    this.requestSequence++;
                    const currentSeq = this.requestSequence;
                    
                    try {
                        const response = await fetch('{{ route("pelanggan.cart.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                menu_id: menuId,
                                quantity: 1
                            })
                        });
                        const data = await response.json();
                        if (data.success && this.requestSequence === currentSeq) {
                            this.cart.totalQty = data.totalQty;
                            this.cart.totalPrice = data.totalPrice;
                            this.cart.menuQty = data.menuQty;
                            this.cart.itemNames = data.itemNames;
                        } else if (!data.success) {
                            // Rollback optimistic update
                            this.cart.totalQty--;
                            this.cart.menuQty[menuId]--;
                            if(price) this.cart.totalPrice -= price;
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan saat menambahkan ke keranjang.',
                                confirmButtonColor: '#E31E24'
                            });
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        // Rollback optimistic update on error
                        this.cart.totalQty--;
                        this.cart.menuQty[menuId]--;
                        if(price) this.cart.totalPrice -= price;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Jaringan',
                            text: 'Gagal menghubungi server. Silakan coba lagi.',
                            confirmButtonColor: '#E31E24'
                        });
                    }
                },
                async decreaseCart(menuId) {
                    // Optimistic UI Update for instant feedback
                    if (this.cart.menuQty[menuId] > 0) {
                        this.cart.totalQty--;
                        this.cart.menuQty[menuId]--;
                    }
                    
                    this.requestSequence++;
                    const currentSeq = this.requestSequence;
                    
                    try {
                        const response = await fetch('{{ route("pelanggan.cart.decrease") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                menu_id: menuId
                            })
                        });
                        const data = await response.json();
                        if (data.success && this.requestSequence === currentSeq) {
                            this.cart.totalQty = data.totalQty;
                            this.cart.totalPrice = data.totalPrice;
                            this.cart.menuQty = data.menuQty;
                            this.cart.itemNames = data.itemNames;
                        }
                    } catch (error) {
                        console.error('Error decreasing cart:', error);
                    }
                },
                async submitModalForm(event, menuId, basePrice) {
                    if (!this.canAddMenu()) return;
                    
                    const form = event.target;
                    const formData = new FormData(form);
                    const qty = parseInt(formData.get('quantity')) || 1;
                    
                    if (this.cart.totalQty === 0) {
                        this.cart.kantinId = this.tenantKantinId;
                    }
                    
                    // Optimistic feedback
                    this.activeModal = null;
                    document.body.style.overflow = '';
                    this.cart.totalQty += qty;
                    this.cart.totalPrice += (basePrice * qty);
                    this.cart.menuQty[menuId] = (this.cart.menuQty[menuId] || 0) + qty;
                    
                    this.requestSequence++;
                    const currentSeq = this.requestSequence;
                    
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        const data = await response.json();
                        if (data.success && this.requestSequence === currentSeq) {
                            this.cart.totalQty = data.totalQty;
                            this.cart.totalPrice = data.totalPrice;
                            this.cart.menuQty = data.menuQty;
                            this.cart.itemNames = data.itemNames;
                            form.reset();
                        } else if (!data.success) {
                            // Rollback optimistic update
                            this.cart.totalQty -= qty;
                            this.cart.totalPrice -= (basePrice * qty);
                            this.cart.menuQty[menuId] = (this.cart.menuQty[menuId] || 0) - qty;
                            if (this.cart.menuQty[menuId] < 0) this.cart.menuQty[menuId] = 0;
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan saat menambahkan ke keranjang.',
                                confirmButtonColor: '#E31E24'
                            });
                        }
                    } catch (error) {
                        console.error('Error submitting form:', error);
                        // Rollback optimistic update
                        this.cart.totalQty -= qty;
                        this.cart.totalPrice -= (basePrice * qty);
                        this.cart.menuQty[menuId] = (this.cart.menuQty[menuId] || 0) - qty;
                        if (this.cart.menuQty[menuId] < 0) this.cart.menuQty[menuId] = 0;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Jaringan',
                            text: 'Gagal menghubungi server. Silakan coba lagi.',
                            confirmButtonColor: '#E31E24'
                        });
                    }
                }
            }));
        });
    </script>
</div>
@endsection
