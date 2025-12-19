<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Relatório PDF')</title>
    
    <style>
        /* ==================== RESET E BÁSICOS ==================== */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            font-size: .75rem; 
            line-height: 1.5; 
            color: #212529; 
        }
        
        /* ==================== UTILITÁRIOS BOOTSTRAP ==================== */
        .container { width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; }
        .row { display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; }
        .col, .col-auto, .col-6, .col-12 { position: relative; width: 100%; padding-right: 15px; padding-left: 15px; }
        .col { flex-basis: 0; flex-grow: 1; max-width: 100%; }
        .col-auto { flex: 0 0 auto; width: auto; max-width: 100%; }
        .col-6 { flex: 0 0 50%; max-width: 50%; }
        .col-12 { flex: 0 0 100%; max-width: 100%; }
        
        /* Alinhamento de texto */
        .text-center { text-align: center !important; }
        .text-end { text-align: right !important; }
        .text-start { text-align: left !important; }
        .text-muted { color: #6c757d !important; }
        
        /* Peso de fonte */
        .fw-bold { font-weight: 700 !important; }
        .fw-normal { font-weight: 400 !important; }
        
        /* Margens */
        .m-0 { margin: 0 !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .mb-4 { margin-bottom: 1.5rem !important; }
        .mt-2 { margin-top: 0.5rem !important; }
        .mt-3 { margin-top: 1rem !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        
        /* Tamanhos de texto */
        .small { font-size: 0.875em; }
        .h1, .h2, .h3, .h4, .h5, .h6 { margin-bottom: 0.5rem; font-weight: 500; line-height: 1.2; }
        .h1 { font-size: 2rem; }
        .h2 { font-size: 1.5rem; }
        .h3 { font-size: 1.25rem; }
        .h4 { font-size: 1rem; }
        .h5 { font-size: 0.875rem; }
        .h6 { font-size: 0.75rem; }
        
        /* ==================== TABELAS ==================== */
        table { width: 100%; border-collapse: collapse; }
        .table { width: 100%; margin-bottom: 1rem; color: #212529; }
        .table th, .table td { padding: 0.5rem; vertical-align: top; border-top: 1px solid #dee2e6; }
        .table thead th { vertical-align: bottom; border-bottom: 2px solid #dee2e6; font-weight: 700; }
        .table tbody + tbody { border-top: 2px solid #dee2e6; }
        .table-sm th, .table-sm td { padding: 0.3rem; }
        .table-bordered { border: 1px solid #dee2e6; }
        .table-bordered th, .table-bordered td { border: 1px solid #dee2e6; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0,0,0,.05); }
        
        /* ==================== BADGES ==================== */
        .badge { 
            display: inline-block; 
            padding: 0.25em 0.4em; 
            font-size: 75%; 
            font-weight: 700; 
            line-height: 1; 
            text-align: center; 
            white-space: nowrap; 
            vertical-align: baseline; 
            border-radius: 0.25rem; 
        }
        .badge-success { background-color: #198754; color: white; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #0dcaf0; color: #000; }
        .badge-secondary { background-color: #6c757d; color: white; }
        
        /* ==================== CARDS ==================== */
        .card { position: relative; display: flex; flex-direction: column; min-width: 0; word-wrap: break-word; background-color: #fff; border: 1px solid rgba(0,0,0,.125); border-radius: 0.25rem; }
        .card-body { flex: 1 1 auto; padding: 1rem; }
        .card-header { padding: 0.5rem 1rem; margin-bottom: 0; background-color: rgba(0,0,0,.03); border-bottom: 1px solid rgba(0,0,0,.125); }
        
        /* ==================== PÁGINA ==================== */
        @page { 
            size: @yield('page-size', 'A4 portrait'); 
            margin: @yield('page-margin', '8mm 8mm 10mm 8mm'); 
        }
        
        .page-break { page-break-after: always; }
        
        /* ==================== ESTILOS CUSTOMIZADOS ==================== */
        @yield('custom-styles')
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
