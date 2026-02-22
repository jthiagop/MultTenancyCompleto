"use strict";

// Class definition
var KTModalOfxExport = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;
	var initialized = false;

	// Load contas financeiras via AJAX (reutiliza o mesmo endpoint do extrato)
	var loadContas = function(tipo) {
		var select = document.getElementById('ofx_entidade_id');
		if (!select) return;

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
						if (conta.id !== 'all') {
							$(select).append(
								$('<option></option>').val(conta.id).text(conta.name)
							);
						}
					});
				}
				$(select).trigger('change');
			},
			error: function() {
				$(select).empty().append('<option value="">Erro ao carregar</option>').trigger('change');
			}
		});
	};

	// Init form inputs
	var initForm = function() {
		var dataInicialInput = form.querySelector('[name="data_inicial_ofx"]');
		var dataFinalInput = form.querySelector('[name="data_final_ofx"]');

		// Locale Flatpickr
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
					validator.revalidateField('data_inicial_ofx');
					validator.revalidateField('data_final_ofx');
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
					validator.revalidateField('data_inicial_ofx');
					validator.revalidateField('data_final_ofx');
				}
			}
		}, localeConfig));

		// Listener para troca de tipo de conta
		var tipoContaRadios = form.querySelectorAll('[name="tipo_conta_ofx"]');
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
					data_inicial_ofx: {
						validators: {
							notEmpty: {
								message: 'O período inicial é obrigatório'
							},
							callback: {
								message: 'O período inicial não pode ser maior que o período final',
								callback: function(input) {
									var dataInicialStr = input.value;
									var dataFinalStr = form.querySelector('[name="data_final_ofx"]').value;
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
					data_final_ofx: {
						validators: {
							notEmpty: {
								message: 'O período final é obrigatório'
							},
							callback: {
								message: 'O período final não pode ser menor que o período inicial',
								callback: function(input) {
									var dataFinalStr = input.value;
									var dataInicialStr = form.querySelector('[name="data_inicial_ofx"]').value;
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

		// Submit — download direto via fetch + blob
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			if (validator) {
				validator.validate().then(function (status) {
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');
						submitButton.disabled = true;

						var dataInicial = form.querySelector('[name="data_inicial_ofx"]').value;
						var dataFinal = form.querySelector('[name="data_final_ofx"]').value;
						var entidadeId = form.querySelector('[name="entidade_id"]').value;

						// Montar URL com query params
						var params = new URLSearchParams({
							entidade_id: entidadeId,
							data_inicial: dataInicial,
							data_final: dataFinal
						});

						fetch('/relatorios/ofx/exportar?' + params.toString(), {
							method: 'GET',
							headers: {
								'Accept': 'application/x-ofx',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
							}
						})
						.then(function(response) {
							if (!response.ok) {
								return response.json().then(function(err) {
									throw new Error(err.message || 'Erro ao gerar OFX');
								});
							}

							// Pegar o nome do arquivo do header Content-Disposition
							var disposition = response.headers.get('Content-Disposition');
							var filename = 'extrato.ofx';
							if (disposition && disposition.indexOf('filename=') !== -1) {
								var match = disposition.match(/filename="?(.+?)"?$/);
								if (match) filename = match[1];
							}

							return response.blob().then(function(blob) {
								return { blob: blob, filename: filename };
							});
						})
						.then(function(data) {
							// Criar link temporário para download
							var url = window.URL.createObjectURL(data.blob);
							var a = document.createElement('a');
							a.href = url;
							a.download = data.filename;
							document.body.appendChild(a);
							a.click();
							window.URL.revokeObjectURL(url);
							a.remove();

							// Fechar modal e resetar
							modal.hide();
							form.reset();
							var bancoRadio = form.querySelector('[name="tipo_conta_ofx"][value="banco"]');
							if (bancoRadio) bancoRadio.checked = true;
							loadContas('banco');

							// Toast de sucesso
							if (window.AppToast) {
								window.AppToast.success(
									'OFX Exportado!',
									'O arquivo foi baixado com sucesso.'
								);
							} else {
								Swal.fire({
									text: 'Arquivo OFX exportado com sucesso!',
									icon: 'success',
									buttonsStyling: false,
									confirmButtonText: 'Ok!',
									customClass: { confirmButton: 'btn btn-primary' }
								});
							}
						})
						.catch(function(error) {
							Swal.fire({
								text: error.message || 'Erro ao gerar o arquivo OFX. Tente novamente.',
								icon: 'error',
								buttonsStyling: false,
								confirmButtonText: 'Ok, entendi!',
								customClass: { confirmButton: 'btn btn-primary' }
							});
						})
						.finally(function() {
							submitButton.removeAttribute('data-kt-indicator');
							submitButton.disabled = false;
						});

					} else {
						Swal.fire({
							text: "Preencha todos os campos obrigatórios.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, entendi!",
							customClass: { confirmButton: "btn btn-primary" }
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
				return;
			}

			modalEl = document.querySelector('#modal_ofx');
			if (!modalEl) return;

			modal = new bootstrap.Modal(modalEl);
			form = document.querySelector('#kt_modal_ofx_form');
			submitButton = document.getElementById('kt_modal_ofx_submit');
			cancelButton = document.getElementById('kt_modal_ofx_cancel');

			initForm();
			handleForm();

			initialized = true;
		}
	};
}();

// On document ready
document.addEventListener('DOMContentLoaded', function () {
	KTModalOfxExport.init();
});
