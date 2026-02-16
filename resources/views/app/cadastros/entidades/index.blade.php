<x-tenant-app-layout pageTitle="Cadastro de Entidade Financeira" :breadcrumbs="[['label' => 'Financeiro', 'url' => route('banco.list')], ['label' => 'Entidades Financeiras']]">

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid mt-5" >
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid">
            <!--begin::Modal - Cadastro de Entidade Financeira-->
            @include('app.components.modals.financeiro.entidade')
            <!--end::Modal-->

            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12 mb-5 mb-xl-10">
                    <!--begin::Engage widget 1-->
                    <div class="card h-md-100" dir="ltr">
                        <div class="card">
                            <!--begin::Products-->
                            <div class="card card-flush">
                                <!--begin::Card header-->
                                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            Entidades Financeiras
                                        </div>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Actions-->
                                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                                        <div class="m-0">
                                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_entidade_financeira">
                                                <i class="bi bi-plus-lg fs-2"></i>
                                                Cadastrar Nova
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_ecommerce_report_sales_table">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-150px">Nome</th>
                                                <th class="text-end min-w-175px">Saldo Inicial</th>
                                                <th class="text-end min-w-150px">Ultima Atualização</th>
                                                <th class="text-end min-w-150px">Saldo Atual</th>
                                                <th class="text-end min-w-100px">Tipo</th>
                                                <th class="text-end min-w-150px">Conta Contábil</th>
                                                <th class="text-end min-w-150px">Descrição</th>
                                                @if (auth()->user()->hasRole(['admin', 'global']))
                                                    <th class="text-end min-w-100px">Ações</th>
                                                @endif
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">
                                            @forelse($entidades as $entidade)
                                                <tr>
                                                    <!-- Nome -->
                                                    <td>{{ $entidade->nome }}</td>
                                                    <!-- Saldo Inicial -->
                                                    <td class="text-end pe-0">R$
                                                        {{ number_format($entidade->saldo_inicial_real, 2, ',', '.') }}
                                                    </td>
                                                    <!-- Saldo Inicial -->
                                                    <td class="text-end pe-0">
                                                        {{ $entidade->updated_at->format('d/m/Y H:i') }}
                                                    </td>
                                                    <!-- Saldo Atual -->
                                                    <td
                                                        class="text-end pe-0 {{ $entidade->saldo_atual >= 0 ? 'text-success' : 'text-danger' }}">
                                                        R$
                                                        {{ number_format($entidade->saldo_atual, 2, ',', '.') }}
                                                    </td>
                                                    <!-- Tipo -->
                                                    <td class="text-end pe-0">{{ ucfirst($entidade->tipo) }}
                                                    </td>
                                                    <!-- Conta Contábil -->
                                                    <td class="text-end pe-0">
                                                        @if ($entidade->contaContabil)
                                                            <span
                                                                class="text-gray-800">{{ $entidade->contaContabil->code }}
                                                                - {{ $entidade->contaContabil->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <!-- Descrição -->
                                                    <td class="text-end">{{ $entidade->descricao ?? '-' }}
                                                    </td>
                                                    @if (auth()->user()->hasRole(['admin', 'global', 'admin_user']))
                                                        <!-- Ações -->
                                                        <td class="text-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-icon btn-light-primary btn-edit-entidade"
                                                                data-entidade-id="{{ $entidade->id }}"
                                                                data-entidade-tipo="{{ $entidade->tipo }}"
                                                                data-entidade-nome="{{ htmlspecialchars($entidade->nome, ENT_QUOTES, 'UTF-8') }}"
                                                                data-entidade-banco-id="{{ $entidade->banco_id ?? '' }}"
                                                                data-entidade-agencia="{{ $entidade->agencia ?? '' }}"
                                                                data-entidade-conta="{{ $entidade->conta ?? '' }}"
                                                                data-entidade-account-type="{{ $entidade->account_type ?? '' }}"
                                                                data-entidade-descricao="{{ htmlspecialchars($entidade->descricao ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                                data-entidade-conta-contabil-id="{{ $entidade->conta_contabil_id ?? '' }}"
                                                                data-bs-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->

                            </div>
                            <!--end::Products-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Engage widget 1-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Drawer de Edição-->
            @if (auth()->user()->hasRole(['admin', 'global']))
                <div id="kt_drawer_edit_entidade" class="bg-white" data-kt-drawer="true"
                    data-kt-drawer-name="edit_entidade" data-kt-drawer-activate="true"
                    data-kt-drawer-toggle="#kt_drawer_edit_entidade_toggle"
                    data-kt-drawer-close="#kt_drawer_edit_entidade_close" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'300px', 'md': '500px', 'lg': '600px'}"
                    data-kt-drawer-direction="end">
                    <!--begin::Card-->
                    <div class="card rounded-0 w-100">
                        <!--begin::Card header-->
                        <div class="card-header pe-5">
                            <!--begin::Title-->
                            <div class="card-title">
                                <h2 class="fw-bold">Editar Entidade Financeira</h2>
                            </div>
                            <!--end::Title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Close-->
                                <div class="btn btn-sm btn-icon btn-active-light-primary"
                                    id="kt_drawer_edit_entidade_close">
                                    <i class="fas fa-times fs-1"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body hover-scroll-overlay-y py-10 px-7">
                            <form id="kt_drawer_edit_entidade_form" method="POST" action="">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="entidade_id" id="edit_entidade_id">

                                <!-- Tipo (readonly) -->
                                <div class="mb-5">
                                    <label class="fs-5 fw-semibold mb-2">Tipo</label>
                                    <input type="text" class="form-control form-control-solid" id="edit_tipo"
                                        readonly disabled>
                                </div>

                                <!-- Nome (para tipo Caixa) -->
                                <div class="mb-5" id="edit_nome-group">
                                    <label class="fs-5 fw-semibold mb-2">Nome da Entidade</label>
                                    <input type="text" class="form-control form-control-solid" name="nome"
                                        id="edit_nome" placeholder="Ex: Caixa Central" />
                                    @error('nome')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Banco (para tipo Banco) -->
                                <div class="mb-5 d-none" id="edit_banco-group">
                                    <label class="fs-5 fw-semibold mb-2">Banco</label>
                                    <select id="edit_banco-select" name="bank_id"
                                        class="form-select form-select-solid" data-control="select2"
                                        data-placeholder="Selecione um banco">
                                        <option></option>
                                        @isset($banks)
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}" data-icon="{{ $bank->logo_url }}">
                                                    {{ $bank->name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('bank_id')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Agência, Conta e Natureza (para tipo Banco) -->
                                <div class="row mb-5 d-none" id="edit_banco-details-group">
                                    <div class="col-md-4">
                                        <label class="fs-5 fw-semibold mb-2">Agência</label>
                                        <input type="text" class="form-control form-control-solid" name="agencia"
                                            id="edit_agencia" placeholder="Número da agência" />
                                        @error('agencia')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="fs-5 fw-semibold mb-2">Conta</label>
                                        <input type="text" class="form-control form-control-solid" name="conta"
                                            id="edit_conta" placeholder="Número da conta" />
                                        @error('conta')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="fs-5 fw-semibold mb-2">Natureza da Conta</label>
                                        <select name="account_type" id="edit_account_type"
                                            class="form-select form-select-solid">
                                            <option value="" disabled>Selecione a natureza</option>
                                            <option value="corrente">Conta Corrente</option>
                                            <option value="poupanca">Poupança</option>
                                            <option value="aplicacao">Aplicação</option>
                                            <option value="renda_fixa">Renda Fixa</option>
                                            <option value="tesouro_direto">Tesouro Direto</option>
                                        </select>
                                        @error('account_type')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="mb-5">
                                    <label class="fs-5 fw-semibold mb-2">Descrição</label>
                                    <textarea class="form-control form-control-solid" rows="4" name="descricao" id="edit_descricao"
                                        placeholder="Insira uma descrição (opcional)"></textarea>
                                    @error('descricao')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Conta Contábil -->
                                <div class="mb-5">
                                    <label class="fs-5 fw-semibold mb-2">Conta Contábil (Plano de
                                        Contas)</label>
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-placeholder="Selecione a conta contábil..." name="conta_contabil_id"
                                        id="edit_conta_contabil_id">
                                        <option></option>
                                        @isset($contas)
                                            @foreach ($contas as $conta)
                                                <option value="{{ $conta->id }}">
                                                    {{ $conta->code }} - {{ $conta->name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <div class="text-muted fs-7 mt-2">Vínculo contábil para exportação
                                        (De/Para)</div>
                                    @error('conta_contabil_id')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Saldo Inicial e Atual (readonly) -->
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="fs-5 fw-semibold mb-2">Saldo Inicial</label>
                                        <input type="text" class="form-control form-control-solid"
                                            id="edit_saldo_inicial" readonly disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fs-5 fw-semibold mb-2">Saldo Atual</label>
                                        <input type="text" class="form-control form-control-solid"
                                            id="edit_saldo_atual" readonly disabled>
                                    </div>
                                </div>

                                <!--begin::Card footer-->
                                <div class="card-footer">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light"
                                            id="kt_drawer_edit_entidade_close_btn">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">
                                            <span class="indicator-label">Salvar Alterações</span>
                                            <span class="indicator-progress">Aguarde...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <!--end::Card footer-->
                            </form>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
            @endif
            <!--end::Drawer de Edição-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/tenancy/assets/js/custom/apps/ecommerce/reports/sales/sales.js"></script>
<script src="/tenancy/assets/js/custom/utilities/modals/bidding.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const submitButton = document.getElementById('kt_modal_submit_button');
        const form = document.querySelector('form'); // Seleciona o formulário

        form.addEventListener('submit', function(e) {
            // Impede o envio do formulário até que o JavaScript seja executado
            e.preventDefault();

            // Mostra o indicador de carregamento
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            // Simula um atraso (substitua isso pelo envio real do formulário)
            setTimeout(function() {
                // Oculta o indicador de carregamento
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;

                // Envia o formulário (substitua isso pelo envio real do formulário)
                form.submit();
            }, 2000); // 2 segundos de atraso (apenas para simulação)
        });
    });

    // Função para abrir drawer de edição
    function openEditDrawer(id, tipo, nome, bancoId, agencia, conta, accountType, descricao, contaContabilId) {
        console.log('=== Iniciando abertura do drawer ===');
        console.log('Parâmetros recebidos:', {
            id,
            tipo,
            nome,
            bancoId,
            agencia,
            conta,
            accountType,
            descricao,
            contaContabilId
        });

        // Verifica se o drawer existe
        const drawerElement = document.getElementById('kt_drawer_edit_entidade');
        if (!drawerElement) {
            console.error('❌ Drawer não encontrado no DOM!');
            console.log('Tentando encontrar elemento com ID: kt_drawer_edit_entidade');
            alert('Erro: Drawer de edição não encontrado. Verifique se você tem permissão de administrador.');
            return;
        }
        console.log('✅ Drawer encontrado no DOM:', drawerElement);

        // Obtém ou cria instância do Drawer
        let drawer;
        if (typeof KTDrawer !== 'undefined') {
            // Tenta obter instância existente
            drawer = KTDrawer.getInstance(drawerElement);

            // Se não existe, cria uma nova instância
            if (!drawer) {
                console.log('Criando nova instância do Drawer...');
                drawer = new KTDrawer(drawerElement);
            } else {
                console.log('Usando instância existente do Drawer');
            }

            // Garante que o drawer foi criado
            if (!drawer) {
                console.error('Falha ao criar instância do Drawer!');
                alert('Erro: Não foi possível criar o Drawer. Verifique o console para mais detalhes.');
                return;
            }
        } else {
            console.error('KTDrawer não encontrado!');
            alert('Erro: Biblioteca KTDrawer não encontrada. Verifique se os scripts foram carregados corretamente.');
            return;
        }

        const form = document.getElementById('kt_drawer_edit_entidade_form');

        if (!form) {
            console.error('Formulário não encontrado!');
            return;
        }

        // Define a action do form
        const baseUrl = '{{ route('entidades.index') }}';
        form.action = `${baseUrl}/${id}`;
        const entidadeIdInput = document.getElementById('edit_entidade_id');
        if (entidadeIdInput) {
            entidadeIdInput.value = id;
        }

        // Trata valores nulos ou undefined
        nome = nome || '';
        // Converte string 'null' para null
        bancoId = (bancoId === 'null' || bancoId === null || bancoId === undefined) ? null : parseInt(bancoId);
        agencia = agencia || '';
        conta = conta || '';
        accountType = accountType || '';
        descricao = descricao || '';

        try {
            // Preenche os campos
            const tipoInput = document.getElementById('edit_tipo');
            if (tipoInput) {
                tipoInput.value = tipo === 'banco' ? 'Banco' : 'Caixa';
            }

            if (tipo === 'caixa') {
                // Mostra campo de nome, esconde campos de banco
                const nomeGroup = document.getElementById('edit_nome-group');
                const bancoGroup = document.getElementById('edit_banco-group');
                const bancoDetailsGroup = document.getElementById('edit_banco-details-group');
                const nomeInput = document.getElementById('edit_nome');

                if (nomeGroup) nomeGroup.classList.remove('d-none');
                if (bancoGroup) bancoGroup.classList.add('d-none');
                if (bancoDetailsGroup) bancoDetailsGroup.classList.add('d-none');
                if (nomeInput) nomeInput.value = nome;
            } else {
                // Esconde campo de nome, mostra campos de banco
                const nomeGroup = document.getElementById('edit_nome-group');
                const bancoGroup = document.getElementById('edit_banco-group');
                const bancoDetailsGroup = document.getElementById('edit_banco-details-group');
                const agenciaInput = document.getElementById('edit_agencia');
                const contaInput = document.getElementById('edit_conta');
                const accountTypeInput = document.getElementById('edit_account_type');

                if (nomeGroup) nomeGroup.classList.add('d-none');
                if (bancoGroup) bancoGroup.classList.remove('d-none');
                if (bancoDetailsGroup) bancoDetailsGroup.classList.remove('d-none');
                if (agenciaInput) agenciaInput.value = agencia;
                if (contaInput) contaInput.value = conta;
                if (accountTypeInput) accountTypeInput.value = accountType;
            }

            const descricaoInput = document.getElementById('edit_descricao');
            if (descricaoInput) {
                descricaoInput.value = descricao;
            }

            // Preencher conta contábil
            const contaContabilSelect = document.getElementById('edit_conta_contabil_id');
            if (contaContabilSelect && contaContabilId) {
                // Aguardar um pouco para garantir que Select2 está pronto
                setTimeout(function() {
                    if (typeof $ !== 'undefined' && $.fn.select2) {
                        $(contaContabilSelect).val(contaContabilId).trigger('change');
                    } else {
                        contaContabilSelect.value = contaContabilId;
                    }
                }, 300);
            }
        } catch (error) {
            console.error('Erro ao preencher campos:', error);
        }

        // Busca os saldos via AJAX
        const jsonUrl = '{{ route('entidades.index') }}';
        fetch(`${jsonUrl}/${id}/json`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.entidade) {
                    const entidade = data.data.entidade;
                    const saldoInicialInput = document.getElementById('edit_saldo_inicial');
                    const saldoAtualInput = document.getElementById('edit_saldo_atual');

                    if (saldoInicialInput) {
                        saldoInicialInput.value = 'R$ ' + parseFloat(entidade.saldo_inicial_real || 0).toLocaleString(
                            'pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                    }
                    if (saldoAtualInput) {
                        saldoAtualInput.value = 'R$ ' + parseFloat(entidade.saldo_atual || 0).toLocaleString(
                            'pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao buscar dados:', error);
            });

        // Abre o drawer primeiro
        try {
            console.log('Tentando abrir o drawer...');
            if (drawer && typeof drawer.show === 'function') {
                drawer.show();
                console.log('Drawer.show() chamado com sucesso');
            } else {
                console.error('drawer.show não é uma função!', drawer);
                alert('Erro: Método show() não disponível no Drawer.');
                return;
            }

            // Função para inicializar Select2
            const initSelect2 = () => {
                if (tipo === 'banco') {
                    setTimeout(() => {
                        const bancoSelect = $('#edit_banco-select');
                        if (bancoSelect.length === 0) {
                            console.warn('Select de banco não encontrado');
                            return;
                        }

                        if (bancoSelect.hasClass('select2-hidden-accessible')) {
                            bancoSelect.select2('destroy');
                        }

                        // Define o valor do select antes de inicializar
                        if (bancoId) {
                            bancoSelect.val(bancoId);
                        }

                        if (typeof KTSelect2 !== 'undefined') {
                            new KTSelect2(bancoSelect[0]);
                        } else {
                            bancoSelect.select2({
                                placeholder: "Selecione um banco",
                                allowClear: true,
                                templateResult: function(state) {
                                    if (!state.id) return state.text;
                                    let iconUrl = $(state.element).attr('data-icon');
                                    if (!iconUrl) return state.text;
                                    let html = '<span class="d-flex align-items-center">';
                                    html += '<img src="' + iconUrl +
                                        '" class="me-2" style="width:24px; height:24px;" />';
                                    html += '<span>' + state.text + '</span>';
                                    html += '</span>';
                                    return $(html);
                                },
                                templateSelection: function(state) {
                                    if (!state.id) return state.text;
                                    let iconUrl = $(state.element).attr('data-icon');
                                    if (!iconUrl) return state.text;
                                    let html = '<span class="d-flex align-items-center">';
                                    html += '<img src="' + iconUrl +
                                        '" class="me-2" style="width:24px; height:24px;" />';
                                    html += '<span>' + state.text + '</span>';
                                    html += '</span>';
                                    return $(html);
                                }
                            });
                        }
                    }, 100);
                }
            };

            // Aguarda o drawer ser exibido antes de inicializar Select2
            drawer.on('kt.drawer.shown', function() {
                initSelect2();
                initContaContabilSelect2();
            });

            // Função para inicializar Select2 da conta contábil
            const initContaContabilSelect2 = function() {
                if (typeof $ === 'undefined' || !$.fn.select2) {
                    return;
                }

                const contaContabilSelect = $('#edit_conta_contabil_id');
                if (contaContabilSelect.length === 0) {
                    return;
                }

                setTimeout(() => {
                    if (!contaContabilSelect.hasClass('select2-hidden-accessible')) {
                        if (typeof KTSelect2 !== 'undefined') {
                            new KTSelect2(contaContabilSelect[0]);
                        } else {
                            contaContabilSelect.select2({
                                placeholder: "Selecione a conta contábil...",
                                allowClear: true
                            });
                        }
                    }
                    // Definir valor se existir (usa a variável do escopo externo)
                    if (contaContabilId) {
                        contaContabilSelect.val(contaContabilId).trigger('change');
                    }
                }, 200);
            };

            // Fallback: se o evento não for disparado, tenta inicializar após 500ms
            setTimeout(() => {
                if (tipo === 'banco' && $('#edit_banco-select').length > 0 && !$('#edit_banco-select').hasClass(
                        'select2-hidden-accessible')) {
                    initSelect2();
                }
                initContaContabilSelect2();
            }, 500);

            // Adiciona evento ao botão de cancelar
            const closeBtn = document.getElementById('kt_drawer_edit_entidade_close_btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    drawer.hide();
                });
            }
        } catch (error) {
            console.error('Erro ao abrir drawer:', error);
            alert('Erro ao abrir drawer de edição. Verifique o console para mais detalhes.');
        }
    }

    // Adiciona event listeners aos botões de editar
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa o Drawer
        const drawerElement = document.getElementById('kt_drawer_edit_entidade');
        if (drawerElement && typeof KTDrawer !== 'undefined') {
            // Verifica se já foi inicializado
            let drawer = KTDrawer.getInstance(drawerElement);
            if (!drawer) {
                // Inicializa o Drawer se ainda não foi inicializado
                console.log('Inicializando Drawer de edição...');
                drawer = new KTDrawer(drawerElement);
            }
        }

        // Adiciona event listeners aos botões de editar
        const editButtons = document.querySelectorAll('.btn-edit-entidade');
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-entidade-id');
                const tipo = this.getAttribute('data-entidade-tipo');
                const nome = this.getAttribute('data-entidade-nome');
                const bancoId = this.getAttribute('data-entidade-banco-id') || null;
                const agencia = this.getAttribute('data-entidade-agencia') || '';
                const conta = this.getAttribute('data-entidade-conta') || '';
                const accountType = this.getAttribute('data-entidade-account-type') || '';
                const descricao = this.getAttribute('data-entidade-descricao') || '';
                const contaContabilId = this.getAttribute('data-entidade-conta-contabil-id') ||
                    '';

                openEditDrawer(id, tipo, nome, bancoId, agencia, conta, accountType, descricao,
                    contaContabilId);
            });
        });
    });
</script>
