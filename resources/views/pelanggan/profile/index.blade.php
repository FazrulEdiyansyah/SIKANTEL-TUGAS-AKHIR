@extends('layouts.pelanggan')

@section('title', 'Profil Saya - SIKANTEL')

@section('content')
<div class="pt-24 pb-20 bg-gray-50 min-h-screen">
    <div class="max-w-[800px] mx-auto px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">Profil Saya</h1>
            <p class="text-gray-500 text-sm">Kelola informasi pribadi Anda.</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="ph-bold ph-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-8">
            <form action="{{ route('pelanggan.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-telkom-red focus:border-transparent transition-all" required>
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Email</label>
                        <input type="email" value="{{ $user->email }}" class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed" disabled>
                        <p class="text-[11px] text-gray-400 mt-1">Email tidak dapat diubah.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-telkom-red focus:border-transparent transition-all" placeholder="Contoh: 081234567890" required>
                        @error('phone_number') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Password Baru <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" name="password" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-telkom-red focus:border-transparent transition-all" placeholder="Masukkan password baru">
                        @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-telkom-red focus:border-transparent transition-all" placeholder="Konfirmasi password baru">
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="bg-telkom-red hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection
