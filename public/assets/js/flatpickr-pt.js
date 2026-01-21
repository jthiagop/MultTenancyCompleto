/* Portuguese locale for flatpickr */
(function () {
    'use strict';

    var Portuguese = {
        weekdays: {
            shorthand: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
            longhand: [
                "Domingo",
                "Segunda-feira",
                "Terça-feira",
                "Quarta-feira",
                "Quinta-feira",
                "Sexta-feira",
                "Sábado"
            ]
        },

        months: {
            shorthand: [
                "Jan",
                "Fev",
                "Mar",
                "Abr",
                "Mai",
                "Jun",
                "Jul",
                "Ago",
                "Set",
                "Out",
                "Nov",
                "Dez"
            ],
            longhand: [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abril",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
            ]
        },

        rangeSeparator: " até ",
        time_24hr: true
    };

    // Função para registrar o locale
    function registerLocale() {
        var registered = false;

        // Verifica múltiplas formas de acesso ao flatpickr
        var fp = null;
        if (typeof flatpickr !== "undefined") {
            fp = flatpickr;
        } else if (typeof window !== "undefined" && window.flatpickr) {
            fp = window.flatpickr;
        } else if (typeof jQuery !== "undefined" && jQuery.fn && jQuery.fn.flatpickr) {
            // Se flatpickr está disponível via jQuery, tenta acessar via jQuery
            fp = jQuery.fn.flatpickr.flatpickr || (typeof flatpickr !== "undefined" ? flatpickr : null);
        }

        if (fp) {
            if (!fp.l10ns) {
                fp.l10ns = {};
            }
            fp.l10ns.pt = Portuguese;
            fp.l10ns.pt_BR = Portuguese; // Alias
            fp.l10ns['pt-BR'] = Portuguese; // Alias alternativo

            // Também registra no objeto global se diferente
            if (typeof flatpickr !== "undefined" && flatpickr !== fp) {
                if (!flatpickr.l10ns) {
                    flatpickr.l10ns = {};
                }
                flatpickr.l10ns.pt = Portuguese;
                flatpickr.l10ns.pt_BR = Portuguese;
                flatpickr.l10ns['pt-BR'] = Portuguese;
            }

            registered = true;
        }

        return registered;
    }

    // Função que tenta registrar o locale até ter sucesso
    function tryRegisterLocale(attempts, maxAttempts) {
        attempts = attempts || 0;
        maxAttempts = maxAttempts || 50; // Tenta até 50 vezes (5 segundos)

        if (registerLocale()) {
            console.log('[Flatpickr PT] Locale registered successfully');
            return;
        }

        if (attempts < maxAttempts) {
            setTimeout(function() {
                tryRegisterLocale(attempts + 1, maxAttempts);
            }, 100);
        } else {
            console.warn('[Flatpickr PT] Failed to register locale after', maxAttempts, 'attempts');
        }
    }

    // Tenta registrar imediatamente
    if (!registerLocale()) {
        // Se não conseguiu, tenta novamente após um delay
        setTimeout(tryRegisterLocale, 100);
    }

    // Também registra quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            tryRegisterLocale();
        });
    } else {
        // DOM já está pronto, tenta registrar novamente
        setTimeout(tryRegisterLocale, 100);
    }

    // Registra quando window.onload for chamado (garantia extra)
    if (typeof window !== "undefined") {
        window.addEventListener('load', function() {
            tryRegisterLocale();
        });
    }
})();
