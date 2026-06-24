@extends('layouts.superadmin')

@section('title', 'Users Management')
@section('breadcrumb', 'Users')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800">All Users</h2>
        <a href="{{ route('superadmin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Add New User
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Name</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Email</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Role</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Joined Date</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $user->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        {{ $user->name }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php
                            $roleColors = [
                                'superadmin' => 'bg-red-100 text-red-700',
                                'pengelola' => 'bg-purple-100 text-purple-700',
                                'tenant' => 'bg-green-100 text-green-700',
                                'pelanggan' => 'bg-blue-100 text-blue-700',
                            ];
                            $colorClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('superadmin.users.edit', $user->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors p-1" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
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
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
