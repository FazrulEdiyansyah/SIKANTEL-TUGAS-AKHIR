@extends('layouts.superadmin')

@section('title', 'Buat Laporan Pencairan Dana')
@section('breadcrumb', 'Pencairan Dana / Create')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-5xl">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Buat Laporan Pencairan Dana</h2>
            <p class="text-sm text-gray-500 mt-1">Lengkapi data laporan pencairan dana tenant berdasarkan periode penjualan.</p>
        </div>
        <a href="{{ route('superadmin.pencairan.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('superadmin.pencairan.store') }}" method="POST" id="disbursementForm" class="p-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Periode Laporan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode Laporan <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="date_range" name="date_range" placeholder="Contoh: 01 Jun 2024 - 07 Jun 2024" required class="w-full pl-9 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border bg-white cursor-pointer transition-all">
                </div>
            </div>

            <!-- Tenant -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tenant <span class="text-red-500">*</span></label>
                <select id="tenant_id" name="tenant_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border transition-all">
                    <option value="">-- Pilih Tenant --</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" data-kantin="{{ $tenant->kantin ? $tenant->kantin->nama_kantin : '-' }}">{{ $tenant->nama_tenant }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kantin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kantin</label>
                <div class="relative">
                    <input type="text" id="kantin_name" disabled placeholder="Kantin..." class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 text-gray-500 text-sm py-2 px-3 border cursor-not-allowed">
                    <i class="fa-solid fa-lock absolute right-3 top-1/2 -translate-y-1/2 text-gray-300"></i>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Otomatis terisi berdasarkan tenant.</p>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="Masukkan keterangan..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border transition-all"></textarea>
            </div>
        </div>

        <!-- Detail Perhitungan -->
        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Detail Perhitungan</h3>
            
            <div class="border border-gray-200 rounded-md overflow-hidden">
                <table class="w-full text-left bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Tenant</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Kantin</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase text-right">Total Penjualan</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase text-right">Dana Tenant 70%</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase text-right">Bagian Tel-U 30%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loading State -->
                        <tr id="calcLoading" class="hidden">
                            <td colspan="5" class="py-8 text-center">
                                <div class="flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xl mr-2 text-blue-500"></i>
                                    <span class="text-sm">Menghitung...</span>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr id="calcEmpty">
                            <td colspan="5" class="py-8 text-center text-sm text-gray-400">
                                Pilih Periode Laporan dan Tenant untuk melihat perhitungan.
                            </td>
                        </tr>

                        <!-- Result State -->
                        <tr id="calcResult" class="hidden hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-gray-200 flex items-center justify-center">
                                        <img id="res_tenant_foto" src="" class="w-full h-full object-cover hidden">
                                        <i id="res_tenant_icon" class="fa-solid fa-shop text-gray-400 text-xs"></i>
                                    </div>
                                    <span id="res_tenant_name" class="text-sm font-semibold text-gray-900">-</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600" id="res_kantin">-</td>
                            <td class="py-4 px-4 text-sm font-semibold text-gray-900 text-right" id="res_total">-</td>
                            <td class="py-4 px-4 text-[14px] font-bold text-gray-900 text-right" id="res_dana_tenant">-</td>
                            <td class="py-4 px-4 text-[14px] font-bold text-gray-900 text-right" id="res_dana_telu">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('superadmin.pencairan.index') }}" class="px-5 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <div class="flex gap-3">
                <button type="button" id="btnPdf" disabled onclick="downloadPdf()" class="px-5 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <i class="fa-solid fa-file-pdf mr-2 text-red-500"></i> Download PDF
                </button>
                <button type="submit" name="action" value="draft" id="btnDraft" disabled class="px-5 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <i class="fa-solid fa-bookmark mr-2"></i> Simpan Draft
                </button>
                <button type="submit" name="action" value="proposed" id="btnProposed" disabled class="px-5 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Laporan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function downloadPdf() {
        const tenantId = document.getElementById('tenant_id').value;
        const dateRange = document.getElementById('date_range').value;
        if(tenantId && dateRange) {
            window.open(`{{ route('superadmin.pencairan.preview_pdf') }}?tenant_id=${tenantId}&date_range=${encodeURIComponent(dateRange)}`, '_blank');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const fp = flatpickr("#date_range", {
            mode: "range",
            dateFormat: "d M Y",
            onChange: function(selectedDates, dateStr, instance) {
                if(selectedDates.length === 2) {
                    calculateSales();
                }
            }
        });

        const tenantSelect = document.getElementById('tenant_id');
        const kantinInput = document.getElementById('kantin_name');
        
        tenantSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            kantinInput.value = selectedOption.dataset.kantin || '';
            calculateSales();
        });

        function calculateSales() {
            const tenantId = tenantSelect.value;
            const dateRange = document.getElementById('date_range').value;
            
            if(!tenantId || !dateRange.includes(' - ')) {
                hideResult();
                return;
            }

            const dates = dateRange.split(' - ');
            const startDate = dates[0];
            const endDate = dates[1];

            showLoading();

            fetch("{{ route('superadmin.pencairan.calculate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    tenant_id: tenantId,
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                showResult(data);
            })
            .catch(error => {
                console.error("Error calculating sales:", error);
                hideResult();
            });
        }

        function showLoading() {
            document.getElementById('calcEmpty').classList.add('hidden');
            document.getElementById('calcResult').classList.add('hidden');
            document.getElementById('calcLoading').classList.remove('hidden');
            document.getElementById('btnDraft').disabled = true;
            document.getElementById('btnProposed').disabled = true;
            document.getElementById('btnPdf').disabled = true;
        }

        function hideResult() {
            document.getElementById('calcLoading').classList.add('hidden');
            document.getElementById('calcResult').classList.add('hidden');
            document.getElementById('calcEmpty').classList.remove('hidden');
            document.getElementById('btnDraft').disabled = true;
            document.getElementById('btnProposed').disabled = true;
            document.getElementById('btnPdf').disabled = true;
        }

        function showResult(data) {
            document.getElementById('calcLoading').classList.add('hidden');
            document.getElementById('calcEmpty').classList.add('hidden');
            
            document.getElementById('res_tenant_name').textContent = data.tenant_name;
            document.getElementById('res_kantin').textContent = data.kantin_name;
            document.getElementById('res_total').textContent = data.formatted_penjualan;
            document.getElementById('res_dana_tenant').textContent = data.formatted_dana_tenant;
            document.getElementById('res_dana_telu').textContent = data.formatted_dana_telu;
            
            const fotoImg = document.getElementById('res_tenant_foto');
            const iconImg = document.getElementById('res_tenant_icon');
            if(data.tenant_foto) {
                fotoImg.src = data.tenant_foto;
                fotoImg.classList.remove('hidden');
                iconImg.classList.add('hidden');
            } else {
                fotoImg.classList.add('hidden');
                iconImg.classList.remove('hidden');
            }

            document.getElementById('calcResult').classList.remove('hidden');
            
            if(data.total_penjualan > 0) {
                document.getElementById('btnDraft').disabled = false;
                document.getElementById('btnProposed').disabled = false;
                document.getElementById('btnPdf').disabled = false;
            } else {
                document.getElementById('btnDraft').disabled = true;
                document.getElementById('btnProposed').disabled = true;
                document.getElementById('btnPdf').disabled = true;
            }
        }
    });
</script>
