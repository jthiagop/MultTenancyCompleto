@props([
    'tableId' => null,
    'markAsPaidRoute' => null,
    'markAsOpenRoute' => null,
    'deleteRoute' => null,
    'reverseTypeRoute' => null,
    'markAsPaidLabel' => 'Definir como pago',
    'markAsOpenLabel' => 'Definir como em aberto',
    'deleteLabel' => 'Excluir',
    'reverseTypeLabel' => 'Inverter Receita ↔ Despesa)',
    'menuWidth' => 'w-200px',
    'id' => null,
])

@php
    // ID único para o menu se fornecido
    $menuId = $id ? 'kt-menu-batch-' . $id : ($tableId ? 'kt-menu-batch-' . $tableId : null);
    $buttonId = $tableId ? 'batch-actions-btn-' . $tableId : null;
    
    // Rotas padrão se não fornecidas
    $markAsPaidRoute = $markAsPaidRoute ?? route('banco.batch-mark-as-paid');
    $markAsOpenRoute = $markAsOpenRoute ?? route('banco.batch-mark-as-open');
    $deleteRoute = $deleteRoute ?? route('banco.batch-delete');
    $reverseTypeRoute = $reverseTypeRoute ?? route('banco.batch-reverse-type');
@endphp

<!--begin::Dropdown-->
<div class="dropdown">
    <button class="btn btn-light btn-sm dropdown-toggle" 
            type="button" 
            id="{{ $buttonId }}"
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            style="pointer-events: none; opacity: 0.65; cursor: not-allowed;">
        Ações em lote
    </button>
    <ul class="dropdown-menu {{ $menuWidth }}" id="{{ $menuId }}">
        @if($markAsPaidRoute)
        <!--begin::Menu item-->
        <li>
            <a class="dropdown-item" 
               href="#" 
               data-batch-action="markAsPaid"
               data-table-id="{{ $tableId }}"
               data-mark-as-paid-route="{{ $markAsPaidRoute }}">
                {{ $markAsPaidLabel }}
            </a>
        </li>
        <!--end::Menu item-->
        @endif

        @if($markAsOpenRoute)
        <!--begin::Menu item-->
        <li>
            <a class="dropdown-item" 
               href="#" 
               data-batch-action="markAsOpen"
               data-table-id="{{ $tableId }}"
               data-mark-as-open-route="{{ $markAsOpenRoute }}">
                {{ $markAsOpenLabel }}
            </a>
        </li>
        <!--end::Menu item-->
        @endif

        @if($deleteRoute)
        <!--begin::Menu item-->
        <li>
            <a class="dropdown-item text-danger" 
               href="#" 
               data-batch-action="delete"
               data-table-id="{{ $tableId }}"
               data-delete-route="{{ $deleteRoute }}">
                {{ $deleteLabel }}
            </a>
        </li>
        <!--end::Menu item-->
        @endif

        @if($reverseTypeRoute)
        <!--begin::Menu item-->
        <li>
            <a class="dropdown-item text-warning" 
               href="#" 
               data-batch-action="reverseType"
               data-table-id="{{ $tableId }}"
               data-reverse-type-route="{{ $reverseTypeRoute }}">
                <i class="fa-solid fa-arrows-rotate me-2"></i>{{ $reverseTypeLabel }}
            </a>
        </li>
        <!--end::Menu item-->
        @endif

        {{ $slot }}
    </ul>
</div>
<!--end::Dropdown-->

