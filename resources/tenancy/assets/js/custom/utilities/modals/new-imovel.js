"use strict";

// Class definition
var KTModalNewImovel = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	// Init form inputs
	var initForm = function() {
		// Data de aquisição. For more info, please visit the official plugin site: https://flatpickr.js.org/
		var dataAquisicao = $(form.querySelector('[name="data_aquisicao"]'));
		if (dataAquisicao.length) {
			dataAquisicao.flatpickr({
				dateFormat: "d/m/Y",
				locale: "pt",
			});
		}

		// Máscara para CEP
		var cepInput = form.querySelector('[name="cep"]');
		if (cepInput) {
			$(cepInput).mask('00000-000');
		}

		// Máscara para valor
		var valorInput = form.querySelector('[name="valor"]');
		if (valorInput) {
			$(valorInput).mask('#.##0,00', {reverse: true});
		}

		// Máscara para áreas
		var areaTotalInput = form.querySelector('[name="area_total"]');
		if (areaTotalInput) {
			$(areaTotalInput).mask('#.##0,00', {reverse: true});
		}

		var areaPrivativaInput = form.querySelector('[name="area_privativa"]');
		if (areaPrivativaInput) {
			$(areaPrivativaInput).mask('#.##0,00', {reverse: true});
		}

		// Select2 para Centro de Custo
		var centroCustoSelect = $(form.querySelector('[name="centro_custo"]'));
		if (centroCustoSelect.length) {
			centroCustoSelect.on('change', function() {
				validator.revalidateField('centro_custo');
			});
		}

		// Select2 para Estado do Bem
		var estadoBemSelect = $(form.querySelector('[name="estado_bem"]'));
		if (estadoBemSelect.length) {
			estadoBemSelect.on('change', function() {
				validator.revalidateField('estado_bem');
			});
		}

		// Select2 para UF
		var ufSelect = $(form.querySelector('[name="uf"]'));
		if (ufSelect.length) {
			ufSelect.on('change', function() {
				validator.revalidateField('uf');
			});
		}
	}

	// Handle form validation and submittion
	var handleForm = function() {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
					descricao: {
						validators: {
							notEmpty: {
								message: 'A descrição é obrigatória'
							}
						}
					},
					valor: {
						validators: {
							notEmpty: {
								message: 'O valor é obrigatório'
							}
						}
					},
					data_aquisicao: {
						validators: {
							notEmpty: {
								message: 'A data de aquisição é obrigatória'
							},
							date: {
								format: 'DD/MM/YYYY',
								message: 'A data deve estar no formato DD/MM/AAAA'
							}
						}
					},
					centro_custo: {
						validators: {
							notEmpty: {
								message: 'O centro de custo é obrigatório'
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

		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');
						submitButton.disabled = true;

						// Prepara os dados do formulário
						var formData = new FormData(form);

						// Converte FormData para objeto JSON
						var jsonData = {};
						for (var pair of formData.entries()) {
							var key = pair[0];
							var value = pair[1];

							// Trata checkbox depreciar
							if (key === 'depreciar') {
								jsonData[key] = form.querySelector('[name="depreciar"]').checked ? '1' : '0';
							} else {
								jsonData[key] = value;
							}
						}

						// Obtém o token CSRF
						var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

						// Envia via AJAX com JSON
						fetch(form.action, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'Accept': 'application/json',
								'X-CSRF-TOKEN': csrfToken
							},
							body: JSON.stringify(jsonData)
						})
						.then(response => {
							return response.json().then(data => {
								return { status: response.status, data: data };
							});
						})
						.then(result => {
							if (result.status === 200 || result.status === 201 || result.status === 302) {
								// Sucesso
								Swal.fire({
									text: "Imóvel cadastrado com sucesso!",
									icon: "success",
									buttonsStyling: false,
									confirmButtonText: "Ok, entendi!",
									customClass: {
										confirmButton: "btn btn-primary"
									}
								}).then(function (result) {
									if (result.isConfirmed) {
										form.reset();
										modal.hide();
										// Recarrega a página para atualizar a lista
										location.reload();
									}
								});
							} else if (result.status === 422) {
								// Erro de validação
								var errorMessages = [];
								if (result.data.errors) {
									Object.keys(result.data.errors).forEach(function(key) {
										if (Array.isArray(result.data.errors[key])) {
											errorMessages = errorMessages.concat(result.data.errors[key]);
										} else {
											errorMessages.push(result.data.errors[key]);
										}
									});
								}
								if (result.data.message) {
									errorMessages.push(result.data.message);
								}
								if (errorMessages.length === 0) {
									errorMessages.push('Erro de validação. Verifique os campos preenchidos.');
								}

								Swal.fire({
									text: errorMessages.join('\n'),
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Ok, entendi!",
									customClass: {
										confirmButton: "btn btn-primary"
									}
								});
							} else {
								// Outro erro
								var errorMsg = result.data.message || 'Erro ao cadastrar imóvel. Tente novamente.';
								Swal.fire({
									text: errorMsg,
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Ok, entendi!",
									customClass: {
										confirmButton: "btn btn-primary"
									}
								});
							}
						})
						.catch(error => {
							console.error('Erro:', error);
							Swal.fire({
								text: "Erro ao processar a requisição. Tente novamente.",
								icon: "error",
								buttonsStyling: false,
								confirmButtonText: "Ok, entendi!",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							});
						})
						.finally(() => {
							submitButton.removeAttribute('data-kt-indicator');
							submitButton.disabled = false;
						});
					} else {
						// Show error message.
						Swal.fire({
							text: "Por favor, verifique os campos obrigatórios e tente novamente.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, entendi!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
					}
				});
			}
		});

		// Botão Cancelar - mesma lógica do new-target.js
		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
				text: "Tem certeza que deseja cancelar?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Sim, cancelar!",
				cancelButtonText: "Não, voltar",
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
						text: "O formulário não foi cancelado.",
						icon: "info",
						buttonsStyling: false,
						confirmButtonText: "Ok, entendi!",
						customClass: {
							confirmButton: "btn btn-primary",
						}
					});
				}
			});
		});

		// Intercepta o evento de fechamento do modal (botão X e ESC)
		var shouldClose = false;
		modalEl.addEventListener('hide.bs.modal', function (e) {
			// Se já foi confirmado, permite fechar
			if (shouldClose) {
				return;
			}

			// Previne o fechamento automático
			e.preventDefault();

			// Mostra confirmação
			Swal.fire({
				text: "Tem certeza que deseja cancelar?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Sim, cancelar!",
				cancelButtonText: "Não, voltar",
				customClass: {
					confirmButton: "btn btn-primary",
					cancelButton: "btn btn-active-light"
				}
			}).then(function (result) {
				if (result.value) {
					form.reset(); // Reset form
					shouldClose = true; // Marca como confirmado
					modal.hide(); // Fecha o modal
					shouldClose = false; // Reset para próxima vez
				} else if (result.dismiss === 'cancel') {
					Swal.fire({
						text: "O formulário não foi cancelado.",
						icon: "info",
						buttonsStyling: false,
						confirmButtonText: "Ok, entendi!",
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
			modalEl = document.querySelector('#kt_modal_new_imovel');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_imovel_form');
			submitButton = document.getElementById('kt_modal_imovel_submit');
			cancelButton = document.getElementById('kt_modal_imovel_cancel');

			initForm();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalNewImovel.init();
});
