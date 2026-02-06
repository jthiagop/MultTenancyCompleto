<x-tenant-drawer
    drawerId="kt_drawer_lancamento"
    title="Novo Lançamento"
    width="100%"
    toggleButtonId="kt_drawer_lancamento_button"
    closeButtonId="kt_drawer_lancamento_close">

    <x-slot name="body">
        <form id="kt_drawer_lancamento_form" class="form" action="{{ route('banco.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('app.components.modals.financeiro.lancamento.components.form-header')

            @include('app.components.modals.financeiro.lancamento.components.card-informacoes-lancamento', [
                'dropdownParent' => '#kt_drawer_lancamento',
                'centrosAtivos' => $centrosAtivos ?? collect(),
                'lps' => $lps ?? collect(),
                'formasPagamento' => $formasPagamento ?? collect(),
                'fornecedores' => $fornecedores ?? collect(),
                'entidadesBanco' => $entidadesBanco ?? collect(),
                'entidadesCaixa' => $entidadesCaixa ?? collect(),
            ])

            @include('app.components.modals.financeiro.lancamento.components.card-condicao-pagamento', ['dropdownParent' => '#kt_drawer_lancamento'])

            @include('app.components.modals.financeiro.lancamento.components.card-parcelas-readonly')

            @include('app.components.modals.financeiro.lancamento.components.tabs-historico-anexos')
        </form>
    </x-slot>

    <x-slot name="footer">
        @include('app.components.modals.financeiro.lancamento.components.modal-footer')
    </x-slot>
</x-tenant-drawer>

@push('styles')
    @include('app.components.drawers.styles.drawer-select2')
@endpush

<!--begin::Drawer - Novo Fornecedor-->
@include('app.components.modals.financeiro.lancamento.drawer_fornecedor')
<!--end::Drawer - Novo Fornecedor-->

<!--begin::Drawer - Configuração de Recorrência-->
@include('app.components.modals.financeiro.lancamento.drawer_recorrencia')
<!--end::Drawer - Configuração de Recorrência-->

{{-- Template para linhas de parcelas --}}
@include('app.components.drawers.templates.parcela-row-template')

{{-- Template para linhas do resumo da baixa --}}
@include('app.components.drawers.templates.resumo-baixa-row-template')

@push('scripts')
    {{-- Módulo compilado do Drawer (ES6) - Futuro --}}
    {{-- @vite('resources/js/financeiro/drawer/index.js') --}}
    
    {{-- Scripts legados (mantidos para compatibilidade durante migração) --}}
    @include('app.components.drawers.scripts.drawer-init')
    @include('app.components.drawers.scripts.drawer-suggestions')
    @include('app.components.drawers.scripts.drawer-pagamento-parcelas')
    @include('app.components.drawers.scripts.drawer-form-submit')
    @include('app.components.drawers.scripts.drawer-dynamic-labels')
@endpush

