@extends('layouts.dashboard')

@section('title', 'Detail Laporan Pencairan Dana')

@section('sidebar_menu')
    @php
        $role = auth()->user()->role ?? 'pengelola';
    @endphp
    @if($role === 'pengelola')
        <x-sidebar.pengelola active="laporan" />
    @else
        <!-- Check if coming from riwayat or dashboard -->
        @php
            $isActiveRiwayat = request()->routeIs('*.riwayat');
        @endphp
        <x-sidebar.approver :active="$isActiveRiwayat ? 'riwayat' : 'dashboard'" />
    @endif
@endsection

@section('content')
    @php
        $role = auth()->user()->role ?? 'pengelola';
        $canApprove = false;
        if ($role === 'kaur' && $batchInfo->status === 'proposed') $canApprove = true;
        if ($role === 'kabag' && $batchInfo->status === 'approved_kaur') $canApprove = true;
    @endphp

    <!-- Header Page -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Detail Laporan Pencairan Dana</h1>
            <p class="text-[15px] text-gray-500 font-medium">Informasi rincian pengajuan pencairan dana ke tenant.</p>
        </div>
        <a href="{{ $role === 'pengelola' ? route('pengelola.pencairan_dana.index') : route($role.'.dashboard') }}" class="inline-flex items-center text-sm font-bold text-telkom-red hover:text-red-700 transition-colors">
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

    @if($batchInfo->status === 'rejected_kaur' && $batchInfo->catatan_kaur)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start">
            <i class="ph-fill ph-warning-circle text-red-600 text-xl mr-3 mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-red-800">Laporan Ditolak oleh Kaur</h3>
                <p class="text-sm text-red-700 mt-1 font-medium"><strong>Catatan:</strong> {{ $batchInfo->catatan_kaur }}</p>
            </div>
        </div>
    @endif

    @if($batchInfo->status === 'rejected_kabag' && $batchInfo->catatan_kabag)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start">
            <i class="ph-fill ph-warning-circle text-red-600 text-xl mr-3 mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-red-800">Laporan Ditolak oleh Kabag</h3>
                <p class="text-sm text-red-700 mt-1 font-medium"><strong>Catatan:</strong> {{ $batchInfo->catatan_kabag }}</p>
            </div>
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

        @php
            $parts = explode(' & ', $batchInfo->approver_name ?? '');
            $kaurName = isset($parts[0]) ? trim(str_replace('(Kaur)', '', $parts[0])) : '-';
            $kabagName = isset($parts[1]) ? trim(str_replace('(Kabag)', '', $parts[1])) : '-';
        @endphp
        
        <!-- Approval Signatures -->
        <div class="mt-10 flex flex-wrap gap-8">
            <!-- Drafter -->
            <div class="w-64">
                <div class="text-sm font-semibold text-gray-800 mb-2 h-6 flex items-center">
                    Drafter
                </div>
                <div class="border border-gray-400 rounded-xl p-4 bg-white">
                    <div class="text-sm font-bold text-gray-800 min-h-[20px]">{{ $batchInfo->pengelola->name ?? 'Pengelola' }}</div>
                    <div class="w-full h-px bg-gray-300 my-2"></div>
                    <div class="text-xs font-semibold text-gray-800">Pengelola</div>
                </div>
            </div>

            <!-- Kaur -->
            <div class="w-64">
                <div class="mb-2 h-6 flex items-center">
                    @if(in_array($batchInfo->status, ['approved_kaur', 'approved']))
                        <span class="px-3 py-1 bg-[#86EFAC] text-green-900 text-[11px] font-bold rounded-full">Approved</span>
                    @elseif(in_array($batchInfo->status, ['rejected_kaur', 'rejected_kabag']))
                        <span class="px-3 py-1 bg-[#FCA5A5] text-red-900 text-[11px] font-bold rounded-full">Rejected</span>
                    @else
                        <span class="px-3 py-1 bg-[#FDE047] text-yellow-900 text-[11px] font-bold rounded-full">Pending</span>
                    @endif
                </div>
                <div class="border border-gray-400 rounded-xl p-4 bg-white">
                    <div class="text-sm font-bold text-gray-800 min-h-[20px]">{{ $kaurName }}</div>
                    <div class="w-full h-px bg-gray-300 my-2"></div>
                    <div class="text-xs font-semibold text-gray-800">Kaur</div>
                </div>
            </div>
            
            <!-- Kabag -->
            <div class="w-64">
                <div class="mb-2 h-6 flex items-center">
                    @if($batchInfo->status == 'approved')
                        <span class="px-3 py-1 bg-[#86EFAC] text-green-900 text-[11px] font-bold rounded-full">Approved</span>
                    @elseif($batchInfo->status == 'rejected_kabag')
                        <span class="px-3 py-1 bg-[#FCA5A5] text-red-900 text-[11px] font-bold rounded-full">Rejected</span>
                    @else
                        <span class="px-3 py-1 bg-[#FDE047] text-yellow-900 text-[11px] font-bold rounded-full">Pending</span>
                    @endif
                </div>
                <div class="border border-gray-400 rounded-xl p-4 bg-white">
                    <div class="text-sm font-bold text-gray-800 min-h-[20px]">{{ $kabagName }}</div>
                    <div class="w-full h-px bg-gray-300 my-2"></div>
                    <div class="text-xs font-semibold text-gray-800">Kabag</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end items-center">
            <div class="flex gap-3">
                <a href="{{ route('pengelola.pencairan_dana.batch_pdf', $batch_id) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition-colors shadow-sm flex items-center">
                    <i class="ph-bold ph-file-pdf mr-2 text-red-500 text-lg"></i> Download Semua (Batch)
                </a>
                
                @if($role === 'pengelola' && $batchInfo->status == 'draft')
                    <form action="{{ route('pengelola.pencairan_dana.propose', $batchInfo->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-green-500 text-white text-sm font-bold rounded-lg hover:bg-green-600 transition-colors shadow-sm flex items-center" onclick="return confirm('Ajukan laporan ini ke Kaur?')">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Laporan
                        </button>
                    </form>
                @endif
                
                @if($role === 'pengelola' && in_array($batchInfo->status, ['rejected_kaur', 'rejected_kabag']))
                    <form action="{{ route('pengelola.pencairan_dana.duplicate', $batch_id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-blue-500 text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-sm flex items-center" onclick="return confirm('Tindakan ini akan menyalin seluruh data laporan ini menjadi Draft baru. Anda yakin ingin melanjutkan?')">
                            <i class="ph-bold ph-copy mr-2 text-lg"></i> Buat Ulang Laporan (Draft Baru)
                        </button>
                    </form>
                @endif
                
                @if($canApprove)
                    <button type="button" onclick="openRejectModal()" class="px-5 py-2.5 bg-red-500 text-white text-sm font-bold rounded-lg hover:bg-red-600 transition-colors shadow-sm flex items-center">
                        Tolak Laporan
                    </button>
                    <button type="button" onclick="openApproveModal()" class="px-5 py-2.5 bg-green-500 text-white text-sm font-bold rounded-lg hover:bg-green-600 transition-colors shadow-sm flex items-center">
                        Setujui Laporan
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($canApprove)
    <!-- Modal Approve -->
    <div id="approveModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 overflow-hidden">
            <form method="POST" action="{{ route($role.'.pencairan.approve', $batch_id) }}">
                @csrf
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Konfirmasi Persetujuan</h3>
                    <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin menyetujui laporan pencairan dana ini?</p>
                </div>
                <div class="p-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-sm font-medium hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Ya, Setujui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reject -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 overflow-hidden">
            <form method="POST" action="{{ route($role.'.pencairan.reject', $batch_id) }}">
                @csrf
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-red-600">Tolak Laporan</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-4">Silakan berikan alasan mengapa pengajuan ini ditolak.</p>
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Penolakan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm py-2 px-3 border" required></textarea>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-sm font-medium hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">Tolak Laporan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openApproveModal() {
            document.getElementById('approveModal').classList.remove('hidden');
            document.getElementById('approveModal').classList.add('flex');
        }
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }
        function closeModals() {
            document.getElementById('approveModal').classList.add('hidden');
            document.getElementById('approveModal').classList.remove('flex');
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }
    </script>
    @endpush
    @endif
@endsection
