@extends('layouts.dashboard')

@section('title', 'Tambah Data Kantin - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="kantin" />
@endsection

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('pengelola.kantin.index') }}" class="hover:text-telkom-red transition-colors font-medium">Data Kantin</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-900 font-semibold">Tambah Kantin Baru</span>
            </div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight">Tambah Kantin Baru</h1>
        </div>
    </div>

    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
        <div class="p-8">
            <form action="{{ route('pengelola.kantin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Nama Kantin -->
                <div>
                    <label for="nama_kantin" class="block text-sm font-semibold text-gray-700 mb-2">Nama Kantin <span class="text-telkom-red">*</span></label>
                    <input type="text" name="nama_kantin" id="nama_kantin" required placeholder="Contoh: Kantin GKU" value="{{ old('nama_kantin') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('nama_kantin') border-red-500 ring-red-100 @enderror">
                    @error('nama_kantin')
                        <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-sm font-semibold text-gray-700 mb-2">Lokasi / Gedung <span class="text-telkom-red">*</span></label>
                    <input type="text" name="lokasi" id="lokasi" required placeholder="Contoh: Gedung Kuliah Umum" value="{{ old('lokasi') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('lokasi') border-red-500 ring-red-100 @enderror">
                    @error('lokasi')
                        <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status Operasional <span class="text-telkom-red">*</span></label>
                    <div class="relative">
                        <select name="status" id="status" required class="w-full px-4 py-3 bg-white rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                            <option value="nonaktif" {{ old('status', 'nonaktif') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        </select>
                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Foto Thumbnail -->
                <div>
                    <label for="foto" class="block text-sm font-semibold text-gray-700 mb-2">Foto / Thumbnail Kantin <span class="text-telkom-red">*</span></label>
                    
                    <!-- Upload Area -->
                    <div id="upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-telkom-red/50 hover:bg-red-50/50 transition-colors cursor-pointer relative" onclick="document.getElementById('foto').click()">
                        <div class="space-y-1 text-center">
                            <i class="ph ph-image text-4xl text-gray-400"></i>
                            <div class="flex text-sm text-gray-600 justify-center mt-2">
                                <span class="relative cursor-pointer bg-transparent rounded-md font-semibold text-telkom-red hover:text-telkom-maroon focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-telkom-red">
                                    <span>Pilih gambar</span>
                                    <input id="foto" name="foto" type="file" class="sr-only" accept="image/*" required onchange="handleImageUpload(this)">
                                </span>
                                <p class="pl-1 text-gray-500">atau tarik dan lepas di sini</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">PNG, JPG, JPEG (Maks. 2MB)</p>
                        </div>
                    </div>

                    <!-- Preview Area -->
                    <div id="preview-area" class="hidden mt-1">
                        <div class="relative rounded-xl overflow-hidden border border-gray-200 mb-3">
                            <img id="image-preview" src="#" alt="Preview" class="w-full h-48 object-cover">
                        </div>
                        <div class="flex items-center justify-center space-x-3">
                            <button type="button" onclick="document.getElementById('foto').click()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-50 transition-colors w-full flex justify-center items-center"><i class="ph ph-arrows-clockwise mr-2"></i> Ganti Foto</button>
                            <button type="button" onclick="removeImage()" class="px-4 py-2 bg-red-50 border border-red-100 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 transition-colors w-full flex justify-center items-center"><i class="ph ph-trash mr-2"></i> Hapus Foto</button>
                        </div>
                    </div>
                    
                    <p id="file-error" class="hidden mt-1.5 text-sm text-red-500 font-medium"></p>

                    @error('foto')
                        <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-6 mt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('pengelola.kantin.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-telkom-red hover:bg-telkom-maroon shadow-sm transition-colors">Simpan Kantin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleImageUpload(input) {
            const file = input.files[0];
            const errorElement = document.getElementById('file-error');
            const uploadArea = document.getElementById('upload-area');
            const previewArea = document.getElementById('preview-area');
            const imagePreview = document.getElementById('image-preview');

            errorElement.classList.add('hidden');

            if (file) {
                // Check file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    errorElement.textContent = 'Ukuran file foto tidak boleh lebih dari 2MB.';
                    errorElement.classList.remove('hidden');
                    input.value = ''; // Reset input
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    uploadArea.classList.add('hidden');
                    previewArea.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            const input = document.getElementById('foto');
            const uploadArea = document.getElementById('upload-area');
            const previewArea = document.getElementById('preview-area');
            const imagePreview = document.getElementById('image-preview');
            const errorElement = document.getElementById('file-error');

            input.value = ''; // Reset input
            imagePreview.src = '#';
            uploadArea.classList.remove('hidden');
            previewArea.classList.add('hidden');
            errorElement.classList.add('hidden');
        }
    </script>
@endsection
