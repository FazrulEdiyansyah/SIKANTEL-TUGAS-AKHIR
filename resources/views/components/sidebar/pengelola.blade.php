@props(['active' => 'dashboard'])

<!-- Dashboard -->
<a href="{{ route('pengelola.dashboard') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'dashboard' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'dashboard' ? 'ph-fill' : 'ph' }} ph-squares-four text-[22px] {{ $active === 'dashboard' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Dashboard</span>
</a>

<!-- Data Kantin -->
<a href="{{ route('pengelola.kantin.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'kantin' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'kantin' ? 'ph-fill' : 'ph' }} ph-storefront text-[22px] {{ $active === 'kantin' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Data Kantin</span>
</a>

<!-- Data Tenant -->
<a href="{{ route('pengelola.tenant.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'tenant' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'tenant' ? 'ph-fill' : 'ph' }} ph-users text-[22px] {{ $active === 'tenant' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Data Tenant</span>
</a>

<!-- Rekap Penjualan -->
<a href="{{ route('pengelola.rekap.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'rekap' || request()->routeIs('pengelola.rekap.*') ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'rekap' || request()->routeIs('pengelola.rekap.*') ? 'ph-fill' : 'ph' }} ph-chart-line-up text-[22px] {{ $active === 'rekap' || request()->routeIs('pengelola.rekap.*') ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Rekap Penjualan</span>
</a>

<!-- Laporan Pencairan Dana -->
<a href="{{ route('pengelola.pencairan_dana.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'laporan' || request()->routeIs('pengelola.pencairan_dana.*') ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'laporan' || request()->routeIs('pengelola.pencairan_dana.*') ? 'ph-fill' : 'ph' }} ph-file-text text-[22px] {{ $active === 'laporan' || request()->routeIs('pengelola.pencairan_dana.*') ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity class="leading-tight">Laporan Pencairan<br>Dana</span>
</a>
