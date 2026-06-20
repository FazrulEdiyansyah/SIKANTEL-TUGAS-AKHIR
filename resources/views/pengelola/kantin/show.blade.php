@extends('layouts.dashboard')

@section('title', 'Detail Kantin - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="kantin" />
@endsection

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('pengelola.kantin.index') }}" class="hover:text-telkom-red transition-colors font-medium">Data Kantin</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-900 font-semibold">Detail Kantin</span>
            </div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight">Detail Informasi Kantin</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('pengelola.kantin.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-semibold rounded-xl shadow-sm transition-colors">
                <i class="ph ph-arrow-left font-bold text-lg mr-2"></i>
                Kembali
            </a>
            <button class="inline-flex items-center justify-center px-5 py-2.5 bg-telkom-red hover:bg-telkom-maroon text-white font-semibold rounded-xl shadow-sm transition-colors">
                <i class="ph ph-pencil-simple font-bold text-lg mr-2"></i>
                Edit Kantin
            </button>
        </div>
    </div>

    <!-- Profil Kantin -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Left Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden h-full">
                <div class="p-8 flex flex-col items-center text-center">
                    <div class="w-32 h-32 rounded-2xl bg-orange-100 mb-5 overflow-hidden border-4 border-white shadow-md flex items-center justify-center relative">
                        @if($kantin->foto)
                            <img src="{{ asset('storage/' . $kantin->foto) }}" alt="{{ $kantin->nama_kantin }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60" alt="no image">
                        @endif
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $kantin->nama_kantin }}</h2>
                    
                    @if($kantin->status === 'aktif')
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-[13px] font-bold bg-green-50 text-green-600 border border-green-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span>
                            Kantin Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-[13px] font-bold bg-gray-50 text-gray-500 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-2"></span>
                            Kantin Non-Aktif
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Details Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden h-full">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Lokasi</h3>
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                        <i class="ph-fill ph-map-pin text-xl"></i>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-sm font-semibold text-gray-500 mb-1">Alamat / Lokasi Gedung</p>
                        <p class="text-[15px] font-medium text-gray-900">{{ $kantin->lokasi }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                            <p class="text-sm font-semibold text-gray-500 mb-1">Total Tenant Terdaftar</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $kantin->tenants->count() }} <span class="text-sm font-medium text-gray-500">Tenant</span></p>
                        </div>
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                            <p class="text-sm font-semibold text-gray-500 mb-1">Tanggal Ditambahkan</p>
                            <p class="text-[15px] font-medium text-gray-900 mt-2">{{ $kantin->created_at->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Tenant -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Daftar Tenant di {{ $kantin->nama_kantin }}</h3>
                <p class="text-[14px] text-gray-500 font-medium">Semua tenant yang berjualan di lokasi ini.</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">No</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Nama Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Jenis Tenant</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">No. Telepon</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900 text-center">Status</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kantin->tenants as $index => $tenant)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 mr-3 overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center">
                                        @if($tenant->foto)
                                            <img src="{{ asset('storage/' . $tenant->foto) }}" alt="{{ $tenant->nama_tenant }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60" alt="no image">
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-[14px] text-gray-900">{{ $tenant->nama_tenant }}</span>
                                        <span class="text-[12px] text-gray-500 mt-0.5">{{ $tenant->user->email ?? 'Belum ada email' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $tenant->jenis_makanan }}</td>
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $tenant->no_telepon }}</td>
                            <td class="py-4 px-6 text-center">
                                @if($tenant->status === 'aktif')
                                    <span class="inline-flex items-center justify-center w-24 py-1.5 rounded-full text-[12px] font-bold bg-green-50 text-green-600 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-24 py-1.5 rounded-full text-[12px] font-bold bg-gray-50 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('pengelola.tenant.show', $tenant->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 border border-telkom-red text-telkom-red hover:bg-red-50 text-[13px] font-bold rounded-lg transition-colors">
                                    <i class="ph ph-eye mr-1.5"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ph ph-users text-4xl text-gray-200 mb-3"></i>
                                    <p class="text-sm font-medium text-gray-400">Belum ada tenant yang terdaftar di kantin ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
