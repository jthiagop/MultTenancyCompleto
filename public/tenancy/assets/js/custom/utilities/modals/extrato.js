"use strict";

// Class definition
var KTModalExtrato = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;
	var initialized = false;

	// Load contas financeiras via AJAX
	var loadContas = function(tipo) {
		var select = document.getElementById('extrato_entidade_id');
		if (!select) return;

		// Limpar e mostrar loading
		$(select).empty().append('<option value="">Carregando...</option>').trigger('change');

		$.ajax({
			url: '/costCenter/contas-financeiras',
			method: 'GET',
			data: { tipo: tipo },
			dataType: 'json',
			success: function(response) {
				$(select).empty().append('<option value="">Selecione a conta</option>');
				if (response.success && response.data) {
					response.data.forEach(function(conta) {
						// Ignorar opção "Todos" para extrato — precisa de uma conta específica
						if (conta.id !== 'all') {
							$(select).append(
								$('<option></option>').val(conta.id).text(conta.name)
							);
						}
					});
				}
				$(select).trigger('change');
			},
			error: function(xhr, status, error) {
				console.error('[Extrato] Erro ao carregar contas:', error);
				$(select).empty().append('<option value="">Erro ao carregar</option>').trigger('change');
			}
		});
	};

	// Init form inputs
	var initForm = function() {
		var dataInicialInput = form.querySelector('[name="data_inicial"]');
		var dataFinalInput = form.querySelector('[name="data_final"]');

		// Locale do Flatpickr
		var localeConfig = {};
		if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && (flatpickr.l10ns.pt || flatpickr.l10ns.pt_BR)) {
			localeConfig.locale = "pt";
		}

		var dataInicialFlatpickr = $(dataInicialInput).flatpickr(Object.assign({
			enableTime: false,
			dateFormat: "d/m/Y",
			onChange: function(selectedDates) {
				if (selectedDates.length > 0) {
					dataFinalFlatpickr.set('minDate', selectedDates[0]);
				}
				if (validator) {
					validator.revalidateField('data_inicial');
					validator.revalidateField('data_final');
				}
			}
		}, localeConfig));

		var dataFinalFlatpickr = $(dataFinalInput).flatpickr(Object.assign({
			enableTime: false,
			dateFormat: "d/m/Y",
			onChange: function(selectedDates) {
				if (selectedDates.length > 0) {
					dataInicialFlatpickr.set('maxDate', selectedDates[0]);
				}
				if (validator) {
					validator.revalidateField('data_inicial');
					validator.revalidateField('data_final');
				}
			}
		}, localeConfig));

		// Listener para troca de tipo de conta (banco/caixa)
		var tipoContaRadios = form.querySelectorAll('[name="tipo_conta"]');
		tipoContaRadios.forEach(function(radio) {
			radio.addEventListener('change', function() {
				loadContas(this.value);
				if (validator) {
					validator.revalidateField('entidade_id');
				}
			});
		});

		// Carregar contas iniciais (banco é default)
		loadContas('banco');
	};

	// Handle form validation and submission
	var handleForm = function() {
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
					entidade_id: {
						validators: {
							notEmpty: {
								message: 'Selecione uma conta financeira'
							}
						}
					},
					data_inicial: {
						validators: {
							notEmpty: {
								message: 'O período inicial é obrigatório'
							},
							callback: {
								message: 'O período inicial não pode ser maior que o período final',
								callback: function(input) {
									var dataInicialStr = input.value;
									var dataFinalStr = form.querySelector('[name="data_final"]').value;

									if (!dataInicialStr || !dataFinalStr) return true;

									var parseDate = function(dateStr) {
										var parts = dateStr.split('/');
										return parts.length === 3 ? new Date(parts[2], parts[1] - 1, parts[0]) : null;
									};

									var dataInicial = parseDate(dataInicialStr);
									var dataFinal = parseDate(dataFinalStr);

									return (dataInicial && dataFinal) ? dataInicial <= dataFinal : true;
								}
							}
						}
					},
					data_final: {
						validators: {
							notEmpty: {
								message: 'O período final é obrigatório'
							},
							callback: {
								message: 'O período final não pode ser menor que o período inicial',
								callback: function(input) {
									var dataFinalStr = input.value;
									var dataInicialStr = form.querySelector('[name="data_inicial"]').value;

									if (!dataInicialStr || !dataFinalStr) return true;

									var parseDate = function(dateStr) {
										var parts = dateStr.split('/');
										return parts.length === 3 ? new Date(parts[2], parts[1] - 1, parts[0]) : null;
									};

									var dataInicial = parseDate(dataInicialStr);
									var dataFinal = parseDate(dataFinalStr);

									return (dataInicial && dataFinal) ? dataFinal >= dataInicial : true;
								}
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

		// Submit
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			if (validator) {
				validator.validate().then(function (status) {
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');
						submitButton.disabled = true;

						var dataInicial = form.querySelector('[name="data_inicial"]').value;
						var dataFinal = form.querySelector('[name="data_final"]').value;
						var entidadeId = form.querySelector('[name="entidade_id"]').value;

						// Geração assíncrona com polling
						var loadingToast = null;
						var pollInterval = null;
						var pollTimeout = null;

						fetch('/relatorios/extrato/pdf-async', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'Accept': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
							},
							body: JSON.stringify({
								data_inicial: dataInicial,
								data_final: dataFinal,
								entidade_id: entidadeId
							})
						})
						.then(function(response) { return response.json(); })
						.then(function(data) {
							if (data.success) {
								var pdfId = data.pdf_id;

								// Fechar modal
								modal.hide();
								form.reset();
								// Re-selecionar banco (default) e recarregar contas
								var bancoRadio = form.querySelector('[name="tipo_conta"][value="banco"]');
								if (bancoRadio) bancoRadio.checked = true;
								loadContas('banco');
								submitButton.removeAttribute('data-kt-indicator');
								submitButton.disabled = false;

								// Toast de loading
								loadingToast = window.AppToast.loading(
									'Gerando Extrato...',
									'Você será notificado quando estiver pronto.'
								);

								// Polling a cada 3 segundos
								pollInterval = setInterval(function() {
									fetch('/relatorios/pdf/status/' + pdfId, {
										headers: {
											'Accept': 'application/json',
											'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
										}
									})
									.then(function(r) { return r.json(); })
									.then(function(statusData) {
										console.log('[Extrato] Status:', statusData.status);

										if (statusData.status === 'completed') {
											clearInterval(pollInterval);
											clearTimeout(pollTimeout);

											if (loadingToast) {
												window.AppToast.close(loadingToast);
											}

											window.dispatchEvent(new CustomEvent('notifications-updated'));
											console.log('[Extrato] PDF pronto — notificação ativada');
										}
										else if (statusData.status === 'failed') {
											clearInterval(pollInterval);
											clearTimeout(pollTimeout);

											if (loadingToast) {
												window.AppToast.close(loadingToast);
											}

											window.AppToast.error(
												'Erro na Geração',
												statusData.error_message || 'Ocorreu um erro ao gerar o PDF. Tente novamente.',
												{ autohide: false }
											);

											window.dispatchEvent(new CustomEvent('notifications-updated'));
										}
									})
									.catch(function(err) {
										console.error('[Extrato] Erro no polling:', err);
									});
								}, 3000);

								// Timeout de segurança (3 minutos)
								pollTimeout = setTimeout(function() {
									clearInterval(pollInterval);

									if (loadingToast) {
										window.AppToast.close(loadingToast);
									}

									window.dispatchEvent(new CustomEvent('notifications-updated'));

									window.AppToast.warning(
										'Processando...',
										'A geração está demorando. Você receberá uma notificação quando estiver pronto.',
										{ autohide: true, delay: 8000 }
									);
								}, 180000);

							} else {
								throw new Error(data.message || 'Erro ao iniciar geração do PDF');
							}
						})
						.catch(function(error) {
							console.error('[Extrato] Erro:', error);

							if (loadingToast) {
								window.AppToast.close(loadingToast);
							}

							window.AppToast.error(
								'Erro',
								error.message || 'Erro ao gerar PDF. Tente novamente.'
							);

							submitButton.removeAttribute('data-kt-indicator');
							submitButton.disabled = false;
						});

					} else {
						Swal.fire({
							text: "Desculpe, parece que foram detectados alguns erros, tente novamente.",
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

		// Cancel
		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
				text: "Tem certeza que deseja cancelar?",
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
					form.reset();
					modal.hide();
				}
			});
		});
	};

	return {
		init: function () {
			if (initialized) {
				console.log('[Extrato] Já inicializado, ignorando...');
				return;
			}

			modalEl = document.querySelector('#modal_extrato');
			if (!modalEl) return;

			modal = new bootstrap.Modal(modalEl);
			form = document.querySelector('#kt_modal_extrato_form');
			submitButton = document.getElementById('kt_modal_extrato_submit');
			cancelButton = document.getElementById('kt_modal_extrato_cancel');

			initForm();
			handleForm();

			initialized = true;
			console.log('[Extrato] Inicializado com sucesso');
		}
	};
}();

// On document ready
document.addEventListener('DOMContentLoaded', function () {
	KTModalExtrato.init();
});
