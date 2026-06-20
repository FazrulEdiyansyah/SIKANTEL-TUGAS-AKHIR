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
<a href="#" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'rekap' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'rekap' ? 'ph-fill' : 'ph' }} ph-chart-line-up text-[22px] mr-3 {{ $active === 'rekap' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Rekap Penjualan
</a>

<!-- Laporan Pencairan Dana -->
<a href="#" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'laporan' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'laporan' ? 'ph-fill' : 'ph' }} ph-file-text text-[22px] mr-3 {{ $active === 'laporan' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Laporan Pencairan Dana
</a>
