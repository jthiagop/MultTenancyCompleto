"use strict";

var KTLancamentoPadraoList = function () {
    // Define shared variables
    var table;
    var datatable;
    var toolbarBase;
    var toolbarSelected;
    var selectedCount;

    // Private functions
    var initDatatable = function () {
        // Init datatable --- more info on datatables: https://datatables.net/manual/
        console.log('Inicializando DataTable para:', table);
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            "pageLength": 10,
            "lengthChange": false,
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': '/lancamentoPadrao/data',
                'type': 'GET',
                'headers': {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                'data': function(d) {
                    console.log('Enviando requisição AJAX:', d);
                    return d;
                },
                'dataSrc': function(json) {
                    console.log('=== RESPOSTA DO SERVIDOR ===');
                    console.log('Resposta completa:', json);
                    console.log('Tipo:', typeof json);
                    console.log('É array?', Array.isArray(json));
                    if (json) {
                        console.log('Chaves:', Object.keys(json));
                        if (json.data) {
                            console.log('Dados encontrados:', json.data.length, 'registros');
                            console.log('Primeiro registro:', json.data[0]);
                            return json.data;
                        } else if (Array.isArray(json)) {
                            console.log('Resposta é array direto:', json.length, 'registros');
                            return json;
                        }
                    }
                    console.warn('Nenhum dado encontrado. Estrutura:', JSON.stringify(json, null, 2));
                    return [];
                },
                'error': function(xhr, error, thrown) {
                    console.error('=== ERRO AO CARREGAR DADOS ===');
                    console.error('Erro:', error);
                    console.error('Thrown:', thrown);
                    console.error('Status HTTP:', xhr.status);
                    console.error('Status Text:', xhr.statusText);
                    console.error('Resposta completa:', xhr.responseText);
                    console.error('Headers:', xhr.getAllResponseHeaders());
                    if (xhr.status === 0) {
                        console.error('Possível problema de CORS ou URL incorreta');
                    } else if (xhr.status === 404) {
                        console.error('URL não encontrada. Verifique a rota.');
                    } else if (xhr.status === 500) {
                        console.error('Erro interno do servidor. Verifique os logs do Laravel.');
                    }
                }
            },
            'columns': [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'description', name: 'description' },
                { data: 'type', name: 'type' },
                { data: 'category', name: 'category' },
                { data: 'conta_debito', name: 'conta_debito', orderable: false },
                { data: 'conta_credito', name: 'conta_credito', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                { orderable: false, targets: 4 }, // Disable ordering on column 4 (conta_debito)
                { orderable: false, targets: 5 }, // Disable ordering on column 5 (conta_credito)
                { orderable: false, targets: 6 }  // Disable ordering on column 6 (actions)
            ]
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleRowDeletion();
            toggleToolbars();
            KTMenu.createInstances(); // Re-init menus
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearch = function () {
        const filterSearch = document.querySelector('[data-kt-lancamento-padrao-table-filter="search"]');
        if (filterSearch) {
            filterSearch.addEventListener('keyup', function (e) {
                datatable.search(e.target.value).draw();
            });
        }
    }

    // Filter Datatable
    var handleFilter = function () {
        // Select filter options
        const filterForm = document.querySelector('[data-kt-lancamento-padrao-table-filter="form"]');
        if (!filterForm) return;

        const filterButton = filterForm.querySelector('[data-kt-lancamento-padrao-table-filter="filter"]');
        const resetButton = filterForm.querySelector('[data-kt-lancamento-padrao-table-filter="reset"]');
        const selectOptions = filterForm.querySelectorAll('select');

        // Filter datatable on submit
        if (filterButton) {
            filterButton.addEventListener('click', function () {
                var filterString = '';

                // Get filter values
                selectOptions.forEach((item, index) => {
                    if (item.value && item.value !== '') {
                        if (index !== 0) {
                            filterString += ' ';
                        }

                        // Build filter value options
                        filterString += item.value;
                    }
                });

                // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
                datatable.search(filterString).draw();
            });
        }

        // Reset datatable
        if (resetButton) {
            resetButton.addEventListener('click', function () {
                // Reset filter form
                selectOptions.forEach((item, index) => {
                    // Reset Select2 dropdown --- official docs reference: https://select2.org/programmatic-control/add-select-clear-items
                    if ($(item).hasClass('select2-hidden-accessible')) {
                        $(item).val(null).trigger('change');
                    }
                });

                // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
                datatable.search('').draw();
            });
        }
    }

    // Delete lancamento padrao
    var handleRowDeletion = function () {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-lancamento-padrao-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');
                const lancamentoId = d.getAttribute('data-id');

                // Get description
                const description = parent.querySelectorAll('td')[1].innerText.trim();

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Tem certeza que deseja excluir o lançamento padrão \"" + description + "\"?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Sim, excluir!",
                    cancelButtonText: "Não, cancelar",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        // Make AJAX request to delete
                        fetch('/lancamentoPadrao/' + lancamentoId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.json().catch(() => ({ success: true }));
                            }
                            return response.json().then(data => ({ success: false, message: data.message || 'Erro ao excluir' }));
                        })
                        .then(data => {
                            if (data.success !== false) {
                                Swal.fire({
                                    text: "Lançamento padrão excluído com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, entendi!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    // Reload datatable
                                    datatable.ajax.reload();
                                }).then(function () {
                                    // Detect checked checkboxes
                                    toggleToolbars();
                                });
                            } else {
                                Swal.fire({
                                    text: data.message || "Erro ao excluir lançamento padrão.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, entendi!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            Swal.fire({
                                text: "Erro ao excluir lançamento padrão.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: "Exclusão cancelada.",
                            icon: "info",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }

    // Init toggle toolbar
    var initToggleToolbar = () => {
        // Toggle selected action toolbar
        // Select all checkboxes
        const checkboxes = table.querySelectorAll('[type="checkbox"]');

        // Select elements
        toolbarBase = document.querySelector('[data-kt-lancamento-padrao-table-toolbar="base"]');
        toolbarSelected = document.querySelector('[data-kt-lancamento-padrao-table-toolbar="selected"]');
        selectedCount = document.querySelector('[data-kt-lancamento-padrao-table-select="selected_count"]');
        const deleteSelected = document.querySelector('[data-kt-lancamento-padrao-table-select="delete_selected"]');

        // Toggle delete selected toolbar
        checkboxes.forEach(c => {
            // Checkbox on click event
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        // Deleted selected rows
        if (deleteSelected) {
            deleteSelected.addEventListener('click', function () {
                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Tem certeza que deseja excluir os lançamentos padrão selecionados?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Sim, excluir!",
                    cancelButtonText: "Não, cancelar",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        // Get selected IDs
                        const selectedIds = [];
                        checkboxes.forEach(c => {
                            if (c.checked && c.value !== '1') { // Exclude header checkbox
                                selectedIds.push(c.value);
                            }
                        });

                        // Delete selected items via AJAX
                        Promise.all(selectedIds.map(id => {
                            return fetch('/lancamentoPadrao/' + id, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            }).then(response => {
                                if (!response.ok) {
                                    throw new Error('Erro ao excluir');
                                }
                                return response;
                            });
                        }))
                        .then(() => {
                            Swal.fire({
                                text: "Lançamentos padrão excluídos com sucesso!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                // Reload datatable
                                datatable.ajax.reload();
                            }).then(function () {
                                toggleToolbars(); // Detect checked checkboxes
                                initToggleToolbar(); // Re-init toolbar to recalculate checkboxes
                            });
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            Swal.fire({
                                text: "Erro ao excluir lançamentos padrão.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: "Exclusão cancelada.",
                            icon: "info",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            });
        }
    }

    // Toggle toolbars
    const toggleToolbars = () => {
        // Select refreshed checkbox DOM elements
        const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');

        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        // Toggle toolbars
        if (checkedState) {
            if (selectedCount) selectedCount.innerHTML = count;
            if (toolbarBase) toolbarBase.classList.add('d-none');
            if (toolbarSelected) toolbarSelected.classList.remove('d-none');
        } else {
            if (toolbarBase) toolbarBase.classList.remove('d-none');
            if (toolbarSelected) toolbarSelected.classList.add('d-none');
        }
    }

    return {
        // Public functions
        init: function () {
            table = document.getElementById('kt_lancamento_padrao_table');

            if (!table) {
                return;
            }

            initDatatable();
            initToggleToolbar();
            handleSearch();
            handleRowDeletion();
            handleFilter();
        }
    }
}();

// On document ready
var initLancamentoPadraoTable = function() {
    // Verifica se a tab está visível antes de inicializar
    var tabPane = document.getElementById('kt_tab_pane_lancamento_padrao');
    if (tabPane && tabPane.classList.contains('show') && tabPane.classList.contains('active')) {
        console.log('Tab de Lançamento Padrão está ativa, inicializando...');
        KTLancamentoPadraoList.init();
    } else {
        // Se a tab não estiver ativa, aguarda o evento de show
        if (tabPane) {
            tabPane.addEventListener('shown.bs.tab', function() {
                console.log('Tab de Lançamento Padrão foi ativada, inicializando...');
                KTLancamentoPadraoList.init();
            });
        }
    }
};

if (typeof KTUtil !== 'undefined' && KTUtil.onDOMContentLoaded) {
    KTUtil.onDOMContentLoaded(function () {
        initLancamentoPadraoTable();
    });
} else {
    // Fallback se KTUtil não estiver disponível
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initLancamentoPadraoTable();
        });
    } else {
        // DOM já está pronto
        initLancamentoPadraoTable();
    }
}

// Também inicializa quando a tab é clicada (Bootstrap 5)
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('[href="#kt_tab_pane_lancamento_padrao"]');
    tabButtons.forEach(function(button) {
        button.addEventListener('shown.bs.tab', function() {
            console.log('Tab clicada, inicializando DataTable...');
            if (!datatable || !$(table).hasClass('dataTable')) {
                KTLancamentoPadraoList.init();
            }
        });
    });
});

