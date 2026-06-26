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
    
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('superadmin.users.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white outline-none transition-shadow hover:shadow-sm">
            </div>
            
            <!-- Role -->
            <div class="relative min-w-[150px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-regular fa-user"></i>
                </div>
                <select name="role" class="w-full pl-10 pr-8 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-shadow hover:shadow-sm" onchange="this.form.submit()">
                    <option value="all"> Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- Status -->
            <div class="relative min-w-[150px]">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-award"></i>
                </div>
                <select name="status" class="w-full pl-10 pr-8 py-2 border border-gray-300 rounded-full text-sm focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-shadow hover:shadow-sm" onchange="this.form.submit()">
                    <option value="all">Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <button type="submit" class="hidden">Filter</button>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-gray-100">ID</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Name</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Email</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Role</th>
                    <th class="px-6 py-4 font-medium border-b border-gray-100">Status</th>
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
                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-check-circle mr-1"></i> Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fa-solid fa-times-circle mr-1"></i> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('superadmin.users.edit', $user->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-circle-info mr-1.5"></i> Detail
                        </a>
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
