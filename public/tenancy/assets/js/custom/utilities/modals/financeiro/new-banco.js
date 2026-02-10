"use strict";

// Class definition
var DMModalNewBanco = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	// Init form inputs
	var initForm = function() {
		// Verificar se o formulário existe antes de tentar acessar seus elementos
		if (!form) {
			console.warn('[new-banco.js] Formulário não encontrado, pulando inicialização');
			return;
		}

		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/
		var tagsElement = form.querySelector('[name="tags"]');
		if (!tagsElement) {
			console.warn('[new-banco.js] Elemento tags não encontrado');
			return;
		}

		var tags = new Tagify(tagsElement, {
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
        var dueDates = document.querySelectorAll('[name=""]');

        dueDates.forEach(function(dueDate) {
            flatpickr(dueDate, {
                enableTime: true,
                dateFormat: "d/m/Y", // Formato pt-BR para exibição
                locale: "pt", // Define a localidade como português do Brasil
                defaultDate: new Date(), // Define a data atual como padrão
                maxDate: new Date(), // Não permite datas futuras, apenas até a data atual
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
		// Verificar se o formulário existe antes de inicializar validação
		if (!form) {
			console.warn('[new-banco.js] Formulário não encontrado, pulando validação');
			return;
		}

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
                        entidade_id: {
						validators: {
							notEmpty: {
								message: 'O banco é requerida!'
							}
						}
					},
                    data_competencia: {
                        validators: {
                            notEmpty: {
                                message: 'A data de competência é requerida!'
                            }
                        }
                    },
                    descricao: {
                        validators: {
                            notEmpty: {
                                message: 'A descrição é requerida!'
                            }
                        }
                    },
                    lancamento_padrao_id: {
                        validators: {
                            notEmpty: {
                                message: 'O lançamento padrão é requerido!'
                            }
                        }
                    },
                    cost_center_id: {
                        validators: {
                            notEmpty: {
                                message: 'O centro de custo é requerido!'
                            }
                        }
                    },
                    tipo_documento: {
                        validators: {
                            notEmpty: {
                                message: 'O tipo de documento é requerido!'
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

					// Garante que o tipo seja sempre enviado (mesmo se o select estiver desabilitado)
					var tipoSelect = form.querySelector('[name="tipo"]');
					var tipoHidden = form.querySelector('[name="tipo_hidden"]');
					var tipoValue = null;

					// Tenta obter do campo hidden primeiro (mais confiável quando desabilitado)
					if (tipoHidden && tipoHidden.value) {
						tipoValue = tipoHidden.value;
					}

					if (tipoSelect && !tipoValue) {
						// Tenta obter do select2 (mais confiável)
						var tipoSelect2 = $('#tipo_select_banco');
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

					// Atualiza o campo hidden com o valor obtido
					if (tipoHidden && tipoValue) {
						tipoHidden.value = tipoValue;
					}

					// Se o select estiver desabilitado, habilita temporariamente para garantir envio
					var wasDisabled = tipoSelect ? tipoSelect.disabled : false;
					if (tipoSelect && wasDisabled && tipoValue) {
						tipoSelect.disabled = false;
					}

					// Sempre envia o tipo (usando o campo hidden como fallback se necessário)
					formData.delete('tipo');
					formData.delete('tipo_hidden'); // Remove o campo hidden para não enviar duplicado

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

					// Formata a data para o formato esperado pelo backend (d-m-Y)
					var dataInput = form.querySelector('[name="data_competencia"]');
					if (dataInput && dataInput.value) {
						var dataValue = dataInput.value;
						// Remove a data antiga e adiciona a formatada
						formData.delete('data_competencia');

						// O flatpickr está configurado com dateFormat: "d-m-Y", então já deve estar no formato correto
						// Mas vamos garantir que está no formato d-m-Y
						if (dataValue.includes('-')) {
							var partes = dataValue.split('-');
							if (partes.length === 3) {
								if (partes[0].length === 4) {
									// Formato Y-m-d, converte para d-m-Y
									formData.append('data_competencia', partes[2] + '-' + partes[1] + '-' + partes[0]);
								} else {
									// Já está em d-m-Y ou d/m/Y
									formData.append('data_competencia', dataValue.replace(/\//g, '-'));
								}
							} else {
								formData.append('data_competencia', dataValue);
							}
						} else if (dataValue.includes('/')) {
							// Formato d/m/Y, converte para d-m-Y
							formData.append('data_competencia', dataValue.replace(/\//g, '-'));
						} else {
							formData.append('data_competencia', dataValue);
						}
					}
					var submitBtn = document.getElementById('Dm_modal_financeiro_submit');
					var cloneBtn = document.getElementById('Dm_modal_financeiro_clone');
					var novoBtn = document.getElementById('Dm_modal_financeiro_novo');

					// Desabilita todos os botões
					submitBtn.disabled = true;
					if (cloneBtn) cloneBtn.style.pointerEvents = 'none';
					if (novoBtn) novoBtn.style.pointerEvents = 'none';
					submitBtn.setAttribute('data-kt-indicator', 'on');

					// Obtém a URL do formulário
					var formAction = form.getAttribute('action');
					if (!formAction || formAction === '#') {
						// Tenta obter a rota do atributo data-action ou usa padrão
						formAction = form.getAttribute('data-action') || window.location.pathname + '/banco';
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
			var tipoSelect = $('#tipo_select_banco');
			var tipoValue = tipoSelect.val() || form.querySelector('[name="tipo"]').value;
			var tipoHidden = form.querySelector('[name="tipo_hidden"]');
			var tipoFinanceiro = form.querySelector('[name="tipo_financeiro"]').value;
			var origem = form.querySelector('[name="origem"]').value;

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

				'#lancamento_padraos_id',
				'#entidade_id',
				'#cost_center_id',
				'#tipo_documento',
				'#bancoSelect'
			];

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

			// Limpa anexos se existir o componente
			var anexosContainer = form.querySelector('.anexos-container');
			if (anexosContainer) {
				var anexosRows = anexosContainer.querySelectorAll('.anexo-row');
				anexosRows.forEach(function(row) {
					row.remove();
				});
			}

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
					}
				}
			}

			// Restaura tipo financeiro (receita/despesa)
			if (tipoFinanceiro) {
				var tipoFinanceiroField = form.querySelector('[name="tipo_financeiro"]');
				if (tipoFinanceiroField) {
					tipoFinanceiroField.value = tipoFinanceiro;
				}
			}

			// Restaura origem
			if (origem) {
				var origemField = form.querySelector('[name="origem"]');
				if (origemField) {
					origemField.value = origem;
				}
			}

			// Restaura status_pagamento
			var statusField = form.querySelector('[name="status_pagamento"]');
			if (statusField) {
				statusField.value = 'em aberto';
			}

			// Limpa erros de validação
			if (validator) {
				validator.resetForm();
			}

			// Esconde campo de banco de depósito se estiver visível
			$('#banco-deposito').hide();

			// Refiltra os lançamentos padrão baseado no tipo mantido
			if (tipoValue) {
				var lancamentoPadraoSelect = $('#lancamento_padraos_id');
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
		window.enviarFormularioBanco = enviarFormulario;

		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
				text: "Tem certeza de que deseja cancelar?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Sim, cancelar!",
				cancelButtonText: "Não, voltar",
				customClass: {
					confirmButton: "btn btn-sm btn-danger",
					cancelButton: "btn btn-sm btn-primary-light"
				}
			}).then(function (result) {
				if (result.value) {
					form.reset(); // Reset form
					modal.hide(); // Hide modal
				} else if (result.dismiss === 'cancel') {
					Swal.fire({
						text: "Seu formulário não foi cancelado!!!!",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, entendi!",
						customClass: {
							confirmButton: "btn btn-sm btn-primary",
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
			modalEl = document.querySelector('#Dm_modal_financeiro');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#Dm_modal_financeiro_form');
			submitButton = document.getElementById('Dm_modal_financeiro_submit');
			cancelButton = document.getElementById('Dm_modal_financeiro_cancel');

			initForm();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	DMModalNewBanco.init();

	// Adiciona event listeners para os botões de ação adicional
	$(document).on('click', '#Dm_modal_financeiro_clone', function(e) {
		e.preventDefault();
		if (typeof window.enviarFormularioBanco === 'function') {
			window.enviarFormularioBanco('clonar');
		}
	});

	$(document).on('click', '#Dm_modal_financeiro_novo', function(e) {
		e.preventDefault();
		if (typeof window.enviarFormularioBanco === 'function') {
			window.enviarFormularioBanco('branco');
		}
	});
});

// Manipulação do modal baseado no tipo de lançamento (Receita/Despesa)
$(document).ready(function() {
	var tipoLancamento = null; // Variável para armazenar o tipo do lançamento

	// Captura o evento de abertura do modal
	$('#Dm_modal_financeiro').on('show.bs.modal', function(event) {
		// Obtém o botão que acionou o modal
		var button = $(event.relatedTarget);
		// Obtém o tipo do atributo data-tipo e armazena
		tipoLancamento = button.data('tipo');
		var modal = $(this);
		var modalTitle = modal.find('#modal_financeiro_title');
		var tipoFinanceiroInput = modal.find('#tipo_financeiro');

		// Define o título e configura o tipo baseado no botão clicado
		if (tipoLancamento === 'receita') {
			modalTitle.text('Nova Receita');
			// Define o valor no campo hidden
			tipoFinanceiroInput.val('receita');
		} else if (tipoLancamento === 'despesa') {
			modalTitle.text('Nova Despesa');
			// Define o valor no campo hidden
			tipoFinanceiroInput.val('despesa');
		} else {
			// Se não houver tipo definido, mantém o padrão
			modalTitle.text('Novo Lançamento');
			tipoFinanceiroInput.val('');
		}
	});

	// Aguarda o modal ser completamente exibido para manipular o select2
	$('#Dm_modal_financeiro').on('shown.bs.modal', function() {
		var modal = $(this);
		var tipoSelect = modal.find('#tipo_select_banco');
		var lancamentoPadraoSelect = modal.find('#lancamento_padraos_id');

		// Aguarda um pequeno delay para garantir que o select2 foi inicializado
		setTimeout(function() {
			var tipoHidden = modal.find('#tipo_hidden');

			if (tipoLancamento === 'receita') {
				// Seleciona "entrada" no select
				tipoSelect.val('entrada').trigger('change');
				// Atualiza o campo hidden
				if (tipoHidden.length > 0) {
					tipoHidden.val('entrada');
				}
				// Desabilita o select e atualiza o select2
				tipoSelect.prop('disabled', true);
				if (tipoSelect.hasClass('select2-hidden-accessible')) {
					tipoSelect.select2('enable', false);
				}
				// Filtra os lançamentos padrão para entrada
				filtrarLancamentosPadrao('entrada', lancamentoPadraoSelect);
			} else if (tipoLancamento === 'despesa') {
				// Seleciona "saida" no select
				tipoSelect.val('saida').trigger('change');
				// Atualiza o campo hidden
				if (tipoHidden.length > 0) {
					tipoHidden.val('saida');
				}
				// Desabilita o select e atualiza o select2
				tipoSelect.prop('disabled', true);
				if (tipoSelect.hasClass('select2-hidden-accessible')) {
					tipoSelect.select2('enable', false);
				}
				// Filtra os lançamentos padrão para saída
				filtrarLancamentosPadrao('saida', lancamentoPadraoSelect);
			} else {
				// Se não houver tipo definido, mantém habilitado
				tipoSelect.prop('disabled', false);
				if (tipoSelect.hasClass('select2-hidden-accessible')) {
					tipoSelect.select2('enable', true);
				}
				// Limpa o campo hidden
				if (tipoHidden.length > 0) {
					tipoHidden.val('');
				}
			}

			// Configura o select2 de lançamento padrão para permitir busca
			configurarSelect2LancamentoPadrao(lancamentoPadraoSelect);
		}, 100);
	});

	// Função para filtrar lançamentos padrão baseado no tipo
	function filtrarLancamentosPadrao(tipo, $select) {
		// Verifica se existe lpsData (dados dos lançamentos padrão)
		if (typeof lpsData === 'undefined') {
			// Se não existir, filtra usando as opções existentes no DOM
			var todasOpcoes = $select.find('option');
			todasOpcoes.each(function() {
				var $option = $(this);
				if ($option.val() !== '') {
					var optionType = $option.data('type');
					if (optionType === tipo) {
						$option.prop('disabled', false);
					} else {
						$option.prop('disabled', true);
					}
				}
			});
		} else {
			// Se existir lpsData, recria as opções
			// Salva o valor selecionado antes de limpar
			var valorSelecionado = $select.val();

			// Limpa todas as opções exceto a vazia
			$select.find('option:not([value=""])').remove();

			// Adiciona apenas as opções do tipo correspondente
			lpsData.forEach(function(lp) {
				if (lp.type === tipo) {
					var option = $('<option></option>')
						.attr('value', lp.id)
						.attr('data-description', lp.description)
						.attr('data-type', lp.type)
						.text(lp.id + ' - ' + lp.description);
					$select.append(option);
				}
			});

			// Restaura o valor selecionado se ainda existir
			if (valorSelecionado && $select.find('option[value="' + valorSelecionado + '"]').length > 0) {
				$select.val(valorSelecionado);
			}
		}

		// Limpa a seleção atual se não houver valor válido
		if (!$select.val() || $select.find('option[value="' + $select.val() + '"]:not(:disabled)').length === 0) {
			$select.val('').trigger('change');
		}

		// Atualiza o select2
		if ($select.hasClass('select2-hidden-accessible')) {
			$select.select2('destroy');
		}
		$select.removeAttr('data-kt-initialized');
		configurarSelect2LancamentoPadrao($select);
	}

	// Função para configurar o select2 de lançamento padrão com busca
	function configurarSelect2LancamentoPadrao($select) {
		$select.select2({
			placeholder: "Escolha um Lançamento...",
			allowClear: true,
			dropdownParent: $('#Dm_modal_financeiro'),
			minimumResultsForSearch: 0, // Permite busca mesmo com poucas opções
			language: {
				noResults: function() {
					return "Nenhum lançamento encontrado";
				},
				searching: function() {
					return "Buscando...";
				}
			}
		});
	}

	// Monitora mudanças no select de tipo (caso seja habilitado no futuro)
	$('#Dm_modal_financeiro').on('change', '#tipo_select_banco', function() {
		var tipoSelecionado = $(this).val();
		var lancamentoPadraoSelect = $('#lancamento_padraos_id');
		var tipoHidden = $('#tipo_hidden');

		// Atualiza o campo hidden quando o tipo muda
		if (tipoHidden.length > 0) {
			tipoHidden.val(tipoSelecionado || '');
		}

		if (tipoSelecionado === 'entrada' || tipoSelecionado === 'saida') {
			filtrarLancamentosPadrao(tipoSelecionado, lancamentoPadraoSelect);
		}
	});

	// Quando o modal é fechado, reseta os valores
	$('#Dm_modal_financeiro').on('hidden.bs.modal', function() {
		var modal = $(this);
		var tipoSelect = modal.find('#tipo_select_banco');
		var tipoFinanceiroInput = modal.find('#tipo_financeiro');
		var modalTitle = modal.find('#modal_financeiro_title');
		var lancamentoPadraoSelect = modal.find('#lancamento_padraos_id');

		// Reseta o select de tipo
		tipoSelect.val('').trigger('change');
		tipoSelect.prop('disabled', false);
		// Habilita o select2 novamente
		if (tipoSelect.hasClass('select2-hidden-accessible')) {
			tipoSelect.select2('enable', true);
		}

		// Restaura todas as opções de lançamento padrão
		if (typeof lpsData !== 'undefined') {
			// Se existir lpsData, recria todas as opções
			lancamentoPadraoSelect.find('option:not([value=""])').remove();
			lpsData.forEach(function(lp) {
				var option = $('<option></option>')
					.attr('value', lp.id)
					.attr('data-description', lp.description)
					.attr('data-type', lp.type)
					.text(lp.id + ' - ' + lp.description);
				lancamentoPadraoSelect.append(option);
			});
		} else {
			// Se não existir, apenas habilita todas as opções
			lancamentoPadraoSelect.find('option').each(function() {
				$(this).prop('disabled', false);
			});
		}
		lancamentoPadraoSelect.val('').trigger('change');

		// Limpa o campo hidden
		tipoFinanceiroInput.val('');
		// Reseta o título
		modalTitle.text('Novo Lançamento');
		// Reseta a variável
		tipoLancamento = null;
	});
});


$(document).ready(function() {
    $('#lancamento_padrao_banco').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === 'Deposito Bancário') {
            $('#banco-deposito').show(); // Mostra o campo do banco de depósito
        } else {
            $('#banco-deposito').hide(); // Esconde o campo do banco de depósito
        }
    });
});



document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_select_banco');
    const lancamentoPadraoSelect = document.getElementById('lancamento_padrao_banco');

    // Função para inicializar o Select2
    const initializeSelect2 = () => {
        $('#lancamento_padrao_banco').select2({
            placeholder: 'Escolha um Lançamento...',
            width: '100%'
        });
    };

    tipoSelect.addEventListener('change', function() {
        const selectedTipo = tipoSelect.value;

        // Limpa todas as opções do select de Lançamento Padrão
        lancamentoPadraoSelect.innerHTML = '';

        // Adiciona a opção vazia
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.text = 'Escolha um Lançamento...';
        lancamentoPadraoSelect.appendChild(emptyOption);

        // Filtra e adiciona as opções de acordo com o tipo selecionado
        lpsData.forEach(function(lp) {
            if (lp.type === selectedTipo) {
                const option = document.createElement('option');
                option.value = lp.id;
                option.text = lp.description;
                lancamentoPadraoSelect.appendChild(option);
            }
        });

        // Recarrega o Select2 após atualizar as opções
        initializeSelect2();
    });

    // Inicializa o Select2 ao carregar a página
    initializeSelect2();
});

// Configuração do Select2 para exibir ícones dos bancos
$(document).ready(function() {
    // Aguarda o modal ser aberto para inicializar o select2 com ícones
    $('#Dm_modal_financeiro').on('shown.bs.modal', function() {
        // Aguarda um pequeno delay para garantir que o select2 automático já foi inicializado
        setTimeout(function() {
            var $select = $('#entidade_id');

            // Verifica se o select2 já foi inicializado e destrói
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            // Remove o atributo de inicialização para evitar reinicialização automática
            $select.removeAttr('data-kt-initialized');

            // Inicializa o Select2 com configuração de ícones
            $select.select2({
                placeholder: "Selecione o Banco",
                allowClear: true,
                dropdownParent: $('#Dm_modal_financeiro'),
                minimumResultsForSearch: Infinity, // Esconde a busca conforme configurado no HTML

                // Exibir ícone no menu suspenso
                templateResult: function(state) {
                    // Se for placeholder ou sem valor, retornar o texto normal
                    if (!state.id) {
                        return state.text;
                    }

                    // Recupera o caminho do ícone do atributo data-icon
                    let iconUrl = $(state.element).attr('data-icon');
                    let nomeCompleto = $(state.element).attr('data-nome') || state.text;

                    // Se não tiver ícone, retorna apenas o texto
                    if (!iconUrl) {
                        return state.text;
                    }

                    // Monta um elemento com img + texto principal e secundário
                    let $state = $(`
                        <div class="d-flex align-items-center">
                            <img src="${iconUrl}" class="me-2" style="width:24px; height:24px; object-fit: contain;" />
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">${state.text}</span>
                                <span class="text-muted fs-7">${nomeCompleto}</span>
                            </div>
                        </div>
                    `);

                    return $state;
                },

                // Exibir ícone na opção selecionada
                templateSelection: function(state) {
                    if (!state.id) {
                        return state.text;
                    }

                    let iconUrl = $(state.element).attr('data-icon');
                    let nomeCompleto = $(state.element).attr('data-nome') || state.text;

                    // Se não tiver ícone, retorna apenas o texto
                    if (!iconUrl) {
                        return state.text;
                    }

                    // Na seleção, mostra apenas o logo e o texto principal (mais compacto)
                    let $state = $(`
                        <span class="d-flex align-items-center">
                            <img src="${iconUrl}" class="me-2" style="width:24px; height:24px; object-fit: contain;" />
                            <span>${state.text}</span>
                        </span>
                    `);
                    return $state;
                },
            });
        }, 100);
    });
});

