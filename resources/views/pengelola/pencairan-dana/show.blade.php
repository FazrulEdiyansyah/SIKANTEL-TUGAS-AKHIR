@extends('layouts.dashboard')

@section('title', 'Detail Laporan Pencairan Dana')

@section('sidebar_menu')
    <x-sidebar.pengelola active="laporan" />
@endsection

@section('content')
    <!-- Header Page -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Detail Laporan Pencairan Dana</h1>
            <p class="text-[15px] text-gray-500 font-medium">Informasi rincian pengajuan pencairan dana ke tenant.</p>
        </div>
        <a href="{{ route('pengelola.pencairan_dana.index') }}" class="inline-flex items-center text-sm font-bold text-telkom-red hover:text-red-700 transition-colors">
            <i class="ph-bold ph-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium text-sm flex items-center">
            <i class="ph-fill ph-check-circle text-xl mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-medium text-sm flex items-center">
            <i class="ph-fill ph-x-circle text-xl mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-8 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Informasi Laporan</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 mb-6">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Referensi / Batch ID</p>
                <p class="text-base font-bold text-gray-900">{{ $batch_id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Periode Laporan</p>
                <p class="text-base font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($batchInfo->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($batchInfo->end_date)->format('d/m/Y') }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</p>
                <p class="text-base font-bold text-gray-900">{{ $batchInfo->pengelola->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tujuan Approval</p>
                <p class="text-base font-bold text-gray-900">{{ $batchInfo->approver_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Status Laporan</p>
                <div>
                    @if($batchInfo->status == 'draft')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-50 text-yellow-600 border border-yellow-200">Draft</span>
                    @elseif($batchInfo->status == 'proposed')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">Diproses (Kaur)</span>
                    @elseif($batchInfo->status == 'approved_kaur')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-200">Diproses (Kabag)</span>
                    @elseif($batchInfo->status == 'approved')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-green-50 text-green-600 border border-green-200">Selesai (Disetujui)</span>
                    @elseif(in_array($batchInfo->status, ['rejected_kaur', 'rejected_kabag']))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 text-red-600 border border-red-200">Ditolak</span>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Keterangan</p>
                <p class="text-sm font-medium text-gray-900">{{ $batchInfo->keterangan ?: '-' }}</p>
            </div>
        </div>

        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Daftar Tenant (Barang & Jasa)</h2>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left border-collapse bg-white">
                <thead class="bg-gray-50/80">
                    <tr class="border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">No</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Tenant</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Total Penjualan</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Dana Tenant (70%)</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Dana Institusi (30%)</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase text-center">Download Laporan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pencairan_danas as $index => $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3 px-4 text-sm font-semibold text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="text-sm font-bold text-gray-900 block">{{ $item->tenant->nama_tenant ?? 'Unknown' }}</span>
                                <span class="text-xs text-gray-500 font-medium">{{ $item->tenant->kantin->nama_kantin ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">Rp{{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-sm font-black text-gray-900">Rp{{ number_format($item->dana_tenant, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-sm font-bold text-gray-700">Rp{{ number_format($item->dana_telu, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pengelola.pencairan_dana.preview_pdf', $item->id) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 transition-colors shadow-sm">
                                    <i class="ph-bold ph-download-simple mr-2"></i> Download PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="2" class="py-3 px-4 text-right text-sm font-bold text-gray-700">Total Keseluruhan:</td>
                        <td class="py-3 px-4 text-sm font-bold text-gray-900">Rp{{ number_format($pencairan_danas->sum('total_penjualan'), 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-sm font-black text-gray-900">Rp{{ number_format($pencairan_danas->sum('dana_tenant'), 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-sm font-bold text-gray-900">Rp{{ number_format($pencairan_danas->sum('dana_telu'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="text-sm font-medium text-gray-600">
                <span class="font-bold text-gray-900">Drafter:</span> {{ $batchInfo->pengelola->name ?? 'Pengelola' }}
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pengelola.pencairan_dana.batch_pdf', $batch_id) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition-colors shadow-sm flex items-center">
                    <i class="ph-bold ph-file-pdf mr-2 text-red-500 text-lg"></i> Download Semua (Batch)
                </a>
                
                @if($batchInfo->status == 'draft')
                    <form action="{{ route('pengelola.pencairan_dana.propose', $batchInfo->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-green-500 text-white text-sm font-bold rounded-lg hover:bg-green-600 transition-colors shadow-sm flex items-center" onclick="return confirm('Ajukan laporan ini ke Kaur?')">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Laporan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
