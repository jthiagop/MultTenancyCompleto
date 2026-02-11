<!--begin::Lista de Arquivos Pendentes-->
<div class="pending-documents-list">
    <div id="pendingDocumentsList" class="position-relative">
        <!-- Skeleton Loading (Renderizado inicialmente) -->
        <div id="loadingSkeleton">
            @for ($i = 0; $i < 5; $i++)
            <div class="skeleton-item mb-3 p-3 rounded d-flex bg-white">
                <div class="skeleton-icon skeleton w-40px h-40px rounded me-3"></div>
                <div class="flex-grow-1">
                    <div class="skeleton w-75 h-20px mb-2 rounded"></div>
                    <div class="skeleton w-50 h-15px rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
<!--end::Lista de Arquivos Pendentes-->

@push('styles')
<style>
    .pending-documents-list {
        max-height: 480px; /* Altura fixa para scroll */
        overflow-y: auto;  /* Scroll vertical */
        padding-right: 5px;
    }
    
    /* Scrollbar Customizada (Estilo Chrome/Safari) */
    .pending-documents-list::-webkit-scrollbar {
        width: 6px;
    }
    .pending-documents-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .pending-documents-list::-webkit-scrollbar-thumb {
        background: #dbdfe9;
        border-radius: 3px;
    }
    .pending-documents-list::-webkit-scrollbar-thumb:hover {
        background: #cdd3e3;
    }

    .pending-document-item .document-item {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent; /* Evita pulo no hover se adicionar borda */
    }

    .pending-document-item .document-item:hover {
        background-color: #f1faff !important; /* light-primary mais suave */
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #dff1ff;
    }

    .pending-document-item.active .document-item {
        background-color: #e1f5ff !important;
        border-color: #009ef7;
        box-shadow: 0 0 0 1px #009ef7; /* Borda mais nítida */
    }

    /* Skeleton Animation */
    @keyframes skeleton-pulse {
        0% { opacity: 0.6; }
        50% { opacity: 0.3; }
        100% { opacity: 0.6; }
    }
    
    .skeleton {
        background-color: #e4e6ef;
        animation: skeleton-pulse 1.5s ease-in-out infinite;
    }
    
    /* Popover Customization */
    .popover-inverse .popover-header {
        background-color: #1e1e2d;
        color: #fff;
    }
    .popover-inverse .popover-body {
        color: #3f4254;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pendingDocumentsList = document.getElementById('pendingDocumentsList');
        const loadingSkeleton = document.getElementById('loadingSkeleton');

        if (!pendingDocumentsList) return;

        // Função para mostrar loading (skeleton)
        window.addLoadingElement = function() {
            if (loadingSkeleton) {
                loadingSkeleton.style.display = 'block';
            }
            // Limpa o conteúdo atual, SE não for o skeleton
            Array.from(pendingDocumentsList.children).forEach(child => {
                 if (child.id !== 'loadingSkeleton') {
                     child.style.display = 'none';
                 }
            });
        };

        // Função para esconder loading
        window.removeLoadingElement = function() {
            if (loadingSkeleton) {
                loadingSkeleton.style.display = 'none';
            }
             // Restaura visibilidade (opcional, gerenciado pelo render)
        };

        // Renderizar lista de documentos pendentes
        // Agora aceita HTML pré-renderizado ou lista de objetos (para compatibilidade/fallback)
        window.renderPendingDocuments = function(dataOrHtml) {
            
            // Garantir que o loading seja removido
            window.removeLoadingElement();

            // Atualizar contadores
            let count = 0;
            let htmlContent = '';

            if (typeof dataOrHtml === 'string') {
                // Modo Novo: HTML Renderizado no Server
                htmlContent = dataOrHtml;
                // Tenta extrair contagem aproximada se necessário, ou usar variável global
                // count será gerenciado fora daqui
            } else if (Array.isArray(dataOrHtml)) {
                // Fallback Modo Antigo (JSON) ou lista vazia
                count = dataOrHtml.length;
                if (count === 0) {
                     htmlContent = `
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fs-1 text-gray-400 mb-3 d-block"></i>
                            <div class="text-gray-500 fw-semibold">Nenhum documento encontrado</div>
                        </div>`;
                }
            }

            // Injetar HTML
            // Mantemos o skeleton escondido mas presente no DOM
            if(htmlContent) {
                // Limpar conteúdo anterior (exceto skeleton)
                // Uma maneira segura é setar innerHTML, mas precisamos reinserir o skeleton se quisermos reutilizar
                // Melhor: wrap do conteúdo real
                
                // Vamos simplificar: sobrescrever tudo, mas se o skeleton for reutilizável, ele deve ser adicionado via JS no addLoadingElement
                pendingDocumentsList.innerHTML = htmlContent;
                // Se precisarmos do skeleton de volta, a função addLoadingElement terá que recriá-lo ou o blade terá que incluí-lo sempre.
                // Ajuste: A função addLoadingElement abaixo recriará o skeleton se necessário.
            }
            
            // Re-inicializar Popovers (Bootstrap 5)
            // Seleciona todos os elementos com data-bs-toggle="popover" DENTRO da lista
            const popoverTriggerList = [].slice.call(pendingDocumentsList.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    trigger: 'hover',
                    html: true,
                    customClass: 'popover-inverse shadow-sm' 
                });
            });
        };
        
        // Sobrescrever função de adicionar loading para recriar o skeleton se ele foi removido
        window.addLoadingElement = function() {
             const skeletonHtml = `
                <div id="loadingSkeleton">
                    @for ($i = 0; $i < 5; $i++)
                    <div class="skeleton-item mb-3 p-3 rounded d-flex bg-white">
                        <div class="skeleton-icon skeleton w-40px h-40px rounded me-3"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton w-75 h-20px mb-2 rounded"></div>
                            <div class="skeleton w-50 h-15px rounded"></div>
                        </div>
                    </div>
                    @endfor
                </div>
             `;
             pendingDocumentsList.innerHTML = skeletonHtml;
        };
    });
</script>
@endpush

