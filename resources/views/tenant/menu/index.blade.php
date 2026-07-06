@extends('layouts.dashboard')

@section('title', 'Menu - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="menu" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Menu</h1>
            <p class="text-[15px] text-gray-500 font-medium">Kelola menu, harga, dan ketersediaan menu tenant Anda.</p>
        </div>
        <a href="{{ route('tenant.menu.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-telkom-red hover:bg-telkom-maroon text-white font-semibold rounded-xl shadow-sm transition-colors shrink-0">
            <i class="ph ph-plus font-bold text-lg mr-2"></i>
            Tambah Menu
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                <i class="ph-fill ph-fork-knife text-[28px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Total Menu</p>
                <div class="flex items-end">
                    <h3 class="text-3xl font-bold text-gray-900 leading-none mr-2">{{ $totalMenu }}</h3>
                    <span class="text-[13px] font-medium text-gray-400 mb-0.5">menu</span>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                <i class="ph-fill ph-check-circle text-[28px] text-green-500"></i>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Menu Tersedia</p>
                <div class="flex items-end">
                    <h3 class="text-3xl font-bold text-gray-900 leading-none mr-2">{{ $menuTersedia }}</h3>
                    <span class="text-[13px] font-medium text-gray-400 mb-0.5">menu</span>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                <i class="ph-fill ph-x-circle text-[28px] text-telkom-red"></i>
            </div>
            <div>
                <p class="text-[14px] font-semibold text-gray-500 mb-0.5">Menu Habis</p>
                <div class="flex items-end">
                    <h3 class="text-3xl font-bold text-gray-900 leading-none mr-2">{{ $menuHabis }}</h3>
                    <span class="text-[13px] font-medium text-gray-400 mb-0.5">menu</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Box -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <!-- Filter & Search Bar -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form action="{{ route('tenant.menu.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center w-full gap-4">
                <!-- Search -->
                <div class="relative w-full md:flex-1">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..." class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all">
                </div>
                
                <!-- Filter Dropdown -->
                <div class="relative w-full md:w-64 shrink-0 flex items-center gap-2">
                    <i class="ph ph-list text-gray-400 text-lg"></i>
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-white border border-gray-200 text-sm text-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>
                <!-- Submit hidden button if needed for enter key search -->
                <button type="submit" class="hidden"></button>
            </form>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">No <i class="ph ph-caret-down align-middle text-gray-400 ml-1"></i></th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Foto</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Nama Menu <i class="ph ph-caret-down align-middle text-gray-400 ml-1"></i></th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Deskripsi <i class="ph ph-caret-down align-middle text-gray-400 ml-1"></i></th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Harga <i class="ph ph-caret-down align-middle text-gray-400 ml-1"></i></th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Status <i class="ph ph-caret-down align-middle text-gray-400 ml-1"></i></th>
                        <th class="py-4 px-6 text-[13px] font-bold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($menus as $index => $menu)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $menus->firstItem() + $index }}</td>
                            <td class="py-4 px-6">
                                <div class="w-14 h-14 rounded-lg bg-gray-100 overflow-hidden border border-gray-100 shrink-0 flex items-center justify-center">
                                    @if($menu->foto)
                                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="w-full h-full object-cover opacity-60">
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 font-bold text-[14px] text-gray-900">{{ $menu->nama_menu }}</td>
                            <td class="py-4 px-6 text-[13px] font-medium text-gray-500 max-w-xs">{{ Str::limit($menu->deskripsi, 50, '...') ?? '-' }}</td>
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
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <!-- Edit Button -->
                                    <a href="{{ route('tenant.menu.edit', $menu->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 border border-telkom-red text-telkom-red hover:bg-red-50 text-[12px] font-bold rounded-lg transition-colors whitespace-nowrap">
                                        <i class="ph ph-pencil-simple mr-1"></i> Edit
                                    </a>
                                    
                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('tenant.menu.toggle-status', $menu->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        @if($menu->status === 'tersedia')
                                            <button type="submit" class="inline-flex items-center justify-center px-4 py-1.5 bg-telkom-red text-white hover:bg-telkom-maroon text-[12px] font-bold rounded-lg transition-colors whitespace-nowrap border border-telkom-red">
                                                Tandai Habis
                                            </button>
                                        @else
                                            <button type="submit" class="inline-flex items-center justify-center px-4 py-1.5 border border-telkom-red text-telkom-red hover:bg-red-50 text-[12px] font-bold rounded-lg transition-colors whitespace-nowrap">
                                                Tandai Tersedia
                                            </button>
                                        @endif
                                    </form>

                                    <!-- 3 dots / Delete -->
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" @click.away="open = false" class="p-1.5 text-gray-400 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                                            <i class="ph ph-dots-three-vertical text-xl"></i>
                                        </button>
                                        
                                        <div x-show="open" 
                                             x-transition.opacity.duration.200ms
                                             class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10" style="display: none;">
                                            <form action="{{ route('tenant.menu.destroy', $menu->id) }}" method="POST" onsubmit="confirmFormSubmit(event, 'Apakah Anda yakin ingin menghapus menu ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-telkom-red hover:bg-red-50 font-medium transition-colors flex items-center">
                                                    <i class="ph ph-trash mr-2"></i> Hapus Menu
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                                        <i class="ph ph-fork-knife text-3xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Menu</p>
                                    <p class="text-xs font-medium text-gray-500 mb-4">Silakan tambahkan menu pertama Anda.</p>
                                    <a href="{{ route('tenant.menu.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-telkom-red hover:bg-telkom-maroon text-white text-[13px] font-semibold rounded-lg shadow-sm transition-colors">
                                        <i class="ph ph-plus font-bold mr-1.5"></i>
                                        Tambah Menu
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($menus->hasPages() || $menus->count() > 0)
        <div class="px-6 py-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-[13px] font-medium text-gray-500">Menampilkan {{ $menus->firstItem() ?? 0 }} - {{ $menus->lastItem() ?? 0 }} dari {{ $menus->total() }} menu</span>
            <div class="flex items-center space-x-1.5">
                @if ($menus->onFirstPage())
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition-colors cursor-not-allowed" disabled>
                        <i class="ph ph-caret-left font-bold"></i>
                    </button>
                @else
                    <a href="{{ $menus->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-telkom-red transition-colors">
                        <i class="ph ph-caret-left font-bold"></i>
                    </a>
                @endif

                @foreach ($menus->getUrlRange(max(1, $menus->currentPage() - 1), min($menus->lastPage(), $menus->currentPage() + 1)) as $page => $url)
                    @if ($page == $menus->currentPage())
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-telkom-red text-white font-bold text-[13px] shadow-sm">
                            {{ $page }}
                        </button>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-transparent text-gray-500 font-medium text-[13px] hover:bg-gray-50 hover:text-telkom-red transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if ($menus->hasMorePages())
                    <a href="{{ $menus->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-telkom-red transition-colors">
                        <i class="ph ph-caret-right font-bold"></i>
                    </a>
                @else
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition-colors cursor-not-allowed" disabled>
                        <i class="ph ph-caret-right font-bold"></i>
                    </button>
                @endif
            </div>
        </div>
        @endif

    </div>
@endsection
