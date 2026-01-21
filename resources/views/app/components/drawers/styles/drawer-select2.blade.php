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
</style>
