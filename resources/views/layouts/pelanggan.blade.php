<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIKANTEL - Pelanggan')</title>

    <!-- Midtrans Snap -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <!-- Font Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body x-data class="font-primary bg-[#FAFBFC] text-gray-800 antialiased flex flex-col min-h-screen">

    <!-- Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 h-20 px-4 sm:px-6 lg:px-16 flex items-center justify-between">
        <!-- Left: Logo -->
        <a href="{{ route('pelanggan.dashboard') }}" class="flex items-center shrink-0">
            <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-8 object-contain">
        </a>

        <!-- Center: Nav Links -->
        <nav class="flex items-center space-x-3 sm:space-x-6 lg:space-x-12 h-full">
            <a href="{{ route('pelanggan.dashboard') }}" class="h-full flex items-center text-xs lg:text-sm font-bold {{ request()->routeIs('pelanggan.dashboard') ? 'text-telkom-red border-b-2 border-telkom-red' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                Beranda
            </a>

            <a href="{{ route('pelanggan.orders.index') }}" class="h-full flex items-center text-xs lg:text-sm font-bold {{ request()->routeIs('pelanggan.orders.*') ? 'text-telkom-red border-b-2 border-telkom-red' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                Pesanan Saya
            </a>
        </nav>

        <!-- Right: Actions -->
        <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Global Search Component -->
            <div x-data="globalSearch()" class="relative hidden lg:block" @click.away="close()">
                <div class="relative">
                    <input type="text" x-model="query" @input.debounce.300ms="fetchResults" @focus="open = true" @keydown.escape="close()" placeholder="Cari menu, tenant..." class="w-64 pl-10 pr-4 py-2.5 bg-gray-50 hover:bg-gray-100 focus:bg-white border border-gray-100 focus:border-telkom-red focus:ring-1 focus:ring-telkom-red rounded-full text-[13px] font-medium transition-all outline-none" autocomplete="off">
                    <i class="ph-bold ph-magnifying-glass absolute left-3.5 top-[11px] text-gray-400 text-lg"></i>
                    
                    <!-- Loading Spinner -->
                    <div x-show="loading" class="absolute right-3.5 top-[11px]">
                        <i class="ph-bold ph-spinner animate-spin text-gray-400 text-lg"></i>
                    </div>
                </div>

                <!-- Dropdown Results -->
                <div x-show="open && (query.length > 0)" x-transition class="absolute top-full mt-2 w-80 right-0 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                    <div x-show="!loading && results.menus.length === 0 && results.tenants.length === 0 && query.length >= 2" class="p-6 text-center text-sm text-gray-500">
                        <i class="ph-fill ph-magnifying-glass text-3xl text-gray-300 mb-2 block"></i>
                        Tidak ada hasil ditemukan.
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto" x-show="!loading && (results.menus.length > 0 || results.tenants.length > 0)">
                        
                        <!-- Tenants Section -->
                        <template x-if="results.tenants.length > 0">
                            <div>
                                <div class="px-4 py-2.5 bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tenant</div>
                                <template x-for="tenant in results.tenants" :key="tenant.id">
                                    <a :href="tenant.url" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                        <img :src="tenant.foto" class="w-9 h-9 rounded-full object-cover shadow-sm">
                                        <div>
                                            <p class="text-[13px] font-bold text-gray-900" x-text="tenant.name"></p>
                                            <p class="text-[11px] font-medium text-gray-500" x-text="tenant.kantin_name"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>

                        <!-- Menus Section -->
                        <template x-if="results.menus.length > 0">
                            <div>
                                <div class="px-4 py-2.5 bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Menu</div>
                                <template x-for="menu in results.menus" :key="menu.id">
                                    <a :href="menu.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0 group">
                                        <img :src="menu.foto" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[13px] font-bold text-gray-900 truncate group-hover:text-telkom-red transition-colors" x-text="menu.name"></p>
                                            <p class="text-[11px] font-medium text-gray-500 truncate" x-text="menu.tenant_name"></p>
                                        </div>
                                        <div class="text-right shrink-0 pl-2">
                                            <p class="text-[12px] font-bold text-telkom-red">Rp<span x-text="new Intl.NumberFormat('id-ID').format(menu.harga)"></span></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>

                    </div>
                    
                    <a :href="'{{ route('pelanggan.search') }}?search=' + encodeURIComponent(query)" class="block text-center py-2.5 bg-gray-50 text-telkom-red text-xs font-bold hover:bg-red-50 transition-colors">
                        Lihat Semua Hasil
                    </a>
                </div>
            </div>
            
            <!-- Mobile Search Icon (Links to search page) -->
            <a href="{{ route('pelanggan.search') }}" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-600 transition-colors shadow-sm">
                <i class="ph-bold ph-magnifying-glass text-lg"></i>
            </a>
            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 px-3 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-100 rounded-full transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm">
                        <i class="ph ph-user"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 hidden sm:block">Pelanggan</span>
                    <i class="ph ph-caret-down text-gray-400 text-xs hidden sm:block"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition.opacity.duration.200ms
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50" style="display: none;">
                    <div class="px-4 py-2 border-b border-gray-50 mb-2">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[12px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('pelanggan.profile.index') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 font-medium transition-colors flex items-center">
                        <i class="ph ph-user-circle mr-2 text-lg text-gray-400"></i> Profil Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-telkom-red hover:bg-red-50 font-semibold transition-colors flex items-center">
                            <i class="ph ph-sign-out mr-2 text-lg"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-3 mt-4">
        <div class="container mx-auto px-6 lg:px-16 text-center">
            <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-4 object-contain mx-auto mb-1.5 opacity-50 grayscale">
            <p class="text-[11px] font-medium text-gray-400">© {{ date('Y') }} SIKANTEL - Sistem Informasi Kantin Telkom University.</p>
        </div>
    </footer>

    <!-- Global Cart State & Banner -->
    @php
        $cartModel = \App\Models\Cart::with('items')->where('user_id', auth()->id())->first();
        $cartItems = $cartModel ? $cartModel->items : collect();
        
        $globalTotalQty = 0;
        $globalTotalPrice = 0;
        $globalItemNames = [];
        $globalMenuQtyData = [];
        $globalKantinId = null;

        if ($cartItems->count() > 0) {
            $firstCartItem = $cartItems->first();
            $globalTenantId = $firstCartItem->tenant_id ?? null;
            if ($globalTenantId) {
                $existingTenant = \App\Models\Tenant::find($globalTenantId);
                $globalKantinId = $existingTenant ? $existingTenant->kantin_id : null;
            }
        }

        foreach($cartItems as $item) {
            $globalTotalQty += $item->quantity;
            $globalTotalPrice += ($item->quantity * $item->harga);
            $globalItemNames[] = $item->nama_menu;
            
            if(!isset($globalMenuQtyData[$item->menu_id])) $globalMenuQtyData[$item->menu_id] = 0;
            $globalMenuQtyData[$item->menu_id] += $item->quantity;
        }
        $globalItemNamesStr = implode(', ', array_unique($globalItemNames));
    @endphp

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('globalSearch', () => ({
                open: false,
                query: '',
                loading: false,
                results: { menus: [], tenants: [] },
                
                async fetchResults() {
                    if (this.query.length < 2) {
                        this.results = { menus: [], tenants: [] };
                        return;
                    }
                    
                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('pelanggan.search.autocomplete') }}?q=${encodeURIComponent(this.query)}`);
                        const data = await response.json();
                        this.results = data;
                        this.open = true;
                    } catch (error) {
                        console.error('Search error:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                
                close() {
                    this.open = false;
                }
            }));

            Alpine.store('cart', {
                totalQty: {{ $globalTotalQty }},
                totalPrice: {{ $globalTotalPrice }},
                kantinId: {{ $globalKantinId ?? 'null' }},
                itemNames: `{!! addslashes($globalItemNamesStr) !!}`,
                menuQty: {!! json_encode($globalMenuQtyData) !!},
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },
                async goToCheckout(url) {
                    // Tunggu antrean selesai secara diam-diam tanpa animasi
                    if (window.cartRequestQueue && window.isCartSyncing) {
                        await window.cartRequestQueue;
                    }
                    window.location.href = url;
                }
            });
            window.cartRequestQueue = Promise.resolve();
            window.isCartSyncing = false;
        });
    </script>

    @if(request()->routeIs('pelanggan.tenant.show') || request()->routeIs('pelanggan.kantin.show'))
        <template x-if="$store.cart.totalQty > 0">
            <div class="fixed bottom-0 inset-x-0 p-4 z-40 flex justify-center pointer-events-none">
                <div x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-y-full opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="translate-y-0 opacity-100"
                     x-transition:leave-end="translate-y-full opacity-0"
                     class="bg-[#E31E24] w-full max-w-3xl rounded-2xl shadow-2xl p-4 flex items-center justify-between text-white pointer-events-auto">
                    <div class="flex-1 flex flex-col">
                        <span class="font-bold text-sm mb-0.5"><span x-text="$store.cart.totalQty"></span> item</span>
                        <span class="text-xs text-white/80 font-medium truncate pr-4" x-text="$store.cart.itemNames"></span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-lg">Rp <span x-text="$store.cart.formatPrice($store.cart.totalPrice)"></span></span>
                        <a href="{{ route('pelanggan.checkout') }}" @click.prevent="$store.cart.goToCheckout('{{ route('pelanggan.checkout') }}')" class="bg-white text-telkom-red font-bold text-sm px-5 py-2 rounded-xl hover:bg-red-50 transition-colors shadow-sm">
                            Keranjang
                        </a>
                    </div>
                </div>
            </div>
        </template>
    @endif

    <x-alert-toast />
    <x-form-loading />

    <script>
        function confirmFormSubmit(event, message, type = 'warning') {
            event.preventDefault();
            const form = event.target || event.currentTarget; 
            const formElement = form.closest ? form.closest('form') : form;
            
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: type,
                showCancelButton: true,
                confirmButtonColor: '#E31E24',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (formElement && typeof formElement.submit === 'function') {
                        formElement.submit();
                    } else if (formElement && typeof formElement.submit === 'object') {
                        HTMLFormElement.prototype.submit.call(formElement);
                    }
                }
            });
        }
    </script>
</body>
</html>
