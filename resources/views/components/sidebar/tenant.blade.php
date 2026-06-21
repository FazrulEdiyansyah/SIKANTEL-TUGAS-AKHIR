@props(['active' => 'dashboard'])

<!-- Dashboard -->
<a href="/tenant/dashboard" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'dashboard' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'dashboard' ? 'ph-fill' : 'ph' }} ph-squares-four text-[22px] mr-3 {{ $active === 'dashboard' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Dashboard
</a>

<!-- Pesanan -->
<a href="{{ route('tenant.orders.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'pesanan' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'pesanan' ? 'ph-fill' : 'ph' }} ph-shopping-bag text-[22px] mr-3 {{ $active === 'pesanan' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Pesanan
</a>

<!-- Menu -->
<a href="{{ route('tenant.menu.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'menu' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'menu' ? 'ph-fill' : 'ph' }} ph-fork-knife text-[22px] mr-3 {{ $active === 'menu' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Menu
</a>

<!-- Rekap Penjualan -->
<a href="{{ route('tenant.reports.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'rekap' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'rekap' ? 'ph-fill' : 'ph' }} ph-chart-line-up text-[22px] mr-3 {{ $active === 'rekap' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Rekap Penjualan
</a>
