@php
    $key = 'repasses';
    $parentId = 'kt_tab_contas';
    $tipoRepasse = request('tipo_repasse', 'a_pagar'); // a_pagar ou a_receber
    $tipo = $tipoRepasse === 'a_receber' ? 'entrada' : 'saida';

    // Labels dinâmicos baseados no tipo de repasse
    $labelPagoRecebido = $tipoRepasse === 'a_receber' ? 'Recebidos (R$)' : 'Pagos (R$)';
    $labelAPagar = $tipoRepasse === 'a_receber' ? 'A receber (R$)' : 'A pagar (R$)';

    // Stats para as sub-tabs
    $stats = [
        ['key' => 'atrasados', 'label' => 'Atrasados (R$)', 'value' => '0,00', 'variant' => 'danger'],
        ['key' => 'a_pagar', 'label' => $labelAPagar, 'value' => '0,00', 'variant' => 'primary'],
        ['key' => 'pagos', 'label' => $labelPagoRecebido, 'value' => '0,00', 'variant' => 'success'],
        ['key' => 'total', 'label' => 'Total do período (R$)', 'value' => '0,00', 'variant' => 'primary'],
    ];

    // Colunas da tabela
    $tableColumns = [
        ['key' => 'vencimento', 'label' => 'Vencimento', 'width' => 'w-100px', 'orderable' => true],
        ['key' => 'descricao', 'label' => 'Descrição', 'width' => 'min-w-200px', 'orderable' => false],
        ['key' => 'filiais', 'label' => 'Filial(is)', 'width' => 'min-w-120px', 'orderable' => false],
        ['key' => 'tipo_documento', 'label' => 'Tipo Doc.', 'width' => 'w-100px', 'orderable' => true],
        ['key' => 'numero_documento', 'label' => 'Nº Doc.', 'width' => 'w-100px', 'orderable' => false],
        ['key' => 'forma_pagamento', 'label' => 'Forma Pgto.', 'width' => 'w-100px', 'orderable' => false],
        ['key' => 'valor', 'label' => 'Valor (R$)', 'width' => 'min-w-80px', 'orderable' => true],
        ['key' => 'status', 'label' => 'Status', 'width' => 'w-80px', 'orderable' => false],
        ['key' => 'acoes', 'label' => 'Ações', 'width' => 'text-end min-w-80px', 'orderable' => false],
    ];

    $tableIdFinal = 'kt_repasses_table';
    $filterId = $tableIdFinal;

    // Preparar colunas para JSON
    $columnsForJson = array_map(
        function ($col, $index) {
            return array_merge($col, ['index' => $index]);
        },
        $tableColumns,
        array_keys($tableColumns),
    );

    // Ordem padrão
    $defaultOrderCol = 0;
    $defaultOrder = [[$defaultOrderCol, 'desc']];

    // Mapear stats para segmented-tabs
    $mappedTabs = array_map(function ($stat) use ($tableIdFinal) {
        return [
            'key' => $stat['key'],
            'label' => $stat['label'],
            'count' => $stat['value'],
            'paneId' => "pane-stat-{$stat['key']}-{$tableIdFinal}",
        ];
    }, $stats);
@endphp

<div class="tab-pane fade show active"
     id="{{ $parentId }}_{{ $key }}"
     role="tabpanel"
     data-pane-id="{{ $parentId }}_{{ $key }}"
     data-table-id="{{ $tableIdFinal }}"
     data-filter-id="{{ $filterId }}"
     data-key="{{ $key }}"
     data-tipo="{{ $tipo }}"
     data-stats-url="{{ route('repasses.stats.data') }}"
     data-data-url="{{ route('repasses.data') }}"
     data-columns-json="{{ json_encode($columnsForJson) }}"
     data-default-order="{{ json_encode($defaultOrder) }}"
     data-page-length="50">

    <x-tenant.segmented-tabs-toolbar
        :tabs="$mappedTabs"
        :active="request('status')"
        id="status-tabs-{{ $tableIdFinal }}"
        :tableId="$tableIdFinal"
        :filterId="$filterId"
        :periodLabel="null"
        :accountOptions="$accountOptions ?? []"
        :showAccountFilter="true"
        :showMoreFilters="true"
        :moreFilters="[]">

        <x-slot:filterActions>
            @if($isMatriz ?? false)
            <button type="button" class="btn btn-sm btn-primary"
                onclick="abrirDrawerRepasse(); return false;">
                @if($tipoRepasse === 'a_receber')
                    <i class="bi bi-arrow-down-left me-1"></i> Novo Repasse a Receber
                @else
                    <i class="bi bi-arrow-up-right me-1"></i> Novo Repasse a Pagar
                @endif
            </button>
            @endif
        </x-slot>

        <x-slot:actionsLeft>
            {{-- Espaço para futuras ações --}}
        </x-slot>

        <x-slot:actionsRight>
            {{-- Espaço para futuras ações --}}
        </x-slot>

        <x-slot:panes>
            @foreach ($mappedTabs as $tab)
                <div class="tab-pane fade {{ request('status') === $tab['key'] || (request('status') === null && $loop->first) ? 'show active' : '' }}"
                    id="{{ $tab['paneId'] }}" role="tabpanel">
                </div>
            @endforeach
        </x-slot:panes>

        <x-slot:tableContent>
            <!--begin::Skeleton Loading-->
            <x-tenant-datatable-skeleton :tableId="$tableIdFinal" :columns="$tableColumns" />
            <!--end::Skeleton Loading-->

            <!--begin::Table Wrapper-->
            <div id="table-wrapper-{{ $tableIdFinal }}" class="d-none mt-4">
                <!--begin::Table-->
                <table class="table align-middle table-striped table-row-dashed fs-6 gy-5 mt-7"
                    id="{{ $tableIdFinal }}" style="width: 100%">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-4">
                            @foreach ($tableColumns as $column)
                                @php
                                    $inlineWidth = '';
                                    if (!empty($column['width'])) {
                                        preg_match('/(?:min-)?w-(\d+)px/', $column['width'], $wMatch);
                                        if (!empty($wMatch[1])) {
                                            $inlineWidth = 'width: ' . $wMatch[1] . 'px; max-width: ' . $wMatch[1] . 'px;';
                                        }
                                    }
                                @endphp
                                @if($column['key'] === 'acoes')
                                    <th class="{{ $column['width'] ?? 'text-center min-w-50px' }}" @if($inlineWidth) style="{{ $inlineWidth }}" @endif>
                                        {{ $column['label'] }}</th>
                                @else
                                    <th class="{{ $column['width'] ?? '' }}" @if($inlineWidth) style="{{ $inlineWidth }}" @endif>{{ $column['label'] }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table Wrapper-->
        </x-slot:tableContent>
    </x-tenant.segmented-tabs-toolbar>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    // Funções globais para ações nos repasses
    window.verRepasse = function(id) {
        fetch('{{ url("repasses") }}/' + id, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && data.repasse) {
                mostrarDetalhesRepasse(data.repasse);
            }
        })
        .catch(function(err) { console.error('Erro ao carregar repasse:', err); });
    };

    window.executarRepasse = function(id) {
        Swal.fire({
            title: 'Executar Repasse?',
            text: 'Isso irá gerar as transações financeiras de saída na matriz e entrada nas filiais. Esta ação não pode ser desfeita.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, executar!',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#50cd89'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch('{{ url("repasses") }}/' + id + '/executar', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        Swal.fire('Sucesso!', data.message, 'success');
                        // Fechar drawer de detalhes se aberto
                        fecharDrawerDetalhes();
                        if (window.TenantDataTablePane) window.TenantDataTablePane.initAllPanes();
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                })
                .catch(function() {
                    Swal.fire('Erro', 'Falha ao executar repasse.', 'error');
                });
            }
        });
    };

    window.cancelarRepasse = function(id) {
        Swal.fire({
            title: 'Cancelar Repasse?',
            text: 'O repasse será marcado como cancelado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, cancelar!',
            cancelButtonText: 'Voltar',
            confirmButtonColor: '#f1416c'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch('{{ url("repasses") }}/' + id + '/cancelar', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        Swal.fire('Cancelado!', data.message, 'success');
                        fecharDrawerDetalhes();
                        if (window.TenantDataTablePane) window.TenantDataTablePane.initAllPanes();
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                });
            }
        });
    };

    function fecharDrawerDetalhes() {
        var drawerEl = document.querySelector('#kt_drawer_repasse_detalhes');
        if (drawerEl) {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (drawer) drawer.hide();
        }
    }

    function getStatusBadge(status) {
        var map = {
            'executado': 'success',
            'pendente': 'warning',
            'cancelado': 'danger'
        };
        return '<span class="badge badge-light-' + (map[status] || 'secondary') + ' fs-7">' + (status || '-') + '</span>';
    }

    function mostrarDetalhesRepasse(repasse) {
        // Preencher dados no drawer
        document.getElementById('rd_titulo').textContent = 'Repasse #' + repasse.id;
        document.getElementById('rd_status').innerHTML = getStatusBadge(repasse.status);
        document.getElementById('rd_descricao').textContent = repasse.descricao || '-';
        document.getElementById('rd_data_emissao').textContent = repasse.data_emissao || '-';
        document.getElementById('rd_data_vencimento').textContent = repasse.data_vencimento || '-';
        document.getElementById('rd_competencia').textContent = repasse.competencia || '-';
        document.getElementById('rd_tipo_documento').textContent = repasse.tipo_documento || '-';
        document.getElementById('rd_numero_documento').textContent = repasse.numero_documento || '-';
        document.getElementById('rd_valor_total').textContent = 'R$ ' + repasse.valor_total;
        document.getElementById('rd_forma_recebimento').textContent = repasse.forma_recebimento_nome || '-';
        document.getElementById('rd_criado_por').textContent = repasse.usuario_nome || '-';

        // Itens/Filiais
        var itensContainer = document.getElementById('rd_itens');
        var itensHtml = '';
        if (repasse.itens && repasse.itens.length) {
            repasse.itens.forEach(function(item) {
                itensHtml += '<div class="d-flex align-items-center justify-content-between py-3 border-bottom">';
                itensHtml += '<div><span class="fw-semibold text-gray-800">' + (item.company_destino_nome || '-') + '</span></div>';
                itensHtml += '<div class="fw-bold text-primary">R$ ' + item.valor + '</div>';
                itensHtml += '</div>';
            });
        } else {
            itensHtml = '<div class="text-muted fs-7">Nenhuma filial destino.</div>';
        }
        itensContainer.innerHTML = itensHtml;

        // Botões de ação (mostrar/ocultar conforme status)
        var btnExecutar = document.getElementById('rd_btn_executar');
        var btnCancelar = document.getElementById('rd_btn_cancelar');
        var btnEditar = document.getElementById('rd_btn_editar');
        if (btnExecutar) btnExecutar.style.display = repasse.status === 'pendente' ? '' : 'none';
        if (btnCancelar) btnCancelar.style.display = repasse.status === 'pendente' ? '' : 'none';
        if (btnEditar) btnEditar.style.display = repasse.status === 'pendente' ? '' : 'none';

        // Setar ID nos botões
        if (btnExecutar) btnExecutar.setAttribute('data-repasse-id', repasse.id);
        if (btnCancelar) btnCancelar.setAttribute('data-repasse-id', repasse.id);
        if (btnEditar) btnEditar.setAttribute('data-repasse-id', repasse.id);

        // Abrir drawer
        var drawerEl = document.querySelector('#kt_drawer_repasse_detalhes');
        if (drawerEl) {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (drawer) drawer.show();
        }
    }

    // Click handlers para botões do drawer
    document.addEventListener('DOMContentLoaded', function() {
        var btnExecutar = document.getElementById('rd_btn_executar');
        if (btnExecutar) {
            btnExecutar.addEventListener('click', function() {
                executarRepasse(this.getAttribute('data-repasse-id'));
            });
        }
        var btnCancelar = document.getElementById('rd_btn_cancelar');
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                cancelarRepasse(this.getAttribute('data-repasse-id'));
            });
        }
        var btnEditar = document.getElementById('rd_btn_editar');
        if (btnEditar) {
            btnEditar.addEventListener('click', function() {
                fecharDrawerDetalhes();
                editarRepasse(this.getAttribute('data-repasse-id'));
            });
        }
    });

    // Abre o drawer de criação em modo edição, preenchendo os dados do repasse
    window.editarRepasse = function(id) {
        fetch('{{ url("repasses") }}/' + id, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && data.repasse) {
                if (typeof window.abrirDrawerRepasseEdicao === 'function') {
                    window.abrirDrawerRepasseEdicao(data.repasse);
                }
            } else {
                Swal.fire('Erro', 'Não foi possível carregar os dados do repasse.', 'error');
            }
        })
        .catch(function(err) {
            console.error('Erro ao carregar repasse para edição:', err);
            Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
        });
    };
})();
</script>
@endpush

<!--begin::Drawer - Detalhes do Repasse-->
<x-tenant-drawer drawerId="kt_drawer_repasse_detalhes" title="Detalhes do Repasse" width="{default:'350px', 'md': '500px'}">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <h4 class="fw-bold text-gray-800 mb-0" id="rd_titulo">Repasse</h4>
        <div id="rd_status"></div>
    </div>

    <!--begin::Info-->
    <div class="fs-6">
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Descrição</div>
            <div class="col-7 text-gray-800" id="rd_descricao">-</div>
        </div>
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Data Emissão</div>
            <div class="col-7 text-gray-800" id="rd_data_emissao">-</div>
        </div>
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Vencimento</div>
            <div class="col-7 text-gray-800" id="rd_data_vencimento">-</div>
        </div>
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Competência</div>
            <div class="col-7 text-gray-800" id="rd_competencia">-</div>
        </div>
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Tipo Doc.</div>
            <div class="col-7 text-gray-800" id="rd_tipo_documento">-</div>
        </div>
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Nº Doc.</div>
            <div class="col-7 text-gray-800" id="rd_numero_documento">-</div>
        </div>
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Forma Recebimento</div>
            <div class="col-7 text-gray-800" id="rd_forma_recebimento">-</div>
        </div>
        <div class="row mb-4">
            <div class="col-5 text-muted fw-semibold">Criado por</div>
            <div class="col-7 text-gray-800" id="rd_criado_por">-</div>
        </div>

        <div class="separator separator-dashed my-5"></div>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <span class="text-muted fw-semibold fs-5">Valor Total</span>
            <span class="fw-bolder text-primary fs-3" id="rd_valor_total">R$ 0,00</span>
        </div>

        <div class="separator separator-dashed my-5"></div>

        <h6 class="fw-bold text-gray-800 mb-3">
            <i class="bi bi-building me-1"></i> Filiais Destino
        </h6>
        <div id="rd_itens"></div>
    </div>
    <!--end::Info-->

    <x-slot name="footer">
        <button type="button" class="btn btn-sm btn-light me-3" data-kt-drawer-dismiss="true">Fechar</button>
        <button type="button" class="btn btn-sm btn-danger me-2" id="rd_btn_cancelar" style="display:none;">
            <i class="bi bi-x-circle me-1"></i> Cancelar
        </button>
        <button type="button" class="btn btn-sm btn-info me-2" id="rd_btn_editar" style="display:none;">
            <i class="bi bi-pencil me-1"></i> Editar
        </button>
        <button type="button" class="btn btn-sm btn-success" id="rd_btn_executar" style="display:none;">
            <i class="bi bi-play-circle me-1"></i> Executar
        </button>
    </x-slot>
</x-tenant-drawer>
<!--end::Drawer - Detalhes do Repasse-->
