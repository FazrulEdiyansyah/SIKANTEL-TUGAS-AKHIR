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
                    <div class="flex gap-4">
                        <div class="relative flex-1">
                            <span class="absolute -top-2.5 left-3 px-1 bg-white text-[11px] font-bold text-gray-500">Mulai</span>
                            <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-01') }}" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all cursor-pointer">
                        </div>
                        <div class="relative flex-1">
                            <span class="absolute -top-2.5 left-3 px-1 bg-white text-[11px] font-bold text-gray-500">Selesai</span>
                            <input type="date" id="end_date" name="end_date" value="{{ date('Y-m-t') }}" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all cursor-pointer">
                        </div>
                    </div>
                </div>



                <!-- Pilih Approver -->
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Pilih Approver 1 (Kaur) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="approver_1_name" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all appearance-none cursor-pointer text-gray-700">
                                <option value="">Pilih Kaur...</option>
                                @foreach($approvers->where('role', 'kaur') as $approver)
                                    <option value="{{ $approver->name }}">{{ $approver->name }}</option>
                                @endforeach
                            </select>
                            <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Pilih Approver 2 (Kabag) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="approver_2_name" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all appearance-none cursor-pointer text-gray-700">
                                <option value="">Pilih Kabag...</option>
                                @foreach($approvers->where('role', 'kabag') as $approver)
                                    <option value="{{ $approver->name }}">{{ $approver->name }}</option>
                                @endforeach
                            </select>
                            <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keterangan Laporan -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-900 mb-2">Keterangan Laporan</label>
                <textarea name="keterangan" rows="4" placeholder="Masukkan keterangan tambahan jika perlu..." class="w-full p-4 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-telkom-red/20 outline-none transition-all"></textarea>
            </div>

            <!-- Pilih Tenant -->
            <div class="mb-8" x-data="{ search: '', selectedKantin: '' }" 
                 x-init="$watch('search', () => document.getElementById('checkAllTenants').checked = false); $watch('selectedKantin', () => document.getElementById('checkAllTenants').checked = false)">
                <label class="block text-sm font-bold text-gray-900 mb-2">Pilih Tenant <span class="text-red-500">*</span></label>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    
                    <!-- Search & Select All Header -->
                    <div class="p-3 border-b border-gray-100 bg-gray-50 flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto flex-1">
                                <!-- Kantin Select -->
                                <div class="relative w-full sm:max-w-[220px]">
                                    <select x-model="selectedKantin" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-telkom-red/20 appearance-none text-gray-700">
                                        <option value="">Semua Kantin</option>
                                        @foreach($kantins as $kantin)
                                            <option value="{{ $kantin->id }}">{{ $kantin->nama_kantin }}</option>
                                        @endforeach
                                    </select>
                                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                
                                <!-- Search Input -->
                                <div class="relative flex-1">
                                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" x-model="search" placeholder="Cari nama tenant..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-telkom-red/20 bg-white">
                                </div>
                            </div>
                            
                            <!-- Select All -->
                            <label class="flex items-center space-x-2 text-sm font-bold text-gray-700 cursor-pointer shrink-0 bg-white px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" id="checkAllTenants" class="rounded border-gray-300 text-telkom-red focus:ring-telkom-red">
                                <span>Pilih Semua yang Tampil</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tenant List -->
                    <div class="max-h-48 overflow-y-auto p-2" id="tenantCheckboxContainer">
                        @foreach($tenants as $tenant)
                            <label x-show="(selectedKantin === '' || '{{ $tenant->kantin_id }}' == selectedKantin) && (search === '' || '{{ strtolower($tenant->nama_tenant) }}'.includes(search.toLowerCase()))" 
                                   class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors tenant-item-wrapper">
                                <input type="checkbox" name="tenant_ids[]" value="{{ $tenant->id }}" class="tenant-checkbox rounded border-gray-300 text-telkom-red focus:ring-telkom-red">
                                <span class="text-sm font-medium text-gray-700">{{ $tenant->nama_tenant }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $tenant->kantin ? $tenant->kantin->nama_kantin : '-' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
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
                        <tbody id="resultsBody">
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
    document.addEventListener("DOMContentLoaded", function() {
        const checkAll = document.getElementById('checkAllTenants');
        const checkboxes = document.querySelectorAll('.tenant-checkbox');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        startDateInput.addEventListener('change', calculateSales);
        endDateInput.addEventListener('change', calculateSales);
        
        checkAll.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(cb => {
                const label = cb.closest('label');
                if (label.style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            calculateSales();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                checkAll.checked = Array.from(checkboxes).every(c => c.checked);
                calculateSales();
            });
        });

        function calculateSales() {
            const selectedTenants = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            
            if(selectedTenants.length === 0) {
                hideResult();
                return;
            }

            if(!startDate || !endDate) {
                Swal.fire('Perhatian', "Mohon isi Periode Laporan (Mulai & Selesai) agar sistem dapat menghitung total penjualan tenant.", 'warning');
                hideResult();
                return;
            }

            if(new Date(startDate) > new Date(endDate)) {
                Swal.fire('Perhatian', "Tanggal 'Selesai' tidak boleh lebih kecil dari tanggal 'Mulai'.", 'warning');
                hideResult();
                return;
            }

            showLoading();

            fetch("{{ route('pengelola.pencairan_dana.calculate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    tenant_ids: selectedTenants,
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
            document.querySelectorAll('.calc-result-row').forEach(el => el.remove());
            document.getElementById('calcLoading').classList.remove('hidden');
            document.getElementById('btnDraft').disabled = true;
            document.getElementById('btnProposed').disabled = true;
        }

        function hideResult() {
            document.getElementById('calcLoading').classList.add('hidden');
            document.querySelectorAll('.calc-result-row').forEach(el => el.remove());
            document.getElementById('calcEmpty').classList.remove('hidden');
            document.getElementById('btnDraft').disabled = true;
            document.getElementById('btnProposed').disabled = true;
        }

        function showResult(dataArray) {
            document.getElementById('calcLoading').classList.add('hidden');
            document.getElementById('calcEmpty').classList.add('hidden');
            
            document.querySelectorAll('.calc-result-row').forEach(el => el.remove());
            
            const tbody = document.getElementById('resultsBody');
            let hasSales = false;

            dataArray.forEach((data, index) => {
                if(data.total_penjualan > 0) hasSales = true;
                
                let imgHtml = '';
                if(data.tenant_foto) {
                    imgHtml = `<img src="${data.tenant_foto}" class="w-full h-full object-cover">`;
                } else {
                    imgHtml = `<i class="ph-fill ph-storefront text-gray-400 text-xs"></i>`;
                }

                const tr = document.createElement('tr');
                tr.className = 'calc-result-row hover:bg-gray-50 transition-colors';
                tr.innerHTML = `
                    <td class="py-5 px-6 text-sm font-medium text-gray-600">${index + 1}</td>
                    <td class="py-5 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                                ${imgHtml}
                            </div>
                            <span class="text-sm font-bold text-gray-900">${data.tenant_name}</span>
                        </div>
                    </td>
                    <td class="py-5 px-6 text-sm font-medium text-gray-600">${data.kantin_name}</td>
                    <td class="py-5 px-6 text-sm font-semibold text-gray-900 text-right">${data.formatted_penjualan}</td>
                    <td class="py-5 px-6 text-[15px] font-black text-gray-900 text-right">${data.formatted_dana_tenant}</td>
                    <td class="py-5 px-6 text-[15px] font-black text-gray-900 text-right">${data.formatted_dana_telu}</td>
                `;
                tbody.appendChild(tr);
            });
            
            if(hasSales) {
                document.getElementById('btnDraft').disabled = false;
                document.getElementById('btnProposed').disabled = false;
            } else {
                document.getElementById('btnDraft').disabled = true;
                document.getElementById('btnProposed').disabled = true;
            }
        }
    });
</script>
@endpush
