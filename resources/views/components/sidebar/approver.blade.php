@props(['active' => 'dashboard'])

@php
    $role = auth()->user()->role;
@endphp

<!-- Dashboard (Approval Pencairan) -->
<a href="{{ route($role.'.dashboard') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'dashboard' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'dashboard' ? 'ph-fill' : 'ph' }} ph-list-checks text-[22px] mr-3 {{ $active === 'dashboard' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Approval Pencairan
</a>

<!-- Data Kantin -->
<a href="{{ route($role.'.kantin.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'kantin' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'kantin' ? 'ph-fill' : 'ph' }} ph-storefront text-[22px] mr-3 {{ $active === 'kantin' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Data Kantin
</a>

<!-- Data Tenant -->
<a href="{{ route($role.'.tenant.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'tenant' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'tenant' ? 'ph-fill' : 'ph' }} ph-users text-[22px] mr-3 {{ $active === 'tenant' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Data Tenant
</a>

<!-- Data Pesanan -->
<a href="{{ route($role.'.orders.index') }}" class="flex items-center px-4 py-3.5 text-[15px] font-semibold rounded-xl transition-colors group {{ $active === 'orders' ? 'text-telkom-red bg-red-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 font-medium' }}">
    <i class="{{ $active === 'orders' ? 'ph-fill' : 'ph' }} ph-shopping-cart text-[22px] mr-3 {{ $active === 'orders' ? '' : 'text-gray-400 group-hover:text-gray-600 transition-colors' }}"></i>
    Data Pesanan
</a>
