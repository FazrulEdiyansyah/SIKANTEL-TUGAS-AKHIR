@props(['menu', 'tenant'])

<!-- Menu Card -->
<div class="bg-white rounded-[20px] sm:rounded-[24px] shadow-sm hover:shadow-md overflow-hidden flex flex-row sm:flex-col transition-all group" :class="(cart.menuQty[{{ $menu->id }}] || 0) > 0 ? 'border-y border-r border-gray-100 border-l-[4px] sm:border-l-[6px] border-l-[#E31E24]' : 'border border-gray-100'">
    <!-- Image -->
    <div class="w-[110px] h-[110px] sm:w-full sm:h-48 bg-gray-100 relative overflow-hidden shrink-0 m-2 sm:m-0 rounded-xl sm:rounded-none">
        @if($menu->foto)
            <img src="{{ asset('storage/' . $menu->foto) }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <img src="{{ asset('images/no-image.png') }}" loading="lazy" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500">
        @endif
    </div>
    
    <!-- Content -->
    <div class="p-3 sm:p-5 flex-1 flex flex-col justify-center sm:justify-start">
        <h3 class="text-[14px] sm:text-[15px] font-bold text-gray-900 leading-tight mb-1 sm:mb-2 line-clamp-2">{{ $menu->nama_menu }}</h3>
        <p class="text-[13px] sm:text-[14px] text-gray-700 font-semibold mb-2 sm:mb-4 flex-1">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
        
        <!-- Status Label -->
        @if($menu->status === 'tersedia')
            <span class="text-[11px] font-bold text-green-600 mb-2 sm:mb-3 hidden sm:flex items-center">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Tersedia
            </span>
        @else
            <span class="text-[11px] font-bold text-gray-400 mb-2 sm:mb-3 block flex items-center">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-1.5"></span> Habis
            </span>
        @endif
        
        <!-- Action Area -->
        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center" :class="(cart.menuQty[{{ $menu->id }}] || 0) > 0 ? 'justify-between' : 'justify-center'">
            @if($menu->status === 'tersedia' && $tenant->is_open)
                
                <!-- In Cart State -->
                <template x-if="(cart.menuQty[{{ $menu->id }}] || 0) > 0">
                    <div class="w-full flex items-center justify-between">
                        <button type="button" @click="activeModal = {{ $menu->id }}; document.body.style.overflow = 'hidden';" class="text-telkom-red font-bold flex items-center text-[13px] px-2 py-1 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="ph-fill ph-note-pencil mr-1 text-lg"></i> Catatan
                        </button>
                        <div class="flex items-center space-x-3">
                            <button type="button" @click.prevent="decreaseCart({{ $menu->id }}, {{ $menu->harga }})" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                <i class="ph-bold ph-minus"></i>
                            </button>
                            
                            <span class="font-medium text-gray-900" x-text="cart.menuQty[{{ $menu->id }}]"></span>
                            
                            @if($menu->is_customizable && !empty($menu->customizations))
                                <button type="button" @click="activeModal = {{ $menu->id }}; document.body.style.overflow = 'hidden';" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                    <i class="ph-bold ph-plus"></i>
                                </button>
                            @else
                                <button type="button" @click.prevent="addToCart({{ $menu->id }}, {{ $menu->harga }})" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                    <i class="ph-bold ph-plus"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </template>

                <!-- Not In Cart State -->
                <template x-if="(cart.menuQty[{{ $menu->id }}] || 0) === 0">
                    <div class="w-full">
                        @if($menu->is_customizable && !empty($menu->customizations))
                            <button type="button" @click="if(canAddMenu()) { activeModal = {{ $menu->id }}; document.body.style.overflow = 'hidden'; }" class="w-full block text-center bg-[#E31E24] hover:bg-red-700 text-white font-bold text-[13px] px-5 py-2.5 rounded-xl transition-colors shadow-sm cursor-pointer">
                                Tambah
                            </button>
                        @else
                            <button type="button" @click.prevent="addToCart({{ $menu->id }}, {{ $menu->harga }})" class="w-full block text-center bg-[#E31E24] hover:bg-red-700 text-white font-bold text-[13px] px-5 py-2.5 rounded-xl transition-colors shadow-sm cursor-pointer">
                                Tambah
                            </button>
                        @endif
                    </div>
                </template>

            @else
                <button disabled class="w-full bg-gray-100 text-gray-400 font-bold text-[13px] px-5 py-2.5 rounded-xl cursor-not-allowed border border-gray-200">
                    Habis
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Alpine.js Modal Implementation -->
@if($menu->status === 'tersedia' && $tenant->is_open)
    <template x-teleport="body">
        <div x-show="activeModal === {{ $menu->id }}" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <!-- Backdrop (No blur to fix lag) -->
            <div x-show="activeModal === {{ $menu->id }}" 
                 x-transition.opacity.duration.300ms
                 class="absolute inset-0 bg-black/60 cursor-pointer" 
                 @click="activeModal = null; document.body.style.overflow = '';"></div>
            
            <!-- Modal Content -->
            <div x-show="activeModal === {{ $menu->id }}" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 class="relative bg-white rounded-3xl shadow-2xl w-full sm:w-[420px] max-w-full overflow-hidden flex flex-col max-h-[85vh]">
                
                <!-- Close Button -->
                <button type="button" @click="activeModal = null; document.body.style.overflow = '';" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:text-gray-900 transition-colors z-10">
                    <i class="ph-bold ph-x text-sm"></i>
                </button>
                
                <!-- Form with Alpine Scope for dynamic price -->
                <form action="{{ route('pelanggan.cart.add') }}" method="POST" data-no-loading class="flex flex-col flex-1 overflow-hidden" @submit.prevent="submitModalForm($event, {{ $menu->id }}, {{ $menu->harga }})" x-data="{ modalQty: 1, basePrice: {{ $menu->harga }} }" x-init="$watch('activeModal', value => { if(value === {{ $menu->id }}) { modalQty = 1; } })">
                    @csrf
                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                    
                    <div class="p-6 pt-10 space-y-6 overflow-y-auto custom-scrollbar">
                        <div class="flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $menu->nama_menu }}</h3>
                            @if($menu->deskripsi)
                                <p class="text-sm text-gray-500 mb-3">{{ $menu->deskripsi }}</p>
                            @endif
                            <span class="text-lg font-bold text-gray-900">{{ number_format($menu->harga, 0, ',', '.') }}</span>
                        </div>
                    
                        <div class="border-t border-gray-100 pt-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 class="font-bold text-gray-900">Catatan</h4>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">Opsional</p>
                            <textarea name="catatan" rows="3" maxlength="200" placeholder="Contoh: banyakin porsinya, ya" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-telkom-red focus:bg-white transition-all resize-none"></textarea>
                        </div>
                    
                        @if(!empty($menu->customizations))
                            @foreach($menu->customizations as $sIndex => $section)
                                <div class="border-t border-gray-100 pt-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $section['name'] }}</h4>
                                            @if($section['is_required'])
                                                <p class="text-xs text-telkom-red font-semibold">Wajib <span class="text-gray-400 font-normal">Pilih 1</span></p>
                                            @else
                                                <p class="text-xs text-gray-400 font-semibold">Opsional</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($section['options'] as $oIndex => $option)
                                            <label class="flex items-center justify-between p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-red-50 hover:border-telkom-red transition-colors group">
                                                <div>
                                                    <span class="text-sm font-semibold text-gray-700 group-hover:text-telkom-red">{{ $option['name'] }}</span>
                                                    @if(isset($option['price_adjustment']) && $option['price_adjustment'] > 0)
                                                        <span class="ml-2 text-xs text-gray-500 font-medium">+Rp {{ number_format($option['price_adjustment'], 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                                <input type="radio" name="custom_options[{{$sIndex}}]" value="{{ $oIndex }}" {{ $section['is_required'] ? 'required' : '' }} class="w-5 h-5 text-telkom-red focus:ring-telkom-red border-gray-300">
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Footer Actions -->
                    <div class="p-6 border-t border-gray-100 bg-white shrink-0">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-medium text-gray-900 text-base">Mau berapa?</span>
                            <div class="flex items-center space-x-4">
                                <button type="button" @click="if(modalQty > 1) modalQty--" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                    <i class="ph-bold ph-minus"></i>
                                </button>
                                <input type="hidden" name="quantity" x-model="modalQty">
                                <span class="font-medium text-lg text-gray-900" x-text="modalQty"></span>
                                <button type="button" @click="modalQty++" class="w-8 h-8 rounded-full border border-telkom-red text-telkom-red flex items-center justify-center hover:bg-red-50 transition-colors">
                                    <i class="ph-bold ph-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3.5 bg-[#E31E24] hover:bg-red-700 text-white font-bold rounded-xl transition-colors shadow-lg text-[15px]">
                            Perbaharui keranjang - <span x-text="formatPrice(basePrice * modalQty)"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
@endif
