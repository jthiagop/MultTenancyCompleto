@php
    $viewAction = $viewAction ?? "abrirDrawerTransacao({$transacao->id}); return false;";
    $editAction = $editAction ?? "abrirDrawerEdicao({$transacao->id}); return false;";
    $deleteAction = $deleteAction ?? 'data-kt-transacao-table-filter="delete_row" data-transacao-id="' . $transacao->id . '"';
    $informarPagamentoAction = $informarPagamentoAction ?? "informarPagamento({$transacao->id}); return false;";
    $viewLabel = $viewLabel ?? 'Visualizar';
    $editLabel = $editLabel ?? 'Editar';
    $deleteLabel = $deleteLabel ?? 'Excluir';
    $informarPagamentoLabel = $informarPagamentoLabel ?? 'Informar pagamento';
    $showInformarPagamento = $showInformarPagamento ?? true;
    $menuWidth = $menuWidth ?? 'w-200px';
    $menuId = 'kt_menu_' . $transacao->id;
    
    // Determinar status pago/recebido e em_aberto
    $isPago = $isPago ?? false;
    $isEmAberto = $isEmAberto ?? false;
    $tipoTransacao = $tipoTransacao ?? $transacao->tipo;
    $labelDefinirPago = $tipoTransacao === 'entrada' ? 'Definir como Recebido' : 'Definir como Pago';
    $labelDefinirAberto = 'Definir como em Aberto';
    $labelInverterTipo = $tipoTransacao === 'entrada' ? 'Converter para Despesa' : 'Converter para Receita';
@endphp

<div class="text-end">
    <a href="#" class="btn btn-light-primary btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-attach="parent">Ações
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
        <span class="svg-icon svg-icon-5 m-0">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
            </svg>
        </span>
        <!--end::Svg Icon-->
    </a>
    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 {{ $menuWidth }} py-4" data-kt-menu="true" id="{{ $menuId }}">
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="#" onclick="{{ $viewAction }}" class="menu-link px-3">
                {{ $viewLabel }}
            </a>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="#" onclick="{{ $editAction }}" class="menu-link px-3">
                {{ $editLabel }}
            </a>
        </div>
        <!--end::Menu item-->
        @if($showInformarPagamento)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="#" onclick="{{ $informarPagamentoAction }}" class="menu-link px-3">
                {{ $informarPagamentoLabel }}
            </a>
        </div>
        <!--end::Menu item-->
        @endif
        @if($isPago)
        <!--begin::Menu item - Definir como Em Aberto (apenas se está pago/recebido)-->
        <div class="menu-item px-3">
            <a href="#" onclick="definirComoAberto({{ $transacao->id }}); return false;" class="menu-link px-3">
                {{ $labelDefinirAberto }}
            </a>
        </div>
        <!--end::Menu item-->
        @elseif(!$isEmAberto)
        <!--begin::Menu item - Definir como Pago/Recebido (apenas se NÃO está em_aberto)-->
        <div class="menu-item px-3">
            <a href="#" onclick="definirComoPago({{ $transacao->id }}); return false;" class="menu-link px-3">
                {{ $labelDefinirPago }}
            </a>
        </div>
        <!--end::Menu item-->
        @endif
        <!--begin::Menu item - Inverter tipo-->
        <div class="menu-item px-3">
            <a href="#" onclick="inverterTipoTransacao({{ $transacao->id }}); return false;" class="menu-link px-3">
                {{ $labelInverterTipo }}
            </a>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            @if($transacao->recorrencia_id)
                {{-- Transação recorrente: abre modal especial --}}
                <a href="#" 
                   onclick="openDeleteRecurrenceModal({{ $transacao->id }}); return false;" 
                   class="menu-link px-3 text-danger">
                    {{ $deleteLabel }}
                </a>
            @else
                {{-- Transação normal: usa função de exclusão direta --}}
                <a href="#" onclick="excluirTransacaoDirecta('{{ route('transacoes-financeiras.destroy', $transacao) }}'); return false;" class="menu-link px-3 text-danger">
                    {{ $deleteLabel }}
                </a>
            @endif
        </div>
        <!--end::Menu item-->
    </div>
    <!--end::Menu-->
</div>

