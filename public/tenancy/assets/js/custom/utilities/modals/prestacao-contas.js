"use strict";

// Class definition
var KTModalPrestacaoContas = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;
	var modalData = null;

	// Load modal data via AJAX
	var loadModalData = function() {
		$.ajax({
			url: '/costCenter/modal/data',
			method: 'GET',
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					modalData = response.data;

					var categoriasSelect = document.getElementById('categorias');
					if (categoriasSelect && modalData.categorias) {
						$(categoriasSelect).empty();
						modalData.categorias.forEach(function(cat) {
							$(categoriasSelect).append(
								$('<option></option>').val(cat.id).text(cat.label)
							);
						});
						$(categoriasSelect).trigger('change');
					}

					var parceirosSelect = document.getElementById('parceiro_id');
					if (parceirosSelect && modalData.parceiros) {
						$(parceirosSelect).empty();
						$(parceirosSelect).append('<option value="">Todos os parceiros</option>');
						modalData.parceiros.forEach(function(p) {
							$(parceirosSelect).append(
								$('<option></option>').val(p.id).text(p.label)
							);
						});
						$(parceirosSelect).trigger('change');
					}
				} else {
					console.error('Failed to load modal data:', response.message);
					Swal.fire({
						text: response.message || 'Erro ao carregar dados do modal',
						icon: 'error',
						buttonsStyling: false,
						confirmButtonText: 'Ok',
						customClass: { confirmButton: 'btn btn-primary' }
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX error loading modal data:', error);
				Swal.fire({
					text: 'Erro ao carregar dados. Por favor, recarregue a p\u00e1gina.',
					icon: 'error',
					buttonsStyling: false,
					confirmButtonText: 'Ok',
					customClass: { confirmButton: 'btn btn-primary' }
				});
			}
		});
	}

	// Init form inputs
	var initForm = function() {
		var dataInicialInput = form.querySelector('[name="data_inicial"]');
		var dataFinalInput = form.querySelector('[name="data_final"]');

		var localeConfig = {};
		if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && (flatpickr.l10ns.pt || flatpickr.l10ns.pt_BR)) {
			localeConfig.locale = "pt";
		}

		var dataInicialFlatpickr = $(dataInicialInput).flatpickr(Object.assign({
			enableTime: false,
			dateFormat: "d/m/Y",
			onChange: function(selectedDates, dateStr, instance) {
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
			onChange: function(selectedDates, dateStr, instance) {
				if (selectedDates.length > 0) {
					dataInicialFlatpickr.set('maxDate', selectedDates[0]);
				}
				if (validator) {
					validator.revalidateField('data_inicial');
					validator.revalidateField('data_final');
				}
			}
		}, localeConfig));

		// Handle filtrar_contas checkbox
		var filtrarContasCheckbox = document.getElementById('filtrar_contas');
		var tipoContaOptions = document.getElementById('tipo_conta_options');
		var tipoContaRadios = document.querySelectorAll('input[name="tipo_conta"]');
		var contaIdSelect = document.getElementById('conta_id');

		$(filtrarContasCheckbox).on('change', function() {
			if (this.checked) {
				tipoContaOptions.style.display = '';
				tipoContaRadios.forEach(function(radio) { radio.disabled = false; });
				contaIdSelect.disabled = false;
			} else {
				tipoContaOptions.style.display = 'none';
				tipoContaRadios.forEach(function(radio) { radio.disabled = true; radio.checked = false; });
				contaIdSelect.disabled = true;
				$(contaIdSelect).val('').trigger('change');
			}
		});

		// Load Caixa or Banco options when radio button changes
		$('input[name="tipo_conta"]').on('change', function() {
			var tipoSelecionado = this.value;
			$(contaIdSelect).empty().append('<option value="">Carregando...</option>');

			$.ajax({
				url: '/costCenter/contas-financeiras',
				method: 'GET',
				data: { tipo: tipoSelecionado },
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						$(contaIdSelect).empty();
						$(contaIdSelect).append('<option value="">Selecione...</option>');
						if (response.data && response.data.length > 0) {
							response.data.forEach(function(conta) {
								$(contaIdSelect).append($('<option></option>').val(conta.id).text(conta.name));
							});
						} else {
							$(contaIdSelect).append('<option value="">Nenhum registro encontrado</option>');
						}
						$(contaIdSelect).trigger('change');
					} else {
						$(contaIdSelect).empty().append('<option value="">Erro ao carregar</option>');
					}
				},
				error: function(xhr, status, error) {
					$(contaIdSelect).empty().append('<option value="">Erro ao carregar</option>');
				}
			});
		});
	}

	// Handle form validation and submission
	var handleForm = function() {
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
					data_inicial: {
						validators: {
							notEmpty: { message: 'A data inicial \u00e9 obrigat\u00f3ria' },
							callback: {
								message: 'A data inicial n\u00e3o pode ser maior que a data final',
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
							notEmpty: { message: 'A data final \u00e9 obrigat\u00f3ria' },
							callback: {
								message: 'A data final n\u00e3o pode ser menor que a data inicial',
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
					},
					modelo: {
						validators: {
							notEmpty: { message: 'Selecione o modelo de relat\u00f3rio' }
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

		// Submit button - gera\u00e7\u00e3o ass\u00edncrona
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			if (validator) {
				validator.validate().then(function (status) {
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');
						submitButton.disabled = true;

						// Coletar dados do formul\u00e1rio
						var dataInicial = form.querySelector('[name="data_inicial"]').value;
						var dataFinal = form.querySelector('[name="data_final"]').value;

						var modeloRadio = form.querySelector('[name="modelo"]:checked');
						var modelo = modeloRadio ? modeloRadio.value : 'horizontal';

						var tipoDataRadio = form.querySelector('[name="tipo_data"]:checked');
						var tipoData = tipoDataRadio ? tipoDataRadio.value : 'competencia';

						var situacoesVal = $(form.querySelector('[name="situacoes[]"]')).val();
						var situacoes = (situacoesVal && situacoesVal.length > 0) ? situacoesVal.join(',') : '';

						var categoriasVal = $(form.querySelector('[name="categorias[]"]')).val();
						var categorias = (categoriasVal && categoriasVal.length > 0) ? categoriasVal.join(',') : '';

						var parceiroVal = $(form.querySelector('[name="parceiro_id"]')).val();

						var comprovacaoFiscal = document.getElementById('comprovacao_fiscal');
						var comprovacaoFiscalVal = comprovacaoFiscal && comprovacaoFiscal.checked ? '1' : '0';

						var tipoValorRadio = form.querySelector('[name="tipo_valor"]:checked');
						var tipoValor = tipoValorRadio ? tipoValorRadio.value : 'previsto';

						var entidadeId = null;
						var filtrarContas = document.getElementById('filtrar_contas');
						if (filtrarContas && filtrarContas.checked) {
							var contaIdVal = $(form.querySelector('[name="conta_id"]')).val();
							if (contaIdVal) entidadeId = contaIdVal;
						}

						// Gera\u00e7\u00e3o ass\u00edncrona com polling
						var loadingToast = null;
						var pollInterval = null;
						var pollTimeout = null;

						fetch('/relatorios/prestacao-de-contas/pdf-async', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'Accept': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
							},
							body: JSON.stringify({
								data_inicial: dataInicial,
								data_final: dataFinal,
								entidade_id: entidadeId,
								modelo: modelo,
								tipo_data: tipoData,
								situacoes: situacoes,
								categorias: categorias,
								parceiro_id: parceiroVal || null,
								comprovacao_fiscal: comprovacaoFiscalVal,
								tipo_valor: tipoValor
							})
						})
						.then(function(response) { return response.json(); })
						.then(function(data) {
							if (data.success) {
								var pdfId = data.pdf_id;

								// Fechar modal e resetar
								modal.hide();
								form.reset();
								submitButton.removeAttribute('data-kt-indicator');
								submitButton.disabled = false;

								// Toast de loading
								loadingToast = window.AppToast.loading(
									'Gerando Presta\u00e7\u00e3o de Contas...',
									'Voc\u00ea ser\u00e1 notificado quando estiver pronto.'
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
										console.log('[PrestacaoContas] Status:', statusData.status);

										if (statusData.status === 'completed') {
											clearInterval(pollInterval);
											clearTimeout(pollTimeout);
											if (loadingToast) window.AppToast.close(loadingToast);
											window.dispatchEvent(new CustomEvent('notifications-updated'));
											console.log('[PrestacaoContas] PDF pronto');
										}
										else if (statusData.status === 'failed') {
											clearInterval(pollInterval);
											clearTimeout(pollTimeout);
											if (loadingToast) window.AppToast.close(loadingToast);
											window.AppToast.error(
												'Erro na Gera\u00e7\u00e3o',
												statusData.error_message || 'Ocorreu um erro ao gerar o PDF. Tente novamente.',
												{ autohide: false }
											);
											window.dispatchEvent(new CustomEvent('notifications-updated'));
										}
									})
									.catch(function(err) {
										console.error('[PrestacaoContas] Erro no polling:', err);
									});
								}, 3000);

								// Timeout de seguran\u00e7a (3 minutos)
								pollTimeout = setTimeout(function() {
									clearInterval(pollInterval);
									if (loadingToast) window.AppToast.close(loadingToast);
									window.dispatchEvent(new CustomEvent('notifications-updated'));
									window.AppToast.warning(
										'Processando...',
										'A gera\u00e7\u00e3o est\u00e1 demorando. Voc\u00ea receber\u00e1 uma notifica\u00e7\u00e3o quando estiver pronto.',
										{ autohide: true, delay: 8000 }
									);
								}, 180000);

							} else {
								throw new Error(data.message || 'Erro ao iniciar gera\u00e7\u00e3o do PDF');
							}
						})
						.catch(function(error) {
							console.error('[PrestacaoContas] Erro:', error);
							if (loadingToast) window.AppToast.close(loadingToast);
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
							customClass: { confirmButton: "btn btn-primary" }
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
				cancelButtonText: "N\u00e3o, retorne",
				customClass: {
					confirmButton: "btn btn-primary",
					cancelButton: "btn btn-active-light"
				}
			}).then(function (result) {
				if (result.value) {
					form.reset();
					modal.hide();
				} else if (result.dismiss === 'cancel') {
					Swal.fire({
						text: "Seu formul\u00e1rio n\u00e3o foi cancelado!.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, entendi!",
						customClass: { confirmButton: "btn btn-primary" }
					});
				}
			});
		});
	}

	return {
		init: function () {
			modalEl = document.querySelector('#modal_prestacao_contas');
			if (!modalEl) return;

			modal = new bootstrap.Modal(modalEl);
			form = document.querySelector('#kt_modal_prestacao_contas_form');
			submitButton = document.getElementById('kt_modal_prestacao_contas_submit');
			cancelButton = document.getElementById('kt_modal_prestacao_contas_cancel');

			loadModalData();
			initForm();
			handleForm();
		}
	};
}();

// On document ready
document.addEventListener('DOMContentLoaded', function () {
	KTModalPrestacaoContas.init();
});
