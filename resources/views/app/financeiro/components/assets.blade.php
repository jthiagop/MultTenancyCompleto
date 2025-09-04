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
</style>
<!--end::CSS Custom-->

<!--begin::JavaScript-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // FUNCIONALIDADES GERAIS
    // ========================================
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // ========================================
    // FUNCIONALIDADES DE NAVEGAÇÃO DE TABS
    // ========================================
    
    // Detectar mudança de tab e atualizar interface
    const navReceitas = document.getElementById('navReceitas');
    const navDespesas = document.getElementById('navDespesas');
    const actionButton = document.getElementById('actionButton');
    const actionButtonText = document.getElementById('actionButtonText');
    
    if (navReceitas && navDespesas && actionButton && actionButtonText) {
        navReceitas.addEventListener('click', function() {
            actionButtonText.textContent = 'Receber';
            actionButton.className = 'btn btn-primary';
            updateInterfaceForTab('receitas');
        });
        
        navDespesas.addEventListener('click', function() {
            actionButtonText.textContent = 'Pagar';
            actionButton.className = 'btn btn-danger';
            updateInterfaceForTab('despesas');
        });
    }
    
    function updateInterfaceForTab(tabType) {
        // Atualizar contadores e dados específicos da tab
        console.log('Mudou para tab:', tabType);
        
        // Aqui você pode adicionar lógica para atualizar os cards de resumo
        // baseado no tipo de tab selecionada
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
            updateMonthDisplay();
        });
        
        nextMonth.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateMonthDisplay();
        });
        
        function updateMonthDisplay() {
            const options = { month: 'long', year: 'numeric' };
            monthSelector.value = currentDate.toLocaleDateString('pt-BR', options);
            // Aqui você pode adicionar uma chamada AJAX para atualizar os dados
            loadDataForMonth(currentDate);
        }
    }
    
    // Filtros de status, conta e fornecedor
    const statusFilter = document.getElementById('statusFilter');
    const accountFilter = document.getElementById('accountFilter');
    const supplierFilter = document.getElementById('supplierFilter');
    
    [statusFilter, accountFilter, supplierFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                applyFilters();
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
    
    // Seleção de registros
    const selectAllCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    const selectedCount = document.getElementById('selectedCount');
    const actionButtonDisabled = document.getElementById('actionButton');
    const batchActionsButton = document.getElementById('batchActionsButton');
    
    selectAllCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectionState();
        });
    });
    
    function updateSelectionState() {
        const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
        const totalRecords = document.getElementById('totalRecords');
        
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
                alert('Selecione pelo menos um registro para continuar.');
                return;
            }
            
            const activeTab = document.querySelector('.nav-link.active');
            const isReceitas = activeTab && activeTab.id === 'navReceitas';
            
            if (isReceitas) {
                // Implementar recebimento
                console.log('Receber registros:', Array.from(checkedBoxes).map(cb => cb.value));
            } else {
                // Implementar pagamento
                console.log('Pagar registros:', Array.from(checkedBoxes).map(cb => cb.value));
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
                alert('Selecione pelo menos um registro para continuar.');
                return;
            }
            
            switch(action) {
                case 'marcar-pago':
                    if (confirm(`Deseja marcar ${checkedBoxes.length} registro(s) como pago(s)?`)) {
                        console.log('Marcar como pago:', Array.from(checkedBoxes).map(cb => cb.value));
                    }
                    break;
                case 'exportar':
                    console.log('Exportar:', Array.from(checkedBoxes).map(cb => cb.value));
                    break;
                case 'imprimir':
                    console.log('Imprimir:', Array.from(checkedBoxes).map(cb => cb.value));
                    break;
                case 'excluir':
                    if (confirm(`Tem certeza que deseja excluir ${checkedBoxes.length} registro(s)?`)) {
                        console.log('Excluir:', Array.from(checkedBoxes).map(cb => cb.value));
                    }
                    break;
            }
        });
    });
    
    // ========================================
    // FUNCIONALIDADES AUXILIARES
    // ========================================
    
    function loadDataForMonth(date) {
        // Implementar carregamento de dados para o mês selecionado
        console.log('Carregando dados para:', date.toLocaleDateString('pt-BR'));
        
        // Exemplo de chamada AJAX
        /*
        fetch(`/financeiro/dados/${date.getFullYear()}/${date.getMonth() + 1}`)
            .then(response => response.json())
            .then(data => {
                updateCards(data);
                updateTable(data);
            });
        */
    }
    
    function applyFilters() {
        // Implementar aplicação de filtros
        console.log('Aplicando filtros...');
        
        const status = statusFilter ? statusFilter.value : '';
        const account = accountFilter ? accountFilter.value : '';
        const supplier = supplierFilter ? supplierFilter.value : '';
        
        console.log('Filtros:', { status, account, supplier });
    }
    
    function showValueFilter() {
        // Implementar modal de filtro por valor
        console.log('Mostrar filtro de valor');
    }
    
    function showDateFilter() {
        // Implementar modal de filtro por data
        console.log('Mostrar filtro de data');
    }
    
    function showCategoryFilter() {
        // Implementar modal de filtro por categoria
        console.log('Mostrar filtro de categoria');
    }
    
    function clearAllFilters() {
        // Limpar todos os filtros
        if (statusFilter) statusFilter.value = '';
        if (accountFilter) accountFilter.value = '';
        if (supplierFilter) supplierFilter.value = '';
        if (monthSelector) monthSelector.value = new Date().toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
        
        console.log('Filtros limpos');
    }
    
    // ========================================
    // INICIALIZAÇÃO
    // ========================================
    
    // Inicializar estado da interface
    updateSelectionState();
    
    // Carregar dados iniciais
    loadDataForMonth(new Date());
    
    console.log('Scripts do financeiro aprimorados carregados com sucesso!');
});
</script>
<!--end::JavaScript-->
