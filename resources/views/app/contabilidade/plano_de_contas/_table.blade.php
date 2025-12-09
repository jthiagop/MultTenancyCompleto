                <!--begin::Card-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header pt-8">
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <i class="bi bi-search"></i>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" id="search-plano-contas"
                                    class="form-control form-control-solid w-250px ps-15"
                                    placeholder="Buscar contas contábeis..." />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-filemanager-table-toolbar="base">
                                <!--begin::Export-->
                                <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal"
                                    data-bs-target="#kt_subscriptions_export_modal">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <i class="bi bi-box-arrow-up fs-3"></i>
                                    </span>
                                    <!--end::Svg Icon-->Exportar</button>
                                <!--end::Export-->
                                <!--begin::Import-->
                                <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_import_plano_contas">
                                    <!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <i class="bi bi-upload"></i>
                                    </span>
                                    <span class="text-nowrap">Importar Plano de Contas</span>
                                </button>
                                <!--end::Import-->
                                <!--begin::Add customer-->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_new_account">
                                    <!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <i class="bi bi-plus"></i>
                                    </span>
                                    <span class="text-nowrap">Nova Conta Contábil</span>
                                </button>
                                <!--end::Add customer-->
                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Table-->
                        <table id="plano_contas_tabela" class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-250px">Nome da Conta</th>
                                    <th class="min-w-150px">Código</th>
                                    <th class="min-w-125px">Tipo</th>
                                    <th class="w-125px">Ações</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-semibold text-gray-600">
                                @foreach ($rootAccounts as $conta)
                                    @include('app.contabilidade.plano_de_contas._conta_linha', [
                                        'conta' => $conta,
                                        'allGroupedAccounts' => $allGroupedAccounts,
                                        'level' => 0,
                                    ])
                                @endforeach
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->

                <!--begin::Modal de Importação-->
                @include('app.components.modals.contabilidade.import-plano-conta')
                <!--end::Modal-->

                <!--begin::Modal de Exportação-->
                @include('app.components.modals.contabilidade.export-plano-conta')
                <!--end::Modal-->

                <!--begin::Modal de Exclusão-->
                @include('app.components.modals.contabilidade.delete-plano-conta')
                <!--end::Modal-->

                <!--begin::Scripts-->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Funcionalidade de busca
                        const searchInput = document.getElementById('search-plano-contas');
                        const tableRows = document.querySelectorAll('#plano_contas_tabela tbody tr');

                        searchInput.addEventListener('input', function() {
                            const searchTerm = this.value.toLowerCase();

                            tableRows.forEach(row => {
                                const contaName = row.querySelector('td:first-child span').textContent
                                    .toLowerCase();
                                const contaCode = row.querySelector('td:nth-child(2)').textContent
                            .toLowerCase();

                                if (contaName.includes(searchTerm) || contaCode.includes(searchTerm)) {
                                    row.style.display = '';
                                } else {
                                    row.style.display = 'none';
                                }
                            });
                        });

                        // Funcionalidade de exclusão
                        document.querySelectorAll('.delete-btn').forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();

                                const contaId = this.getAttribute('data-id');
                                const contaName = this.getAttribute('data-name');

                                // Atualiza o modal
                                document.getElementById('conta-name').textContent = contaName;
                                document.getElementById('delete-plano-conta-form').action =
                                    `/contabilidade/plano-contas/${contaId}`;

                                // Abre o modal
                                const modal = new bootstrap.Modal(document.getElementById(
                                    'kt_modal_delete_plano_conta'));
                                modal.show();
                            });
                        });

                        // Funcionalidade de edição
                        document.querySelectorAll('.edit-btn').forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();

                                const contaId = this.getAttribute('data-id');

                                // Busca os dados da conta via AJAX
                                fetch(`/contabilidade/plano-contas/${contaId}/edit`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            // Usa a função global para edição
                                            window.editPlanoConta(data.conta);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Erro ao buscar dados da conta:', error);
                                        Swal.fire({
                                            text: "Erro ao carregar dados da conta para edição.",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        });
                                    });
                            });
                        });
                    });
                </script>
                <!--end::Scripts-->
