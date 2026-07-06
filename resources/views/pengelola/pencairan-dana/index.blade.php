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

    <div x-data="pencairanDana()" x-init="$watch('activeTab', () => currentPage = 1)">
        <!-- Tabs and Action Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div class="flex flex-wrap items-center bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <!-- All Tab -->
                <button @click="activeTab = 'all'" 
                        class="px-6 py-2.5 text-sm font-bold transition-colors border-r border-gray-200 last:border-r-0"
                        :class="activeTab === 'all' ? 'bg-white text-gray-900' : 'bg-gray-50/50 text-gray-500 hover:bg-white'">
                    All
                </button>
                
                <!-- Draft Tab -->
                <button @click="activeTab = 'draft'" 
                        class="px-6 py-2.5 text-sm font-bold transition-colors border-r border-gray-200 last:border-r-0"
                        :class="activeTab === 'draft' ? 'bg-[#FFFBF0] text-[#8B5E34]' : 'bg-gray-50/50 text-gray-500 hover:bg-white'">
                    Draft
                </button>
                
                <!-- Proposed Tab -->
                <button @click="activeTab = 'proposed'" 
                        class="px-6 py-2.5 text-sm font-bold transition-colors border-r border-gray-200 last:border-r-0"
                        :class="activeTab === 'proposed' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-50/50 text-gray-500 hover:bg-white'">
                    Proposed
                </button>
                
                <!-- Approved Tab -->
                <button @click="activeTab = 'approved'" 
                        class="px-6 py-2.5 text-sm font-bold transition-colors border-r border-gray-200 last:border-r-0"
                        :class="activeTab === 'approved' ? 'bg-emerald-50 text-emerald-800' : 'bg-gray-50/50 text-gray-500 hover:bg-white'">
                    Approved
                </button>
                
                <!-- Rejected Tab -->
                <button @click="activeTab = 'rejected'" 
                        class="px-6 py-2.5 text-sm font-bold transition-colors border-r border-gray-200 last:border-r-0"
                        :class="activeTab === 'rejected' ? 'bg-red-50 text-red-800' : 'bg-gray-50/50 text-gray-500 hover:bg-white'">
                    Rejected
                </button>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('pengelola.pencairan_dana.create') }}" class="px-5 py-3 bg-telkom-red hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center shrink-0">
                    <i class="ph-bold ph-plus mr-2"></i> Buat Laporan Pencairan
                </a>
            </div>
        </div>

        <!-- Filters (Server-side) -->
        <form method="GET" action="{{ route('pengelola.pencairan_dana.index') }}" class="bg-white p-6 rounded-[20px] shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-6 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-bold text-gray-700 mb-2">Pencarian Laporan</label>
                <div class="relative">
                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Batch ID atau keterangan..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium transition-all" onchange="this.form.submit()">
                </div>
            </div>
            
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-bold text-gray-700 mb-2">Dari Tanggal</label>
                <div class="relative">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium transition-all cursor-pointer" onchange="this.form.submit()">
                </div>
            </div>
            
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-bold text-gray-700 mb-2">Sampai Tanggal</label>
                <div class="relative">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-telkom-red/20 outline-none text-gray-600 font-medium transition-all cursor-pointer" onchange="this.form.submit()">
                </div>
            </div>

            <div class="w-full md:w-auto">
                <a href="{{ route('pengelola.pencairan_dana.index') }}" class="w-full md:w-11 h-[42px] shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors" title="Reset Filter">
                    <i class="ph-bold ph-arrows-clockwise text-lg"></i>
                </a>
            </div>
        </form>

        <!-- Main Content Table -->
        <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Riwayat Laporan Pencairan Dana</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Periode Laporan</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Laporan</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Referensi / Batch ID</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan Kantin</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Dana Tenant 70%</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, index) in paginatedItems" :key="item.batch_id">
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="py-4 px-4 text-sm font-semibold text-gray-600" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="py-4 px-4 text-sm font-medium text-gray-700" x-text="formatDate(item.start_date, item.end_date)"></td>
                                <td class="py-4 px-4">
                                    <span class="text-sm font-bold text-gray-900" x-text="item.judul || '-'"></span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-red-50 text-telkom-red flex items-center justify-center shrink-0 border border-red-100">
                                            <i class="ph-bold ph-files"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-gray-900 block" x-text="item.batch_id"></span>
                                            <span class="text-[11px] text-gray-500 font-medium" x-text="item.tenant_count + ' Tenant'"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm font-medium text-gray-600" x-text="item.keterangan_kantin"></td>
                                <td class="py-4 px-4 text-sm font-semibold text-gray-900" x-text="'Rp' + formatRupiah(item.total_penjualan)"></td>
                                <td class="py-4 px-4 text-[14px] font-black text-gray-900" x-text="'Rp' + formatRupiah(item.dana_tenant)"></td>
                                <td class="py-4 px-4">
                                    <template x-if="item.status === 'draft'">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-600 border border-yellow-200 whitespace-nowrap w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 shrink-0"></span> Dibuat
                                        </span>
                                    </template>
                                    <template x-if="item.status === 'proposed'">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200 whitespace-nowrap w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 shrink-0"></span> Diproses (Kaur)
                                        </span>
                                    </template>
                                    <template x-if="item.status === 'approved_kaur'">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-200 whitespace-nowrap w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5 shrink-0"></span> Diproses (Kabag)
                                        </span>
                                    </template>
                                    <template x-if="item.status === 'approved'">
                                        <div class="flex flex-col gap-1 w-fit">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-200 whitespace-nowrap w-fit">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 shrink-0"></span> Selesai
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap">Approv. by Kaur & Kabag</span>
                                        </div>
                                    </template>
                                    <template x-if="item.status === 'rejected_kaur' || item.status === 'rejected_kabag'">
                                        <div class="flex flex-col gap-1 w-fit">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200 whitespace-nowrap w-fit">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5 shrink-0"></span> Ditolak (<span x-text="item.status === 'rejected_kaur' ? 'Kaur' : 'Kabag'"></span>)
                                            </span>
                                        </div>
                                    </template>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a :href="getDetailUrl(item.batch_id)" class="px-3 py-1.5 text-xs font-bold text-telkom-red border border-red-200 rounded-lg hover:bg-red-50 transition-colors flex items-center justify-center">
                                            <i class="ph ph-eye mr-1.5 text-sm"></i> Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        
                        <template x-if="filteredItems.length === 0">
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
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm border-t border-gray-50 pt-6">
                <div class="text-gray-500 font-medium">
                    Menampilkan <span x-text="filteredItems.length === 0 ? 0 : ((currentPage - 1) * itemsPerPage + 1)" class="font-bold text-gray-900"></span> - <span x-text="Math.min(currentPage * itemsPerPage, filteredItems.length)" class="font-bold text-gray-900"></span> dari <span x-text="filteredItems.length" class="font-bold text-gray-900"></span> Laporan
                </div>
                
                <div class="flex items-center gap-2" x-show="totalPages > 1" x-cloak>
                    <button @click="prevPage()" :disabled="currentPage === 1" 
                            class="px-3 py-2 border border-gray-200 rounded-lg font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-1 text-xs"
                            :class="currentPage === 1 ? 'text-gray-400 bg-gray-50/50' : 'text-blue-500 bg-white'">
                        <i class="ph-bold ph-caret-left"></i>
                        <span>Prev</span>
                    </button>
                    
                    <div class="flex items-center gap-1">
                        <template x-if="pageNumbers[0] > 1">
                            <div class="flex items-center gap-1">
                                <button @click="currentPage = 1" class="w-8 h-8 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-100 transition-colors flex items-center justify-center">1</button>
                                <span class="px-1 text-gray-400">...</span>
                            </div>
                        </template>
                        
                        <template x-for="page in pageNumbers" :key="page">
                            <button @click="currentPage = page"
                                    class="w-8 h-8 rounded-lg text-xs font-bold transition-colors flex items-center justify-center"
                                    :class="currentPage === page ? 'bg-telkom-red text-white' : 'text-gray-700 hover:bg-gray-100'">
                                <span x-text="page"></span>
                            </button>
                        </template>

                        <template x-if="pageNumbers[pageNumbers.length - 1] < totalPages">
                            <div class="flex items-center gap-1">
                                <span class="px-1 text-gray-400">...</span>
                                <button @click="currentPage = totalPages" class="w-8 h-8 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-100 transition-colors flex items-center justify-center" x-text="totalPages"></button>
                            </div>
                        </template>
                    </div>
                    
                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                            class="px-3 py-2 border border-gray-200 rounded-lg font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-1 text-xs"
                            :class="currentPage === totalPages ? 'text-gray-400 bg-gray-50/50' : 'text-blue-500 bg-white'">
                        <span>Next</span>
                        <i class="ph-bold ph-caret-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pencairanDana', () => ({
                activeTab: new URLSearchParams(window.location.search).get('status') || 'all',
                pencairans: {!! json_encode($pencairan_danas) !!},
                currentPage: 1,
                itemsPerPage: 10,
                
                get filteredItems() {
                    if (this.activeTab === 'all') return this.pencairans;
                    
                    return this.pencairans.filter(item => {
                        if (this.activeTab === 'proposed') {
                            return item.status === 'proposed' || item.status === 'approved_kaur';
                        } else if (this.activeTab === 'rejected') {
                            return item.status === 'rejected_kaur' || item.status === 'rejected_kabag';
                        }
                        return item.status === this.activeTab;
                    });
                },
                
                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredItems.slice(start, end);
                },
                
                get totalPages() {
                    return Math.ceil(this.filteredItems.length / this.itemsPerPage) || 1;
                },
                
                get pageNumbers() {
                    let pages = [];
                    let maxVisible = 5;
                    let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
                    let end = Math.min(this.totalPages, start + maxVisible - 1);
                    
                    if (end - start + 1 < maxVisible) {
                        start = Math.max(1, end - maxVisible + 1);
                    }
                    
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                },
                
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },
                
                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },
                
                formatDate(startDate, endDate) {
                    const s = new Date(startDate);
                    const e = new Date(endDate);
                    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                    return `${s.getDate().toString().padStart(2, '0')} ${months[s.getMonth()]} ${s.getFullYear()} - ${e.getDate().toString().padStart(2, '0')} ${months[e.getMonth()]} ${e.getFullYear()}`;
                },
                
                formatRupiah(amount) {
                    return new Intl.NumberFormat('id-ID').format(amount);
                },
                
                getDetailUrl(batchId) {
                    return `{{ url('pengelola/pencairan-dana/batch') }}/${batchId}`;
                }
            }));
        });
    </script>
    @endpush
@endsection
