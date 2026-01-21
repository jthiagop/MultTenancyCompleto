<script>
// Função global para inicializar e carregar dados do select de configuração de recorrência
// Pode ser chamada tanto do modal quanto do drawer
function carregarConfiguracaoRecorrencia(parentElement) {
    var configuracaoRecorrenciaSelect = $('#configuracao_recorrencia');
    
    if (!configuracaoRecorrenciaSelect.length) {
        console.warn('[Recorrencia] Select de configuração de recorrência não encontrado');
        return;
    }
    
    console.log('[Recorrencia] Inicializando select de configuração de recorrência...');
    
    // Remove atributo de inicialização para forçar reinicialização
    configuracaoRecorrenciaSelect.removeAttr('data-kt-initialized');
    
    // Se já foi inicializado, destroi
    if (configuracaoRecorrenciaSelect.hasClass('select2-hidden-accessible')) {
        configuracaoRecorrenciaSelect.select2('destroy');
    }
    
    // Determina o elemento pai para o dropdown
    var dropdownParent = parentElement || $('#Dm_modal_financeiro');
    if (!dropdownParent.length || !dropdownParent.is(':visible')) {
        dropdownParent = $('body'); // Fallback para body se o modal não estiver visível
    }
    
    // Reinicializa o Select2
    setTimeout(function() {
        if (typeof KTSelect2 !== 'undefined') {
            new KTSelect2(configuracaoRecorrenciaSelect[0]);
        } else {
            configuracaoRecorrenciaSelect.select2({
                dropdownParent: dropdownParent,
                placeholder: 'Selecione uma configuração',
                allowClear: true,
                minimumResultsForSearch: 0
            });
        }
        
        // Carrega configurações existentes do banco
        $.ajax({
            url: '{{ route("recorrencias.index") }}',
            method: 'GET',
            success: function(response) {
                console.log('[Recorrencia] Resposta do servidor:', response);
                if (response.success && response.data) {
                    // Remove opções existentes (exceto a vazia)
                    configuracaoRecorrenciaSelect.find('option[value!=""]').remove();
                    
                    // Adiciona cada configuração como opção
                    response.data.forEach(function(config) {
                        var option = $('<option></option>')
                            .attr('value', config.id)
                            .text(config.nome);
                        configuracaoRecorrenciaSelect.append(option);
                    });
                    
                    console.log('[Recorrencia] ' + response.data.length + ' configurações carregadas');
                    
                    // Atualiza o Select2
                    if (configuracaoRecorrenciaSelect.hasClass('select2-hidden-accessible')) {
                        configuracaoRecorrenciaSelect.trigger('change.select2');
                    }
                }
            },
            error: function(xhr) {
                console.error('[Recorrencia] Erro ao carregar configurações de recorrência:', xhr);
            }
        });
        
        // Adiciona botão "Adicionar Configuração de Recorrência" no footer do Select2
        // Remove evento anterior para evitar múltiplos bindings
        configuracaoRecorrenciaSelect.off('select2:open.recorrencia');
        configuracaoRecorrenciaSelect.on('select2:open.recorrencia', function() {
            var $dropdown = $('.select2-container--open');
            var $results = $dropdown.find('.select2-results');
            
            // Remove botão anterior se existir
            $results.find('.select2-add-recorrencia-footer').remove();
            
            // Adiciona footer com botão
            var $footer = $(
                '<div class="select2-add-recorrencia-footer border-top p-2 text-center"></div>'
            );
            var $button = $(
                '<button type="button" class="btn btn-sm btn-light-primary w-100"><i class="fas fa-plus"></i> Adicionar Configuração de Recorrência</button>'
            );
            $footer.append($button);
            $results.append($footer);
            
            // Evento de clique no botão
            $button.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Fecha o Select2
                configuracaoRecorrenciaSelect.select2('close');
                
                // Abre o drawer de recorrência
                var drawerRecorrenciaElement = document.querySelector('#kt_drawer_recorrencia');
                var modalElement = document.querySelector('#Dm_modal_financeiro');
                
                if (drawerRecorrenciaElement && typeof KTDrawer !== 'undefined') {
                    var drawer = KTDrawer.getInstance(drawerRecorrenciaElement);
                    
                    if (drawer) {
                        // Disable modal focus trap when drawer opens
                        if (modalElement && modalElement.classList.contains('show')) {
                            var bsModal = bootstrap.Modal.getInstance(modalElement);
                            
                            if (bsModal) {
                                // CRÍTICO: Desativa o FocusTrap do Bootstrap Modal
                                if (bsModal._focustrap) {
                                    bsModal._focustrap.deactivate();
                                }
                                
                                // Remove tabindex do modal
                                modalElement.removeAttribute('tabindex');
                            }
                        }
                        
                        drawer.show();
                    }
                }
            });
        });
    }, 100);
}

// Configura o modal dinamicamente baseado na origem (Banco ou Caixa) e tipo (receita/despesa)
                        $(document).ready(function() {
                            var tipoLancamento = null; // Variável para armazenar o tipo do lançamento

                            $('#Dm_modal_financeiro').on('show.bs.modal', function(event) {
                                var button = $(event.relatedTarget);
                                var origem = button.data('origem') || 'Banco'; // Padrão: Banco
                                tipoLancamento = button.data('tipo'); // Receita ou Despesa
                                var modal = $(this);
                                var form = modal.find('#Dm_modal_financeiro_form');
                                var origemInput = modal.find('#origem');
                                var entidadeSelect = modal.find('#entidade_id');
                                var labelEntidade = modal.find('#label_entidade');
                                var modalTitle = modal.find('#modal_financeiro_title');
                                var tipoFinanceiroInput = modal.find('#tipo_financeiro');
                                var modalIcon = modal.find('#modal_financeiro_icon');

                                // Atualiza o título do modal baseado no tipo
                                if (tipoLancamento === 'receita') {
                                    modalTitle.text('Nova Receita');
                                    tipoFinanceiroInput.val('receita');
                                    modalIcon.attr('class', 'fa-regular fa-circle-up text-success');
                                } else if (tipoLancamento === 'despesa') {
                                    modalTitle.text('Nova Despesa');
                                    tipoFinanceiroInput.val('despesa');
                                    modalIcon.attr('class', 'fa-regular fa-circle-down text-danger');
                                } else {
                                    modalTitle.text('Novo Lançamento');
                                    tipoFinanceiroInput.val('');
                                    modalIcon.attr('class', 'fa-regular');
                                }

                                // Atualiza o campo hidden de origem
                                origemInput.val(origem);

                                // Atualiza a action do form baseado na origem
                                if (origem === 'Caixa') {
                                    form.attr('action', '{{ route('caixa.store') }}');
                                    labelEntidade.text('Entidade');
                                    entidadeSelect.attr('data-placeholder', 'Selecione a Entidade');
                                } else {
                                    form.attr('action', '{{ route('banco.store') }}');
                                    labelEntidade.text('Banco');
                                    entidadeSelect.attr('data-placeholder', 'Selecione o Banco');
                                }

                                // Filtra as opções do select baseado na origem
                                entidadeSelect.find('option').each(function() {
                                    var optionOrigem = $(this).data('origem');
                                    if (optionOrigem && optionOrigem !== origem) {
                                        $(this).prop('disabled', true).hide();
                                    } else {
                                        $(this).prop('disabled', false).show();
                                    }
                                });

                                // Limpa a seleção
                                entidadeSelect.val(null);

                                // Reinicializa o Select2 se necessário
                                setTimeout(function() {
                                    if (entidadeSelect.hasClass('select2-hidden-accessible')) {
                                        entidadeSelect.select2('destroy');
                                    }
                                    // Reinicializa o Select2
                                    if (typeof KTSelect2 !== 'undefined') {
                                        new KTSelect2(entidadeSelect[0]);
                                    }
                                }, 100);
                            });

                            // Aguarda o modal ser completamente exibido para definir o tipo no campo hidden
                            $('#Dm_modal_financeiro').on('shown.bs.modal', function() {
                                var modal = $(this);
                                var lancamentoPadraoSelect = modal.find('#lancamento_padraos_id');
                                var tipoDocumentoSelect = modal.find('#tipo_documento');
                                var fornecedorSelect = modal.find('#fornecedor_id');
                                var costCenterSelect = modal.find('#cost_center_id');
                                var configuracaoRecorrenciaSelect = modal.find('#configuracao_recorrencia');
                                var parcelamentoSelect = modal.find('#parcelamento');
                                var tipoInput = modal.find('#tipo');

                                // Inicializa/reinicializa o Select2 do tipo de documento
                                setTimeout(function() {
                                    if (tipoDocumentoSelect.length && typeof $ !== 'undefined' && $.fn.select2) {
                                        // Remove atributo de inicialização para forçar reinicialização
                                        tipoDocumentoSelect.removeAttr('data-kt-initialized');

                                        // Se já foi inicializado, destroi
                                        if (tipoDocumentoSelect.hasClass('select2-hidden-accessible')) {
                                            tipoDocumentoSelect.select2('destroy');
                                        }

                                        // Reinicializa o Select2
                                        if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(tipoDocumentoSelect[0]);
                                        } else {
                                            tipoDocumentoSelect.select2({
                                                dropdownParent: modal,
                                                placeholder: 'Selecione um tipo de documento',
                                                allowClear: true,
                                                minimumResultsForSearch: 0
                                            });
                                        }
                                    }

                                    // Inicializa/reinicializa o Select2 do fornecedor
                                    if (fornecedorSelect.length && typeof $ !== 'undefined' && $.fn.select2) {
                                        // Remove atributo de inicialização para forçar reinicialização
                                        fornecedorSelect.removeAttr('data-kt-initialized');

                                        // Se já foi inicializado, destroi
                                        if (fornecedorSelect.hasClass('select2-hidden-accessible')) {
                                            fornecedorSelect.select2('destroy');
                                        }

                                        // Reinicializa o Select2
                                        if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(fornecedorSelect[0]);
                                        } else {
                                            fornecedorSelect.select2({
                                                dropdownParent: modal,
                                                placeholder: 'Selecione um fornecedor',
                                                allowClear: true,
                                                minimumResultsForSearch: 0
                                            });
                                        }

                                        // Adiciona botão "Adicionar Fornecedor" no footer do Select2
                                        fornecedorSelect.on('select2:open', function() {
                                            var $dropdown = $('.select2-container--open');
                                            var $results = $dropdown.find('.select2-results');

                                            // Remove botão anterior se existir
                                            $results.find('.select2-add-fornecedor-footer').remove();

                                            // Adiciona footer com botão
                                            var $footer = $(
                                                '<div class="select2-add-fornecedor-footer border-top p-2 text-center"></div>'
                                            );
                                            var $button = $(
                                                '<button type="button" class="btn btn-sm btn-light-primary w-100"><i class="fas fa-plus"></i> Adicionar Fornecedor</button>'
                                            );
                                            $footer.append($button);
                                            $results.append($footer);

                                            // Evento de clique no botão
                                            $button.on('click', function(e) {
                                                e.preventDefault();
                                                e.stopPropagation();

                                                // Fecha o Select2
                                                fornecedorSelect.select2('close');

                                                // Abre o drawer
                                                var drawerElement = document.querySelector(
                                                    '#kt_drawer_fornecedor');
                                                var modalElement = document.querySelector(
                                                    '#Dm_modal_financeiro');

                                                if (drawerElement && typeof KTDrawer !==
                                                    'undefined') {
                                                    var drawer = KTDrawer.getInstance(
                                                        drawerElement);

                                                    if (drawer) {
                                                        // Disable modal focus trap when drawer opens
                                                        if (modalElement) {
                                                            var bsModal = bootstrap.Modal
                                                                .getInstance(modalElement);

                                                            if (bsModal) {
                                                                // CRÍTICO: Desativa o FocusTrap do Bootstrap Modal
                                                                if (bsModal._focustrap) {
                                                                    bsModal._focustrap.deactivate();
                                                                }

                                                                // Remove tabindex do modal
                                                                modalElement.removeAttribute(
                                                                    'tabindex');
                                                            }
                                                        }

                                                        drawer.show();

                                                        // Focus the first input in the drawer
                                                        setTimeout(function() {
                                                            $('#fornecedor_nome').focus();
                                                        }, 300);
                                                    }
                                                }
                                            });
                                        });
                                    }

                                    // Inicializa/reinicializa o Select2 do Centro de Custo
                                    if (costCenterSelect.length && typeof $ !== 'undefined' && $.fn.select2) {
                                        // Remove atributo de inicialização para forçar reinicialização
                                        costCenterSelect.removeAttr('data-kt-initialized');

                                        // Se já foi inicializado, destroi
                                        if (costCenterSelect.hasClass('select2-hidden-accessible')) {
                                            costCenterSelect.select2('destroy');
                                        }

                                        // Reinicializa o Select2
                                        if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(costCenterSelect[0]);
                                        } else {
                                            costCenterSelect.select2({
                                                dropdownParent: modal,
                                                placeholder: 'Selecione o Centro de Custo',
                                                allowClear: true,
                                                minimumResultsForSearch: 0
                                            });
                                        }
                                    }

                                    // Inicializa/reinicializa o Select2 do parcelamento
                                    if (parcelamentoSelect.length && typeof $ !== 'undefined' && $.fn.select2) {
                                        // Remove atributo de inicialização para forçar reinicialização
                                        parcelamentoSelect.removeAttr('data-kt-initialized');

                                        // Se já foi inicializado, destroi
                                        if (parcelamentoSelect.hasClass('select2-hidden-accessible')) {
                                            parcelamentoSelect.select2('destroy');
                                        }

                                        // Reinicializa o Select2
                                        if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(parcelamentoSelect[0]);
                                        } else {
                                            parcelamentoSelect.select2({
                                                dropdownParent: modal,
                                                placeholder: 'Selecione o parcelamento',
                                                allowClear: false,
                                                minimumResultsForSearch: 0
                                            });
                                        }
                                    }

                                    // Comentário: Inicialização do select de recorrência movida para fora do evento do modal
                                    // A inicialização agora é feita pela função global carregarConfiguracaoRecorrencia()
                                    // que é chamada quando o checkbox "Repetir lançamento" é marcado
                                }, 50);

                                // Aguarda um pequeno delay para garantir que tudo foi inicializado
                                setTimeout(function() {
                                    if (tipoLancamento === 'receita') {
                                        // Define "entrada" no campo hidden
                                        tipoInput.val('entrada');

                                        // Filtra os lançamentos padrão para entrada
                                        filtrarLancamentosPadrao('entrada', lancamentoPadraoSelect);
                                    } else if (tipoLancamento === 'despesa') {
                                        // Define "saida" no campo hidden
                                        tipoInput.val('saida');

                                        // Filtra os lançamentos padrão para saída
                                    } else {
                                        // Se não houver tipo definido, limpa o campo hidden
                                        tipoInput.val('');
                                    }
                                }, 150);
                            });

                            // Controla a exibição do select de configuração de recorrência baseado no checkbox
                            $('#flexSwitchDefault').on('change', function() {
                                var wrapperRecorrencia = $('#configuracao-recorrencia-wrapper');
                                var wrapperParcelamento = $('#parcelamento_wrapper');
                                var wrapperDiaCobranca = $('#dia_cobranca_wrapper');
                                var vencimentoLabel = $('#vencimento').closest('.fv-row').find('label');

                                if ($(this).is(':checked')) {
                                    wrapperRecorrencia.show();

                                    // UI Changes for Recurring
                                    wrapperParcelamento.hide();
                                    wrapperDiaCobranca.show();
                                    vencimentoLabel.text('1º Vencimento');

                                    // Hide checkboxes
                                    $('#checkbox-pago-wrapper').hide();
                                    $('#checkbox-agendado-wrapper').hide();

                                    // Initialize Select2 for dia_cobranca if needed
                                    var diaCobrancaSelect = $('#dia_cobranca');

                                    // Default to 1st day if empty
                                    if (!diaCobrancaSelect.val()) {
                                        diaCobrancaSelect.val('1'); // Select value logic is updated later if it's select2
                                    }

                                    if (!diaCobrancaSelect.hasClass("select2-hidden-accessible")) {
                                         if (typeof KTSelect2 !== 'undefined') {
                                            new KTSelect2(diaCobrancaSelect[0]);
                                        } else {
                                            diaCobrancaSelect.select2({
                                                dropdownParent: $('#Dm_modal_financeiro'),
                                                minimumResultsForSearch: 0
                                            });
                                        }
                                    }

                                    // Trigger change to update UI if needed (especially if we set it to 1)
                                    // If we use trigger('change'), it hits our custom listener which updates date/opacity too!
                                    diaCobrancaSelect.trigger('change');

                                    // Add required
                                    diaCobrancaSelect.prop('required', true);
                                    
                                    // Carrega configurações de recorrência ao marcar o checkbox
                                    carregarConfiguracaoRecorrencia($('#Dm_modal_financeiro'));


                                } else {
                                    wrapperRecorrencia.hide();

                                    // Revert UI Changes
                                    wrapperParcelamento.show();
                                    wrapperDiaCobranca.hide();
                                    vencimentoLabel.text('Vencimento');

                                    var diaCobrancaSelect = $('#dia_cobranca');
                                    diaCobrancaSelect.prop('required', false);

                                    // Restore checkboxes visibility based on their logic
                                    // We can just call the toggle functions if they exist in scope, or manually reset
                                    // Since toggleCheckboxPago/Agendado are defined in the document ready block below this one,
                                    // we might need to rely on them being global or accessible.
                                    // However, this script block is inside the same $(document).ready as the others?
                                    // No, lines 30-755 are one script block.
                                    // The other functions seem to be further down (around 1900).
                                    // So we can't call them directly if they are scoped.
                                    // Let's check visibility reset manually for now or rely on event triggers if possible.

                                    // Re-show Agendado (it's usually visible unless hidden by something else)
                                    $('#checkbox-agendado-wrapper').show();

                                    // Trigger change on parcelamento to update Pago visibility
                                    $('#parcelamento').trigger('change');

                                    // Limpa a seleção quando desmarcar
                                    var configuracaoRecorrenciaSelect = $('#configuracao_recorrencia');
                                    if (configuracaoRecorrenciaSelect.hasClass('select2-hidden-accessible')) {
                                        configuracaoRecorrenciaSelect.val(null).trigger('change.select2');
                                    }

                                    // Limpa dia de cobrança
                                    var diaCobrancaSelect = $('#dia_cobranca');
                                    if (diaCobrancaSelect.hasClass('select2-hidden-accessible')) {
                                        diaCobrancaSelect.val(null).trigger('change.select2');
                                    }

                                    // Remove campos hidden de recorrência
                                    $('#Dm_modal_financeiro_form').find('input[name="intervalo_repeticao"]').remove();
                                    $('#Dm_modal_financeiro_form').find('input[name="frequencia"]').remove();
                                    $('#Dm_modal_financeiro_form').find('input[name="apos_ocorrencias"]').remove();
                                }
                            });

                            // Função para filtrar lançamentos padrão baseado no tipo
                            function filtrarLancamentosPadrao(tipo, $select) {
                                // Filtra usando as opções existentes no DOM
                                $select.find('option').each(function() {
                                    var $option = $(this);
                                    var optionType = $option.data('type');

                                    // Se for a opção vazia, mantém visível
                                    if ($option.val() === '' || !optionType) {
                                        $option.prop('disabled', false).show();
                                    } else if (optionType === tipo) {
                                        // Mostra opções do tipo correto
                                        $option.prop('disabled', false).show();
                                    } else {
                                        // Esconde opções de outro tipo
                                        $option.prop('disabled', true).hide();
                                    }
                                });

                                // Limpa a seleção atual
                                $select.val(null);

                                // Atualiza o Select2
                                setTimeout(function() {
                                    if ($select.hasClass('select2-hidden-accessible')) {
                                        $select.select2('destroy');
                                    }
                                    // Reinicializa o Select2
                                    if (typeof KTSelect2 !== 'undefined') {
                                        new KTSelect2($select[0]);
                                    } else if (typeof $select.select2 !== 'undefined') {
                                        $select.select2();
                                    }
                                }, 50);
                            }

                            // Inicializa o Drawer de fornecedor quando o documento estiver pronto
                            $(document).ready(function() {
                                var drawerElement = document.querySelector('#kt_drawer_fornecedor');
                                var modalElement = document.querySelector('#Dm_modal_financeiro');

                                if (drawerElement && typeof KTDrawer !== 'undefined') {
                                    var drawerInstance = KTDrawer.getInstance(drawerElement);

                                    if (!drawerInstance) {
                                        drawerInstance = new KTDrawer(drawerElement);
                                    }

                                    // Add event listener for drawer hide to restore modal tabindex
                                    drawerElement.addEventListener('kt.drawer.hide', function() {
                                        if (modalElement) {
                                            var bsModal = bootstrap.Modal.getInstance(modalElement);
                                            if (bsModal && bsModal._focustrap) {
                                                bsModal._focustrap.activate();
                                            }
                                            modalElement.setAttribute('tabindex', '-1');
                                        }
                                    });
                                }

                                // Handle close button click
                                $('#kt_drawer_fornecedor_close').on('click', function() {
                                    if (modalElement) {
                                        var bsModal = bootstrap.Modal.getInstance(modalElement);
                                        if (bsModal && bsModal._focustrap) {
                                            bsModal._focustrap.activate();
                                        }
                                        modalElement.setAttribute('tabindex', '-1');
                                    }
                                });
                            });

                            // Formulário de novo fornecedor
                            var fornecedorForm = $('#kt_drawer_fornecedor_form');
                            var fornecedorSubmitButton = $('#kt_drawer_fornecedor_submit');
                            var fornecedorCancelButton = $('#kt_drawer_fornecedor_cancel');

                            // Cancelar drawer
                            fornecedorCancelButton.on('click', function() {
                                var drawerElement = document.querySelector('#kt_drawer_fornecedor');
                                var modalElement = document.querySelector('#Dm_modal_financeiro');

                                var drawer = KTDrawer.getInstance(drawerElement);

                                if (drawer) {
                                    drawer.hide();
                                }

                                // Restore modal focus trap and tabindex
                                if (modalElement) {
                                    var bsModal = bootstrap.Modal.getInstance(modalElement);
                                    if (bsModal && bsModal._focustrap) {
                                        bsModal._focustrap.activate();
                                    }
                                    modalElement.setAttribute('tabindex', '-1');
                                }

                                fornecedorForm[0].reset();

                                // Remove indicador de loading se existir
                                fornecedorSubmitButton.removeAttr('data-kt-indicator');
                                fornecedorSubmitButton.prop('disabled', false);
                            });

                            // Submeter formulário
                            fornecedorForm.on('submit', function(e) {
                                e.preventDefault();

                                // Desabilita botão de submit
                                fornecedorSubmitButton.attr('data-kt-indicator', 'on');
                                fornecedorSubmitButton.prop('disabled', true);

                                // Dados do formulário
                                var formData = {
                                    nome: $('#fornecedor_nome').val(),
                                    cnpj: $('#fornecedor_cnpj').val(),
                                    telefone: $('#fornecedor_telefone').val(),
                                    email: $('#fornecedor_email').val(),
                                };

                                // CSRF Token
                                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                                // Envia requisição AJAX
                                $.ajax({
                                    url: '{{ route('fornecedores.store') }}',
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    data: formData,
                                    success: function(response) {
                                        if (response.success) {
                                            // Busca o select novamente para garantir que está disponível
                                            var fornecedorSelect = $('#fornecedor_id');

                                            if (fornecedorSelect.length) {
                                                // Adiciona nova opção ao select
                                                var newOption = new Option(response.fornecedor.nome, response
                                                    .fornecedor.id, true, true);
                                                fornecedorSelect.append(newOption);

                                                // Atualiza o Select2 para refletir a nova opção
                                                if (fornecedorSelect.hasClass('select2-hidden-accessible')) {
                                                    fornecedorSelect.val(response.fornecedor.id).trigger(
                                                        'change.select2');
                                                } else {
                                                    fornecedorSelect.val(response.fornecedor.id).trigger(
                                                        'change');
                                                }
                                            }

                                            // Limpa o formulário antes de fechar
                                            fornecedorForm[0].reset();

                                            // Fecha o drawer
                                            var drawerElement = document.querySelector('#kt_drawer_fornecedor');
                                            var modalElement = document.querySelector('#Dm_modal_financeiro');

                                            if (drawerElement) {
                                                var drawer = KTDrawer.getInstance(drawerElement);
                                                if (drawer) {
                                                    drawer.hide();
                                                }
                                            }

                                            // Restore modal focus trap and tabindex
                                            if (modalElement) {
                                                var bsModal = bootstrap.Modal.getInstance(modalElement);
                                                if (bsModal && bsModal._focustrap) {
                                                    bsModal._focustrap.activate();
                                                }
                                                modalElement.setAttribute('tabindex', '-1');
                                            }

                                            // Mostra mensagem de sucesso
                                            Swal.fire({
                                                text: response.message,
                                                icon: "success",
                                                buttonsStyling: false,
                                                confirmButtonText: "Ok",
                                                customClass: {
                                                    confirmButton: "btn btn-primary"
                                                },
                                                timer: 2000,
                                                timerProgressBar: true
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        var errorMessage = 'Erro ao cadastrar fornecedor.';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage = xhr.responseJSON.message;
                                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                            var errors = Object.values(xhr.responseJSON.errors).flat();
                                            errorMessage = errors.join('<br>');
                                        }

                                        Swal.fire({
                                            text: errorMessage,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        });
                                    },
                                    complete: function() {
                                        // SEMPRE reabilita botão de submit, mesmo em caso de erro
                                        fornecedorSubmitButton.removeAttr('data-kt-indicator');
                                        fornecedorSubmitButton.prop('disabled', false);
                                    }
                                });
                            });

                            // Inicializa o Drawer de recorrência quando o documento estiver pronto
                            $(document).ready(function() {
                                var drawerRecorrenciaElement = document.querySelector('#kt_drawer_recorrencia');
                                var modalElement = document.querySelector('#Dm_modal_financeiro');

                                if (drawerRecorrenciaElement && typeof KTDrawer !== 'undefined') {
                                    var drawerInstance = KTDrawer.getInstance(drawerRecorrenciaElement);

                                    if (!drawerInstance) {
                                        drawerInstance = new KTDrawer(drawerRecorrenciaElement);
                                    }

                                    // Add event listener for drawer hide to restore modal tabindex
                                    drawerRecorrenciaElement.addEventListener('kt.drawer.hide', function() {
                                        if (modalElement) {
                                            var bsModal = bootstrap.Modal.getInstance(modalElement);
                                            if (bsModal && bsModal._focustrap) {
                                                bsModal._focustrap.activate();
                                            }
                                            modalElement.setAttribute('tabindex', '-1');
                                        }
                                    });
                                }

                                // Handle close/cancel button click
                                $('#kt_drawer_recorrencia_close').on('click', function() {
                                    if (modalElement) {
                                        var bsModal = bootstrap.Modal.getInstance(modalElement);
                                        if (bsModal && bsModal._focustrap) {
                                            bsModal._focustrap.activate();
                                        }
                                        modalElement.setAttribute('tabindex', '-1');
                                    }
                                });
                            });

                            // Formulário de configuração de recorrência
                            var recorrenciaForm = $('#kt_drawer_recorrencia_form');
                            var recorrenciaSubmitButton = $('#kt_drawer_recorrencia_submit');
                            var drawerRecorrenciaElement = document.querySelector('#kt_drawer_recorrencia');

                            // Função para processar o submit do formulário de recorrência
                            function processarRecorrencia() {
                                // Desabilita botão de submit
                                recorrenciaSubmitButton.attr('data-kt-indicator', 'on');
                                recorrenciaSubmitButton.prop('disabled', true);

                                // Coleta os dados do formulário
                                var intervalo = $('#intervalo_repeticao').val();
                                var frequencia = $('#frequencia_recorrencia').val();
                                var aposOcorrencias = $('#apos_ocorrencias').val();

                                // Valida se os campos obrigatórios foram preenchidos
                                if (!intervalo || !frequencia || !aposOcorrencias) {
                                    Swal.fire({
                                        text: "Por favor, preencha todos os campos obrigatórios.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                    
                                    // Reabilita botão
                                    recorrenciaSubmitButton.removeAttr('data-kt-indicator');
                                    recorrenciaSubmitButton.prop('disabled', false);
                                    return;
                                }

                                // Mapeia frequência para texto legível
                                var frequenciaText = {
                                    'diario': 'Dia(s)',
                                    'semanal': 'Semana(s)',
                                    'mensal': 'Mês(es)',
                                    'anual': 'Ano(s)'
                                };

                                // Cria o texto da configuração
                                var configText = 'A cada ' + intervalo + ' ' + (frequenciaText[frequencia] || frequencia) +
                                    ' - Após ' + aposOcorrencias + ' ocorrências';

                                // Busca o select de configuração de recorrência
                                var configuracaoRecorrenciaSelect = $('#configuracao_recorrencia');

                                if (configuracaoRecorrenciaSelect.length) {
                                    // Cria ID temporário para a configuração (será salva apenas quando o form principal for enviado)
                                    var tempId = 'temp_' + Date.now();

                                    // Adiciona nova opção com ID temporário
                                    var newOption = $('<option></option>')
                                        .attr('value', tempId)
                                        .attr('selected', true)
                                        .attr('data-intervalo', intervalo)
                                        .attr('data-frequencia', frequencia)
                                        .attr('data-apos-ocorrencias', aposOcorrencias)
                                        .text(configText);

                                    configuracaoRecorrenciaSelect.append(newOption);

                                    // Atualiza o Select2
                                    if (configuracaoRecorrenciaSelect.hasClass('select2-hidden-accessible')) {
                                        configuracaoRecorrenciaSelect.val(tempId).trigger('change.select2');
                                    } else {
                                        configuracaoRecorrenciaSelect.val(tempId).trigger('change');
                                    }
                                }

                                // Atualiza os campos hidden do formulário principal com os dados da configuração
                                // (não salva no banco ainda, apenas armazena temporariamente)
                                // Pode estar no modal ou no drawer
                                var mainForm = $('#Dm_modal_financeiro_form');
                                if (!mainForm.length) {
                                    mainForm = $('#kt_drawer_lancamento_form');
                                }
                                
                                if (mainForm.length) {
                                    mainForm.find('input[name="intervalo_repeticao"]').remove();
                                    mainForm.find('input[name="frequencia"]').remove();
                                    mainForm.find('input[name="apos_ocorrencias"]').remove();
                                    mainForm.find('input[name="configuracao_recorrencia"]').remove();

                                    mainForm.append(
                                        '<input type="hidden" name="intervalo_repeticao" value="' + intervalo + '">');
                                    mainForm.append('<input type="hidden" name="frequencia" value="' +
                                        frequencia + '">');
                                    mainForm.append(
                                        '<input type="hidden" name="apos_ocorrencias" value="' + aposOcorrencias + '">');
                                    mainForm.append(
                                        '<input type="hidden" name="configuracao_recorrencia_temp" value="' + tempId + '">');
                                }

                                // Marca o checkbox de repetir e mostra o select
                                $('#flexSwitchDefault').prop('checked', true).trigger('change');

                                // Fecha o drawer de recorrência
                                if (drawerRecorrenciaElement && typeof KTDrawer !== 'undefined') {
                                    var drawer = KTDrawer.getInstance(drawerRecorrenciaElement);
                                    if (drawer) {
                                        drawer.hide();
                                    }
                                }

                                // Restore modal focus trap and tabindex
                                var modalElement = document.querySelector('#Dm_modal_financeiro');
                                if (modalElement) {
                                    var bsModal = bootstrap.Modal.getInstance(modalElement);
                                    if (bsModal && bsModal._focustrap) {
                                        bsModal._focustrap.activate();
                                    }
                                    modalElement.setAttribute('tabindex', '-1');
                                }

                                // Limpa o formulário
                                recorrenciaForm[0].reset();

                                // Reabilita botão de submit
                                recorrenciaSubmitButton.removeAttr('data-kt-indicator');
                                recorrenciaSubmitButton.prop('disabled', false);
                            }

                            // Evento de clique no botão de submit (que está fora do form)
                            recorrenciaSubmitButton.off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('[Modal Init] Botão de submit de recorrência clicado');
                                // Processa o formulário diretamente
                                processarRecorrencia();
                            });

                            // Submeter formulário de recorrência (caso seja submetido via form submit)
                            recorrenciaForm.off('submit').on('submit', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('[Modal Init] Formulário de recorrência submetido');
                                // Processa o formulário
                                processarRecorrencia();
                            });
                        });

                        // Função para processar o submit do formulário de recorrência (global, acessível de qualquer lugar)
                        function processarRecorrenciaDrawer() {
                            var recorrenciaForm = $('#kt_drawer_recorrencia_form');
                            var recorrenciaSubmitButton = $('#kt_drawer_recorrencia_submit');
                            
                            if (!recorrenciaForm.length || !recorrenciaSubmitButton.length) {
                                console.warn('[Modal Init] Formulário ou botão de recorrência não encontrado');
                                return;
                            }
                                    console.log('[Modal Init] Processando recorrência...');
                                    
                                    // Desabilita botão de submit
                                    recorrenciaSubmitButton.attr('data-kt-indicator', 'on');
                                    recorrenciaSubmitButton.prop('disabled', true);

                                    // Coleta os dados do formulário
                                    var intervalo = $('#intervalo_repeticao').val();
                                    var frequencia = $('#frequencia_recorrencia').val();
                                    var aposOcorrencias = $('#apos_ocorrencias').val();

                                    console.log('[Modal Init] Dados coletados:', { intervalo, frequencia, aposOcorrencias });

                                    // Valida se os campos obrigatórios foram preenchidos
                                    if (!intervalo || !frequencia || !aposOcorrencias) {
                                        Swal.fire({
                                            text: "Por favor, preencha todos os campos obrigatórios.",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        });
                                        
                                        // Reabilita botão
                                        recorrenciaSubmitButton.removeAttr('data-kt-indicator');
                                        recorrenciaSubmitButton.prop('disabled', false);
                                        return;
                                    }

                                    // Mapeia frequência para texto legível
                                    var frequenciaText = {
                                        'diario': 'Dia(s)',
                                        'semanal': 'Semana(s)',
                                        'mensal': 'Mês(es)',
                                        'anual': 'Ano(s)'
                                    };

                                    // Cria o texto da configuração
                                    var configText = 'A cada ' + intervalo + ' ' + (frequenciaText[frequencia] || frequencia) +
                                        ' - Após ' + aposOcorrencias + ' ocorrências';

                                    // Busca o select de configuração de recorrência (pode estar no modal ou no drawer)
                                    var configuracaoRecorrenciaSelect = $('#configuracao_recorrencia');

                                    if (configuracaoRecorrenciaSelect.length) {
                                        // Cria ID temporário para a configuração (será salva apenas quando o form principal for enviado)
                                        var tempId = 'temp_' + Date.now();

                                        // Adiciona nova opção com ID temporário
                                        var newOption = $('<option></option>')
                                            .attr('value', tempId)
                                            .attr('selected', true)
                                            .attr('data-intervalo', intervalo)
                                            .attr('data-frequencia', frequencia)
                                            .attr('data-apos-ocorrencias', aposOcorrencias)
                                            .text(configText);

                                        configuracaoRecorrenciaSelect.append(newOption);

                                        // Atualiza o Select2
                                        if (configuracaoRecorrenciaSelect.hasClass('select2-hidden-accessible')) {
                                            configuracaoRecorrenciaSelect.val(tempId).trigger('change.select2');
                                        } else {
                                            configuracaoRecorrenciaSelect.val(tempId).trigger('change');
                                        }
                                    }

                                    // Atualiza os campos hidden do formulário principal com os dados da configuração
                                    // (não salva no banco ainda, apenas armazena temporariamente)
                                    // Pode estar no modal ou no drawer
                                    var mainForm = $('#Dm_modal_financeiro_form');
                                    if (!mainForm.length) {
                                        mainForm = $('#kt_drawer_lancamento_form');
                                    }
                                    
                                    if (mainForm.length) {
                                        mainForm.find('input[name="intervalo_repeticao"]').remove();
                                        mainForm.find('input[name="frequencia"]').remove();
                                        mainForm.find('input[name="apos_ocorrencias"]').remove();
                                        mainForm.find('input[name="configuracao_recorrencia"]').remove();

                                        mainForm.append(
                                            '<input type="hidden" name="intervalo_repeticao" value="' + intervalo + '">');
                                        mainForm.append('<input type="hidden" name="frequencia" value="' +
                                            frequencia + '">');
                                        mainForm.append(
                                            '<input type="hidden" name="apos_ocorrencias" value="' + aposOcorrencias + '">');
                                        mainForm.append(
                                            '<input type="hidden" name="configuracao_recorrencia_temp" value="' + tempId + '">');
                                    } else {
                                        console.warn('[Modal Init] Formulário principal não encontrado (modal ou drawer)');
                                    }

                                    // Marca o checkbox de repetir e mostra o select
                                    $('#flexSwitchDefault').prop('checked', true).trigger('change');

                                    // Fecha o drawer de recorrência
                                    var drawerRecorrenciaElement = document.querySelector('#kt_drawer_recorrencia');
                                    if (drawerRecorrenciaElement && typeof KTDrawer !== 'undefined') {
                                        var drawer = KTDrawer.getInstance(drawerRecorrenciaElement);
                                        if (drawer) {
                                            drawer.hide();
                                        }
                                    }

                                    // Restore modal focus trap and tabindex (se estiver em um modal)
                                    var modalElement = document.querySelector('#Dm_modal_financeiro');
                                    if (modalElement) {
                                        var bsModal = bootstrap.Modal.getInstance(modalElement);
                                        if (bsModal && bsModal._focustrap) {
                                            bsModal._focustrap.activate();
                                        }
                                        modalElement.setAttribute('tabindex', '-1');
                                    }

                                    // Limpa o formulário
                                    recorrenciaForm[0].reset();

                                    // Reabilita botão de submit
                                    recorrenciaSubmitButton.removeAttr('data-kt-indicator');
                                    recorrenciaSubmitButton.prop('disabled', false);

                                    console.log('[Modal Init] Recorrência processada com sucesso!');
                        }
                        
                        // Inicialização do drawer de recorrência (fora do evento do modal para garantir que esteja sempre disponível)
                        // Esta função será chamada sempre que o drawer for aberto
                        function inicializarDrawerRecorrencia() {
                            console.log('[DEBUG] inicializarDrawerRecorrencia CHAMADA');
                            
                            var recorrenciaForm = $('#kt_drawer_recorrencia_form');
                            var recorrenciaSubmitButton = $('#kt_drawer_recorrencia_submit');
                            
                            console.log('[DEBUG] Buscando elementos:');
                            console.log('  - Form encontrado:', recorrenciaForm.length > 0, recorrenciaForm);
                            console.log('  - Botão encontrado:', recorrenciaSubmitButton.length > 0, recorrenciaSubmitButton);
                            
                            if (!recorrenciaForm.length || !recorrenciaSubmitButton.length) {
                                console.warn('[Modal Init] Formulário ou botão de recorrência não encontrado');
                                return;
                            }
                            
                            console.log('[Modal Init] Inicializando event listeners do drawer de recorrência...');
                            
                            // Remove listeners anteriores para evitar duplicação
                            console.log('[DEBUG] Removendo event listeners antigos...');
                            recorrenciaSubmitButton.off('click.recorrencia');
                            recorrenciaForm.off('submit.recorrencia');

                            // Evento de clique no botão de submit (que está fora do form)
                            console.log('[DEBUG] Adicionando event listener de CLIQUE no botão...');
                            recorrenciaSubmitButton.on('click.recorrencia', function(e) {
                                console.log('[DEBUG] ========================================');
                                console.log('[DEBUG] BOTÃO CLICADO! Event handler executado');
                                console.log('[DEBUG] ========================================');
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('[Modal Init] Botão de submit de recorrência clicado (global)');
                                processarRecorrenciaDrawer();
                                return false; // Garantia extra
                            });

                            // Submeter formulário de recorrência (caso seja submetido via form submit)
                            console.log('[DEBUG] Adicionando event listener de SUBMIT no formulário...');
                            recorrenciaForm.on('submit.recorrencia', function(e) {
                                console.log('[DEBUG] FORM SUBMETIDO! Event handler executado');
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('[Modal Init] Formulário de recorrência submetido (global)');
                                processarRecorrenciaDrawer();
                                return false; // Garantia extra
                            });
                            
                            console.log('[Modal Init] Event listeners do drawer de recorrência configurados com sucesso!');
                            console.log('[DEBUG] Verificando se eventos foram anexados:');
                            console.log('  - Eventos no botão:', $._data(recorrenciaSubmitButton[0], 'events'));
                            console.log('  - Eventos no form:', $._data(recorrenciaForm[0], 'events'));
                        }
                        
                        // Inicializar quando o documento estiver pronto
                        $(document).ready(function() {
                            // Aguardar um pouco para garantir que o drawer esteja no DOM
                            setTimeout(function() {
                                inicializarDrawerRecorrencia();
                            }, 300); // Reduzido de 500ms para 300ms
                        });
                        
                        // Reinicializar quando o drawer for aberto (para garantir que funcione sempre)
                        $(document).on('kt.drawer.shown', '#kt_drawer_recorrencia', function() {
                            console.log('[Modal Init] Drawer de recorrência aberto, reinicializando...');
                            // Inicializa imediatamente quando drawer abre
                            inicializarDrawerRecorrencia();
                        });
</script>
