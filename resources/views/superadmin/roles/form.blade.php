@extends('layouts.superadmin')

@section('title', isset($role) ? 'Edit Role' : 'Add New Role')
@section('breadcrumb', 'Roles / ' . (isset($role) ? 'Edit' : 'Create'))

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-800">{{ isset($role) ? 'Edit Role' : 'Create New Role' }}</h2>
    </div>
    
    <form action="{{ isset($role) ? route('superadmin.roles.update', $role->id) : route('superadmin.roles.store') }}" method="POST" class="p-6">
        @csrf
        @if(isset($role))
            @method('PUT')
        @endif

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name (lowercase, no spaces recommended)</label>
                <input type="text" name="name" id="name" value="{{ old('name', $role->name ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border" required {{ isset($role) && in_array($role->name, ['superadmin', 'pengelola', 'tenant', 'pelanggan']) ? 'readonly title="System role name cannot be changed"' : '' }}>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="description" id="description" value="{{ old('description', $role->description ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 border">
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-8 flex items-center space-x-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ isset($role) ? 'Update Role' : 'Save Role' }}
            </button>
            <a href="{{ route('superadmin.roles.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
