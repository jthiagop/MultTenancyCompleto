// Script para gerenciar o estado do sidebar
(function() {
    'use strict';

    // Função para ler cookies
    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
        return null;
    }

    // Interceptar o salvamento do cookie do sidebar para garantir configurações corretas
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (typeof KTToggle !== 'undefined' && typeof KTCookie !== 'undefined') {
                var toggle = document.getElementById('kt_app_sidebar_toggle');
                if (toggle) {
                    var toggleObj = KTToggle.getInstance(toggle);
                    if (toggleObj) {
                        // Interceptar o evento kt.toggle.changed
                        toggleObj.on('kt.toggle.changed', function() {
                            var isEnabled = toggleObj.isEnabled();
                            var date = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000); // 30 days
                            var value = isEnabled ? "on" : "off";
                            
                            // Salvar cookie com SameSite=Lax explicitamente
                            var cookieString = "sidebar_minimize_state=" + encodeURIComponent(value) + 
                                "; expires=" + date.toUTCString() + 
                                "; path=/" + 
                                "; SameSite=Lax";
                            
                            document.cookie = cookieString;
                        });
                    }
                }
            }
        }, 500);
    });

    // Verificar se o estado foi restaurado corretamente (sem restaurar novamente para evitar flash)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var cookieValue = getCookie('sidebar_minimize_state');
            var bodyHasAttribute = document.body.hasAttribute('data-kt-app-sidebar-minimize');
            var toggle = document.getElementById('kt_app_sidebar_toggle');
            
            // Apenas corrigir se houver inconsistência (não restaurar se já estiver correto)
            if (cookieValue === 'on' && !bodyHasAttribute && toggle) {
                document.body.setAttribute('data-kt-app-sidebar-minimize', 'on');
                toggle.classList.add('active');
            } else if (cookieValue === 'off' && bodyHasAttribute && toggle) {
                document.body.removeAttribute('data-kt-app-sidebar-minimize');
                toggle.classList.remove('active');
            }
        }, 100);
    });
})();

