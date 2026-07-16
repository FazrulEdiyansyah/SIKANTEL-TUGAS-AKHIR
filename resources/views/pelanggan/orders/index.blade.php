@extends('layouts.pelanggan')

@section('title', 'Pesanan Saya - SIKANTEL')

@section('content')

<div class="pt-24 pb-20 bg-gray-50 min-h-screen" x-data="ordersPage()">
    <div class="max-w-[1000px] mx-auto px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">Pesanan Saya</h1>
            <p class="text-gray-500 text-sm">Pantau status pesanan dan lihat riwayat pesanan Anda.</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-8 border-b border-gray-200 mb-6">
            <button @click="activeTab = 'aktif'; statusFilter = 'all'" 
                    class="pb-4 text-sm font-bold transition-colors relative"
                    :class="activeTab === 'aktif' ? 'text-telkom-red' : 'text-gray-500 hover:text-gray-700'">
                Pesanan Aktif
                <div class="absolute bottom-0 left-0 w-full h-1 bg-telkom-red rounded-t-md transition-opacity"
                     x-show="activeTab === 'aktif'"></div>
            </button>
            <button @click="activeTab = 'riwayat'; statusFilter = 'all'" 
                    class="pb-4 text-sm font-bold transition-colors relative"
                    :class="activeTab === 'riwayat' ? 'text-telkom-red' : 'text-gray-500 hover:text-gray-700'">
                Riwayat Pesanan
                <div class="absolute bottom-0 left-0 w-full h-1 bg-telkom-red rounded-t-md transition-opacity"
                     x-show="activeTab === 'riwayat'" style="display: none;"></div>
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
            <div class="relative flex-1">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari tenant atau nama pesanan" 
                       class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all">
            </div>
            <div class="relative w-full sm:w-[200px]">
                <select x-model="statusFilter" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red cursor-pointer">
                    <option value="all">Semua Status</option>
                    <template x-for="status in availableStatuses" :key="status.value">
                        <option :value="status.value" x-text="status.label"></option>
                    </template>
                </select>
                <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>
        </div>

        <!-- Orders List -->
        <div class="space-y-8">
            <template x-for="dateGroup in ordersByDate" :key="dateGroup.date">
                <div>
                    <!-- Date Header -->
                    <h2 class="text-lg font-bold text-gray-900 mb-4 px-2" x-text="dateGroup.label"></h2>
                    
                    <div class="space-y-4">
                        <template x-for="group in dateGroup.groups" :key="group.order_id">
                            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex flex-col md:flex-row gap-6">
                                    
                                    <!-- Tenant Info List -->
                                    <div class="flex flex-col gap-4 md:w-[300px] shrink-0">
                                        <template x-for="order in group.tenant_orders" :key="order.id">
                                            <div class="flex gap-4 items-center">
                                                <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-50 flex items-center justify-center overflow-hidden shrink-0">
                                                    <template x-if="order.tenant && order.tenant.foto">
                                                        <img :src="'/storage/' + order.tenant.foto" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!order.tenant || !order.tenant.foto">
                                                        <i class="ph ph-storefront text-xl text-gray-400"></i>
                                                    </template>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="font-bold text-gray-900 text-sm mb-1 line-clamp-1" x-text="order.tenant ? order.tenant.nama_tenant : 'Tenant Tidak Diketahui'"></h3>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold"
                                                        :class="getStatusClass(getUnifiedStatus(order))">
                                                        <i class="ph-bold" :class="getStatusIcon(getUnifiedStatus(order))"></i>
                                                        <span x-text="getStatusText(getUnifiedStatus(order))"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Order Info -->
                                    <div class="flex-1 flex flex-col justify-between border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                            <div>
                                                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-3">
                                                    <i class="ph ph-clock"></i>
                                                    <span x-text="formatTimeOnly(group.created_at)"></span>
                                                </div>
                                                <p class="text-sm font-bold text-gray-900 mb-0.5" x-text="getTotalItems(group) + ' menu'"></p>
                                                <p class="text-sm text-gray-500 line-clamp-2" x-text="getAllItemNames(group)"></p>
                                            </div>
                                            <div class="text-left sm:text-right shrink-0">
                                                <p class="text-xs text-gray-500 mb-0.5">Total Transaksi</p>
                                                <p class="text-base font-bold text-telkom-red" x-text="'Rp' + formatPrice(group.total_price)"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between sm:justify-end mt-6 pt-4 border-t border-gray-50">
                                            <div class="sm:hidden">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold"
                                                      :class="getGroupStatusClass(group)">
                                                    <span x-text="getGroupStatusText(group)"></span>
                                                </span>
                                            </div>
                                            <a :href="'/pelanggan/orders/' + group.order_id" class="px-5 py-2 border border-telkom-red text-telkom-red hover:bg-red-50 text-sm font-bold rounded-xl transition-colors">
                                                Lihat Detail &gt;
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            
            <!-- Empty State -->
            <template x-if="ordersByDate.length === 0">
                <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
                    <i class="ph flex justify-center text-4xl text-gray-300 mb-4" :class="activeTab === 'aktif' ? 'ph-cooking-pot' : 'ph-receipt'"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada pesanan</h3>
                    <p class="text-gray-500 text-sm" x-text="activeTab === 'aktif' ? 'Anda belum memiliki pesanan yang sedang diproses.' : 'Riwayat pesanan Anda masih kosong.'"></p>
                    <a href="{{ route('pelanggan.dashboard') }}" class="inline-flex mt-6 px-6 py-3 bg-telkom-red hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors">
                        Pesan Makanan Sekarang
                    </a>
                </div>
            </template>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('ordersPage', () => ({
                activeTab: 'aktif',
                searchQuery: '',
                statusFilter: 'all',
                rawOrders: {!! json_encode($orders) !!},

                get availableStatuses() {
                    if (this.activeTab === 'aktif') {
                        return [
                            { value: 'pending', label: 'Menunggu Pembayaran' },
                            { value: 'belum_diproses', label: 'Pesanan Diterima' },
                            { value: 'diproses', label: 'Sedang Diproses' },
                            { value: 'siap_diambil', label: 'Siap Diambil/Diantar' }
                        ];
                    } else {
                        return [
                            { value: 'selesai', label: 'Selesai' },
                            { value: 'failed', label: 'Gagal / Dibatalkan' }
                        ];
                    }
                },

                get groupedOrders() {
                    let transactionGroups = {};
                    this.rawOrders.forEach(order => {
                        if (!transactionGroups[order.order_id]) {
                            transactionGroups[order.order_id] = {
                                order_id: order.order_id,
                                created_at: order.created_at,
                                payment_status: order.payment_status,
                                snap_token: order.snap_token,
                                total_price: 0,
                                tenant_orders: []
                            };
                        }
                        transactionGroups[order.order_id].total_price += parseInt(order.total_price);
                        transactionGroups[order.order_id].tenant_orders.push(order);
                    });

                    let grouped = Object.values(transactionGroups);

                    return grouped.filter(group => {
                        let isTabMatch = false;
                        let isStatusMatch = false;

                        let hasActive = group.tenant_orders.some(o => {
                            let stat = this.getUnifiedStatus(o);
                            return ['pending', 'belum_diproses', 'diproses', 'siap_diambil'].includes(stat);
                        });

                        if (this.activeTab === 'aktif') {
                            isTabMatch = hasActive;
                        } else {
                            isTabMatch = !hasActive;
                        }
                        
                        if (this.statusFilter === 'all') {
                            isStatusMatch = true;
                        } else {
                            isStatusMatch = group.tenant_orders.some(o => this.getUnifiedStatus(o) === this.statusFilter);
                        }

                        let searchLower = this.searchQuery.toLowerCase();
                        let isSearchMatch = true;
                        if (searchLower) {
                            isSearchMatch = group.tenant_orders.some(o => {
                                let tenantMatch = o.tenant && o.tenant.nama_tenant.toLowerCase().includes(searchLower);
                                let itemMatch = o.items.some(item => item.nama_menu.toLowerCase().includes(searchLower));
                                return tenantMatch || itemMatch;
                            });
                        }

                        return isTabMatch && isStatusMatch && isSearchMatch;
                    });
                },

                get ordersByDate() {
                    let byDate = {};
                    this.groupedOrders.forEach(group => {
                        let dateStr = this.formatDateOnly(group.created_at);
                        if (!byDate[dateStr]) byDate[dateStr] = [];
                        byDate[dateStr].push(group);
                    });
                    
                    return Object.keys(byDate).sort((a,b) => new Date(b) - new Date(a)).map(date => {
                        return {
                            date: date,
                            label: this.formatDateLabel(date),
                            groups: byDate[date].sort((a,b) => new Date(b.created_at) - new Date(a.created_at))
                        };
                    });
                },

                formatDateOnly(dateString) {
                    const date = new Date(dateString);
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`; // ISO format for easy sorting/parsing
                },

                formatDateLabel(dateString) {
                    const date = new Date(dateString);
                    const today = new Date();
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);

                    if (date.toDateString() === today.toDateString()) {
                        return 'Hari Ini';
                    } else if (date.toDateString() === yesterday.toDateString()) {
                        return 'Kemarin';
                    }

                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const day = date.getDate();
                    const month = months[date.getMonth()];
                    const year = date.getFullYear();
                    return `${day} ${month} ${year}`;
                },

                formatTimeOnly(dateString) {
                    const date = new Date(dateString);
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${hours}:${minutes}`;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                getTotalItems(group) {
                    let total = 0;
                    group.tenant_orders.forEach(o => {
                        total += o.items.length;
                    });
                    return total;
                },

                getAllItemNames(group) {
                    let names = [];
                    group.tenant_orders.forEach(o => {
                        o.items.forEach(i => {
                            names.push(i.nama_menu);
                        });
                    });
                    return names.join(', ');
                },

                getGroupStatusText(group) {
                    if (group.payment_status === 'pending') return 'Menunggu Pembayaran';
                    if (group.payment_status === 'failed') return 'Dibatalkan';
                    let allFinished = group.tenant_orders.every(o => o.order_status === 'selesai');
                    if (allFinished) return 'Selesai';
                    return 'Diproses';
                },

                getGroupStatusClass(group) {
                    if (group.payment_status === 'pending') return 'bg-orange-50 text-orange-600';
                    if (group.payment_status === 'failed') return 'bg-red-50 text-red-600';
                    let allFinished = group.tenant_orders.every(o => o.order_status === 'selesai');
                    if (allFinished) return 'bg-green-50 text-green-600';
                    return 'bg-blue-50 text-blue-600';
                },

                getUnifiedStatus(order) {
                    if (order.payment_status === 'pending') return 'pending';
                    if (order.payment_status === 'failed' || order.payment_status === 'expired') return 'failed';
                    if (order.payment_status === 'success') return order.order_status;
                    return order.payment_status;
                },

                getStatusText(status) {
                    switch (status) {
                        case 'pending': return 'Menunggu Pembayaran';
                        case 'belum_diproses': return 'Pesanan Diterima';
                        case 'diproses': return 'Sedang Disiapkan';
                        case 'siap_diambil': return 'Siap Diambil';
                        case 'selesai': return 'Selesai';
                        case 'failed': return 'Dibatalkan';
                        default: return status;
                    }
                },

                getStatusClass(status) {
                    switch (status) {
                        case 'pending': return 'bg-orange-50 text-orange-600';
                        case 'belum_diproses': return 'bg-blue-50 text-blue-600';
                        case 'diproses': return 'bg-yellow-50 text-yellow-600';
                        case 'siap_diambil': return 'bg-teal-50 text-teal-600';
                        case 'selesai': return 'bg-green-50 text-green-600';
                        case 'failed': return 'bg-red-50 text-red-600';
                        default: return 'bg-gray-50 text-gray-600';
                    }
                },

                getStatusIcon(status) {
                    switch (status) {
                        case 'pending': return 'ph-clock';
                        case 'belum_diproses': return 'ph-check-circle';
                        case 'diproses': return 'ph-cooking-pot';
                        case 'siap_diambil': return 'ph-shopping-bag';
                        case 'selesai': return 'ph-check-circle';
                        case 'failed': return 'ph-x-circle';
                        default: return 'ph-info';
                    }
                }
            }));
        });
    </script>
</div>
@endsection
