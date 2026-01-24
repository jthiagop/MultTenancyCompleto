"use strict";

var KTNotaFiscalAdicionarConta = function() {
    var form;
    var submitButton;
    var indicatorLabel;
    var indicatorProgress;

    // Função para formatar CNPJ
    function formatCNPJ(value) {
        // Remove tudo que não é dígito
        value = value.replace(/\D/g, '');
        
        // Aplica a máscara
        if (value.length <= 14) {
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        }
        
        return value;
    }

    // Função para validar CNPJ
    function validateCNPJ(cnpj) {
        cnpj = cnpj.replace(/\D/g, '');
        
        if (cnpj.length !== 14) {
            return false;
        }

        // Elimina CNPJs conhecidos como inválidos
        if (/^(\d)\1+$/.test(cnpj)) {
            return false;
        }

        // Validação dos dígitos verificadores
        let length = cnpj.length - 2;
        let numbers = cnpj.substring(0, length);
        let digits = cnpj.substring(length);
        let sum = 0;
        let pos = length - 7;

        for (let i = length; i >= 1; i--) {
            sum += numbers.charAt(length - i) * pos--;
            if (pos < 2) pos = 9;
        }

        let result = sum % 11 < 2 ? 0 : 11 - sum % 11;
        if (result != digits.charAt(0)) return false;

        length = length + 1;
        numbers = cnpj.substring(0, length);
        sum = 0;
        pos = length - 7;

        for (let i = length; i >= 1; i--) {
            sum += numbers.charAt(length - i) * pos--;
            if (pos < 2) pos = 9;
        }

        result = sum % 11 < 2 ? 0 : 11 - sum % 11;
        if (result != digits.charAt(1)) return false;

        return true;
    }

    return {
        init: function() {
            form = document.getElementById('kt_modal_adicionar_conta_form');
            if (!form) {
                console.warn('Formulário kt_modal_adicionar_conta_form não encontrado!'); // Debug
                return;
            }
            
            console.log('Formulário encontrado:', form); // Debug

            // Garantir que o método seja POST
            if (form.getAttribute('method') !== 'POST') {
                form.setAttribute('method', 'POST');
            }

            submitButton = form.querySelector('#kt_modal_adicionar_conta_submit');
            if (!submitButton) {
                console.warn('Botão de submit não encontrado!'); // Debug
                return;
            }
            
            indicatorLabel = submitButton.querySelector('.indicator-label');
            indicatorProgress = submitButton.querySelector('.indicator-progress');
            
            console.log('Inicialização completa. Form:', form, 'SubmitButton:', submitButton); // Debug

            // CNPJ é readonly, não precisa de máscara

            // Toggle senha e limpar erros ao digitar
            const toggleSenha = document.getElementById('toggle_senha_a1');
            const senhaInput = document.getElementById('senha_a1_input');
            const iconToggle = document.getElementById('icon_toggle_senha');

            if (toggleSenha && senhaInput && iconToggle) {
                toggleSenha.addEventListener('click', function() {
                    const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    senhaInput.setAttribute('type', type);
                    
                    // Alterna o ícone
                    if (type === 'password') {
                        iconToggle.className = 'ki-duotone ki-eye fs-2';
                        iconToggle.innerHTML = '<span class="path1"></span><span class="path2"></span><span class="path3"></span>';
                    } else {
                        iconToggle.className = 'ki-duotone ki-eye-slash fs-2';
                        iconToggle.innerHTML = '<span class="path1"></span><span class="path2"></span><span class="path3"></span>';
                    }
                });
            }
            
            // Limpar erros ao digitar na senha
            if (senhaInput) {
                senhaInput.addEventListener('input', function() {
                    const errorElement = document.getElementById('senha_a1_error');
                    if (errorElement) {
                        errorElement.textContent = '';
                        errorElement.classList.add('d-none');
                    }
                    senhaInput.classList.remove('is-invalid');
                });
            }

            // Mostrar nome do arquivo selecionado
            const certificadoInput = document.getElementById('certificado_a1_input');
            const certificadoFileName = document.getElementById('certificado_file_name');
            const certificadoFileNameText = document.getElementById('certificado_file_name_text');
            
            if (certificadoInput && certificadoFileName && certificadoFileNameText) {
                certificadoInput.addEventListener('change', function(e) {
                    // Limpar erro ao selecionar novo arquivo
                    const errorElement = document.getElementById('certificado_a1_error');
                    if (errorElement) {
                        errorElement.textContent = '';
                        errorElement.classList.add('d-none');
                    }
                    certificadoInput.classList.remove('is-invalid');
                    
                    if (e.target.files.length > 0) {
                        const fileName = e.target.files[0].name;
                        const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2); // MB
                        
                        // Mostrar nome do arquivo
                        certificadoFileNameText.textContent = fileName + ' (' + fileSize + ' MB)';
                        certificadoFileName.classList.remove('d-none');
                    } else {
                        certificadoFileName.classList.add('d-none');
                    }
                });
            }

            // Submit do formulário - interceptar tanto no form quanto no botão
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Submit do formulário interceptado (form)'); // Debug
                KTNotaFiscalAdicionarConta.submit();
                return false;
            });
            
            // Também interceptar o clique no botão submit
            if (submitButton) {
                submitButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Clique no botão submit interceptado'); // Debug
                    KTNotaFiscalAdicionarConta.submit();
                    return false;
                });
            }
            
            console.log('Event listeners de submit adicionados ao formulário e botão'); // Debug
        },

        clearErrors: function() {
            if (!form) return;
            
            // Limpar todos os erros
            const errorElements = document.querySelectorAll('[id$="_error"]');
            errorElements.forEach(function(el) {
                el.textContent = '';
                el.classList.add('d-none');
            });
            
            // Remover classes de erro dos inputs
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(function(input) {
                input.classList.remove('is-invalid');
            });
        },

        displayErrors: function(errors) {
            if (!form) {
                console.error('Form não encontrado!');
                return;
            }
            
            console.log('displayErrors chamado com:', errors); // Debug
            
            // Mapear nomes dos campos para IDs dos elementos de erro
            const errorMap = {
                'cnpj': 'cnpj_error',
                'cnpj_raw': 'cnpj_error',
                'certificado_a1': 'certificado_a1_error',
                'senha_a1': 'senha_a1_error'
            };
            
            // Exibir erros para cada campo
            Object.keys(errors).forEach(function(field) {
                const errorId = errorMap[field] || field + '_error';
                const errorElement = document.getElementById(errorId);
                
                console.log('Campo:', field, 'ErrorId:', errorId, 'Elemento encontrado:', errorElement); // Debug
                
                // Tentar encontrar o input pelo nome do campo
                let inputElement = form.querySelector('[name="' + field + '"]');
                
                // Se não encontrar, tentar pelo ID
                if (!inputElement) {
                    inputElement = document.getElementById(field + '_input');
                }
                
                console.log('Input encontrado:', inputElement); // Debug
                
                if (errorElement) {
                    // Exibir mensagem de erro (juntar todas as mensagens se houver múltiplas)
                    const errorMessages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
                    const errorText = errorMessages.join(', '); // Juntar todas as mensagens
                    errorElement.textContent = errorText;
                    errorElement.classList.remove('d-none');
                    console.log('Erro exibido:', errorText, 'no elemento:', errorElement); // Debug
                } else {
                    console.warn('Elemento de erro não encontrado para:', errorId); // Debug
                }
                
                // Adicionar classe de erro ao input
                if (inputElement) {
                    inputElement.classList.add('is-invalid');
                    console.log('Classe is-invalid adicionada ao input:', inputElement); // Debug
                } else {
                    console.warn('Input não encontrado para:', field); // Debug
                }
            });
        },

        submit: function(e) {
            console.log('submit() chamado'); // Debug
            
            // Prevenir comportamento padrão se o evento foi passado
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            if (!form) {
                console.error('Form não encontrado no submit!'); // Debug
                return false;
            }
            
            // Validação
            const cnpjRaw = document.querySelector('input[name="cnpj_raw"]')?.value;
            const cnpj = document.getElementById('cnpj_input')?.value;
            const certificado = document.getElementById('certificado_a1_input')?.files[0];
            const senha = document.getElementById('senha_a1_input')?.value;
            
            console.log('Valores coletados:', { cnpjRaw, cnpj, certificado, senha }); // Debug

            // Validar CNPJ (usar o valor raw do hidden input)
            if (!cnpjRaw || cnpjRaw.length !== 14) {
                Swal.fire({
                    text: "CNPJ da matriz não encontrado ou inválido.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            if (!validateCNPJ(cnpjRaw)) {
                Swal.fire({
                    text: "CNPJ da matriz inválido. Verifique os dados da empresa.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            // Validar certificado
            if (!certificado) {
                Swal.fire({
                    text: "Por favor, selecione o arquivo do certificado A1.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            // Validar senha
            if (!senha) {
                Swal.fire({
                    text: "Por favor, informe a senha do certificado A1.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            // Mostrar loading
            submitButton.setAttribute('data-kt-indicator', 'on');
            indicatorLabel.style.display = 'none';
            indicatorProgress.style.display = 'inline-block';

            // Preparar FormData
            const formData = new FormData(form);
            formData.set('cnpj', cnpjRaw); // Usar o CNPJ raw do hidden input

            // Obter URL da action do form
            const actionUrl = form.getAttribute('action');
            
            if (!actionUrl) {
                Swal.fire({
                    text: "Erro: URL do formulário não encontrada.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                submitButton.removeAttribute('data-kt-indicator');
                indicatorLabel.style.display = 'inline-block';
                indicatorProgress.style.display = 'none';
                return;
            }
            
            // Obter token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value;
            
            if (!csrfToken) {
                Swal.fire({
                    text: "Erro: Token CSRF não encontrado.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                submitButton.removeAttribute('data-kt-indicator');
                indicatorLabel.style.display = 'inline-block';
                indicatorProgress.style.display = 'none';
                return;
            }
            
            console.log('Enviando requisição para:', actionUrl); // Debug
            
            // Enviar requisição
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug
                
                // Verificar se a resposta é JSON
                const contentType = response.headers.get("content-type");
                const isJson = contentType && contentType.includes("application/json");
                
                console.log('Content-Type:', contentType, 'Is JSON:', isJson); // Debug
                
                if (isJson) {
                    return response.json().then(data => {
                        console.log('Dados JSON recebidos:', data); // Debug
                        // Adicionar status da resposta para tratamento de erros
                        // IMPORTANTE: Mesmo com status 422, retornar os dados para processar os erros
                        return { ...data, status: response.status, ok: response.ok };
                    }).catch(err => {
                        console.error('Erro ao parsear JSON:', err); // Debug
                        throw err;
                    });
                } else {
                    // Se não for JSON, pode ser um erro HTML
                    return response.text().then(text => {
                        console.error('Resposta não é JSON:', text); // Debug
                        throw new Error('Resposta do servidor não é JSON');
                    });
                }
            })
            .then(data => {
                console.log('=== RESPOSTA RECEBIDA ==='); // Debug
                console.log('Data completa:', data); // Debug
                console.log('Status:', data.status); // Debug
                console.log('Success:', data.success); // Debug
                console.log('Errors:', data.errors); // Debug
                console.log('Tipo de errors:', typeof data.errors); // Debug
                console.log('Keys de errors:', data.errors ? Object.keys(data.errors) : 'null'); // Debug
                
                // Limpar erros anteriores
                KTNotaFiscalAdicionarConta.clearErrors();
                
                // PRIORIDADE 1: Se houver erros de validação, exibir abaixo dos inputs e PARAR aqui
                // Verificar de múltiplas formas para garantir que capturamos os erros
                const hasErrors = data.errors && 
                                  typeof data.errors === 'object' && 
                                  Object.keys(data.errors).length > 0;
                
                console.log('hasErrors:', hasErrors); // Debug
                
                if (hasErrors) {
                    console.log('=== EXIBINDO ERROS DE VALIDAÇÃO ==='); // Debug
                    console.log('Erros:', data.errors); // Debug
                    KTNotaFiscalAdicionarConta.displayErrors(data.errors);
                    // IMPORTANTE: Não mostrar Swal e não continuar quando há erros de validação
                    // Esconder loading antes de retornar
                    submitButton.removeAttribute('data-kt-indicator');
                    indicatorLabel.style.display = 'inline-block';
                    indicatorProgress.style.display = 'none';
                    console.log('=== RETORNANDO (não mostrar Swal) ==='); // Debug
                    return;
                }
                
                // PRIORIDADE 2: Se a resposta foi bem-sucedida
                if (data.success === true) {
                    Swal.fire({
                        text: data.message || "Conta adicionada com sucesso!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(() => {
                        // Fechar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_adicionar_conta_notafiscal'));
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Resetar formulário e estado visual
                        form.reset();
                        KTNotaFiscalAdicionarConta.clearErrors();
                        const certificadoFileName = document.getElementById('certificado_file_name');
                        
                        if (certificadoFileName) {
                            certificadoFileName.classList.add('d-none');
                        }
                        
                        // Recarregar página ou atualizar dados
                        if (data.reload !== false) {
                            window.location.reload();
                        }
                    });
                    return;
                }
                
                // PRIORIDADE 3: Se não houver erros específicos e não foi sucesso, mostrar mensagem geral
                console.log('Nenhum erro de validação encontrado, mostrando mensagem geral'); // Debug
                Swal.fire({
                    text: data.message || "Erro ao adicionar conta. Tente novamente.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Limpar erros anteriores
                KTNotaFiscalAdicionarConta.clearErrors();
                
                // Mensagem de erro mais específica
                let errorMessage = "Erro ao processar requisição. Tente novamente.";
                
                if (error.message && error.message.includes('JSON')) {
                    errorMessage = "Erro ao processar resposta do servidor. Verifique sua conexão e tente novamente.";
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
            })
            .finally(() => {
                // Esconder loading
                submitButton.removeAttribute('data-kt-indicator');
                indicatorLabel.style.display = 'inline-block';
                indicatorProgress.style.display = 'none';
            });
        }
    };
}();

// Função para inicializar
function initNotaFiscalAdicionarConta() {
    console.log('Inicializando KTNotaFiscalAdicionarConta...'); // Debug
    KTNotaFiscalAdicionarConta.init();
}

// Inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        initNotaFiscalAdicionarConta();
    });
} else {
    initNotaFiscalAdicionarConta();
}

// Re-inicializar quando o modal for aberto (para casos de modais carregados dinamicamente)
document.addEventListener('shown.bs.modal', function(event) {
    if (event.target.id === 'kt_modal_adicionar_conta_notafiscal') {
        console.log('Modal aberto, reinicializando...'); // Debug
        // Pequeno delay para garantir que o DOM está totalmente renderizado
        setTimeout(function() {
            initNotaFiscalAdicionarConta();
        }, 100);
    }
});

// Também interceptar quando o modal for mostrado (antes de abrir completamente)
document.addEventListener('show.bs.modal', function(event) {
    if (event.target.id === 'kt_modal_adicionar_conta_notafiscal') {
        console.log('Modal sendo mostrado, adicionando listeners preventivos...'); // Debug
        const form = document.getElementById('kt_modal_adicionar_conta_form');
        const submitButton = document.getElementById('kt_modal_adicionar_conta_submit');
        
        if (form) {
            // Remover listeners anteriores para evitar duplicação
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            
            // Adicionar listener preventivo imediatamente
            newForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Submit preventivo interceptado'); // Debug
                return false;
            }, true); // Use capture phase para interceptar antes
        }
        
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Clique preventivo interceptado'); // Debug
                return false;
            }, true); // Use capture phase
        }
    }
});

// Resetar estado quando o modal for fechado
document.addEventListener('hidden.bs.modal', function(event) {
    if (event.target.id === 'kt_modal_adicionar_conta_notafiscal') {
        const form = document.getElementById('kt_modal_adicionar_conta_form');
        if (form) {
            form.reset();
            
            // Limpar erros
            if (typeof KTNotaFiscalAdicionarConta !== 'undefined' && KTNotaFiscalAdicionarConta.clearErrors) {
                KTNotaFiscalAdicionarConta.clearErrors();
            }
            
            const certificadoFileName = document.getElementById('certificado_file_name');
            
            if (certificadoFileName) {
                certificadoFileName.classList.add('d-none');
            }
        }
    }
});

