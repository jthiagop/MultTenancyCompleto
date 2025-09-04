<!-- CSS do Kendo (tema) -->
<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />

<!-- jQuery (obrigatório) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Kendo UI (JS principal) -->
<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>



<x-tenant-app-layout>

    <!-- Modal -->
    @include('app.financeiro.banco.components.modal')

    <!-- Estilos -->
    @include('app.financeiro.banco.components.styles')

    <!-- Script -->
    @include('app.financeiro.banco.components.modal-script')
    
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            @include('app.financeiro.banco.components.header')
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!-- Mensagens de Alerta -->
                    @include('app.financeiro.banco.components.alerts')

                    <!--begin::Navbar-->
                    <div class="row no-gutters">
                        <!-- Card Principal -->
                        @include('app.financeiro.banco.components.main-card')
                        
                        <!-- Card Lateral -->
                        @include('app.financeiro.banco.components.side-card')
                    </div>
                    <!--end::Navbar-->
                    
                    <!-- Conteúdo das Abas -->
                    @include('app.financeiro.banco.components.tab-content')

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    
    <!-- Assets (CSS e JavaScript) -->
    @include('app.financeiro.banco.components.assets')
</x-tenant-app-layout>
