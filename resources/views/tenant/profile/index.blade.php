@extends('layouts.dashboard')

@section('title', 'Profil Saya - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.tenant active="" />
@endsection

@section('content')
<div class="pt-4 pb-20">
    <div class="max-w-[800px] mx-auto px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <a href="{{ route('tenant.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-telkom-red transition-colors mb-6 bg-white border border-gray-100 shadow-sm px-4 py-2 rounded-xl">
            <i class="ph-bold ph-arrow-left"></i>
            Kembali ke Dashboard
        </a>

        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">Profil Saya</h1>
            <p class="text-gray-500 text-sm">Kelola informasi pribadi dan foto profil toko Anda.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 relative" role="alert">
                <span class="block sm:inline font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-8">
            <form action="{{ route('tenant.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Foto Profil -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-3">Foto Profil Toko</label>
                        <div class="flex items-center gap-6">
                            <!-- Clickable Avatar -->
                            <div class="relative group cursor-pointer">
                                <label for="foto" class="cursor-pointer block">
                                    <div class="w-28 h-28 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200 shadow-sm relative">
                                        @if(isset($tenant) && $tenant->foto)
                                            <img src="{{ asset('storage/' . $tenant->foto) }}" alt="Foto Tenant" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" id="photo-preview">
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 hidden" id="photo-preview-placeholder">
                                                <i class="ph-fill ph-storefront text-4xl"></i>
                                            </div>
                                        @else
                                            <img src="" class="w-full h-full object-cover hidden transition-transform duration-300 group-hover:scale-105" id="photo-preview">
                                            <div class="w-full h-full flex items-center justify-center text-gray-400" id="photo-preview-placeholder">
                                                <i class="ph-fill ph-storefront text-4xl"></i>
                                            </div>
                                        @endif
                                        
                                        <!-- Hover Overlay -->
                                        <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <i class="ph-fill ph-camera text-2xl mb-1"></i>
                                            <span class="text-[10px] font-bold uppercase tracking-wider">Ubah Foto</span>
                                        </div>
                                    </div>
                                </label>
                                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg" class="hidden" onchange="previewImage(this)">
                            </div>
                            
                            <!-- Information -->
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-900 mb-1">Upload foto baru</h4>
                                <p class="text-xs text-gray-500 font-medium mb-3">Klik gambar di samping untuk memilih foto dari perangkat Anda.</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[11px] font-medium text-gray-600">
                                    <i class="ph-fill ph-info text-gray-400"></i> JPG atau PNG. Maks 2MB. Rasio 1:1.
                                </div>
                                @error('foto') 
                                    <div class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1">
                                        <i class="ph-fill ph-warning-circle"></i> {{ $message }}
                                    </div> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-gray-100 w-full my-6"></div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-telkom-red focus:border-transparent transition-all" required>
                        @error('name') <span class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Email</label>
                        <input type="email" value="{{ $user->email }}" class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed font-medium" disabled>
                        <p class="text-[11px] text-gray-400 mt-1 font-medium">Email tidak dapat diubah.</p>
                    </div>

                    <div class="h-px bg-gray-100 w-full my-6"></div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Password Baru <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" name="password" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-telkom-red focus:border-transparent transition-all" placeholder="Masukkan password baru">
                        @error('password') <span class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-telkom-red focus:border-transparent transition-all" placeholder="Konfirmasi password baru">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-telkom-red hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="ph-bold ph-floppy-disk text-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById('photo-preview');
            var placeholder = document.getElementById('photo-preview-placeholder');
            
            img.src = e.target.result;
            img.classList.remove('hidden');
            
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
