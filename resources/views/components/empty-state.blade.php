{{-- 
    Empty State Component
    Usage: <x-empty-state icon="ph ph-users" title="Data tidak ditemukan" message="Belum ada data tenant." :colspan="7" />
--}}

@props([
    'icon' => 'ph ph-database',
    'title' => 'Belum ada data',
    'message' => 'Data yang Anda cari tidak ditemukan.',
    'colspan' => 6
])

<tr>
    <td colspan="{{ $colspan }}" class="py-16 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <i class="{{ $icon }} text-2xl text-gray-300"></i>
            </div>
            <p class="text-base font-bold text-gray-900 mb-1">{{ $title }}</p>
            <p class="text-sm font-medium text-gray-500">{{ $message }}</p>
        </div>
    </td>
</tr>
