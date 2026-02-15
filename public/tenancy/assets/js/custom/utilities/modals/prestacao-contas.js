"use strict";

// Class definition
var KTModalPrestacaoContas = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;
	var modalData = null; // Store loaded data

	// Load modal data via AJAX
	var loadModalData = function() {
		$.ajax({
			url: '/costCenter/modal/data',
			method: 'GET',
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					modalData = response.data;

					// Populate categorias select
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

					// Populate parceiros select
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
						customClass: {
							confirmButton: 'btn btn-primary'
						}
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX error loading modal data:', error);
				Swal.fire({
					text: 'Erro ao carregar dados. Por favor, recarregue a página.',
					icon: 'error',
					buttonsStyling: false,
					confirmButtonText: 'Ok',
					customClass: {
						confirmButton: 'btn btn-primary'
					}
				});
			}
		});
	}

	// Init form inputs
	var initForm = function() {
		// Data Inicial. For more info, please visit the official plugin site: https://flatpickr.js.org/
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

        // Data Final
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

        // Handle filtrar_contas checkbox
        var filtrarContasCheckbox = document.getElementById('filtrar_contas');
        var tipoContaOptions = document.getElementById('tipo_conta_options');
        var tipoContaRadios = document.querySelectorAll('input[name="tipo_conta"]');
        var contaIdSelect = document.getElementById('conta_id');
        
        $(filtrarContasCheckbox).on('change', function() {
            if (this.checked) {
                // Show options and enable radios and select
                tipoContaOptions.style.display = '';
                tipoContaRadios.forEach(function(radio) {
                    radio.disabled = false;
                });
                contaIdSelect.disabled = false;
            } else {
                // Hide options, disable and clear everything
                tipoContaOptions.style.display = 'none';
                tipoContaRadios.forEach(function(radio) {
                    radio.disabled = true;
                    radio.checked = false;
                });
                contaIdSelect.disabled = true;
                $(contaIdSelect).val('').trigger('change');
            }
        });
        
        // Load Caixa or Banco options when radio button changes
        $('input[name="tipo_conta"]').on('change', function() {
            var tipoSelecionado = this.value;
            
            // Clear current options
            $(contaIdSelect).empty().append('<option value="">Carregando...</option>');
            
            // Load options via AJAX
            $.ajax({
                url: '/costCenter/contas-financeiras',
                method: 'GET',
                data: { tipo: tipoSelecionado },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Clear loading option
                        $(contaIdSelect).empty();
                        
                        // Add placeholder
                        $(contaIdSelect).append('<option value="">Selecione...</option>');
                        
                        // Add options
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(conta) {
                                $(contaIdSelect).append(
                                    $('<option></option>').val(conta.id).text(conta.name)
                                );
                            });
                        } else {
                            $(contaIdSelect).append('<option value="">Nenhum registro encontrado</option>');
                        }
                        
                        // Trigger Select2 update
                        $(contaIdSelect).trigger('change');
                    } else {
                        console.error('Erro ao carregar contas:', response.message);
                        $(contaIdSelect).empty().append('<option value="">Erro ao carregar</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    $(contaIdSelect).empty().append('<option value="">Erro ao carregar</option>');
                }
            });
        });
	}

	// Handle form validation and submittion
	var handleForm = function() {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
					data_inicial: {
						validators: {
							notEmpty: {
								message: 'A data inicial é obrigatória'
							},
							callback: {
								message: 'A data inicial não pode ser maior que a data final',
								callback: function(input) {
									var dataInicialStr = input.value;
									var dataFinalStr = form.querySelector('[name="data_final"]').value;
									
									if (!dataInicialStr || !dataFinalStr) {
										return true; // Let notEmpty validator handle empty values
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
								message: 'A data final é obrigatória'
							},
							callback: {
								message: 'A data final não pode ser menor que a data inicial',
								callback: function(input) {
									var dataFinalStr = input.value;
									var dataInicialStr = form.querySelector('[name="data_inicial"]').value;
									
									if (!dataInicialStr || !dataFinalStr) {
										return true; // Let notEmpty validator handle empty values
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
					},
					modelo: {
						validators: {
							notEmpty: {
								message: 'Selecione o modelo de relatório'
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
					console.log('validated!');

					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click
						submitButton.disabled = true;

						// Montar URL com query params para abrir PDF em nova aba
						var params = new URLSearchParams();

						// Data inicial (converter dd/mm/yyyy → yyyy-mm-dd)
						var dataInicialStr = form.querySelector('[name="data_inicial"]').value;
						if (dataInicialStr) {
							var parts = dataInicialStr.split('/');
							if (parts.length === 3) {
								params.set('data_inicial', parts[2] + '-' + parts[1] + '-' + parts[0]);
							}
						}

						// Data final (converter dd/mm/yyyy → yyyy-mm-dd)
						var dataFinalStr = form.querySelector('[name="data_final"]').value;
						if (dataFinalStr) {
							var parts = dataFinalStr.split('/');
							if (parts.length === 3) {
								params.set('data_final', parts[2] + '-' + parts[1] + '-' + parts[0]);
							}
						}

						// Modelo (horizontal/vertical)
						var modeloRadio = form.querySelector('[name="modelo"]:checked');
						if (modeloRadio) {
							params.set('modelo', modeloRadio.value);
						}

						// Entidade financeira (caixa/banco) — só se filtro ativo
						var filtrarContas = document.getElementById('filtrar_contas');
						if (filtrarContas && filtrarContas.checked) {
							var contaIdVal = $(form.querySelector('[name="conta_id"]')).val();
							if (contaIdVal) {
								params.set('entidade_id', contaIdVal);
							}
						}

						// Tipo de data (competencia/pagamento)
						var tipoDataRadio = form.querySelector('[name="tipo_data"]:checked');
						if (tipoDataRadio) {
							params.set('tipo_data', tipoDataRadio.value);
						}

						// Situações (multi-select)
						var situacoesVal = $(form.querySelector('[name="situacoes[]"]')).val();
						if (situacoesVal && situacoesVal.length > 0) {
							params.set('situacoes', situacoesVal.join(','));
						}

						// Categorias financeiras (multi-select)
						var categoriasVal = $(form.querySelector('[name="categorias[]"]')).val();
						if (categoriasVal && categoriasVal.length > 0) {
							params.set('categorias', categoriasVal.join(','));
						}

						// Parceiro / Fornecedor
						var parceiroVal = $(form.querySelector('[name="parceiro_id"]')).val();
						if (parceiroVal) {
							params.set('parceiro_id', parceiroVal);
						}

						// Comprovação fiscal (checkbox)
						var comprovacaoFiscal = document.getElementById('comprovacao_fiscal');
						if (comprovacaoFiscal && comprovacaoFiscal.checked) {
							params.set('comprovacao_fiscal', '1');
						}

						// Tipo de valor (previsto/pago)
						var tipoValorRadio = form.querySelector('[name="tipo_valor"]:checked');
						if (tipoValorRadio) {
							params.set('tipo_valor', tipoValorRadio.value);
						}

						// Abrir PDF em nova aba
						var pdfUrl = '/relatorios/prestacao-de-contas/pdf?' + params.toString();
						window.open(pdfUrl, '_blank');

						// Restaurar botão e fechar modal
						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');
							submitButton.disabled = false;
							modal.hide();
						}, 1000);
					} else {
						// Show error message.
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
					form.reset(); // Reset form
					modal.hide(); // Hide modal
				} else if (result.dismiss === 'cancel') {
					Swal.fire({
						text: "Seu formulário não foi cancelado!.",
						icon: "error",
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
			modalEl = document.querySelector('#modal_prestacao_contas');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_prestacao_contas_form');
			submitButton = document.getElementById('kt_modal_prestacao_contas_submit');
			cancelButton = document.getElementById('kt_modal_prestacao_contas_cancel');

			// Load modal data via AJAX
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
