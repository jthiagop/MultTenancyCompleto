"use strict";

var KTFavorites = function() {
    var menuElement;
    var listElement;

    // Carregar módulos disponíveis
    var loadAvailableModules = function() {
        if (!listElement) return;
        
        listElement.innerHTML = '<div class="text-center py-5"><span class="spinner-border spinner-border-sm"></span> Carregando...</div>';
        
        fetch('/api/favorites/available', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.modules && data.modules.length > 0) {
                renderModules(data.modules);
            } else {
                listElement.innerHTML = '<div class="text-center text-muted py-5">Todos os módulos já estão nos favoritos</div>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar módulos:', error);
            listElement.innerHTML = '<div class="text-center text-danger py-5">Erro ao carregar módulos</div>';
        });
    };

    // Renderizar lista de módulos
    var renderModules = function(modules) {
        if (!listElement) return;
        
        let html = '';
        
        modules.forEach(module => {
            html += `
                <div class="menu-item px-3 mb-2">
                    <button type="button" class="btn btn-light-primary w-100 text-start d-flex align-items-center justify-content-between add-favorite-btn"
                            data-module-key="${module.key}">
                        <span class="d-flex align-items-center">
                            ${module.icon && module.icon.startsWith('/') 
                                ? `<img src="${module.icon}" alt="${module.name}" style="width: 24px; height: 24px;" class="me-3">`
                                : `<i class="fa-solid ${module.icon_class} fs-3 me-3"></i>`
                            }
                            <span>
                                <div class="fw-bold">${module.name}</div>
                                <div class="fs-7 text-muted">${module.description}</div>
                            </span>
                        </span>
                        <i class="fa-solid fa-plus fs-5"></i>
                    </button>
                </div>
            `;
        });
        
        listElement.innerHTML = html;
        
        // Adicionar event listeners
        document.querySelectorAll('.add-favorite-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                addFavorite(this.dataset.moduleKey);
            });
        });
    };

    // Adicionar favorito
    var addFavorite = function(moduleKey) {
        fetch('/api/favorites', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ module_key: moduleKey })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        text: "Módulo adicionado aos favoritos!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    alert('Módulo adicionado aos favoritos!');
                    location.reload();
                }
            } else {
                throw new Error(data.error || 'Erro ao adicionar favorito');
            }
        })
        .catch(error => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    text: error.message || "Erro ao adicionar aos favoritos!",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            } else {
                alert(error.message || "Erro ao adicionar aos favoritos!");
            }
        });
    };

    // Remover favorito
    var removeFavorite = function(favoriteId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: "Tem certeza que deseja remover este módulo dos favoritos?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, remover!",
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-light"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteFavorite(favoriteId);
                }
            });
        } else {
            if (confirm('Tem certeza que deseja remover este módulo dos favoritos?')) {
                deleteFavorite(favoriteId);
            }
        }
    };

    // Deletar favorito (chamada AJAX)
    var deleteFavorite = function(favoriteId) {
        fetch(`/api/favorites/${favoriteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erro ao remover favorito:', error);
        });
    };

    // Inicializar
    var init = function() {
        menuElement = document.getElementById('kt_add_favorite_menu');
        listElement = document.getElementById('available-modules-list');
        
        if (!menuElement || !listElement) {
            return;
        }

        // Carregar módulos quando o menu abrir
        var btnAddFavorite = document.getElementById('btn_add_favorite');
        if (btnAddFavorite) {
            btnAddFavorite.addEventListener('click', function() {
                loadAvailableModules();
            });
        }

        // Event listener para remover favoritos
        document.querySelectorAll('.remove-favorite').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                removeFavorite(this.dataset.favoriteId);
            });
        });
    };

    return {
        init: init
    };
}();

// Inicializar quando DOM estiver pronto
if (typeof KTUtil !== 'undefined') {
    KTUtil.onDOMContentLoaded(function() {
        KTFavorites.init();
    });
} else {
    // Fallback se KTUtil não estiver disponível
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            KTFavorites.init();
        });
    } else {
        KTFavorites.init();
    }
}

