@props(['active' => 'dashboard'])

<!-- Dashboard -->
<a href="{{ route('pengelola.dashboard') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'dashboard' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'dashboard' ? 'ph-fill' : 'ph' }} ph-squares-four text-[22px] mr-3 {{ $active === 'dashboard' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Dashboard
</a>

<!-- Data Kantin -->
<a href="{{ route('pengelola.kantin.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'kantin' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'kantin' ? 'ph-fill' : 'ph' }} ph-storefront text-[22px] mr-3 {{ $active === 'kantin' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Data Kantin
</a>

<!-- Data Tenant -->
<a href="{{ route('pengelola.tenant.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'tenant' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'tenant' ? 'ph-fill' : 'ph' }} ph-users text-[22px] mr-3 {{ $active === 'tenant' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Data Tenant
</a>

<!-- Rekap Penjualan -->
<a href="{{ route('pengelola.rekap.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('pengelola.rekap.*') ? 'bg-red-50 text-telkom-red font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
    <i class="ph {{ request()->routeIs('pengelola.rekap.*') ? 'ph-chart-line-up-fill' : 'ph-chart-line-up' }} text-xl transition-transform group-hover:scale-110"></i>
    <span class="text-[14px]">Rekap Penjualan</span>
</a>

<!-- Laporan Pencairan Dana -->
<a href="{{ route('pengelola.pencairan_dana.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('pengelola.pencairan_dana.*') ? 'bg-red-50 text-telkom-red font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
    <i class="ph {{ request()->routeIs('pengelola.pencairan_dana.*') ? 'ph-file-text-fill' : 'ph-file-text' }} text-xl transition-transform group-hover:scale-110"></i>
    <span class="text-[14px]">Laporan Pencairan<br>Dana</span>
</a>
