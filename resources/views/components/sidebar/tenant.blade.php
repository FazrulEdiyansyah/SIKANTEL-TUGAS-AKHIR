@props(['active' => 'dashboard'])

<!-- Dashboard -->
<a href="/tenant/dashboard" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'dashboard' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'dashboard' ? 'ph-fill' : 'ph' }} ph-squares-four text-[22px] {{ $active === 'dashboard' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Dashboard</span>
</a>

<!-- Pesanan -->
<a href="{{ route('tenant.orders.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'pesanan' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'pesanan' ? 'ph-fill' : 'ph' }} ph-shopping-bag text-[22px] {{ $active === 'pesanan' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Pesanan</span>
</a>

<!-- Menu -->
<a href="{{ route('tenant.menu.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'menu' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'menu' ? 'ph-fill' : 'ph' }} ph-fork-knife text-[22px] {{ $active === 'menu' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Menu</span>
</a>

<!-- Rekap Penjualan -->
<a href="{{ route('tenant.reports.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'rekap' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'rekap' ? 'ph-fill' : 'ph' }} ph-chart-line-up text-[22px] {{ $active === 'rekap' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Rekap Penjualan</span>
</a>
