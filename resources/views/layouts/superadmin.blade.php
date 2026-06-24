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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #fff;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#1e1e2d] text-gray-300 flex flex-col h-full shrink-0">
        <!-- Sidebar Header -->
        <div class="h-16 flex items-center px-6 border-b border-gray-700">
            <span class="text-xl font-bold text-white tracking-wide">Admin.</span>
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
                    <a href="{{ route('superadmin.roles.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.roles.*') ? 'sidebar-active text-white' : '' }}">
                        <i class="fa-solid fa-user-shield w-5 text-center mr-3"></i>
                        <span class="text-sm font-medium">Roles</span>
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
                    <a href="{{ route('superadmin.pencairan.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.pencairan.*') ? 'sidebar-active text-white' : '' }}">
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
                <button class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-bell"></i>
                </button>
                <div class="h-8 w-px bg-gray-200"></div>
                
                <!-- Profile Dropdown (Simplified) -->
                <div class="relative flex items-center space-x-2 cursor-pointer group">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Superadmin' }}</span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-solid fa-sign-out-alt mr-2"></i> Log Out
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

            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <!-- Main Yield -->
            @yield('content')
            
        </main>
    </div>

    @stack('scripts')
</body>
</html>
