@extends('layouts.app')

@section('title', 'Login - SIKANTEL')

@section('content')
<div class="min-h-screen flex">
    
    <!-- Kolom Kiri: Banner Merah (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 relative items-end justify-start p-16 overflow-hidden bg-telkom-red">
        
        <!-- Background Warna Dasar Merah (Gradient) -->
        <div class="absolute inset-0 bg-gradient-to-b from-telkom-red to-telkom-maroon"></div>

        <!-- Background Image (Foto Kantin Asli) -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/foto-kantin.png') }}" 
                 alt="Kantin Background" 
                 class="w-full h-full object-cover mix-blend-overlay opacity-50 grayscale">
        </div>
        
        <!-- Teks di Kolom Kiri -->
        <div class="relative z-10 text-white">
            <h1 class="text-[40px] font-bold leading-tight tracking-wide">
                Selamat datang di SIKANTEL<br>
                Layanan kantin Tel-U<br>
                dalam satu sistem.
            </h1>
        </div>
    </div>

    <!-- Kolom Kanan: Form Login -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-20 bg-white">
        <div class="w-full max-w-[450px]">
            
            <!-- Logo SIKANTEL Asli -->
            <div class="mb-10 flex items-center">
                <img src="{{ asset('images/logo-sikantel.png') }}" alt="Logo SIKANTEL" class="h-14 object-contain">
            </div>

            <!-- Heading -->
            <h2 class="text-4xl font-bold text-gray-900 mb-3 tracking-tight">Selamat Datang</h2>
            <p class="text-gray-500 text-[17px] leading-relaxed mb-10 pr-4">
                Masuk untuk mengakses layanan kantin Universitas Telkom
            </p>

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Email Input -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-gray-800">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-5 py-3.5 rounded-xl border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500/10 focus:border-red-500' : 'border-gray-300 focus:ring-telkom-red/10 focus:border-telkom-red' }} outline-none transition-all placeholder:text-gray-400 font-medium"
                        placeholder="Masukkan email Anda" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-2 relative">
                    <label for="password" class="block text-sm font-bold text-gray-800">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" 
                            class="w-full px-5 py-3.5 rounded-xl border {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500/10 focus:border-red-500' : 'border-gray-300 focus:ring-telkom-red/10 focus:border-telkom-red' }} outline-none transition-all placeholder:text-gray-400 font-medium"
                            placeholder="Masukkan password Anda" required>
                        <button type="button" onclick="togglePassword('password', 'eye-icon-login')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon-login" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-telkom-red focus:ring-telkom-red border-gray-300 rounded cursor-pointer">
                        <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer select-none">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <!-- Tombol Masuk -->
                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-telkom-red hover:bg-telkom-maroon text-white font-bold text-lg py-4 rounded-xl transition-all shadow-lg shadow-telkom-red/30 hover:shadow-xl hover:-translate-y-0.5">
                        Masuk
                    </button>
                </div>
            </form>

            <!-- Link Daftar -->
            <p class="mt-10 text-center text-gray-500 font-medium">
                Belum punya akun? 
                <a href="/register" class="font-bold text-telkom-red hover:text-telkom-maroon transition-colors">Daftar</a>
            </p>

        </div>
    </div>

</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
        }
    }
</script>
@endsection
