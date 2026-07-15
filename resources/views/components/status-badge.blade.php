{{-- 
    Status Badge Component
    Usage: <x-status-badge status="aktif" />
    Usage: <x-status-badge status="draft" />
    Usage: <x-status-badge status="proposed" label="Diproses (Kaur)" />
--}}

@props([
    'status',
    'label' => null
])

@php
    $config = match($status) {
        'aktif', 'tersedia', 'success' => [
            'bg' => 'bg-green-50', 
            'text' => 'text-green-600', 
            'border' => 'border-green-100', 
            'dot' => 'bg-green-500',
            'label' => $label ?? 'Aktif',
        ],
        'nonaktif', 'habis' => [
            'bg' => 'bg-gray-50', 
            'text' => 'text-gray-500', 
            'border' => 'border-gray-200', 
            'dot' => 'bg-gray-400',
            'label' => $label ?? 'Nonaktif',
        ],
        'draft' => [
            'bg' => 'bg-yellow-50', 
            'text' => 'text-yellow-600', 
            'border' => 'border-yellow-200', 
            'dot' => 'bg-yellow-500',
            'label' => $label ?? 'Dibuat',
        ],
        'proposed' => [
            'bg' => 'bg-blue-50', 
            'text' => 'text-blue-600', 
            'border' => 'border-blue-200', 
            'dot' => 'bg-blue-500',
            'label' => $label ?? 'Diproses (Kaur)',
        ],
        'approved_kaur' => [
            'bg' => 'bg-indigo-50', 
            'text' => 'text-indigo-600', 
            'border' => 'border-indigo-200', 
            'dot' => 'bg-indigo-500',
            'label' => $label ?? 'Diproses (Kabag)',
        ],
        'approved' => [
            'bg' => 'bg-green-50', 
            'text' => 'text-green-600', 
            'border' => 'border-green-200', 
            'dot' => 'bg-green-500',
            'label' => $label ?? 'Selesai',
        ],
        'rejected_kaur' => [
            'bg' => 'bg-red-50', 
            'text' => 'text-red-600', 
            'border' => 'border-red-200', 
            'dot' => 'bg-red-500',
            'label' => $label ?? 'Ditolak (Kaur)',
        ],
        'rejected_kabag' => [
            'bg' => 'bg-red-50', 
            'text' => 'text-red-600', 
            'border' => 'border-red-200', 
            'dot' => 'bg-red-500',
            'label' => $label ?? 'Ditolak (Kabag)',
        ],
        'pending', 'menunggu' => [
            'bg' => 'bg-yellow-50', 
            'text' => 'text-yellow-600', 
            'border' => 'border-yellow-200', 
            'dot' => 'bg-yellow-500',
            'label' => $label ?? 'Menunggu',
        ],
        'diproses' => [
            'bg' => 'bg-blue-50', 
            'text' => 'text-blue-600', 
            'border' => 'border-blue-200', 
            'dot' => 'bg-blue-500',
            'label' => $label ?? 'Diproses',
        ],
        'selesai' => [
            'bg' => 'bg-green-50', 
            'text' => 'text-green-600', 
            'border' => 'border-green-200', 
            'dot' => 'bg-green-500',
            'label' => $label ?? 'Selesai',
        ],
        'belum_diproses' => [
            'bg' => 'bg-orange-50',
            'text' => 'text-orange-600',
            'border' => 'border-orange-200',
            'dot' => 'bg-orange-500',
            'label' => $label ?? 'Belum Diproses',
        ],
        'siap_diambil' => [
            'bg' => 'bg-teal-50',
            'text' => 'text-teal-600',
            'border' => 'border-teal-200',
            'dot' => 'bg-teal-500',
            'label' => $label ?? 'Siap Diambil',
        ],
        'dibatalkan', 'cancelled', 'failed' => [
            'bg' => 'bg-red-50', 
            'text' => 'text-red-600', 
            'border' => 'border-red-200', 
            'dot' => 'bg-red-500',
            'label' => $label ?? 'Dibatalkan',
        ],
        default => [
            'bg' => 'bg-gray-50', 
            'text' => 'text-gray-600', 
            'border' => 'border-gray-200', 
            'dot' => 'bg-gray-400',
            'label' => $label ?? ucfirst($status),
        ],
    };
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-bold {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }} whitespace-nowrap">
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }} mr-1.5 shrink-0"></span>
    {{ $config['label'] }}
</span>
