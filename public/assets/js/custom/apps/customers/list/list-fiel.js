"use strict";

// Class definition
var KTCustomersList = function () {
    // Define shared variables
    var datatable;
    var filterMonth;
    var filterPayment;
    var table

    // Private functions
    var initCustomerList = function () {
        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': window.location.href, // Use current URL which maps to FielController@index
                'type': 'GET'
            },
            'columns': [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'nome_completo', name: 'nome_completo' },
                { data: 'email', name: 'email' },
                { data: 'company', name: 'company.name' },
                { data: 'status', name: 'status' },
                { data: 'data_nascimento', name: 'data_nascimento' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                { orderable: false, targets: 6 }, // Disable ordering on column 6 (actions)
            ]
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleEditRows();
            handleDeleteRows();
            toggleToolbars();
            KTMenu.createInstances(); // Re-init menus
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        filterMonth = $('[data-kt-customer-table-filter="month"]');
        filterPayment = document.querySelectorAll('[data-kt-customer-table-filter="payment_type"] [name="payment_type"]');
        const filterButton = document.querySelector('[data-kt-customer-table-filter="filter"]');

        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
            // Get filter values
            const monthValue = filterMonth.val();
            let paymentValue = '';

            // Get payment value
            filterPayment.forEach(r => {
                if (r.checked) {
                    paymentValue = r.value;
                }

                // Reset payment value if "All" is selected
                if (paymentValue === 'all') {
                    paymentValue = '';
                }
            });

            // Build filter string from filter options
            const filterString = monthValue + ' ' + paymentValue;

            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            datatable.search(filterString).draw();
        });
    }

// Edit customer
var handleEditRows = () => {
    // Select all edit buttons
    const editButtons = document.querySelectorAll('[data-kt-fiel-table-filter="edit_row"]');

    editButtons.forEach(button => {
        // Edit button on click
        button.addEventListener('click', function (e) {
            e.preventDefault();

            // Get fiel ID
            const fielId = button.getAttribute('data-id');

            // Show loading
            Swal.fire({
                title: 'Carregando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch fiel data
            let editUrl;
            if (window.fieisRoutes && window.fieisRoutes.edit) {
                editUrl = window.fieisRoutes.edit.replace(':id', fielId);
            } else {
                // Fallback: construir URL baseada na rota atual
                const basePath = window.location.pathname.split('/').slice(0, -1).join('/') || window.location.pathname;
                editUrl = `${basePath}/${fielId}/edit`;
            }
            fetch(editUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success && data.data) {
                    // Fill form with data
                    fillFormWithData(data.data);
                    
                    // Change form action to update
                    const form = document.querySelector('#kt_modal_new_ticket_form');
                    if (form) {
                        let updateUrl;
                        if (window.fieisRoutes && window.fieisRoutes.update) {
                            updateUrl = window.fieisRoutes.update.replace(':id', fielId);
                        } else {
                            // Fallback: construir URL baseada na rota atual
                            const basePath = window.location.pathname.split('/').slice(0, -1).join('/') || window.location.pathname;
                            updateUrl = `${basePath}/${fielId}`;
                        }
                        form.action = updateUrl;
                        form.method = 'POST';
                        // Add method spoofing for PUT
                        let methodInput = form.querySelector('input[name="_method"]');
                        if (!methodInput) {
                            methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            form.appendChild(methodInput);
                        }
                        methodInput.value = 'PUT';
                    }
                    
                    // Update modal title
                    const modalTitle = document.querySelector('#kt_modal_new_ticket h1');
                    if (modalTitle) {
                        modalTitle.textContent = 'Editar Fiel';
                    }
                    
                    // Open modal
                    const modal = new bootstrap.Modal(document.querySelector('#kt_modal_new_ticket'));
                    modal.show();
                } else {
                    Swal.fire({
                        text: 'Erro ao carregar dados do fiel.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'OK, entendi!',
                        customClass: {
                            confirmButton: 'btn fw-bold btn-primary',
                        }
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    text: 'Erro ao carregar dados do fiel.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary',
                    }
                });
            });
        });
    });
};

// Fill form with data
var fillFormWithData = (data) => {
    const form = document.querySelector('#kt_modal_new_ticket_form');
    if (!form) return;

    // Basic fields
    if (data.nome_completo) form.querySelector('[name="nome_completo"]').value = data.nome_completo;
    if (data.data_nascimento) form.querySelector('[name="data_nascimento"]').value = data.data_nascimento;
    if (data.sexo) form.querySelector('[name="sexo"]').value = data.sexo;
    if (data.cpf) form.querySelector('[name="cpf"]').value = data.cpf;
    if (data.rg) form.querySelector('[name="rg"]').value = data.rg;
    if (data.profissao) form.querySelector('[name="profissao"]').value = data.profissao;
    if (data.estado_civil) form.querySelector('[name="estado_civil"]').value = data.estado_civil;
    if (data.telefone) form.querySelector('[name="telefone"]').value = data.telefone;
    if (data.telefone_secundario) form.querySelector('[name="telefone_secundario"]').value = data.telefone_secundario;
    if (data.email) form.querySelector('[name="email"]').value = data.email;
    if (data.cep) form.querySelector('[name="cep"]').value = data.cep;
    if (data.endereco) form.querySelector('[name="endereco"]').value = data.endereco;
    if (data.bairro) form.querySelector('[name="bairro"]').value = data.bairro;
    if (data.cidade) form.querySelector('[name="cidade"]').value = data.cidade;
    if (data.estado) form.querySelector('[name="estado"]').value = data.estado;

    // Notifications
    if (data.notifications && Array.isArray(data.notifications)) {
        data.notifications.forEach(notification => {
            const checkbox = form.querySelector(`[name="notifications[]"][value="${notification}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }

    // Dizimista
    const dizimistaSwitch = form.querySelector('[name="dizimista"]');
    if (dizimistaSwitch) {
        dizimistaSwitch.checked = data.dizimista || false;
    }

    // Avatar
    if (data.avatar) {
        const avatarWrapper = form.querySelector('.image-input-wrapper');
        if (avatarWrapper) {
            // Construir URL do avatar
            let avatarUrl = data.avatar;
            if (!avatarUrl.startsWith('http') && !avatarUrl.startsWith('/')) {
                avatarUrl = '/' + avatarUrl;
            }
            // Se for um caminho relativo, usar route helper ou construir URL
            if (avatarUrl.startsWith('avatars/') || avatarUrl.startsWith('storage/')) {
                avatarUrl = '/storage/' + avatarUrl.replace('storage/', '');
            }
            avatarWrapper.style.backgroundImage = `url('${avatarUrl}')`;
        }
    }
    
    // Trigger change events for select2 if needed
    const estadoSelect = form.querySelector('[name="estado"]');
    if (estadoSelect && typeof $(estadoSelect).select2 === 'function') {
        $(estadoSelect).trigger('change');
    }
};

// Delete customer
var handleDeleteRows = () => {
    // Select all delete buttons
    const deleteButtons = document.querySelectorAll('[data-kt-customer-table-filter="delete_row"]');

    deleteButtons.forEach(button => {
        // Delete button on click
        button.addEventListener('click', function (e) {
            e.preventDefault();

            // Select parent row
            const parent = e.target.closest('tr');

            // Get bank ID and name
            const bancoId = button.getAttribute('data-id'); // Use data-id directly from the button
            const customerName = parent.querySelector('td[data-banco-code]').innerText.trim(); // Nome do banco

            // SweetAlert2 pop up
            Swal.fire({
                text: "Tem certeza de que deseja excluir " + customerName + "?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, Exclua!",
                cancelButtonText: "Não, Cancelell",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    // Perform AJAX request to delete the record
                    axios.delete(`/cadastroBancos/${bancoId}`)
                        .then(function (response) {
                            Swal.fire({
                                text: "Você excluiu " + customerName + "!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "OK, entendi!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                // Remove current row from table
                                parent.remove(); // Remover a linha diretamente
                            });
                        })
                        .catch(function (error) {
                            Swal.fire({
                                text: "Houve um erro ao excluir " + customerName + ".",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "OK, entendi!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: customerName + " não foi excluído.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, obrigado!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });
    });
};

// Initialize the delete functionality
handleDeleteRows();



    // Reset Filter
    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-customer-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset month
            filterMonth.val(null).trigger('change');

            // Reset payment type
            filterPayment[0].checked = true;

            // Reset datatable --- official docs reference: https://datatables.net/reference/api/search()
            datatable.search('').draw();
        });
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
                        confirmButtonText: "OK, entendi!",
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
                        confirmButtonText: "OK, entendi!",
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
            table = document.querySelector('#kt_customers_table');

            if (!table) {
                return;
            }

            initCustomerList();
            initToggleToolbar();
            handleSearchDatatable();
            handleFilterDatatable();
            handleEditRows();
            handleDeleteRows();
            handleResetForm();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTCustomersList.init();
});
