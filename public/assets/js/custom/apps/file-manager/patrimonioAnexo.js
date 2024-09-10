"use strict";

// Class definition
var KTFileManagerList = function () {
    // Define shared variables
    var datatable;
    var table

    // Define template element variables
    var uploadTemplate;
    var renameTemplate;
    var actionTemplate;
    var checkboxTemplate;


    // Private functions
    const initTemplates = () => {
        uploadTemplate = document.querySelector('[data-kt-filemanager-template="upload"]');
        renameTemplate = document.querySelector('[data-kt-filemanager-template="rename"]');
        actionTemplate = document.querySelector('[data-kt-filemanager-template="action"]');
        checkboxTemplate = document.querySelector('[data-kt-filemanager-template="checkbox"]');
    }

    const initDatatable = () => {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const dateCol = dateRow[3]; // select date from 4th column in table
            const realDate = moment(dateCol.innerHTML, "DD MMM YYYY, LT").format();
            dateCol.setAttribute('data-order', realDate);
        });

        const foldersListOptions = {
            "info": false,
            'order': [],
            "scrollY": "700px",
            "scrollCollapse": true,
            "paging": false,
            'ordering': false,
            'language': {
                emptyTable: `<div class="d-flex flex-column flex-center">
                <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-400px" />
                <div class="fs-1 fw-bolder text-dark">No items found.</div>
                <div class="fs-6">enviar um novo arquivo!</div>
                </div>`
            }
        };


        const filesListOptions = {
            "info": true,
            'order': [],
            'pageLength': 10,
            "lengthChange": true,
            'ordering': true,
            'language': {
                emptyTable: `<div class="d-flex flex-column flex-center">
                    <img src="${hostUrl}/media/illustrations/sketchy-1/5.png" class="mw-400px" />
                    <div class="fs-1 fw-bolder text-dark mb-4">No items found.</div>
                    <div class="fs-6">Comece enviando um novo arquivo!</div>
                </div>`
            },
            conditionalPaging: true
        };



        // Define datatable options to load
        var loadOptions;
        if (table.getAttribute('data-kt-filemanager-table') === 'folders') {
            loadOptions = foldersListOptions;
        } else {
            loadOptions = filesListOptions;
        }

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable(loadOptions);

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleDeleteRows();
            toggleToolbars();
            resetNewFolder();
            KTMenu.createInstances();
            initCopyLink();
            countTotalItems();
            handleRename();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-filemanager-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Delete customer
// Função para excluir arquivos (anexo)
const handleDeleteRows = () => {
    // Seleciona todos os botões de exclusão
    const deleteButtons = table.querySelectorAll('[data-kt-filemanager-table-filter="delete_row"]');

    deleteButtons.forEach(d => {
        // Evento de clique no botão de exclusão
        d.addEventListener('click', function (e) {
            e.preventDefault();

            // Seleciona a linha pai (tr)
            const parent = e.target.closest('tr');

            // Obtém o nome do arquivo e o ID do arquivo (supondo que o ID esteja como um atributo data)
            const fileName = parent.querySelectorAll('td')[1].innerText;

            const fileId = parent.getAttribute('data-file-id'); // Supondo que o fileId esteja presente
            const url = deleteUrl.replace(':id', fileId);  // Substitui o placeholder ':id' pelo fileId

            // Exibe uma confirmação com SweetAlert2
            Swal.fire({
                text: "Tem certeza de que deseja excluir o arquivo: " + fileName + "?",
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
                    // Faz a requisição AJAX para excluir o arquivo
// Agora use a URL no fetch
fetch(url, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                text: "Você excluiu o arquivo " + fileName + "!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                // Remove a linha correspondente da tabela
                                datatable.row($(parent)).remove().draw();
                            });
                        } else {
                            Swal.fire({
                                text: "Erro ao excluir o arquivo: " + data.message,
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
                            text: "Ocorreu um erro ao excluir o arquivo.",
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
                        text: fileName + " não foi excluído.",
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
    });
}


    // Init toggle toolbar
    const initToggleToolbar = () => {
        // Toggle selected action toolbar
        // Select all checkboxes
        var checkboxes = table.querySelectorAll('[type="checkbox"]');
        if (table.getAttribute('data-kt-filemanager-table') === 'folders') {
            checkboxes = document.querySelectorAll('#kt_file_manager_list_wrapper [type="checkbox"]');
        }

        // Select elements
        const deleteSelected = document.querySelector('[data-kt-filemanager-table-select="delete_selected"]');

        // Toggle delete selected toolbar
        checkboxes.forEach(c => {
            // Checkbox on click event
            c.addEventListener('click', function () {
                console.log(c);
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        // Deleted selected rows
        deleteSelected.addEventListener('click', function () {
            // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
            Swal.fire({
                text: "Are you sure you want to delete selected files or folders?",
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
                        text: "You have deleted all selected  files or folders!.",
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
                        text: "Selected  files or folders was not deleted.",
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
        const toolbarBase = document.querySelector('[data-kt-filemanager-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-filemanager-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-filemanager-table-select="selected_count"]');

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

    // Handle new folder
    const handleNewFolder = () => {
        // Select button
        const newFolder = document.getElementById('kt_file_manager_new_folder');

        // Handle click action
        newFolder.addEventListener('click', e => {
            e.preventDefault();

            // Ignore if input already exist
            if (table.querySelector('#kt_file_manager_new_folder_row')) {
                return;
            }

            // Add new blank row to datatable
            const tableBody = table.querySelector('tbody');
            const rowElement = uploadTemplate.cloneNode(true); // Clone template markup
            tableBody.prepend(rowElement);

            // Define template interactive elements
            const rowForm = rowElement.querySelector('#kt_file_manager_add_folder_form');
            const rowButton = rowElement.querySelector('#kt_file_manager_add_folder');
            const cancelButton = rowElement.querySelector('#kt_file_manager_cancel_folder');
            const folderIcon = rowElement.querySelector('.svg-icon-2x');
            const rowInput = rowElement.querySelector('[name="new_folder_name"]');

            // Define validator
            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                rowForm,
                {
                    fields: {
                        'new_folder_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Folder name is required'
                                }
                            }
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.fv-row',
                            eleInvalidClass: '',
                            eleValidClass: ''
                        })
                    }
                }
            );

            // Handle add new folder button
            rowButton.addEventListener('click', e => {
                e.preventDefault();

                // Activate indicator
                rowButton.setAttribute("data-kt-indicator", "on");

                // Validate form before submit
                if (validator) {
                    validator.validate().then(function (status) {
                        console.log('validated!');

                        if (status == 'Valid') {
                            // Simulate process for demo only
                            setTimeout(function () {
                                // Create folder link
                                const folderLink = document.createElement('a');
                                const folderLinkClasses = ['text-gray-800', 'text-hover-primary'];
                                folderLink.setAttribute('href', '?page=apps/file-manager/blank');
                                folderLink.classList.add(...folderLinkClasses);
                                folderLink.innerText = rowInput.value;

                                const newRow = datatable.row.add({
                                    'checkbox': checkboxTemplate.innerHTML,
                                    'name': folderIcon.outerHTML + folderLink.outerHTML,
                                    "size": '-',
                                    "date": '-',
                                    "date": '-',
                                    'action': actionTemplate.innerHTML
                                }).node();
                                $(newRow).find('td').eq(4).attr('data-kt-filemanager-table', 'action_dropdown');
                                $(newRow).find('td').eq(4).addClass('text-end'); // Add custom class to last 'td' element --- more info: https://datatables.net/forums/discussion/22341/row-add-cell-class

                                // Re-sort datatable to allow new folder added at the top
                                var index = datatable.row(0).index(),
                                    rowCount = datatable.data().length - 1,
                                    insertedRow = datatable.row(rowCount).data(),
                                    tempRow;

                                for (var i = rowCount; i > index; i--) {
                                    tempRow = datatable.row(i - 1).data();
                                    datatable.row(i).data(tempRow);
                                    datatable.row(i - 1).data(insertedRow);
                                }

                                toastr.options = {
                                    "closeButton": true,
                                    "debug": false,
                                    "newestOnTop": false,
                                    "progressBar": false,
                                    "positionClass": "toastr-top-right",
                                    "preventDuplicates": false,
                                    "showDuration": "300",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                };

                                toastr.success(rowInput.value + ' was created!');

                                // Disable indicator
                                rowButton.removeAttribute("data-kt-indicator");

                                // Reset input
                                rowInput.value = '';

                                datatable.draw(false);

                            }, 2000);
                        } else {
                            // Disable indicator
                            rowButton.removeAttribute("data-kt-indicator");
                        }
                    });
                }
            });

            // Handle cancel new folder button
            cancelButton.addEventListener('click', e => {
                e.preventDefault();

                // Activate indicator
                cancelButton.setAttribute("data-kt-indicator", "on");

                setTimeout(function () {
                    // Disable indicator
                    cancelButton.removeAttribute("data-kt-indicator");

                    // Toggle toastr
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toastr-top-right",
                        "preventDuplicates": false,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };

                    toastr.error('Cancelled new folder creation');
                    resetNewFolder();
                }, 1000);
            });
        });
    }

    // Reset add new folder input
    const resetNewFolder = () => {
        const newFolderRow = table.querySelector('#kt_file_manager_new_folder_row');

        if (newFolderRow) {
            newFolderRow.parentNode.removeChild(newFolderRow);
        }
    }

    // Handle rename file or folder
    const handleRename = () => {
        const renameButton = table.querySelectorAll('[data-kt-filemanager-table="rename"]');

        renameButton.forEach(button => {
            button.addEventListener('click', renameCallback);
        });
    }

// Callback de renomear
const renameCallback = (e) => {
    e.preventDefault();

    // Definir valor compartilhado
    let valorNome;

    // Interromper renomeação se já houver um input existente
    if (table.querySelectorAll('#kt_file_manager_rename_input').length > 0) {
        Swal.fire({
            text: "Há uma entrada não salva. Por favor, salve ou cancele o item atual",
            icon: "warning",
            buttonsStyling: false,
            confirmButtonText: "Ok, entendi!",
            customClass: {
                confirmButton: "btn fw-bold btn-danger"
            }
        });

        return;
    }

    // Selecionar a linha pai
    const parent = e.target.closest('tr');

    // Obter a coluna do nome
    const nameCol = parent.querySelectorAll('td')[1];
    const colIcon = nameCol.querySelector('.svg-icon');
    valorNome = nameCol.innerText;

    // Obter o ID do arquivo (assumindo que o ID do arquivo esteja em um atributo data como 'data-id')
    const fileId = parent.getAttribute('data-id');

    // Definir o template do input de renomeação
    const renameInput = renameTemplate.cloneNode(true);
    renameInput.querySelector('#kt_file_manager_rename_folder_icon').innerHTML = colIcon.outerHTML;

    // Trocar o conteúdo da coluna atual pelo template do input
    nameCol.innerHTML = renameInput.innerHTML;

    // Definir o valor do input com o nome atual do arquivo/pasta
    parent.querySelector('#kt_file_manager_rename_input').value = valorNome;

    // Validador de renomear arquivo/pasta
    var renameValidator = FormValidation.formValidation(
        nameCol,
        {
            fields: {
                'rename_folder_name': {
                    validators: {
                        notEmpty: {
                            message: 'O nome é obrigatório!'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: ''
                })
            }
        }
    );

    // Ação do botão de input de renomear
    const renameInputButton = document.querySelector('#kt_file_manager_rename_folder');
    renameInputButton.addEventListener('click', e => {
        e.preventDefault();

        // Detectar se é válido
        if (renameValidator) {
            renameValidator.validate().then(function (status) {
                console.log('validado!');

                if (status === 'Valid') {
                    // Confirmação de pop-up
                    Swal.fire({
                        text: "Você tem certeza que deseja renomear " + valorNome + "?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Sim, renomear!",
                        cancelButtonText: "Não, cancelar",
                        customClass: {
                            confirmButton: "btn fw-bold btn-danger",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            // Obter o novo nome do arquivo/pasta
                            const novoValor = document.querySelector('#kt_file_manager_rename_input').value;

                            // Enviar a requisição AJAX para atualizar o nome no backend
                            axios.patch(`/patrimonioAnexo/${fileId}`, {
                                nome_arquivo: novoValor,
                                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRF token
                            })
                            .then(function (response) {
                                Swal.fire({
                                    text: "Você renomeou " + valorNome + " para " + novoValor + "!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, entendi!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    // Novo template de dados da coluna
                                    const newData = `<div class="d-flex align-items-center">
                                        ${colIcon.outerHTML}
                                        <a href="?page=apps/file-manager/files/" class="text-gray-800 text-hover-primary">${novoValor}</a>
                                    </div>`;

                                    // Atualizar a tabela com o novo conteúdo
                                    datatable.cell($(nameCol)).data(newData).draw();
                                });
                            })
                            .catch(function (error) {
                                Swal.fire({
                                    text: "Houve um erro ao renomear o arquivo.",
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
                                text: valorNome + " não foi renomeado.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    });
                }
            });
        }
    });

    // Cancelar renomeação
    const cancelInputButton = document.querySelector('#kt_file_manager_rename_folder_cancel');
    cancelInputButton.addEventListener('click', e => {
        e.preventDefault();

        // Simulação de processo apenas para demonstração
        cancelInputButton.setAttribute("data-kt-indicator", "on");

        setTimeout(function () {
            const revertTemplate = `<div class="d-flex align-items-center">
                ${colIcon.outerHTML}
                <a href="?page=apps/file-manager/files/" class="text-gray-800 text-hover-primary">${valorNome}</a>
            </div>`;

            // Remover spinner
            cancelInputButton.removeAttribute("data-kt-indicator");

            // Atualizar a tabela com o conteúdo original
            datatable.cell($(nameCol)).data(revertTemplate).draw();

            toastr.error('Renomeação cancelada');
        }, 1000);
    });
}


// Defina a função initDropzone fora do DOMContentLoaded para garantir que ela esteja no escopo global
const initDropzone = () => {
    // Obtém o formulário e a URL de upload do atributo action
    const form = document.querySelector("#kt_modal_upload_form");

    if (form) {
        // Obtém o valor do atributo action (URL) do formulário
        const uploadUrl = form.getAttribute("action");

        // Obtém o elemento Dropzone
        const id = "#kt_modal_upload_dropzone";
        const dropzone = document.querySelector(id);

        // Configura o template de preview
        var previewNode = dropzone.querySelector(".dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        Dropzone.autoDiscover = false;

        // Inicializa o Dropzone com a URL do formulário
        var myDropzone = new Dropzone(id, {
            url: uploadUrl,  // Passa a URL do action do formulário
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            parallelUploads: 10,
            previewTemplate: previewTemplate,
            maxFilesize: 1, // Max filesize in MB
            previewsContainer: ".dropzone-items",
            clickable: ".dropzone-select",
            autoProcessQueue: false // Para processar manualmente ao clicar no botão de upload
        });

        // Evento "sending" para adicionar dados extras à requisição
        myDropzone.on("sending", function (file, xhr, formData) {
            // Adiciona o patrimonio_id ao formData
            const patrimonioId = document.querySelector('#patrimonio_id').value; // Obtém o valor do campo patrimonio_id
            formData.append('patrimonio_id', patrimonioId);
        });

        // Evento de arquivo adicionado
        myDropzone.on("addedfile", function (file) {
            // Hook para o botão de start
            file.previewElement.querySelector(id + " .dropzone-start").onclick = function () {
                // Simulação de progresso para demonstração
                const progressBar = file.previewElement.querySelector('.progress-bar');
                progressBar.style.opacity = "1";
                var width = 1;
                var timer = setInterval(function () {
                    if (width >= 100) {
                        myDropzone.emit("success", file);
                        myDropzone.emit("complete", file);
                        clearInterval(timer);
                    } else {
                        width++;
                        progressBar.style.width = width + '%';
                    }
                }, 20);
            };

            const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
            dropzoneItems.forEach(dropzoneItem => {
                dropzoneItem.style.display = '';
            });
            dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
            dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
        });

        // Evento para esconder a barra de progresso quando completo
        myDropzone.on("complete", function (file) {
            const progressBars = dropzone.querySelectorAll('.dz-complete');
            setTimeout(function () {
                progressBars.forEach(progressBar => {
                    progressBar.querySelector('.progress-bar').style.opacity = "0";
                    progressBar.querySelector('.progress').style.opacity = "0";
                    progressBar.querySelector('.dropzone-start').style.opacity = "0";
                });
            }, 300);


        });

        // Botão para processar o upload manualmente
        dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
            myDropzone.processQueue();
        });

        // Botão para remover todos os arquivos
        dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
            Swal.fire({
                text: "Are you sure you would like to remove all files?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, remove it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    dropzone.querySelector('.dropzone-upload').style.display = "none";
                    dropzone.querySelector('.dropzone-remove-all').style.display = "none";
                    myDropzone.removeAllFiles(true);
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your files were not removed!",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        }
                    });
                }
            });
        });

        // Evento para ocultar os ícones de upload quando a fila estiver completa
        myDropzone.on("queuecomplete", function () {
            const uploadIcons = dropzone.querySelectorAll('.dropzone-upload');
            uploadIcons.forEach(uploadIcon => {
                uploadIcon.style.display = "none";
            });
        });

        // Evento para remover arquivos
        myDropzone.on("removedfile", function () {
            if (myDropzone.files.length < 1) {
                dropzone.querySelector('.dropzone-upload').style.display = "none";
                dropzone.querySelector('.dropzone-remove-all').style.display = "none";
            }
        });
    } else {
        console.error("Formulário não encontrado!");
    }
};


// Usa DOMContentLoaded para garantir que o DOM esteja pronto
document.addEventListener("DOMContentLoaded", function() {
    // Chama a função initDropzone após o DOM ser carregado
    initDropzone();
});



    // Init copy link
    const initCopyLink = () => {
        // Select all copy link elements
        const elements = table.querySelectorAll('[data-kt-filemanger-table="copy_link"]');

        elements.forEach(el => {
            // Define elements
            const button = el.querySelector('button');
            const generator = el.querySelector('[data-kt-filemanger-table="copy_link_generator"]');
            const result = el.querySelector('[data-kt-filemanger-table="copy_link_result"]');
            const input = el.querySelector('input');

            // Click action
            button.addEventListener('click', e => {
                e.preventDefault();

                // Reset toggle
                generator.classList.remove('d-none');
                result.classList.add('d-none');

                var linkTimeout;
                clearTimeout(linkTimeout);
                linkTimeout = setTimeout(() => {
                    generator.classList.add('d-none');
                    result.classList.remove('d-none');
                    input.select();
                }, 2000);
            });
        });
    }

    // Handle move to folder
    const handleMoveToFolder = () => {
        const element = document.querySelector('#kt_modal_move_to_folder');
        const form = element.querySelector('#kt_modal_move_to_folder_form');
        const saveButton = form.querySelector('#kt_modal_move_to_folder_submit');
        const moveModal = new bootstrap.Modal(element);

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        var validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'move_to_folder': {
                        validators: {
                            notEmpty: {
                                message: 'Please select a folder.'
                            }
                        }
                    },
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        saveButton.addEventListener('click', e => {
            e.preventDefault();

            saveButton.setAttribute("data-kt-indicator", "on");

            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');

                    if (status == 'Valid') {
                        // Simulate process for demo only
                        setTimeout(function () {

                            Swal.fire({
                                text: "Are you sure you would like to move to this folder",
                                icon: "warning",
                                showCancelButton: true,
                                buttonsStyling: false,
                                confirmButtonText: "Yes, move it!",
                                cancelButtonText: "No, return",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                    cancelButton: "btn btn-active-light"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    form.reset(); // Reset form
                                    moveModal.hide(); // Hide modal

                                    toastr.options = {
                                        "closeButton": true,
                                        "debug": false,
                                        "newestOnTop": false,
                                        "progressBar": false,
                                        "positionClass": "toastr-top-right",
                                        "preventDuplicates": false,
                                        "showDuration": "300",
                                        "hideDuration": "1000",
                                        "timeOut": "5000",
                                        "extendedTimeOut": "1000",
                                        "showEasing": "swing",
                                        "hideEasing": "linear",
                                        "showMethod": "fadeIn",
                                        "hideMethod": "fadeOut"
                                    };

                                    toastr.success('1 item has been moved.');

                                    saveButton.removeAttribute("data-kt-indicator");
                                } else {
                                    Swal.fire({
                                        text: "Your action has been cancelled!.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary",
                                        }
                                    });

                                    saveButton.removeAttribute("data-kt-indicator");
                                }
                            });
                        }, 500);
                    } else {
                        saveButton.removeAttribute("data-kt-indicator");
                    }
                });
            }
        });
    }

    // Count total number of items
    const countTotalItems = () => {
        const counter = document.getElementById('kt_file_manager_items_counter');

        // Count total number of elements in datatable --- more info: https://datatables.net/reference/api/count()
        //counter.innerText = datatable.rows().count() + ' items';
    }

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#kt_file_manager_list');

            if (!table) {
                return;
            }

            initTemplates();
            initDatatable();
            initToggleToolbar();
            handleSearchDatatable();
            handleDeleteRows();
            handleNewFolder();
            initDropzone();
            initCopyLink();
            handleRename();
            handleMoveToFolder();
            countTotalItems();
            KTMenu.createInstances();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTFileManagerList.init();
});
