"use strict";

/**
 * Lote Contábil Export — inicializa os dois modais (TXT e CSV)
 * seguindo o mesmo padrão do KTModalOfxExport.
 */
var KTModalLoteContabilExport = function () {
	var initialized = false;

	/**
	 * Carrega contas financeiras via AJAX (mesmo endpoint do OFX/Extrato)
	 */
	var loadContas = function (tipo, selectId) {
		var select = document.getElementById(selectId);
		if (!select) return;

		$(select).empty().append('<option value="">Carregando...</option>').trigger('change');

		$.ajax({
			url: '/costCenter/contas-financeiras',
			method: 'GET',
			data: { tipo: tipo },
			dataType: 'json',
			success: function (response) {
				$(select).empty().append('<option value="">Selecione a conta</option>');
				if (response.success && response.data) {
					response.data.forEach(function (conta) {
						if (conta.id !== 'all') {
							$(select).append(
								$('<option></option>').val(conta.id).text(conta.name)
							);
						}
					});
				}
				$(select).trigger('change');
			},
			error: function () {
				$(select).empty().append('<option value="">Erro ao carregar</option>').trigger('change');
			}
		});
	};

	/**
	 * Inicializa um modal de Lote Contábil (TXT ou CSV).
	 *
	 * @param {Object} config
	 * @param {string} config.modalId          — ex: 'modal_lote_contabil_txt'
	 * @param {string} config.formId           — ex: 'kt_modal_lote_contabil_txt_form'
	 * @param {string} config.submitId         — ex: 'kt_modal_lote_contabil_txt_submit'
	 * @param {string} config.cancelId         — ex: 'kt_modal_lote_contabil_txt_cancel'
	 * @param {string} config.selectId         — ex: 'lote_txt_entidade_id'
	 * @param {string} config.tipoContaName    — ex: 'tipo_conta_lote_txt'
	 * @param {string} config.dataInicialName  — ex: 'data_inicial_lote_txt'
	 * @param {string} config.dataFinalName    — ex: 'data_final_lote_txt'
	 * @param {string} config.formato          — 'txt' ou 'csv'
	 * @param {string} config.label            — 'TXT' ou 'CSV'
	 */
	var initModal = function (config) {
		var modalEl = document.querySelector('#' + config.modalId);
		if (!modalEl) return;

		var modal = new bootstrap.Modal(modalEl);
		var form = document.querySelector('#' + config.formId);
		var submitButton = document.getElementById(config.submitId);
		var cancelButton = document.getElementById(config.cancelId);

		if (!form || !submitButton || !cancelButton) return;

		// ── Flatpickr ──
		var dataInicialInput = form.querySelector('[name="' + config.dataInicialName + '"]');
		var dataFinalInput = form.querySelector('[name="' + config.dataFinalName + '"]');

		var localeConfig = {};
		if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && (flatpickr.l10ns.pt || flatpickr.l10ns.pt_BR)) {
			localeConfig.locale = "pt";
		}

		var dataInicialFlatpickr = $(dataInicialInput).flatpickr(Object.assign({
			enableTime: false,
			dateFormat: "d/m/Y",
			onChange: function (selectedDates) {
				if (selectedDates.length > 0) {
					dataFinalFlatpickr.set('minDate', selectedDates[0]);
				}
				if (validator) {
					validator.revalidateField(config.dataInicialName);
					validator.revalidateField(config.dataFinalName);
				}
			}
		}, localeConfig));

		var dataFinalFlatpickr = $(dataFinalInput).flatpickr(Object.assign({
			enableTime: false,
			dateFormat: "d/m/Y",
			onChange: function (selectedDates) {
				if (selectedDates.length > 0) {
					dataInicialFlatpickr.set('maxDate', selectedDates[0]);
				}
				if (validator) {
					validator.revalidateField(config.dataInicialName);
					validator.revalidateField(config.dataFinalName);
				}
			}
		}, localeConfig));

		// ── Listeners tipo de conta ──
		var tipoContaRadios = form.querySelectorAll('[name="' + config.tipoContaName + '"]');
		tipoContaRadios.forEach(function (radio) {
			radio.addEventListener('change', function () {
				loadContas(this.value, config.selectId);
				if (validator) {
					validator.revalidateField('entidade_id');
				}
			});
		});

		// Carrega contas iniciais (banco é default)
		loadContas('banco', config.selectId);

		// ── Date comparison helper ──
		var parseDate = function (dateStr) {
			var parts = dateStr.split('/');
			return parts.length === 3 ? new Date(parts[2], parts[1] - 1, parts[0]) : null;
		};

		// ── FormValidation ──
		var validator = FormValidation.formValidation(form, {
			fields: {
				entidade_id: {
					validators: {
						notEmpty: { message: 'Selecione uma conta financeira' }
					}
				},
				[config.dataInicialName]: {
					validators: {
						notEmpty: { message: 'O período inicial é obrigatório' },
						callback: {
							message: 'O período inicial não pode ser maior que o final',
							callback: function (input) {
								var di = input.value;
								var df = form.querySelector('[name="' + config.dataFinalName + '"]').value;
								if (!di || !df) return true;
								var a = parseDate(di), b = parseDate(df);
								return (a && b) ? a <= b : true;
							}
						}
					}
				},
				[config.dataFinalName]: {
					validators: {
						notEmpty: { message: 'O período final é obrigatório' },
						callback: {
							message: 'O período final não pode ser menor que o inicial',
							callback: function (input) {
								var df = input.value;
								var di = form.querySelector('[name="' + config.dataInicialName + '"]').value;
								if (!di || !df) return true;
								var a = parseDate(di), b = parseDate(df);
								return (a && b) ? b >= a : true;
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
		});

		// ── Submit — download via fetch + blob ──
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();

			validator.validate().then(function (status) {
				if (status !== 'Valid') {
					Swal.fire({
						text: "Preencha todos os campos obrigatórios.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, entendi!",
						customClass: { confirmButton: "btn btn-primary" }
					});
					return;
				}

				submitButton.setAttribute('data-kt-indicator', 'on');
				submitButton.disabled = true;

				var dataInicial = form.querySelector('[name="' + config.dataInicialName + '"]').value;
				var dataFinal = form.querySelector('[name="' + config.dataFinalName + '"]').value;
				var entidadeId = form.querySelector('[name="entidade_id"]').value;
				var campoData = form.querySelector('[name="campo_data"]:checked').value;
				var formato = form.querySelector('[name="formato"]').value;

				var params = new URLSearchParams({
					entidade_id: entidadeId,
					data_inicial: dataInicial,
					data_final: dataFinal,
					campo_data: campoData,
					formato: formato
				});

				fetch('/relatorios/lote-contabil/exportar?' + params.toString(), {
					method: 'GET',
					headers: {
						'Accept': formato === 'csv' ? 'text/csv' : 'text/plain',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					}
				})
				.then(function (response) {
					if (!response.ok) {
						return response.json().then(function (err) {
							throw new Error(err.message || 'Erro ao gerar o arquivo');
						});
					}

					// Ler contadores dos headers
					var totalLancamentos = response.headers.get('X-Total-Lancamentos') || '0';
					var totalIgnoradas = response.headers.get('X-Total-Ignoradas') || '0';

					// Nome do arquivo
					var disposition = response.headers.get('Content-Disposition');
					var filename = 'lote_contabil.' + formato;
					if (disposition && disposition.indexOf('filename=') !== -1) {
						var match = disposition.match(/filename="?(.+?)"?$/);
						if (match) filename = match[1];
					}

					return response.blob().then(function (blob) {
						return {
							blob: blob,
							filename: filename,
							totalLancamentos: totalLancamentos,
							totalIgnoradas: totalIgnoradas
						};
					});
				})
				.then(function (data) {
					// Download via <a> temporário
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
					var bancoRadio = form.querySelector('[name="' + config.tipoContaName + '"][value="banco"]');
					if (bancoRadio) bancoRadio.checked = true;
					var dataRadio = form.querySelector('[name="campo_data"][value="data"]');
					if (dataRadio) dataRadio.checked = true;
					loadContas('banco', config.selectId);

					// Toast de sucesso com contadores
					var msgIgnoradas = parseInt(data.totalIgnoradas) > 0
						? '\n' + data.totalIgnoradas + ' movimentação(ões) ignorada(s) por falta de código externo.'
						: '';

					if (typeof toastr !== 'undefined') {
						toastr.success(
							data.totalLancamentos + ' lançamento(s) exportado(s) com sucesso!' + msgIgnoradas,
							'Lote Contábil ' + config.label
						);
					} else if (window.AppToast) {
						window.AppToast.success(
							'Lote Contábil ' + config.label + ' Exportado!',
							data.totalLancamentos + ' lançamento(s) exportado(s).' + msgIgnoradas
						);
					} else {
						Swal.fire({
							text: data.totalLancamentos + ' lançamento(s) exportado(s) com sucesso!' + msgIgnoradas,
							icon: 'success',
							buttonsStyling: false,
							confirmButtonText: 'Ok!',
							customClass: { confirmButton: 'btn btn-primary' }
						});
					}
				})
				.catch(function (error) {
					Swal.fire({
						text: error.message || 'Erro ao gerar o arquivo. Tente novamente.',
						icon: 'error',
						buttonsStyling: false,
						confirmButtonText: 'Ok, entendi!',
						customClass: { confirmButton: 'btn btn-primary' }
					});
				})
				.finally(function () {
					submitButton.removeAttribute('data-kt-indicator');
					submitButton.disabled = false;
				});
			});
		});

		// ── Cancel ──
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
			if (initialized) return;

			// Modal TXT
			initModal({
				modalId: 'modal_lote_contabil_txt',
				formId: 'kt_modal_lote_contabil_txt_form',
				submitId: 'kt_modal_lote_contabil_txt_submit',
				cancelId: 'kt_modal_lote_contabil_txt_cancel',
				selectId: 'lote_txt_entidade_id',
				tipoContaName: 'tipo_conta_lote_txt',
				dataInicialName: 'data_inicial_lote_txt',
				dataFinalName: 'data_final_lote_txt',
				formato: 'txt',
				label: 'TXT'
			});

			// Modal CSV
			initModal({
				modalId: 'modal_lote_contabil_csv',
				formId: 'kt_modal_lote_contabil_csv_form',
				submitId: 'kt_modal_lote_contabil_csv_submit',
				cancelId: 'kt_modal_lote_contabil_csv_cancel',
				selectId: 'lote_csv_entidade_id',
				tipoContaName: 'tipo_conta_lote_csv',
				dataInicialName: 'data_inicial_lote_csv',
				dataFinalName: 'data_final_lote_csv',
				formato: 'csv',
				label: 'CSV'
			});

			initialized = true;
		}
	};
}();

// On document ready
document.addEventListener('DOMContentLoaded', function () {
	KTModalLoteContabilExport.init();

	// Handler para abrir modais via data-lote-contabil-target (evita conflito com KTMenu submenu)
	document.addEventListener('click', function (e) {
		var link = e.target.closest('[data-lote-contabil-target]');
		if (!link) return;

		e.preventDefault();
		e.stopPropagation();

		var targetSelector = link.getAttribute('data-lote-contabil-target');
		var modalEl = document.querySelector(targetSelector);
		if (modalEl) {
			var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
			modal.show();
		}
	});
});
