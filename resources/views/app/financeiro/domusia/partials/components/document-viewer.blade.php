@php
    // Definição de IDs e Parâmetros
    // Se idPrefix não for passado, usamos vazio para manter compatibilidade com IDs antigos (documentViewer)
    // Se for passado, garantimos que seja único
    $idPrefix = $idPrefix ?? '';
    $showCard = $showCard ?? true;
    $showControls = $showControls ?? true;
    $viewerHeight = $viewerHeight ?? '600px';
    $viewerMaxHeight = $viewerMaxHeight ?? '70vh';

    // IDs principais (Mantendo convenção se prefixo estiver vazio)
    $rootName = 'documentViewer';
    $wrapperId = $idPrefix ? $idPrefix . '_' . $rootName : $rootName . 'Card'; // Card ID antigo era ...Card

    // Ajuste fino para manter compatibilidade com seletores CSS/JS se necessário,
    // mas agora estamos usando classes. O importante é o Wrapper ID para a instância JS.
    if(!$idPrefix) {
        // IDs Clássicos para o Main Viewer
        $wrapperId = 'documentViewerWrapper'; // Novo Wrapper
        $cardId = 'documentViewerCard'; // ID que o pendentes.blade.php pode estar referenciando para show/hide
    } else {
        $wrapperId = $idPrefix . '_wrapper';
        $cardId = $idPrefix . '_card';
    }

    $viewerContainerId = $idPrefix . 'viewer_container';
    $pdfViewerId = $idPrefix . 'pdf_viewer';
    $imageViewerId = $idPrefix . 'image_viewer';
    $emptyStateId = $idPrefix . 'empty_state';
    $loadingStateId = $idPrefix . 'loading_state';
@endphp

@push('styles')
    @once
    <style>
        /* ============================================
         *  DOMUS DOCUMENT VIEWER — Estilos
         * ============================================ */

        /* Container do Viewer */
        .domus-viewer-container {
            background-color: #2d2d2d;
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0);
            background-size: 20px 20px;
            width: 100%;
            height: 100%;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 2;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* ======= IMAGEM — Centralizada via position absolute + transform ======= */
        /* Técnica bulletproof: não depende de flex, margin ou display */
        .domus-viewer-container img {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important; /* Base: centralização */
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            cursor: grab;
            user-select: none;
            -webkit-user-drag: none;
            transition: transform 0.15s ease-out;
            transform-origin: center center;
            display: block;
        }

        .domus-viewer-container img.dragging {
            cursor: grabbing;
            transition: none;
        }

        /* Cursor padrão quando zoom = fit (sem pan) */
        .domus-viewer-container img.cursor-default {
            cursor: default;
        }

        /* ======= TOOLBAR FLUTUANTE (Glassmorphism) ======= */
        .domus-floating-toolbar {
            position: absolute;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(30, 30, 30, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
            z-index: 100;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Toolbar aparece quando hover no container pai */
        .domus-document-viewer-wrapper:hover .domus-floating-toolbar,
        .domus-floating-toolbar:hover {
            opacity: 1;
        }

        /* Botões da Toolbar Flutuante */
        .domus-toolbar-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 14px;
        }

        .domus-toolbar-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .domus-toolbar-btn:active {
            transform: scale(0.92);
        }

        .domus-toolbar-divider {
            width: 1px;
            height: 20px;
            background: rgba(255, 255, 255, 0.15);
            margin: 0 4px;
        }

        .domus-zoom-indicator {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            font-size: 12px;
            min-width: 46px;
            text-align: center;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.3px;
        }

        /* ======= LOADING ======= */
        .domus-loading-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(45, 45, 45, 0.85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 50;
            backdrop-filter: blur(4px);
        }

        .domus-spinner {
            width: 36px;
            height: 36px;
            border: 3px solid rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: domus-spin 0.8s linear infinite;
        }

        @keyframes domus-spin {
            to { transform: rotate(360deg); }
        }

        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.06) 0%,
                rgba(255, 255, 255, 0.12) 50%,
                rgba(255, 255, 255, 0.06) 100%
            );
            background-size: 200% 100%;
            animation: domus-shimmer 1.5s ease-in-out infinite;
            border-radius: 4px;
        }

        .skeleton-text { margin: 0 auto; }

        @keyframes domus-shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* ======= EMPTY STATE ======= */
        .domus-document-viewer-wrapper .domus-empty-state {
            background: #f8f9fa;
            transition: opacity 0.2s ease;
        }

        /* ======= HEADER DO CARD ======= */
        .domus-document-viewer-wrapper > .card-header {
            min-height: 50px;
            padding: 0 16px;
            border-bottom: 1px solid #eee;
        }

        .domus-document-viewer-wrapper > .card-header .card-toolbar {
            gap: 4px !important;
            padding: 4px !important;
            background: transparent !important;
        }

        /* Botões da toolbar do card — estilo compacto */
        .domus-document-viewer-wrapper > .card-header .btn-sm.btn-icon {
            width: 32px;
            height: 32px;
        }
    </style>
    @endonce
@endpush

{{-- Template dos Botões da Toolbar (Reutilizável) --}}
@php
    $toolbarButtons = function($isFloating = false) {
        $btnClass = $isFloating ? 'domus-toolbar-btn' : 'btn btn-sm btn-icon btn-light-primary';
        $textClass = $isFloating ? 'domus-zoom-indicator' : 'fw-bold text-gray-600 fs-7 px-2 btn-light-primary btn-sm';
        $iconClass = $isFloating ? 'fs-6' : 'fs-7';
        $divider = $isFloating ? '<div class="domus-toolbar-divider"></div>' : '';
        $dangerClass = $isFloating ? ' text-danger' : '';

        $html = '';

        // Controles Zoom
        $html .= '<button type="button" class="' . $btnClass . ' btn-zoom-out" data-bs-toggle="tooltip" title="Diminuir Zoom (-)">';
        $html .= '<i class="fa-solid fa-minus ' . $iconClass . '"></i></button>';

        $html .= '<span class="' . $textClass . ' zoom-indicator">100%</span>';

        $html .= '<button type="button" class="' . $btnClass . ' btn-zoom-in" data-bs-toggle="tooltip" title="Aumentar Zoom (+)">';
        $html .= '<i class="fa-solid fa-plus ' . $iconClass . '"></i></button>';

        $html .= '<button type="button" class="' . $btnClass . ' btn-fit-zoom" data-bs-toggle="tooltip" title="Ajustar ao Container (0)">';
        $html .= '<i class="fa-solid fa-expand ' . $iconClass . '"></i></button>';

        $html .= $divider;

        // Rotação
        $html .= '<button type="button" class="' . $btnClass . ' btn-rotate-left" data-bs-toggle="tooltip" title="Girar Esquerda">';
        $html .= '<i class="fa-solid fa-rotate-left ' . $iconClass . '"></i></button>';

        $html .= '<button type="button" class="' . $btnClass . ' btn-rotate-right" data-bs-toggle="tooltip" title="Girar Direita">';
        $html .= '<i class="fa-solid fa-rotate-right ' . $iconClass . '"></i></button>';

        $html .= $divider;

        // Ações
        $html .= '<button type="button" class="' . $btnClass . ' btn-delete" data-bs-toggle="tooltip" title="Excluir">';
        $html .= '<i class="fa-solid fa-trash-can ' . $iconClass . $dangerClass . '"></i></button>';

        return $html;
    };
@endphp

<!-- Wrapper Principal -->
<div id="{{ $wrapperId }}" class="domus-document-viewer-wrapper {{ $showCard ? 'card card-bordered shadow-sm mb-5' : 'position-relative w-100 h-100' }}" style="position: relative; z-index: auto; overflow: visible;">

    @if($showCard)
        <!-- HEADER (Modo Card) -->
        <div class="card-header">
            <h3 class="card-title fw-bold fs-5 m-0 text-gray-800">
                <i class="fa-solid fa-file-lines text-primary me-2"></i>
                Visualização
            </h3>
            @if($showControls)
                <div class="card-toolbar d-flex align-items-center gap-2 bg-light-white p-4">
                    {!! $toolbarButtons(false) !!}
                </div>
            @endif
        </div>
    @endif

    <!-- BODY (Container do Viewer) -->
    <div class="{{ $showCard ? 'card-body p-0' : '' }} position-relative"
         style="height: {{ $viewerHeight }}; max-height: {{ $viewerMaxHeight }}; overflow: hidden; position: relative; z-index: auto; min-height: 600px; margin: 0; padding: 0 !important;">

         <!-- TOOLBAR FLUTUANTE (Modo Sem Card) -->
         @if(!$showCard && $showControls)
            <div class="domus-floating-toolbar">
                {!! $toolbarButtons(true) !!}
            </div>
         @endif

         <!-- EMPTY STATE -->
         <div id="{{ $emptyStateId }}" class="domus-empty-state d-flex flex-column align-items-center justify-content-center h-100 text-center p-10" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;">
            <i class="ki-outline ki-folder-up fs-5x text-gray-300 mb-4"></i>
            <p class="text-gray-500 fw-semibold mb-1">Nenhum documento selecionado</p>
            <p class="text-gray-400 fs-7 mb-0">Clique em um documento da lista para visualizá-lo</p>
         </div>

         <!-- VIEWER CONTAINER REAIS -->
         <div id="{{ $viewerContainerId }}" class="domus-viewer-container" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; z-index: 2; margin: 0; padding: 0;">

            <!-- Loading Overlay -->
            <div id="{{ $loadingStateId }}" class="domus-loading-overlay" style="display: none;">
                 <div class="domus-spinner mb-3"></div>
                 <!-- Skeleton Loader -->
                 <div class="d-flex flex-column gap-3 w-75" style="max-width: 400px;">
                     <div class="skeleton skeleton-text" style="height: 12px; width: 80%;"></div>
                     <div class="skeleton skeleton-text" style="height: 12px; width: 100%;"></div>
                     <div class="skeleton skeleton-text" style="height: 12px; width: 60%;"></div>
                 </div>
            </div>

            <!-- Elementos de Mídia -->
            <iframe id="{{ $pdfViewerId }}" class="w-100 h-100 border-0 domus-viewer-pdf" style="min-height: 580px; display: none; position: relative;" allowfullscreen></iframe>
            <img id="{{ $imageViewerId }}" class="domus-viewer-img" style="display: none;" draggable="false" alt="Documento" />
         </div>

    </div>
</div>

@push('scripts')
    @once
    <script>
        /**
         * DomusDocumentViewer v2
         * 
         * Viewer profissional com:
         * - Imagem preenche o container automaticamente (object-fit: contain via CSS)
         * - Zoom 100% = imagem "fit" no container (sem ficar minúscula)
         * - Scroll-wheel zoom (sem precisar de Ctrl)
         * - Duplo-clique para zoom in/out
         * - Rotação 90° esquerda/direita
         * - Pan (arrastar) quando zoom > 100%
         * - Atalhos de teclado (+, -, 0, R)
         */
        class DomusDocumentViewer {
            constructor(wrapperId, config = {}) {
                this.wrapper = document.getElementById(wrapperId);
                if (!this.wrapper) return;

                // Configurações
                this.config = Object.assign({
                    zoomStep: 15,
                    maxZoom: 500,
                    minZoom: 25,
                    wheelZoomStep: 8,
                    doubleClickZoom: 200,
                }, config);

                // Estado
                this.zoomLevel = 100;    // 100% = fit no container (CSS cuida)
                this.rotation = 0;       // Graus (0, 90, 180, 270)
                this.isDragging = false;
                this.startX = 0;
                this.startY = 0;
                this.translateX = 0;
                this.translateY = 0;
                this.currentDoc = null;

                // Elementos UI
                this.container = this.wrapper.querySelector('.domus-viewer-container');
                this.img = this.wrapper.querySelector('.domus-viewer-img');
                this.pdf = this.wrapper.querySelector('.domus-viewer-pdf');
                this.emptyState = this.wrapper.querySelector('[id$="_empty_state"]') ||
                                  this.wrapper.querySelector('[id$="_empty"]') ||
                                  this.wrapper.querySelector('#empty_state') ||
                                  this.wrapper.querySelector('#emptyState');
                this.loadingState = this.wrapper.querySelector('.domus-loading-overlay');

                // Controles
                this.zoomIndicator = this.wrapper.querySelector('.zoom-indicator');
                this.btnZoomIn = this.wrapper.querySelector('.btn-zoom-in');
                this.btnZoomOut = this.wrapper.querySelector('.btn-zoom-out');
                this.btnFitZoom = this.wrapper.querySelector('.btn-fit-zoom');
                this.btnReset = this.wrapper.querySelector('.btn-reset-zoom');
                this.btnRotateLeft = this.wrapper.querySelector('.btn-rotate-left');
                this.btnRotateRight = this.wrapper.querySelector('.btn-rotate-right');
                this.btnDelete = this.wrapper.querySelector('.btn-delete');

                this.initEvents();
            }

            initEvents() {
                // Zoom
                if(this.btnZoomIn) this.btnZoomIn.onclick = () => this.zoomIn();
                if(this.btnZoomOut) this.btnZoomOut.onclick = () => this.zoomOut();
                if(this.btnFitZoom) this.btnFitZoom.onclick = () => this.fitToContainer();
                if(this.btnReset) this.btnReset.onclick = () => this.fitToContainer();

                // Rotação
                if(this.btnRotateLeft) this.btnRotateLeft.onclick = () => this.rotate(-90);
                if(this.btnRotateRight) this.btnRotateRight.onclick = () => this.rotate(90);

                // Excluir
                if(this.btnDelete) {
                    this.btnDelete.onclick = () => {
                        if(this.currentDoc && typeof confirmDeleteDocument === 'function') {
                            confirmDeleteDocument(this.currentDoc.id);
                        }
                    };
                }

                if (this.img) {
                    // Drag / Pan
                    this.img.addEventListener('mousedown', (e) => this.startDrag(e));
                    window.addEventListener('mousemove', (e) => this.drag(e));
                    window.addEventListener('mouseup', () => this.stopDrag());

                    // Scroll-wheel zoom — só quando a imagem está visível e ativa
                    this.container?.addEventListener('wheel', (e) => {
                        // Não interceptar scroll se a imagem não está visível
                        if (!this.img || this.img.style.display === 'none') return;
                        // Não interceptar se o container não está visível
                        if (this.container.style.display === 'none') return;

                        e.preventDefault();
                        const delta = e.deltaY < 0 ? this.config.wheelZoomStep : -this.config.wheelZoomStep;
                        const newZoom = Math.max(this.config.minZoom, Math.min(this.config.maxZoom, this.zoomLevel + delta));
                        if (newZoom !== this.zoomLevel) {
                            this.zoomLevel = newZoom;
                            this.updateCursor();
                            this.applyTransform();
                        }
                    }, { passive: false });

                    // Duplo-clique: zoom in 200% ou volta ao fit
                    this.img.addEventListener('dblclick', (e) => {
                        e.preventDefault();
                        if (this.zoomLevel > 100) {
                            this.fitToContainer();
                        } else {
                            this.zoomLevel = this.config.doubleClickZoom;
                            this.translateX = 0;
                            this.translateY = 0;
                            this.updateCursor();
                            this.applyTransform();
                        }
                    });

                    // Touch (pinch-to-zoom básico)
                    let lastTouchDist = 0;
                    this.container?.addEventListener('touchstart', (e) => {
                        if (e.touches.length === 2) {
                            lastTouchDist = Math.hypot(
                                e.touches[0].clientX - e.touches[1].clientX,
                                e.touches[0].clientY - e.touches[1].clientY
                            );
                        }
                    }, { passive: true });

                    this.container?.addEventListener('touchmove', (e) => {
                        if (e.touches.length === 2) {
                            e.preventDefault();
                            const dist = Math.hypot(
                                e.touches[0].clientX - e.touches[1].clientX,
                                e.touches[0].clientY - e.touches[1].clientY
                            );
                            const delta = (dist - lastTouchDist) * 0.3;
                            this.zoomLevel = Math.max(this.config.minZoom, Math.min(this.config.maxZoom, this.zoomLevel + delta));
                            lastTouchDist = dist;
                            this.updateCursor();
                            this.applyTransform();
                        }
                    }, { passive: false });
                }

                // Atalhos de teclado (só quando o viewer tem foco, sem Ctrl/Cmd para não interferir com zoom do browser)
                this.wrapper.setAttribute('tabindex', '0');
                this.wrapper.style.outline = 'none';
                this.wrapper.addEventListener('keydown', (e) => {
                    // Ignorar se Ctrl/Cmd está pressionado (atalhos do browser)
                    if (e.ctrlKey || e.metaKey) return;

                    switch(e.key) {
                        case '+': case '=': e.preventDefault(); this.zoomIn(); break;
                        case '-':           e.preventDefault(); this.zoomOut(); break;
                        case '0':           e.preventDefault(); this.fitToContainer(); break;
                        case 'r': case 'R': e.preventDefault(); this.rotate(e.shiftKey ? -90 : 90); break;
                    }
                });
            }

            // ============================
            //  LOAD (Carregar documento)
            // ============================
            load(doc) {
                this.currentDoc = doc;
                this.setLoading(true);

                // Wrapper visível
                if(this.wrapper) {
                    this.wrapper.style.display = 'block';
                    this.wrapper.style.zIndex = 'auto';
                    this.wrapper.style.position = 'relative';
                }

                // Esconder empty state
                if(this.emptyState) {
                    this.emptyState.style.setProperty('display', 'none', 'important');
                }

                // Mostrar container e aplicar estilos inline via JS
                if(this.container) {
                    this.container.style.setProperty('display', 'flex', 'important');
                    this.container.style.setProperty('visibility', 'visible', 'important');
                    this.container.style.setProperty('opacity', '1', 'important');
                    this.container.classList.remove('hidden');
                    this.applyContainerStyles();
                }

                const isPdf = doc.mime_type === 'application/pdf';
                const isImage = doc.mime_type && doc.mime_type.startsWith('image/');

                // Esconder ambos
                if(this.pdf) this.pdf.style.display = 'none';
                if(this.img) this.img.style.display = 'none';

                let src = doc.file_url;

                // URL Fallback
                if (!src || src === 'null' || src === 'undefined') {
                    if (doc.caminho_arquivo) {
                        src = '{{ route("domusia.file", ":id") }}'.replace(':id', doc.id);
                        if(src.includes(':id')) {
                            src = '/storage/' + doc.caminho_arquivo;
                        }
                    }
                }

                const hasBase64 = doc.base64_content && doc.base64_content.length < 65000;

                if (isPdf) {
                    this.loadPdf(src, hasBase64, doc);
                } else if (isImage) {
                    this.loadImage(src, hasBase64, doc);
                }
            }

            loadPdf(src, hasBase64, doc) {
                this.zoomLevel = 100;
                this.rotation = 0;

                const setupPdf = (url) => {
                    if(this.pdf) {
                        this.pdf.style.display = 'block';
                        this.pdf.style.width = '100%';
                        this.pdf.style.height = '100%';
                        this.pdf.style.minHeight = '580px';
                        this.pdf.style.border = 'none';
                        this.pdf.src = url;
                        this.pdf.onload = () => this.setLoading(false);
                        this.pdf.onerror = () => { this.setLoading(false); this.showError('Erro ao carregar PDF'); };
                    }
                };

                if (src) {
                    setupPdf(src + '#toolbar=1&navpanes=1&scrollbar=1&zoom=page-width');
                } else if (hasBase64) {
                    setupPdf(`data:application/pdf;base64,${doc.base64_content}#toolbar=1&navpanes=1&scrollbar=1&zoom=page-width`);
                } else {
                    this.showError('PDF não disponível');
                }

                setTimeout(() => this.setLoading(false), 2000);
            }

            loadImage(src, hasBase64, doc) {
                // Resetar estado
                this.zoomLevel = 100;
                this.rotation = 0;
                this.translateX = 0;
                this.translateY = 0;

                // Aplicar estilos no CONTAINER via JS
                this.applyContainerStyles();

                if(this.img) {
                    this.img.style.display = 'block';
                    this.img.classList.remove('dragging');
                    // Aplicar estilos inline na IMAGEM via JS
                    this.applyImageBaseStyles();
                }

                this.img.onload = () => {
                    this.setLoading(false);
                    this.zoomLevel = 100;
                    this.translateX = 0;
                    this.translateY = 0;
                    this.rotation = 0;
                    this.applyImageBaseStyles();
                    this.updateCursor();
                    this.applyTransform();
                };

                this.img.onerror = () => {
                    if (hasBase64) {
                        this.img.src = `data:${doc.mime_type};base64,${doc.base64_content}`;
                    } else {
                        this.setLoading(false);
                        this.showError('Erro ao carregar imagem');
                    }
                };

                if (src) {
                    this.img.src = src;
                } else if (hasBase64) {
                    this.img.src = `data:${doc.mime_type};base64,${doc.base64_content}`;
                } else {
                    this.setLoading(false);
                    this.showError('Imagem não disponível');
                }
            }

            // ============================
            //  ZOOM
            // ============================
            zoomIn() {
                if (this.zoomLevel < this.config.maxZoom) {
                    this.zoomLevel = Math.min(this.config.maxZoom, this.zoomLevel + this.config.zoomStep);
                    this.updateCursor();
                    this.applyTransform();
                }
            }

            zoomOut() {
                if (this.zoomLevel > this.config.minZoom) {
                    this.zoomLevel = Math.max(this.config.minZoom, this.zoomLevel - this.config.zoomStep);
                    // Se voltou para 100% ou menos, centralizar
                    if (this.zoomLevel <= 100) {
                        this.translateX = 0;
                        this.translateY = 0;
                    }
                    this.updateCursor();
                    this.applyTransform();
                }
            }

            fitToContainer() {
                this.zoomLevel = 100;
                this.translateX = 0;
                this.translateY = 0;
                this.updateCursor();
                this.applyTransform();
            }

            // Mantém compatibilidade com código antigo
            resetZoom() {
                this.fitToContainer();
            }

            // ============================
            //  ROTAÇÃO
            // ============================
            rotate(degrees) {
                this.rotation = (this.rotation + degrees + 360) % 360;
                this.translateX = 0;
                this.translateY = 0;
                this.applyTransform();
            }

            // ============================
            //  TRANSFORM
            // ============================
            /**
             * Aplica estilos inline no container do viewer (background, layout)
             * Chamado via JS para garantir estilos independente de CSS externo
             */
            applyContainerStyles() {
                if (!this.container) return;
                const c = this.container.style;
                c.setProperty('background-color', '#2d2d2d', 'important');
                c.setProperty('background-image', 'radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0)', 'important');
                c.setProperty('background-size', '20px 20px', 'important');
                c.setProperty('overflow', 'hidden', 'important');
                c.setProperty('position', 'absolute', 'important');
                c.setProperty('top', '0', 'important');
                c.setProperty('left', '0', 'important');
                c.setProperty('right', '0', 'important');
                c.setProperty('bottom', '0', 'important');
                c.setProperty('width', '100%', 'important');
                c.setProperty('height', '100%', 'important');
            }

            /**
             * Aplica estilos base na imagem via JS inline + !important
             * Técnica: position absolute + top:50% + left:50% + translate(-50%,-50%)
             * Isso centraliza a imagem independente de Tailwind, Bootstrap ou qualquer framework
             */
            applyImageBaseStyles() {
                if (!this.img) return;
                const s = this.img.style;
                s.setProperty('position', 'absolute', 'important');
                s.setProperty('top', '50%', 'important');
                s.setProperty('left', '50%', 'important');
                s.setProperty('max-width', '100%', 'important');
                s.setProperty('max-height', '100%', 'important');
                s.setProperty('width', 'auto', 'important');
                s.setProperty('height', 'auto', 'important');
                s.setProperty('object-fit', 'contain', 'important');
                s.setProperty('user-select', 'none', 'important');
                s.setProperty('transform-origin', 'center center', 'important');
                s.setProperty('display', 'block', 'important');
            }

            applyTransform() {
                if(this.zoomIndicator) {
                    this.zoomIndicator.textContent = `${Math.round(this.zoomLevel)}%`;
                }

                if (this.img && this.img.style.display !== 'none') {
                    const scale = this.zoomLevel / 100;

                    // Base: translate(-50%, -50%) SEMPRE para centralizar via position absolute
                    let transform = `translate(-50%, -50%) scale(${scale})`;

                    // Rotação
                    if (this.rotation !== 0) {
                        transform += ` rotate(${this.rotation}deg)`;
                    }

                    // Pan (translação adicional) — só quando zoom > 100%
                    if (this.zoomLevel > 100 && (this.translateX !== 0 || this.translateY !== 0)) {
                        transform += ` translate(${this.translateX / scale}px, ${this.translateY / scale}px)`;
                    }

                    // Usar setProperty com !important para NADA poder sobrescrever
                    this.img.style.setProperty('transform', transform, 'important');
                    this.img.style.setProperty('transform-origin', 'center center', 'important');
                }
            }

            updateCursor() {
                if (!this.img) return;
                if (this.zoomLevel > 100) {
                    this.img.classList.remove('cursor-default');
                    this.img.style.cursor = 'grab';
                } else {
                    this.img.classList.add('cursor-default');
                    this.img.style.cursor = 'default';
                }
            }

            // ============================
            //  DRAG / PAN
            // ============================
            startDrag(e) {
                if (this.zoomLevel <= 100) return;
                this.isDragging = true;
                this.img.classList.add('dragging');
                this.startX = e.clientX - this.translateX;
                this.startY = e.clientY - this.translateY;
                e.preventDefault();
            }

            drag(e) {
                if (!this.isDragging) return;
                this.translateX = e.clientX - this.startX;
                this.translateY = e.clientY - this.startY;
                this.applyTransform();
            }

            stopDrag() {
                this.isDragging = false;
                if(this.img) this.img.classList.remove('dragging');
            }

            // ============================
            //  UTILIDADES
            // ============================
            setLoading(isLoading) {
                if(this.loadingState) this.loadingState.style.display = isLoading ? 'flex' : 'none';
            }

            updateNavButtons() {
                // Mantida para compatibilidade
            }

            showError(msg) {
                this.setLoading(false);
                console.warn('[DomusViewer]', msg);
            }
        }

        // Expose Class globally
        window.DomusDocumentViewer = DomusDocumentViewer;
    </script>
    @endonce

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewer = new DomusDocumentViewer('{{ $wrapperId }}');

            if ('{{ $wrapperId }}'.includes('documentViewer')) {
                window.mainDocumentViewer = viewer;
                window.loadDocumentFromDatabase = (doc) => viewer.load(doc);
                window.resetZoom = () => viewer.resetZoom();
            }
        });
    </script>
@endpush
