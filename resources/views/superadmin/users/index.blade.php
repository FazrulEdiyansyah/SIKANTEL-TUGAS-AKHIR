@extends('layouts.superadmin')

@section('title', 'Manajemen Pengguna')
@section('breadcrumb', 'Pengguna')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Data Pengguna</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua data pengguna sistem.</p>
        </div>
        <a href="{{ route('superadmin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center">
            <i class="ph ph-plus font-bold mr-2"></i> Tambah Pengguna
        </a>
    </div>
    
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('superadmin.users.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white outline-none transition-all">
            </div>
            
            <!-- Role -->
            <div class="relative min-w-[150px]">
                <select name="role" class="w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-all" onchange="this.form.submit()">
                    <option value="all">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>

            <!-- Status -->
            <div class="relative min-w-[150px]">
                <select name="status" class="w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white appearance-none cursor-pointer outline-none transition-all" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>

            <!-- Reset -->
            <a href="{{ route('superadmin.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors shrink-0" title="Reset Filter">
                <i class="ph ph-arrows-clockwise text-lg"></i>
            </a>

            <button type="submit" class="hidden">Filter</button>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Tgl. Bergabung</th>
                    <th class="py-4 px-6 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $index => $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $users->firstItem() + $index }}</td>
                    <td class="py-4 px-6 font-bold text-[14px] text-gray-800">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3 shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            {{ $user->name }}
                        </div>
                    </td>
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-600">{{ $user->email }}</td>
                    <td class="py-4 px-6">
                        @php
                            $roleColors = [
                                'superadmin' => 'bg-red-50 text-red-600 border-red-100',
                                'pengelola' => 'bg-purple-50 text-purple-600 border-purple-100',
                                'tenant' => 'bg-green-50 text-green-600 border-green-100',
                                'pelanggan' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'kaur' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'kabag' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                            ];
                            $colorClass = $roleColors[$user->role] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-bold {{ $colorClass }} border">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <x-status-badge :status="$user->is_active ? 'aktif' : 'nonaktif'" />
                    </td>
                    <td class="py-4 px-6 text-[14px] font-medium text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('superadmin.users.edit', $user->id) }}" class="inline-flex items-center px-3 py-1.5 text-[13px] font-bold text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="ph ph-eye mr-1.5"></i> Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <x-empty-state icon="ph ph-users" title="Belum ada data pengguna" message="Silakan tambah pengguna baru." :colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-5 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
