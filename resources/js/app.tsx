/**
 * Arquivo principal de inicialização
 * 
 * Este arquivo é responsável por inicializar todos os componentes,
 * layouts e páginas da aplicação.
 */

import './bootstrap';
import Alpine from 'alpinejs';

// Tornar Alpine disponível globalmente
window.Alpine = Alpine;

// Inicializar Alpine
Alpine.start();

// Importar componentes
import './Components';
import './Layouts';
import './Pages';

// DARK MODE TOGGLE
const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
const themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");

if (
    localStorage.getItem("color-theme") === "dark" ||
    (!("color-theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
    themeToggleLightIcon?.classList.remove("hidden");
} else {
    themeToggleDarkIcon?.classList.remove("hidden");
}

const themeToggleBtn = document.getElementById("theme-toggle");

themeToggleBtn?.addEventListener("click", function () {
    themeToggleDarkIcon?.classList.toggle("hidden");
    themeToggleLightIcon?.classList.toggle("hidden");

    if (localStorage.getItem("color-theme")) {
        if (localStorage.getItem("color-theme") === "light") {
            document.documentElement.classList.add("dark");
            localStorage.setItem("color-theme", "dark");
        } else {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("color-theme", "light");
        }
    } else {
        if (document.documentElement.classList.contains("dark")) {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("color-theme", "light");
        } else {
            document.documentElement.classList.add("dark");
            localStorage.setItem("color-theme", "dark");
        }
    }
});

export default {};
