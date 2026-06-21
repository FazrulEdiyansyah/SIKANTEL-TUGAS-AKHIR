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
            <button @click="activeTab = 'aktif'" 
                    class="pb-4 text-sm font-bold transition-colors relative"
                    :class="activeTab === 'aktif' ? 'text-telkom-red' : 'text-gray-500 hover:text-gray-700'">
                Pesanan Aktif
                <div class="absolute bottom-0 left-0 w-full h-1 bg-telkom-red rounded-t-md transition-opacity"
                     x-show="activeTab === 'aktif'"></div>
            </button>
            <button @click="activeTab = 'riwayat'" 
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
            <!-- Untuk kemudahan, kita skip dropdown status asli dan biarkan desainnya saja -->
            <div class="relative w-full sm:w-[200px]">
                <select x-model="statusFilter" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="pending">Menunggu Pembayaran</option>
                    <option value="success">Sedang Diproses</option>
                    <option value="failed">Gagal/Dibatalkan</option>
                </select>
                <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>
        </div>

        <!-- Orders List -->
        <div class="space-y-4">
            <template x-for="order in filteredOrders" :key="order.id">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row gap-6">
                        
                        <!-- Tenant Info -->
                        <div class="flex gap-4 md:w-[300px] shrink-0">
                            <div class="w-16 h-16 rounded-full bg-gray-100 border border-gray-50 flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="order.tenant && order.tenant.foto">
                                    <img :src="'/storage/' + order.tenant.foto" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!order.tenant || !order.tenant.foto">
                                    <i class="ph ph-storefront text-2xl text-gray-400"></i>
                                </template>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-1" x-text="order.tenant ? order.tenant.nama_tenant : 'Tenant Tidak Diketahui'"></h3>
                                <p class="text-xs text-gray-500 mb-3" x-text="order.tenant ? order.tenant.lokasi : '-'"></p>
                                
                                <div class="flex items-center gap-1.5 text-xs text-gray-600 mb-1.5">
                                    <i class="ph ph-fork-knife text-telkom-red"></i>
                                    <span x-text="order.order_type === 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang'"></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-600" x-show="order.order_type === 'dine-in'">
                                    <i class="ph ph-armchair text-gray-400"></i>
                                    <span x-text="'Meja ' + (order.table_number || '-')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Info -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-3">
                                        <i class="ph ph-calendar-blank"></i>
                                        <span x-text="formatDate(order.created_at)"></span>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900 mb-0.5" x-text="order.items.length + ' menu'"></p>
                                    <p class="text-sm text-gray-500 line-clamp-1" x-text="getItemNames(order.items)"></p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-xs text-gray-500 mb-0.5">Total</p>
                                    <p class="text-base font-bold text-telkom-red" x-text="'Rp' + formatPrice(order.total_price)"></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-50">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold"
                                          :class="getStatusClass(order.status)">
                                        <i class="ph-bold" :class="getStatusIcon(order.status)"></i>
                                        <span x-text="getStatusText(order.status)"></span>
                                    </span>
                                </div>
                                
                                <a :href="'/pelanggan/orders/' + order.id" class="px-5 py-2 border border-telkom-red text-telkom-red hover:bg-red-50 text-sm font-bold rounded-xl transition-colors">
                                    Lihat Detail &gt;
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </template>
            
            <!-- Empty State -->
            <template x-if="filteredOrders.length === 0">
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
                orders: {!! json_encode($orders) !!},

                get filteredOrders() {
                    return this.orders.filter(order => {
                        // Filter Tab
                        let isTabMatch = false;
                        if (this.activeTab === 'aktif') {
                            isTabMatch = ['pending', 'success', 'preparing', 'ready'].includes(order.status);
                        } else {
                            isTabMatch = !['pending', 'success', 'preparing', 'ready'].includes(order.status);
                        }
                        
                        // Dropdown Status Filter
                        let isStatusMatch = this.statusFilter === 'all' || order.status === this.statusFilter;

                        // Search Filter (by tenant name or item names)
                        let searchLower = this.searchQuery.toLowerCase();
                        let isSearchMatch = true;
                        if (searchLower) {
                            let tenantMatch = order.tenant && order.tenant.nama_tenant.toLowerCase().includes(searchLower);
                            let itemMatch = order.items.some(item => item.nama_menu.toLowerCase().includes(searchLower));
                            isSearchMatch = tenantMatch || itemMatch;
                        }

                        return isTabMatch && isStatusMatch && isSearchMatch;
                    });
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const day = date.getDate();
                    const month = months[date.getMonth()];
                    const year = date.getFullYear();
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${day} ${month} ${year}, ${hours}:${minutes}`;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                getItemNames(items) {
                    if (!items || items.length === 0) return '';
                    return items.map(i => i.nama_menu).join(', ');
                },

                getStatusText(status) {
                    switch (status) {
                        case 'pending': return 'Menunggu Pembayaran';
                        case 'success': return 'Pesanan Diterima';
                        case 'preparing': return 'Sedang Disiapkan';
                        case 'ready': return 'Siap Diambil';
                        case 'completed': return 'Selesai';
                        case 'failed': return 'Gagal / Ditolak';
                        case 'expired': return 'Kedaluwarsa';
                        default: return status;
                    }
                },

                getStatusClass(status) {
                    switch (status) {
                        case 'pending': return 'bg-orange-50 text-orange-600';
                        case 'success': return 'bg-blue-50 text-blue-600';
                        case 'preparing': return 'bg-yellow-50 text-yellow-600';
                        case 'ready': return 'bg-teal-50 text-teal-600';
                        case 'completed': return 'bg-green-50 text-green-600';
                        case 'failed': 
                        case 'expired': return 'bg-red-50 text-red-600';
                        default: return 'bg-gray-50 text-gray-600';
                    }
                },

                getStatusIcon(status) {
                    switch (status) {
                        case 'pending': return 'ph-clock';
                        case 'success': return 'ph-check-circle';
                        case 'preparing': return 'ph-cooking-pot';
                        case 'ready': return 'ph-shopping-bag';
                        case 'completed': return 'ph-check-circle';
                        case 'failed': 
                        case 'expired': return 'ph-x-circle';
                        default: return 'ph-info';
                    }
                }
            }));
        });
    </script>
</div>
@endsection
