<!--begin::Drawer - Configuração de Integração-->
<div id="kt_drawer_integracao"
    class="bg-white"
    data-kt-drawer="true"
    data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_integracao_button"
    data-kt-drawer-close="#kt_drawer_integracao_close"
    data-kt-drawer-width="500px"
>
    <!--begin::Card-->
    <div class="card shadow-none rounded-0 w-100">
        <!--begin::Header-->
        <div class="card-header" id="kt_drawer_integracao_header">
            <h3 class="card-title fw-bold text-gray-700">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Recebimento via WhatsApp
            </h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_integracao_close">
                    <i class="fa-solid fa-xmark fs-2"></i>
                </button>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body" id="kt_drawer_integracao_body">
            <!--begin::Instrução-->
                <p class="text-gray-600 fs-7">
                    Escaneie o QR code ou clique no botão para iniciar a conversa no WhatsApp.
                    Em seguida, envie o código de vinculação ou os documentos diretamente por lá.
                </p>
            <!--end::Instrução-->

            <!--begin::QR Code-->
            <div class="text-center mb-7">
                <div class="p-3 rounded" id="qr_code_container">
                    <div class="text-center py-5">
                        <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        <p class="text-muted mt-3">Gerando QR Code de Vinculação...</p>
                    </div>
                </div>
            </div>
            <!--end::QR Code-->

            <!--begin::WhatsApp Button-->
            <div class="text-center mb-10" id="whatsapp_button_container" style="display: none;">
                <a href="#" id="whatsapp_link_btn" class="btn btn-sm btn-light-primary" target="_blank">
                    <i class="fa-brands fa-whatsapp me-2"></i>
                    Acessar WhatsApp
                </a>
            </div>
            <!--end::WhatsApp Button-->

            <!--begin::Instruções-->
            <div class="mb-7">
                <h4 class="fw-bold text-gray-800 mb-5">Como receber documentos por WhatsApp?</h4>

                <!--begin::Step 1-->
                <div class="d-flex align-items-start mb-5">
                    <div class="symbol symbol-40px symbol-circle me-4">
                        <span class="symbol-label bg-light-primary">
                            <span class="text-primary fw-bold">1</span>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-gray-700 mb-1">
                            Acesse o WhatsApp pelo QR code ou link e envie o código
                        </p>
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <code class="text-gray-800" id="verification_code_display">Aguardando geração do código...</code>
                                <button class="btn btn-sm btn-icon btn-light-primary btn-copy-code" style="display: none;">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                            <div id="expiration_timer" class="text-muted fs-7" style="display: none;">
                                <i class="fa-solid fa-clock me-1"></i>
                                <span id="timer_text">Código válido por: <strong id="countdown">10:00</strong></span>
                            </div>
                            <div class="alert alert-warning alert-dismissible fade d-none mt-2" id="expired_alert" role="alert">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                <span>Este código expirou. Clique no botão abaixo para gerar um novo código.</span>
                                <button type="button" class="btn btn-sm btn-primary ms-2" id="regenerate_code_btn">
                                    Gerar Novo Código
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Step 1-->

                <!--begin::Step 2-->
                <div class="d-flex align-items-start mb-5">
                    <div class="symbol symbol-40px symbol-circle me-4">
                        <span class="symbol-label bg-light-primary">
                            <span class="text-primary fw-bold">2</span>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-gray-700 mb-0">
                            Após a validação, seu número será vinculado ao seu usuário.
                        </p>
                    </div>
                </div>
                <!--end::Step 2-->

                <!--begin::Step 3-->
                <div class="d-flex align-items-start">
                    <div class="symbol symbol-40px symbol-circle me-4">
                        <span class="symbol-label bg-light-primary">
                            <span class="text-primary fw-bold">3</span>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-gray-700 mb-0">
                            Pronto! Você pode enviar documentos em PDF ou imagem para o Sistema Dominus.
                        </p>
                    </div>
                </div>
                <!--end::Step 3-->
            </div>
            <!--end::Instruções-->
        </div>
        <!--end::Body-->

        <!--begin::Footer-->
        <div class="card-footer d-flex justify-content-end py-6">
            <button type="button" class="btn btn-light" id="kt_drawer_integracao_cancel">
                Cancelar
            </button>
        </div>
        <!--end::Footer-->
    </div>
    <!--end::Card-->
</div>
<!--end::Drawer-->

<script>
    // Fechar drawer ao clicar em Cancelar
    document.getElementById('kt_drawer_integracao_cancel')?.addEventListener('click', function() {
        const drawer = KTDrawer.getInstance(document.getElementById('kt_drawer_integracao'));
        if (drawer) {
            drawer.hide();
        }
    });

    // Variáveis globais para o countdown
    let countdownInterval = null;
    let expirationTime = null;

    // Função para iniciar o countdown
    function startCountdown() {
        // Limpar intervalo anterior se existir
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        // Tempo de expiração: 10 minutos (600 segundos)
        let totalSeconds = 10 * 60;
        expirationTime = Date.now() + (totalSeconds * 1000);

        // Mostrar timer
        const timerElement = document.getElementById('expiration_timer');
        const countdownElement = document.getElementById('countdown');
        if (timerElement) {
            timerElement.style.display = 'block';
        }

        // Atualizar countdown a cada segundo
        countdownInterval = setInterval(() => {
            const now = Date.now();
            const remaining = Math.max(0, expirationTime - now);
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);

            if (countdownElement) {
                countdownElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }

            // Se expirou
            if (remaining === 0) {
                clearInterval(countdownInterval);
                countdownInterval = null;

                // Mostrar alerta de expiração
                const expiredAlert = document.getElementById('expired_alert');
                if (expiredAlert) {
                    expiredAlert.classList.remove('d-none');
                }

                // Esconder timer
                if (timerElement) {
                    timerElement.style.display = 'none';
                }
            }
        }, 1000);
    }

    // Função para parar o countdown
    function stopCountdown() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
        }
        const timerElement = document.getElementById('expiration_timer');
        if (timerElement) {
            timerElement.style.display = 'none';
        }
    }

    // Variável para o intervalo de verificação de status
    let statusCheckInterval = null;
    let currentVerificationCode = null;

    // Função para verificar o status da integração
    async function checkStatus() {
        if (!currentVerificationCode) return;

        try {
            const statusUrl = `{{ route('whatsapp.status', '') }}/${currentVerificationCode}`;
            const response = await fetch(statusUrl);
            const data = await response.json();

            console.log('Status check:', data);

            if (data.success && data.status === 'active') {
                // Sucesso! Parar verificação e recarregar
                stopCountdown();
                
                // Mostrar feedback visual
                const qrContainer = document.getElementById('qr_code_container');
                if (qrContainer) {
                    qrContainer.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            Integração realizada com sucesso! Recarregando...
                        </div>
                    `;
                }
                
                // Recarregar a página após 1.5s
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else if (data.status === 'expired') {
                stopCountdown();
                // Mostrar alerta de expiração (já tratado pelo countdown, mas reforçando)
                const expiredAlert = document.getElementById('expired_alert');
                if (expiredAlert) {
                    expiredAlert.classList.remove('d-none');
                }
            }
        } catch (error) {
            console.error('Erro ao verificar status:', error);
        }
    }

    // Função para gerar o QR Code
    async function generateQRCode() {
        const qrCodeContainer = document.getElementById('qr_code_container');
        const codeDisplay = document.getElementById('verification_code_display');
        const copyButton = document.querySelector('.btn-copy-code');
        const expiredAlert = document.getElementById('expired_alert');
        const whatsappButtonContainer = document.getElementById('whatsapp_button_container');
        const whatsappLinkBtn = document.getElementById('whatsapp_link_btn');

        if (!qrCodeContainer) return;

        // Esconder alerta de expiração
        if (expiredAlert) {
            expiredAlert.classList.add('d-none');
        }

        // Parar countdown anterior se existir
        stopCountdown();

        // Mostrar loading
        qrCodeContainer.innerHTML = `
            <div class="text-center py-5">
                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                <p class="text-muted mt-3">Gerando QR Code de Vinculação...</p>
            </div>
        `;

        // Esconder botão WhatsApp enquanto carrega
        if (whatsappButtonContainer) {
            whatsappButtonContainer.style.display = 'none';
        }

        try {
            // Buscar o QR Code de Autenticação
            const qrUrl = '{{ route('whatsapp.qrcode') }}';
            const qrResponse = await fetch(qrUrl);
            const qrData = await qrResponse.json();

            console.log('Auth QR response:', qrData);

            if (qrData.success && qrData.base64) {
                // Exibir QR Code gerado
                qrCodeContainer.innerHTML = `
                    <img src="${qrData.base64}"
                         alt="QR Code WhatsApp"
                         class="w-150px h-150px">
                `;

                // Atualizar o código exibido
                if (codeDisplay && qrData.code) {
                    codeDisplay.textContent = qrData.code;
                    codeDisplay.parentElement.classList.remove('d-none');
                    currentVerificationCode = qrData.code; // Store for polling
                }

                // Mostrar botão de copiar
                if (copyButton) {
                    copyButton.style.display = 'block';
                }

                // Atualizar botão WhatsApp com o link gerado
                if (whatsappLinkBtn && qrData.link) {
                    whatsappLinkBtn.href = qrData.link;
                    if (whatsappButtonContainer) {
                        whatsappButtonContainer.style.display = 'block';
                    }
                }

                // Iniciar countdown
                startCountdown();
                
                // Iniciar polling de status (check a cada 3 segundos)
                statusCheckInterval = setInterval(checkStatus, 3000);
            } else {
                qrCodeContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Não foi possível gerar o QR Code. Tente novamente.
                    </div>
                `;
            }
        } catch (error) {
            console.error('Erro ao carregar QR Code:', error);
            qrCodeContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa-solid fa-exclamation-circle me-2"></i>
                    Erro ao conectar com o servidor: ${error.message}
                </div>
            `;
        }
    }

    // Gerar QR Code quando o drawer abrir
    const drawerElement = document.getElementById('kt_drawer_integracao');
    if (drawerElement) {
        // Aguardar o drawer ser inicializado
        setTimeout(() => {
            const drawer = KTDrawer.getInstance(drawerElement);

            if (drawer) {
                // Usar eventos do KTDrawer se disponíveis
                drawerElement.addEventListener('shown.bs.drawer', function() {
                    generateQRCode();
                });

                drawerElement.addEventListener('hidden.bs.drawer', function() {
                    stopCountdown();
                });
            }

            // Fallback: usar MutationObserver para detectar quando o drawer é aberto
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const drawer = KTDrawer.getInstance(drawerElement);
                        if (drawer && drawer.isShown()) {
                            // Drawer foi aberto, gerar QR Code
                            generateQRCode();
                        } else {
                            // Drawer foi fechado, parar countdown
                            stopCountdown();
                        }
                    }
                });
            });

            observer.observe(drawerElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        }, 100);

        // Também gerar quando clicar no botão (fallback direto)
        document.getElementById('kt_drawer_integracao_button')?.addEventListener('click', function() {
            setTimeout(() => {
                generateQRCode();
            }, 500); // Delay para garantir que o drawer abriu completamente
        });
    }

    // Botão para regenerar código
    document.getElementById('regenerate_code_btn')?.addEventListener('click', function() {
        // Gerar novo QR Code
        generateQRCode();
    });

    // Copiar código para clipboard
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-copy-code')) {
            const code = document.getElementById('verification_code_display')?.textContent;
            if (code && code !== 'Aguardando geração do código...') {
                navigator.clipboard.writeText(code).then(() => {
                    // Mostrar feedback
                    const btn = e.target.closest('.btn-copy-code');
                    const originalIcon = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                    btn.classList.add('btn-success');
                    btn.classList.remove('btn-light-primary');
                    setTimeout(() => {
                        btn.innerHTML = originalIcon;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-light-primary');
                    }, 2000);
                });
            }
        }
    });

    // Limpar countdown quando drawer fechar
    document.getElementById('kt_drawer_integracao_close')?.addEventListener('click', function() {
        stopCountdown();
    });
    document.getElementById('kt_drawer_integracao_cancel')?.addEventListener('click', function() {
        stopCountdown();
    });
</script>

