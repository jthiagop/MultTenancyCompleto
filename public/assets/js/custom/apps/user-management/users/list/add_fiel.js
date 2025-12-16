"use strict";

// Class definition
var KTModalNewTicket = function () {
    var table = document.getElementById('kt_table_fiel');
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	// Init form inputs
	var initForm = function() {

		// Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
        // Inicializar o flatpickr com o locale em português do Brasil
        var dueDate = flatpickr(form.querySelector('[name="data_nascimento"]'), {
            enableTime: false,
            dateFormat: "d/m/Y", // Formato de data: DD/MM/YYYY
            locale: "pt", // Definir o locale para português brasileiro
            altInput: true, // Usar um campo alternativo para exibir a data formatada
            altFormat: "d/m/Y", // Formato exibido no campo alternativo
            allowInput: true, // Permite que o usuário digite a data manualmente
            yearSelectorType: "dropdown", // Mostrar o ano como um dropdown
        });


		// Ticket user. For more info, plase visit the official plugin site: https://select2.org/
        $(form.querySelector('[name="user"]')).on('change', function() {
            // Revalidate the field when an option is chosen
            validator.revalidateField('user');
        });

		// Ticket status. For more info, plase visit the official plugin site: https://select2.org/
        $(form.querySelector('[name="status"]')).on('change', function() {
            // Revalidate the field when an option is chosen
            validator.revalidateField('status');
        });
	}

	// Handle form validation and submittion
	var handleForm = function() {
		// Stepper custom navigation

		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validator = FormValidation.formValidation(
			form,
            {
                fields: {
                    nome_completo: {
                        validators: {
                            notEmpty: {
                                message: 'O nome completo é obrigatório'
                            },
                            stringLength: {
                                min: 3,
                                message: 'O nome deve ter pelo menos 3 caracteres'
                            }
                        }
                    },
                    user_email: {
                        validators: {
                            notEmpty: {
                                message: 'O e-mail é obrigatório'
                            },
                            emailAddress: {
                                message: 'Por favor, insira um e-mail válido'
                            }
                        }
                    },
                    data_nascimento: {
                        validators: {
                            notEmpty: {
                                message: 'A data é obrigatória'
                            }
                        }
                    },
                    descricao: {
                        validators: {
                            notEmpty: {
                                message: 'A descrição é obrigatória'
                            },
                            stringLength: {
                                min: 10,
                                message: 'A descrição deve ter pelo menos 10 caracteres'
                            }
                        }
                    },
                    cpf: {
                        validators: {
                            notEmpty: {
                                message: 'O CPF é obrigatório'
                            },
                            regexp: {
                                regexp: /^\d{3}\.\d{3}\.\d{3}\-\d{2}$/,
                                message: 'Por favor, insira um CPF válido'
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
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit()
                }
            }

		);

		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					console.log('validated!');

					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click
						submitButton.disabled = true;

						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');

							// Enable button
							submitButton.disabled = false;

							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
								text: "Form has been successfully submitted!",
								icon: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok, got it!",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							}).then(function (result) {
								if (result.isConfirmed) {
									modal.hide();
								}
							});

							//form.submit(); // Submit form
						}, 2000);
					} else {
						// Show error message.
						Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
					}
				});
			}
		});

		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
				text: "Tem certeza de que deseja cancelar?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Sim, cancele!",
				cancelButtonText: "Não, retorne",
				customClass: {
					confirmButton: "btn btn-primary",
					cancelButton: "btn btn-active-light"
				}
			}).then(function (result) {
				if (result.value) {
					form.reset(); // Reset form
					modal.hide(); // Hide modal
				} else if (result.dismiss === 'cancel') {
					Swal.fire({
						text: "Your form has not been cancelled!.",
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
	}

	return {
		// Public functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#kt_modal_new_ticket');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_new_ticket_form');
			submitButton = document.getElementById('kt_modal_new_ticket_submit');
			cancelButton = document.getElementById('kt_modal_new_ticket_cancel');

			initForm();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalNewTicket.init();
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

    // Delete subscirption
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-fiel-table-filter="delete_row"]');

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


    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-fiel-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        const filterForm = document.querySelector('[data-kt-fiel-table-filter="form"]');
        const filterButton = filterForm.querySelector('[data-kt-fiel-table-filter="filter"]');
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
        const resetButton = document.querySelector('[data-kt-fiel-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Select filter options
            const filterForm = document.querySelector('[data-kt-fiel-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');

            // Reset select2 values -- more info: https://select2.org/programmatic-control/add-select-clear-items
            selectOptions.forEach(select => {
                $(select).val('').trigger('change');
            });

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
    toolbarBase = document.querySelector('[data-kt-fiel-table-toolbar="base"]');
    toolbarSelected = document.querySelector('[data-kt-fiel-table-toolbar="selected"]');
    selectedCount = document.querySelector('[data-kt-fiel-table-select="selected_count"]');
    const deactivateSelected = document.querySelector('[data-kt-fiel-table-select="deactivate_selected"]');

    // Toggle toolbar visibility
    checkboxes.forEach(c => {
        c.addEventListener('click', function () {
            setTimeout(function () {
                toggleToolbars();
            }, 50);
        });
    });

    // Deactivate selected rows
    deactivateSelected.addEventListener('click', function () {
        // Exibir um pop-up de confirmação
        Swal.fire({
            text: "Tem certeza de que deseja desativar os registros selecionados?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Sim, desative!",
            cancelButtonText: "Não, cancele",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function (result) {
            if (result.value) {
                // Obter os IDs selecionados
                let selectedIds = [];
                checkboxes.forEach(c => {
                    if (c.checked) {
                        selectedIds.push(c.value);
                    }
                });

                console.log('Selected IDs:', selectedIds);

                // AJAX request to update the status to 'inativo'
                $.ajax({
                    url: '{{ route("fieis.deactivateSelected") }}',
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        status: 'inativo',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            text: "Os registros foram desativados com sucesso!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, obrigado!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function () {
                            // Atualizar a tabela após a alteração de status
                            checkboxes.forEach(c => {
                                if (c.checked) {
                                    const row = c.closest('tbody tr');
                                    datatable.cell($(row).find('td.status')).data('Inativo').draw();
                                }
                            });

                            // Limpar a seleção
                            const headerCheckbox = table.querySelector('[type="checkbox"]');
                            if (headerCheckbox) headerCheckbox.checked = false;

                            // Re-inicializar as toolbars
                            toggleToolbars();
                            initToggleToolbar();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            text: "Houve um erro ao desativar os registros.",
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
                    text: "Os registros selecionados não foram desativados.",
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

// Filtrar no Datatable
var handleFilterDatatable = () => {
    // Selecione as opções de filtro
    const filterForm = document.querySelector('[data-kt-fiel-table-filter="form"]');
    const filterButton = filterForm.querySelector('[data-kt-fiel-table-filter="filter"]');
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
        const resetButton = document.querySelector('[data-kt-fiel-table-filter="reset"]');

        // Redefinir datatable
        resetButton.addEventListener('click', function () {
            // Selecione as opções de filtro
            const filterForm = document.querySelector('[data-kt-fiel-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');

            // Redefinir valores select2 -- mais informações: https://select2.org/programmatic-control/add-select-clear-items
            selectOptions.forEach(select => {
                $(select).val('').trigger('change');
            });

            // Redefinir datatable --- referência nos docs oficiais: https://datatables.net/reference/api/search()
            datatable.search('').draw();
        });
}


//Buscar por CEP
$(document).ready(function() {
    // Quando o campo CEP perde o foco
    $('#cep').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');

        if (cep !== "") {
            // Verifica se o CEP tem 8 dígitos
            var validacep = /^[0-9]{8}$/;

            if(validacep.test(cep)) {
                // Preenche os campos com "..." enquanto carrega
                $('#logradouro').val('...');
                $('#bairro').val('...');
                $('#localidade').val('...');
                $('#uf').val('...');
                $('#ibge').val('...');
                $('#complemento').val('...');

                // Faz a requisição para a API ViaCEP
                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                    if (!("erro" in dados)) {
                        // Atualiza os campos com os valores da consulta
                        $('#logradouro').val(dados.logradouro);
                        $('#bairro').val(dados.bairro);
                        $('#localidade').val(dados.localidade);
                        $('#uf').val(dados.uf).trigger('change'); // Atualiza o select2
                        $('#ibge').val(dados.ibge);
                        $('#complemento').val(dados.complemento);
                    } else {
                        // CEP não encontrado
                        alert("CEP não encontrado.");
                    }
                });
            } else {
                alert("Formato de CEP inválido.");
            }
        } else {
            // CEP sem valor, limpa o formulário
            limpaFormularioCEP();
        }
    });

    function limpaFormularioCEP() {
        // Limpa valores do formulário de CEP
        $('#logradouro').val('');
        $('#bairro').val('');
        $('#localidade').val('');
        $('#uf').val('').trigger('change');
        $('#ibge').val('');
        $('#complemento').val('');
    }
});

document.getElementById('kt_modal_new_ticket_form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            // Limpar o formulário e fechar o modal
            form.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_new_ticket'));
            modal.hide();
        } else {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    })
    .catch(error => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Ocorreu um erro inesperado',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        console.error('Erro:', error);
    });
});

