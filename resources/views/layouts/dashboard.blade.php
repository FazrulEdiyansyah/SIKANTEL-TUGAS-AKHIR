<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard SIKANTEL')</title>

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

    @stack('styles')
</head>
<body class="font-primary bg-[#F8F9FA] text-gray-800 antialiased overflow-hidden flex flex-col h-screen" x-data="{ sidebarOpen: false, desktopSidebarOpen: true }">
    
    <!-- Top Navbar -->
    <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 lg:px-10 shrink-0 relative z-50 w-full">
        
        <!-- Left: Mobile Toggle, Desktop Toggle & Logo -->
        <div class="flex items-center gap-4">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                <i class="ph ph-list text-[24px]"></i>
            </button>
            
            <!-- Desktop Toggle -->
            <button @click="desktopSidebarOpen = !desktopSidebarOpen" class="hidden lg:block p-2 -ml-2 text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors">
                <i class="ph ph-list text-[24px]"></i>
            </button>

            <!-- Logo in Navbar -->
            <a href="#" class="flex items-center shrink-0">
                <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-8 object-contain">
            </a>
        </div>

        <!-- Right: Icons & Profile -->
        <div class="flex items-center space-x-3 lg:space-x-5">

            <!-- Profile Dropdown -->
            <div x-data="{ profileOpen: false }" class="relative">
                <div @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center pl-3 lg:pl-4 border-l border-gray-200 cursor-pointer">
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

                <!-- Dropdown Menu -->
                <div x-show="profileOpen" 
                     x-transition.opacity.duration.200ms
                     class="absolute top-full right-0 mt-3 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50" style="display: none;">
                    <div class="px-4 py-2 border-b border-gray-50 mb-2 sm:hidden">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[12px] text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>

                    @php
                        $role = Auth::user()->role;
                        $profileRoute = '';
                        if ($role === 'tenant') $profileRoute = route('tenant.profile.index');
                        elseif ($role === 'pengelola') $profileRoute = route('pengelola.profile.index');
                        elseif ($role === 'kaur') $profileRoute = route('kaur.profile.index');
                        elseif ($role === 'kabag') $profileRoute = route('kabag.profile.index');
                    @endphp
                    
                    @if($profileRoute)
                        <a href="{{ $profileRoute }}" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 font-medium transition-colors flex items-center">
                            <i class="ph ph-user-circle mr-2 text-lg text-gray-400"></i> Profil Saya
                        </a>
                    @endif

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

    <!-- Main Wrapper (Content below Navbar) -->
    <div class="flex-1 flex overflow-hidden">

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
    <aside :class="{ 
            'translate-x-0': sidebarOpen, 
            '-translate-x-full': !sidebarOpen,
            'w-64': desktopSidebarOpen,
            'w-20': !desktopSidebarOpen
        }" class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-100 flex flex-col transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
        


        <!-- Navigation Menu (Dynamic depending on role) -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 scrollbar-hide">
            @yield('sidebar_menu')
        </nav>

    </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-6 lg:p-10 overflow-y-auto bg-[#F8F9FA]">
            <x-alert-toast />
            @yield('content')
        </main>
    </div>

    <x-form-loading />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmFormSubmit(event, message, type = 'warning') {
            event.preventDefault();
            const form = event.target || event.currentTarget; // In case icon is clicked
            // Ensure we get the form element
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
                        // Workaround for some cases where submit is overridden by a button with name="submit"
                        HTMLFormElement.prototype.submit.call(formElement);
                    }
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>
