<style>
/* Garante que o Select2 dropdown apareça acima do drawer overlay */
.select2-container--open {
    z-index: 10000 !important;
}

.select2-dropdown {
    z-index: 10001 !important;
}

/* Garante que o dropdown do Select2 dentro do drawer tenha z-index alto */
#kt_drawer_lancamento .select2-container--open .select2-dropdown {
    z-index: 10001 !important;
}

/* Ajusta o backdrop do Select2 se necessário */
.select2-container--open .select2-search--dropdown {
    z-index: 10002 !important;
}

/* Estilos para as estrelas de sugestão */
.suggestion-star-wrapper {
    transition: opacity 0.2s ease-in-out;
}

/* Garante que a estrela não interfira com dropdowns do Select2 */
.suggestion-star-wrapper {
    z-index: 3 !important;
}

/* Quando o Select2 estiver aberto, reduz o z-index da estrela */
.select2-container--open + .suggestion-star-wrapper {
    z-index: 1 !important;
}

/* Ajustes de posicionamento para diferentes cenários */
.input-group .select2-container + .suggestion-star-wrapper {
    right: 50px; /* Posição padrão: à esquerda do X e chevron */
}

/* Para Select2 com allowClear (tem X) - estrela fica mais à esquerda */
.input-group .select2-container .select2-selection--single .select2-selection__clear + .select2-selection__arrow {
    right: 20px; /* Ajusta posição do chevron quando há clear button */
}

/* Para Select2 sem allowClear (sem X) - estrela fica mais próxima do chevron */
.input-group .select2-container:not([data-allow-clear="true"]) + .suggestion-star-wrapper {
    right: 30px;
}

/* Hover state para melhor UX */
.suggestion-star-wrapper:hover {
    opacity: 0.8;
}
</style>
