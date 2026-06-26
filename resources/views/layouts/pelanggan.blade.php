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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-primary bg-[#FAFBFC] text-gray-800 antialiased flex flex-col min-h-screen">

    <!-- Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 h-20 px-6 lg:px-16 flex items-center justify-between">
        <!-- Left: Logo -->
        <a href="{{ route('pelanggan.dashboard') }}" class="flex items-center shrink-0">
            <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-8 object-contain">
        </a>

        <!-- Center: Nav Links -->
        <nav class="hidden lg:flex items-center space-x-12 h-full">
            <a href="{{ route('pelanggan.dashboard') }}" class="h-full flex items-center text-sm font-bold {{ request()->routeIs('pelanggan.dashboard') ? 'text-telkom-red border-b-2 border-telkom-red' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                Beranda
            </a>

            <a href="{{ route('pelanggan.orders.index') }}" class="h-full flex items-center text-sm font-bold {{ request()->routeIs('pelanggan.orders.*') ? 'text-telkom-red border-b-2 border-telkom-red' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                Pesanan Saya
            </a>
        </nav>

        <!-- Right: Actions -->
        <div class="flex items-center space-x-4">
            <!-- Search -->
            <button class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                <i class="ph ph-magnifying-glass text-xl"></i>
            </button>

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
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-telkom-red hover:bg-red-50 font-semibold transition-colors flex items-center">
                            <i class="ph ph-sign-out mr-2 text-lg"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="ph ph-list text-2xl"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-8 mt-12">
        <div class="container mx-auto px-6 lg:px-16 text-center">
            <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-6 object-contain mx-auto mb-4 opacity-50 grayscale">
            <p class="text-[13px] font-medium text-gray-400">© {{ date('Y') }} SIKANTEL - Sistem Informasi Kantin Telkom University.</p>
        </div>
    </footer>

    <!-- Global Toast Notification (CSS Only) -->
    @if(session('success_cart'))
        <div class="fixed top-24 left-1/2 -translate-x-1/2 z-[100] animate-fade-in-down pointer-events-none">
            <div class="bg-green-500 text-white px-6 py-3 rounded-full shadow-xl font-bold text-sm flex items-center">
                <i class="ph-fill ph-check-circle text-xl mr-2"></i>
                {{ session('success_cart') }}
            </div>
        </div>
        <style>
            @keyframes fadeInDown {
                0% { opacity: 0; transform: translate(-50%, -20px); }
                10%, 90% { opacity: 1; transform: translate(-50%, 0); }
                100% { opacity: 0; transform: translate(-50%, -20px); }
            }
            .animate-fade-in-down {
                animation: fadeInDown 3s ease-in-out forwards;
            }
        </style>
    @endif

</body>
</html>
