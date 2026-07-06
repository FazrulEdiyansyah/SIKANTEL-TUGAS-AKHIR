@if ($paginator->hasPages())
    <div class="flex items-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 border border-gray-200 rounded-lg text-gray-400 font-medium cursor-not-allowed flex items-center gap-1 text-sm bg-gray-50/50">
                <i class="ph-bold ph-caret-left"></i>
                <span>Prev</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 border border-gray-200 rounded-lg text-blue-500 font-medium hover:bg-gray-50 transition-colors flex items-center gap-1 text-sm bg-white">
                <i class="ph-bold ph-caret-left"></i>
                <span>Prev</span>
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 border border-gray-200 rounded-lg text-blue-500 font-medium hover:bg-gray-50 transition-colors flex items-center gap-1 text-sm bg-white">
                <span>Next</span>
                <i class="ph-bold ph-caret-right"></i>
            </a>
        @else
            <span class="px-3 py-2 border border-gray-200 rounded-lg text-gray-400 font-medium cursor-not-allowed flex items-center gap-1 text-sm bg-gray-50/50">
                <span>Next</span>
                <i class="ph-bold ph-caret-right"></i>
            </span>
        @endif
    </div>
@endif
