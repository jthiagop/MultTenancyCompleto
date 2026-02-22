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
                                        <i class="bi bi-plus fs-3"></i>
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
                        const planoContasBaseUrl = "{{ route('contabilidade.plano-contas.index') }}";
                        const tableDataUrl = "{{ route('contabilidade.plano-contas.table-data') }}";
                        const tableBody = document.querySelector('#plano_contas_tabela tbody');

                        // ─── Função global: recarrega apenas a tabela via AJAX ───
                        window.reloadPlanoContasTable = async function() {
                            try {
                                const response = await fetch(tableDataUrl, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });
                                const data = await response.json();

                                if (data.success) {
                                    // Atualiza o tbody da tabela
                                    tableBody.innerHTML = data.tbodyHtml;

                                    // Re-inicializa os menus do Metronic nas novas linhas
                                    if (typeof KTMenu !== 'undefined') {
                                        KTMenu.createInstances();
                                    }

                                    // Atualiza o Select2 do modal de criação/edição
                                    const parentSelect = document.getElementById('parent_id_select');
                                    if (parentSelect && data.accounts) {
                                        const currentVal = $(parentSelect).val();
                                        // Limpa opções existentes (mantém o placeholder vazio)
                                        parentSelect.innerHTML = '<option></option>';
                                        data.accounts.forEach(acc => {
                                            const opt = document.createElement('option');
                                            opt.value = acc.id;
                                            opt.textContent = `${acc.code} - ${acc.name}`;
                                            opt.setAttribute('data-type', acc.type);
                                            parentSelect.appendChild(opt);
                                        });
                                        // Restaura seleção se existia
                                        if (currentVal) {
                                            $(parentSelect).val(currentVal);
                                        }
                                        $(parentSelect).trigger('change');
                                    }
                                }
                            } catch (error) {
                                console.error('Erro ao recarregar tabela:', error);
                            }
                        };

                        // ─── Busca (funciona com event delegation — não precisa re-bind) ───
                        const searchInput = document.getElementById('search-plano-contas');
                        searchInput.addEventListener('input', function() {
                            const searchTerm = this.value.toLowerCase();
                            const rows = tableBody.querySelectorAll('tr');

                            rows.forEach(row => {
                                const contaName = row.querySelector('td:first-child span')?.textContent?.toLowerCase() || '';
                                const contaCode = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';

                                row.style.display = (contaName.includes(searchTerm) || contaCode.includes(searchTerm)) ? '' : 'none';
                            });
                        });

                        // ─── Event delegation: edição e exclusão (funciona p/ linhas novas) ───
                        document.getElementById('plano_contas_tabela').addEventListener('click', function(e) {
                            // Exclusão
                            const deleteBtn = e.target.closest('.delete-btn');
                            if (deleteBtn) {
                                e.preventDefault();
                                const contaId = deleteBtn.getAttribute('data-id');
                                const contaName = deleteBtn.getAttribute('data-name');

                                document.getElementById('conta-name').textContent = contaName;
                                document.getElementById('delete-plano-conta-form').action =
                                    `${planoContasBaseUrl}/${contaId}`;

                                const modal = new bootstrap.Modal(document.getElementById('kt_modal_delete_plano_conta'));
                                modal.show();
                                return;
                            }

                            // Edição
                            const editBtn = e.target.closest('.edit-btn');
                            if (editBtn) {
                                e.preventDefault();
                                const contaId = editBtn.getAttribute('data-id');

                                fetch(`${planoContasBaseUrl}/${contaId}/edit`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.editPlanoConta(data.conta);
                                    }
                                })
                                .catch(error => {
                                    console.error('Erro ao buscar dados da conta:', error);
                                    toastr.error('Erro ao carregar dados da conta para edição.');
                                });
                            }
                        });
                    });
                </script>
                <!--end::Scripts-->
