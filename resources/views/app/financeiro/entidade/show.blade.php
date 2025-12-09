{{-- CSS e Scripts Externos --}}
<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>

<x-tenant-app-layout>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            {{-- Header da Página --}}
            @include('app.financeiro.entidade.partials.header')

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-fluid">
                    {{-- Mensagens de Sistema --}}
                    @include('app.financeiro.entidade.partials.alerts')

                    {{-- Navegação por Abas --}}
                    @include('app.financeiro.entidade.partials.tabs')

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="myTabContent">
                        {{-- Aba de Movimentação --}}
                        @include('app.financeiro.entidade.partials.movimentacao')

                        {{-- Aba de Conciliações Pendentes --}}
                        @include('app.financeiro.entidade.partials.conciliacoes')

                    {{-- Aba de Informações --}}
                    @include('app.financeiro.entidade.partials.informacoes')
                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
</div>

{{-- Modal de Conciliação de Missas --}}
@include('app.components.modals.financeiro.conciliacao-missas')
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

{{-- Script de Conciliação de Missas --}}
<script src="/assets/js/custom/apps/financeiro/conciliacao-missas.js"></script>
