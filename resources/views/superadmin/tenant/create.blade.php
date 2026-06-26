@extends('layouts.superadmin')

@section('title', 'Add New Tenant')
@section('breadcrumb', 'Tenant / Create')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">Create New Tenant</h2>
    </div>
    
    <form action="{{ route('superadmin.tenant.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf
        
        <h3 class="font-semibold text-gray-700 border-b pb-2">Profil Tenant / Warung</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nama_tenant" class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha / Tenant <span class="text-red-500">*</span></label>
                <input type="text" name="nama_tenant" id="nama_tenant" value="{{ old('nama_tenant') }}" required placeholder="Contoh: Ayam Geprek Sa'i" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border @error('nama_tenant') border-red-500 @enderror">
                @error('nama_tenant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="kantin_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kantin <span class="text-red-500">*</span></label>
                <select name="kantin_id" id="kantin_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border @error('kantin_id') border-red-500 @enderror">
                    <option value="" disabled selected>-- Pilih Kantin --</option>
                    @foreach($kantins as $kantin)
                        <option value="{{ $kantin->id }}" {{ old('kantin_id') == $kantin->id ? 'selected' : '' }}>{{ $kantin->nama_kantin }}</option>
                    @endforeach
                </select>
                @error('kantin_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="jenis_makanan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Tenant <span class="text-red-500">*</span></label>
                <select name="jenis_makanan" id="jenis_makanan" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border @error('jenis_makanan') border-red-500 @enderror">
                    <option value="" disabled {{ old('jenis_makanan') ? '' : 'selected' }}>-- Pilih Jenis --</option>
                    <option value="Makanan Berat" {{ old('jenis_makanan') == 'Makanan Berat' ? 'selected' : '' }}>Makanan Berat</option>
                    <option value="Makanan Ringan" {{ old('jenis_makanan') == 'Makanan Ringan' ? 'selected' : '' }}>Makanan Ringan</option>
                    <option value="Minuman" {{ old('jenis_makanan') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                </select>
                @error('jenis_makanan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
                <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon') }}" required placeholder="Contoh: 081234567890" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border @error('no_telepon') border-red-500 @enderror">
                @error('no_telepon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Operasional <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border">
                    <option value="nonaktif" {{ old('status', 'nonaktif') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                </select>
            </div>
        </div>

        <h3 class="font-semibold text-gray-700 border-b pb-2 pt-4">Logo / Foto Profil Tenant (Opsional)</h3>
        <div>
            <div id="upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition-colors cursor-pointer max-w-sm" onclick="document.getElementById('foto').click()">
                <div class="space-y-1 text-center">
                    <i class="fa-regular fa-image text-4xl text-gray-400"></i>
                    <div class="flex text-sm text-gray-600 justify-center mt-2">
                        <span class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                            <span>Pilih gambar</span>
                            <input id="foto" name="foto" type="file" class="sr-only" accept="image/*" onchange="handleImageUpload(this)">
                        </span>
                        <p class="pl-1 text-gray-500">atau tarik</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, JPEG (Maks. 2MB)</p>
                </div>
            </div>

            <div id="preview-area" class="hidden mt-1 max-w-sm">
                <div class="relative rounded-md overflow-hidden border border-gray-200 mb-3 bg-gray-50 flex justify-center">
                    <img id="image-preview" src="#" alt="Preview" class="h-48 w-auto object-contain">
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="document.getElementById('foto').click()" class="w-full px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors"><i class="fa-solid fa-arrows-rotate mr-2"></i> Ganti Foto</button>
                    <button type="button" onclick="removeImage()" class="w-full px-4 py-2 bg-red-50 border border-red-100 text-red-600 rounded-md text-sm font-medium hover:bg-red-100 transition-colors"><i class="fa-solid fa-trash mr-2"></i> Hapus Foto</button>
                </div>
            </div>
            
            <p id="file-error" class="hidden mt-1.5 text-sm text-red-500 font-medium"></p>
            @error('foto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mt-8 flex items-center space-x-3 pt-4 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                Save Tenant
            </button>
            <a href="{{ route('superadmin.tenant.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>
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
            if (file.size > 2 * 1024 * 1024) {
                errorElement.textContent = 'Ukuran file foto tidak boleh lebih dari 2MB.';
                errorElement.classList.remove('hidden');
                input.value = '';
                return;
            }

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

        input.value = '';
        imagePreview.src = '#';
        uploadArea.classList.remove('hidden');
        previewArea.classList.add('hidden');
        errorElement.classList.add('hidden');
    }
</script>
@endsection
