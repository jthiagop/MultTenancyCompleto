<style>
    /* Adiciona escala ao passar o mouse */
    .hover-scale:hover {
        transform: scale(1.1);
        transition: transform 0.2s ease-in-out;
    }

    /* Ajustes de sombra e bordas */
    .symbol {
        border: 1px solid #e4e6ef;
        border-radius: 50%;
    }
</style>

<div class="tab-content mt-5">
    <!-- Conteúdo da Aba Resumo -->
    <div class="tab-pane fade show active" id="resumo">
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
                            class="form-control form-control-solid w-250px ps-14" placeholder="Buscar Lançamento" />
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
                    <input class="form-control form-control-solid w-100 mw-250px" placeholder="Pick date range"
                        id="kt_ecommerce_report_shipping_daterangepicker" />

                    <!--end::Daterangepicker-->
                    <!--begin::Filter-->
                    <div class="w-150px">
                        <!--begin::Select2-->
                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true"
                            data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                            <option></option>
                            <option value="all">Todos</option>
                            <option value="entrada">entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                        <!--end::Select2-->
                    </div>
                    <!--end::Filter-->
                    @include('app.components.modals.relatorio.prestacao')
                    <!--begin::Export dropdown-->
                    <button type="button" class="btn btn-light-primary"  data-bs-toggle="modal"
                    data-bs-target="#prestacaoConta">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1"
                                    transform="rotate(90 12.75 4.25)" fill="currentColor" />
                                <path
                                    d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->Relatório</button>
                    <!--end::Export dropdown-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-2" id="kt_ecommerce_report_shipping_table">
                    <!--begin::Table head-->
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-75px">ID</th>
                            <th class="min-w-100px">Data</th>
                            <th class="min-w-150px">Tipo Documento</th>
                            <th class="min-w-50px">NF</th>
                            <th class="min-w-400px">Descrição</th>
                            <th class="min-w-125px">Tipo</th>
                            <th class="min-w-125px">Valor</th>
                            <th class="min-w-200px">Origem</th>
                            <th class="min-w-70px">Anexos</th>
                            <th class="text-end min-w-50px">Ações</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-semibold text-gray-600" id="transacoes-tbody">
                        @foreach ($transacoes as $transacao)
                            @include('app.financeiro.banco.partials.transacao_row', [
                                'transacao' => $transacao,
                            ])
                        @endforeach
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
                <!--begin::Load More Button-->
                <div id="load-more" class="text-center mt-4">
                    @if ($transacoes->hasMorePages())
                        <button class="btn btn-primary" onclick="loadMore()">Carregar Mais</button>
                    @endif
                </div>
                <!--end::Load More Button-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Products-->
    </div>
</div>

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let loading = false;
        function loadMore() {
            if (loading) return;
            let nextPage = '{{ $transacoes->nextPageUrl() }}';
            if (nextPage) {
                loading = true;
                $.ajax({
                    url: nextPage,
                    type: 'GET',
                    success: function (response) {
                        $('#transacoes-tbody').append($(response).find('#transacoes-tbody').html());
                        if (!$(response).find('#load-more').length || !$(response).find('#load-more button').length) {
                            $('#load-more').hide();
                        } else {
                            $('#load-more').html($(response).find('#load-more').html());
                        }
                        loading = false;
                    },
                    error: function () {
                        alert('Erro ao carregar mais transações.');
                        loading = false;
                    }
                });
            }
        }

        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadMore();
            }
        });
    </script>
@endsection