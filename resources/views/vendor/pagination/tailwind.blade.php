@if ($paginator->hasPages())
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm border-t border-gray-50 pt-6">
        {{-- Information part (Left Side) --}}
        <div class="text-gray-500 font-medium">
            Menampilkan <span class="font-bold text-gray-900">{{ $paginator->firstItem() }}</span> - 
            <span class="font-bold text-gray-900">{{ $paginator->lastItem() }}</span> dari 
            <span class="font-bold text-gray-900">{{ $paginator->total() }}</span> Data
        </div>

        {{-- Pagination Links (Right Side) --}}
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

            <div class="flex items-center gap-1">
                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-1 text-gray-400">...</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-9 h-9 rounded-xl text-sm font-bold bg-telkom-red text-white flex items-center justify-center shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors flex items-center justify-center">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

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
    </div>
@endif
