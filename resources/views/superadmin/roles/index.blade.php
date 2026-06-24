@extends('layouts.superadmin')

@section('title', 'Roles Management')
@section('breadcrumb', 'Roles')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800">All Roles</h2>
        <a href="{{ route('superadmin.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Add New Role
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Name</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Description</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($roles as $role)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $role->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ ucfirst($role->name) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $role->description ?? '-' }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('superadmin.roles.edit', $role->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors p-1" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        @if(!in_array($role->name, ['superadmin', 'pengelola', 'tenant', 'pelanggan']))
                        <form action="{{ route('superadmin.roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini? Semua user dengan role ini akan dikembalikan ke role pelanggan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No roles found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
