@extends('layouts.dashboard')

@section('title', 'Laporan Pencairan Dana')

@section('sidebar_menu')
    <x-sidebar.pengelola active="laporan" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Laporan Pencairan Dana</h1>
        <p class="text-[15px] text-gray-500 font-medium">Kelola riwayat laporan pencairan dana tenant.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium text-sm flex items-center">
            <i class="ph-fill ph-check-circle text-xl mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs and Action Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div class="flex rounded-xl overflow-hidden border border-gray-200 bg-white">
            <a href="{{ route('pengelola.pencairan_dana.index', ['status' => 'draft']) }}" class="px-6 py-3 text-sm font-semibold transition-colors {{ $status == 'draft' ? 'bg-yellow-50 text-yellow-700 border-b-2 border-yellow-400' : 'text-gray-500 hover:bg-gray-50' }}">
                Draft
            </a>
            <a href="{{ route('pengelola.pencairan_dana.index', ['status' => 'proposed']) }}" class="px-6 py-3 text-sm font-semibold transition-colors border-l border-gray-200 {{ $status == 'proposed' ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-400' : 'text-gray-500 hover:bg-gray-50' }}">
                Proposed
            </a>
            <a href="{{ route('pengelola.pencairan_dana.index', ['status' => 'approved']) }}" class="px-6 py-3 text-sm font-semibold transition-colors border-l border-gray-200 {{ $status == 'approved' ? 'bg-green-50 text-green-700 border-b-2 border-green-400' : 'text-gray-500 hover:bg-gray-50' }}">
                Approved
            </a>
            <a href="{{ route('pengelola.pencairan_dana.index', ['status' => 'rejected']) }}" class="px-6 py-3 text-sm font-semibold transition-colors border-l border-gray-200 {{ $status == 'rejected' ? 'bg-red-50 text-red-700 border-b-2 border-red-400' : 'text-gray-500 hover:bg-gray-50' }}">
                Rejected
            </a>
        </div>
        
        <a href="{{ route('pengelola.pencairan_dana.create') }}" class="px-5 py-3 bg-telkom-red hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center">
            <i class="ph-bold ph-plus mr-2"></i>
            Buat Laporan Pencairan Dana
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-[20px] shadow-sm border border-gray-100 mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">Periode</label>
            <div class="relative">
                <i class="ph ph-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" placeholder="Pilih periode..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">Kantin</label>
            <div class="relative">
                <select class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium appearance-none">
                    <option value="">Semua Kantin</option>
                </select>
                <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">Tenant</label>
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" placeholder="Cari nama tenant..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium">
            </div>
        </div>
    </div>

    <!-- Main Content Table -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Riwayat Laporan Pencairan Dana</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Periode Laporan</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Tenant</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kantin</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Dana Tenant 70%</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pencairan_danas as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-4 px-4 text-sm font-semibold text-gray-600">{{ $pencairan_danas->firstItem() + $index }}</td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-700">
                                {{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                        @if($item->tenant && $item->tenant->foto)
                                            <img src="{{ asset('storage/' . $item->tenant->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs"><i class="ph-fill ph-storefront"></i></div>
                                        @endif
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">{{ $item->tenant->nama_tenant ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-600">
                                {{ $item->tenant && $item->tenant->kantin ? $item->tenant->kantin->nama_kantin : '-' }}
                            </td>
                            <td class="py-4 px-4 text-sm font-semibold text-gray-900">
                                Rp{{ number_format($item->total_penjualan, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-[14px] font-black text-gray-900">
                                Rp{{ number_format($item->dana_tenant, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                @if($item->status == 'draft')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-600 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span> Dibuat
                                    </span>
                                @elseif($item->status == 'proposed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200" title="Menunggu Kaur">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span> Diproses (Kaur)
                                    </span>
                                @elseif($item->status == 'approved_kaur')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-200" title="Menunggu Kabag">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span> Diproses (Kabag)
                                    </span>
                                @elseif($item->status == 'approved')
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Selesai
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-medium">Approv. by Kaur & Kabag</span>
                                    </div>
                                @elseif(in_array($item->status, ['rejected_kaur', 'rejected_kabag']))
                                    <div class="flex flex-col gap-1 max-w-[150px]">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Ditolak ({{ $item->status == 'rejected_kaur' ? 'Kaur' : 'Kabag' }})
                                        </span>
                                        <div class="text-[11px] text-red-500 bg-red-50 p-1.5 rounded border border-red-100">
                                            <strong>Catatan:</strong> {{ $item->status == 'rejected_kaur' ? $item->catatan_kaur : $item->catatan_kabag }}
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="px-3 py-1.5 text-xs font-bold text-telkom-red border border-red-200 rounded-lg hover:bg-red-50 transition-colors flex items-center justify-center">
                                        <i class="ph ph-eye mr-1.5 text-sm"></i> Detail
                                    </button>
                                    @if($item->status == 'draft')
                                        <form action="{{ route('pengelola.pencairan_dana.propose', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors flex items-center justify-center" onclick="return confirm('Ajukan laporan ini ke Kaur?')">
                                                <i class="fa-solid fa-paper-plane mr-1.5"></i> Ajukan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="ph-fill ph-file-text text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-base font-bold text-gray-900 mb-1">Belum ada data</p>
                                    <p class="text-sm font-medium text-gray-500">Tidak ada laporan pencairan dana di status ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-between items-center text-sm">
            <div class="text-gray-500 font-medium">
                Menampilkan {{ $pencairan_danas->firstItem() ?? 0 }} - {{ $pencairan_danas->lastItem() ?? 0 }} dari {{ $pencairan_danas->total() }} data
            </div>
            <div>
                {{ $pencairan_danas->links() }}
            </div>
        </div>
    </div>
@endsection
