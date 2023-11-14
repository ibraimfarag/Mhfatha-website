@if ($paginator->lastPage() > 1)
    <div id="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="blocks disabled">&laquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="blocks" rel="prev">&laquo;</a>
        @endif

        {{-- Pagination Elements --}}
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <a href="{{ $paginator->url($i) . '&lang=' . app()->getLocale() }}" class="blocks {{ $i === $paginator->currentPage() ? 'active' : '' }}">{{ $i }}</a>
        @endfor

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() . '&lang=' . app()->getLocale() }}" class="blocks" rel="next">&raquo;</a>
        @else
            <span class="blocks disabled">&raquo;</span>
        @endif
    </div>
@endif
