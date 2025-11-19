"use strict";

// Class definition
var KTModalNewTarget = function () {
    // Shared variables
    var submitButton, cloneButton, newButton, cancelButton, closeButtonX; // Adicionado closeButtonX
    var validator;
    var form;
    var modal;
    var modalEl;

    // Private functions
    var initForm = function() {
        // Init Flatpickr
        const dataInput = form.querySelector('[name="data"]');
        if (dataInput) {
            flatpickr(dataInput, {
                dateFormat: "d/m/Y",
                locale: "pt",
                allowInput: true, // Permite digitação, mas valida
                // defaultDate: new Date(), // Removido para não preencher por padrão
                // maxDate: new Date(), // Removido se puder registrar datas futuras
            });
            Inputmask("99/99/9999").mask(dataInput);
        }

        // Init Select2 --- Certifique-se que Select2 está sendo carregado
        $(form.querySelector('[name="patrimonio"]')).select2({
            dropdownParent: $(modalEl) // Crucial para modal
        });
        $(form.querySelector('[name="uf"]')).select2({
            dropdownParent: $(modalEl), // Crucial para modal
            minimumResultsForSearch: Infinity // Opcional: esconde busca para UF
        });

         // Revalidate Select2 on change
         $(form.querySelector('[name="patrimonio"]')).on('change', function() {
             validator.revalidateField('patrimonio');
             // Atualiza campos hidden (código já existente no HTML, mas pode ser feito aqui também)
             const selectedOption = this.options[this.selectedIndex];
             form.querySelector('#numForo').value = selectedOption.getAttribute('data-num-foro') || '';
             form.querySelector('#numIbge').value = selectedOption.getAttribute('data-ibge') || '';
         });
         $(form.querySelector('[name="uf"]')).on('change', function() {
            // Nenhuma revalidação necessária normalmente para UF, a menos que tenha regras
         });

    };

    // Define Validation Rules
    var initValidation = function() {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'descricao': { validators: { notEmpty: { message: '⚠️ A descrição é obrigatória' }}},
                    'patrimonio': { validators: { notEmpty: { message: 'O patrimônio é obrigatório.' }}},
                    'data': { validators: { notEmpty: { message: 'A data é obrigatória' }}},
                    'cep': { validators: { notEmpty: { message: 'O CEP é obrigatório' }}}, // Adicione validação de formato se precisar
                    'bairro': { validators: { notEmpty: { message: 'O bairro é obrigatório' }}},
                    'logradouro': { validators: { notEmpty: { message: 'A rua é obrigatória' }}},
                    // Adicione outras validações (livro, folha, registro, cidade, uf) se forem obrigatórias
                    'livro': { validators: { /* Ex: notEmpty: { message: '...' } */ }},
                    'folha': { validators: { /* Ex: notEmpty: { message: '...' } */ }},
                    'registro': { validators: { /* Ex: notEmpty: { message: '...' } */ }},
                    'localidade': { validators: { notEmpty: { message: 'A cidade é obrigatória' } }},
                    'uf': { validators: { notEmpty: { message: 'O estado é obrigatório' } }},
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '', // Sem classes extras de inválido
                        eleValidClass: '' // Sem classes extras de válido
                    })
                }
            }
        );
    }

    // Reset form function
    const resetForm = () => {
        // Reset standard inputs
        form.reset(); // Reseta a maioria dos campos

        // Reset Select2 fields
        $(form.querySelector('[name="patrimonio"]')).val(null).trigger('change');
        $(form.querySelector('[name="uf"]')).val(null).trigger('change');

        // Reset Flatpickr (limpa a data selecionada)
        const fpInstance = form.querySelector('[name="data"]')._flatpickr;
        if (fpInstance) {
            fpInstance.clear();
        }

        // Clear hidden fields derived from patrimonio
        form.querySelector('#numForo').value = '';
        form.querySelector('#numIbge').value = '';

        // Reset validation state
        validator.resetForm(true); // true para limpar mensagens
    }

    // Handle form submission
    const handleSubmission = () => {

        // Botão "Enviar" (Submit Padrão)
        submitButton.addEventListener('click', function (e) {
            e.preventDefault(); // Prevenir submit padrão imediato

            form.querySelector('#save_action_field').value = 'submit'; // Marca a ação

            validator.validate().then(function (status) {
                if (status === 'Valid') {
                    setButtonProcessing(submitButton, true); // Ativa indicador e desabilita

                    // Permite o submit padrão do formulário após validação
                     // A action do form levará ao controller, que fará o redirect
                    form.submit();

                    // Não precisamos do timeout ou do Swal aqui, pois a página recarregará

                } else {
                    showValidationErrors();
                }
            });
        });

        // Botão "Salvar e Clonar" (AJAX)
        cloneButton.addEventListener('click', function (e) {
            e.preventDefault();
            form.querySelector('#save_action_field').value = 'clone'; // Marca a ação
            submitViaAjax('clone');
        });

        // Botão "Salvar e em Branco" (AJAX)
        newButton.addEventListener('click', function (e) {
            e.preventDefault();
            form.querySelector('#save_action_field').value = 'new'; // Marca a ação
            submitViaAjax('new');
        });

        // Cancel button handler
        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();
            confirmCancel();
        });

        // Close button handler (X no header)
        closeButtonX.addEventListener('click', function(e) {
            e.preventDefault();
            confirmCancel(); // Mesma lógica do botão Sair/Cancelar
        });
    }

    // AJAX Submission Function
    const submitViaAjax = async (actionType) => {
        if (!validator) {
            initValidation();
        }

        const validationStatus = await validator.validate();

        if (validationStatus === 'Valid') {
            // Set processing state for all save buttons
            setButtonProcessing(submitButton, true);
            setButtonProcessing(cloneButton, true); // Desabilita enquanto processa
            setButtonProcessing(newButton, true); // Desabilita enquanto processa

            const formData = new FormData(form);
            // O campo hidden 'save_action' já está no formData com 'clone' ou 'new'

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json', // Informa que esperamos JSON
                        'X-CSRF-TOKEN': csrfToken
                        // 'Content-Type': 'application/json' // Não necessário com FormData
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showSuccessMessage(data.message || 'Salvo com sucesso!');

                    if (actionType === 'new') {
                        resetForm(); // Limpa o formulário
                    } else {
                        // Para 'clone', apenas reseta a validação, mantém os dados
                         validator.resetForm(false);
                    }

                } else {
                    // Erro vindo do servidor (validação backend ou exceção)
                    showErrorMessage(data.message || 'Ocorreu um erro no servidor.');
                     // Se houver erros de validação específicos do backend:
                     if (data.errors) {
                         // Você pode tentar exibir esses erros nos campos correspondentes
                         console.error("Erros do servidor:", data.errors);
                     }
                }

            } catch (error) {
                console.error('Erro na requisição AJAX:', error);
                showErrorMessage('Erro de comunicação ao salvar.');
            } finally {
                 // Reset processing state for all save buttons
                setButtonProcessing(submitButton, false);
                setButtonProcessing(cloneButton, false);
                setButtonProcessing(newButton, false);
            }

        } else {
            showValidationErrors();
        }
    };

     // Helper para definir estado de processamento do botão
     const setButtonProcessing = (button, processing) => {
         if (!button) return; // Verifica se o botão existe
         if (processing) {
             button.setAttribute('data-kt-indicator', 'on');
             button.disabled = true;
         } else {
             button.removeAttribute('data-kt-indicator');
             button.disabled = false;
         }
     };

    // Helper para exibir erros de validação
    const showValidationErrors = () => {
        Swal.fire({
            text: "Desculpe, parece que foram detectados alguns erros. Por favor, verifique os campos e tente novamente.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, entendi!",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        });
    };

    // Helper para exibir mensagem de erro geral/AJAX
    const showErrorMessage = (message) => {
         Swal.fire({
            text: message,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, entendi!",
            customClass: {
                confirmButton: "btn btn-danger" // Botão vermelho para erro
            }
        });
    };

     // Helper para exibir mensagem de sucesso AJAX (usando toast)
		 const showSuccessMessage = (message) => {
			Swal.fire({
				 text: message,          // A mensagem de sucesso
				 icon: "success",        // Ícone de sucesso
				 buttonsStyling: false,  // Usa os estilos de botão do tema (Bootstrap/Metronic)
				 confirmButtonText: "Ok, entendi!", // Texto do botão de confirmação
				 customClass: {
						 confirmButton: "btn btn-primary" // Classe do botão primário do tema
				 }
		 });
		 // Nota: Este alerta não fecha sozinho e exige clique no botão "Ok"
	};


     // Helper para confirmação de cancelamento
    const confirmCancel = () => {
        Swal.fire({
            text: "Tem certeza de que deseja cancelar/sair?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Sim, sair!",
            cancelButtonText: "Não, voltar",
            customClass: {
                confirmButton: "btn btn-light", // Botão cinza para sair
                cancelButton: "btn btn-primary" // Botão primário para voltar
            }
        }).then(function (result) {
            if (result.isConfirmed) { // Usar isConfirmed em vez de result.value
                resetForm(); // Limpa o formulário ao cancelar
                modal.hide(); // Esconde o modal
            }
            // Não faz nada se clicar em "Não, voltar"
        });
    };


    // Public methods
    return {
        init: function () {
            modalEl = document.querySelector('#kt_modal_new_foro'); // Ajustado ID do Modal

            if (!modalEl) {
                console.warn("Elemento do modal #kt_modal_new_foro não encontrado.");
                return;
            }

            modal = new bootstrap.Modal(modalEl);
            form = modalEl.querySelector('#kt_modal_foro_form'); // Ajustado ID do Form

             if (!form) {
                 console.error("Elemento do formulário #kt_modal_foro_form não encontrado dentro do modal.");
                 return;
             }

            // Seleciona todos os botões de ação
            submitButton = form.querySelector('#kt_modal_new_foro_submit');
            cloneButton = form.querySelector('#kt_modal_new_foro_clone');
            newButton = form.querySelector('#kt_modal_new_foro_novo');
            cancelButton = form.querySelector('#kt_modal_new_foro_cancel');
            closeButtonX = modalEl.querySelector('#kt_modal_new_foro_close_x'); // Seleciona o botão X

             // Verifica se todos os botões foram encontrados
             if (!submitButton || !cloneButton || !newButton || !cancelButton || !closeButtonX) {
                 console.error("Um ou mais botões de ação não foram encontrados no formulário.");
                 // return; // Pode continuar mesmo se algum botão de ação extra não existir
             }


            initForm();         // Inicializa Flatpickr, Select2, etc.
            initValidation();   // Configura as regras de validação
            handleSubmission(); // Adiciona os event listeners aos botões
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalNewTarget.init();
});
