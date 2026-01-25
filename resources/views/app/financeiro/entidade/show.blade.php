<x-tenant-app-layout :page-title="$entidade->nome" :breadcrumbs="array(
    array('label' => 'Financeiro', 'url' => route('banco.list')),
    array('label' => 'Conciliação Bancária')
)">
    <!--begin::Toolbar-->
    @include('app.financeiro.entidade.partials.header')
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid">
            {{-- Mensagens de Sistema --}}
            @include('app.financeiro.entidade.partials.alerts')

                    {{-- Navegação por Abas --}}
                    @include('app.financeiro.entidade.partials.tabs')

                    {{-- Conteúdo da Aba Ativa --}}
                    @if(($activeTab ?? 'conciliacoes') === 'conciliacoes')
                        {{-- Aba de Conciliações Pendentes --}}
                        @include('app.financeiro.entidade.partials.conciliacoes')
                    @elseif($activeTab === 'movimentacoes')
                        {{-- Aba de Movimentação --}}
                        @include('app.financeiro.entidade.partials.movimentacao')
                    @elseif($activeTab === 'informacoes')
                        {{-- Aba de Informações --}}
                        @include('app.financeiro.entidade.partials.informacoes')
                    @elseif($activeTab === 'historico')
                        {{-- Aba de Histórico de Conciliações --}}
                        @include('app.financeiro.entidade.partials.historico')
                    @endif
            </div>
            <!--end::Content container-->
        </div>
    <!--end::Content-->

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
