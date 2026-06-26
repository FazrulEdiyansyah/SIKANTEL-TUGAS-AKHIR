@extends('layouts.superadmin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Card 1 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Users</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
            <i class="fa-solid fa-users text-blue-500 text-xl"></i>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Orders</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalOrders }}</h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center">
            <i class="fa-solid fa-cart-shopping text-purple-500 text-xl"></i>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Laporan Pencairan</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalPencairan }}</h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center">
            <i class="fa-solid fa-file-invoice-dollar text-orange-500 text-xl"></i>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Tenants / Kantin</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalTenant }} / {{ $totalKantin }}</h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
            <i class="fa-solid fa-store text-green-500 text-xl"></i>
        </div>
    </div>

</div>

<!-- Example Chart / Table Area -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-800">Welcome Superadmin</h2>
        <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All Data <i class="fa-solid fa-arrow-right ml-1"></i></button>
    </div>
    
    <div class="text-gray-600 text-sm">
        <p class="mb-4">You have full access to manage the SIKANTEL system. From here you can:</p>
        <ul class="list-disc pl-5 space-y-2">
            <li>Manage user accounts and assign roles.</li>
            <li>Add, edit, or remove system roles.</li>
            <li>Monitor all Kantin and Tenant activities.</li>
            <li>Review all customer orders and statuses.</li>
            <li>Manage fund disbursements (Pencairan Dana) for all tenants.</li>
        </ul>
    </div>
</div>
@endsection
