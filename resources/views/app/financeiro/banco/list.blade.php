<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<x-tenant-app-layout pageTitle="Lançamentos Financeiros - Banco, Caixa" :breadcrumbs="[['label' => 'Lançamentos Financeiros']]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                </div>
                <!--end::Page title-->

            </div>
            <!--end::Toolbar container-->


        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">

            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Navbar-->
                @include('app.financeiro.banco.components.main-card')
                <!--end::Navbar-->

                @includeIf("app.financeiro.banco.tabs.{$activeTab}")
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>


    <!--begin::Modal - Boletim Financeiro-->
    @include('app.components.modals.financeiro.boletim.modal_boletim_financeiro')
    <!--end::Modal - Boletim Financeiro-->

    <!--begin::Modal - Prestação de Contas-->
    @include('app.components.modals.financeiro.prestacao_contas.modal_prestacao_contas')
    <!--end::Modal - Prestação de Contas-->

    <!--begin::Modal - Conciliação Bancária-->
    @include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
    <!--end::Modal - Conciliação Bancária-->

    <!--end::Modal - Upgrade plan-->

    {{-- Drawers e Modals que dependem do jQuery --}}
    @include('app.components.drawers.transacao_detalhes')
    @include('app.components.drawers.lancamento')
    @include('app.components.modals.financeiro.recibo.modal_gerar_recibo_ajax')
    @include('app.components.modals.financeiro.modal-delete-recurrence')

    @push('scripts')
        <!--begin::DominusEvents - Sistema de eventos global-->
        @include('components.scripts.dominus-events')
        <!--end::DominusEvents-->

        <!--begin::Event Listeners para atualização de componentes-->
        @include('app.financeiro.banco.scripts.banco-event-listeners')
        <!--end::Event Listeners-->

        <script>
            var lpsData = @json($lps);
            // Gerar apenas o caminho da rota (sem domínio)
            var bancoFluxoChartDataUrl = '{{ route('banco.fluxo.chart.data', [], false) }}';
            var bancoTransacoesDataUrl = '{{ route('banco.transacoes.data', [], false) }}';
        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!--begin::Vendors Javascript(used for this page only)-->
        <script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>
        <!--end::Vendors Javascript-->

        <script src="/tenancy/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>

        <!--begin::Custom Javascript(used for this page only)-->
        <script src="{{ url('/tenancy/assets/js/custom/apps/subscriptions/list/list.js') }}"></script>

        <!--end::Custom Javascript chats-->
        <script src="/tenancy/assets/js/custom/apps/bancos/shipping.js"></script>
        <!--end::Custom Javascript chats bancos-->

        <script src="/tenancy/assets/js/custom/apps/bancos/widgets.bundle.js"></script>


        <script src="/tenancy/assets/js/custom/apps/bancos/fluxo-banco-chart.js"></script>
        <!--end::Custom Javascript-->

        <script src="/tenancy/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
        <script src="/tenancy/assets/js/custom/utilities/modals/create-campaign.js"></script>
        <script src="/tenancy/assets/js/custom/utilities/modals/users-search.js"></script>

        <script src="/tenancy/assets/js/custom/utilities/modals/company/prestacaoConta.js"></script>
        <script src="/tenancy/assets/js/custom/utilities/modals/boletim-financeiro.js"></script>
        <script src="/tenancy/assets/js/custom/utilities/modals/conciliacao-bancaria.js"></script>

        <script>
            // Função para excluir transação diretamente do dropdown (sem abrir drawer)
            function excluirTransacaoDirecta(deleteUrl) {
                Swal.fire({
                    title: 'Excluir Transação?',
                    html: `
                        <p>Esta ação irá:</p>
                        <ul class="text-start">
                            <li>Deletar a transação financeira</li>
                            <li>Deletar a movimentação relacionada</li>
                            <li>Reverter o saldo da entidade</li>
                            <li>Desfazer conciliação (se aplicável)</li>
                        </ul>
                        <p class="text-danger fw-bold mt-3">Esta ação não pode ser desfeita!</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: 'Sim, excluir',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading
                        Swal.fire({
                            title: 'Processando...',
                            text: 'Excluindo transação',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Fazer requisição DELETE
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Excluída!',
                                    text: data.message || 'Transação excluída com sucesso.',
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(() => {
                                    // Recarregar a página para atualizar a lista
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Erro!',
                                    text: data.message || 'Erro ao excluir transação.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao excluir transação:', error);
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Erro ao excluir transação. Tente novamente.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        });
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const deleteLinks = document.querySelectorAll('.delete-link');

                deleteLinks.forEach(link => {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const id = this.getAttribute('data-id');
                        const form = document.getElementById(`delete-form-${id}`);
                        Swal.fire({
                            title: 'Você tem certeza?',
                            text: 'Esta ação não pode ser desfeita!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sim, exclua!',
                            cancelButtonText: 'Não, cancele',
                            customClass: {
                                confirmButton: 'btn btn-danger',
                                cancelButton: 'btn btn-secondary'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-tenant-app-layout>
