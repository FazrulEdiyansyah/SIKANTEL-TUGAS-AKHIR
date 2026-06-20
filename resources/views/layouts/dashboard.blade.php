<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard SIKANTEL')</title>

    <!-- Font Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-poppins bg-[#F8F9FA] text-gray-800 antialiased overflow-x-hidden flex h-screen" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" 
         @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
        
        <!-- Logo -->
        <div class="h-20 flex items-center px-8 border-b border-gray-50/50 shrink-0">
            <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-8 object-contain">
        </div>

        <!-- Navigation Menu (Dynamic depending on role) -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 scrollbar-hide">
            @yield('sidebar_menu')
        </nav>

        <!-- Logout Button -->
        <div class="p-4 border-t border-gray-50/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 text-[15px] font-bold text-telkom-red hover:bg-red-50 rounded-xl transition-colors">
                    <i class="ph ph-sign-out text-[22px] mr-3"></i>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden lg:ml-0 transition-all duration-300">
        
        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 lg:px-10 shrink-0 sticky top-0 z-30">
            
            <!-- Left: Mobile Toggle -->
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="p-2 mr-3 -ml-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <i class="ph ph-list text-[24px]"></i>
                </button>
            </div>

            <!-- Right: Icons & Profile -->
            <div class="flex items-center space-x-3 lg:space-x-5">
                <!-- Search Icon -->
                <button class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors hidden sm:block">
                    <i class="ph ph-magnifying-glass text-[22px]"></i>
                </button>

                <!-- Notification -->
                <button class="relative p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors mr-2">
                    <i class="ph ph-bell text-[22px]"></i>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-telkom-red rounded-full ring-2 ring-white"></span>
                </button>

                <!-- Profile Dropdown -->
                <div class="flex items-center pl-3 lg:pl-4 border-l border-gray-200 cursor-pointer">
                    <!-- User Avatar -->
                    <div class="w-10 h-10 rounded-full bg-red-100 text-telkom-red flex items-center justify-center font-bold text-lg ring-2 ring-white shadow-sm mr-3">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <!-- User Name & Role -->
                    <div class="flex flex-col items-start mr-3 hidden sm:flex">
                        <span class="text-sm font-bold text-gray-800 leading-tight truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                        <span class="text-[12px] font-medium text-gray-500 capitalize">{{ Auth::user()->role }}</span>
                    </div>
                    <i class="ph ph-caret-down text-gray-400 text-sm"></i>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 p-6 lg:p-10 pt-4 overflow-y-auto bg-[#F8F9FA]">
            @yield('content')
        </div>

    </main>

</body>
</html>
