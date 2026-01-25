"use strict";

// Class definition
var KTCustomersList = function () {
    // Define shared variables
    var datatable;
    var validator;
    var table;
    var form;
    var modal;
    var modalEl;
    var dataInicialFlatpickr;
    var dataFinalFlatpickr;

    // Init datepickers
    var initDatepickers = function() {
        // Verificar se jQuery e Flatpickr estão disponíveis
        if (typeof $ === 'undefined' || typeof $.fn.flatpickr === 'undefined') {
            console.warn('jQuery ou Flatpickr não estão disponíveis. Datepickers não serão inicializados.');
            return;
        }

        // Data Inicial - Flatpickr
        var dataInicialInput = document.getElementById('nfe_data_inicial');
        var dataFinalInput = document.getElementById('nfe_data_final');

        if (!dataInicialInput || !dataFinalInput) {
            console.warn('Campos de data não encontrados. Datepickers não serão inicializados.');
            return;
        }

        // Inicializar Data Inicial
        dataInicialFlatpickr = $(dataInicialInput).flatpickr({
            enableTime: false,
            dateFormat: "d/m/Y",
            locale: "pt",
            allowInput: true,
            clickOpens: true,
            onChange: function(selectedDates, dateStr, instance) {
                // Quando a data inicial mudar, atualiza a data mínima da data final
                if (selectedDates.length > 0 && dataFinalFlatpickr) {
                    dataFinalFlatpickr.set('minDate', selectedDates[0]);
                }
            }
        });

        // Inicializar Data Final
        dataFinalFlatpickr = $(dataFinalInput).flatpickr({
            enableTime: false,
            dateFormat: "d/m/Y",
            locale: "pt",
            allowInput: true,
            clickOpens: true,
            onChange: function(selectedDates, dateStr, instance) {
                // Quando a data final mudar, atualiza a data máxima da data inicial
                if (selectedDates.length > 0 && dataInicialFlatpickr) {
                    dataInicialFlatpickr.set('maxDate', selectedDates[0]);
                }
            }
        });

        console.log('Datepickers inicializados com sucesso');
    }

    // Private functions
    var initCustomerList = function () {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[5].innerHTML, "DD MMM YYYY, LT").format(); // select date from 5th column in table
            dateRow[5].setAttribute('data-order', realDate);
        });

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                { orderable: false, targets: 6 }, // Disable ordering on column 6 (actions)
            ]
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleDeleteRows();
            toggleToolbars();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Delete customer
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-customer-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                // Get customer name
                const customerName = parent.querySelectorAll('td')[1].innerText;

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to delete " + customerName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            text: "You have deleted " + customerName + "!.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function () {
                            // Remove current row
                            datatable.row($(parent)).remove().draw();
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: customerName + " was not deleted.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }

     // Handle status filter dropdown
     var handleStatusFilter = () => {
         const filterStatus = document.querySelector('[data-kt-ecommerce-order-filter="status"]');
         if (filterStatus) {
             $(filterStatus).on('change', e => {
                 let value = e.target.value;
                 if (value === 'all') {
                     value = '';
                 }
                 datatable.column(4).search(value).draw();
             });
         }
     }

     // Handle buscar button
     var handleBuscarNotas = function() {
         const buscarButton = document.getElementById('kt_nfe_buscar_btn');

         console.log('handleBuscarNotas: Inicializando...', buscarButton);

         if (buscarButton) {
             console.log('handleBuscarNotas: Botão encontrado, anexando event listener');

             buscarButton.addEventListener('click', function(e) {
                 e.preventDefault();

                 console.log('handleBuscarNotas: Botão clicado!');

                 const dataInicial = document.getElementById('nfe_data_inicial')?.value;
                 const dataFinal = document.getElementById('nfe_data_final')?.value;

                 // Validação básica
                 if (!dataInicial || !dataFinal) {
                     Swal.fire({
                         text: "Por favor, preencha o período inicial e final.",
                         icon: "warning",
                         buttonsStyling: false,
                         confirmButtonText: "Ok",
                         customClass: {
                             confirmButton: "btn btn-primary"
                         }
                     });
                     return;
                 }

                 // Validar se data inicial não é maior que data final
                 const parseDate = function(dateStr) {
                     const parts = dateStr.split('/');
                     if (parts.length === 3) {
                         return new Date(parts[2], parts[1] - 1, parts[0]);
                     }
                     return null;
                 };

                 const dataInicialDate = parseDate(dataInicial);
                 const dataFinalDate = parseDate(dataFinal);

                 if (dataInicialDate && dataFinalDate && dataInicialDate > dataFinalDate) {
                     Swal.fire({
                         text: "A data inicial não pode ser maior que a data final.",
                         icon: "error",
                         buttonsStyling: false,
                         confirmButtonText: "Ok",
                         customClass: {
                             confirmButton: "btn btn-primary"
                         }
                     });
                     return;
                 }

                 // Mostrar loading
                 buscarButton.setAttribute('data-kt-indicator', 'on');
                 buscarButton.disabled = true;

                 // AJAX real para buscar documentos
                 $.ajax({
                     url: '/nfe-entrada/filtrar',
                     method: 'POST',
                     data: {
                         data_inicial: dataInicial,
                         data_final: dataFinal,
                         _token: $('meta[name="csrf-token"]').attr('content')
                     },
                    success: function(response) {
                        buscarButton.removeAttribute('data-kt-indicator');
                        buscarButton.disabled = false;

                        if (response.success) {
                            // Atualizar tabela se datatable estiver inicializado
                            if (datatable) {
                                // Limpar tabela atual
                                datatable.clear();

                                // Adicionar novos dados
                                if (response.documentos && response.documentos.length > 0) {
                                    // Preparar array de linhas
                                    var rows = [];
                                    response.documentos.forEach(function(doc) {
                                        rows.push([
                                            '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input" type="checkbox" value="' + doc.id + '" /></div>',
                                            doc.data_emissao,
                                            '<span class="badge badge-light fw-bold" title="' + doc.chave_acesso + '">' + doc.chave_resumida + '</span>',
                                            '<div class="d-flex flex-column"><span class="text-gray-800 fw-bold mb-1">' + doc.emitente_nome + '</span><span class="text-gray-500">' + doc.emitente_cnpj + '</span></div>',
                                            'R$ ' + doc.valor_total,
                                            '<span class="badge ' + doc.status_label.class + '">' + doc.status_label.text + '</span>',
                                            '<span class="badge ' + (doc.tp_amb == 1 ? 'badge-light-success' : 'badge-light-warning') + '">' + doc.ambiente_label + '</span>',
                                            '<a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Ações<i class="ki-duotone ki-down fs-5 ms-1"></i></a>'
                                        ]);
                                    });

                                    // Adicionar todas as linhas de uma vez
                                    datatable.rows.add(rows);
                                }

                                datatable.draw();
                            }

                            Swal.fire({
                                text: `Encontrados ${response.total} documento(s) no período de ${response.periodo.inicial} a ${response.periodo.final}.`,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        } else {
                             Swal.fire({
                                 text: response.message || "Erro ao buscar documentos.",
                                 icon: "error",
                                 buttonsStyling: false,
                                 confirmButtonText: "Ok",
                                 customClass: {
                                     confirmButton: "btn btn-primary"
                                 }
                             });
                         }
                     },
                     error: function(xhr) {
                         buscarButton.removeAttribute('data-kt-indicator');
                         buscarButton.disabled = false;

                         let errorMessage = "Erro ao buscar documentos.";
                         if (xhr.responseJSON && xhr.responseJSON.message) {
                             errorMessage = xhr.responseJSON.message;
                         }

                         Swal.fire({
                             text: errorMessage,
                             icon: "error",
                             buttonsStyling: false,
                             confirmButtonText: "Ok",
                             customClass: {
                                 confirmButton: "btn btn-primary"
                             }
                         });
                     }
                 });
             });
         }
     }

    // Init toggle toolbar
    var initToggleToolbar = () => {
        // Toggle selected action toolbar
        // Select all checkboxes
        const checkboxes = table.querySelectorAll('[type="checkbox"]');

        // Select elements
        const deleteSelected = document.querySelector('[data-kt-customer-table-select="delete_selected"]');

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
        deleteSelected.addEventListener('click', function () {
            // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
            Swal.fire({
                text: "Are you sure you want to delete selected customers?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    Swal.fire({
                        text: "You have deleted all selected customers!.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {
                        // Remove all selected customers
                        checkboxes.forEach(c => {
                            if (c.checked) {
                                datatable.row($(c.closest('tbody tr'))).remove().draw();
                            }
                        });

                        // Remove header checked box
                        const headerCheckbox = table.querySelectorAll('[type="checkbox"]')[0];
                        headerCheckbox.checked = false;
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Selected customers was not deleted.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });
    }

    // Toggle toolbars
    const toggleToolbars = () => {
        // Define variables
        const toolbarBase = document.querySelector('[data-kt-customer-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-customer-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-customer-table-select="selected_count"]');

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
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

     // Public methods
     return {
         init: function () {
             // Inicializar datepickers SEMPRE
             initDatepickers();

             // Inicializar tabela se existir
             table = document.querySelector('#kt_customers_table');

             if (table) {
                 initCustomerList(); // Isso inicializa o datatable
                 initToggleToolbar();
                 handleSearchDatatable();
                 handleDeleteRows();
             }

             // Inicializar filtros e botão de busca (funcionam mesmo sem datatable)
             handleStatusFilter();
             handleBuscarNotas();
         }
     }
 }();

// On document ready
if (typeof KTUtil !== 'undefined') {
    KTUtil.onDOMContentLoaded(function () {
        KTCustomersList.init();
    });
} else {
    document.addEventListener('DOMContentLoaded', function () {
        KTCustomersList.init();
    });
}
