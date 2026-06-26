@extends('layouts.dashboard')

@section('title', 'Buat Laporan Pencairan Dana')

@section('sidebar_menu')
    <x-sidebar.pengelola active="laporan" />
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <!-- Header Page -->
    <div class="mb-6">
        <a href="{{ route('pengelola.pencairan_dana.index') }}" class="inline-flex items-center text-sm font-bold text-telkom-red hover:text-red-700 transition-colors mb-4">
            <i class="ph-bold ph-caret-left mr-2"></i> Kembali ke Laporan Pencairan Dana
        </a>
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Buat Laporan Pencairan Dana</h1>
        <p class="text-[15px] text-gray-500 font-medium">Lengkapi data laporan pencairan dana tenant berdasarkan periode penjualan.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-medium text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pengelola.pencairan_dana.store') }}" method="POST" id="disbursementForm">
        @csrf
        <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Periode Laporan -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Periode Laporan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ph ph-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="text" id="date_range" name="date_range" placeholder="Contoh: 01 Jun 2024 - 07 Jun 2024" required class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all cursor-pointer">
                    </div>
                </div>

                <!-- Tenant -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Tenant <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="tenant_id" name="tenant_id" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Pilih Tenant...</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" data-kantin="{{ $tenant->kantin ? $tenant->kantin->nama_kantin : '-' }}">{{ $tenant->nama_tenant }}</option>
                            @endforeach
                        </select>
                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    </div>
                </div>

                <!-- Kantin -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Kantin</label>
                    <div class="relative">
                        <input type="text" id="kantin_name" disabled placeholder="Kantin GKU" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-500 outline-none cursor-not-allowed">
                        <i class="ph ph-lock-key absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-lg"></i>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2 font-medium">Kantin otomatis terisi berdasarkan tenant.</p>
                </div>

                <!-- Pilih Approver -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Pilih Approver <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="approver_name" name="approver_name" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Pilih Approver...</option>
                            <option value="Approver 1">Approver 1</option>
                            <option value="Approver 2">Approver 2</option>
                        </select>
                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2 font-medium">Pilih pihak yang akan melakukan approval laporan ini.</p>
                </div>
            </div>

            <!-- Keterangan Laporan -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-900 mb-2">Keterangan Laporan</label>
                <textarea name="keterangan" rows="4" placeholder="Masukkan keterangan tambahan jika perlu..." class="w-full p-4 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all"></textarea>
            </div>

            <!-- Detail Perhitungan -->
            <div class="mb-8">
                <h3 class="text-[15px] font-bold text-gray-900 mb-4">Detail Perhitungan</h3>
                
                <div class="border border-gray-200 rounded-[20px] overflow-hidden">
                    <table class="w-full text-left bg-white">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase">No</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase">Nama Tenant</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase">Kantin</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase text-right">Total Penjualan</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase text-right">Dana Tenant 70%</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase text-right">Bagian Tel-U 30%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loading State -->
                            <tr id="calcLoading" class="hidden">
                                <td colspan="6" class="py-8 text-center">
                                    <div class="flex items-center justify-center text-gray-400">
                                        <i class="ph ph-spinner animate-spin text-2xl mr-2 text-telkom-red"></i>
                                        <span class="text-sm font-medium">Menghitung data penjualan...</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Empty State -->
                            <tr id="calcEmpty">
                                <td colspan="6" class="py-8 text-center text-sm font-medium text-gray-400">
                                    Pilih Periode Laporan dan Tenant untuk melihat detail perhitungan.
                                </td>
                            </tr>

                            <!-- Result State -->
                            <tr id="calcResult" class="hidden hover:bg-gray-50 transition-colors">
                                <td class="py-5 px-6 text-sm font-medium text-gray-600">1</td>
                                <td class="py-5 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                                            <img id="res_tenant_foto" src="" class="w-full h-full object-cover hidden">
                                            <i id="res_tenant_icon" class="ph-fill ph-storefront text-gray-400 text-xs"></i>
                                        </div>
                                        <span id="res_tenant_name" class="text-sm font-bold text-gray-900">-</span>
                                    </div>
                                </td>
                                <td class="py-5 px-6 text-sm font-medium text-gray-600" id="res_kantin">-</td>
                                <td class="py-5 px-6 text-sm font-semibold text-gray-900 text-right" id="res_total">-</td>
                                <td class="py-5 px-6 text-[15px] font-black text-gray-900 text-right" id="res_dana_tenant">-</td>
                                <td class="py-5 px-6 text-[15px] font-black text-gray-900 text-right" id="res_dana_telu">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('pengelola.pencairan_dana.index') }}" class="px-8 py-3.5 bg-white border border-red-200 text-telkom-red text-sm font-bold rounded-xl hover:bg-red-50 transition-colors">
                    Batal
                </a>
                <div class="flex gap-4">
                    <button type="button" id="btnPdf" disabled onclick="downloadPdf()" class="px-8 py-3.5 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-file-pdf mr-2 text-red-500"></i> Download PDF
                    </button>
                    <button type="submit" name="action" value="draft" id="btnDraft" disabled class="px-8 py-3.5 bg-yellow-500 text-white text-sm font-bold rounded-xl hover:bg-yellow-600 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-bookmark mr-2"></i> Simpan Draft
                    </button>
                    <button type="submit" name="action" value="proposed" id="btnProposed" disabled class="px-8 py-3.5 bg-green-500 text-white text-sm font-bold rounded-xl hover:bg-green-600 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Laporan (Proposed)
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function downloadPdf() {
        const tenantId = document.getElementById('tenant_id').value;
        const dateRange = document.getElementById('date_range').value;
        if(tenantId && dateRange) {
            window.open(`{{ route('pengelola.pencairan_dana.preview_pdf') }}?tenant_id=${tenantId}&date_range=${encodeURIComponent(dateRange)}`, '_blank');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Init Flatpickr for Date Range
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
            // Auto fill kantin name
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

            // Fetch AJAX
            fetch("{{ route('pengelola.pencairan_dana.calculate') }}", {
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
            
            // Set values
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
            
            // Enable submit if there are sales
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
@endpush
