@if ($paginator->hasPages())
    <ul class="pagination pagination-circle pagination-outline">
        {{-- Botão Primeira Página --}}
        @if ($paginator->onFirstPage())
            <li class="page-item first disabled m-1">
                <a href="#" class="page-link px-0" aria-disabled="true" aria-label="Primeira Página">
                    <i class="ki-duotone ki-double-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </li>
        @else
            <li class="page-item first m-1">
                <a href="{{ $paginator->url(1) }}" class="page-link px-0" aria-label="Primeira Página">
                    <i class="ki-duotone ki-double-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </li>
        @endif

        {{-- Botão Página Anterior --}}
        @if ($paginator->onFirstPage())
            <li class="page-item previous disabled m-1">
                <a href="#" class="page-link px-0" aria-disabled="true" aria-label="Página Anterior">
                    <i class="ki-duotone ki-left fs-2"></i>
                </a>
            </li>
        @else
            <li class="page-item previous m-1">
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link px-0" aria-label="Página Anterior">
                    <i class="ki-duotone ki-left fs-2"></i>
                </a>
            </li>
        @endif

        {{-- Links das Páginas --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled m-1" aria-disabled="true">
                    <a href="#" class="page-link">{{ $element }}</a>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active m-1" aria-current="page">
                            <a href="#" class="page-link">{{ $page }}</a>
                        </li>
                    @else
                        <li class="page-item m-1">
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Botão Próxima Página --}}
        @if ($paginator->hasMorePages())
            <li class="page-item next m-1">
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link px-0" aria-label="Próxima Página">
                    <i class="ki-duotone ki-right fs-2"></i>
                </a>
            </li>
        @else
            <li class="page-item next disabled m-1">
                <a href="#" class="page-link px-0" aria-disabled="true" aria-label="Próxima Página">
                    <i class="ki-duotone ki-right fs-2"></i>
                </a>
            </li>
        @endif

        {{-- Botão Última Página --}}
        @if ($paginator->hasMorePages())
            <li class="page-item last m-1">
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-link px-0" aria-label="Última Página">
                    <i class="ki-duotone ki-double-right fs-2"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </li>
        @else
            <li class="page-item last disabled m-1">
                <a href="#" class="page-link px-0" aria-disabled="true" aria-label="Última Página">
                    <i class="ki-duotone ki-double-right fs-2"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </li>
        @endif
    </ul>
@endif
