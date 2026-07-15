@extends('layouts.superadmin')

@section('title', isset($user) ? 'Edit User' : 'Add New User')
@section('breadcrumb', 'Users / ' . (isset($user) ? 'Edit' : 'Create'))

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h2>
    </div>
    
    <form action="{{ isset($user) ? route('superadmin.users.update', $user->id) : route('superadmin.users.store') }}" method="POST" class="p-6">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border" required>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border" required>
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            @if(isset($user))
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
                <select name="is_active" id="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border" required>
                    <option value="1" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $user->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('is_active') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @else
            <div class="bg-blue-50 p-3 rounded-md text-sm text-blue-700 mb-4 border border-blue-100">
                <i class="fa-solid fa-info-circle mr-2"></i> Password default untuk user baru adalah <strong>password</strong>
            </div>
            @endif

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border" required>
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role', $user->role ?? '') == $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-8 flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center space-x-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    {{ isset($user) ? 'Update User' : 'Save User' }}
                </button>
                <a href="{{ route('superadmin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </form>
    
    @if(isset($user) && $user->id !== auth()->id())
    <div class="px-6 pb-6 mt-[-1rem]">
        <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="confirmFormSubmit(event, 'Apakah Anda yakin ingin menghapus user ini secara permanen?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                <i class="fa-solid fa-trash mr-2"></i> Hapus User
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
