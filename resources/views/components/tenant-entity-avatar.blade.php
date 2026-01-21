@props([
    'entidade',
    'size' => '70px', // Tamanho do símbolo
])

@php
    $sizeClass = "symbol-{$size}";
@endphp

<!--begin::Symbol-->
<div class="symbol {{ $sizeClass }} me-5">
    @if ($entidade->tipo === 'banco')
        {{-- Exibir logo do banco --}}
        @if ($entidade->bank && $entidade->bank->logo_path)
            {{-- Usa o caminho do logo salvo no banco de dados --}}
            <img src="{{ $entidade->bank->logo_path }}"
                alt="{{ $entidade->bank->name ?? 'Banco' }}"
                class="p-3" />
        @else
            {{-- Fallback: Mostra um ícone genérico de banco se não houver logo --}}
            <span class="symbol-label bg-light-primary">
                <span class="svg-icon svg-icon-3x svg-icon-primary">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 14H18V10H20V14ZM10 14H8V10H10V14ZM15 14H13V10H15V14Z" fill="currentColor" />
                        <path opacity="0.3" d="M22 18V6C22 5.4 21.6 5 21 5H3C2.4 5 2 5.4 2 6V18C2 18.6 2.4 19 3 19H21C21.6 19 22 18.6 22 18ZM5 14H7V10H5V14ZM12 14H10V10H12V14ZM17 14H15V10H17V14Z" fill="currentColor" />
                    </svg>
                </span>
            </span>
        @endif
    @else
        {{-- Ícone de caixa (cofre) --}}
        <span class="symbol-label rounded-circle">
            <img src="/assets/media/icons/png/porco.png"
                 alt="Caixa"
                 class="w-100 h-100"
                 style="object-fit: cover;" />
        </span>
    @endif
</div>
<!--end::Symbol-->

