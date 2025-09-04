                <!--begin::Card-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header pt-8">
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                            rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="currentColor" />
                                    </svg>
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
                                <!--begin::Add customer-->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_new_account">
                                    <!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
                                    <i class="bi bi-plus"></i>Nova Conta Contábil</button>
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
                            const contaName = row.querySelector('td:first-child span').textContent.toLowerCase();
                            const contaCode = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                            
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
                            document.getElementById('delete-plano-conta-form').action = `/contabilidade/plano-contas/${contaId}`;
                            
                            // Abre o modal
                            const modal = new bootstrap.Modal(document.getElementById('kt_modal_delete_plano_conta'));
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
