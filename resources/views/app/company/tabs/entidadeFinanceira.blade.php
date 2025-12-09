<!--begin::Row-->
<div class="row gy-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-xl-6 mb-xl-10">
        <!--begin::Chart widget 5-->
        <div class="card card-flush h-lg-100">
            <div class="card-body">
                <!--begin:Form-->
                <form method="POST" action="{{ route('entidades.store') }}" class="form mb-15">
                    @csrf <!-- Token CSRF obrigatório para proteção -->

                    <!--begin::Heading-->
                    <div class="mb-13 text-center">
                        <!--begin::Title-->
                        <h1 class="mb-3">Cadastrar Nova Entidade Financeira</h1>
                        <!--end::Title-->
                    </div>
                    <!--end::Heading-->
                    <!--end::Title-->
                    <input type="hidden" name="company_id" value="{{ $companyShow->id }}">
                    <div class="row mb-5">
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="fs-5 fw-semibold mb-2">Tipo</label>
                            <select name="tipo" id="tipo" class="form-select form-select-solid" required>
                                <option value="" disabled selected>Selecione o tipo</option>
                                <option value="caixa" {{ old('tipo') == 'caixa' ? 'selected' : '' }}>Caixa</option>
                                <option value="banco" {{ old('tipo') == 'banco' ? 'selected' : '' }}>Banco</option>
                                <option value="dizimo" {{ old('tipo') == 'dizimo' ? 'selected' : '' }}>Dízimo</option>
                                <option value="coleta" {{ old('tipo') == 'coleta' ? 'selected' : '' }}>Coleta</option>
                                <option value="doacao" {{ old('tipo') == 'doacao' ? 'selected' : '' }}>Doação</option>
                            </select>
                            @error('tipo')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-8 fv-row" id="nome-entidade-group">
                            <label class="fs-5 fw-semibold mb-2">Nome da Entidade</label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Ex: Caixa Central" name="nome" value="{{ old('nome') }}" />
                            @error('nome')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->

                        <!-- Campos para Banco (inicialmente ocultos) -->
                        <!-- Grupo de Banco (Oculto por padrão) -->
                        <div class="col-md-8 fv-row d-none" id="banco-group">
                            <label class="fs-5 fw-semibold mb-2">Banco</label>
                            <select id="banco-select" name="banco" class="form-select form-select-solid"
                                data-control="select2" data-placeholder="Selecione um banco">
                                <option></option> <!-- para placeholder vazio -->
                                <!-- Exemplo manual: -->
                                <option value="Banco Bradesco" data-icon="/assets/media/svg/bancos/bradesco.svg">Banco Bradesco</option>
                                <option value="Banco Caixa" data-icon="/assets/media/svg/bancos/caixa.svg">Banco Caixa </option>
                                <option value="Banco Nubank" data-icon="/assets/media/svg/bancos/nubank.svg">Banco Nubank </option>
                                <option value="Banco Itau" data-icon="/assets/media/svg/bancos/itau.svg">Banco Itaú </option>
                                <option value="Banco do Brasil" data-icon="/assets/media/svg/bancos/brasil.svg">Banco do Brasil</option>
                                <option value="Banco stone" data-icon="/assets/media/svg/bancos/stone.svg">Banco Stone </option>
                                <option value="Banco Unicred" data-icon="/assets/media/svg/bancos/unicred.svg">Banco Unicred </option>
                                <option value="Banco Sicoob" data-icon="/assets/media/svg/bancos/Sicoob.svg">Banco Sicoob</option>
                                <option value="Banco Inter" data-icon="/assets/media/svg/bancos/inter.svg">Banco Inter </option>
                                <!-- ... e assim por diante -->
                            </select>

                            @error('banco')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row mb-5">
                        <div class="col-md-6 fv-row d-none" id="agencia-group">
                            <label class="fs-5 fw-semibold mb-2">Agência</label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Número da agência" name="agencia" value="{{ old('agencia') }}" />
                            @error('agencia')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 fv-row d-none" id="conta-group">
                            <label class="fs-5 fw-semibold mb-2">Conta</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Número da conta"
                                name="conta" value="{{ old('conta') }}" />
                            @error('conta')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!--begin::Linha Saldo Inicial / Saldo Atual-->
                    <div class="row mb-5">
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <!--begin::Label-->
                            <label class="fs-5 fw-semibold mb-2">Saldo Inicial</label>
                            <!--end::Label-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <svg class="icon icon-tabler icon-tabler-currency-real" fill="none"
                                        height="24" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <!-- O preenchimento inicial não está definido -->
                                        <path d="M0 0h24v24H0z" fill="none" stroke="none">
                                        </path>
                                        <!-- Desenha a primeira linha que representa o símbolo da moeda -->
                                        <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                        <!-- Traça a segunda linha da moeda -->
                                        <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                        <!-- Traça duas linhas verticais curtas -->
                                        <path d="M18 6v-2"></path>
                                        <path d="M17 20v-2"></path>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid ps-12 money"
                                    placeholder="Ex: 1.000,00" id="valor2" name="saldo_inicial" required />
                                <!--end::Input-->
                                @error('saldo_inicial')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <!--begin::Label-->
                            <label class="fs-5 fw-semibold mb-2">Saldo Atual</label>
                            <!--end::Label-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <svg class="icon icon-tabler icon-tabler-currency-real" fill="none"
                                        height="24" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <!-- O preenchimento inicial não está definido -->
                                        <path d="M0 0h24v24H0z" fill="none" stroke="none">
                                        </path>
                                        <!-- Desenha a primeira linha que representa o símbolo da moeda -->
                                        <path d="M21 6h-4a3 3 0 0 0 0 6h1a3 3 0 0 1 0 6h-4"></path>
                                        <!-- Traça a segunda linha da moeda -->
                                        <path d="M4 18v-12h3a3 3 0 1 1 0 6h-3c5.5 0 5 4 6 6"></path>
                                        <!-- Traça duas linhas verticais curtas -->
                                        <path d="M18 6v-2"></path>
                                        <path d="M17 20v-2"></path>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid ps-12 money"
                                    placeholder="Ex: 1.000,00" id="valor2" name="saldo_atual" />
                                <!--end::Input-->
                                @error('saldo_atual')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--end::Linha Saldo-->

                    <!--begin::Descrição-->
                    <div class="d-flex flex-column mb-5 fv-row">
                        <label class="fs-5 fw-semibold mb-2">Descrição</label>
                        <textarea class="form-control form-control-solid" rows="4" name="descricao"
                            placeholder="Insira uma descrição (opcional)"></textarea>
                        @error('descricao')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Descrição-->

                    <!--begin::Notice (Opcional)-->
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                            <!-- Ícone ilustrativo -->
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M3.20001 5.91897L16.9 3.01895C17.4 2.91895
                                                        18 3.219 18.1 3.819L19.2 9.01895L3.20001 5.91897Z"
                                    fill="currentColor" />
                                <path opacity="0.3" d="M13 13.9189C13 12.2189 14.3 10.9189
                                                        16 10.9189H21C21.6 10.9189 22 11.3189 22
                                                        11.9189V15.9189C22 16.5189 21.6 16.9189
                                                        21 16.9189H16C14.3 16.9189 13 15.6189
                                                        13 13.9189ZM16 12.4189C15.2 12.4189 14.5
                                                        13.1189 14.5 13.9189C14.5 14.7189 15.2
                                                        15.4189 16 15.4189C16.8 15.4189 17.5
                                                        14.7189 17.5 13.9189C17.5 13.1189 16.8
                                                        12.4189 16 12.4189Z" fill="currentColor" />
                                <path d="M13 13.9189C13 12.2189 14.3 10.9189
                                                    16 10.9189H21V7.91895C21 6.81895 20.1
                                                    5.91895 19 5.91895H3C2.4 5.91895
                                                    2 6.31895 2 6.91895V20.9189C2
                                                    21.5189 2.4 21.9189 3
                                                    21.9189H19C20.1 21.9189 21
                                                    21.0189 21 19.9189V16.9189H16C14.3
                                                    16.9189 13 15.6189 13 13.9189Z" fill="currentColor" />
                            </svg>
                        </span>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Dica</h4>
                                <div class="fs-6 text-gray-700">
                                    Certifique-se de preencher corretamente todos os campos
                                    obrigatórios.
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Notice-->

                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-kt-modal-action-type="cancel">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="kt_modal_submit_button">
                            <span class="indicator-label">Enviar</span>
                            <span class="indicator-progress">
                                Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end:Form-->
            </div>
        </div>
        <!--end::Chart widget 5-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-xl-6 mb-5 mb-xl-10">
        <!--begin::Engage widget 1-->
        <div class="card h-md-100" dir="ltr">
            <!--Begin::Card -->
            <!--begin::Card-->
            <div class="card pt-4 mb-6 mb-xl-9">
                <!--begin::Card header-->
                <div class="card-header border-0">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>Transaction History</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 pb-5">
                    <!--begin::Table-->
                    <table class="table text-left align-middle table-row-dashed gy-5" id="kt_table_customers_payment">
                        <!--begin::Table head-->
                        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                            <tr class="text-start text-muted text-uppercase gs-0">
                                <th class="min-w-150px text-left">Nome</th>
                                <th class="min-w-100px text-left">Saldo Inicial</th>
                                <th class="min-w-100px text-left">Atualização</th>
                                <th class="min-w-100px text-left">Saldo Atual</th>
                                <th class="min-w-50px text-left">Tipo</th>
                                <th class="min-w-200px text-left">Descrição</th>
                                <th class="text-left">Ação</th>
                            </tr>
                        </thead>

                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fs-6 fw-semibold text-gray-600">
                            <!--begin::Table row-->
                            @foreach ($entidades as $entidade)
                                <tr>
                                    <!-- Nome -->
                                    <td>{{ $entidade->nome }}</td>
                                    <!-- Saldo Inicial -->
                                    <td class="text-end pe-0">R$
                                        {{ number_format($entidade->saldo_inicial, 2, ',', '.') }}
                                    </td>
                                    <!-- Saldo Inicial -->
                                    <td class="text-end pe-0">
                                        {{ \Carbon\Carbon::parse($entidade->updated_at)->format('d M, Y') }}
                                    </td>
                                    <!-- Saldo Atual -->
                                    <td
                                        class="text-end pe-0 {{ $entidade->saldo_atual >= 0 ? 'text-success' : 'text-danger' }}">
                                        R$ {{ number_format($entidade->saldo_atual, 2, ',', '.') }}
                                    </td>
                                    <!-- Tipo -->
                                    <td class="text-end pe-0">
                                        {{ ucfirst($entidade->tipo) }}
                                    </td>
                                    <!-- Descrição -->
                                    <td class="text-end">
                                        {{ $entidade->descricao ?? '-' }}
                                    </td>
                                    <td>
                                        <!--begin::Action-->
                                        <div class="d-flex justify-content-end align-items-center">
                                            <!--begin::Button-->
                                            <a href=""
                                                class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto me-5"
                                                data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_add_one_time_password">
                                                <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3"
                                                            d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
                                            <!--end::Button-->
                                            <!--begin::Button-->
                                            <button type="button"
                                                class="btn btn-icon btn-active-light-danger w-30px h-30px ms-auto"
                                                id="kt_users_delete_two_step" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_delete_card">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.5"
                                                            d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.5"
                                                            d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </button>
                                            <!--end::Button-->
                                        </div>
                                        <!--end::Action-->
                                    </td>
                                </tr>
                                                    <!--begin::Modal - Confirmar Exclusão-->

                    <div class="modal fade" id="kt_modal_delete_card" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <!-- Cabeçalho -->
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger fw-bold">Confirmar Exclusão</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Corpo -->
                                <div class="modal-body text-center">
                                    <i class="bi bi-exclamation-circle-fill text-danger fs-2 mb-4"></i>
                                    <p class="mb-0 fs-5 fw-semibold text-center">
                                        Tem certeza que deseja excluir o registro <strong>#{{ $entidade->nome }}</strong>?
                                    </p>
                                    <small class="text-muted d-block mt-3">
                                        Esta ação não pode ser desfeita.
                                    </small>
                                </div>

                                <!-- Rodapé -->
                                <div class="modal-footer justify-content-center">
                                    <form id="delete-form" method="POST" action="{{ route('entidades.destroy', $entidade->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger px-4">
                                            <i class="fas fa-trash-alt me-2"></i> Confirmar Exclusão
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--end::Modal - Confirmar Exclusão-->
                            @endforeach
                            <!--end::Table row-->
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
            <!--end::Card-->
        </div>
        <!--end::Engage widget 1-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo');
        const nomeEntidadeGroup = document.getElementById('nome-entidade-group');
        const bancoGroup = document.getElementById('banco-group');
        const agenciaGroup = document.getElementById('agencia-group');
        const contaGroup = document.getElementById('conta-group');

        // Função para exibir/esconder campos
        function toggleFields() {
            const selected = tipoSelect.value;
            if (selected === 'banco') {
                nomeEntidadeGroup.classList.add('d-none'); // Esconde Nome da Entidade
                bancoGroup.classList.remove('d-none'); // Mostra o select de Banco
                agenciaGroup.classList.remove('d-none'); // Mostra Agência
                contaGroup.classList.remove('d-none'); // Mostra Conta
            } else {
                nomeEntidadeGroup.classList.remove('d-none');
                bancoGroup.classList.add('d-none');
                agenciaGroup.classList.add('d-none');
                contaGroup.classList.add('d-none');
            }
        }

        // Evento de mudança no select "tipo"
        tipoSelect.addEventListener('change', toggleFields);

        // Ao carregar a página, se "tipo=banco" já estiver selecionado (ex.: old value),
        // podemos chamar toggleFields() para exibir/esconder adequadamente.
        toggleFields();
    });
</script>

<script>
    $(document).ready(function() {
        $('#banco-select').select2({
            placeholder: "Selecione um banco",
            allowClear: true,

            // Exibir ícone no menu suspenso
            templateResult: function(state) {
                // Se for placeholder ou sem valor, retornar o texto normal
                if (!state.id) {
                    return state.text;
                }

                // Recupera o caminho do ícone do atributo data-icon
                let iconUrl = $(state.element).attr('data-icon');
                if (!iconUrl) {
                    return state.text;
                }

                // Monta um elemento com img + texto
                let $state = $(`
                    <span class="d-flex align-items-center">
                        <img src="${iconUrl}" class="me-2" style="width:24px; height:24px;" />
                        <span>${state.text}</span>
                    </span>
                `);

                return $state;
            },

            // Exibir ícone na opção selecionada
            templateSelection: function(state) {
                if (!state.id) {
                    return state.text;
                }

                let iconUrl = $(state.element).attr('data-icon');
                if (!iconUrl) {
                    return state.text;
                }

                let $state = $(`
                    <span class="d-flex align-items-center">
                        <img src="${iconUrl}" class="me-2" style="width:24px; height:24px;" />
                        <span>${state.text}</span>
                    </span>
                `);
                return $state;
            },
        });
    });
</script>

