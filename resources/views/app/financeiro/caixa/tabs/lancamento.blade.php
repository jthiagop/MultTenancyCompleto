    <!--end::Card head-->
    <div class="card">
        <!--begin::Card head-->
        <div class="card-header mt-5 ">
            <!--begin::Filtros-->
            <div class="d-flex flex-wrap align-items-center gap-4 mb-5">
                <!--begin::Ano-->
                <div class="d-flex flex-column">
                    <label class="form-label fs-6 fw-semibold mb-2 required">Ano</label>
                    <div class="input-group" style="width: 150px;">
                        <input type="number" class="form-control" id="filtro_ano" value="{{ date('Y') }}"
                            min="2000" max="2100" placeholder="Ano">
                        <span class="input-group-text">
                            <i class="bi bi-calendar-event"></i>
                        </span>
                    </div>
                </div>
                <!--end::Ano-->


                <!--begin::Centro de Custo-->
                <div class="d-flex flex-column flex-grow-1">
                    <label class="form-label fs-6 fw-semibold mb-2">Centro de custo</label>
                    <select class="form-select" id="filtro_centro_custo" data-control="select2"
                        data-placeholder="Selecione o centro de custo" style="min-width: 250px;">
                        <option value="">Todos</option>
                        @php
                            $costCenters = \App\Models\Financeiro\CostCenter::where(
                                'company_id',
                                session('active_company_id'),
                            )
                                ->orderBy('name')
                                ->get();
                        @endphp
                        @foreach ($costCenters as $costCenter)
                            <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--end::Centro de Custo-->

                <!--begin::Botão Gerar-->
                <div class="d-flex flex-column">
                    <label class="form-label fs-6 fw-semibold mb-2" style="visibility: hidden;">Botão</label>
                    <button type="button" class="btn btn-primary" id="btn_gerar_saldos">
                        <i class="bi bi-printer me-2"></i>
                        Gerar
                    </button>
                </div>
                <!--end::Botão Gerar-->
            </div>
            <!--end::Filtros-->
        </div>
        <!--begin::Card body-->
        <div class="card-body pt-0">


            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr class="text-start bg-gray-200 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-125px">Mês</th>
                        <th class="min-w-125px">Saldo Anterior</th>
                        <th class="min-w-125px">Entradas</th>
                        <th class="min-w-125px">Saídas</th>
                        <th class="min-w-125px">Saldo Atual</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-semibold text-gray-600">
                    <!-- Os dados serão populados via JavaScript -->
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            <span class="spinner-border spinner-border-sm"></span> Carregando dados...
                        </td>
                    </tr>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    <!--begin::Scripts-->
    <script>
        // Definir rota para busca de saldos
        window.saldosCaixaRoute = '{{ route('financeiro.saldos-mensais') }}';
    </script>
    <script src="/assets/js/custom/apps/financeiro/saldos-caixa.js"></script>
    <!--end::Scripts-->
