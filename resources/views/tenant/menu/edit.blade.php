@extends('layouts.dashboard')

@section('title', 'Edit Menu - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="menu" />
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('tenant.menu.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-telkom-red transition-colors mb-4">
            <i class="ph ph-arrow-left mr-2"></i>
            Kembali ke Menu
        </a>
        <h1 class="text-[26px] font-bold text-gray-900 tracking-tight mb-1">Edit Menu</h1>
        <p class="text-[15px] text-gray-500 font-medium">Ubah informasi menu, harga, foto, dan status ketersediaan.</p>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Informasi Menu</h2>
            
            <form action="{{ route('tenant.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" x-data="menuForm()">
                    
                    <!-- Left Column (Foto & Harga) -->
                    <div class="lg:col-span-5 flex flex-col gap-6">
                        
                        <!-- Foto Menu -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Menu</label>
                            
                            <!-- Image Preview Area -->
                            <div class="relative w-full aspect-video rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors flex flex-col items-center justify-center overflow-hidden group cursor-pointer" @click="$refs.fotoInput.click()">
                                
                                <template x-if="imageUrl">
                                    <div class="absolute inset-0 w-full h-full">
                                        <img :src="imageUrl" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="text-white font-medium text-sm flex items-center"><i class="ph ph-camera mr-2"></i> Ganti Foto</span>
                                        </div>
                                    </div>
                                </template>
                                
                                <template x-if="!imageUrl">
                                    <div class="text-center p-6 flex flex-col items-center">
                                        <div class="w-24 h-24 mb-3 opacity-60">
                                            <img src="{{ asset('images/no-image.png') }}" class="w-full h-full object-contain" alt="no image">
                                        </div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Klik untuk unggah foto</p>
                                        <p class="text-[11px] text-gray-400">Format: JPG, PNG. Maksimal 2MB.</p>
                                    </div>
                                </template>
                                
                                <!-- File Input (Hidden) -->
                                <input x-ref="fotoInput" type="file" name="foto" accept="image/jpeg, image/png, image/webp" class="hidden" @change="fileChosen">
                            </div>
                            
                            <!-- Remove Button (only show if image exists) -->
                            <template x-if="imageUrl">
                                <button type="button" @click.stop="removeImage" class="mt-3 w-full py-2.5 flex items-center justify-center rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    <i class="ph ph-upload-simple mr-2"></i> Ganti Foto
                                </button>
                            </template>
                            @error('foto')
                                <p class="text-telkom-red text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Harga -->
                        <div>
                            <label for="harga" class="block text-sm font-semibold text-gray-700 mb-2">Harga <span class="text-telkom-red">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">Rp</span>
                                <input type="number" name="harga" id="harga" value="{{ old('harga', $menu->harga) }}" required min="0" placeholder="15000" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2">Masukkan harga menu tanpa titik/koma.</p>
                            @error('harga')
                                <p class="text-telkom-red text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column (Info) -->
                    <div class="lg:col-span-7 flex flex-col gap-6">
                        
                        <!-- Nama Menu -->
                        <div>
                            <label for="nama_menu" class="block text-sm font-semibold text-gray-700 mb-2">Nama Menu <span class="text-telkom-red">*</span></label>
                            <input type="text" name="nama_menu" id="nama_menu" x-model="nama" required maxlength="100" placeholder="Contoh: Ayam Geprek Original" class="w-full px-4 py-3 bg-white border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all">
                            <div class="flex justify-between mt-2">
                                <p class="text-[11px] text-gray-400">Masukkan nama menu.</p>
                                <p class="text-[11px] text-gray-400"><span x-text="nama.length"></span> / 100</p>
                            </div>
                            @error('nama_menu')
                                <p class="text-telkom-red text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Menu <span class="text-telkom-red">*</span></label>
                            <textarea name="deskripsi" id="deskripsi" x-model="deskripsi" required rows="4" maxlength="255" placeholder="Contoh: Nasi ayam geprek dengan sambal original..." class="w-full px-4 py-3 bg-white border border-gray-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all resize-none"></textarea>
                            <div class="flex justify-between mt-2">
                                <p class="text-[11px] text-gray-400">Jelaskan menu secara singkat.</p>
                                <p class="text-[11px] text-gray-400"><span x-text="deskripsi.length"></span> / 255</p>
                            </div>
                            @error('deskripsi')
                                <p class="text-telkom-red text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Ketersediaan -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status Ketersediaan <span class="text-telkom-red">*</span></label>
                            <div class="relative">
                                <select name="status" id="status" required class="w-full px-4 py-3 bg-white border border-gray-200 text-sm text-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                                    <option value="tersedia" {{ old('status', $menu->status) === 'tersedia' ? 'selected' : '' }}>🟢 Tersedia</option>
                                    <option value="habis" {{ old('status', $menu->status) === 'habis' ? 'selected' : '' }}>🔴 Habis</option>
                                </select>
                                <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2">Pilih status ketersediaan menu.</p>
                            @error('status')
                                <p class="text-telkom-red text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Opsi Kustomisasi -->
                        <div class="pt-2 relative">
                            <input type="checkbox" id="is_customizable" name="is_customizable" value="1" {{ old('is_customizable', $menu->is_customizable) ? 'checked' : '' }} class="peer absolute top-0 left-0 w-5 h-5 text-telkom-red border-gray-300 rounded focus:ring-telkom-red transition-colors cursor-pointer">
                            
                            <label for="is_customizable" class="ml-8 block text-sm font-semibold text-gray-900 cursor-pointer">Bisa Dikustomisasi?</label>
                            <p class="text-[12px] text-gray-500 mt-1 ml-8 leading-snug">Centang jika pelanggan bisa menambahkan opsi (seperti level pedas atau ukuran) untuk menu ini saat memesan.</p>

                            <!-- Alpine.js Dynamic Builder -->
                            <div class="hidden peer-checked:block mt-6 ml-8 bg-gray-50 p-5 rounded-2xl border border-gray-200">
                                <div class="space-y-6">
                                    <template x-for="(section, sIndex) in customizations" :key="sIndex">
                                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative">
                                            <!-- Section Header -->
                                            <div class="flex flex-wrap gap-4 items-start mb-4">
                                                <div class="flex-1">
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Grup Opsi</label>
                                                    <input type="text" x-model="section.name" :name="`customizations[${sIndex}][name]`" placeholder="Misal: Level Pedas" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-telkom-red focus:border-telkom-red" required>
                                                </div>
                                                <div class="w-32">
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Sifat Pilihan</label>
                                                    <select x-model="section.is_required" :name="`customizations[${sIndex}][is_required]`" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-telkom-red focus:border-telkom-red">
                                                        <option value="1">Wajib Pilih 1</option>
                                                        <option value="0">Opsional</option>
                                                    </select>
                                                </div>
                                                <div class="flex items-center self-end mb-2 ml-2">
                                                    <input type="checkbox" :id="`multi_qty_${sIndex}`" x-model="section.is_multiple_qty" :name="`customizations[${sIndex}][is_multiple_qty]`" value="1" class="text-telkom-red border-gray-300 rounded focus:ring-telkom-red transition-colors cursor-pointer w-4 h-4 mr-2">
                                                    <label :for="`multi_qty_${sIndex}`" class="text-xs font-bold text-gray-700 cursor-pointer">Bisa Tambah Kuantitas</label>
                                                </div>
                                                <button type="button" @click="removeSection(sIndex)" class="mt-6 text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg transition-colors" title="Hapus Grup">
                                                    <i class="ph-bold ph-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Options List -->
                                            <div class="space-y-3 pl-4 border-l-2 border-gray-100">
                                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilihan-pilihannya:</p>
                                                
                                                <template x-for="(option, oIndex) in section.options" :key="oIndex">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex-1">
                                                            <input type="text" x-model="option.name" :name="`customizations[${sIndex}][options][${oIndex}][name]`" placeholder="Nama pilihan (Misal: Ekstra Pedas)" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-telkom-red focus:border-telkom-red" required>
                                                        </div>
                                                        <div class="w-32 relative">
                                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">+Rp</span>
                                                            <input type="number" x-model="option.price_adjustment" :name="`customizations[${sIndex}][options][${oIndex}][price_adjustment]`" min="0" class="w-full pl-9 pr-2 py-2 border border-gray-300 rounded-lg text-sm focus:ring-telkom-red focus:border-telkom-red" required>
                                                        </div>
                                                        <button type="button" @click="removeOption(sIndex, oIndex)" class="text-gray-400 hover:text-red-500 p-2 transition-colors" title="Hapus Pilihan">
                                                            <i class="ph-bold ph-x"></i>
                                                        </button>
                                                    </div>
                                                </template>

                                                <!-- Add Option Button -->
                                                <button type="button" @click="addOption(sIndex)" class="mt-2 text-[13px] font-bold text-telkom-red hover:text-red-700 flex items-center">
                                                    <i class="ph-bold ph-plus mr-1"></i> Tambah Pilihan
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Add Section Button -->
                                <button type="button" @click="addSection" class="mt-4 w-full py-3 border-2 border-dashed border-gray-300 text-gray-500 font-bold text-sm rounded-xl hover:border-telkom-red hover:text-telkom-red hover:bg-red-50 transition-colors flex items-center justify-center">
                                    <i class="ph-bold ph-plus-circle mr-2 text-lg"></i> Tambah Grup Kustomisasi Baru
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('tenant.menu.index') }}" class="px-6 py-2.5 border border-gray-200 text-gray-700 font-semibold text-sm rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-telkom-red hover:bg-telkom-maroon text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alpine.js logic for form -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('menuForm', () => ({
                nama: '{{ old('nama_menu', $menu->nama_menu) }}',
                deskripsi: `{{ old('deskripsi', $menu->deskripsi) }}`,
                imageUrl: '{{ $menu->foto ? asset('storage/' . $menu->foto) : '' }}',
                customizations: @json(old('customizations', $menu->customizations ?? [])),

                fileChosen(event) {
                    this.fileToDataUrl(event, src => this.imageUrl = src)
                },
                removeImage() {
                    this.imageUrl = null;
                    this.$refs.fotoInput.value = '';
                },
                fileToDataUrl(event, callback) {
                    if (! event.target.files.length) return
                    let file = event.target.files[0],
                        reader = new FileReader()
                    reader.readAsDataURL(file)
                    reader.onload = e => callback(e.target.result)
                },
                addSection() {
                    this.customizations.push({
                        name: '',
                        is_required: 1,
                        is_multiple_qty: 0,
                        options: [
                            { name: '', price_adjustment: 0 }
                        ]
                    });
                },
                removeSection(index) {
                    this.customizations.splice(index, 1);
                },
                addOption(sectionIndex) {
                    this.customizations[sectionIndex].options.push({ name: '', price_adjustment: 0 });
                },
                removeOption(sectionIndex, optionIndex) {
                    this.customizations[sectionIndex].options.splice(optionIndex, 1);
                }
            }))
        })
    </script>
@endsection
