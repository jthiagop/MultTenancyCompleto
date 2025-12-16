{{-- Incluindo assets e scripts --}}
@include('app.financeiro.components.assets')

<x-tenant-app-layout>
    {{-- *** Modal de Receitas e Despesas *** --}}
    @include('app.components.modals.financeiro.recitasDespesas.Dm_modal_financeiro')
    {{-- *** Fim Modal de Receitas e Despesas *** --}}

    {{-- *** Modal Prestação de Contas *** --}}
    @include('app.components.modals.financeiro.prestacao_contas.modal_prestacao_contas')
    {{-- *** Fim Modal Prestação de Contas *** --}}

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">

            {{-- Header/Toolbar --}}
            @include('app.financeiro.components.header')

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">

                    {{-- Módulos Financeiros --}}
                    @include('app.financeiro.components.financial-modules')

                </div>
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->

        <!--begin::Modals-->
        @include('app.components.modals.financeiro.lancamento.modal_lacamento')
        <!--end::Modals-->
    </div>
    <!--end::Main-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Financial Overview-->
            <div class="card card-flush">

                {{-- Tabs Navigation --}}
                @include('app.financeiro.components.tabs-navigation')

                <!--begin::Tab Content-->
                <div class="tab-content">

                    {{-- Receitas Container --}}
                    @include('app.financeiro.components.receitas-container')

                    {{-- Despesas Container --}}
                    @include('app.financeiro.components.despesas-container')

                </div>
                <!--end::Tab Content-->
            </div>
            <!--end::Financial Overview-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</x-tenant-app-layout>