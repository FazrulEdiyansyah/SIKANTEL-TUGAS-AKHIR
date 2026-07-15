@extends('layouts.pelanggan')

@section('title', $tenant->nama_tenant . ' - SIKANTEL')

@section('content')
<div x-data="tenantMenu()">

    <!-- Hero Section -->
    <div class="relative max-w-[1400px] mx-auto mt-6 px-6 lg:px-16 mb-4">
        
        <!-- Banner Container -->
        <div class="relative w-full min-h-[420px] md:h-[360px] bg-[#E31E24] rounded-[32px] overflow-hidden flex text-white z-0 shadow-lg">
            
            @if($tenant->foto)
                <!-- Background Image Area (Right) -->
                <div class="absolute inset-y-0 right-0 w-2/3 md:w-3/4 pointer-events-none">
                    <!-- Gradient Masking for smooth transition -->
                    <div class="absolute inset-0 bg-gradient-to-r from-[#E31E24] via-[#E31E24]/80 to-transparent z-10"></div>
                    <img src="{{ asset('storage/' . $tenant->foto) }}" class="w-full h-full object-cover object-center">
                </div>
            @endif

            <!-- Content Area (Left) -->
            <div class="relative z-20 w-full h-full p-8 md:p-12 pb-16 md:pb-12 flex flex-col justify-center">
                
                <!-- Breadcrumb Inside -->
                <div class="flex items-center space-x-3 mb-6">
                    <a href="{{ route('pelanggan.kantin.show', $tenant->kantin_id) }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white transition-colors shadow-sm border border-white/20">
                        <i class="ph-bold ph-arrow-left text-sm"></i>
                    </a>
                    <div class="flex items-center space-x-2 text-[13px] text-white/80 font-medium">
                        <a href="{{ route('pelanggan.dashboard') }}" class="hidden sm:inline hover:text-white transition-colors">Beranda</a>
                        <span class="hidden sm:inline">></span>
                        <a href="{{ route('pelanggan.kantin.show', $tenant->kantin_id) }}" class="hidden sm:inline hover:text-white transition-colors">{{ $tenant->kantin->nama_kantin ?? 'Kantin' }}</a>
                        <span class="hidden sm:inline">></span>
                        <span class="text-white font-bold truncate">{{ $tenant->nama_tenant }}</span>
                    </div>
                </div>

                <div class="flex items-center mt-2">
                    <!-- Initials Box -->
                    @php
                        $words = explode(' ', $tenant->nama_tenant);
                        $initials = '';
                        foreach($words as $w) {
                            $initials .= strtoupper(substr($w, 0, 1));
                            if(strlen($initials) >= 2) break;
                        }
                    @endphp
                    <div class="w-20 h-20 md:w-24 md:h-24 bg-white text-[#E31E24] rounded-[24px] flex items-center justify-center text-3xl md:text-4xl font-black shadow-xl shrink-0 mr-5 md:mr-6 border border-gray-100">
                        {{ $initials }}
                    </div>

                    <!-- Text Info -->
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h1 class="text-3xl md:text-5xl font-bold tracking-tight text-white drop-shadow-md">{{ $tenant->nama_tenant }}</h1>
                            @if($tenant->reviews_count > 0)
                                <div class="flex items-center bg-white/20 backdrop-blur-sm border border-white/30 rounded-full px-4 py-1 text-yellow-400 text-sm font-bold shadow-sm">
                                    <i class="ph-fill ph-star mr-1.5"></i> {{ number_format($tenant->reviews_avg_rating, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center text-white/90 text-sm md:text-base font-medium mb-4 drop-shadow-sm">
                            <i class="ph ph-map-pin mr-2"></i>
                            {{ $tenant->kantin->nama_kantin ?? 'Lokasi tidak diketahui' }}
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            @if($tenant->is_open)
                                <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-full px-5 py-1.5 flex items-center text-sm font-bold shadow-sm">
                                    <i class="ph ph-clock mr-1.5"></i> Buka
                                </div>
                            @else
                                <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-500/30 rounded-full px-5 py-1.5 flex items-center text-sm font-bold shadow-sm">
                                    <i class="ph ph-clock mr-1.5"></i> Tutup
                                </div>
                            @endif
                            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-5 py-1.5 flex items-center text-sm font-medium shadow-sm whitespace-nowrap">
                                <i class="ph ph-clock mr-2"></i> 07.00 - 16.00 WIB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Search Bar -->
        <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-[640px] bg-white rounded-2xl shadow-xl border border-gray-100 p-2.5 z-30 flex items-center">
            <form id="search-form" action="{{ route('pelanggan.tenant.show', $tenant->id) }}" method="GET" class="flex w-full items-center">
                <div class="pl-4 flex items-center shrink-0 hidden sm:flex">
                    <span class="text-gray-800 font-bold text-sm whitespace-nowrap">Cari Menu</span>
                </div>
                <div class="h-8 w-px bg-gray-200 mx-4 hidden sm:block"></div>
                <div class="sm:hidden pl-3"></div>
                <i class="ph ph-magnifying-glass text-gray-400 text-lg mr-2 shrink-0"></i>
                <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Cari menu di tenant ini..." class="w-full bg-transparent border-none focus:outline-none text-sm text-gray-700 placeholder-gray-400">
                <button type="submit" class="shrink-0 ml-2 bg-[#E31E24] hover:bg-red-700 text-white font-bold text-sm px-6 sm:px-8 py-3.5 rounded-xl transition-colors shadow-sm">
                    Cari
                </button>
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
                <x-pelanggan.menu-card :menu="$menu" :tenant="$tenant" />
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
                pendingDelta: {},
                syncTimers: {},
                init() {
                    window.addEventListener('cart-checkout-clicked', () => {
                        let flushPromises = [];
                        for (let menuId in this.syncTimers) {
                            if (this.syncTimers[menuId] !== null && this.syncTimers[menuId] !== undefined) {
                                clearTimeout(this.syncTimers[menuId]);
                                this.syncTimers[menuId] = null;
                                
                                const delta = this.pendingDelta[menuId];
                                if (delta && delta !== 0) {
                                    this.pendingDelta[menuId] = 0;
                                    const endpoint = delta > 0 ? '{{ route("pelanggan.cart.add") }}' : '{{ route("pelanggan.cart.decrease") }}';
                                    
                                    let p = fetch(endpoint, {
                                        method: 'POST',
                                        keepalive: true,
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            menu_id: menuId,
                                            quantity: Math.abs(delta)
                                        })
                                    });
                                    flushPromises.push(p);
                                }
                            }
                        }
                        if (flushPromises.length > 0) {
                            window.flushPromise = Promise.all(flushPromises);
                        }
                    });
                },
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
                async syncCartData(menuId, price) {
                    const delta = this.pendingDelta[menuId];
                    if (!delta || delta === 0) return;
                    
                    this.pendingDelta[menuId] = 0;
                    
                    this.requestSequence++;
                    const currentSeq = this.requestSequence;
                    
                    window.isCartSyncing = true;
                    window.cartRequestQueue = window.cartRequestQueue.then(async () => {
                        try {
                            const endpoint = delta > 0 ? '{{ route("pelanggan.cart.add") }}' : '{{ route("pelanggan.cart.decrease") }}';
                            
                            const response = await fetch(endpoint, {
                                method: 'POST',
                                keepalive: true,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    menu_id: menuId,
                                    quantity: Math.abs(delta)
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
                                this.cart.totalQty -= delta;
                                this.cart.menuQty[menuId] -= delta;
                                if(price) this.cart.totalPrice -= (price * delta);
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message || 'Terjadi kesalahan saat mengupdate keranjang.',
                                    confirmButtonColor: '#E31E24'
                                });
                            }
                        } catch (error) {
                            console.error('Error syncing cart:', error);
                            // Rollback optimistic update on error
                            this.cart.totalQty -= delta;
                            this.cart.menuQty[menuId] -= delta;
                            if(price) this.cart.totalPrice -= (price * delta);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Jaringan',
                                text: 'Gagal menghubungi server. Silakan coba lagi.',
                                confirmButtonColor: '#E31E24'
                            });
                        }
                    }).finally(() => {
                        if (this.requestSequence === currentSeq) {
                            window.isCartSyncing = false;
                        }
                    });
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
                    
                    this.pendingDelta[menuId] = (this.pendingDelta[menuId] || 0) + 1;
                    
                    if (this.syncTimers[menuId]) clearTimeout(this.syncTimers[menuId]);
                    this.syncTimers[menuId] = setTimeout(() => {
                        this.syncCartData(menuId, price);
                    }, 400);
                },
                async decreaseCart(menuId, price) {
                    // Optimistic UI Update for instant feedback
                    if (this.cart.menuQty[menuId] > 0) {
                        this.cart.totalQty--;
                        this.cart.menuQty[menuId]--;
                        if(price) this.cart.totalPrice -= price;
                        
                        this.pendingDelta[menuId] = (this.pendingDelta[menuId] || 0) - 1;
                        
                        if (this.syncTimers[menuId]) clearTimeout(this.syncTimers[menuId]);
                        this.syncTimers[menuId] = setTimeout(() => {
                            this.syncCartData(menuId, price);
                        }, 400);
                    }
                },
                async submitModalForm(event, menuId, basePrice, extraPrice = 0) {
                    if (!this.canAddMenu()) return;
                    
                    const form = event.target;
                    const formData = new FormData(form);
                    const qty = parseInt(formData.get('quantity')) || 1;
                    
                    const catatan = (formData.get('catatan') || '').trim();
                    let hasCustomOptions = false;
                    for (let key of formData.keys()) {
                        if (key.startsWith('custom_options')) {
                            hasCustomOptions = true;
                            break;
                        }
                    }
                    
                    // Short-circuit: jika tidak ada perubahan sama sekali, abaikan pemrosesan
                    if (qty === 1 && catatan === '' && !hasCustomOptions) {
                        this.activeModal = null;
                        document.body.style.overflow = '';
                        return;
                    }
                    
                    if (this.cart.totalQty === 0) {
                        this.cart.kantinId = this.tenantKantinId;
                    }
                    
                    // Optimistic feedback
                    this.activeModal = null;
                    document.body.style.overflow = '';
                    
                    this.cart.totalQty += qty;
                    this.cart.totalPrice += ((basePrice + extraPrice) * qty);
                    this.cart.menuQty[menuId] = (this.cart.menuQty[menuId] || 0) + qty;
                    
                    this.requestSequence++;
                    const currentSeq = this.requestSequence;
                    
                    window.isCartSyncing = true;
                    window.cartRequestQueue = window.cartRequestQueue.then(async () => {
                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                keepalive: true,
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
                                this.cart.totalPrice -= ((basePrice + extraPrice) * qty);
                                this.cart.menuQty[menuId] -= qty;
                                
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
                            this.cart.totalPrice -= ((basePrice + extraPrice) * qty);
                            this.cart.menuQty[menuId] -= qty;
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Jaringan',
                                text: 'Gagal menghubungi server. Silakan coba lagi.',
                                confirmButtonColor: '#E31E24'
                            });
                        }
                    }).finally(() => {
                        if (this.requestSequence === currentSeq) {
                            window.isCartSyncing = false;
                        }
                    });
                }
            }));
        });
    </script>
</div>
@endsection
