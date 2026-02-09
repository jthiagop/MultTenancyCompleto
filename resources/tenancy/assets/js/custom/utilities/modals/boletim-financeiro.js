"use strict";

// Class definition
var KTModalBoletimFinanceiro = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;
	var initialized = false; // Flag para evitar dupla inicialização

	// Init form inputs
	var initForm = function() {
		// Data Inicial - Flatpickr
		var dataInicialInput = form.querySelector('[name="data_inicial"]');
		var dataFinalInput = form.querySelector('[name="data_final"]');
		
		// Verifica se locale pt está disponível
		var localeConfig = {};
		if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && (flatpickr.l10ns.pt || flatpickr.l10ns.pt_BR)) {
			localeConfig.locale = "pt";
		}
		
		var dataInicialFlatpickr = $(dataInicialInput).flatpickr(Object.assign({
			enableTime: false,
			dateFormat: "d/m/Y",
			onChange: function(selectedDates, dateStr, instance) {
				// Quando a data inicial mudar, atualiza a data mínima da data final
				if (selectedDates.length > 0) {
					dataFinalFlatpickr.set('minDate', selectedDates[0]);
				}
				// Revalidate both fields
				if (validator) {
					validator.revalidateField('data_inicial');
					validator.revalidateField('data_final');
				}
			}
		}, localeConfig));

        // Data Final - Flatpickr
        var dataFinalFlatpickr = $(dataFinalInput).flatpickr(Object.assign({
			enableTime: false,
			dateFormat: "d/m/Y",
			onChange: function(selectedDates, dateStr, instance) {
				// Quando a data final mudar, atualiza a data máxima da data inicial
				if (selectedDates.length > 0) {
					dataInicialFlatpickr.set('maxDate', selectedDates[0]);
				}
				// Revalidate both fields
				if (validator) {
					validator.revalidateField('data_inicial');
					validator.revalidateField('data_final');
				}
			}
		}, localeConfig));
	}

	// Handle form validation and submittion
	var handleForm = function() {
		// Init form validation rules
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
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
									
									if (!dataInicialStr || !dataFinalStr) {
										return true;
									}
									
									// Parse dates in d/m/Y format
									var parseDate = function(dateStr) {
										var parts = dateStr.split('/');
										if (parts.length === 3) {
											return new Date(parts[2], parts[1] - 1, parts[0]);
										}
										return null;
									};
									
									var dataInicial = parseDate(dataInicialStr);
									var dataFinal = parseDate(dataFinalStr);
									
									if (dataInicial && dataFinal) {
										return dataInicial <= dataFinal;
									}
									
									return true;
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
									
									if (!dataInicialStr || !dataFinalStr) {
										return true;
									}
									
									// Parse dates in d/m/Y format
									var parseDate = function(dateStr) {
										var parts = dateStr.split('/');
										if (parts.length === 3) {
											return new Date(parts[2], parts[1] - 1, parts[0]);
										}
										return null;
									};
									
									var dataInicial = parseDate(dataInicialStr);
									var dataFinal = parseDate(dataFinalStr);
									
									if (dataInicial && dataFinal) {
										return dataFinal >= dataInicial;
									}
									
									return true;
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

		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');
						submitButton.disabled = true;

						// Get form data
						var dataInicial = form.querySelector('[name="data_inicial"]').value;
						var dataFinal = form.querySelector('[name="data_final"]').value;

						// ===== GERAÇÃO ASSÍNCRONA COM POLLING → NOTIFICAÇÃO =====
						var loadingToast = null;
						var pollInterval = null;
						var pollTimeout = null;
						
						// 1. Disparar geração assíncrona via Job
						fetch('/relatorios/boletim-financeiro/pdf-async', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'Accept': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
							},
							body: JSON.stringify({
								data_inicial: dataInicial,
								data_final: dataFinal
							})
						})
						.then(function(response) {
							return response.json();
						})
						.then(function(data) {
							
							if (data.success) {
								var pdfId = data.pdf_id;
								
								// Fechar modal imediatamente — usuário pode continuar navegando
								modal.hide();
								form.reset();
								submitButton.removeAttribute('data-kt-indicator');
								submitButton.disabled = false;
								
								// 2. Toast de loading (feedback imediato enquanto processa)
								loadingToast = window.AppToast.loading(
									'Gerando Boletim...', 
									'Você será notificado quando estiver pronto.'
								);
								
								// 3. Polling a cada 3 segundos (fecha toast e ativa notificação)
								pollInterval = setInterval(function() {
									fetch('/relatorios/pdf/status/' + pdfId, {
										headers: {
											'Accept': 'application/json',
											'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
										}
									})
									.then(function(r) { return r.json(); })
									.then(function(statusData) {
										console.log('[BoletimFinanceiro] Status:', statusData.status);
										
										if (statusData.status === 'completed') {
											clearInterval(pollInterval);
											clearTimeout(pollTimeout);
											
											// Fechar toast de loading
											if (loadingToast) {
												window.AppToast.close(loadingToast);
											}
											
											// Atualizar notificações e abrir popup
											window.dispatchEvent(new CustomEvent('notifications-updated'));
											
											console.log('[BoletimFinanceiro] PDF pronto — notificação ativada');
										} 
										else if (statusData.status === 'failed') {
											clearInterval(pollInterval);
											clearTimeout(pollTimeout);
											
											if (loadingToast) {
												window.AppToast.close(loadingToast);
											}
											
											// Erro: toast + atualizar notificações
											window.AppToast.error(
												'Erro na Geração', 
												statusData.error_message || 'Ocorreu um erro ao gerar o PDF. Tente novamente.',
												{ autohide: false }
											);
											
											window.dispatchEvent(new CustomEvent('notifications-updated'));
										}
										// Se 'pending' ou 'processing', continua polling
									})
									.catch(function(err) {
										console.error('[BoletimFinanceiro] Erro no polling:', err);
									});
								}, 3000); // Poll a cada 3s
								
								// 4. Timeout de segurança (3 minutos)
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
								}, 180000); // 3 minutos
								
							} else {
								throw new Error(data.message || 'Erro ao iniciar geração do PDF');
							}
						})
						.catch(function(error) {
							console.error('[BoletimFinanceiro] Erro:', error);
							
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
						// Show error message
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
	}

	return {
		// Public functions
		init: function () {
			// Evitar dupla inicialização
			if (initialized) {
				console.log('[BoletimFinanceiro] Já inicializado, ignorando...');
				return;
			}
			
			// Elements
			modalEl = document.querySelector('#modal_boletim_financeiro');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_boletim_financeiro_form');
			submitButton = document.getElementById('kt_modal_boletim_submit');
			cancelButton = document.getElementById('kt_modal_boletim_cancel');

			initForm();
			handleForm();
			
			initialized = true;
			console.log('[BoletimFinanceiro] Inicializado com sucesso');
		}
	};
}();

// On document ready
document.addEventListener('DOMContentLoaded', function () {
	KTModalBoletimFinanceiro.init();
});
