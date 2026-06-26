@extends('layouts.dashboard')

@section('title', 'Detail Pencairan Dana')

@section('sidebar_menu')
    <x-sidebar.approver active="dashboard" />
@endsection

@php
    $role = auth()->user()->role;
    $canApprove = false;
    if ($role === 'kaur' && $pencairan->status === 'proposed') $canApprove = true;
    if ($role === 'kabag' && $pencairan->status === 'approved_kaur') $canApprove = true;
@endphp

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight">Detail Pencairan Dana untuk Approval</h1>
        <a href="{{ route($role.'.dashboard') }}" class="text-sm font-bold text-telkom-red hover:text-red-700 transition-colors">
            &larr; Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-8 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 border-b border-gray-100 pb-8 mb-8">
            <!-- Informasi Laporan -->
            <div>
                <h3 class="text-[17px] font-bold text-gray-900 mb-4">Informasi Laporan</h3>
                <div class="space-y-3 text-[14px]">
                    <div class="flex">
                        <span class="text-gray-500 w-40">Periode Penjualan:</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pencairan->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($pencairan->end_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-40">Total Penjualan Kotor:</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($pencairan->total_penjualan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-40">Dana Tenant (70%):</span>
                        <span class="font-black text-green-600">Rp {{ number_format($pencairan->dana_tenant, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center mt-2">
                        <span class="text-gray-500 w-40">Status:</span>
                        @php
                            $badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                            $statusText = ucfirst($pencairan->status);
                            if($pencairan->status === 'proposed') {
                                $badgeClass = 'bg-blue-50 text-blue-600 border-blue-200';
                                $statusText = 'Proposed';
                            } elseif($pencairan->status === 'approved_kaur') {
                                $badgeClass = 'bg-indigo-50 text-indigo-600 border-indigo-200';
                                $statusText = 'Approved Kaur';
                            } elseif($pencairan->status === 'approved') {
                                $badgeClass = 'bg-green-50 text-green-600 border-green-200';
                                $statusText = 'Approved (Final)';
                            } elseif(in_array($pencairan->status, ['rejected_kaur', 'rejected_kabag'])) {
                                $badgeClass = 'bg-red-50 text-red-600 border-red-200';
                                $statusText = 'Rejected';
                            }
                        @endphp
                        <span class="px-2.5 py-1 rounded-md text-xs font-bold border {{ $badgeClass }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informasi Tenant -->
            <div>
                <h3 class="text-[17px] font-bold text-gray-900 mb-4">Informasi Tenant</h3>
                <div class="space-y-3 text-[14px]">
                    <div class="flex">
                        <span class="text-gray-500 w-32">Nama Tenant:</span>
                        <span class="font-medium text-gray-900">{{ $pencairan->tenant->nama_tenant ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-32">Kantin:</span>
                        <span class="font-medium text-gray-900">{{ $pencairan->tenant->kantin->nama_kantin ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-32">Pemilik Akun:</span>
                        <span class="font-medium text-gray-900">{{ $pencairan->tenant->user->name ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-32">Diajukan Oleh:</span>
                        <span class="font-medium text-gray-900">{{ $pencairan->pengelola->name ?? '-' }} (Pengelola)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Detail -->
        <h3 class="text-[17px] font-bold text-gray-900 mb-4">Daftar Detail Penjualan</h3>
        <div class="border border-gray-200 rounded-[20px] overflow-hidden mb-10">
            <table class="w-full text-left bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase">No</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase">Menu Barang/Jasa</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase">Quantity</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase">Harga Satuan</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pencairan->details as $index => $detail)
                    <tr class="hover:bg-gray-50/50 transition-colors text-[14px]">
                        <td class="py-4 px-6 text-gray-500">{{ $index + 1 }}</td>
                        <td class="py-4 px-6 font-medium text-gray-900">{{ $detail->menu->nama_menu ?? '-' }}</td>
                        <td class="py-4 px-6 text-gray-700">{{ $detail->qty }}</td>
                        <td class="py-4 px-6 text-gray-700">Rp {{ number_format($detail->menu->harga ?? 0, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 font-semibold text-gray-900 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-sm font-medium text-gray-400">
                            Tidak ada detail item yang tersimpan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-end gap-6">
            <!-- Info Drafter -->
            <div class="w-full md:w-1/3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-orange-700 mb-2">
                    Keterangan / Catatan
                </span>
                <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-600">
                    {{ $pencairan->keterangan ?? 'Tidak ada keterangan khusus dari pengelola.' }}
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                <a href="{{ route($role.'.pencairan.pdf', $pencairan->id) }}" target="_blank" class="px-6 py-3 bg-[#a52a2a] hover:bg-[#801b1b] text-white text-[14px] font-bold rounded-md transition-colors shadow-sm">
                    Download PDF
                </a>
                
                @if($canApprove)
                    <button type="button" onclick="openRejectModal()" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white text-[14px] font-bold rounded-md transition-colors shadow-sm">
                        Tolak Laporan
                    </button>
                    <button type="button" onclick="openApproveModal()" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white text-[14px] font-bold rounded-md transition-colors shadow-sm">
                        Setujui Laporan
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($canApprove)
    <!-- Modal Approve -->
    <div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 overflow-hidden">
            <form method="POST" action="{{ route($role.'.pencairan.approve', $pencairan->id) }}">
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
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 overflow-hidden">
            <form method="POST" action="{{ route($role.'.pencairan.reject', $pencairan->id) }}">
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
