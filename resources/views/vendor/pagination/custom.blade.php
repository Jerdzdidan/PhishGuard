@if ($paginator->hasPages())
    <ul class="pagination mb-0 pagination-rounded">
        {{-- First Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item first disabled">
                <span class="page-link"><i class="icon-base bx bx-chevrons-left icon-sm scaleX-n1-rtl"></i></span>
            </li>
        @else
            <li class="page-item first">
                <a class="page-link" href="{{ $paginator->url(1) }}"><i class="icon-base bx bx-chevrons-left icon-sm scaleX-n1-rtl"></i></a>
            </li>
        @endif

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item prev disabled">
                <span class="page-link"><i class="icon-base bx bx-chevron-left icon-sm scaleX-n1-rtl"></i></span>
            </li>
        @else
            <li class="page-item prev">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}"><i class="icon-base bx bx-chevron-left icon-sm scaleX-n1-rtl"></i></a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item next">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}"><i class="icon-base bx bx-chevron-right icon-sm scaleX-n1-rtl"></i></a>
            </li>
        @else
            <li class="page-item next disabled">
                <span class="page-link"><i class="icon-base bx bx-chevron-right icon-sm scaleX-n1-rtl"></i></span>
            </li>
        @endif

        {{-- Last Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item last">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}"><i class="icon-base bx bx-chevrons-right icon-sm scaleX-n1-rtl"></i></a>
            </li>
        @else
            <li class="page-item last disabled">
                <span class="page-link"><i class="icon-base bx bx-chevrons-right icon-sm scaleX-n1-rtl"></i></span>
            </li>
        @endif
    </ul>
@endif