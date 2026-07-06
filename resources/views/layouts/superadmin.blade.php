<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard - {{ config('app.name', 'SIKANTEL') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Phosphor Icons (for global components) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #fff;
        }
    </style>
    @stack('styles')
</head>
<body class="font-primary bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#1e1e2d] text-gray-300 flex flex-col h-full shrink-0">
        <!-- Sidebar Header -->
        <div class="h-16 flex items-center px-6 border-b border-gray-700">
            <span class="text-xl font-bold text-white tracking-wide">SuperAdmin.</span>
        </div>
        
        <!-- Sidebar Navigation -->
        <div class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.dashboard') ? 'sidebar-active text-white' : '' }}">
                        <i class="fa-solid fa-table-cells-large w-5 text-center mr-3"></i>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.users.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.users.*') ? 'sidebar-active text-white' : '' }}">
                        <i class="fa-solid fa-users w-5 text-center mr-3"></i>
                        <span class="text-sm font-medium">Users</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('superadmin.kantin.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.kantin.*') ? 'sidebar-active text-white' : '' }}">
                        <i class="fa-solid fa-store w-5 text-center mr-3"></i>
                        <span class="text-sm font-medium">Kantin</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.tenant.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.tenant.*') ? 'sidebar-active text-white' : '' }}">
                        <i class="fa-solid fa-shop w-5 text-center mr-3"></i>
                        <span class="text-sm font-medium">Tenants</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.orders.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.orders.*') ? 'sidebar-active text-white' : '' }}">
                        <i class="fa-solid fa-cart-shopping w-5 text-center mr-3"></i>
                        <span class="text-sm font-medium">Orders</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.pencairan.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.pencairan.*') && !request()->routeIs('superadmin.pencairan.create') && !request()->routeIs('superadmin.pencairan.edit') ? 'sidebar-active text-white' : '' }}">
                        <i class="fa-solid fa-money-bill-transfer w-5 text-center mr-3"></i>
                        <span class="text-sm font-medium">Pencairan Dana</span>
                    </a>
                </li>
                

            </ul>
        </div>
        
        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-gray-700 text-xs text-center text-gray-500">
            &copy; 2026 SIKANTEL
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-white flex items-center justify-between px-6 border-b shadow-sm z-10 shrink-0">
            <div class="flex items-center">
                <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center space-x-4">
                
                <!-- Buat Data Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg font-medium text-sm hover:bg-blue-100 transition-colors">
                        <i class="fa-solid fa-plus"></i>
                        <span>Buat Data</span>
                        <i class="fa-solid fa-chevron-down text-xs ml-1" :class="{'rotate-180': open}"></i>
                    </button>
                    
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 border border-gray-100 z-50">
                        
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Pilih Data
                        </div>
                        
                        <a href="{{ route('superadmin.kantin.create') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <i class="fa-solid fa-store w-5 text-center mr-2 text-gray-400"></i>
                            Data Kantin
                        </a>
                        
                        <a href="{{ route('superadmin.tenant.create') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <i class="fa-solid fa-shop w-5 text-center mr-2 text-gray-400"></i>
                            Data Tenant
                        </a>
                        
                        <a href="{{ route('superadmin.pencairan.create') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <i class="fa-solid fa-money-bill-transfer w-5 text-center mr-2 text-gray-400"></i>
                            Laporan Pencairan
                        </a>
                    </div>
                </div>

                <div class="h-8 w-px bg-gray-200"></div>
                
                <!-- Profile Dropdown (AlpineJS) -->
                <div x-data="{ openProfile: false }" class="relative flex items-center space-x-2 cursor-pointer">
                    <div @click="openProfile = !openProfile" @click.away="openProfile = false" class="flex items-center space-x-2 select-none">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Super Administrator' }}</span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400" :class="{'rotate-180': openProfile}"></i>
                    </div>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="openProfile" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 top-full mt-3 w-48 bg-white rounded-xl shadow-lg py-1 border border-gray-100 z-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                                <i class="fa-solid fa-sign-out-alt w-5 text-center mr-2"></i> Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 relative">
            
            <!-- Breadcrumbs -->
            <div class="mb-6 text-sm text-gray-500 flex items-center space-x-2">
                <a href="{{ route('superadmin.dashboard') }}" class="hover:text-blue-600 transition-colors">Home</a>
                @hasSection('breadcrumb')
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-700">@yield('breadcrumb')</span>
                @endif
            </div>

            <x-alert-toast />

            <!-- Main Yield -->
            @yield('content')
            
        </main>
    </div>

    <x-form-loading />
    @stack('scripts')
</body>
</html>
