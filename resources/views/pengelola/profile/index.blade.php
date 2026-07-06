@extends('layouts.dashboard')

@section('title', 'Profil Saya - SIKANTEL')

@section('sidebar_menu')
    <x-sidebar.pengelola active="" />
@endsection

@section('content')
<div class="pt-4 pb-20">
    <div class="max-w-[800px] mx-auto px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">Profil Saya</h1>
            <p class="text-gray-500 text-sm">Kelola informasi pribadi akun Anda.</p>
        </div>

        <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-8">
            <form action="{{ route('pengelola.profile.update') }}" method="POST">
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
