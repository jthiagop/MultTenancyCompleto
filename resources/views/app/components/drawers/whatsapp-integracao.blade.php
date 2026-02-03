<!--begin::Drawer - Configuração de Integração-->
<div id="kt_drawer_integracao"
    class="bg-white"
    data-kt-drawer="true"
    data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_integracao_button"
    data-kt-drawer-close="#kt_drawer_integracao_close"
    data-kt-drawer-width="500px"
    data-qrcode-url="{{ route('whatsapp.qrcode') }}"
    data-status-url-template="{{ route('whatsapp.status', ['code' => '__CODE__']) }}"
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
                    <div class="card card-bordered mb-7 text-center py-5">
                        <div class="card-body">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            <p class="text-muted mt-3">Gerando QR Code de Vinculação...</p>
                        </div>
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
            <button type="button" class="btn btn-light" data-kt-drawer-dismiss="true">
                Cancelar
            </button>
        </div>
        <!--end::Footer-->
    </div>
    <!--end::Card-->
</div>
<!--end::Drawer-->

<script>
    // Classe encapsulada para gerenciar integração WhatsApp
    class WhatsAppIntegrationDrawer {
        constructor(drawerId) {
            this.drawerEl = document.getElementById(drawerId);
            if (!this.drawerEl) return;

            this.drawer = KTDrawer.getInstance(this.drawerEl);
            if (!this.drawer) return;

            // URLs via data-attributes
            this.qrUrl = this.drawerEl.dataset.qrcodeUrl;
            this.statusTemplate = this.drawerEl.dataset.statusUrlTemplate;

            // State management
            this.countdownInterval = null;
            this.statusInterval = null;
            this.expirationTime = null;
            this.currentCode = null;
            this.abortController = null;
            this.isGenerating = false;

            // Initialize
            this.bindDrawerEvents();
            this.bindUIEvents();
        }

        bindDrawerEvents() {
            // Tentar múltiplas abordagens para detectar quando o drawer abre
            
            // 1. Eventos oficiais do KTDrawer (se disponíveis)
            if (this.drawer && typeof this.drawer.on === 'function') {
                console.log('Binding KTDrawer events...');
                this.drawer.on("kt.drawer.shown", () => {
                    console.log('KTDrawer shown event fired');
                    this.generateQRCode();
                });
                this.drawer.on("kt.drawer.after.hidden", () => {
                    console.log('KTDrawer hidden event fired');
                    this.cleanup();
                });
            }

            // 2. Fallback: observar mudanças na classe do drawer
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const classList = this.drawerEl.classList;
                        if (classList.contains('drawer-on') || classList.contains('show')) {
                            console.log('Drawer opened via MutationObserver');
                            setTimeout(() => this.generateQRCode(), 100);
                        } else if (!classList.contains('drawer-on') && !classList.contains('show')) {
                            console.log('Drawer closed via MutationObserver');
                            this.cleanup();
                        }
                    }
                });
            });

            observer.observe(this.drawerEl, {
                attributes: true,
                attributeFilter: ['class', 'style']
            });

            // 3. Fallback adicional: listener no botão que abre o drawer
            const toggleButton = document.querySelector('#kt_drawer_integracao_button');
            if (toggleButton) {
                console.log('Adding click listener to toggle button');
                toggleButton.addEventListener('click', () => {
                    console.log('Toggle button clicked');
                    setTimeout(() => {
                        console.log('Generating QR Code after button click');
                        this.generateQRCode();
                    }, 500);
                });
            }
        }

        bindUIEvents() {
            document.getElementById('regenerate_code_btn')?.addEventListener('click', () => {
                this.generateQRCode(true);
            });

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-copy-code');
                if (!btn) return;

                const code = document.getElementById('verification_code_display')?.textContent?.trim();
                if (!code || code.includes('Aguardando')) return;

                this.copyToClipboard(code, btn);
            });
        }

        cleanup() {
            this.stopCountdown();
            this.stopStatusPolling();

            if (this.abortController) {
                this.abortController.abort();
                this.abortController = null;
            }

            this.isGenerating = false;
            this.currentCode = null;
        }

        stopCountdown() {
            if (this.countdownInterval) clearInterval(this.countdownInterval);
            this.countdownInterval = null;

            const timerElement = document.getElementById('expiration_timer');
            if (timerElement) timerElement.style.display = 'none';
        }

        stopStatusPolling() {
            if (this.statusInterval) clearInterval(this.statusInterval);
            this.statusInterval = null;
        }

        startCountdown(minutes = 10) {
            this.stopCountdown();

            const totalSeconds = minutes * 60;
            this.expirationTime = Date.now() + totalSeconds * 1000;

            const timerElement = document.getElementById('expiration_timer');
            const countdownEl = document.getElementById('countdown');

            if (timerElement) timerElement.style.display = 'block';

            this.countdownInterval = setInterval(() => {
                const remaining = Math.max(0, this.expirationTime - Date.now());
                const mm = String(Math.floor(remaining / 60000)).padStart(2, '0');
                const ss = String(Math.floor((remaining % 60000) / 1000)).padStart(2, '0');

                if (countdownEl) countdownEl.textContent = `${mm}:${ss}`;

                if (remaining === 0) {
                    this.stopCountdown();
                    this.stopStatusPolling();
                    document.getElementById('expired_alert')?.classList.remove('d-none');
                }
            }, 1000);
        }

        makeStatusUrl(code) {
            return this.statusTemplate.replace('__CODE__', encodeURIComponent(code));
        }

        async checkStatus() {
            if (!this.currentCode) return;

            try {
                const response = await fetch(this.makeStatusUrl(this.currentCode), {
                    headers: { 
                        'Accept': 'application/json', 
                        'X-Requested-With': 'XMLHttpRequest' 
                    },
                    signal: this.abortController?.signal
                });

                if (!response.ok) return;
                const data = await response.json();

                if (data.success && data.status === 'active') {
                    this.cleanup();

                    const qrContainer = document.getElementById('qr_code_container');
                    if (qrContainer) {
                        qrContainer.innerHTML = `
                            <div class="alert alert-success">
                                <i class="fa-solid fa-check-circle me-2"></i>
                                Integração realizada com sucesso! Recarregando...
                            </div>
                        `;
                    }

                    setTimeout(() => window.location.reload(), 1500);
                }

                if (data.status === 'expired') {
                    this.stopCountdown();
                    this.stopStatusPolling();
                    document.getElementById('expired_alert')?.classList.remove('d-none');
                }
            } catch (error) {
                // Se foi abort, ignora silenciosamente
                if (error.name !== 'AbortError') {
                    console.warn('Erro ao verificar status:', error.message);
                }
            }
        }

        async generateQRCode(force = false) {
            console.log('generateQRCode called, force:', force);
            
            if (this.isGenerating && !force) {
                console.log('Already generating, skipping...');
                return;
            }
            this.isGenerating = true;

            this.cleanup();
            this.abortController = new AbortController();

            const qrCodeContainer = document.getElementById('qr_code_container');
            const codeDisplay = document.getElementById('verification_code_display');
            const copyButton = document.querySelector('.btn-copy-code');
            const expiredAlert = document.getElementById('expired_alert');
            const whatsappBtnWrap = document.getElementById('whatsapp_button_container');
            const whatsappLinkBtn = document.getElementById('whatsapp_link_btn');

            console.log('Elements found:', {
                qrCodeContainer: !!qrCodeContainer,
                codeDisplay: !!codeDisplay,
                copyButton: !!copyButton
            });

            expiredAlert?.classList.add('d-none');
            if (whatsappBtnWrap) whatsappBtnWrap.style.display = 'none';

            if (qrCodeContainer) {
                qrCodeContainer.innerHTML = `
                    <div class="card card-bordered mb-7 text-center py-5">
                        <div class="card-body">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            <p class="text-muted mt-3">Gerando QR Code de Vinculação...</p>
                        </div>
                    </div>
                `;
            }

            try {
                console.log('Fetching QR Code from:', this.qrUrl);
                
                const response = await fetch(this.qrUrl, {
                    headers: { 
                        'Accept': 'application/json', 
                        'X-Requested-With': 'XMLHttpRequest' 
                    },
                    signal: this.abortController.signal
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    throw new Error(`Servidor retornou status: ${response.status}`);
                }

                const qrData = await response.json();
                console.log('QR Data received:', qrData);

                if (qrData.success && qrData.base64) {
                    if (qrCodeContainer) {
                        qrCodeContainer.innerHTML = `
                            <div class="position-relative d-inline-block">
                                <div class="bg-white p-3 rounded shadow-sm d-inline-block">
                                    <img src="${qrData.base64}" alt="QR Code WhatsApp" class="w-150px h-150px">
                                </div>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <div class="bg-white rounded-circle p-2 shadow-sm" style="width:35px;height:35px;">
                                        <img src="{{ url('tenancy/assets/media/app/mini-logo.svg') }}"
                                             alt="Logo" class="w-100 h-100" style="object-fit:contain;">
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    if (codeDisplay && qrData.code) {
                        codeDisplay.textContent = qrData.code;
                        this.currentCode = qrData.code;
                    }

                    if (copyButton) copyButton.style.display = 'block';

                    if (whatsappLinkBtn && qrData.link) {
                        whatsappLinkBtn.href = qrData.link;
                        if (whatsappBtnWrap) whatsappBtnWrap.style.display = 'block';
                    }

                    console.log('QR Code generated successfully');
                    this.startCountdown(10);
                    this.statusInterval = setInterval(() => this.checkStatus(), 3000);
                } else {
                    console.error('Invalid QR data:', qrData);
                    throw new Error('Não foi possível gerar o QR Code. Resposta inválida do servidor.');
                }
            } catch (error) {
                console.error('Error generating QR Code:', error);
                if (qrCodeContainer && error.name !== 'AbortError') {
                    qrCodeContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-exclamation-circle me-2"></i>
                            Erro: ${error.message}
                        </div>
                    `;
                }
            } finally {
                this.isGenerating = false;
                console.log('generateQRCode finished');
            }
        }

        async copyToClipboard(text, btn) {
            try {
                await navigator.clipboard.writeText(text);

                const original = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-light-primary');

                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-light-primary');
                }, 2000);
            } catch (error) {
                console.warn('Falha ao copiar para clipboard:', error);
                // Fallback opcional: mostrar toast ou alert
            }
        }
    }

    // Inicializar quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Initializing WhatsApp Drawer');
        
        // Aguardar um pouco para garantir que o KTDrawer foi inicializado
        setTimeout(() => {
            const drawer = new WhatsAppIntegrationDrawer('kt_drawer_integracao');
            
            // Fallback adicional: tentar gerar QR quando clicar no botão do drawer
            const toggleBtn = document.querySelector('[data-kt-drawer-toggle="#kt_drawer_integracao"]');
            if (toggleBtn) {
                console.log('Found drawer toggle button, adding fallback listener');
                toggleBtn.addEventListener('click', () => {
                    console.log('Drawer toggle button clicked');
                    setTimeout(() => {
                        if (drawer && typeof drawer.generateQRCode === 'function') {
                            console.log('Calling generateQRCode from toggle button');
                            drawer.generateQRCode();
                        }
                    }, 600);
                });
            }
        }, 500);
    });
</script>

