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
                         <input type="text" data-kt-ecommerce-order-filter="search"
                             class="form-control form-control-solid w-250px ps-14" placeholder="Search Report" />
                     </div>
                     <!--end::Search-->
                     <!--begin::Export buttons-->
                     <div id="kt_ecommerce_report_customer_orders_export" class="d-none"></div>
                     <!--end::Export buttons-->
                 </div>
                 <!--end::Card title-->
             </div>
             <!--end::Card header-->
             <!--begin::Card body-->
             <div class="card-body pt-0">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5"
                       id="kt_ecommerce_report_customer_orders_table">
                    <!--begin::Table head-->
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-80px">ID</th>
                            <th class="min-w-150px">Nome da Entidade</th>
                            <th class="min-w-200px">Descrição</th>
                            <th class="min-w-150px">Última Conciliação</th>
                            <th class="text-end min-w-120px">Status de Conciliação</th>
                            <th class="text-end min-w-100px">Valor Conciliado</th>
                            <th class="text-end min-w-100px">Saldo Atual</th>
                            <th class="text-end min-w-100px">Valor Pendente</th>
                            <th class="text-end min-w-150px">Arquivo Importado</th>
                            <th class="text-end min-w-150px">Responsável</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-semibold text-gray-600">
                        @foreach ($entidadesBanco as $entidade)
                            <tr>
                                <!-- ID -->
                                <td>
                                    <a href="#" class="text-dark text-hover-primary">{{ $entidade->id }}</a>
                                </td>
                                <!-- Nome da Entidade -->
                                <td>
                                    <a href="#" class="text-dark text-hover-primary">{{ $entidade->nome }}</a>
                                </td>
                                <!-- Descrição -->
                                <td>
                                    {{ $entidade->descricao }}
                                </td>
                                <!-- Data da Última Conciliação -->
                                <td>
                                    {{ $entidade->updated_at ? $entidade->updated_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <!-- Status de Conciliação -->
                                <td class="text-end">
                                    <span class="badge {{ $entidade->badge_class }} fs-base">
                                        {{ ucfirst($entidade->status_conciliacao) }}
                                    </span>
                                </td>
                                <!-- Valor Conciliado -->
                                <td class="text-end">
                                    R$ {{ number_format($entidade->valor_conciliado ?? 0, 2, ',', '.') }}
                                </td>
                                <!-- Saldo Atual -->
                                <td class="text-end">
                                    R$ {{ number_format($entidade->saldo_atual ?? 0, 2, ',', '.') }}
                                </td>
                                <!-- Valor Pendente -->
                                <td class="text-end">
                                    R$ {{ number_format(($entidade->amount ?? 0) - ($entidade->valor_conciliado ?? 0), 2, ',', '.') }}
                                </td>
                                <!-- Arquivo Importado -->
                                <td class="text-end">
                                    {{ $entidade->file_info ? json_decode($entidade->file_info)->name : '-' }}
                                </td>
                                <!-- Responsável -->
                                <td class="text-end">
                                    {{ $entidade->updated_by_name ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>

             <!--end::Card body-->
         </div>
         <!--end::Products-->
