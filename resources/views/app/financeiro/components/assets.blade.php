{{-- Assets e Scripts para o Financeiro --}}

<!--begin::CSS Custom-->
<style>
    .bg-danger.w-4px {
        width: 4px !important;
    }
    
    .h-20px {
        height: 20px !important;
    }
    
    .table-row-dashed > tbody > tr {
        border-bottom: 1px dashed #e1e3ea;
    }
    
    .card-flush {
        border: none;
        box-shadow: 0 0 20px 0 rgba(76, 87, 125, 0.02);
    }
    
    .form-check-custom .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
    }
    
    .badge-light-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .badge-light-success {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-light-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .badge-light-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    
    .badge-light-dark {
        background-color: #d6d8db;
        color: #1b1e21;
    }
    
    .badge-light-secondary {
        background-color: #e2e3e5;
        color: #383d41;
    }
    
    /* Estilos para os cards de resumo */
    .border-dashed {
        border-style: dashed !important;
    }
    
    .card-flush.border {
        transition: all 0.3s ease;
    }
    
    .card-flush.border:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    /* Estilos para filtros */
    .input-group .btn-icon {
        border: 1px solid #e1e3ea;
    }
    
    .input-group .btn-icon:hover {
        background-color: #f8f9fa;
    }
    
    /* Estilos para a barra de ações */
    .vr {
        width: 1px;
        height: 20px;
        background-color: #e1e3ea;
    }
    
    /* Estilos para DataTables */
    .dataTables_wrapper .dataTables_length select {
        min-width: 80px;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        min-width: 200px;
    }
    
    .dataTables_wrapper .dataTables_info {
        padding-top: 0.5rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border: 1px solid #e1e3ea;
        border-radius: 0.375rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #009ef7;
        color: white !important;
        border-color: #009ef7;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f8f9fa;
        border-color: #009ef7;
    }
</style>
<!--end::CSS Custom-->

<!--begin::JavaScript-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // VARIÁVEIS GLOBAIS
    // ========================================
    
    let currentTab = 'receitas';
    let currentMonth = new Date().getMonth() + 1;
    let currentYear = new Date().getFullYear();
    let dataTable = null;
    
    // ========================================
    // INICIALIZAÇÃO
    // ========================================
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Carregar opções de filtros
    loadFilterOptions();
    
    // Inicializar DataTable
    initializeDataTable();
    
    // ========================================
    // FUNCIONALIDADES DE NAVEGAÇÃO DE TABS
    // ========================================
    
    const navReceitas = document.getElementById('navReceitas');
    const navDespesas = document.getElementById('navDespesas');
    const actionButton = document.getElementById('actionButton');
    const actionButtonText = document.getElementById('actionButtonText');
    
    if (navReceitas && navDespesas && actionButton && actionButtonText) {
        navReceitas.addEventListener('click', function() {
            currentTab = 'receitas';
            actionButtonText.textContent = 'Receber';
            actionButton.className = 'btn btn-primary';
            updateInterfaceForTab('receitas');
            reloadDataTable();
        });
        
        navDespesas.addEventListener('click', function() {
            currentTab = 'despesas';
            actionButtonText.textContent = 'Pagar';
            actionButton.className = 'btn btn-danger';
            updateInterfaceForTab('despesas');
            reloadDataTable();
        });
    }
    
    function updateInterfaceForTab(tabType) {
        console.log('Mudou para tab:', tabType);
        updateCards(tabType);
    }
    
    // ========================================
    // FUNCIONALIDADES DE FILTROS
    // ========================================
    
    // Navegação de meses
    const monthSelector = document.getElementById('monthSelector');
    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');
    
    if (monthSelector && prevMonth && nextMonth) {
        let currentDate = new Date();
        
        prevMonth.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            currentMonth = currentDate.getMonth() + 1;
            currentYear = currentDate.getFullYear();
            updateMonthDisplay();
            reloadDataTable();
        });
        
        nextMonth.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            currentMonth = currentDate.getMonth() + 1;
            currentYear = currentDate.getFullYear();
            updateMonthDisplay();
            reloadDataTable();
        });
        
        function updateMonthDisplay() {
            const options = { month: 'long', year: 'numeric' };
            monthSelector.value = currentDate.toLocaleDateString('pt-BR', options);
        }
    }
    
    // Filtros de status, conta e fornecedor
    const statusFilter = document.getElementById('statusFilter');
    const accountFilter = document.getElementById('accountFilter');
    const supplierFilter = document.getElementById('supplierFilter');
    
    [statusFilter, accountFilter, supplierFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                reloadDataTable();
            });
        }
    });
    
    // Mais filtros
    document.querySelectorAll('[data-filter]').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const filterType = this.getAttribute('data-filter');
            
            switch(filterType) {
                case 'valor':
                    showValueFilter();
                    break;
                case 'data':
                    showDateFilter();
                    break;
                case 'categoria':
                    showCategoryFilter();
                    break;
                case 'limpar':
                    clearAllFilters();
                    break;
            }
        });
    });
    
    // ========================================
    // FUNCIONALIDADES DE SELEÇÃO
    // ========================================
    
    function updateSelectionState() {
        const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
        const selectedCount = document.getElementById('selectedCount');
        const actionButtonDisabled = document.getElementById('actionButton');
        const batchActionsButton = document.getElementById('batchActionsButton');
        
        if (selectedCount) {
            selectedCount.textContent = checkedBoxes.length;
        }
        
        if (actionButtonDisabled) {
            actionButtonDisabled.disabled = checkedBoxes.length === 0;
        }
        
        if (batchActionsButton) {
            batchActionsButton.disabled = checkedBoxes.length === 0;
        }
    }
    
    // ========================================
    // FUNCIONALIDADES DE AÇÕES
    // ========================================
    
    // Botão principal de ação (Receber/Pagar)
    if (actionButton) {
        actionButton.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
            
            if (checkedBoxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Selecione pelo menos um registro para continuar.'
                });
                return;
            }
            
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (currentTab === 'receitas') {
                markAsPaid(ids, 'receita');
            } else {
                markAsPaid(ids, 'despesa');
            }
        });
    }
    
    // Ações em lote
    document.querySelectorAll('[data-action]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
            
            if (checkedBoxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Selecione pelo menos um registro para continuar.'
                });
                return;
            }
            
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            switch(action) {
                case 'marcar-pago':
                    markAsPaid(ids, currentTab === 'receitas' ? 'receita' : 'despesa');
                    break;
                case 'exportar':
                    exportData(ids);
                    break;
                case 'imprimir':
                    printData(ids);
                    break;
                case 'excluir':
                    deleteEntries(ids);
                    break;
            }
        });
    });
    
    // ========================================
    // FUNCIONALIDADES AJAX
    // ========================================
    
    function loadFilterOptions() {
        fetch('/financeiro/filter-options')
            .then(response => response.json())
            .then(data => {
                populateFilterDropdown('supplierFilter', data.fornecedores);
                populateFilterDropdown('accountFilter', data.contas);
            })
            .catch(error => console.error('Erro ao carregar filtros:', error));
    }
    
    function populateFilterDropdown(elementId, data) {
        const element = document.getElementById(elementId);
        if (element) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nome;
                element.appendChild(option);
            });
        }
    }
    
    
    function reloadDataTable() {
        if (dataTable) {
            dataTable.ajax.reload();
        }
    }
    
    // Expõe função globalmente para uso em outros scripts (ex: drawer form)
    window.reloadDataTable = reloadDataTable;
    
    function initializeDataTable() {
        const tableId = currentTab === 'receitas' ? 'receitasTable' : 'despesasTable';
        const table = document.getElementById(tableId);
        
        if (table) {
            dataTable = $(table).DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/financeiro/data',
                    type: 'GET',
                    data: function(d) {
                        d.tipo = currentTab === 'receitas' ? 'receita' : 'despesa';
                        d.month = currentMonth;
                        d.year = currentYear;
                        d.status = statusFilter ? statusFilter.value : '';
                        d.fornecedor = supplierFilter ? supplierFilter.value : '';
                        d.conta = accountFilter ? accountFilter.value : '';
                    },
                    dataSrc: function(json) {
                        updateCards(json.cards);
                        return json.data;
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="${row.id}">
                            </div>`;
                        }
                    },
                    {
                        data: 'data_primeiro_vencimento',
                        render: function(data, type, row) {
                            const isVencido = new Date(data) < new Date();
                            const statusClass = isVencido ? 'bg-danger' : 'bg-success';
                            return `<div class="d-flex align-items-center">
                                <div class="${statusClass} w-4px h-20px me-3"></div>
                                ${formatDate(data)}
                            </div>`;
                        }
                    },
                    {
                        data: 'data_pagamento',
                        render: function(data, type, row) {
                            return data ? formatDate(data) : '-';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let html = `<div class="d-flex flex-column">
                                <span class="fw-bold">${row.descricao}</span>`;
                            
                            if (row.lancamento_padrao) {
                                html += `<small class="text-muted">${row.lancamento_padrao.nome}</small>`;
                            }
                            
                            if (row.fornecedor) {
                                html += `<small class="text-muted">${row.fornecedor.nome}</small>`;
                            }
                            
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'valor',
                        className: 'text-end',
                        render: function(data, type, row) {
                            return `R$ ${formatCurrency(data)}`;
                        }
                    },
                    {
                        data: 'valor_restante',
                        className: 'text-end',
                        render: function(data, type, row) {
                            const valor = data || row.valor;
                            return `R$ ${formatCurrency(valor)}`;
                        }
                    },
                    {
                        data: 'status_pagamento',
                        render: function(data, type, row) {
                            const statusClass = getStatusClass(data);
                            return `<span class="badge ${statusClass}">${capitalizeFirst(data)}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-end',
                        render: function(data, type, row) {
                            return `<div class="dropdown">
                                <button class="btn btn-sm btn-light-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Ações
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-action="editar" data-id="${row.id}">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" data-action="marcar-pago" data-id="${row.id}">
                                        <i class="fas fa-check me-2"></i>Marcar como Pago
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" data-action="excluir" data-id="${row.id}">
                                        <i class="fas fa-trash me-2"></i>Excluir
                                    </a></li>
                                </ul>
                            </div>`;
                        }
                    }
                ],
                order: [[1, 'asc']],
                pageLength: 25,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                initComplete: function() {
                    // Adicionar event listeners para checkboxes
                    $(table).on('change', 'input[type="checkbox"]', function() {
                        updateSelectionState();
                    });
                    
                    // Adicionar event listeners para ações
                    $(table).on('click', '[data-action]', function(e) {
                        e.preventDefault();
                        const action = this.getAttribute('data-action');
                        const id = this.getAttribute('data-id');
                        
                        switch(action) {
                            case 'editar':
                                editEntry(id);
                                break;
                            case 'marcar-pago':
                                markAsPaid([id], currentTab === 'receitas' ? 'receita' : 'despesa');
                                break;
                            case 'excluir':
                                deleteEntries([id]);
                                break;
                        }
                    });
                }
            });
        }
    }
    
    function markAsPaid(ids, tipo) {
        Swal.fire({
            title: 'Confirmar Ação',
            text: `Deseja marcar ${ids.length} registro(s) como pago(s)?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, marcar como pago',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/financeiro/mark-as-paid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ids: ids,
                        data_pagamento: new Date().toISOString().split('T')[0]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: data.message
                        });
                        reloadDataTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao processar a solicitação.'
                    });
                });
            }
        });
    }
    
    function deleteEntries(ids) {
        Swal.fire({
            title: 'Confirmar Exclusão',
            text: `Tem certeza que deseja excluir ${ids.length} registro(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/financeiro/delete', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: data.message
                        });
                        reloadDataTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao processar a solicitação.'
                    });
                });
            }
        });
    }
    
    function exportData(ids) {
        const format = prompt('Escolha o formato (pdf, excel, csv):', 'excel');
        if (format && ['pdf', 'excel', 'csv'].includes(format.toLowerCase())) {
            fetch('/financeiro/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tipo: currentTab === 'receitas' ? 'receita' : 'despesa',
                    format: format.toLowerCase(),
                    ids: ids
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Exportação Preparada',
                        text: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao processar a exportação.'
                });
            });
        }
    }
    
    function printData(ids) {
        // Implementar impressão
        console.log('Imprimir registros:', ids);
        window.print();
    }
    
    function editEntry(id) {
        // Implementar edição
        console.log('Editar registro:', id);
        // Aqui você pode abrir um modal ou redirecionar para a página de edição
    }
    
    // ========================================
    // FUNCIONALIDADES AUXILIARES
    // ========================================
    
    function updateCards(cardsData) {
        const cardElements = {
            'vencidos': document.querySelector('.card-flush.border-danger .fw-bold'),
            'vencemHoje': document.querySelector('.card-flush.border-warning .fw-bold'),
            'aVencer': document.querySelector('.card-flush.border-primary .fw-bold'),
            'pagos': document.querySelector('.card-flush.border-success .fw-bold'),
            'total': document.querySelector('.card-flush.border-info .fw-bold')
        };
        
        if (cardsData) {
            Object.keys(cardsData).forEach(key => {
                if (cardElements[key]) {
                    cardElements[key].textContent = formatCurrency(cardsData[key]);
                }
            });
        }
    }
    
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }
    
    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value || 0);
    }
    
    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    function getStatusClass(status) {
        const statusMap = {
            'pago': 'badge-light-success',
            'em aberto': 'badge-light-warning',
            'pendente': 'badge-light-info',
            'vencido': 'badge-light-danger',
            'cancelado': 'badge-light-dark'
        };
        return statusMap[status] || 'badge-light-secondary';
    }
    
    function showValueFilter() {
        Swal.fire({
            title: 'Filtro por Valor',
            html: `
                <div class="row">
                    <div class="col-6">
                        <label>Valor Mínimo</label>
                        <input type="number" id="minValue" class="form-control" step="0.01">
                    </div>
                    <div class="col-6">
                        <label>Valor Máximo</label>
                        <input type="number" id="maxValue" class="form-control" step="0.01">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Aplicar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implementar filtro por valor
                console.log('Filtro por valor aplicado');
                reloadDataTable();
            }
        });
    }
    
    function showDateFilter() {
        Swal.fire({
            title: 'Filtro por Data',
            html: `
                <div class="row">
                    <div class="col-6">
                        <label>Data Inicial</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="col-6">
                        <label>Data Final</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Aplicar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implementar filtro por data
                console.log('Filtro por data aplicado');
                reloadDataTable();
            }
        });
    }
    
    function showCategoryFilter() {
        // Implementar filtro por categoria
        console.log('Mostrar filtro de categoria');
    }
    
    function clearAllFilters() {
        if (statusFilter) statusFilter.value = '';
        if (accountFilter) accountFilter.value = '';
        if (supplierFilter) supplierFilter.value = '';
        if (monthSelector) {
            const currentDate = new Date();
            monthSelector.value = currentDate.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
        }
        
        reloadDataTable();
        
        Swal.fire({
            icon: 'success',
            title: 'Filtros Limpos',
            text: 'Todos os filtros foram removidos.'
        });
    }
    
    // ========================================
    // INICIALIZAÇÃO FINAL
    // ========================================
    
    console.log('Scripts do financeiro aprimorados carregados com sucesso!');
});
</script>
<!--end::JavaScript-->
