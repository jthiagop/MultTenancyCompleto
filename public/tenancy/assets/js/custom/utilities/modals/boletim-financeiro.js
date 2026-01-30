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
					console.log('[BoletimFinanceiro] Validação:', status);

					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');
						submitButton.disabled = true;

						// Get form data
						var dataInicial = form.querySelector('[name="data_inicial"]').value;
						var dataFinal = form.querySelector('[name="data_final"]').value;

						// ===== GERAÇÃO ASSÍNCRONA COM POLLING =====
						
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
							console.log('[BoletimFinanceiro] Resposta async:', data);
							
							if (data.success) {
								var pdfId = data.pdf_id;
								var pollInterval = null;
								var pollTimeout = null;
								
								// 2. Mostrar loading com SweetAlert
								Swal.fire({
									title: 'Gerando Boletim...',
									html: '<div class="d-flex flex-column align-items-center">' +
										  '<div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>' +
										  '<p class="text-gray-600 mb-0">Processando seu relatório.</p>' +
										  '<small class="text-muted">Isso pode levar alguns segundos...</small>' +
										  '</div>',
									allowOutsideClick: false,
									allowEscapeKey: false,
									showConfirmButton: false,
									didOpen: function() {
										// 3. Polling a cada 2 segundos
										pollInterval = setInterval(function() {
											fetch('/relatorios/pdf/status/' + pdfId, {
												headers: {
													'Accept': 'application/json',
													'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
												}
											})
											.then(function(r) { return r.json(); })
											.then(function(statusData) {
												console.log('[BoletimFinanceiro] Status:', statusData);
												
												if (statusData.status === 'completed') {
													// Limpar polling
													clearInterval(pollInterval);
													clearTimeout(pollTimeout);
													Swal.close();
													
													// Abrir PDF em nova aba
													window.open(statusData.download_url, '_blank');
													
													// Reset estado
													submitButton.removeAttribute('data-kt-indicator');
													submitButton.disabled = false;
													modal.hide();
													form.reset();
													
													// Toast de sucesso
													Swal.fire({
														icon: 'success',
														title: 'PDF Gerado!',
														text: 'O boletim financeiro foi gerado com sucesso.',
														timer: 3000,
														timerProgressBar: true,
														showConfirmButton: false
													});
												} 
												else if (statusData.status === 'failed') {
													// Limpar polling
													clearInterval(pollInterval);
													clearTimeout(pollTimeout);
													
													Swal.fire({
														icon: 'error',
														title: 'Erro na Geração',
														text: statusData.error_message || 'Ocorreu um erro ao gerar o PDF. Tente novamente.',
														confirmButtonText: 'Ok, entendi',
														customClass: {
															confirmButton: 'btn btn-primary'
														},
														buttonsStyling: false
													});
													
													submitButton.removeAttribute('data-kt-indicator');
													submitButton.disabled = false;
												}
												// Se status === 'pending' ou 'processing', continua polling
											})
											.catch(function(err) {
												console.error('[BoletimFinanceiro] Erro no polling:', err);
											});
										}, 2000); // Poll a cada 2 segundos
										
										// 4. Timeout de segurança (3 minutos)
										pollTimeout = setTimeout(function() {
											clearInterval(pollInterval);
											if (Swal.isVisible()) {
												Swal.fire({
													icon: 'warning',
													title: 'Tempo Excedido',
													text: 'A geração está demorando mais que o esperado. O relatório continuará sendo processado em segundo plano.',
													confirmButtonText: 'Ok',
													customClass: {
														confirmButton: 'btn btn-primary'
													},
													buttonsStyling: false
												});
												submitButton.removeAttribute('data-kt-indicator');
												submitButton.disabled = false;
											}
										}, 180000); // 3 minutos
									}
								});
							} else {
								throw new Error(data.message || 'Erro ao iniciar geração do PDF');
							}
						})
						.catch(function(error) {
							console.error('[BoletimFinanceiro] Erro:', error);
							Swal.fire({
								icon: 'error',
								title: 'Erro',
								text: error.message || 'Erro ao gerar PDF. Tente novamente.',
								confirmButtonText: 'Ok, entendi',
								customClass: {
									confirmButton: 'btn btn-primary'
								},
								buttonsStyling: false
							});
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
