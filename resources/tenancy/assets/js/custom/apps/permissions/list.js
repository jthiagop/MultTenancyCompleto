"use strict";

// Class definition
var KTPermissionsList = function () {
    // Define shared variables
    var table;
    var datatable;

    // Private functions
    var initPermissionList = function () {
        // Get table element
        table = document.querySelector('#kt_permissions_table');

        // Init datatable
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
                { data: 'guard', name: 'guard', orderable: false, searchable: false },
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
                emptyTable: "Nenhuma permissão cadastrada",
                paginate: {
                    first: "Primeiro",
                    previous: "Anterior",
                    next: "Próximo",
                    last: "Último"
                }
            }
        });

        // Re-init functions on every table re-draw
        datatable.on('draw', function () {
            handleDeleteRows();
            handleUpdateRows();
        });
    }

    // Search Datatable
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-permissions-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Delete permission
    var handleDeleteRows = () => {
        const deleteButtons = table.querySelectorAll('[data-kt-permissions-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const permissionId = d.getAttribute('data-permission-id');
                const permissionName = d.getAttribute('data-permission-name');

                Swal.fire({
                    text: `Tem certeza que deseja excluir a permissão "${permissionName}"?`,
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Sim, excluir!",
                    cancelButtonText: "Cancelar",
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-active-light"
                    }
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: `/permissions/${permissionId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire({
                                    text: response.message || "Permissão excluída com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function () {
                                    datatable.ajax.reload();
                                });
                            },
                            error: function (xhr) {
                                var errorMessage = "Erro ao excluir a permissão!";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    text: errorMessage,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    }

    // Update permission - populate modal
    var handleUpdateRows = () => {
        const updateButtons = table.querySelectorAll('[data-bs-target="#kt_modal_update_permission"]');

        updateButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const permissionId = button.getAttribute('data-permission-id');
                const permissionName = button.getAttribute('data-permission-name');
                const permissionGuard = button.getAttribute('data-permission-guard');

                const form = document.querySelector('#kt_modal_update_permission_form');
                form.querySelector('[name="permission_id"]').value = permissionId;
                form.querySelector('[name="permission_name"]').value = permissionName;
                form.querySelector('[name="permission_guard"]').value = permissionGuard;
            });
        });
    }

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#kt_permissions_table');

            if (!table) {
                return;
            }

            initPermissionList();
            handleSearchDatatable();
            handleDeleteRows();
            handleUpdateRows();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTPermissionsList.init();
});
