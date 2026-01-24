"use strict";

// Definir função global IMEDIATAMENTE para garantir disponibilidade
window.openDizimoModal = function() {
    // Verificar se jQuery está disponível
    if (typeof $ === 'undefined') {
        console.warn('jQuery não está disponível ainda. Aguardando...');
        setTimeout(function() {
            if (typeof window.openDizimoModal === 'function') {
                window.openDizimoModal();
            }
        }, 100);
        return;
    }

    // Verificar se o modal existe no DOM
    var modalElement = $('#kt_modal_dizimo');
    if (!modalElement.length) {
        console.error('Modal #kt_modal_dizimo não encontrado no DOM');
        // Tentar abrir diretamente com Bootstrap se disponível
        var modalDom = document.getElementById('kt_modal_dizimo');
        if (modalDom) {
            if (typeof bootstrap !== 'undefined') {
                var bsModal = new bootstrap.Modal(modalDom);
                bsModal.show();
            } else if (typeof $ !== 'undefined') {
                // Usar jQuery se disponível
                $(modalDom).modal('show');
            }
        }
        return;
    }

    // Se KTModalDizimo já inicializou, usar
    if (typeof KTModalDizimo !== 'undefined' && KTModalDizimo.openCreate) {
        try {
            KTModalDizimo.openCreate();
        } catch (e) {
            console.error('Erro ao abrir modal:', e);
            modalElement.modal('show');
        }
    } else {
        // Tentar inicializar se necessário
        if (typeof KTModalDizimo !== 'undefined') {
            KTModalDizimo.init();
            setTimeout(function() {
                if (KTModalDizimo && KTModalDizimo.openCreate) {
                    KTModalDizimo.openCreate();
                } else {
                    modalElement.modal('show');
                }
            }, 100);
        } else {
            // Fallback: abrir modal diretamente
            modalElement.modal('show');
        }
    }
};

window.editDizimo = function(id) {
    if (!id) {
        console.error('ID não fornecido para edição');
        return;
    }

    if (typeof $ === 'undefined') {
        console.warn('jQuery não está disponível ainda. Aguardando...');
        setTimeout(function() {
            if (typeof window.editDizimo === 'function') {
                window.editDizimo(id);
            }
        }, 100);
        return;
    }

    var modalElement = $('#kt_modal_dizimo');
    if (!modalElement.length) {
        console.error('Modal #kt_modal_dizimo não encontrado no DOM');
        return;
    }

    if (typeof KTModalDizimo !== 'undefined' && KTModalDizimo.openEdit) {
        KTModalDizimo.openEdit(id);
    } else {
        if (typeof KTModalDizimo !== 'undefined') {
            KTModalDizimo.init();
            setTimeout(function() {
                if (KTModalDizimo && KTModalDizimo.openEdit) {
                    KTModalDizimo.openEdit(id);
                }
            }, 100);
        } else {
            console.error('KTModalDizimo não está disponível');
        }
    }
};

var KTModalDizimo = function() {
    var modal;
    var form;
    var submitButton;
    var storeUrl = (window.dizimosRoutes && window.dizimosRoutes.store) ? window.dizimosRoutes.store : '/dizimos';
    var csrfToken = '';

    // Obter CSRF token de forma segura
    if (typeof $ !== 'undefined') {
        csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
    } else if (document.querySelector('meta[name="csrf-token"]')) {
        csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';
    }

    // Inicializar Select2 para todos os selects
    var initSelect2 = function() {
        // Select de Fiel
        var fielSelect = $('#kt_modal_dizimo_fiel');
        if (fielSelect.length && typeof $.fn.select2 !== 'undefined') {
            if (fielSelect.hasClass('select2-hidden-accessible')) {
                fielSelect.select2('destroy');
            }
            fielSelect.select2({
                dropdownParent: $('#kt_modal_dizimo'),
                placeholder: 'Selecione um fiel',
                allowClear: true,
                minimumResultsForSearch: 0, // Sempre mostrar busca
                language: {
                    noResults: function() {
                        return "Nenhum fiel encontrado";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
        }

        // Select de Tipo
        var tipoSelect = $('#kt_modal_dizimo_tipo');
        if (tipoSelect.length && typeof $.fn.select2 !== 'undefined') {
            if (tipoSelect.hasClass('select2-hidden-accessible')) {
                tipoSelect.select2('destroy');
            }
            tipoSelect.select2({
                dropdownParent: $('#kt_modal_dizimo'),
                placeholder: 'Selecione o tipo',
                allowClear: false,
                minimumResultsForSearch: Infinity // Não precisa buscar em opções fixas
            });
        }

        // Select de Forma de Pagamento
        var formaSelect = $('#kt_modal_dizimo_forma');
        if (formaSelect.length && typeof $.fn.select2 !== 'undefined') {
            if (formaSelect.hasClass('select2-hidden-accessible')) {
                formaSelect.select2('destroy');
            }
            formaSelect.select2({
                dropdownParent: $('#kt_modal_dizimo'),
                placeholder: 'Selecione a forma de pagamento',
                allowClear: false,
                minimumResultsForSearch: Infinity
            });
        }

        // Select de Entidade Financeira
        var entidadeSelect = $('#kt_modal_dizimo_entidade');
        if (entidadeSelect.length && typeof $.fn.select2 !== 'undefined') {
            if (entidadeSelect.hasClass('select2-hidden-accessible')) {
                entidadeSelect.select2('destroy');
            }
            entidadeSelect.select2({
                dropdownParent: $('#kt_modal_dizimo'),
                placeholder: 'Selecione uma entidade',
                allowClear: true,
                minimumResultsForSearch: 0,
                language: {
                    noResults: function() {
                        return "Nenhuma entidade encontrada";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
        }
    };

    // Inicializar DateRangePicker para data de pagamento
    var initDatePicker = function() {
        var dateInput = $('#kt_modal_dizimo_data');

        if (dateInput.length) {
            // Configurar locale do moment se disponível
            if (typeof moment !== 'undefined' && typeof moment.locale === 'function') {
                moment.locale('pt-br');
            }

            // Definir data padrão se vazio
            if (!dateInput.val()) {
                dateInput.val(moment().format('DD/MM/YYYY'));
            }

            // Verificar se library está disponível
            if (typeof $.fn.daterangepicker === 'undefined') {
                console.error('DateRangePicker não está disponível!');
                return;
            }

            // Inicializar daterangepicker
            dateInput.daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: true,
                parentEl: '#kt_modal_dizimo', // Importante para funcionar dentro de modal
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'De',
                    toLabel: 'Até',
                    customRangeLabel: 'Personalizado',
                    weekLabel: 'S',
                    daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    firstDay: 1
                },
                opens: 'left', // Melhor para modal
                drops: 'auto'
            });

            // Forçar atualização do input ao aplicar
            dateInput.on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            });
        }
    };

    // Máscara para valor monetário usando InputMask
    var initMoneyMask = function() {
        var valorInput = $('#kt_modal_dizimo_valor');

        if (valorInput.length) {
            if (typeof Inputmask !== 'undefined') {
                Inputmask("currency", {
                    "numericInput": true,
                    "radixPoint": ",",
                    "groupSeparator": ".",
                    "digits": 2,
                    "autoGroup": true,
                    "prefix": "", // Sem prefixo R$ no value, já está no span
                    "rightAlign": false,
                    "rightAlign": false,
                    "allowMinus": false
                }).mask(valorInput[0]);
            } else {
                // Fallback básico se a lib não carregar
                 valorInput.on('keyup', function() {
                    var v = $(this).val().replace(/\D/g,'');
                    v = (v/100).toFixed(2) + '';
                    v = v.replace(".", ",");
                    v = v.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                    v = v.replace(/(\d)(\d{3}),/g, "$1.$2,");
                    $(this).val(v);
                });
            }
        }
    };

    // Resetar formulário
    var resetForm = function() {
        if (form) {
            form[0].reset();
            $('#kt_modal_dizimo_id').val('');
            $('#kt_modal_dizimo_integrar').prop('checked', false);

            // Limpar erros de validação
            clearValidationErrors();

            // Resetar Select2
            $('#kt_modal_dizimo_fiel, #kt_modal_dizimo_tipo, #kt_modal_dizimo_forma, #kt_modal_dizimo_entidade').val(null).trigger('change');

            // Resetar data para hoje
            if (typeof moment !== 'undefined') {
                var today = moment().format('DD/MM/YYYY');
                $('#kt_modal_dizimo_data').val(today);
                if ($('#kt_modal_dizimo_data').data('daterangepicker')) {
                    $('#kt_modal_dizimo_data').data('daterangepicker').setStartDate(moment());
                }
            } else {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                $('#kt_modal_dizimo_data').val(dd + '/' + mm + '/' + yyyy);
            }
        }
    };

    // Abrir modal para criar novo
    var openCreateModal = function() {
        // Garantir que modal e form estão inicializados
        if (!modal || !modal.length) {
            modal = $('#kt_modal_dizimo');
        }
        if (!form || !form.length) {
            form = $('#kt_modal_dizimo_form');
        }

        if (!modal.length || !form.length) {
            console.error('Modal ou formulário não encontrado');
            return;
        }

        resetForm();
        $('#kt_modal_dizimo_title').text('Novo Lançamento de Dízimo/Doação');
        form.attr('action', storeUrl);
        $('#kt_modal_dizimo_method').val('POST');
        $('#kt_modal_dizimo_id').val('');

        modal.modal('show');
    };

    // Abrir modal para editar
    var openEditModal = function(id) {
        if (!id) return;

        $.ajax({
            url: storeUrl + '/' + id + '/edit',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success && response.data) {
                    var data = response.data;

                    resetForm();

                    $('#kt_modal_dizimo_title').text('Editar Lançamento de Dízimo/Doação');
                    form.attr('action', storeUrl + '/' + id);
                    $('#kt_modal_dizimo_method').val('PUT');
                    $('#kt_modal_dizimo_id').val(data.id);

                    // Preencher campos
                    $('#kt_modal_dizimo_fiel').val(data.fiel_id).trigger('change');
                    $('#kt_modal_dizimo_tipo').val(data.tipo).trigger('change');
                    $('#kt_modal_dizimo_valor').val(data.valor);
                    $('#kt_modal_dizimo_data').val(data.data_pagamento);
                    if ($('#kt_modal_dizimo_data').data('daterangepicker') && typeof moment !== 'undefined') {
                        $('#kt_modal_dizimo_data').data('daterangepicker').setStartDate(moment(data.data_pagamento));
                    }
                    $('#kt_modal_dizimo_forma').val(data.forma_pagamento).trigger('change');
                    $('#kt_modal_dizimo_entidade').val(data.entidade_financeira_id).trigger('change');
                    $('#kt_modal_dizimo_observacoes').val(data.observacoes || '');
                    $('#kt_modal_dizimo_integrar').prop('checked', data.integrado_financeiro);

                    if (modal && modal.length) {
                        modal.modal('show');
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({
                    text: "Erro ao carregar dados do lançamento!",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            }
        });
    };

    // Limpar erros de validação
    var clearValidationErrors = function() {
        $('.invalid-feedback').text('').hide();
        $('.form-control, .form-select').removeClass('is-invalid');
    };

    // Mostrar erros de validação no formulário
    var showValidationErrors = function(errors) {
        clearValidationErrors();

        $.each(errors, function(field, errorMessages) {
            var input = null;
            var errorElement = null;

            // Mapear nomes de campos para IDs
            switch(field) {
                case 'fiel_id':
                    input = $('#kt_modal_dizimo_fiel');
                    errorElement = $('#fielid-error');
                    break;
                case 'tipo':
                    input = $('#kt_modal_dizimo_tipo');
                    errorElement = $('#tipo-error');
                    break;
                case 'valor':
                    input = $('#kt_modal_dizimo_valor');
                    errorElement = $('#valor-error');
                    break;
                case 'data_pagamento':
                    input = $('#kt_modal_dizimo_data');
                    errorElement = $('#data_pagamento-error');
                    break;
                case 'forma_pagamento':
                    input = $('#kt_modal_dizimo_forma');
                    errorElement = $('#forma_pagamento-error');
                    break;
                case 'entidade_financeira_id':
                    input = $('#kt_modal_dizimo_entidade');
                    errorElement = $('#entidadefinanceiraid-error');
                    break;
                case 'observacoes':
                    input = $('#kt_modal_dizimo_observacoes');
                    errorElement = $('#observacoes-error');
                    break;
                default:
                    input = $('#kt_modal_dizimo_' + field);
                    errorElement = $('#' + field.replace(/_/g, '') + '-error');
            }

            if (input && input.length) {
                input.addClass('is-invalid');
                if (errorElement && errorElement.length) {
                    var errorMsg = Array.isArray(errorMessages) ? errorMessages[0] : errorMessages;
                    errorElement.text(errorMsg).show();
                }
            }
        });

        // Scroll para o primeiro erro
        var firstError = $('.is-invalid').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    };

    // Submeter formulário via AJAX
    var submitForm = function() {
        if (!form) return;

        form.on('submit', function(e) {
            e.preventDefault();

            // Limpar erros anteriores
            clearValidationErrors();

            // Validar formulário HTML5 primeiro
            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return false;
            }

            var url = form.attr('action');
            var method = $('#kt_modal_dizimo_method').val();

            // Converter valor para formato numérico
            var valorInput = $('#kt_modal_dizimo_valor');
            var valorStr = valorInput.val() || '';
            var valor = valorStr.toString().replace(/\./g, '').replace(',', '.');

            // Validar se o valor é um número válido
            if (isNaN(valor) || parseFloat(valor) <= 0) {
                valorInput.addClass('is-invalid');
                $('#valor-error').text('O valor deve ser um número maior que zero.').show();
                valorInput.focus();
                return false;
            }

            // Preparar dados
            var formData = new FormData(form[0]);
            formData.set('valor', valor);

            // Converter data de DD/MM/YYYY para YYYY-MM-DD
            var dataInput = $('#kt_modal_dizimo_data');
            var dataStr = dataInput.val() || '';
            if (dataStr && dataStr.includes('/')) {
                var partes = dataStr.split('/');
                if (partes.length === 3) {
                    var dataFormatada = partes[2] + '-' + partes[1] + '-' + partes[0];
                    formData.set('data_pagamento', dataFormatada);
                }
            }

            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            // Desabilitar botão
            submitButton.prop('disabled', true);
            submitButton.find('.indicator-label').addClass('d-none');
            submitButton.find('.indicator-progress').removeClass('d-none');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire({
                        text: response.message || "Operação realizada com sucesso!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(() => {
                        if (modal && modal.length) {
                            modal.modal('hide');
                        }

                        // Recarregar tabela se existir
                        var table = $('#kt_dizimos_table');
                        if (table.length && typeof table.DataTable !== 'undefined') {
                            table.DataTable().ajax.reload();
                        }

                        resetForm();
                    });
                },
                error: function(xhr) {
                    var message = "Erro ao processar solicitação!";

                    // Mostrar erros de validação do backend
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        showValidationErrors(xhr.responseJSON.errors);

                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else {
                            message = "Por favor, corrija os erros no formulário.";
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    // Mostrar mensagem de erro geral apenas se não houver erros específicos
                    if (!xhr.responseJSON || !xhr.responseJSON.errors) {
                        Swal.fire({
                            text: message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                    submitButton.find('.indicator-label').removeClass('d-none');
                    submitButton.find('.indicator-progress').addClass('d-none');
                }
            });
        });
    };

    // Inicialização
    var init = function() {
        modal = $('#kt_modal_dizimo');
        form = $('#kt_modal_dizimo_form');
        submitButton = $('#kt_modal_dizimo_submit');

        if (!modal.length || !form.length) {
            return;
        }

        // Inicializar quando o modal for aberto
        modal.on('shown.bs.modal', function() {
            console.log('Modal aberto, inicializando componentes...');
            initSelect2();
            initDatePicker();
            initMoneyMask();
        });

        // Resetar ao fechar
        modal.on('hidden.bs.modal', function() {
            resetForm();
        });

        // Submeter formulário
        submitForm();

        // Atualizar referências globais após inicialização (se já não foram definidas)
        if (typeof window.openDizimoModal === 'undefined') {
            window.openDizimoModal = openCreateModal;
        }
        if (typeof window.editDizimo === 'undefined') {
            window.editDizimo = openEditModal;
        }
    };

    return {
        init: init,
        openCreate: openCreateModal,
        openEdit: openEditModal
    };
}();

// Funções já foram definidas no início do arquivo, então não precisamos redefinir aqui

// Inicializar quando jQuery e DOM estiverem prontos
(function() {
    function initDizimoModal() {
        if (typeof KTModalDizimo !== 'undefined' && typeof $ !== 'undefined') {
            KTModalDizimo.init();
        } else {
            // Tentar novamente após um pequeno delay
            setTimeout(initDizimoModal, 100);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDizimoModal);
    } else {
        // Se jQuery ainda não estiver disponível, aguardar
        if (typeof $ !== 'undefined') {
            initDizimoModal();
        } else {
            setTimeout(initDizimoModal, 100);
        }
    }
})();

