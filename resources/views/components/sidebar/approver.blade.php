@props(['active' => 'dashboard'])

@php
    $role = auth()->user()->role;
@endphp

<!-- Dashboard (Approval Pencairan) -->
<a href="{{ route($role.'.dashboard') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'dashboard' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'dashboard' ? 'ph-fill' : 'ph' }} ph-list-checks text-[22px] {{ $active === 'dashboard' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Approval Pencairan</span>
</a>

<!-- Riwayat Approval -->
<a href="{{ route($role.'.riwayat') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'riwayat' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'riwayat' ? 'ph-fill' : 'ph' }} ph-clock-counter-clockwise text-[22px] {{ $active === 'riwayat' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Riwayat Approval</span>
</a>

<!-- Data Kantin -->
<a href="{{ route($role.'.kantin.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'kantin' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'kantin' ? 'ph-fill' : 'ph' }} ph-storefront text-[22px] {{ $active === 'kantin' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Data Kantin</span>
</a>

<!-- Data Tenant -->
<a href="{{ route($role.'.tenant.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'tenant' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'tenant' ? 'ph-fill' : 'ph' }} ph-users text-[22px] {{ $active === 'tenant' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Data Tenant</span>
</a>

<!-- Semua Pesanan -->
<a href="{{ route($role.'.orders.index') }}" :class="desktopSidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'" class="flex items-center py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'orders' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i :class="desktopSidebarOpen ? 'mr-3' : 'mr-0'" class="{{ $active === 'orders' ? 'ph-fill' : 'ph' }} ph-receipt text-[22px] {{ $active === 'orders' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    <span x-show="desktopSidebarOpen" x-transition.opacity>Semua Pesanan</span>
</a>
