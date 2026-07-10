@extends('layouts.dashboard')

@section('title', 'Pesanan Masuk - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="pesanan" />
@endsection

@section('content')
<div class="font-sans" x-data="tenantOrders()">
    
    <!-- Top Navbar is already provided by layouts.dashboard, but we need to check if it does. If we look at other tenant pages, they just start with content. So we remove the custom top navbar and just put the content! -->
    
    <div class="pb-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">Pesanan</h1>
            <p class="text-gray-500 text-sm">Daftar pesanan terbaru yang perlu segera diproses.</p>
        </div>

            <!-- Filters & Search -->
            <div class="flex flex-col xl:flex-row justify-between gap-4 mb-8">
                
                <!-- Status Tabs -->
                <div class="flex flex-wrap gap-3">
                    <!-- Pesanan Masuk (belum_diproses) -->
                    <button @click="activeTab = 'belum_diproses'" 
                            class="px-4 py-2.5 rounded-xl border flex items-center gap-2 text-sm font-bold transition-colors"
                            :class="activeTab === 'belum_diproses' ? 'bg-red-50 border-red-100 text-telkom-red' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50'">
                        <i class="ph-bold ph-tray"></i>
                        Pesanan Masuk
                        <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px]"
                              :class="activeTab === 'belum_diproses' ? 'bg-telkom-red text-white' : 'bg-gray-100 text-gray-500'" x-text="counts.belum_diproses"></span>
                    </button>
                    
                    <!-- Sedang Disiapkan (diproses) -->
                    <button @click="activeTab = 'diproses'" 
                            class="px-4 py-2.5 rounded-xl border flex items-center gap-2 text-sm font-bold transition-colors"
                            :class="activeTab === 'diproses' ? 'bg-yellow-50 border-yellow-100 text-yellow-600' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50'">
                        <i class="ph-bold ph-cooking-pot"></i>
                        Sedang Disiapkan
                        <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px]"
                              :class="activeTab === 'diproses' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-500'" x-text="counts.diproses"></span>
                    </button>
                    
                    <!-- Siap Diambil (siap_diambil) -->
                    <button @click="activeTab = 'siap_diambil'" 
                            class="px-4 py-2.5 rounded-xl border flex items-center gap-2 text-sm font-bold transition-colors"
                            :class="activeTab === 'siap_diambil' ? 'bg-blue-50 border-blue-100 text-blue-600' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50'">
                        <i class="ph-bold ph-shopping-bag"></i>
                        Siap Diambil
                        <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px]"
                              :class="activeTab === 'siap_diambil' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-500'" x-text="counts.siap_diambil"></span>
                    </button>
                    
                    <!-- Selesai (selesai) -->
                    <button @click="activeTab = 'selesai'" 
                            class="px-4 py-2.5 rounded-xl border flex items-center gap-2 text-sm font-bold transition-colors"
                            :class="activeTab === 'selesai' ? 'bg-green-50 border-green-100 text-green-600' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50'">
                        <i class="ph-bold ph-check-circle"></i>
                        Selesai
                        <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px]"
                              :class="activeTab === 'selesai' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-500'" x-text="counts.selesai"></span>
                    </button>
                </div>

                <div class="flex gap-3">
                    <div class="relative w-full xl:w-64">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari pesanan" class="w-full pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-telkom-red transition-colors">
                    </div>
                    <button class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors flex items-center gap-2 shrink-0">
                        <i class="ph ph-sliders-horizontal"></i> Filter
                    </button>
                </div>
            </div>

            <!-- Grid Orders -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <template x-for="order in filteredOrders" :key="order.id">
                    <div class="bg-white rounded-[20px] p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                        
                        <!-- Header Card -->
                        <div class="flex justify-between items-start mb-5">
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-full bg-red-50 text-telkom-red flex items-center justify-center shrink-0">
                                    <i class="ph-bold text-lg" :class="order.order_type === 'dine-in' ? 'ph-armchair' : 'ph-bag'"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base leading-tight mb-1" x-text="order.user ? order.user.name : 'Guest'"></h3>
                                    <p class="text-[11px] text-gray-500 flex items-center gap-1">
                                        <i class="ph-fill ph-storefront"></i>
                                        <span x-text="order.order_type === 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang'"></span>
                                        <span x-show="order.order_type === 'dine-in'" x-text="'• Meja ' + (order.table_number || '-')"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <h4 class="font-bold text-gray-900 text-base" x-text="getTime(order.created_at)"></h4>
                                <p class="text-[10px] text-gray-400 mb-1" x-text="getDate(order.created_at)"></p>
                                <span class="text-[10px] font-bold" :class="getStatusColor(order.order_status)" x-text="getStatusText(order.order_status, order.order_type, order.table_number)"></span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="w-full border-t border-dashed border-gray-200 mb-5"></div>

                        <!-- Items List -->
                        <div class="flex-1 overflow-y-auto pr-2 space-y-4 mb-6 custom-scrollbar" style="max-h: 180px;">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="flex items-start gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-telkom-red mt-1.5 shrink-0"></div>
                                    <div>
                                        <p class="text-sm text-gray-900 leading-snug">
                                            <span class="font-bold" x-text="item.nama_menu"></span> 
                                            <span class="font-semibold text-gray-500" x-text="'x' + item.quantity"></span>
                                        </p>
                                        
                                        <!-- Customizations & Notes -->
                                        <template x-if="item.selected_options || item.catatan">
                                            <div class="mt-1 space-y-0.5">
                                                <template x-if="item.selected_options">
                                                    <template x-for="opt in parseOptions(item.selected_options)">
                                                        <p class="text-[11px] text-gray-400 italic" x-text="opt.label + ': ' + opt.value"></p>
                                                    </template>
                                                </template>
                                                <template x-if="item.catatan">
                                                    <p class="text-[11px] text-gray-400 italic" x-text="'Catatan: ' + item.catatan"></p>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div class="mt-auto">
                            <div class="flex items-end justify-between mb-5">
                                <div>
                                    <p class="text-[11px] text-gray-400 mb-0.5">Total</p>
                                    <p class="text-lg font-black text-telkom-red" x-text="'Rp' + formatPrice(order.total_price)"></p>
                                </div>
                                <div class="flex items-center gap-1 text-[11px] text-gray-400">
                                    <i class="ph ph-note-pencil"></i>
                                    <span x-text="countNotes(order) + ' catatan'"></span>
                                </div>
                            </div>
                            
                            <div class="flex gap-3">
                                <!-- Form untuk Proses ke Next Status -->
                                <template x-if="order.order_status !== 'selesai'">
                                    <form :action="'/tenant/orders/' + order.id + '/status'" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="order_status" :value="getNextStatus(order.order_status, order.order_type, order.table_number)">
                                        <button type="submit" class="w-full py-2.5 bg-telkom-red hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                                            <span x-text="getProcessButtonText(order.order_status, order.order_type, order.table_number)"></span>
                                        </button>
                                    </form>
                                </template>
                                
                                <template x-if="order.order_status === 'selesai'">
                                    <div class="flex-1 py-2.5 bg-green-50 text-green-600 text-center text-sm font-bold rounded-xl">
                                        Pesanan Selesai
                                    </div>
                                </template>

                                <a :href="'/tenant/orders/' + order.id" class="px-5 py-2.5 bg-white border border-telkom-red text-telkom-red hover:bg-red-50 text-sm font-bold rounded-xl transition-colors text-center shrink-0">
                                    Detail
                                </a>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
            
            <!-- Empty State -->
            <template x-if="filteredOrders.length === 0">
                <div class="w-full bg-white rounded-3xl p-16 text-center border border-gray-100 mt-4">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ph ph-receipt text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada pesanan</h3>
                    <p class="text-gray-500">Belum ada pesanan dengan status ini.</p>
                </div>
            </template>



        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tenantOrders', () => ({
                activeTab: 'belum_diproses', // default tab
                searchQuery: '',
                orders: {!! json_encode($orders->items()) !!},

                get counts() {
                    return {
                        belum_diproses: this.orders.filter(o => o.order_status === 'belum_diproses').length,
                        diproses: this.orders.filter(o => o.order_status === 'diproses').length,
                        siap_diambil: this.orders.filter(o => o.order_status === 'siap_diambil').length,
                        selesai: this.orders.filter(o => o.order_status === 'selesai').length,
                    };
                },

                get filteredOrders() {
                    return this.orders.filter(order => {
                        let matchesTab = order.order_status === this.activeTab;
                        
                        let search = this.searchQuery.toLowerCase();
                        let matchesSearch = true;
                        if (search) {
                            let nameMatch = order.user && order.user.name.toLowerCase().includes(search);
                            let itemMatch = order.items.some(item => item.nama_menu.toLowerCase().includes(search));
                            let orderIdMatch = order.order_id.toLowerCase().includes(search);
                            matchesSearch = nameMatch || itemMatch || orderIdMatch;
                        }

                        return matchesTab && matchesSearch;
                    });
                },

                getTime(dateString) {
                    const date = new Date(dateString);
                    return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                },

                getDate(dateString) {
                    const date = new Date(dateString);
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                parseOptions(optionsData) {
                    if (!optionsData) return [];
                    let opts = optionsData;
                    if (typeof opts === 'string') {
                        try {
                            opts = JSON.parse(opts);
                        } catch (e) {
                            return [{label: 'Pilihan', value: opts}];
                        }
                    }
                    if (Array.isArray(opts)) return opts;
                    return [];
                },

                countNotes(order) {
                    let c = 0;
                    order.items.forEach(i => {
                        if (i.catatan) c++;
                        if (i.selected_options) c++;
                    });
                    return c;
                },

                getStatusText(status, orderType, tableNumber) {
                    switch (status) {
                        case 'belum_diproses': return 'Pesanan Masuk';
                        case 'diproses': return 'Sedang Disiapkan';
                        case 'siap_diambil': 
                            if (orderType === 'dine-in') {
                                return tableNumber ? 'Menunggu Diselesaikan' : 'Ambil Sendiri';
                            }
                            return 'Siap Diambil';
                        case 'selesai': return 'Selesai';
                        default: return status;
                    }
                },

                getStatusColor(status) {
                    switch (status) {
                        case 'belum_diproses': return 'text-telkom-red';
                        case 'diproses': return 'text-yellow-600';
                        case 'siap_diambil': return 'text-blue-600';
                        case 'selesai': return 'text-green-600';
                        default: return 'text-gray-500';
                    }
                },
                
                getNextStatus(currentStatus, orderType, tableNumber) {
                    switch (currentStatus) {
                        case 'belum_diproses': return 'diproses';
                        case 'diproses': 
                            if (orderType === 'dine-in') {
                                return tableNumber ? 'selesai' : 'siap_diambil';
                            }
                            return 'siap_diambil';
                        case 'siap_diambil': return 'selesai';
                        default: return 'selesai';
                    }
                },
                
                getProcessButtonText(currentStatus, orderType, tableNumber) {
                    switch (currentStatus) {
                        case 'belum_diproses': return 'Proses Pesanan';
                        case 'diproses': 
                            if (orderType === 'dine-in') {
                                return tableNumber ? 'Selesaikan Pesanan' : 'Tandai Ambil Sendiri';
                            }
                            return 'Pesanan Siap Diambil';
                        case 'siap_diambil': 
                            return 'Selesaikan Pesanan';
                        default: return 'Selesai';
                    }
                }
            }));
        });
    </script>
</div>
@endsection
