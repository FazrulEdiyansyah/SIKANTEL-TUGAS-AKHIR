@extends('layouts.dashboard')

@section('title', 'Detail Tenant - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="tenant" />
@endsection

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('pengelola.tenant.index') }}" class="hover:text-telkom-red transition-colors font-medium">Data Tenant</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-900 font-semibold">Detail Tenant</span>
            </div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight">Detail Informasi Tenant</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('pengelola.tenant.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-semibold rounded-xl shadow-sm transition-colors">
                <i class="ph ph-arrow-left font-bold text-lg mr-2"></i>
                Kembali
            </a>
            <!-- Edit Button -->
            <a href="{{ route('pengelola.tenant.edit', $tenant->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-telkom-red hover:bg-telkom-maroon text-white font-semibold rounded-xl shadow-sm transition-colors">
                <i class="ph ph-pencil-simple font-bold text-lg mr-2"></i>
                Edit Tenant
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 flex flex-col items-center text-center">
                    <div class="w-32 h-32 rounded-full bg-orange-100 mb-5 overflow-hidden border-4 border-white shadow-md flex items-center justify-center relative">
                        @if($tenant->foto)
                            <img src="{{ asset('storage/' . $tenant->foto) }}" alt="{{ $tenant->nama_tenant }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-60" alt="no image">
                        @endif
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $tenant->nama_tenant }}</h2>
                    <p class="text-[14px] font-medium text-gray-500 mb-4">{{ $tenant->user->email ?? 'Belum ada email' }}</p>
                    
                    @if($tenant->status === 'aktif')
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-[13px] font-bold bg-green-50 text-green-600 border border-green-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span>
                            Tenant Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-[13px] font-bold bg-gray-50 text-gray-500 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-2"></span>
                            Tenant Non-Aktif
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Details Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Lengkap</h3>
                </div>
                <div class="p-0">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-gray-50">
                            <!-- Nama Tenant -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Nama Usaha / Tenant</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->nama_tenant }}</td>
                            </tr>
                            
                            <!-- Username Akun -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Username Sistem</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->user->name ?? '-' }}</td>
                            </tr>
                            
                            <!-- Email Akun -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Alamat Email Login</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->user->email ?? '-' }}</td>
                            </tr>

                            <!-- Kantin -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Lokasi Kantin</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">
                                    @if($tenant->kantin)
                                        <div class="flex flex-col">
                                            <span>{{ $tenant->kantin->nama_kantin }}</span>
                                            <span class="text-xs text-gray-500">{{ $tenant->kantin->lokasi }}</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>

                            <!-- Jenis Tenant -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Jenis Tenant</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->jenis_makanan }}</td>
                            </tr>

                            <!-- No Telepon -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Nomor Telepon</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->no_telepon }}</td>
                            </tr>

                            <!-- Tanggal Bergabung -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Tanggal Bergabung</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->created_at->translatedFormat('d F Y') }}</td>
                            </tr>

                            <!-- Data Tambahan (Personal & Bank) -->
                            <tr class="bg-gray-50/50">
                                <td colspan="2" class="py-3 px-6 text-[14px] font-bold text-gray-900 border-t border-gray-100">Informasi Pribadi & Bank</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">NIK</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->nik ?? '-' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Alamat Lengkap</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->address ?? '-' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Nama Bank</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->bank_name ?? '-' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Nomor Rekening</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->bank_account_number ?? '-' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Atas Nama Rekening</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">{{ $tenant->bank_account_name ?? '-' }}</td>
                            </tr>

                            <!-- Data Kontrak & Dokumen -->
                            <tr class="bg-gray-50/50">
                                <td colspan="2" class="py-3 px-6 text-[14px] font-bold text-gray-900 border-t border-gray-100">Kontrak & Dokumen</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Masa Kontrak</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">
                                    @if($tenant->contract_start_date && $tenant->contract_end_date)
                                        {{ $tenant->contract_start_date->translatedFormat('d F Y') }} s/d {{ $tenant->contract_end_date->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Dokumen KTP</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">
                                    @if($tenant->ktp_document)
                                        <a href="{{ asset('storage/' . $tenant->ktp_document) }}" target="_blank" class="text-telkom-red hover:underline font-semibold flex items-center">
                                            <i class="ph ph-file-pdf mr-2 text-lg"></i> Lihat KTP
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-semibold text-gray-500 w-1/3">Surat Perjanjian / Kontrak</td>
                                <td class="py-4 px-6 text-[15px] font-medium text-gray-900">
                                    @if($tenant->contract_document)
                                        <a href="{{ asset('storage/' . $tenant->contract_document) }}" target="_blank" class="text-telkom-red hover:underline font-semibold flex items-center">
                                            <i class="ph ph-file-pdf mr-2 text-lg"></i> Lihat Kontrak
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Menu -->
    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Daftar Menu Tenant</h3>
        <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="py-4 px-6 text-[13px] font-bold text-gray-900">No</th>
                            <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Foto</th>
                            <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Nama Menu</th>
                            <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Harga</th>
                            <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tenant->menus as $index => $menu)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $index + 1 }}</td>
                                <td class="py-4 px-6">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center">
                                        @if($menu->foto)
                                            <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="w-full h-full object-cover opacity-60">
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-bold text-[14px] text-gray-900">{{ $menu->nama_menu }}</td>
                                <td class="py-4 px-6 text-[14px] font-bold text-telkom-red">Rp{{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td class="py-4 px-6">
                                    @if($menu->status === 'tersedia')
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[12px] font-bold bg-green-50 text-green-600 border border-green-100 whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[12px] font-bold bg-red-50 text-telkom-red border border-red-100 whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-telkom-red mr-1.5"></span>
                                            Habis
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                                            <i class="ph ph-fork-knife text-3xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Menu</p>
                                        <p class="text-xs font-medium text-gray-500">Tenant ini belum menambahkan menu apapun.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
