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
        /* Container do Viewer - Simples e Direto */
        .domus-viewer-container {
            background-color: #525659;
            width: 100%;
            height: 100%;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            margin: 0;
            padding: 0;
        }

        /* Imagem - Centralizada e com zoom customizado */
        .domus-viewer-container img {
            cursor: grab;
            user-select: none;
            transition: transform 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transform-origin: center center;
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto;
            object-fit: contain;
            position: relative;
        }
        
        .domus-viewer-container img.dragging {
            cursor: grabbing;
            transition: none; /* Remove transição durante o drag */
        }

        /* Toolbar Flutuante (Glassmorphism) - mantida para modo sem card */
        .domus-floating-toolbar {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            z-index: 100;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .domus-floating-toolbar:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Botões da Toolbar Flutuante */
        .domus-toolbar-btn {
            background: transparent;
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .domus-toolbar-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .domus-zoom-indicator {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            min-width: 50px;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        /* Loading Spinner */
        .domus-loading-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(82, 86, 89, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 50;
            backdrop-filter: blur(2px);
        }
        
        .domus-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0.1) 100%
            );
            background-size: 200% 100%;
            animation: shimmer 1.5s ease-in-out infinite;
            border-radius: 4px;
        }
        
        .skeleton-text {
            margin: 0 auto;
        }
        
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
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

        ob_start();
        @endphp
        <!-- Botão Excluir -->
        <button type="button" class="{{ $btnClass }} btn-delete" data-bs-toggle="tooltip" title="Excluir">
            <i class="fa-solid fa-trash-can {{ $iconClass }} {{ $isFloating ? 'text-danger' : '' }}"></i>
        </button>

        @if($isFloating) <div class="vr bg-white opacity-25 mx-1 h-50"></div> @endif

        <!-- Controles Zoom -->
        <button type="button" class="{{ $btnClass }} btn-zoom-out" data-bs-toggle="tooltip" title="Diminuir Zoom">
            <i class="fa-solid fa-minus {{ $iconClass }}"></i>
        </button>

        <span class="{{ $textClass }} zoom-indicator">100%</span>

        <button type="button" class="{{ $btnClass }} btn-zoom-in" data-bs-toggle="tooltip" title="Aumentar Zoom">
            <i class="fa-solid fa-plus {{ $iconClass }}"></i>
        </button>

        <button type="button" class="{{ $btnClass }} btn-reset-zoom" data-bs-toggle="tooltip" title="Resetar">
            <i class="fa-solid fa-compress {{ $iconClass }}"></i>
        </button>
        @php
        return ob_get_clean();
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
         <div id="{{ $emptyStateId }}" class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-10 bg-light" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1; margin: 0; padding: 0;">
            <i class="ki-outline ki-folder-up fs-5x text-gray-300 mb-4 scale-up-hover"></i>
            <p class="text-gray-500 fw-semibold mb-0">Nenhum documento selecionado</p>
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
            <img id="{{ $imageViewerId }}" class="domus-viewer-img" style="max-width: 100%; max-height: 100%; width: auto; height: auto; display: none; margin: 0 auto; position: relative;" draggable="false" alt="Documento" />
         </div>

    </div>
</div>

@push('scripts')
    @once
    <script>
        /**
         * Classe DomusDocumentViewer
         * Gerencia visualização de documentos de forma isolada e orientada a objetos.
         */
        class DomusDocumentViewer {
            constructor(wrapperId, config = {}) {
                this.wrapper = document.getElementById(wrapperId);
                if (!this.wrapper) return;

                // Configurações
                this.config = Object.assign({
                    zoomStep: 25,
                    maxZoom: 500,
                    minZoom: 50
                }, config);

                // Estado
                this.zoomLevel = 100;
                this.isDragging = false;
                this.startX = 0;
                this.startY = 0;
                this.translateX = 0;
                this.translateY = 0;
                this.currentDoc = null;

                // Elementos UI (Busca dentro do wrapper para isolamento)
                this.container = this.wrapper.querySelector('.domus-viewer-container');
                this.img = this.wrapper.querySelector('.domus-viewer-img');
                this.pdf = this.wrapper.querySelector('.domus-viewer-pdf');
                // Buscar empty state por ID que termina em _empty_state ou _empty
                this.emptyState = this.wrapper.querySelector('[id$="_empty_state"]') ||
                                  this.wrapper.querySelector('[id$="_empty"]') ||
                                  this.wrapper.querySelector('#empty_state') ||
                                  this.wrapper.querySelector('#emptyState');
                this.loadingState = this.wrapper.querySelector('.domus-loading-overlay');

                // Controles
                this.zoomIndicator = this.wrapper.querySelector('.zoom-indicator');
                this.btnZoomIn = this.wrapper.querySelector('.btn-zoom-in');
                this.btnZoomOut = this.wrapper.querySelector('.btn-zoom-out');
                this.btnReset = this.wrapper.querySelector('.btn-reset-zoom');
                this.btnDelete = this.wrapper.querySelector('.btn-delete');

                // Inicializar
                this.initEvents();
            }

            initEvents() {
                // Zoom
                if(this.btnZoomIn) this.btnZoomIn.onclick = () => this.zoomIn();
                if(this.btnZoomOut) this.btnZoomOut.onclick = () => this.zoomOut();
                if(this.btnReset) this.btnReset.onclick = () => this.resetZoom();

                // Excluir
                if(this.btnDelete) {
                    this.btnDelete.onclick = () => {
                        if(this.currentDoc && typeof confirmDeleteDocument === 'function') {
                            confirmDeleteDocument(this.currentDoc.id);
                        }
                    };
                }

                // Drag & Drop (Pan) na Imagem
                if (this.img) {
                    this.img.addEventListener('mousedown', (e) => this.startDrag(e));
                    window.addEventListener('mousemove', (e) => this.drag(e));
                    window.addEventListener('mouseup', () => this.stopDrag());
                    this.img.addEventListener('wheel', (e) => {
                        if (e.ctrlKey) {
                            e.preventDefault();
                            e.deltaY < 0 ? this.zoomIn() : this.zoomOut();
                        }
                    });
                }
            }

            load(doc) {
                this.currentDoc = doc;
                // Não resetar zoom aqui - será feito no processamento específico de imagem/PDF
                this.setLoading(true);

                // Primeiro, garantir que o wrapper está visível
                if(this.wrapper) {
                    if(this.wrapper.style.display === 'none') {
                        this.wrapper.style.display = 'block';
                    }
                    this.wrapper.style.display = 'block';
                    // Garantir que o wrapper não tenha z-index alto que cause sobreposição
                    this.wrapper.style.zIndex = 'auto';
                    this.wrapper.style.position = 'relative';
                }

                // Esconder empty state PRIMEIRO
                if(this.emptyState) {
                    this.emptyState.style.setProperty('display', 'none', 'important');
                    this.emptyState.style.setProperty('visibility', 'hidden', 'important');
                    this.emptyState.style.setProperty('opacity', '0', 'important');
                    this.emptyState.classList.add('hidden');
                }

                // Mostrar container DEPOIS
                if(this.container) {
                    this.container.style.setProperty('display', 'flex', 'important');
                    this.container.style.setProperty('visibility', 'visible', 'important');
                    this.container.style.setProperty('opacity', '1', 'important');
                    this.container.style.setProperty('position', 'absolute', 'important');
                    this.container.style.setProperty('top', '0', 'important');
                    this.container.style.setProperty('left', '0', 'important');
                    this.container.style.setProperty('right', '0', 'important');
                    this.container.style.setProperty('bottom', '0', 'important');
                    this.container.style.setProperty('width', '100%', 'important');
                    this.container.style.setProperty('height', '100%', 'important');
                    this.container.classList.remove('hidden');
                }

                // Tipo de arquivo
                const isPdf = doc.mime_type === 'application/pdf';
                const isImage = doc.mime_type && doc.mime_type.startsWith('image/');

                // Esconder ambos inicialmente
                if(this.pdf) this.pdf.style.display = 'none';
                if(this.img) this.img.style.display = 'none';

                let src = doc.file_url;

                // URL Fallback Logic
                if (!src || src === 'null' || src === 'undefined') {
                    if (doc.caminho_arquivo) {
                         src = '{{ route("domusia.file", ":id") }}'.replace(':id', doc.id);
                         if(src.includes(':id')) {
                             src = '/storage/' + doc.caminho_arquivo;
                         }
                    }
                }

                // Base64 Fallback
                const hasBase64 = doc.base64_content && doc.base64_content.length < 65000;

                if (isPdf) {
                    this.setLoading(true);
                    // Para PDF, resetar zoom (não se aplica mas mantém consistência)
                    this.zoomLevel = 100;

                    if (src) {
                        // Parâmetros completos da toolbar do PDF
                        const pdfUrl = src + '#toolbar=1&navpanes=1&scrollbar=1&zoom=page-width';

                        if(this.pdf) {
                            this.pdf.style.display = 'block';
                            this.pdf.style.width = '100%';
                            this.pdf.style.height = '100%';
                            this.pdf.style.minHeight = '580px';
                            this.pdf.style.border = 'none';
                        }

                        this.pdf.src = pdfUrl;
                        this.pdf.onload = () => {
                            this.setLoading(false);
                        };
                        this.pdf.onerror = (e) => {
                            this.setLoading(false);
                            this.showError('Erro ao carregar PDF');
                        };
                    } else if (hasBase64) {
                        const base64Url = `data:application/pdf;base64,${doc.base64_content}#toolbar=1&navpanes=1&scrollbar=1&zoom=page-width`;

                        if(this.pdf) {
                            this.pdf.style.display = 'block';
                            this.pdf.style.width = '100%';
                            this.pdf.style.height = '100%';
                            this.pdf.style.minHeight = '580px';
                            this.pdf.style.border = 'none';
                        }

                        this.pdf.src = base64Url;
                        this.pdf.onload = () => {
                            this.setLoading(false);
                        };
                        this.pdf.onerror = (e) => {
                            this.setLoading(false);
                            this.showError('Erro ao carregar PDF via Base64');
                        };
                    } else {
                        this.showError('PDF não disponível');
                    }

                    // Safety fallback timeout
                    setTimeout(() => {
                        this.setLoading(false);
                    }, 2000);

                } else if (isImage) {
                    this.setLoading(true);

                    // Mostrar a imagem e configurar
                    if(this.img) {
                        this.img.style.display = 'block';
                        this.img.classList.remove('dragging');
                        // Resetar transformações e zoom inicial
                        this.img.style.transform = '';
                        this.translateX = 0;
                        this.translateY = 0;
                        // Resetar zoom para calcular corretamente após imagem carregar
                        this.zoomLevel = 100;
                    }

                    this.img.onload = () => {
                        this.setLoading(false);
                        // Garantir que a imagem esteja visível após carregar
                        if(this.img) {
                            this.img.style.display = 'block';
                            this.img.style.margin = '0 auto';
                            this.img.style.maxWidth = '100%';
                            this.img.style.maxHeight = '100%';
                            this.img.style.width = 'auto';
                            this.img.style.height = 'auto';
                            this.img.style.position = 'relative';
                            
                            // Calcular zoom inicial inteligente: fit para caber no container
                            const container = this.container;
                            if(container && this.img.naturalWidth && this.img.naturalHeight) {
                                const containerWidth = container.clientWidth || container.offsetWidth;
                                const containerHeight = container.clientHeight || container.offsetHeight;
                                const imgWidth = this.img.naturalWidth;
                                const imgHeight = this.img.naturalHeight;
                                
                                // Calcular escala para preencher melhor o container
                                // Se a imagem for maior: ajustar para caber (fit)
                                // Se a imagem for menor: ampliar para ocupar ~85% do container (mantendo proporção)
                                const scaleX = containerWidth / imgWidth;
                                const scaleY = containerHeight / imgHeight;
                                const fitScale = Math.min(scaleX, scaleY); // Escala para caber perfeitamente
                                
                                // Se a imagem é menor que o container, ampliar para ocupar 85% do espaço
                                if (fitScale > 1) {
                                    // Imagem é menor: ampliar para 85% do container
                                    this.zoomLevel = Math.floor(fitScale * 100 * 0.85);
                                } else {
                                    // Imagem é maior: ajustar para caber (fit)
                                    this.zoomLevel = Math.floor(fitScale * 100);
                                }
                                
                                // Resetar transformações para centralizar
                                this.translateX = 0;
                                this.translateY = 0;
                                
                                // Aplicar transformação com centralização
                                this.applyTransform();
                                
                                // Atualizar indicador de zoom
                                if(this.zoomIndicator) {
                                    this.zoomIndicator.textContent = `${this.zoomLevel}%`;
                                }
                            } else {
                                // Fallback: usar 100% se não conseguir calcular
                                this.zoomLevel = 100;
                                this.translateX = 0;
                                this.translateY = 0;
                                this.applyTransform();
                                
                                // Garantir que indicador seja atualizado
                                if(this.zoomIndicator) {
                                    this.zoomIndicator.textContent = `${this.zoomLevel}%`;
                                }
                            }
                        }
                    };
                    
                    this.img.onerror = (e) => {
                        if (hasBase64) {
                            const base64Url = `data:${doc.mime_type};base64,${doc.base64_content}`;
                            this.img.src = base64Url;
                        } else {
                           this.setLoading(false);
                           this.showError('Erro ao carregar imagem');
                        }
                    };

                    if (src) {
                        this.img.src = src;
                    } else if (hasBase64) {
                        const base64Url = `data:${doc.mime_type};base64,${doc.base64_content}`;
                        this.img.src = base64Url;
                    } else {
                        this.setLoading(false);
                        this.showError('Imagem não disponível');
                    }
                }
            }

            setLoading(isLoading) {
                if(this.loadingState) this.loadingState.style.display = isLoading ? 'flex' : 'none';
            }

            zoomIn() {
                if (this.zoomLevel < this.config.maxZoom) {
                    this.zoomLevel += this.config.zoomStep;
                    this.applyTransform();
                }
            }

            zoomOut() {
                if (this.zoomLevel > this.config.minZoom) {
                    this.zoomLevel -= this.config.zoomStep;
                    this.applyTransform();
                }
            }

            resetZoom() {
                // Resetar zoom para o nível inicial inteligente: 100% se couber, fit se for muito grande
                if(this.img && this.img.style.display !== 'none') {
                    const container = this.container;
                    if(container && this.img.naturalWidth && this.img.naturalHeight) {
                        const containerWidth = container.clientWidth || container.offsetWidth;
                        const containerHeight = container.clientHeight || container.offsetHeight;
                        const imgWidth = this.img.naturalWidth;
                        const imgHeight = this.img.naturalHeight;
                        
                        // Calcular escala para preencher melhor o container
                        // Se a imagem for maior: ajustar para caber (fit)
                        // Se a imagem for menor: ampliar para ocupar ~85% do container (mantendo proporção)
                        const scaleX = containerWidth / imgWidth;
                        const scaleY = containerHeight / imgHeight;
                        const fitScale = Math.min(scaleX, scaleY); // Escala para caber perfeitamente
                        
                        // Se a imagem é menor que o container, ampliar para ocupar 85% do espaço
                        if (fitScale > 1) {
                            // Imagem é menor: ampliar para 85% do container
                            this.zoomLevel = Math.floor(fitScale * 100 * 0.85);
                        } else {
                            // Imagem é maior: ajustar para caber (fit)
                            this.zoomLevel = Math.floor(fitScale * 100);
                        }
                    } else {
                        // Fallback: usar 100% se não conseguir calcular
                        this.zoomLevel = 100;
                    }
                    
                    this.translateX = 0;
                    this.translateY = 0;
                    this.applyTransform();
                } else {
                    this.zoomLevel = 100;
                }
            }

            applyTransform() {
                if(this.zoomIndicator) this.zoomIndicator.textContent = `${this.zoomLevel}%`;

                if (this.img && this.img.style.display !== 'none') {
                    const scale = this.zoomLevel / 100;
                    // Garantir que a imagem permaneça centralizada mesmo com zoom e pan
                    this.img.style.transform = `scale(${scale}) translate(${this.translateX / scale}px, ${this.translateY / scale}px)`;
                    this.img.style.transformOrigin = 'center center';
                    // Garantir margin auto para centralização horizontal
                    if(!this.img.style.margin || this.img.style.margin === '0px') {
                        this.img.style.margin = '0 auto';
                    }
                }
            }

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

            updateNavButtons() {
                // Botões de navegação removidos - função mantida para compatibilidade
            }

            showError(msg) {
                this.setLoading(false);
            }
        }

        // Expose Class globally
        window.DomusDocumentViewer = DomusDocumentViewer;
    </script>
    @endonce

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Instantiate viewer for this specific component
            const viewer = new DomusDocumentViewer('{{ $wrapperId }}');

            // Expose instance if this is the main viewer
            if ('{{ $wrapperId }}'.includes('documentViewer')) {
                window.mainDocumentViewer = viewer;

                // Adapter for old global functions (Backward Compatibility)
                window.loadDocumentFromDatabase = (doc) => {
                    viewer.load(doc);
                };
                window.resetZoom = () => viewer.resetZoom();
            }
        });
    </script>
@endpush
