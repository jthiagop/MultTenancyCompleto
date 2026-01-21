@props([
    'viewRoute' => null,
    'viewAction' => null,
    'editRoute' => null,
    'editAction' => null,
    'deleteAction' => null,
    'deleteDataAttribute' => null,
    'deleteDataValue' => null,
    'viewLabel' => 'Visualizar',
    'editLabel' => 'Editar',
    'deleteLabel' => 'Excluir',
    'menuWidth' => 'w-125px',
    'id' => null,
])

@php
    // Gerar atributos para o menu de ações
    $viewHref = $viewRoute ?? ($viewAction ? '#' : null);
    $editHref = $editRoute ?? ($editAction ? '#' : null);
    $deleteHref = $deleteAction ? '#' : null;

    // Atributos para delete
    $deleteAttributes = '';
    if ($deleteDataAttribute) {
        $deleteAttributes = 'data-' . $deleteDataAttribute;
        if ($deleteDataValue !== null) {
            $deleteAttributes .= '="' . e($deleteDataValue) . '"';
        }
    }

    // ID único para o menu se fornecido
    $menuId = $id ? 'kt-menu-' . $id : null;
@endphp

<!--begin::Action-->
<td class="text-end">
    <a href="#" class="btn btn-light btn-active-light-primary  btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Ações
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
        <span class="svg-icon svg-icon-5 m-0">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
            </svg>
        </span>
        <!--end::Svg Icon-->
    </a>
    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 {{ $menuWidth }} py-4" data-kt-menu="true" @if($menuId) id="{{ $menuId }}" @endif>
        @if($viewRoute || $viewAction)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            @if($viewAction)
                <a href="#" onclick="{{ $viewAction }}; return false;" class="menu-link px-3">{{ $viewLabel }}</a>
            @else
                <a href="{{ $viewHref }}" class="menu-link px-3">{{ $viewLabel }}</a>
            @endif
        </div>
        <!--end::Menu item-->
        @endif

        @if($editRoute || $editAction)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            @if($editAction)
                <a href="#" onclick="{{ $editAction }}; return false;" class="menu-link px-3">{{ $editLabel }}</a>
            @else
                <a href="{{ $editHref }}" class="menu-link px-3">{{ $editLabel }}</a>
            @endif
        </div>
        <!--end::Menu item-->
        @endif

        @if($deleteAction || $deleteDataAttribute)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="#" @if($deleteDataAttribute) {{ $deleteAttributes }} @endif class="menu-link px-3">{{ $deleteLabel }}</a>
        </div>
        <!--end::Menu item-->
        @endif

        {{ $slot }}
    </div>
    <!--end::Menu-->
</td>
<!--end::Action-->

