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
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 h-20 px-6 lg:px-16 flex items-center justify-between">
        <!-- Left: Logo -->
        <a href="{{ route('pelanggan.dashboard') }}" class="flex items-center shrink-0">
            <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-8 object-contain">
        </a>

        <!-- Center: Nav Links -->
        <nav class="flex items-center space-x-6 lg:space-x-12 h-full">
            <a href="{{ route('pelanggan.dashboard') }}" class="h-full flex items-center text-xs lg:text-sm font-bold {{ request()->routeIs('pelanggan.dashboard') ? 'text-telkom-red border-b-2 border-telkom-red' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                Beranda
            </a>

            <a href="{{ route('pelanggan.orders.index') }}" class="h-full flex items-center text-xs lg:text-sm font-bold {{ request()->routeIs('pelanggan.orders.*') ? 'text-telkom-red border-b-2 border-telkom-red' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                Pesanan Saya
            </a>
        </nav>

        <!-- Right: Actions -->
        <div class="flex items-center space-x-4">
            <!-- Global Search Icon -->
            <a href="{{ route('pelanggan.search') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-600 transition-colors shadow-sm">
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
        $cartItems = session('cart') ?: [];
        $globalTotalQty = 0;
        $globalTotalPrice = 0;
        $globalItemNames = [];
        $globalMenuQtyData = [];
        $globalKantinId = null;

        if (count($cartItems) > 0) {
            $firstCartItem = reset($cartItems);
            $globalTenantId = $firstCartItem['tenant_id'] ?? null;
            if (!$globalTenantId) {
                $existingMenu = \App\Models\Menu::find($firstCartItem['menu_id']);
                $globalTenantId = $existingMenu ? $existingMenu->tenant_id : null;
            }
            if ($globalTenantId) {
                $existingTenant = \App\Models\Tenant::find($globalTenantId);
                $globalKantinId = $existingTenant ? $existingTenant->kantin_id : null;
            }
        }

        foreach($cartItems as $item) {
            $globalTotalQty += $item['quantity'];
            $globalTotalPrice += ($item['quantity'] * $item['harga']);
            $globalItemNames[] = $item['nama_menu'];
            
            if(!isset($globalMenuQtyData[$item['menu_id']])) $globalMenuQtyData[$item['menu_id']] = 0;
            $globalMenuQtyData[$item['menu_id']] += $item['quantity'];
        }
        $globalItemNamesStr = implode(', ', array_unique($globalItemNames));
    @endphp

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                totalQty: {{ $globalTotalQty }},
                totalPrice: {{ $globalTotalPrice }},
                kantinId: {{ $globalKantinId ?? 'null' }},
                itemNames: `{!! addslashes($globalItemNamesStr) !!}`,
                menuQty: {!! json_encode($globalMenuQtyData) !!},
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                }
            });
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
                        <a href="{{ route('pelanggan.checkout') }}" class="bg-white text-telkom-red font-bold text-sm px-5 py-2 rounded-xl hover:bg-red-50 transition-colors shadow-sm">
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
