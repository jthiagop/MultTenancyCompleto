"use strict";

// Class definition
var KTModulesList = function () {
    // Define shared variables
    var table;
    var datatable;

    // Private functions
    var initModuleList = function () {
        // Set date data order (if you have date columns)
        const tableRows = table.querySelectorAll('tbody tr');

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: table.dataset.url,
                type: 'GET',
                error: function(xhr, error, code) {
                    console.error('DataTables error:', error, code);
                    console.error('Response:', xhr.responseText);
                }
            },
            columns: [
                { data: 'name', name: 'name' },
                { data: 'key', name: 'key' },
                { data: 'route_name', name: 'route_name' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'dashboard', name: 'dashboard', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']],
            info: false,
            pageLength: 10,
            lengthChange: false,
            language: {
                processing: "Carregando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros no total)",
                loadingRecords: "Carregando...",
                zeroRecords: "Nenhum registro encontrado",
                emptyTable: "Nenhum módulo cadastrado",
                paginate: {
                    first: "Primeiro",
                    previous: "Anterior",
                    next: "Próximo",
                    last: "Último"
                }
            }
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            handleDeleteRows();
            handleUpdateRows();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-modules-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Delete module
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-modules-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                // Get module data
                const moduleId = this.getAttribute('data-module-id');
                const moduleName = this.getAttribute('data-module-name');

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Tem certeza que deseja excluir o módulo " + moduleName + "?",
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
                        // Delete via AJAX
                        $.ajax({
                            url: '/modules/' + moduleId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    text: "Módulo excluído com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    // Reload datatable
                                    datatable.ajax.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    text: "Erro ao excluir o módulo!",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: moduleName + " não foi excluído.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }

    // Update module
    var handleUpdateRows = () => {
        // Select all update buttons
        const updateButtons = table.querySelectorAll('[data-bs-target="#kt_modal_update_module"]');

        updateButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                // Get module data from data attributes
                const moduleId = this.getAttribute('data-module-id');
                const moduleName = this.getAttribute('data-module-name');
                const moduleKey = this.getAttribute('data-module-key');
                const moduleRoute = this.getAttribute('data-module-route');
                const modulePermission = this.getAttribute('data-module-permission');
                const moduleDescription = this.getAttribute('data-module-description');
                const moduleActive = this.getAttribute('data-module-active');
                const moduleDashboard = this.getAttribute('data-module-dashboard');
                const moduleOrder = this.getAttribute('data-module-order');
                const moduleIconPath = this.getAttribute('data-module-icon-path');

                // Populate the update modal form
                const modal = document.querySelector('#kt_modal_update_module');
                modal.querySelector('[name="module_id"]').value = moduleId;
                modal.querySelector('[name="module_name"]').value = moduleName;
                modal.querySelector('[name="module_key"]').value = moduleKey;
                modal.querySelector('[name="module_route"]').value = moduleRoute;
                modal.querySelector('[name="module_permission"]').value = modulePermission;
                modal.querySelector('[name="module_description"]').value = moduleDescription;
                modal.querySelector('[name="module_active"]').checked = moduleActive === '1';
                modal.querySelector('[name="module_dashboard"]').checked = moduleDashboard === '1';
                modal.querySelector('[name="module_order"]').value = moduleOrder;

                // Update icon preview if exists
                if (moduleIconPath) {
                    const imageWrapper = modal.querySelector('.image-input-wrapper');
                    if (imageWrapper) {
                        imageWrapper.style.backgroundImage = 'url(' + moduleIconPath + ')';
                    }
                }
            });
        });
    }

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#kt_modules_table');

            if (!table) {
                return;
            }

            initModuleList();
            handleSearchDatatable();
            handleDeleteRows();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModulesList.init();
});
