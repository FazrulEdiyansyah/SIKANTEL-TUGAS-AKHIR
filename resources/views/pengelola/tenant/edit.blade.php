@extends('layouts.dashboard')

@section('title', 'Edit Data Tenant - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="tenant" />
@endsection

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('pengelola.tenant.index') }}" class="hover:text-telkom-red transition-colors font-medium">Data Tenant</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-900 font-semibold">Edit Data Tenant</span>
            </div>
            <h1 class="text-[26px] font-bold text-gray-900 tracking-tight">Edit Data Tenant</h1>
        </div>
    </div>

    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
        <div class="p-8">
            <form action="{{ route('pengelola.tenant.update', $tenant->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- SEKSI 1: PROFIL TENANT -->
                <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-3">Profil Tenant / Warung</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Nama Tenant -->
                    <div>
                        <label for="nama_tenant" class="block text-sm font-semibold text-gray-700 mb-2">Nama Usaha / Tenant <span class="text-telkom-red">*</span></label>
                        <input type="text" name="nama_tenant" id="nama_tenant" required placeholder="Contoh: Ayam Geprek Sa'i" value="{{ old('nama_tenant', $tenant->nama_tenant) }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('nama_tenant') border-red-500 ring-red-100 @enderror">
                        @error('nama_tenant')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kantin -->
                    <div>
                        <label for="kantin_id" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kantin <span class="text-telkom-red">*</span></label>
                        <div class="relative">
                            <select name="kantin_id" id="kantin_id" required class="w-full px-4 py-3 bg-white rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all @error('kantin_id') border-red-500 ring-red-100 @enderror">
                                <option value="" disabled>-- Pilih Kantin --</option>
                                @foreach($kantins as $kantin)
                                    <option value="{{ $kantin->id }}" {{ old('kantin_id', $tenant->kantin_id) == $kantin->id ? 'selected' : '' }}>{{ $kantin->nama_kantin }}</option>
                                @endforeach
                            </select>
                            <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                        @error('kantin_id')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Tenant -->
                    <div>
                        <label for="jenis_makanan" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Tenant <span class="text-telkom-red">*</span></label>
                        <div class="relative">
                            <select name="jenis_makanan" id="jenis_makanan" required class="w-full px-4 py-3 bg-white rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all @error('jenis_makanan') border-red-500 ring-red-100 @enderror">
                                <option value="" disabled>-- Pilih Jenis Tenant --</option>
                                <option value="Makanan Berat" {{ old('jenis_makanan', $tenant->jenis_makanan) == 'Makanan Berat' ? 'selected' : '' }}>Makanan Berat</option>
                                <option value="Makanan Ringan" {{ old('jenis_makanan', $tenant->jenis_makanan) == 'Makanan Ringan' ? 'selected' : '' }}>Makanan Ringan</option>
                                <option value="Minuman" {{ old('jenis_makanan', $tenant->jenis_makanan) == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                            </select>
                            <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                        @error('jenis_makanan')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status Operasional <span class="text-telkom-red">*</span></label>
                        <div class="relative">
                            <select name="status" id="status" required class="w-full px-4 py-3 bg-white rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red appearance-none cursor-pointer transition-all">
                                <option value="nonaktif" {{ old('status', $tenant->status) == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                                <option value="aktif" {{ old('status', $tenant->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            </select>
                            <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Foto Thumbnail -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Logo / Foto Profil Tenant (Opsional)</label>
                    <!-- Upload Area -->
                    <div id="upload-area" class="{{ $tenant->foto ? 'hidden' : '' }} mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-telkom-red/50 hover:bg-red-50/50 transition-colors cursor-pointer relative" onclick="document.getElementById('foto').click()">
                        <div class="space-y-1 text-center">
                            <i class="ph ph-image text-4xl text-gray-400"></i>
                            <div class="flex text-sm text-gray-600 justify-center mt-2">
                                <span class="relative cursor-pointer bg-transparent rounded-md font-semibold text-telkom-red hover:text-telkom-maroon focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-telkom-red">
                                    <span>Pilih gambar</span>
                                    <input id="foto" name="foto" type="file" class="sr-only" accept="image/*" onchange="handleImageUpload(this)">
                                </span>
                                <p class="pl-1 text-gray-500">atau tarik dan lepas di sini</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">PNG, JPG, JPEG (Maks. 2MB)</p>
                        </div>
                    </div>

                    <!-- Preview Area -->
                    <div id="preview-area" class="{{ $tenant->foto ? '' : 'hidden' }} mt-1 max-w-sm">
                        <div class="relative rounded-xl overflow-hidden border border-gray-200 mb-3 flex justify-center bg-gray-50">
                            <img id="image-preview" src="{{ $tenant->foto ? asset('storage/' . $tenant->foto) : '#' }}" alt="Preview" class="h-48 w-auto object-contain">
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

                <!-- SEKSI 2: DATA PRIBADI & KONTAK -->
                <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-3 mt-10">Data Pribadi & Kontak</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">NIK (Nomor Induk Kependudukan)</label>
                        <input type="text" name="nik" id="nik" placeholder="Contoh: 327000111222333" value="{{ old('nik', $tenant->nik) }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('nik') border-red-500 ring-red-100 @enderror">
                        @error('nik')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No Telepon -->
                    <div>
                        <label for="no_telepon" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon / WhatsApp <span class="text-telkom-red">*</span></label>
                        <input type="text" name="no_telepon" id="no_telepon" required placeholder="Contoh: 081234567890" value="{{ old('no_telepon', $tenant->no_telepon) }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('no_telepon') border-red-500 ring-red-100 @enderror">
                        @error('no_telepon')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3" placeholder="Contoh: Jl. Telekomunikasi No. 1..." 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('address') border-red-500 ring-red-100 @enderror">{{ old('address', $tenant->address) }}</textarea>
                        @error('address')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SEKSI 3: INFORMASI REKENING BANK -->
                <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-3 mt-10">Informasi Rekening Bank</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Nama Bank -->
                    <div>
                        <label for="bank_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Bank</label>
                        <input type="text" name="bank_name" id="bank_name" placeholder="Contoh: BCA, Mandiri, BNI" value="{{ old('bank_name', $tenant->bank_name) }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('bank_name') border-red-500 ring-red-100 @enderror">
                        @error('bank_name')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Rekening -->
                    <div>
                        <label for="bank_account_number" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" placeholder="Contoh: 1234567890" value="{{ old('bank_account_number', $tenant->bank_account_number) }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('bank_account_number') border-red-500 ring-red-100 @enderror">
                        @error('bank_account_number')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Atas Nama -->
                    <div>
                        <label for="bank_account_name" class="block text-sm font-semibold text-gray-700 mb-2">Atas Nama</label>
                        <input type="text" name="bank_account_name" id="bank_account_name" placeholder="Contoh: Jhon Doe" value="{{ old('bank_account_name', $tenant->bank_account_name) }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('bank_account_name') border-red-500 ring-red-100 @enderror">
                        @error('bank_account_name')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SEKSI 4: DETAIL KONTRAK & DOKUMEN -->
                <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-3 mt-10">Detail Kontrak & Dokumen</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Mulai Kontrak -->
                    <div>
                        <label for="contract_start_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai Kontrak</label>
                        <input type="date" name="contract_start_date" id="contract_start_date" value="{{ old('contract_start_date', $tenant->contract_start_date ? $tenant->contract_start_date->format('Y-m-d') : '') }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('contract_start_date') border-red-500 ring-red-100 @enderror">
                        @error('contract_start_date')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Selesai Kontrak -->
                    <div>
                        <label for="contract_end_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Berakhir Kontrak</label>
                        <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date', $tenant->contract_end_date ? $tenant->contract_end_date->format('Y-m-d') : '') }}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all @error('contract_end_date') border-red-500 ring-red-100 @enderror">
                        @error('contract_end_date')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- KTP Document -->
                    <div>
                        <label for="ktp_document" class="block text-sm font-semibold text-gray-700 mb-2">Unggah Dokumen KTP</label>
                        @if($tenant->ktp_document)
                            <div class="mb-3 px-4 py-2 border border-gray-200 bg-gray-50 rounded-xl flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 truncate">Dokumen tersimpan (KTP)</span>
                                <a href="{{ asset('storage/' . $tenant->ktp_document) }}" target="_blank" class="text-sm text-telkom-red hover:underline font-semibold ml-2">Lihat</a>
                            </div>
                        @endif
                        <div class="relative">
                            <input type="file" name="ktp_document" id="ktp_document" accept=".pdf,.jpg,.jpeg,.png,.webp"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all bg-white @error('ktp_document') border-red-500 ring-red-100 @enderror">
                        </div>
                        <p class="mt-1.5 text-[13px] text-gray-500">Format: PDF, JPG, PNG (Maks 2MB). {{ $tenant->ktp_document ? 'Upload file baru untuk mengganti.' : '' }}</p>
                        @error('ktp_document')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contract Document -->
                    <div>
                        <label for="contract_document" class="block text-sm font-semibold text-gray-700 mb-2">Unggah Surat Perjanjian / Kontrak</label>
                        @if($tenant->contract_document)
                            <div class="mb-3 px-4 py-2 border border-gray-200 bg-gray-50 rounded-xl flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 truncate">Dokumen tersimpan (Kontrak)</span>
                                <a href="{{ asset('storage/' . $tenant->contract_document) }}" target="_blank" class="text-sm text-telkom-red hover:underline font-semibold ml-2">Lihat</a>
                            </div>
                        @endif
                        <div class="relative">
                            <input type="file" name="contract_document" id="contract_document" accept=".pdf,.jpg,.jpeg,.png,.webp"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-telkom-red/20 focus:border-telkom-red transition-all bg-white @error('contract_document') border-red-500 ring-red-100 @enderror">
                        </div>
                        <p class="mt-1.5 text-[13px] text-gray-500">Format: PDF, JPG, PNG (Maks 2MB). {{ $tenant->contract_document ? 'Upload file baru untuk mengganti.' : '' }}</p>
                        @error('contract_document')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('pengelola.tenant.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-telkom-red hover:bg-telkom-maroon shadow-sm transition-colors">Simpan Perubahan</button>
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
