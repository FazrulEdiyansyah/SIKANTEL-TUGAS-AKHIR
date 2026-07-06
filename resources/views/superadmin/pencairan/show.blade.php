@extends('layouts.superadmin')

@section('title', 'Detail Pencairan Dana')
@section('breadcrumb', 'Pencairan Dana / Detail')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('superadmin.pencairan.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Riwayat
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Informasi Laporan</h3>
                @if($pencairan->status == 'draft')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 uppercase">Draft</span>
                @elseif($pencairan->status == 'proposed')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 uppercase">Menunggu Kaur</span>
                @elseif($pencairan->status == 'approved_kaur')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 uppercase">Menunggu Kabag</span>
                @elseif($pencairan->status == 'approved' || $pencairan->status == 'completed')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 uppercase">Selesai</span>
                @elseif($pencairan->status == 'rejected_kaur' || $pencairan->status == 'rejected_kabag')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 uppercase">Ditolak</span>
                @endif
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-y-6 gap-x-4 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">Periode Penjualan</p>
                        <p class="font-semibold text-gray-900">{{ $pencairan->start_date->translatedFormat('d M Y') }} - {{ $pencairan->end_date->translatedFormat('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Tanggal Dibuat</p>
                        <p class="font-semibold text-gray-900">{{ $pencairan->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Tenant</p>
                        <p class="font-semibold text-gray-900">{{ $pencairan->tenant->nama_tenant ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Kantin</p>
                        <p class="font-semibold text-gray-900">{{ $pencairan->tenant->kantin->nama_kantin ?? '-' }}</p>
                    </div>
                    @if($pencairan->keterangan)
                    <div class="col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-gray-500 mb-1">Keterangan / Catatan Pengajuan</p>
                        <p class="text-gray-800">{{ $pencairan->keterangan }}</p>
                    </div>
                    @endif
                    
                    @if($pencairan->status == 'rejected_kaur' || $pencairan->status == 'rejected_kabag')
                    <div class="col-span-2 bg-red-50 p-4 rounded-lg border border-red-100 mt-2">
                        <p class="text-red-700 font-bold mb-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> Alasan Penolakan</p>
                        <p class="text-red-600">{{ $pencairan->status == 'rejected_kaur' ? $pencairan->catatan_kaur : $pencairan->catatan_kabag }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Rincian Menu Terjual</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-medium border-b border-gray-100">Menu</th>
                            <th class="px-6 py-4 font-medium border-b border-gray-100 text-center">Jumlah</th>
                            <th class="px-6 py-4 font-medium border-b border-gray-100 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        @foreach($pencairan->details as $detail)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $detail->menu->nama_menu ?? 'Menu Terhapus' }}</td>
                            <td class="px-6 py-4 text-gray-600 text-center">{{ $detail->qty }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Summary & Actions -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-6 border-b pb-2">Ringkasan Dana</h3>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Total Penjualan Kotor</span>
                    <span class="font-bold text-gray-900">Rp {{ number_format($pencairan->total_penjualan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm bg-blue-50/50 p-3 rounded-lg border border-blue-50">
                    <span class="font-semibold text-blue-800">Hak Tenant (70%)</span>
                    <span class="font-black text-blue-700">Rp {{ number_format($pencairan->dana_tenant, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm bg-red-50/50 p-3 rounded-lg border border-red-50">
                    <span class="font-semibold text-red-800">Potongan Tel-U (30%)</span>
                    <span class="font-black text-red-700">Rp {{ number_format($pencairan->dana_telu, 0, ',', '.') }}</span>
                </div>
            </div>

            <hr class="border-gray-100 mb-6">

            <!-- Superadmin Actions -->
            <h3 class="text-sm font-bold text-gray-900 mb-4">Aksi Superadmin</h3>
            <div class="space-y-3">
                @if(in_array($pencairan->status, ['draft', 'rejected_kaur', 'rejected_kabag']))
                    <form action="{{ route('superadmin.pencairan.propose', $pencairan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg text-sm transition-colors shadow-sm">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Laporan
                        </button>
                    </form>
                @endif

                @if(in_array($pencairan->status, ['proposed', 'approved_kaur']))
                    <form action="{{ route('superadmin.pencairan.approve', $pencairan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg text-sm transition-colors shadow-sm">
                            <i class="fa-solid fa-check mr-2"></i> Setujui Laporan
                        </button>
                    </form>

                    <form action="{{ route('superadmin.pencairan.reject', $pencairan->id) }}" method="POST" onsubmit="confirmFormSubmit(event, 'Apakah Anda yakin ingin menolak laporan ini?')">
                        @csrf
                        <button type="submit" class="w-full bg-orange-100 hover:bg-orange-200 text-orange-700 font-bold py-2.5 rounded-lg text-sm transition-colors">
                            <i class="fa-solid fa-xmark mr-2"></i> Tolak Laporan
                        </button>
                    </form>
                @endif

                <form action="{{ route('superadmin.pencairan.destroy', $pencairan->id) }}" method="POST" onsubmit="confirmFormSubmit(event, 'Apakah Anda yakin ingin menghapus laporan ini? Data yang dihapus tidak dapat dikembalikan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2.5 rounded-lg text-sm transition-colors border border-red-200">
                        <i class="fa-solid fa-trash mr-2"></i> Hapus Laporan
                    </button>
                </form>
            </div>
            
            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500">
                <i class="fa-solid fa-circle-info mr-1"></i> Sebagai Superadmin, Anda memiliki hak penuh untuk menyetujui, menolak, atau menghapus laporan pada tahap apa pun.
            </div>
        </div>
    </div>
</div>
@endsection
