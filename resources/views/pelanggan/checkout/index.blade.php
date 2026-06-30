@extends('layouts.pelanggan')

@section('title', 'Checkout - SIKANTEL')

@section('content')
<div x-data="checkoutPage()">
    <!-- Breadcrumb -->
    <div class="max-w-[1400px] mx-auto mt-6 px-6 lg:px-16 mb-4">
        <div class="flex items-center space-x-2 text-[13px] text-gray-500 font-medium">
            <a href="{{ route('pelanggan.dashboard') }}" class="hover:text-telkom-red transition-colors">Kantin</a>
            <span>></span>
            @if($tenantId)
                <a href="{{ route('pelanggan.tenant.show', $tenantId) }}" class="hover:text-telkom-red transition-colors">Kantin GKU</a>
            @else
                <span>Kantin GKU</span>
            @endif
            <span>></span>
            <span class="text-gray-900 font-bold">Checkout</span>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-6 lg:px-16 mb-20">
        <!-- Header Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">Checkout</h1>
            <p class="text-gray-500 text-sm">Pilih jenis layanan, lengkapi detail pesanan, dan periksa kembali pesanan Anda.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Column -->
            <div class="flex-1 space-y-6">
                
                <!-- Pilih Jenis Layanan -->
                <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Pilih Jenis Layanan</h3>
                    <p class="text-sm text-gray-500 mb-6">Pilih sesuai kebutuhan Anda.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-colors" :class="orderType === 'dine-in' ? 'border-telkom-red bg-red-50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="order_type" value="dine-in" x-model="orderType" class="w-5 h-5 text-telkom-red border-gray-300 focus:ring-telkom-red">
                            <span class="ml-3 font-semibold text-gray-900">Makan di Tempat (Dine-in)</span>
                        </label>
                        <label class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-colors" :class="orderType === 'takeaway' ? 'border-telkom-red bg-red-50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="order_type" value="takeaway" x-model="orderType" class="w-5 h-5 text-telkom-red border-gray-300 focus:ring-telkom-red">
                            <span class="ml-3 font-semibold text-gray-900">Bawa Pulang (Takeaway)</span>
                        </label>
                    </div>
                </div>

                <!-- Nomor Meja -->
                <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-6 transition-all" x-show="orderType === 'dine-in'" x-transition>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Nomor Meja</h3>
                    <p class="text-sm text-gray-500 mb-4">Isi nomor meja atau tandai jika belum mendapatkan meja.</p>
                    
                    <div class="flex flex-col gap-4">
                        <input type="text" x-model="tableNumber" placeholder="Contoh: Meja 12" 
                               class="w-full border text-gray-900 text-sm rounded-xl focus:ring-telkom-red focus:border-telkom-red block p-3.5 font-medium transition-colors disabled:opacity-50 disabled:bg-gray-100"
                               :class="showTableError ? 'border-red-500 bg-red-50 focus:ring-red-500 focus:border-red-500' : 'bg-gray-50 border-gray-200'"
                               :disabled="noTableYet"
                               @input="showTableError = false">
                               
                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" x-model="noTableYet" @change="if(noTableYet) { tableNumber = ''; showTableError = false; }" class="peer sr-only">
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-md peer-checked:bg-telkom-red peer-checked:border-telkom-red transition-all"></div>
                                <i class="ph-bold ph-check text-white absolute opacity-0 peer-checked:opacity-100 text-sm"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">Saya belum dapat meja</span>
                        </label>
                    </div>

                    <div x-show="showTableError" x-transition>
                        <div class="flex items-center gap-1.5 mt-3 text-sm text-red-500 font-medium">
                            <i class="ph-fill ph-warning-circle"></i>
                            <span>Harap isi nomor meja atau centang opsi "belum dapat meja" di atas.</span>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Pesanan -->
                <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Ringkasan Pesanan</h3>
                    </div>
                    
                    <div class="p-6">
                        <!-- Table Header -->
                        <div class="hidden sm:grid grid-cols-12 gap-4 text-sm font-bold text-gray-400 mb-6 px-2">
                            <div class="col-span-6">Item</div>
                            <div class="col-span-2 text-center">Harga</div>
                            <div class="col-span-2 text-center">Jumlah</div>
                            <div class="col-span-2 text-right">Subtotal</div>
                        </div>

                        <!-- Items List -->
                        <div class="space-y-6">
                            <template x-for="(item, key) in cartItems" :key="key">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center group relative">
                                    <!-- Item Info -->
                                    <div class="col-span-1 sm:col-span-6 flex gap-4 items-start">
                                        <div class="w-[72px] h-[72px] rounded-2xl bg-gray-100 overflow-hidden shrink-0 shadow-sm border border-gray-50">
                                            <template x-if="item.foto">
                                                <img :src="'/storage/' + item.foto" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.foto">
                                                <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-cover opacity-50">
                                            </template>
                                        </div>
                                        <div class="flex-1 py-1">
                                            <h4 class="font-bold text-gray-900 text-base mb-1.5" x-text="item.nama_menu"></h4>
                                            
                                            <!-- Customizations -->
                                            <template x-if="item.selected_options">
                                                <div class="space-y-1">
                                                    <template x-for="(opt, idx) in getFormattedOptions(item.selected_options)" :key="idx">
                                                        <p class="text-[13px] text-gray-500 leading-normal">
                                                            <span class="font-semibold text-gray-700" x-text="opt.label ? opt.label + ': ' : ''"></span>
                                                            <span x-text="opt.value"></span>
                                                        </p>
                                                    </template>
                                                </div>
                                            </template>

                                            <!-- Catatan Display & Edit Button -->
                                            <div class="mt-1.5" x-show="editingNoteKey !== key">
                                                <template x-if="item.catatan">
                                                    <p class="text-[13px] text-gray-500 leading-normal mb-1">
                                                        <span class="font-semibold text-gray-700">Catatan: </span>
                                                        <span x-text="item.catatan"></span>
                                                    </p>
                                                </template>
                                                <button type="button" @click="startEditNote(key)" class="text-[12px] text-telkom-red font-semibold hover:underline flex items-center gap-1">
                                                    <i class="ph-bold" :class="item.catatan ? 'ph-pencil' : 'ph-plus'"></i> 
                                                    <span x-text="item.catatan ? 'Edit Catatan' : 'Tambah Catatan'"></span>
                                                </button>
                                            </div>

                                            <!-- Catatan Edit Form -->
                                            <div class="mt-2 flex flex-col gap-2" x-show="editingNoteKey === key" x-transition>
                                                <input type="text" x-model="editNoteText" placeholder="Contoh: Pedas, tanpa micin" class="w-full text-[13px] bg-gray-50 border border-gray-200 text-gray-900 rounded-lg focus:ring-telkom-red focus:border-telkom-red block px-3 py-2" @keydown.enter="saveNote(key)">
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="saveNote(key)" class="text-[12px] bg-telkom-red text-white font-semibold py-1.5 px-3 rounded-md hover:bg-red-700 transition-colors">Simpan</button>
                                                    <button type="button" @click="cancelEditNote()" class="text-[12px] bg-gray-200 text-gray-700 font-semibold py-1.5 px-3 rounded-md hover:bg-gray-300 transition-colors">Batal</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Price Mobile -->
                                    <div class="sm:hidden flex justify-between items-center text-sm font-bold text-gray-900">
                                        <span>Harga:</span>
                                        <span>Rp <span x-text="formatPrice(item.harga)"></span></span>
                                    </div>

                                    <!-- Price Desktop -->
                                    <div class="hidden sm:block col-span-2 text-center font-bold text-gray-900 text-sm">
                                        Rp <span x-text="formatPrice(item.harga)"></span>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="col-span-1 sm:col-span-2 flex justify-between sm:justify-center items-center">
                                        <span class="sm:hidden text-sm font-bold text-gray-900">Jumlah:</span>
                                        <div class="flex items-center space-x-3 bg-white border border-gray-200 rounded-full px-1 py-1">
                                            <button type="button" @click.prevent="updateQuantity(key, 'decrease')" class="w-7 h-7 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-900 flex items-center justify-center transition-colors">
                                                <i class="ph-bold ph-minus text-xs"></i>
                                            </button>
                                            <span class="font-bold text-gray-900 text-sm min-w-[1rem] text-center" x-text="item.quantity"></span>
                                            <button type="button" @click.prevent="updateQuantity(key, 'increase')" class="w-7 h-7 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-900 flex items-center justify-center transition-colors">
                                                <i class="ph-bold ph-plus text-xs"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="col-span-1 sm:col-span-2 flex justify-between sm:justify-end items-center">
                                        <span class="sm:hidden text-sm font-bold text-gray-900">Subtotal:</span>
                                        <div class="flex items-center gap-3">
                                            <span class="font-black text-gray-900 text-[15px]">Rp <span x-text="formatPrice(item.harga * item.quantity)"></span></span>
                                            <button type="button" @click.prevent="removeItem(key)" class="text-gray-300 hover:text-telkom-red transition-colors p-1" title="Hapus Item">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="Object.keys(cartItems).length === 0">
                                <div class="text-center py-8">
                                    <i class="ph ph-shopping-cart text-4xl text-gray-300 mb-3 block"></i>
                                    <p class="text-gray-500 text-sm">Keranjang Anda kosong.</p>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Add More Button -->
                        <div class="mt-8">
                            <a href="{{ $tenantId ? route('pelanggan.tenant.show', $tenantId) : route('pelanggan.dashboard') }}" class="w-full flex items-center justify-center py-3.5 border-2 border-telkom-red text-telkom-red hover:bg-red-50 font-bold rounded-xl transition-colors">
                                <i class="ph-bold ph-plus mr-2"></i> Tambah Menu Lagi
                            </a>
                            <p class="text-center text-xs text-gray-400 mt-3">Kembali ke menu untuk menambahkan pesanan lainnya.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-sm text-gray-500 px-2">
                    <i class="ph ph-info"></i>
                    <p>Pastikan semua pesanan sudah benar sebelum melanjutkan ke proses berikutnya.</p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="w-full lg:w-[380px] shrink-0">
                <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h3>
                    
                    <p class="text-sm text-gray-500 mb-6"><span x-text="totalQty"></span> item</p>
                    
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                        <span class="text-sm font-semibold text-gray-600">Subtotal</span>
                        <span class="text-sm font-bold text-gray-900">Rp <span x-text="formatPrice(totalPrice)"></span></span>
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-base font-bold text-gray-900">Total</span>
                        <span class="text-2xl font-black text-telkom-red">Rp <span x-text="formatPrice(totalPrice)"></span></span>
                    </div>
                    
                    <div class="flex items-start gap-2 bg-gray-50 border border-gray-100 p-3.5 rounded-xl mb-6">
                        <i class="ph ph-info text-gray-400 mt-0.5"></i>
                        <p class="text-[11px] text-gray-500 leading-relaxed">Periksa kembali detail pesanan, jenis layanan, dan nomor meja sebelum melanjutkan.</p>
                    </div>
                    
                    <button type="button" @click="proceedToPayment()" class="w-full py-4 bg-[#E31E24] hover:bg-red-700 text-white font-bold rounded-xl transition-colors shadow-lg flex items-center justify-center" :disabled="Object.keys(cartItems).length === 0 || isProcessingPayment" :class="(Object.keys(cartItems).length === 0 || isProcessingPayment) ? 'opacity-50 cursor-not-allowed' : ''">
                        <span x-show="!isProcessingPayment">Lanjut ke Pembayaran</span>
                        <span x-show="isProcessingPayment" class="flex items-center">
                            <i class="ph ph-spinner animate-spin text-xl mr-2"></i> Memproses Pesanan...
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkoutPage', () => ({
                orderType: 'dine-in',
                tableNumber: '',
                noTableYet: false,
                showTableError: false,
                cartItems: {!! json_encode($cart) !!},
                isProcessingPayment: false,
                editingNoteKey: null,
                editNoteText: '',
                
                get totalQty() {
                    let total = 0;
                    for (const key in this.cartItems) {
                        total += this.cartItems[key].quantity;
                    }
                    return total;
                },
                
                get totalPrice() {
                    let total = 0;
                    for (const key in this.cartItems) {
                        total += (this.cartItems[key].harga * this.cartItems[key].quantity);
                    }
                    return total;
                },
                
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },
                
                getFormattedOptions(options) {
                    if (!options) return [];
                    if (Array.isArray(options)) return options;
                    if (typeof options === 'string' && options.length > 0) {
                        return options.split(', ').map(opt => {
                            const parts = opt.split(': ');
                            if (parts.length >= 2) {
                                return { label: parts[0], value: parts.slice(1).join(': ') };
                            }
                            return { label: '', value: opt };
                        });
                    }
                    return [];
                },
                
                startEditNote(key) {
                    this.editingNoteKey = key;
                    this.editNoteText = this.cartItems[key].catatan || '';
                },
                
                cancelEditNote() {
                    this.editingNoteKey = null;
                    this.editNoteText = '';
                },
                
                async saveNote(cartKey) {
                    // Optimistic update
                    const oldNote = this.cartItems[cartKey].catatan;
                    this.cartItems[cartKey].catatan = this.editNoteText;
                    this.editingNoteKey = null;
                    
                    try {
                        const response = await fetch('{{ route("pelanggan.cart.update-note") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                cart_key: cartKey,
                                catatan: this.editNoteText
                            })
                        });
                    } catch (error) {
                        console.error('Error saving note:', error);
                        // Revert on error
                        this.cartItems[cartKey].catatan = oldNote;
                    }
                },
                
                async updateQuantity(cartKey, action) {
                    const currentQty = this.cartItems[cartKey].quantity;
                    if (action === 'minus' && currentQty <= 1) return;
                    
                    // Optimistic update
                    if (action === 'plus') this.cartItems[cartKey].quantity++;
                    if (action === 'minus') this.cartItems[cartKey].quantity--;
                    
                    try {
                        const response = await fetch('{{ route("pelanggan.cart.update") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                cart_key: cartKey,
                                action: action === 'plus' ? 'increase' : 'decrease'
                            })
                        });
                    } catch (error) {
                        console.error('Error updating cart:', error);
                    }
                },
                
                async removeItem(cartKey) {
                    if(!confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) return;
                    
                    // Optimistic
                    delete this.cartItems[cartKey];
                    
                    try {
                        const response = await fetch('{{ route("pelanggan.cart.remove") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                cart_key: cartKey
                            })
                        });
                    } catch (error) {
                        console.error('Error removing item:', error);
                    }
                },
                
                async proceedToPayment() {
                    if (this.orderType === 'dine-in' && !this.noTableYet && !this.tableNumber.trim()) {
                        this.showTableError = true;
                        window.scrollTo({top: 0, behavior: 'smooth'});
                        return;
                    }
                    this.showTableError = false;

                    this.isProcessingPayment = true;
                    try {
                        const response = await fetch('{{ route("pelanggan.checkout.process") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                order_type: this.orderType,
                                table_number: this.tableNumber
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success && data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: async function(result){
                                    // Panggil endpoint untuk update status karena webhook tidak jalan di localhost
                                    try {
                                        await fetch('/pelanggan/checkout/success-local', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                order_id: data.order_id,
                                                payment_type: result.payment_type || 'qris'
                                            })
                                        });
                                    } catch (e) {
                                        console.error(e);
                                    }
                                    alert('Pembayaran sukses!');
                                    window.location.href = '/pelanggan/orders';
                                },
                                onPending: function(result){
                                    alert('Menunggu pembayaran Anda!');
                                    window.location.href = '/pelanggan/orders';
                                },
                                onError: function(result){
                                    alert('Pembayaran gagal!');
                                    window.location.reload();
                                },
                                onClose: function() {
                                    alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                                    window.location.href = '/pelanggan/orders';
                                }
                            });
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat memproses pesanan.');
                            this.isProcessingPayment = false;
                        }
                    } catch (error) {
                        console.error('Checkout error:', error);
                        alert('Terjadi kesalahan koneksi.');
                        this.isProcessingPayment = false;
                    }
                }
            }));
        });
    </script>
</div>
@endsection
