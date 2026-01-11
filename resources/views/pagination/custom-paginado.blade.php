@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between align-items-center w-100">
        {{-- Texto de resultados --}}
        <div class="flex-grow-1">
            <p class="text-muted mb-0">
                Mostrando 
                <span class="fw-bold">{{ $paginator->firstItem() }}</span>
                a
                <span class="fw-bold">{{ $paginator->lastItem() }}</span>
                de
                <span class="fw-bold">{{ $paginator->total() }}</span>
                resultados
            </p>
        </div>

        {{-- Controles de navegación --}}
        <ul class="pagination pagination-lg mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0" href="javascript:void(0)" aria-hidden="true">&lsaquo;</a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0 text-dark" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0" href="javascript:void(0)">{{ $element }}</a></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0" href="javascript:void(0)">{{ $page }}</a></li>
                        @else
                            <li class="page-item"><a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0 text-dark" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-0 shadow-sm border-top-0 border-start-0 text-dark" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <a class="page-link rounded-0 shadow-sm border-top-0 border-start-0" href="javascript:void(0)" aria-hidden="true">&rsaquo;</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
