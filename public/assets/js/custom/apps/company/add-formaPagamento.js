// Supondo que esses elementos já existam no seu HTML
const form = document.querySelector('#kt_modal_add_form');
const submitButton = document.querySelector('#kt_modal_add_submit');
const cancelButton = document.querySelector('#kt_modal_add_cancel');

// Exemplo de inicialização do Modal (caso esteja usando o modal do Bootstrap ou Metronic)
let modalElement = document.querySelector('#kt_modal_add'); // seu modal
let modal;
if(modalElement){
    modal = new bootstrap.Modal(modalElement);
}

// Variável para armazenar a instância do validador
let validator;

// Função para inicializar o form + validação
var initForm = function () {
    // Inicializa a validação
    validator = FormValidation.formValidation(
        form,
        {
            fields: {
                'nome': {
                    validators: {
                        notEmpty: {
                            message: 'O nome da Forma de Pagamento é obrigatório.'
                        }
                    }
                },
                'codigo': {
                    validators: {
                        notEmpty: {
                            message: 'O código é obrigatório. Ex: PIX, CC, BOL.'
                        }
                    }
                },
                'ativo': {
                    validators: {
                        notEmpty: {
                            message: 'Selecione se a forma de pagamento está ativa ou não.'
                        }
                    }
                },
                'tipo_taxa': {
                    validators: {
                        notEmpty: {
                            message: 'Selecione um tipo de taxa (Valor Fixo ou Porcentagem).'
                        }
                    }
                },
                'taxa': {
                    validators: {
                        notEmpty: {
                            message: 'O valor da taxa é obrigatório.'
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

    // Exemplo de revalidação de campo "status" se existisse no form
    /*
    $(form.querySelector('[name="status"]')).on('change', function () {
        validator.revalidateField('status');
    });
    */

    // Ação ao clicar no botão "Salvar" (submitButton)
    submitButton.addEventListener('click', function (e) {
        e.preventDefault();

        if (validator) {
            validator.validate().then(function (status) {
                console.log('validated!');

                if (status === 'Valid') {
                    // Mostrar indicador de carregamento
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    // Desabilitar botão para evitar duplo clique
                    submitButton.disabled = true;

                    // Captura o token CSRF (se estiver no meta ou hidden input)
                    let token = document.querySelector('meta[name="csrf-token"]');
                    if(token) {
                        token = token.getAttribute('content');
                    } else {
                        token = document.querySelector('input[name="_token"]').value;
                    }

                    // Montar dados do formulário para envio
                    // Opção 1: usando FormData
                    // const formData = new FormData(form);

                    // Opção 2: usando serialize se você utiliza jQuery
                    const formData = $(form).serialize();

                    // Faz requisição AJAX para enviar os dados ao Controller
                    // Aqui, utilizamos jQuery AJAX como exemplo
                    $.ajax({
                        url: form.action, // deve ser a rota store do seu controller
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        success: function(response) {
                            // Sucesso no envio
                            submitButton.removeAttribute('data-kt-indicator');
                            Swal.fire({
                                text: "Forma de Pagamento criada com sucesso!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    // Fechar modal (se estiver usando)
                                    if (modal) {
                                        modal.hide();
                                    }

                                    // Reativar botão e limpar formulário
                                    submitButton.disabled = false;
                                    form.reset();

                                    // Aqui você pode atualizar uma listagem na tela, caso tenha
                                    // Ex: atualizar tabela via AJAX, ou redirecionar:
                                    // window.location.href = '/formas-pagamento';
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            // Erro no envio
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            // Mensagem de erro
                            Swal.fire({
                                text: "Não foi possível salvar a Forma de Pagamento. Verifique se os dados estão corretos e tente novamente.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });

                } else {
                    // Formulário inválido
                    Swal.fire({
                        text: "Desculpe, parece que alguns campos estão inválidos. Tente novamente.",
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

    // Ação ao clicar no botão "Cancelar"
    cancelButton.addEventListener('click', function (e) {
        e.preventDefault();
        Swal.fire({
            text: "Deseja realmente cancelar?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Sim, cancelar!",
            cancelButtonText: "Não, voltar",
            customClass: {
                confirmButton: "btn btn-sm btn-primary",
                cancelButton: "btn btn-sm btn-active-light"
            }
        }).then(function (result) {
            if (result.value) {
                form.reset(); // Limpa o formulário
                if (modal) {
                    modal.hide(); // Fecha o modal, se estiver aberto
                }
            } else if (result.dismiss === 'cancel') {
                Swal.fire({
                    text: "Seu formulário não foi cancelado.",
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
};

// Chame a função ao carregar a página (ou ao exibir o modal)
document.addEventListener('DOMContentLoaded', function() {
    initForm();
});
