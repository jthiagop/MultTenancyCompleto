{{-- Incluindo assets e scripts --}}
@include('app.financeiro.components.assets')

<x-tenant-app-layout>
    {{-- *** Modal de Receitas e Despesas *** --}}
    @include('app.components.modals.financeiro.recitasDespesas.Dm_modal_financeiro')
    {{-- *** Fim Modal de Receitas e Despesas *** --}}
    
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
        @include('app.components.modals.lancar-caixa')
        @include('app.components.modals.lancar-banco')
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

{{-- Scripts JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2 com placeholder
        $('.lancamento_padrao_banco').select2({
            placeholder: "Selecione um lançamento padrão", // Texto do placeholder
            allowClear: true // Permite limpar a seleção
        });

        // Filtrar as opções de cada select, para mostrar só 'entrada' OU 'saida'
        $('.lancamento_padrao_banco').each(function() {
            const tipoLancamento = $(this).closest('.row').find('.tipo-lancamento').val();

            $(this).find('option').each(function() {
                // 'data-type' em cada <option> do Lançamento
                const optType = $(this).data('type');

                // Se não coincide com o tipo da linha (entrada vs. saída), removemos
                if (optType !== tipoLancamento && $(this).val() !== '') {
                    $(this).remove();
                }
            });
        });
    });
</script>