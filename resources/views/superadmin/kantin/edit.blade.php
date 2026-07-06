@extends('layouts.superadmin')

@section('title', 'Edit Kantin')
@section('breadcrumb', 'Kantin / Edit')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">Edit Kantin: {{ $kantin->nama_kantin }}</h2>
    </div>
    
    <form action="{{ route('superadmin.kantin.update', $kantin->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label for="nama_kantin" class="block text-sm font-medium text-gray-700 mb-1">Nama Kantin <span class="text-red-500">*</span></label>
            <input type="text" name="nama_kantin" id="nama_kantin" value="{{ old('nama_kantin', $kantin->nama_kantin) }}" required placeholder="Contoh: Kantin GKU" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border @error('nama_kantin') border-red-500 @enderror">
            @error('nama_kantin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi / Gedung <span class="text-red-500">*</span></label>
            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $kantin->lokasi) }}" required placeholder="Contoh: Gedung Kuliah Umum" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border @error('lokasi') border-red-500 @enderror">
            @error('lokasi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Operasional <span class="text-red-500">*</span></label>
            <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border">
                <option value="nonaktif" {{ old('status', $kantin->status) == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                <option value="aktif" {{ old('status', $kantin->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
            </select>
        </div>

        <div>
            <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto / Thumbnail Kantin (Biarkan kosong jika tidak ingin mengubah)</label>
            
            <div id="upload-area" class="{{ $kantin->foto ? 'hidden' : '' }} mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition-colors cursor-pointer" onclick="document.getElementById('foto').click()">
                <div class="space-y-1 text-center">
                    <i class="fa-regular fa-image text-4xl text-gray-400"></i>
                    <div class="flex text-sm text-gray-600 justify-center mt-2">
                        <span class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                            <span>Pilih gambar</span>
                            <input id="foto" name="foto" type="file" class="sr-only" accept="image/*" onchange="handleImageUpload(this)">
                        </span>
                        <p class="pl-1 text-gray-500">atau tarik dan lepas di sini</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, JPEG (Maks. 2MB)</p>
                </div>
            </div>

            <div id="preview-area" class="{{ !$kantin->foto ? 'hidden' : '' }} mt-1">
                <div class="relative rounded-md overflow-hidden border border-gray-200 mb-3">
                    <img id="image-preview" src="{{ $kantin->foto ? asset('storage/'.$kantin->foto) : '#' }}" alt="Preview" class="w-full h-48 object-cover">
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="document.getElementById('foto').click()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors"><i class="fa-solid fa-arrows-rotate mr-2"></i> Ganti Foto</button>
                    @if(!$kantin->foto)
                    <button type="button" onclick="removeImage()" id="btn-remove-image" class="px-4 py-2 bg-red-50 border border-red-100 text-red-600 rounded-md text-sm font-medium hover:bg-red-100 transition-colors"><i class="fa-solid fa-trash mr-2"></i> Hapus Foto</button>
                    @endif
                </div>
            </div>
            
            <p id="file-error" class="hidden mt-1.5 text-sm text-red-500 font-medium"></p>
            @error('foto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mt-8 flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center space-x-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Update Kantin
                </button>
                <a href="{{ route('superadmin.kantin.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </form>
    
    <div class="px-6 pb-6 mt-[-1rem]">
        <form action="{{ route('superadmin.kantin.destroy', $kantin->id) }}" method="POST" class="inline-block" onsubmit="confirmFormSubmit(event, 'Apakah Anda yakin ingin menghapus kantin ini? Pastikan tidak ada tenant aktif di kantin ini.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                <i class="fa-solid fa-trash mr-2"></i> Hapus Kantin
            </button>
        </form>
    </div>
    
    <div class="px-6 pb-6">
        <h3 class="font-semibold text-gray-700 border-b pb-2 mb-4">Daftar Tenant ({{ $kantin->tenants->count() }})</h3>
        @if($kantin->tenants->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($kantin->tenants as $tenant)
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800">{{ $tenant->nama_tenant }}</h4>
                        <p class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-tag mr-1"></i> {{ $tenant->jenis_makanan }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $tenant->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $tenant->status }}
                    </span>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <p class="text-sm text-gray-500">Belum ada tenant yang terdaftar di kantin ini.</p>
            </div>
        @endif
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
                
                // Show remove button if it exists
                const btnRemove = document.getElementById('btn-remove-image');
                if(btnRemove) btnRemove.classList.remove('hidden');
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
