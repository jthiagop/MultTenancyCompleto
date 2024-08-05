"use strict";

var KTUsersList = function () {
    // Define shared variables
    var table = document.getElementById('kt_table_users');
    var datatable;
    var toolbarBase;
    var toolbarSelected;
    var selectedCount;

    // Private functions
    var initUserTable = function () {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const lastLogin = dateRow[2].innerText.toLowerCase(); // Obtenha a última hora de login
            let timeCount = 0;
            let timeFormat = 'minutes';

            // Determine o formato de data e hora -- adicione mais formatos, se necessário
            if (lastLogin.includes('yesterday')) {
                timeCount = 1;
                timeFormat = 'days';
            } else if (lastLogin.includes('mins')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'minutes';
            } else if (lastLogin.includes('hours')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'hours';
            } else if (lastLogin.includes('days')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'days';
            } else if (lastLogin.includes('weeks')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'weeks';
            }

            // Subtraia data/hora de hoje -- mais informações sobre subtração de data e hora do moment: https://momentjs.com/docs/#/durations/subtract/
            const realDate = moment().subtract(timeCount, timeFormat).format();

            // Insira a data real no atributo de último login
            dateRow[2].setAttribute('data-order', realDate);

            // Defina a data real para a coluna de junção
            const joinedDate = moment(dateRow[2].innerHTML, "DD MMM YYYY, LT").format("DD/MM/YYYY"); // selecione a data da 5ª coluna na tabela
            dateRow[2].setAttribute('data-order', joinedDate);
        });

        // Inicialize o datatable --- mais informações em datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [[1, 'desc']], // Ordena pela primeira coluna (índice 0) em ordem decrescente
            "pageLength": 10,
            "lengthChange": false,
            'columnDefs': [
                { orderable: false, targets: 0 }, // Desabilita ordenação na coluna 0 (checkbox)
                { orderable: false, targets: 6 }, // Desabilita ordenação na coluna 6 (ações)
            ]
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        const filterForm = document.querySelector('[data-kt-user-table-filter="form"]');
        const filterButton = filterForm.querySelector('[data-kt-user-table-filter="filter"]');
        const selectOptions = filterForm.querySelectorAll('select');

        // Filter datatable on submit
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

    // Reset Filter
    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-user-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Select filter options
            const filterForm = document.querySelector('[data-kt-user-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');

            // Reset select2 values -- more info: https://select2.org/programmatic-control/add-select-clear-items
            selectOptions.forEach(select => {
                $(select).val('').trigger('change');
            });

            // Reset datatable --- official docs reference: https://datatables.net/reference/api/search()
            datatable.search('').draw();
        });
    }


    // Delete subscirption
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-users-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                // Get user name
                const userName = parent.querySelectorAll('td')[1].querySelectorAll('a')[1].innerText;

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to delete " + userName + "?",
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
                            text: "You have deleted " + userName + "!.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function () {
                            // Remove current row
                            datatable.row($(parent)).remove().draw();
                        }).then(function () {
                            // Detect checked checkboxes
                            toggleToolbars();
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

    var KTCustomersList = function () {
        var datatable;
        var table;
        var toolbarBase;
        var toolbarSelected;
        var selectedCount;

        var initCustomerList = function () {
            datatable = $(table).DataTable({
                "info": false,
                'order': [],
                'columnDefs': [
                    { orderable: false, targets: 0 },
                    { orderable: false, targets: 8 },
                ]
            });

            datatable.on('draw', function () {
                handleDeleteRows();
            });
        }

        var handleDeleteRows = () => {
            const checkboxes = table.querySelectorAll('[type="checkbox"]');
            const deleteSelected = document.querySelector('[data-kt-user-table-select="delete_selected"]');

            deleteSelected.addEventListener('click', function () {
                Swal.fire({
                    text: "Tem certeza de que deseja excluir os registros selecionados?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Sim, Exclua!",
                    cancelButtonText: "Não, Cancele",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        let selectedIds = [];
                        checkboxes.forEach(c => {
                            if (c.checked && c.value != 1) {  // Exclui o checkbox do cabeçalho
                                selectedIds.push(c.value);
                            }
                        });

                        console.log('Selected IDs:', selectedIds);

                        $.ajax({
                            url: '{{ route("caixas.destroySelected") }}',
                            type: 'DELETE',
                            data: {
                                ids: selectedIds,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    text: "Você excluiu o registro com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, obrigado!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    checkboxes.forEach(c => {
                                        if (c.checked && c.value != 1) {
                                            datatable.row($(c.closest('tbody tr'))).remove().draw();
                                        }
                                    });

                                    const headerCheckbox = table.querySelectorAll('[type="checkbox"]')[0];
                                    headerCheckbox.checked = false;

                                    toggleToolbars();
                                });
                            },
                            error: function(response) {
                                Swal.fire({
                                    text: "Houve um erro ao excluir os registros.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, obrigado!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: "Os registros selecionados não foram excluídos.",
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
        };

        var toggleToolbars = () => {
            const toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
            const toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
            const selectedCount = document.querySelector('[data-kt-user-table-select="selected_count"]');
            const checkboxes = table.querySelectorAll('tbody [type="checkbox"]');

            let checkedState = false;
            let count = 0;

            checkboxes.forEach(c => {
                if (c.checked) {
                    checkedState = true;
                    count++;
                }
            });

            if (checkedState) {
                selectedCount.innerHTML = count;
                toolbarBase.classList.add('d-none');
                toolbarSelected.classList.remove('d-none');
            } else {
                toolbarBase.classList.remove('d-none');
                toolbarSelected.classList.add('d-none');
            }
        }

        return {
            init: function () {
                table = document.querySelector('#kt_table_users');

                if (!table) {
                    return;
                }

                initCustomerList();
                handleDeleteRows();
                toggleToolbars();
            }
        }
    }();

    KTUtil.onDOMContentLoaded(function () {
        KTCustomersList.init();
    });

    // Call the function to initialize the toolbar
    initToggleToolbar();

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
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

    return {
        // Public functions
        init: function () {
            if (!table) {
                return;
            }

            initUserTable();
            initToggleToolbar();
            handleSearchDatatable();
            handleResetForm();
            handleDeleteRows();
            handleFilterDatatable();

        }
    }
}();



// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTUsersList.init();
});


var KTULancamentoList = function () {
    // Defina variáveis compartilhadas
    var table = document.getElementById('kt_table_lancamento');
    var datatable;
    var toolbarBase;
    var toolbarSelected;
    var selectedCount;

    // Funções privadas
    var initUserTable = function () {
        // Defina a ordem dos dados da data
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const lastLogin = dateRow[3].innerText.toLowerCase(); // Obtenha a última hora de login
            let timeCount = 0;
            let timeFormat = 'minutes';

            // Determine o formato de data e hora -- adicione mais formatos, se necessário
            if (lastLogin.includes('yesterday')) {
                timeCount = 1;
                timeFormat = 'days';
            } else if (lastLogin.includes('mins')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'minutes';
            } else if (lastLogin.includes('hours')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'hours';
            } else if (lastLogin.includes('days')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'days';
            } else if (lastLogin.includes('weeks')) {
                timeCount = parseInt(lastLogin.replace(/\D/g, ''));
                timeFormat = 'weeks';
            }

            // Subtraia data/hora de hoje -- mais informações sobre subtração de data e hora do moment: https://momentjs.com/docs/#/durations/subtract/
            const realDate = moment().subtract(timeCount, timeFormat).format();

            // Insira a data real no atributo de último login
            dateRow[3].setAttribute('data-order', realDate);

            // Defina a data real para a coluna de junção
            const joinedDate = moment(dateRow[5].innerHTML, "DD MMM YYYY, LT").format(); // selecione a data da 5ª coluna na tabela
            dateRow[5].setAttribute('data-order', joinedDate);
        });

        // Inicialize o datatable --- mais informações em datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [[1, 'desc']], // Ordena pela primeira coluna (índice 0) em ordem decrescente
            "pageLength": 10,
            "lengthChange": false,
            'columnDefs': [
                { orderable: false, targets: 0 }, // Desabilita ordenação na coluna 0 (checkbox)
                { orderable: false, targets: 6 }, // Desabilita ordenação na coluna 6 (ações)
            ]
        });

        // Re-inicialize funções a cada redesenho da tabela -- mais informações: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleDeleteRows();
            toggleToolbars();
        });
    }

    // Pesquisar no Datatable --- referência nos docs oficiais: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Filtrar no Datatable
    var handleFilterDatatable = () => {
        // Selecione as opções de filtro
        const filterForm = document.querySelector('[data-kt-user-table-filter="form"]');
        const filterButton = filterForm.querySelector('[data-kt-user-table-filter="filter"]');
        const selectOptions = filterForm.querySelectorAll('select');

        // Filtrar no datatable ao enviar
        filterButton.addEventListener('click', function () {
            var filterString = '';

            // Obtenha os valores de filtro
            selectOptions.forEach((item, index) => {
                if (item.value && item.value !== '') {
                    if (index !== 0) {
                        filterString += ' ';
                    }

                    // Construa opções de valor de filtro
                    filterString += item.value;
                }
            });

            // Filtrar no datatable --- referência nos docs oficiais: https://datatables.net/reference/api/search()
            datatable.search(filterString).draw();
        });
    }

    // Redefinir Filtro
    var handleResetForm = () => {
        // Selecione o botão de redefinir
        const resetButton = document.querySelector('[data-kt-user-table-filter="reset"]');

        // Redefinir datatable
        resetButton.addEventListener('click', function () {
            // Selecione as opções de filtro
            const filterForm = document.querySelector('[data-kt-user-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');

            // Redefinir valores select2 -- mais informações: https://select2.org/programmatic-control/add-select-clear-items
            selectOptions.forEach(select => {
                $(select).val('').trigger('change');
            });

            // Redefinir datatable --- referência nos docs oficiais: https://datatables.net/reference/api/search()
            datatable.search('').draw();
        });
    }

    // Excluir subscrição
    var handleDeleteRows = () => {
        // Selecione todos os botões de exclusão
        const deleteButtons = table.querySelectorAll('[data-kt-users-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Botão de exclusão ao clicar
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Selecione a linha pai
                const parent = e.target.closest('tr');

                // Obtenha o nome do usuário
                const userName = parent.querySelectorAll('td')[1].querySelectorAll('a')[1].innerText;

                // Pop-up do SweetAlert2 --- referência nos docs oficiais: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Tem certeza de que deseja excluir " + userName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Sim, exclua!",
                    cancelButtonText: "Não, cancele",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            text: "Você excluiu " + userName + "!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function () {
                            // Remova a linha atual
                            datatable.row($(parent)).remove().draw();
                        }).then(function () {
                            // Detecte checkboxes marcados
                            toggleToolbars();
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: userName + " não foi excluído.",
                            icon: "error",
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

    // Inicialize a barra de ferramentas de alternância
    var initToggleToolbar = () => {
        // Alternar a barra de ferramentas de ação selecionada
        // Selecione todos os checkboxes
        const checkboxes = table.querySelectorAll('[type="checkbox"]');

        // Selecione elementos
        toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
        toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
        selectedCount = document.querySelector('[data-kt-user-table-select="selected_count"]');
        const deleteSelected = document.querySelector('[data-kt-user-table-select="delete_selected"]');

        // Alternar barra de ferramentas de exclusão selecionada
        checkboxes.forEach(c => {
            // Evento de clique no checkbox
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        // Excluir linhas selecionadas
        deleteSelected.addEventListener('click', function () {
            // Pop-up do SweetAlert2 --- referência nos docs oficiais: https://sweetalert2.github.io/
            Swal.fire({
                text: "Tem certeza de que deseja excluir os registros selecionados?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, Exclua!",
                cancelButtonText: "Não, Cancele",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    // Obtenha os IDs selecionados
                    let selectedIds = [];
                    checkboxes.forEach(c => {
                        if (c.checked) {
                            selectedIds.push(c.value);
                        }
                    });

                    // Exclua os registros selecionados no datatable
                    datatable.rows().every(function (rowIdx, tableLoop, rowLoop) {
                        var data = this.data();
                        if (selectedIds.includes(data[0])) { // Verifique se o ID está na lista de IDs selecionados
                            this.remove(); // Remova a linha
                        }
                    });
                    datatable.draw(); // Atualize a tabela

                    Swal.fire({
                        text: "Registros excluídos com sucesso!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Nenhum registro foi excluído.",
                        icon: "error",
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

    // Alternar barras de ferramentas
    var toggleToolbars = () => {
        const checkedCheckboxes = table.querySelectorAll('tbody [type="checkbox"]:checked');
        const checkedCount = checkedCheckboxes.length;

        if (checkedCount > 0) {
            // Exibir barra de ferramentas selecionada
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
            selectedCount.innerText = checkedCount;
        } else {
            // Exibir barra de ferramentas base
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

    // Inicialização do código
    return {
        init: function () {
            if (!table) {
                return;
            }
            initUserTable();
            handleSearchDatatable();
            handleFilterDatatable();
            handleResetForm();
            initToggleToolbar();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTULancamentoList.init();
});
