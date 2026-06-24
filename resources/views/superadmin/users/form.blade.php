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

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password {!! isset($user) ? '<span class="text-gray-400 font-normal">(Leave blank to keep current)</span>' : '' !!}</label>
                <input type="password" name="password" id="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border" {{ isset($user) ? '' : 'required' }}>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border" required>
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $user->role ?? '') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-8 flex items-center space-x-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ isset($user) ? 'Update User' : 'Save User' }}
            </button>
            <a href="{{ route('superadmin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
