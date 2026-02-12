@props([
    'lps' => [],
    'centrosAtivos' => [],
    'entidade',
])

@php
    $drawerId = 'conciliacao_transferencia_drawer';
    $formId = 'conciliacao_transferencia_form';
    $closeId = $drawerId . '_close';
    $submitId = $drawerId . '_submit';

    // Filtrar LPs do tipo "ambos" ou que contenham "transferência" na descrição
    $lpsTransferencia = $lps
        ->filter(
            fn($lp) => $lp->type === 'ambos' ||
                str_contains(strtolower($lp->description), 'transferência') ||
                str_contains(strtolower($lp->description), 'transferencia'),
        )
        ->sortBy('description')
        ->values();
@endphp

<x-tenant-drawer drawerId="{{ $drawerId }}" title="Nova Transferência entre Contas"
    width="{default:'100%', 'md': '550px'}" :showCloseButton="true" bodyClass="drawer-body-default" headerClass="border-bottom">

    <x-slot name="body">
        <form id="{{ $formId }}" action="{{ route('conciliacao.transferir') }}" method="POST">
            @csrf

            {{-- Campos hidden preenchidos via JS --}}
            <input type="hidden" name="bank_statement_id" id="transf_bank_statement_id">
            <input type="hidden" name="entidade_origem_id" value="{{ $entidade->id }}">
            <input type="hidden" name="checknum" id="transf_checknum">
            <input type="hidden" name="valor" id="transf_valor">
            <input type="hidden" name="data_transferencia" id="transf_data">

            {{-- Card: Dados da Transação OFX --}}
            <div class="card border border-gray-300 mb-5">
                <div class="card-header min-h-45px bg-light-primary">
                    <h3 class="card-title fs-6 fw-bold text-primary">
                        <i class="fa-solid fa-file-invoice fs-7 me-2 text-primary"></i>
                        Dados do Extrato
                    </h3>
                </div>
                <div class="card-body px-6 py-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-gray-500 fs-7">Origem:</span>
                            <span class="fw-bold text-gray-800 ms-1"
                                id="transf_info_origem">{{ $entidade->nome }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 fs-7">Data:</span>
                            <span class="fw-semibold text-gray-800 ms-1" id="transf_info_data">-</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="flex-grow-1">
                            <span class="text-gray-500 fs-7">Descrição:</span>
                            <span class="fw-semibold text-gray-700 ms-1 fs-7" id="transf_info_memo">-</span>
                        </div>
                        <div>
                            <span class="badge badge-light-danger fs-5 fw-bolder px-3 py-2" id="transf_info_valor">R$
                                0,00</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Configuração da Transferência --}}
            <div class="card border border-gray-300 mb-5">
                <div class="card-body px-6 py-5">
                    {{-- Conta de Destino --}}
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-5 required" for="transf_entidade_destino_id">
                            Conta de Destino
                        </label>
                        <select
                            class="form-select form-select-md"
                            data-control="select2"
                            data-dropdown-parent="#{{ $drawerId }}"
                            data-placeholder="Selecione a conta de destino"
                            name="entidade_destino_id"
                            id="transf_entidade_destino_id"
                            required>
                            <option value="" disabled selected>Selecione a conta de destino</option>
                            {{-- Options carregadas via AJAX --}}
                        </select>
                        <div class="text-muted fs-7 mt-2">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            As contas disponíveis serão carregadas automaticamente
                        </div>
                    </div>

                    {{-- Descrição (opcional) --}}
                    <div>
                        <label class="form-label fw-semibold fs-5" for="transf_descricao">
                            Descrição <span class="text-muted fs-7">(opcional)</span>
                        </label>
                        <textarea id="transf_descricao" name="descricao" class="form-control form-control-md" rows="2"
                            placeholder="Ex: Aplicação automática Rende Fácil"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <div class="d-flex justify-content-between w-100">
            <x-tenant-button type="button" color="light" :id="$closeId . '_cancel'">
                <i class="fa-solid fa-xmark me-1"></i> Cancelar
            </x-tenant-button>

            <x-tenant-button type="button" color="primary" :id="$submitId">
                <i class="fa-solid fa-arrow-right-arrow-left me-1"></i> Transferir
            </x-tenant-button>
        </div>
    </x-slot>
</x-tenant-drawer>
