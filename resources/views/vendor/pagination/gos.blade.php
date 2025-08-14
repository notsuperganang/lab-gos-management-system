@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="mt-10 flex justify-center">
        <div class="inline-flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center rounded-full px-3 py-2 text-sm font-medium bg-white text-gray-400 ring-1 ring-gray-200 opacity-50 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="inline-flex items-center justify-center rounded-full px-3 py-2 text-sm font-medium transition shadow-sm ring-1 ring-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:ring-gray-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-medium bg-white text-gray-500 ring-1 ring-gray-200">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-medium bg-blue-600 text-white ring-1 ring-blue-600 shadow">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" 
                               class="inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-medium transition shadow-sm ring-1 ring-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:ring-gray-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="inline-flex items-center justify-center rounded-full px-3 py-2 text-sm font-medium transition shadow-sm ring-1 ring-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:ring-gray-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sr-only">Next</span>
                </a>
            @else
                <span class="inline-flex items-center justify-center rounded-full px-3 py-2 text-sm font-medium bg-white text-gray-400 ring-1 ring-gray-200 opacity-50 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            @endif
        </div>
    </nav>
@endif