                    <!--begin::Products-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" data-kt-ecommerce-order-filter="search"
                                        class="form-control form-control-solid w-250px ps-14"
                                        placeholder="Buscar Lançamento" />
                                </div>
                                <!--end::Search-->
                                <!--begin::Export buttons-->
                                <div id="kt_ecommerce_report_shipping_export" class="d-none"></div>
                                <!--end::Export buttons-->
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <!--begin::Daterangepicker-->
                                <input class="form-control form-control-solid w-100 mw-250px"
                                    placeholder="Pick date range" id="kt_ecommerce_report_shipping_daterangepicker" />
                                <!--end::Daterangepicker-->
                                <!--begin::Filter-->
                                <div class="w-150px">
                                    <!--begin::Select2-->
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-hide-search="true" data-placeholder="Status"
                                        data-kt-ecommerce-order-filter="status">
                                        <option></option>
                                        <option value="all">Todos</option>
                                        <option value="entrada">entrada</option>
                                        <option value="saida">Saída</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>
                                <!--end::Filter-->
                                <!--begin::Export dropdown-->
                                <button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click"
                                    data-kt-menu-placement="bottom-end">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fa-solid fa-file-export"></i>
                                    </span>
                                    <!--end::Svg Icon-->Relatório</button>
                                <!--begin::Menu-->
                                <div id="kt_ecommerce_report_shipping_export_menu"
                                    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                                    data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3"
                                            data-kt-ecommerce-export="excel">Exporta Excel</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-ecommerce-export="csv">Exporta
                                            CSV</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-ecommerce-export="pdf">Exporta
                                            PDF</a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->
                                <!--end::Export dropdown-->
                                <!--begin::Menu-->
                                <div class="me-0">
                                    <button class="btn btn-sm btn-light-warning" data-kt-menu-trigger="click"
                                        data-kt-menu-placement="bottom-end">
                                        <i class="bi bi-plus-circle fs-3"></i>
                                        Novo Lançamento
                                    </button>

                                    <!--begin::Menu Dropdown-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                        data-kt-menu="true">
                                        <!--begin::Heading-->
                                        <div class="menu-item px-3">
                                            <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                Novo Lançamento</div>
                                        </div>
                                        <!--end::Heading-->

                                        <!--begin::Menu Item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                                data-bs-target="#Dm_modal_caixa" data-tipo="receita"
                                                aria-label="Adicionar nova receita">
                                                <span class="me-2">💰</span> Nova Receita
                                            </a>
                                        </div>
                                        <!--end::Menu Item-->
                                        <!--begin::Menu Item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                                data-bs-target="#Dm_modal_caixa" data-tipo="despesa"
                                                aria-label="Adicionar nova despesa">
                                                <span class="me-2">💸</span> Nova Despesa
                                            </a>
                                        </div>
                                        <!--end::Menu Item-->
                                    </div>
                                    <!--end::Menu Dropdown-->
                                </div>
                                <!--end::Menu-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-2"
                                id="kt_ecommerce_report_shipping_table">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-75px">ID</th>
                                        <th class="min-w-100px">Data</th>
                                        <th class="min-w-150px">Tipo Documento</th>
                                        <th class="min-w-100px">NF</th>
                                        <th class="min-w-400px">Descrição</th>
                                        <th class="min-w-125px">Tipo</th>
                                        <th class="min-w-125px">Valor</th>
                                        <th class="min-w-75px">Origem</th>
                                        <th class="min-w-70px">Anexos</th>
                                        <th class="text-end min-w-50px">Ações</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    <!--begin::Table row-->
                                    @foreach ($transacoes as $transacao)
                                        <tr>
                                            <td>{{ $transacao->id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transacao->data_competencia)->format('d/m/y') }}
                                            </td>
                                            <td>{{ $transacao->tipo_documento }}</td>
                                            <td>
                                                {{-- Verifica se tem anexos ativos diretamente do relacionamento --}}
                                                {!! $transacao->modulos_anexos->where('status', 'ativo')->isNotEmpty()
                                                    ? '<i class="fas fa-check-circle text-success" title="Comprovação Fiscal"></i>'
                                                    : '<i class="bi bi-x-circle-fill text-danger" title="Sem Comprovação Fiscal"></i>' !!}
                                            </td>
                                            <td>
                                                <div class="fw-bold" style="cursor: pointer;" onclick="abrirDrawerCaixa({{ $transacao->id }})" title="Clique para ver detalhes">
                                                    {{ Str::limit(optional($transacao->lancamentoPadrao)->description, 50, '...') }}
                                                </div>
                                                <div class="text-muted small" style="cursor: pointer;" onclick="abrirDrawerCaixa({{ $transacao->id }})" title="Clique para ver detalhes">
                                                    {{ Str::limit($transacao->descricao, 50, '...') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="badge fw-bold {{ $transacao->tipo == 'entrada' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $transacao->tipo }}
                                                </div>
                                            </td>
                                            <td>R$ {{ number_format($transacao->valor, 2, ',', '.') }}</td>
                                            <td class="text-center">{{ $transacao->origem }}</td>
                                            <td class="text-center">
                                                <!--begin::Anexos-->
                                                <div class="symbol-group symbol-hover fs-8">
                                                    @php
                                                        $anexos = $transacao->modulos_anexos->take(3); // Exibir até 5 anexos
                                                        $remainingAnexos = $transacao->modulos_anexos->count() - 3; // Contar anexos extras
                                                        $icons = [
                                                            'pdf' => [
                                                                'icon' => 'bi-file-earmark-pdf-fill',
                                                                'color' => 'text-danger',
                                                            ],
                                                            'jpg' => [
                                                                'icon' => 'bi-file-earmark-image-fill',
                                                                'color' => 'text-warning',
                                                            ],
                                                            'jpeg' => [
                                                                'icon' => 'bi-file-earmark-image-fill',
                                                                'color' => 'text-primary',
                                                            ],
                                                            'png' => [
                                                                'icon' => 'bi-file-earmark-image-fill',
                                                                'color' => 'text-success',
                                                            ],
                                                            'doc' => [
                                                                'icon' => 'bi-file-earmark-word-fill',
                                                                'color' => 'text-info',
                                                            ],
                                                            'docx' => [
                                                                'icon' => 'bi-file-earmark-word-fill',
                                                                'color' => 'text-info',
                                                            ],
                                                            'xls' => [
                                                                'icon' => 'bi-file-earmark-spreadsheet-fill',
                                                                'color' => 'text-warning',
                                                            ],
                                                            'xlsx' => [
                                                                'icon' => 'bi-file-earmark-spreadsheet-fill',
                                                                'color' => 'text-warning',
                                                            ],
                                                            'txt' => [
                                                                'icon' => 'bi-file-earmark-text-fill',
                                                                'color' => 'text-muted',
                                                            ],
                                                        ];
                                                        $defaultIcon = [
                                                            'icon' => 'bi-file-earmark-fill',
                                                            'color' => 'text-secondary',
                                                        ];
                                                    @endphp

                                                    <!-- Mostrar até 5 anexos -->
                                                    @foreach ($anexos as $anexo)
                                                        @php
                                                            $formaAnexo = $anexo->forma_anexo ?? 'arquivo';
                                                            $isLink = $formaAnexo === 'link';

                                                            if ($isLink) {
                                                                $href = $anexo->link ?? '#';
                                                                $tooltip = $anexo->link ?? 'Link';
                                                                $iconData = ['icon' => 'bi-link-45deg', 'color' => 'text-primary'];
                                                            } else {
                                                                $extension = pathinfo(
                                                                    $anexo->nome_arquivo ?? '',
                                                                    PATHINFO_EXTENSION,
                                                                );
                                                                $iconData = $icons[strtolower($extension)] ?? $defaultIcon;
                                                                $tooltip = $anexo->nome_arquivo ?? 'Arquivo';

                                                                if ($anexo->caminho_arquivo) {
                                                                    $href = route('file', ['path' => $anexo->caminho_arquivo]);
                                                                } else {
                                                                    $href = '#';
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary d-flex justify-content-center align-items-center"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ $tooltip }}">
                                                            <a href="{{ $href }}"
                                                                target="_blank" class="text-decoration-none">
                                                                <i
                                                                    class="bi {{ $iconData['icon'] }} {{ $iconData['color'] }} fs-3"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach

                                                    <!-- Mostrar contador de anexos extras, se houver -->
                                                    @if ($remainingAnexos > 0)
                                                        <div class="symbol symbol-25px symbol-circle"
                                                            data-bs-toggle="tooltip"
                                                            title="Mais {{ $remainingAnexos }} anexos">
                                                            <a href="#" onclick="abrirDrawerEdicao({{ $transacao->id }}); return false;">
                                                                <span
                                                                    class="symbol-label fs-8 fw-bold bg-light text-gray-800">
                                                                    +{{ $remainingAnexos }}
                                                                </span>
                                                            </a>
                                                        </div>
                                                    @endif

                                                    <!-- Exibir mensagem se não houver anexos -->
                                                    @if ($transacao->modulos_anexos->isEmpty())
                                                        <div class="symbol symbol-25px symbol-circle text-center"
                                                            data-bs-toggle="tooltip" title="Nenhum anexo disponível">
                                                            <span
                                                                class="symbol-label fs-8 fw-bold bg-light text-gray-800">
                                                                {{ 0 }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!--end::Anexos-->
                                            </td>

                                            <!--begin::Action=-->
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <!--begin::Button-->
                                                    <a href="{{ route('caixa.edit', $transacao->id) }}"
                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto me-5">
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
                                            </td>
                                            <!--end::Action=-->
                                        </tr>
                                    @endforeach
                                    <!--end::Table row-->
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Products-->

                    <!-- Modal de Lançamento -->
                    @include('app.components.modals.financeiro.lancamento.modal_lancamento_caixa')

                    <!-- Drawer de Detalhes do Caixa -->
                    @include('app.components.drawers.caixa_detalhes')

                    <!-- Modal de Gerar Recibo -->
                    @include('app.components.modals.financeiro.recibo.modal_gerar_recibo_ajax')
