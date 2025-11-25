"use strict";

// Class definition
var DMModalNewCaixa = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	// Init form inputs
	var initForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/
		var tags = new Tagify(form.querySelector('[name="tags"]'), {
			whitelist: ["Important", "Urgent", "High", "Medium", "Low"],
			maxTags: 5,
			dropdown: {
				maxItems: 10,           // <- mixumum allowed rendered suggestions
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		});
		tags.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('tags');
		});

        // Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
        var dueDates = document.querySelectorAll('[name="data_competencia"]');

        dueDates.forEach(function(dueDate) {
            flatpickr(dueDate, {
                enableTime: false,
                dateFormat: "d/m/Y", // Formato pt-BR para exibição
                locale: "pt", // Define a localidade como português do Brasil
                defaultDate: new Date(), // Define a data atual como padrão
                maxDate: new Date() // Não permite datas futuras, apenas até a data atual
            });
        });


		// Team assign. For more info, plase visit the official plugin site: https://select2.org/
        $(form.querySelector('[name="team_assign"]')).on('change', function() {
            // Revalidate the field when an option is chosen
            validator.revalidateField('team_assign');
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
					data: {
						validators: {
							notEmpty: {
								message: 'A data é requerida!'
							}
						}
					},
					valor: {
                        validators: {
                            notEmpty: {
                                message: 'O valor é obrigatório'
                            },
                            callback: {
                                message: 'O valor não pode conter apenas uma vírgula',
                                callback: function(input) {
                                    // Verifica se o valor é uma vírgula ou uma string vazia
                                    var value = input.value.trim();
                                    return value !== ',';  // Retorna falso se o valor for apenas uma vírgula
                                }
                            }
                        }
                    },
					tipo: {
						validators: {
							notEmpty: {
								message: 'O tipo é requerido.'
							}
						}
					},
					lancamento_padrao: {
						validators: {
							notEmpty: {
								message: 'Lançamento padrão é obrigatório.'
							}
						}
					},
					'targets_notifications[]': {
                        validators: {
                            notEmpty: {
                                message: 'Please select at least one communication method'
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

		// Função para enviar formulário via AJAX (tornada acessível globalmente)
		var enviarFormulario = function(mode) {
			if (!validator) return;

			validator.validate().then(function (status) {
				if (status == 'Valid') {
					// Prepara os dados do formulário
					var formData = new FormData(form);

					// Garante que o tipo seja sempre enviado
					var tipoSelect = form.querySelector('[name="tipo"]');
					var tipoHidden = form.querySelector('[name="tipo_hidden"]');
					var tipoValue = null;

					// Tenta obter do campo hidden primeiro (se existir)
					if (tipoHidden && tipoHidden.value) {
						tipoValue = tipoHidden.value;
					}

					if (tipoSelect && !tipoValue) {
						// Tenta obter do select2 (mais confiável)
						var tipoSelect2 = $('#tipo_select_caixa');
						if (tipoSelect2.length > 0 && tipoSelect2.hasClass('select2-hidden-accessible')) {
							tipoValue = tipoSelect2.val();
						}

						// Se não obteve do select2, tenta do elemento nativo
						if (!tipoValue) {
							tipoValue = tipoSelect.value;
						}

						// Se ainda não tem valor, tenta obter da opção selecionada
						if (!tipoValue && tipoSelect.options.length > 0) {
							var selectedOption = tipoSelect.options[tipoSelect.selectedIndex];
							if (selectedOption && selectedOption.value) {
								tipoValue = selectedOption.value;
							}
						}
					}

					// Atualiza o campo hidden com o valor obtido (se existir)
					if (tipoHidden && tipoValue) {
						tipoHidden.value = tipoValue;
					}

					// Se o select estiver desabilitado, habilita temporariamente para garantir envio
					var wasDisabled = tipoSelect ? tipoSelect.disabled : false;
					if (tipoSelect && wasDisabled && tipoValue) {
						tipoSelect.disabled = false;
					}

					// Sempre envia o tipo
					formData.delete('tipo');
					if (tipoHidden) {
						formData.delete('tipo_hidden'); // Remove o campo hidden para não enviar duplicado
					}

					if (tipoValue) {
						formData.append('tipo', tipoValue);
					} else {
						// Se não tem valor, envia vazio para que a validação mostre o erro
						formData.append('tipo', '');
					}

					// Restaura o estado desabilitado se necessário
					if (tipoSelect && wasDisabled) {
						tipoSelect.disabled = true;
					}

					// Garante que comprovacao_fiscal seja enviado como 0 ou 1
					var comprovacaoFiscal = form.querySelector('[name="comprovacao_fiscal"]');
					if (comprovacaoFiscal) {
						formData.delete('comprovacao_fiscal');
						formData.append('comprovacao_fiscal', comprovacaoFiscal.checked ? '1' : '0');
					}

					// Formata a data para o formato esperado pelo backend
					var dataInput = form.querySelector('[name="data_competencia"]');
					if (dataInput && dataInput.value) {
						var dataValue = dataInput.value;
						formData.delete('data_competencia');

						// O flatpickr está configurado com dateFormat: "d/m/Y"
						if (dataValue.includes('/')) {
							// Formato d/m/Y, converte para d-m-Y
							formData.append('data_competencia', dataValue.replace(/\//g, '-'));
						} else if (dataValue.includes('-')) {
							var partes = dataValue.split('-');
							if (partes.length === 3) {
								if (partes[0].length === 4) {
									// Formato Y-m-d, converte para d-m-Y
									formData.append('data_competencia', partes[2] + '-' + partes[1] + '-' + partes[0]);
								} else {
									// Já está em d-m-Y
									formData.append('data_competencia', dataValue);
								}
							} else {
								formData.append('data_competencia', dataValue);
							}
						} else {
							formData.append('data_competencia', dataValue);
						}
					}

					var submitBtn = document.getElementById('kt_modal_new_target_submit');
					var cloneBtn = document.getElementById('kt_modal_new_target_clone');
					var novoBtn = document.getElementById('kt_modal_new_target_novo');

					// Desabilita todos os botões
					submitBtn.disabled = true;
					if (cloneBtn) cloneBtn.style.pointerEvents = 'none';
					if (novoBtn) novoBtn.style.pointerEvents = 'none';
					submitBtn.setAttribute('data-kt-indicator', 'on');

					// Obtém a URL do formulário
					var formAction = form.getAttribute('action');
					if (!formAction || formAction === '#') {
						// Tenta obter a rota do atributo data-action ou usa padrão
						formAction = form.getAttribute('data-action') || window.location.pathname + '/caixa';
					}

					// Envia via AJAX
					fetch(formAction, {
						method: 'POST',
						body: formData,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						},
						redirect: 'follow'
					})
					.then(response => {
						// Verifica se a resposta é OK (status 200-299)
						if (response.ok) {
							// Tenta parsear como JSON, se falhar assume sucesso
							return response.json().catch(() => ({ success: true }));
						}
						// Se for redirect (302), assume sucesso
						if (response.status === 302 || response.redirected) {
							return { success: true };
						}
						// Se for erro 422 (validação), tenta obter as mensagens de erro
						if (response.status === 422) {
							return response.json().then(data => {
								var errorMessages = [];
								if (data.errors) {
									// Extrai todas as mensagens de erro
									Object.keys(data.errors).forEach(function(key) {
										if (Array.isArray(data.errors[key])) {
											errorMessages = errorMessages.concat(data.errors[key]);
										} else {
											errorMessages.push(data.errors[key]);
										}
									});
								}
								if (data.message) {
									errorMessages.push(data.message);
								}
								if (errorMessages.length === 0) {
									errorMessages.push('Erro de validação. Verifique os campos preenchidos.');
								}
								throw new Error(errorMessages.join('\n'));
							});
						}
						// Se houver outro erro, tenta obter a mensagem
						return response.text().then(text => {
							throw new Error(text || 'Erro ao salvar');
						});
					})
					.then(data => {
						submitBtn.removeAttribute('data-kt-indicator');
						submitBtn.disabled = false;
						if (cloneBtn) cloneBtn.style.pointerEvents = 'auto';
						if (novoBtn) novoBtn.style.pointerEvents = 'auto';

						Swal.fire({
							text: "O lançamento foi salvo com sucesso!",
							icon: "success",
							buttonsStyling: false,
							confirmButtonText: "Ok, entendi!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						}).then(function (result) {
							if (result.isConfirmed || result.value) {
								if (mode === 'enviar') {
									// Modo 1: Enviar - Fecha o modal e recarrega a página
									modal.hide();
									// Recarrega a página para atualizar a lista
									setTimeout(function() {
										window.location.reload();
									}, 300);
								} else if (mode === 'clonar') {
									// Modo 2: Salvar e Clonar - Mantém dados e modal aberto
									// Não faz nada, mantém tudo como está para permitir novo envio
									// Apenas remove o indicador de loading
								} else if (mode === 'branco') {
									// Modo 3: Salvar e em Branco - Limpa formulário mantendo data e tipo
									limparFormularioMantendoDataTipo();
								}
							}
						});
					})
					.catch(error => {
						submitBtn.removeAttribute('data-kt-indicator');
						submitBtn.disabled = false;
						if (cloneBtn) cloneBtn.style.pointerEvents = 'auto';
						if (novoBtn) novoBtn.style.pointerEvents = 'auto';

						// Mostra mensagem de erro mais detalhada
						var errorMessage = error.message || "Erro ao salvar o lançamento. Por favor, tente novamente.";

						Swal.fire({
							title: "Erro ao salvar",
							html: errorMessage.replace(/\n/g, '<br>'),
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, entendi!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
					});
				} else {
					// Mostrar mensagem de erro de validação
					Swal.fire({
						text: "Desculpe, parece que alguns erros foram detectados, por favor tente novamente.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, entendi!",
						customClass: {
							confirmButton: "btn btn-primary"
						}
					});
				}
			});
		};

		// Função para limpar formulário mantendo data e tipo
		var limparFormularioMantendoDataTipo = function() {
			// Salva os valores de data e tipo antes de limpar
			var dataCompetencia = form.querySelector('[name="data_competencia"]').value;
			var tipoSelect = $('#tipo_select_caixa');
			var tipoValue = tipoSelect.val() || form.querySelector('[name="tipo"]').value;
			var tipoHidden = form.querySelector('[name="tipo_hidden"]');
			var origem = form.querySelector('[name="origem"]');
			var origemValue = origem ? origem.value : 'Caixa';

			// Limpa todos os campos de texto
			var descricaoField = form.querySelector('[name="descricao"]');
			if (descricaoField) descricaoField.value = '';
			
			var valorField = form.querySelector('[name="valor"]');
			if (valorField) valorField.value = '';
			
			var numeroDocField = form.querySelector('[name="numero_documento"]');
			if (numeroDocField) numeroDocField.value = '';
			
			var historicoField = form.querySelector('[name="historico_complementar"]');
			if (historicoField) historicoField.value = '';

			// Limpa checkbox de comprovação fiscal
			var comprovacaoCheckbox = form.querySelector('[name="comprovacao_fiscal"][type="checkbox"]');
			if (comprovacaoCheckbox) {
				comprovacaoCheckbox.checked = false;
			}

			// Limpa os selects usando Select2
			var selectsToClear = [
				'#lancamento_padrao_caixa',
				'#entidade_id',
				'#tipo_documento',
				'#bancoSelect'
			];

			// Nota: O campo centro (centro de custo) no caixa é readonly e não precisa ser limpo

			selectsToClear.forEach(function(selector) {
				var $select = $(selector);
				if ($select.length > 0) {
					if ($select.hasClass('select2-hidden-accessible')) {
						$select.val(null).trigger('change');
					} else {
						$select.val('');
					}
				}
			});

			// Restaura data e tipo
			if (dataCompetencia) {
				var dataField = form.querySelector('[name="data_competencia"]');
				if (dataField) dataField.value = dataCompetencia;
			}

			// Restaura tipo (entrada/saída)
			if (tipoValue) {
				var tipoNativeField = form.querySelector('[name="tipo"]');
				if (tipoNativeField) {
					tipoNativeField.value = tipoValue;
				}
				if (tipoHidden) {
					tipoHidden.value = tipoValue;
				}
				// Atualiza o Select2 do tipo
				if (tipoSelect.length > 0) {
					if (tipoSelect.hasClass('select2-hidden-accessible')) {
						tipoSelect.val(tipoValue).trigger('change');
					} else {
						tipoSelect.val(tipoValue);
						// Se não for Select2, tenta inicializar
						if (typeof KTSelect2 !== 'undefined') {
							new KTSelect2(tipoSelect[0]);
						}
					}
				}
			}

			// Restaura origem
			if (origem) {
				origem.value = origemValue;
			}

			// Limpa erros de validação
			if (validator) {
				validator.resetForm();
			}

			// Esconde campo de banco de depósito se estiver visível
			$('#banco-deposito').hide();

			// Refiltra os lançamentos padrão baseado no tipo mantido
			if (tipoValue) {
				var lancamentoPadraoSelect = $('#lancamento_padrao_caixa');
				if (lancamentoPadraoSelect.length > 0) {
					// Filtra usando as opções existentes no DOM
					lancamentoPadraoSelect.find('option').each(function() {
						var $option = $(this);
						var optionType = $option.data('type');
						
						// Se for a opção vazia, mantém visível
						if ($option.val() === '' || !optionType) {
							$option.prop('disabled', false).show();
						} else if (optionType === tipoValue) {
							// Mostra opções do tipo correto
							$option.prop('disabled', false).show();
						} else {
							// Esconde opções de outro tipo
							$option.prop('disabled', true).hide();
						}
					});
					
					// Atualiza o Select2
					setTimeout(function() {
						if (lancamentoPadraoSelect.hasClass('select2-hidden-accessible')) {
							lancamentoPadraoSelect.select2('destroy');
						}
						// Reinicializa o Select2
						if (typeof KTSelect2 !== 'undefined') {
							new KTSelect2(lancamentoPadraoSelect[0]);
						} else if (typeof lancamentoPadraoSelect.select2 !== 'undefined') {
							lancamentoPadraoSelect.select2();
						}
					}, 50);
				}
			}

			// Foca no primeiro campo após limpar
			setTimeout(function() {
				var firstField = form.querySelector('[name="descricao"]') || form.querySelector('[name="entidade_id"]');
				if (firstField) {
					firstField.focus();
				}
			}, 200);
		};

		// Action buttons
		// Botão Enviar (modo 1: Salvar e fechar)
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();
			enviarFormulario('enviar');
		});

		// Torna a função acessível globalmente para os outros botões
		window.enviarFormularioCaixa = enviarFormulario;

		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
				text: "Are you sure you would like to cancel?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Yes, cancel it!",
				cancelButtonText: "No, return",
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
						text: "Seu formulário não foi cancelado!",
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
			modalEl = document.querySelector('#kt_modal_new_target');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_new_target_form');
			submitButton = document.getElementById('kt_modal_new_target_submit');
			cancelButton = document.getElementById('kt_modal_new_target_cancel');

			initForm();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	DMModalNewCaixa.init();

	// Adiciona event listeners para os botões de ação adicional
	$(document).on('click', '#kt_modal_new_target_clone', function(e) {
		e.preventDefault();
		if (typeof window.enviarFormularioCaixa === 'function') {
			window.enviarFormularioCaixa('clonar');
		}
	});

	$(document).on('click', '#kt_modal_new_target_novo', function(e) {
		e.preventDefault();
		if (typeof window.enviarFormularioCaixa === 'function') {
			window.enviarFormularioCaixa('branco');
		}
	});
});
