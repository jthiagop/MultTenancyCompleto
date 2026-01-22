/**
 * Domusia - Gerenciador de Documentos Pendentes
 *
 * Encapsula toda a lógica de gerenciamento de documentos pendentes
 */
class DomusiaPendentes {
    constructor(config) {
        this.config = config;
        this.currentDocument = null;
        this.selectedFiles = [];
        this.documentosCarregados = [];
        this.currentDocumentIndex = 0;
        this.zoomLevel = 100;
        this.documentList = [];

        // Elementos do DOM (compatibilidade com código existente)
        // Nota: O DomusDocumentViewer agora gerencia a visualização de documentos
        this.elements = {
            documentViewer: document.getElementById('documentViewerWrapper') || document.getElementById('documentViewerCard'),
            emptyState: document.querySelector('[id$="_empty_state"]') || document.getElementById('emptyState'),
            deleteDocumentBtn: document.getElementById('deleteDocumentBtn'),
            extractedEntriesCard: document.getElementById('extractedEntriesCard'),
            extractedEntriesList: document.getElementById('extractedEntriesList'),
            thumbnailsContainer: document.getElementById('thumbnailsContainer'),
            sidebarContainer: document.getElementById('sidebarContainer'),
            documentPositionIndicator: document.getElementById('documentPositionIndicator'),
            prevDocumentBtn: document.getElementById('prevDocumentBtn'),
            nextDocumentBtn: document.getElementById('nextDocumentBtn'),
            deleteDocumentViewerBtn: document.getElementById('deleteDocumentViewerBtn'),
            countBadge: document.getElementById('documentosCountBadge'),
        };

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadPendingDocuments();
    }

    setupEventListeners() {
        // Botão de excluir documento
        if (this.elements.deleteDocumentBtn) {
            this.elements.deleteDocumentBtn.addEventListener('click', () => {
                this.handleDeleteDocument();
            });
        }

        // Botões de navegação
        if (this.elements.prevDocumentBtn) {
            this.elements.prevDocumentBtn.addEventListener('click', () => {
                this.navigateDocument('prev');
            });
        }

        if (this.elements.nextDocumentBtn) {
            this.elements.nextDocumentBtn.addEventListener('click', () => {
                this.navigateDocument('next');
            });
        }
    }

    /**
     * Carrega documentos pendentes do servidor
     */
    async loadPendingDocuments() {
        this.addLoadingElement();

        try {
            const response = await fetch(this.config.routes.list, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            this.validateJsonResponse(response);

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP error! status: ${response.status}`);
            }

            this.removeLoadingElement();

            if (result.success) {
                this.documentosCarregados = result.data || [];
                this.documentList = this.documentosCarregados;

                // Verificar se o backend retornou HTML pré-renderizado
                if (result.html && typeof window.renderPendingDocuments === 'function') {
                    window.renderPendingDocuments(result.html);
                } else if (typeof window.renderPendingDocuments === 'function') {
                    window.renderPendingDocuments(this.documentosCarregados);
                }

                if (typeof window.renderThumbnails === 'function') {
                    window.renderThumbnails(this.documentosCarregados);
                }

                this.updateCountBadge(this.documentosCarregados.length);

                // Auto-selecionar o primeiro documento se não houver nenhum selecionado
                // Mas apenas se não estivermos pós-upload (quando _skipAutoSelect está true)
                if (this.documentosCarregados.length > 0 && !this.currentDocument && !this._skipAutoSelect) {
                    // Aguardar um pouco para garantir que a lista foi renderizada
                    setTimeout(() => {
                        const firstDocument = this.documentosCarregados[0];
                        if (firstDocument && firstDocument.id) {
                            this.selectDocumentFromDatabase(firstDocument.id);
                        }
                    }, 300);
                }
            } else {
                this.documentosCarregados = [];
                this.documentList = [];
                if (typeof window.renderPendingDocuments === 'function') {
                    window.renderPendingDocuments([]);
                }
                if (typeof window.renderThumbnails === 'function') {
                    window.renderThumbnails([]);
                }
                this.updateCountBadge(0);
            }
        } catch (error) {
            console.error('Erro ao carregar documentos:', error);
            this.removeLoadingElement();
            this.showErrorInList(error.message);
        }
    }

    /**
     * Valida se a resposta é JSON
     */
    validateJsonResponse(response) {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error(
                `Servidor retornou ${contentType || 'tipo desconhecido'} ao invés de JSON. Status: ${response.status}`
            );
        }
    }

    /**
     * Atualiza o badge de contagem de documentos
     */
    updateCountBadge(count) {
        if (this.elements.countBadge) {
            this.elements.countBadge.textContent = count + ' restantes';
        }
    }

    /**
     * Mostra erro na lista de documentos
     */
    showErrorInList(message) {
        const pendingDocumentsList = document.getElementById('pendingDocumentsList');
        if (pendingDocumentsList) {
            pendingDocumentsList.innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fa-solid fa-exclamation-triangle fs-2x mb-3"></i>
                    <div class="fw-bold">Erro ao carregar documentos</div>
                    <div class="text-muted fs-7">${message}</div>
                </div>
            `;
        }
    }

    /**
     * Processa arquivos enviados
     */
    handleFiles(files) {
        files.forEach(file => {
            // Validar tamanho
            if (file.size > this.config.maxFileSize) {
                this.showSwal({
                    icon: 'error',
                    title: 'Arquivo muito grande',
                    text: `O arquivo "${file.name}" excede o limite de ${this.config.maxFileSizeMB} MB.`,
                });
                return;
            }

            // Validar tipo
            if (!this.config.allowedTypes.includes(file.type)) {
                this.showSwal({
                    icon: 'error',
                    title: 'Tipo de arquivo não permitido',
                    text: `O arquivo "${file.name}" não é um PDF ou imagem válida.`,
                });
                return;
            }

            // Verificar se o arquivo já está sendo processado
            const isProcessing = this.selectedFiles.find(
                f => f.name === file.name && f.size === file.size
            );
            if (isProcessing) {
                return;
            }

            // Adicionar arquivo à lista de processamento
            this.selectedFiles.push(file);

            // Processar arquivo
            this.processAndDisplayFile(file);
        });
    }

    /**
     * Processa e exibe arquivo
     */
    async processAndDisplayFile(file) {
        // Mostrar preview imediatamente
        const reader = new FileReader();
        reader.onload = (e) => {
            const fileData = e.target.result;
            this.loadDocumentFromData(fileData, file.name, file.type);
        };
        reader.readAsDataURL(file);

        // Salvar arquivo no servidor
        try {
            const readerForUpload = new FileReader();
            readerForUpload.onload = async (e) => {
                try {
                    const base64Content = e.target.result.split(',')[1];
                    const mimeType = file.type;

                    const response = await fetch(this.config.routes.extract, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.config.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            base64_content: base64Content,
                            mime_type: mimeType,
                            filename: file.name
                        })
                    });

                    let responseData;
                    try {
                        responseData = await response.json();
                    } catch (jsonError) {
                        throw new Error('Erro ao processar resposta do servidor');
                    }

                    if (!response.ok) {
                        throw new Error(
                            responseData.message || 'Erro ao salvar documento'
                        );
                    }

                    // Sucesso
                    this.showSwal({
                        icon: 'success',
                        title: 'Arquivo salvo!',
                        text: 'O arquivo foi salvo com sucesso.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Remover arquivo da lista de processamento
                    this.selectedFiles = this.selectedFiles.filter(
                        f => !(f.name === file.name && f.size === file.size)
                    );

                    const newDocumentId = responseData.documento_id;

                    // Recarregar lista de documentos após salvar
                    // Usar uma flag para evitar auto-seleção do primeiro documento
                    this._skipAutoSelect = true;
                    
                    setTimeout(async () => {
                        await this.loadPendingDocuments();

                        // Exibir o documento recém-enviado (último documento)
                        if (newDocumentId) {
                            setTimeout(() => {
                                if (typeof window.selectDocumentFromDatabase === 'function') {
                                    window.selectDocumentFromDatabase(newDocumentId);
                                }
                            }, 300);
                        } else {
                            // Se não houver ID específico, selecionar o último documento da lista
                            setTimeout(() => {
                                if (this.documentList.length > 0) {
                                    const lastDocument = this.documentList[this.documentList.length - 1];
                                    if (lastDocument && lastDocument.id) {
                                        this.selectDocumentFromDatabase(lastDocument.id);
                                    }
                                }
                            }, 300);
                        }
                        
                        // Limpar flag após processamento
                        setTimeout(() => {
                            this._skipAutoSelect = false;
                        }, 1000);
                    }, 500);

                } catch (error) {
                    this.selectedFiles = this.selectedFiles.filter(
                        f => !(f.name === file.name && f.size === file.size)
                    );

                    if (error.status === 401) {
                        this.handleSessionExpired();
                        return;
                    }

                    this.showSwal({
                        icon: 'error',
                        title: 'Erro ao salvar',
                        text: error.message || 'Ocorreu um erro ao salvar o arquivo.',
                    });
                }
            };
            readerForUpload.readAsDataURL(file);
        } catch (error) {
            this.showSwal({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao processar arquivo: ' + error.message,
            });
        }
    }

    /**
     * Carrega documento a partir de dados Base64
     * Usa o componente DomusDocumentViewer se disponível
     */
    loadDocumentFromData(fileData, fileName, mimeType = null) {
        if (!mimeType) {
            if (fileName.toLowerCase().endsWith('.pdf')) {
                mimeType = 'application/pdf';
            } else if (fileName.toLowerCase().match(/\.(png|jpg|jpeg|webp)$/)) {
                mimeType = 'image/' + fileName.split('.').pop().toLowerCase();
            }
        }

        // Se o DomusDocumentViewer estiver disponível, usar ele
        if (window.mainDocumentViewer) {
            // Criar objeto documento temporário para o viewer
            const tempDoc = {
                id: 'temp-' + Date.now(),
                nome_arquivo: fileName,
                mime_type: mimeType,
                base64_content: fileData.includes(',') ? fileData.split(',')[1] : fileData,
                file_url: null,
                caminho_arquivo: null
            };

            // Usar o viewer para carregar o documento
            window.mainDocumentViewer.load(tempDoc);
            return;
        }

        // Fallback para manipulação direta (caso o viewer não esteja disponível)
        if (this.elements.emptyState) {
            this.elements.emptyState.style.display = 'none';
        }

        if (this.elements.documentViewer) {
            this.elements.documentViewer.style.display = 'block';
        }
    }

    /**
     * Seleciona documento do banco de dados
     */
    async selectDocumentFromDatabase(documentId) {
        try {
            const url = this.config.routes.show.replace(':id', documentId);
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            this.validateJsonResponse(response);

            let result;
            try {
                result = await response.json();
            } catch (jsonError) {
                throw new Error(`Erro ao processar resposta do servidor: ${jsonError.message}`);
            }

            if (!response.ok) {
                throw new Error(result.message || `HTTP error! status: ${response.status}`);
            }

            if (result.success && result.data) {
                const doc = result.data;

                // Remover seleção anterior
                document.querySelectorAll('.pending-document-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Adicionar seleção atual
                const selectedItem = document.querySelector(`[data-document-id="${documentId}"]`);
                if (selectedItem) {
                    selectedItem.classList.add('active');
                }

                // Carregar documento no visualizador
                this.loadDocumentFromDatabase(doc);

                // Atualizar índice atual
                const index = this.documentList.findIndex(d => d.id === parseInt(documentId));
                if (index !== -1) {
                    this.currentDocumentIndex = index;
                    if (typeof window.updateDocumentPosition === 'function') {
                        window.updateDocumentPosition();
                    }
                    if (typeof window.updateThumbnails === 'function') {
                        window.updateThumbnails();
                    }

                    // Scroll thumbnail into view
                    setTimeout(() => {
                        const thumb = document.querySelector(
                            `.thumbnail-item[data-document-id="${documentId}"]`
                        );
                        if (thumb) {
                            thumb.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest',
                                inline: 'center'
                            });
                        }
                    }, 100);
                }
            } else {
                throw new Error('Documento não encontrado ou dados inválidos');
            }
        } catch (error) {
            console.error('Erro ao carregar documento:', error);
            this.showSwal({
                icon: 'error',
                title: 'Erro ao carregar documento',
                html: `
                    <div class="text-start">
                        <p><strong>Detalhes:</strong></p>
                        <p class="text-muted small">${error.message}</p>
                        <p class="text-muted fs-8 mt-2">Verifique o console para mais detalhes.</p>
                    </div>
                `,
            });
        }
    }

    /**
     * Carrega documento do banco no visualizador
     */
    loadDocumentFromDatabase(doc) {
        this.currentDocument = doc;

        // Esconder empty state e mostrar o viewer
        if (this.elements.emptyState) {
            this.elements.emptyState.style.display = 'none';
        }

        if (this.elements.documentViewer) {
            this.elements.documentViewer.style.display = 'block';
        }

        // Delegar para a classe DomusDocumentViewer
        if (window.mainDocumentViewer) {
            window.mainDocumentViewer.load(doc);
        }

        // Exibir dados extraídos se disponíveis
        if (doc.dados_extraidos) {
            try {
                const extractedData = typeof doc.dados_extraidos === 'string' ?
                    JSON.parse(doc.dados_extraidos) :
                    doc.dados_extraidos;
                if (typeof window.displayExtractedEntries === 'function') {
                    window.displayExtractedEntries(extractedData);
                }
            } catch (e) {
                console.error('Erro ao parsear dados extraídos:', e);
                if (typeof window.displayExtractedEntries === 'function') {
                    window.displayExtractedEntries(null);
                }
            }
        } else {
            if (typeof window.displayExtractedEntries === 'function') {
                window.displayExtractedEntries(null);
            }
        }

        // Sidebar scroll thumbnails
        setTimeout(() => {
            const thumb = document.querySelector(`.thumbnail-item[data-document-id="${doc.id}"]`);
            if (thumb) {
                thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }, 100);
    }

    /**
     * Navega entre documentos
     */
    navigateDocument(direction) {
        if (this.documentList.length === 0) return;

        let newIndex = this.currentDocumentIndex;

        if (direction === 'prev') {
            newIndex--;
            if (newIndex < 0) newIndex = this.documentList.length - 1;
        } else {
            newIndex++;
            if (newIndex >= this.documentList.length) newIndex = 0;
        }

        this.selectDocumentFromDatabase(this.documentList[newIndex].id);
    }

    /**
     * Lida com exclusão de documento
     */
    async handleDeleteDocument() {
        if (!this.currentDocument || !this.currentDocument.id) {
            this.showSwal({
                icon: 'warning',
                title: 'Nenhum documento selecionado',
                text: 'Selecione um documento para excluir.',
            });
            return;
        }

        const result = await this.showSwal({
            title: 'Excluir documento?',
            html: `
                <div class="text-start">
                    <p>Tem certeza que deseja excluir o documento:</p>
                    <p class="fw-bold text-gray-800">${this.currentDocument.nome_arquivo || 'Documento'}</p>
                    <p class="text-danger fs-7 mt-3">Esta ação não pode ser desfeita.</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        });

        if (result.isConfirmed) {
            await this.deleteDocument(this.currentDocument.id);
        }
    }

    /**
     * Exclui documento
     */
    async deleteDocument(id) {
        try {
            this.showSwal({
                title: 'Excluindo...',
                text: 'Por favor, aguarde.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const url = this.config.routes.destroy.replace(':id', id);
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            let responseData;
            try {
                responseData = await response.json();
            } catch (jsonError) {
                throw new Error('Erro ao processar resposta do servidor');
            }

            if (!response.ok) {
                throw new Error(responseData.message || 'Erro ao excluir documento');
            }

            this.showSwal({
                icon: 'success',
                title: 'Excluído!',
                text: 'O documento foi removido com sucesso.',
                timer: 2000,
                showConfirmButton: false
            });

            this.currentDocument = null;

            // Esconder o viewer e mostrar o empty state
            // O DomusDocumentViewer tem suas próprias referências aos elementos
            if (window.mainDocumentViewer) {
                if (window.mainDocumentViewer.container) {
                    window.mainDocumentViewer.container.style.display = 'none';
                }
                if (window.mainDocumentViewer.emptyState) {
                    window.mainDocumentViewer.emptyState.style.display = 'flex';
                }
            } else {
                // Fallback caso o viewer não esteja disponível
                if (this.elements.documentViewer) {
                    this.elements.documentViewer.style.display = 'none';
                }
                if (this.elements.emptyState) {
                    this.elements.emptyState.style.display = 'flex';
                }
            }

            if (this.elements.deleteDocumentBtn) this.elements.deleteDocumentBtn.style.display = 'none';
            if (this.elements.extractedEntriesCard) this.elements.extractedEntriesCard.style.display = 'none';

            document.querySelectorAll('.pending-document-item').forEach(item => {
                item.classList.remove('active');
            });

            await this.loadPendingDocuments();

        } catch (error) {
            console.error('Erro ao excluir documento:', error);
            this.showSwal({
                icon: 'error',
                title: 'Erro ao excluir',
                text: error.message || 'Ocorreu um erro ao excluir o documento. Tente novamente.',
            });
        }
    }

    /**
     * Lida com sessão expirada
     */
    handleSessionExpired() {
        this.showSwal({
            icon: 'warning',
            title: 'Sessão Expirada',
            text: 'Sua sessão expirou. Por favor, faça login novamente.',
            confirmButtonText: 'Fazer Login',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = this.config.routes.login;
            }
        });
    }

    /**
     * Wrapper para SweetAlert2
     */
    showSwal(options) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire(options);
        } else {
            return Promise.resolve({ isConfirmed: false });
        }
    }

    /**
     * Adiciona elemento de loading (deve ser implementado externamente)
     */
    addLoadingElement() {
        if (typeof window.addLoadingElement === 'function') {
            window.addLoadingElement();
        }
    }

    /**
     * Remove elemento de loading (deve ser implementado externamente)
     */
    removeLoadingElement() {
        if (typeof window.removeLoadingElement === 'function') {
            window.removeLoadingElement();
        }
    }
}

// Exportar para uso global
window.DomusiaPendentes = DomusiaPendentes;

// Função global para compatibilidade com código existente
window.handleFiles = function(files) {
    if (window.domusiaPendentesInstance) {
        window.domusiaPendentesInstance.handleFiles(files);
    }
};

window.selectDocumentFromDatabase = function(documentId) {
    if (window.domusiaPendentesInstance) {
        window.domusiaPendentesInstance.selectDocumentFromDatabase(documentId);
    }
};

window.loadPendingDocuments = function() {
    if (window.domusiaPendentesInstance) {
        window.domusiaPendentesInstance.loadPendingDocuments();
    }
};

// Função global para compatibilidade com o botão de excluir do document-viewer
window.confirmDeleteDocument = function(documentId) {
    if (!window.domusiaPendentesInstance) {
        console.error('DomusiaPendentes não está inicializado');
        return;
    }

    const instance = window.domusiaPendentesInstance;
    const currentDoc = instance.currentDocument;
    
    // Se o documento já está selecionado e o ID corresponde, apenas excluir
    if (currentDoc && currentDoc.id == documentId) {
        instance.handleDeleteDocument();
        return;
    }
    
    // Se não houver documento ou o ID for diferente, tentar usar o ID passado diretamente
    if (documentId && typeof documentId === 'number' || (typeof documentId === 'string' && !isNaN(documentId))) {
        // Verificar se o documento existe na lista
        const doc = instance.documentList.find(d => d.id == documentId);
        if (doc) {
            // Temporariamente definir como documento atual e excluir
            const originalDoc = instance.currentDocument;
            instance.currentDocument = doc;
            instance.handleDeleteDocument().finally(() => {
                // Restaurar documento original se houver
                if (!originalDoc && instance.currentDocument === doc) {
                    instance.currentDocument = null;
                }
            });
        } else {
            // Documento não encontrado na lista, mostrar erro
            instance.showSwal({
                icon: 'error',
                title: 'Erro',
                text: 'Documento não encontrado para exclusão.'
            });
        }
    } else {
        // Sem ID válido, tentar excluir documento atual se houver
        if (currentDoc) {
            instance.handleDeleteDocument();
        } else {
            instance.showSwal({
                icon: 'warning',
                title: 'Nenhum documento selecionado',
                text: 'Selecione um documento para excluir.'
            });
        }
    }
};
