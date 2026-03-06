<div class="flex gap-1.5">
    {{-- Nút Quay Lại --}}
    @if ($paginator->onFirstPage())
        <button class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 bg-gray-50 flex items-center justify-center cursor-not-allowed opacity-50" disabled><i class="fa-solid fa-chevron-left text-xs"></i></button>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-forest-700 flex items-center justify-center transition-colors"><i class="fa-solid fa-chevron-left text-xs"></i></a>
    @endif

    {{-- Các số trang --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 flex items-center justify-center text-sm font-medium">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="w-8 h-8 rounded-lg bg-forest-700 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-forest-500/20">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-forest-700 flex items-center justify-center text-sm font-medium transition-colors">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Nút Tiếp Theo --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-forest-700 flex items-center justify-center transition-colors"><i class="fa-solid fa-chevron-right text-xs"></i></a>
    @else
        <button class="w-8 h-8 rounded-lg border border-gray-200 text-gray-400 bg-gray-50 flex items-center justify-center cursor-not-allowed opacity-50" disabled><i class="fa-solid fa-chevron-right text-xs"></i></button>
    @endif
</div>
