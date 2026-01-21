<x-tenant-app-layout
    pageTitle="Domus IA"
    :breadcrumbs="array(
        array('label' => 'Financeiro', 'url' => route('caixa.index')),
        array('label' => 'Domus IA')
    )">
    <!-- Modal de Importação OFX -->
    @include('app.financeiro.banco.components.import-ofx-modal')

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">

            <!-- Cabeçalho da Página -->
            @include('app.financeiro.banco.components.header')

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!-- Mensagens de Alerta -->
                    @include('app.financeiro.banco.components.alerts')

                    <!-- Card de Informações do Banco -->
                    @include('app.financeiro.banco.components.bank-info-card')

                    <!-- Navegação por Abas -->
                    @include('app.financeiro.banco.components.tabs-navigation')

                    <!-- Conteúdo das Abas -->
                    @include('app.financeiro.banco.components.tab-content')
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->

    <!-- Assets (CSS e JavaScript) -->
    @include('app.financeiro.banco.components.assets')
</x-tenant-app-layout>
