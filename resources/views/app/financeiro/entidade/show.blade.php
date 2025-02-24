<x-tenant-app-layout>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Detalhes do Banco - {{ $entidade->nome }}</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('caixa.index') }}"
                                    class="text-muted text-hover-primary">Financeiro</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('banco.list') }}" class="text-muted text-hover-primary">Movimentações
                                    Bacária</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <aspan class="text-muted text-hover-primary">Detalhes</aspan>
                            </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="d-flex">
                            <select name="campaign-type" data-control="select2" data-hide-search="true"
                                class="form-select form-select-sm bg-body border-body w-175px">
                                <option value="Twitter" selected="selected">Select Campaign</option>
                                <option value="Twitter">Twitter Campaign</option>
                                <option value="Twitter">Facebook Campaign</option>
                                <option value="Twitter">Adword Campaign</option>
                                <option value="Twitter">Carbon Campaign</option>
                            </select>
                            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </a>
                        </div>
                        <!--end::Filter menu-->
                        <!--begin::Secondary button-->
                        <!--end::Secondary button-->
                        <!--begin::Primary button-->
                        <!--end::Primary button-->
                    </div> --}}
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!-- Mensagem de sucesso -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Fechar"></button>
                        </div>
                    @endif

                    <!-- Mensagem de erro geral (não relacionada à validação) -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Fechar"></button>
                        </div>
                    @endif

                    <!-- Mensagens de erro de validação (caso existam) -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Fechar"></button>
                        </div>
                    @endif
                    <!-- Navbar -->
                    <div class="container">
                        <!-- Nav Tabs -->
                        <div class="mb-5 hover-scroll-x">
                            <div class="d-grid">
                                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                                    <!-- Aba de Movimentação -->
                                    <li class="nav-item">
                                        <a class="nav-link  btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                            data-bs-toggle="tab" href="#kt_tab_pane_movimentacao">
                                            Movimentação
                                        </a>
                                    </li>

                                    <!-- Aba de Conciliações Pendentes -->
                                    <li class="nav-item">
                                        <a class="nav-link active btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                            data-bs-toggle="tab" href="#kt_tab_pane_conciliacoes">
                                            Conciliações Pendentes
                                            @if ($conciliacoesPendentes->count() > 0)
                                                <span
                                                    class="badge badge-danger">{{ $conciliacoesPendentes->count() }}</span>
                                            @endif
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                            data-bs-toggle="tab" href="#kt_tab_pane_informacao">
                                            Informações
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Conteúdo das Abas -->
                        <div class="tab-content" id="myTabContent">
                            <!-- Aba de Movimentação -->
                            <div class="tab-pane fade  " id="kt_tab_pane_movimentacao" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Movimentação</h5>
                                        @if ($transacoes->isEmpty())
                                            <p class="text-muted">Nenhuma movimentação encontrada.</p>
                                        @else
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Descrição</th>
                                                        <th>Valor</th>
                                                        <th>Tipo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transacoes as $transacao)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($transacao->data_competencia)->format('d/m/Y') }}
                                                            </td>
                                                            <td>{{ $transacao->descricao }}</td>
                                                            <td>R$ {{ number_format($transacao->valor, 2, ',', '.') }}
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $transacao->tipo == 'entrada' ? 'badge-success' : 'badge-danger' }}">
                                                                    {{ ucfirst($transacao->tipo) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Aba de Conciliações Pendentes -->
                            <div class="tab-pane fade show active" id="kt_tab_pane_conciliacoes" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row gx-5 gx-xl-10">
                                            <!--begin::Nome do Banco e do Dominus-->
                                            <div class="col-md-5">
                                                <!--begin::Payment address-->
                                                <!--begin::Info-->
                                                <div class="d-flex flex-stack pb-10">
                                                    <!--begin::Info-->
                                                    <div class="d-flex">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-circle symbol-45px">
                                                            <img src="/assets/media/svg/bancos/brasil.svg"
                                                                alt="" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Details-->
                                                        <div class="ms-5">
                                                            <!--begin::Desc-->
                                                            <span class="text-muted fw-semibold mb-3">Lançamentos
                                                                Importantes
                                                            </span>
                                                            <!--end::Desc-->
                                                            <!--begin::Name-->
                                                            <div class="d-flex align-items-center">
                                                                <a
                                                                    class="text-dark fw-bold text-hover-primary fs-5 me-4">{{ $entidade->nome }}
                                                                    - {{ $entidade->conta }} </a>
                                                            </div>
                                                            <!--end::Name-->
                                                        </div>
                                                        <!--end::Details-->
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Info-->
                                                <!--end::Payment address-->
                                            </div>
                                            <!--end::Payment address-->

                                            <!--begin::Conciliar (Botão Central)-->
                                            <div class="col-md-1 d-flex align-items-center justify-content-center">
                                            </div>
                                            <!--end::Conciliar-->

                                            <!--begin::Shipping address-->
                                            <div class="col-md-6">
                                                <!--begin::Info-->
                                                <div class="d-flex flex-stack pb-10">
                                                    <!--begin::Info-->
                                                    <div class="d-flex">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-circle symbol-45px">
                                                            <img src="/assets/media/avatars/300-35.png"
                                                                alt="" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Details-->
                                                        <div class="ms-5">
                                                            <!--begin::Desc-->
                                                            <span class="text-muted fw-semibold mb-3">
                                                                Lançamentos a cadastrar
                                                            </span>
                                                            <!--end::Desc-->
                                                            <!--begin::Name-->
                                                            <div class="d-flex align-items-center">
                                                                <a
                                                                    class="text-dark fw-bold text-hover-primary fs-5 me-4">Dominus
                                                                    Sistema</a>
                                                            </div>
                                                            <!--end::Name-->
                                                        </div>
                                                        <!--end::Details-->
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <!--end::Nome do Banco e do Dominus-->
                                        </div>

                                        @if ($conciliacoesPendentes->isEmpty())
                                            <p class="text-muted">Nenhuma conciliação pendente encontrada.</p>
                                        @else
                                            @foreach ($conciliacoesPendentes as $conciliacao)
                                                <!--begin::Row-->
                                                <div class="row gx-5 gx-xl-10">
                                                    @php
                                                        $sugestoes = $conciliacao->possiveisTransacoes ?? collect();
                                                    @endphp
                                                    <!--begin::Col-->
                                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                                        <!--begin::List widget 8-->
                                                        <div class="card card-flush h-lg-100">
                                                            <div
                                                                class="card card-flush flex-row-fluid overflow-hidden border border-hover-primary mb-3">
                                                                <div class="p-7 rounded">
                                                                    <!-- Cabeçalho (Data, Checknum, etc.) -->
                                                                    <div class="d-flex flex-stack pb-3 ">

                                                                        <div class="d-flex">
                                                                            <div>
                                                                                <div class="d-flex align-items-center">
                                                                                    <span
                                                                                        class="text-dark fw-bold text-hover-primary fs-5 me-4">
                                                                                        {{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('d/m/Y') }}
                                                                                    </span>
                                                                                    <!-- Número do Cheque (se houver) -->
                                                                                    @if ($conciliacao->checknum)
                                                                                        <span
                                                                                            class="badge badge-light-success d-flex align-items-center fs-8 fw-semibold">
                                                                                            {{ $conciliacao->checknum }}
                                                                                        </span>
                                                                                    @endif

                                                                                </div>
                                                                                <!-- Dia da Semana (opcional) -->
                                                                                <span
                                                                                    class="text-muted fw-semibold mb-3">
                                                                                    {{ strtoupper(\Carbon\Carbon::parse($conciliacao->dtposted)->translatedFormat('l')) }}
                                                                                </span>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Valor -->
                                                                        <div class="text-end pb-3">
                                                                            <span
                                                                                class="{{ $conciliacao->amount < 0 ? 'text-danger' : 'text-dark' }}
                                                                                    fw-bold fs-5">
                                                                                R$
                                                                                {{ number_format($conciliacao->amount, 2, ',', '.') }}
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Descrição / Memo -->
                                                                    <div class="p-0">
                                                                        <div class="d-flex flex-column">
                                                                            <p
                                                                                class="text-gray-700 fw-semibold fs-6 mb-4">
                                                                                {{ $conciliacao->memo }}
                                                                            </p>
                                                                        </div>
                                                                        <td>
                                                                            @if ($conciliacao->status_conciliacao == 'ok')
                                                                                <span
                                                                                    class="badge badge-success">✅
                                                                                    Conciliado</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'pendente')
                                                                                <span
                                                                                    class="badge badge-warning">⏳
                                                                                    Pendente</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'parcial')
                                                                                <span
                                                                                    class="badge badge-info">🟡
                                                                                    Parcial</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'divergente')
                                                                                <span
                                                                                    class="badge badge-danger">❌
                                                                                    Divergente</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'ignorado')
                                                                                <span
                                                                                    class="badge badge-secondary">🚫
                                                                                    Ignorado</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'ajustado')
                                                                                <span
                                                                                    class="badge badge-primary">🔧
                                                                                    Ajustado</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'em análise')
                                                                                <span
                                                                                    class="badge badge-dark">🔍
                                                                                    Em Análise</span>
                                                                            @endif
                                                                        </td>

                                                                        <div class="d-flex flex-column">
                                                                            <div
                                                                                class="separator separator-dashed border-muted my-5">
                                                                            </div>
                                                                            <div class="d-flex flex-stack">

                                                                                <div
                                                                                    class="d-flex flex-column mw-200px">
                                                                                    <div
                                                                                        class="d-flex align-items-center mb-2">
                                                                                        <span
                                                                                            class="text-gray-700 fs-6 fw-semibold me-2">
                                                                                            Importado via OFX
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- Botão "Ignorar" (se houver essa funcionalidade) -->
                                                                                <form
                                                                                    action="{{ route('conciliacao.ignorar', $conciliacao->id) }}"
                                                                                    method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('PATCH')
                                                                                    <button type="submit"
                                                                                        class="btn btn-sm btn-secondary">
                                                                                        🚫 Ignorar
                                                                                    </button>
                                                                                </form>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--end::LIst widget 8-->
                                                    </div>
                                                    <!--end::Col-->
                                                    @php
                                                        $sugestoes = $conciliacao->possiveisTransacoes ?? collect();
                                                    @endphp
                                                    <!-- Exemplo: mostrando apenas a primeira sugestão -->
                                                    @php
                                                        $transacaoSugerida = $sugestoes->first();
                                                    @endphp
                                                    <!-- Se houver pelo menos 1 transação possível... -->
                                                    @if ($sugestoes->count() > 0)
                                                        <!--begin::Col-->
                                                        <div class="col-xxl-2 mb-5 mb-xl-10">
                                                            <!--begin::List widget 9-->
                                                            <div class="card card-flush h-xl-100">
                                                                <!--begin::Header-->
                                                                <div class="card-body d-flex align-items-center justify-content-center h-100"> <!-- Centraliza horizontal e verticalmente -->
                                                                    <button class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#modalConciliar{{ $conciliacao->id }}">
                                                                            <span class="fs-1 me-2">🫱🏻‍🫲🏽</span> <!-- Emoji com tamanho ajustado -->
                                                                            <span>Conciliar</span> <!-- Texto -->
                                                                    </button>
                                                                </div>
                                                                <!--end::Body-->
                                                            </div>
                                                            <!--end::List widget 9-->
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-xxl-6 mb-5 mb-xl-10">
                                                            <!--begin::List widget 9-->
                                                            <div class="card card-flush h-xl-100">
                                                                <!--begin::Header-->
                                                                <div
                                                                class="card card-flush py-4 flex-row-fluid overflow-hidden  mb-3 h-xl-100 ">


                                                                <p><strong>Encontramos um lançamento que parece
                                                                        corresponder:</strong></p>
                                                                <p class="mb-1">
                                                                    <strong>Descrição:</strong>
                                                                    {{ $transacaoSugerida->descricao }}
                                                                </p>
                                                                <p class="mb-1">
                                                                    <strong>Data Competência:</strong>
                                                                    {{ \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('d/m/Y') }}
                                                                </p>
                                                                <p class="mb-1">
                                                                    <strong>Valor:</strong> R$
                                                                    {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}
                                                                </p>
                                                                <p class="mb-1">
                                                                    <strong>Número:</strong>
                                                                    {{ $transacaoSugerida->numero_documento }}
                                                                </p>
                                                                <p class="mb-1">
                                                                    <strong>Número:</strong>
                                                                    {{ $transacaoSugerida->tipo }}
                                                                </p>
                                                                <!-- Botão para conciliar usando essa transação -->
                                                                <form action="" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="bank_statement_id"
                                                                        value="{{ $conciliacao->id }}">
                                                                    <input type="hidden" name="transacao_id"
                                                                        value="{{ $transacaoSugerida->id }}">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-success">
                                                                        Selecionar lançamento existente
                                                                    </button>
                                                                </form>

                                                                <hr>
                                                                <!-- Se quiser listar todas as sugestões ao invés de só uma, podemos iterar -->
                                                                @if ($sugestoes->count() > 1)
                                                                    <p><strong>Outras sugestões:</strong></p>
                                                                    <ul class="list-group mb-3">
                                                                        @foreach ($sugestoes->skip(1) as $t)
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    {{ \Carbon\Carbon::parse($t->data_competencia)->format('d/m/Y') }}
                                                                                    - R$
                                                                                    {{ number_format($t->valor, 2, ',', '.') }}
                                                                                    <small>({{ $t->descricao }})</small>
                                                                                </div>
                                                                                <form action="" method="POST">
                                                                                    @csrf
                                                                                    <input type="hidden"
                                                                                        name="bank_statement_id"
                                                                                        value="{{ $conciliacao->id }}">
                                                                                    <input type="hidden"
                                                                                        name="transacao_id"
                                                                                        value="{{ $t->id }}">
                                                                                    <button type="submit"
                                                                                        class="btn btn-sm btn-primary">Usar
                                                                                        este</button>
                                                                                </form>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </div>
                                                                <!--end::Body-->
                                                            </div>
                                                            <!--end::List widget 9-->
                                                        </div>
                                                        <!--end::Col-->
                                                    @else
                                                        <!--begin::Col-->
                                                        <div class="col-xxl-2 mb-5 mb-xl-10">
                                                            <!--begin::List widget 9-->
                                                            <div class="card card-flush h-xl-100">
                                                                <!--begin::Header-->
                                                                <div class="card-body d-flex align-items-center justify-content-center h-100">
                                                                    <button class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                                                            type="submit" form="{{ $conciliacao->id }}">
                                                                        <span class="fs-1 me-2">🫱🏻‍🫲🏽</span> <!-- Emoji com tamanho ajustado -->
                                                                        <span>Conciliar</span> <!-- Texto -->
                                                                    </button>
                                                                </div>
                                                                <!--end::Body-->
                                                            </div>
                                                            <!--end::List widget 9-->
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-xxl-6 mb-5 mb-xl-10">
                                                            <!--begin::Engage widget 11-->
                                                            <div class="card card-flush h-xl-100">
                                                                <!--begin::Body-->
                                                                <div
                                                                    class="card card-flush  flex-row-fluid overflow-hidden h-xl-100 ">
                                                                    <!-- Aqui suas abas para criar / buscar lançamento -->
                                                                    <ul class="nav nav-tabs"
                                                                        id="lancamentoTab{{ $conciliacao->id }}"
                                                                        role="tablist">
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link active"
                                                                                id="novo-lancamento-{{ $conciliacao->id }}-tab"
                                                                                data-bs-toggle="tab"
                                                                                data-bs-target="#novo-lancamento-{{ $conciliacao->id }}-pane"
                                                                                type="button" role="tab"
                                                                                aria-controls="novo-lancamento-{{ $conciliacao->id }}-pane"
                                                                                aria-selected="true">
                                                                                Novo lançamento
                                                                            </button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link"
                                                                                id="transferencia-{{ $conciliacao->id }}-tab"
                                                                                data-bs-toggle="tab"
                                                                                data-bs-target="#transferencia-{{ $conciliacao->id }}-pane"
                                                                                type="button" role="tab"
                                                                                aria-controls="transferencia-{{ $conciliacao->id }}-pane"
                                                                                aria-selected="false">
                                                                                Transferência
                                                                            </button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link"
                                                                                id="buscar-criar-{{ $conciliacao->id }}-tab"
                                                                                data-bs-toggle="tab"
                                                                                data-bs-target="#buscar-criar-{{ $conciliacao->id }}-pane"
                                                                                type="button" role="tab"
                                                                                aria-controls="buscar-criar-{{ $conciliacao->id }}-pane"
                                                                                aria-selected="false">
                                                                                Buscar/Criar vários
                                                                            </button>
                                                                        </li>
                                                                    </ul>

                                                                    <!-- Conteúdo das Abas -->
                                                                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden tab-content p-5 border"
                                                                        id="lancamentoTabContent{{ $conciliacao->id }}">
                                                                        <!-- Aba Novo Lançamento -->
                                                                        <div class="tab-pane fade show active "
                                                                            id="novo-lancamento-{{ $conciliacao->id }}-pane"
                                                                            role="tabpanel"aria-labelledby="novo-lancamento-{{ $conciliacao->id }}-tab">
                                                                            <!-- Exibe as abas de "Novo Lançamento", "Transferência", etc. -->
                                                                            <div
                                                                                class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row h-5 p-5 mb-10">
                                                                                <!-- Conteúdo do alerta -->
                                                                                <div class="d-flex flex-column">
                                                                                    <span
                                                                                        class="fs-6 fw-bold">Lançamento
                                                                                        não encontrado
                                                                                        automaticamente:</span>
                                                                                    <span class="fs-6">Crie um novo
                                                                                        ao
                                                                                        alimentar o formulário e
                                                                                        clicando no
                                                                                        botão conciliar.</span>
                                                                                </div>

                                                                                <!-- Botão de fechar -->
                                                                                <button type="button"
                                                                                    class="btn-close position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 ms-sm-auto"
                                                                                    data-bs-dismiss="alert"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <form id="{{ $conciliacao->id }}"
                                                                                action="{{ route('conciliacao.conciliar') }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <!-- Exemplo simples de formulário -->
                                                                                <div class="row mb-3">
                                                                                    <div class="col-md-6">
                                                                                        <label for="descricao"
                                                                                            class="required form-label fw-semibold">Descrição
                                                                                        </label>
                                                                                        <input type="text"
                                                                                            value="{{ $conciliacao->memo }}"
                                                                                            class="form-control"
                                                                                            name="descricao"
                                                                                            placeholder="Ex: PAYMENT - Fulano">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="categoria"
                                                                                            class="required form-label fw-semibold">Centro
                                                                                            de Custo
                                                                                        </label>
                                                                                        <select name="cost_center_id"
                                                                                            id="banco_id"
                                                                                            class="form-select form-select-solid @error('cost_center_id') is-invalid @enderror"
                                                                                            data-control="select2"
                                                                                            data-dropdown-css-class="auto"
                                                                                            data-placeholder="Selecione o Centro de Custo">
                                                                                            <!-- Placeholder configurado aqui -->
                                                                                            @foreach ($centrosAtivos as $centrosAtivo)
                                                                                                <option
                                                                                                    value="{{ $centrosAtivo->id }}">
                                                                                                    {{ $centrosAtivo->name }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>

                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <div class="col-md-8">
                                                                                        <!-- Campo oculto para armazenar se é entrada ou saída -->
                                                                                        <input type="hidden"
                                                                                            name="tipo"
                                                                                            class="tipo-lancamento"
                                                                                            value="{{ $conciliacao->amount > 0 ? 'entrada' : 'saida' }}">
                                                                                        <input type="hidden"
                                                                                            name="valor"
                                                                                            class=""
                                                                                            value="{{ $conciliacao->amount }}">
                                                                                        <input type="hidden"
                                                                                            name="data_competencia"
                                                                                            value="{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}">
                                                                                        <input type="hidden"
                                                                                            name="numero_documento"
                                                                                            class=""
                                                                                            value="{{ $conciliacao->checknum }}">
                                                                                        <input type="hidden"
                                                                                            name="descricao"
                                                                                            class=""
                                                                                            value="{{ $conciliacao->memo }}">
                                                                                        <input type="hidden"
                                                                                            name="origem"
                                                                                            class=""
                                                                                            value="Importado via OFX">
                                                                                        <input type="hidden"
                                                                                            name="entidade_id"
                                                                                            class=""
                                                                                            value="{{ $entidade->id }}">
                                                                                        <input type="hidden"
                                                                                            name="bank_statement_id"
                                                                                            value="{{ $conciliacao->id }}">
                                                                                        <input type="hidden"
                                                                                            name="transacao_id"
                                                                                            value="{{ $transacao->id }}">

                                                                                        <label for="descricao"
                                                                                            class="required form-label fw-semibold">Lançamento
                                                                                            Padrão
                                                                                        </label>
                                                                                        <!-- SEU SELECT LANÇAMENTO PADRÃO -->
                                                                                        <select
                                                                                            name="lancamento_padrao_id"
                                                                                            class="form-select form-select-solid lancamento_padrao_banco"
                                                                                            data-control="select2">
                                                                                            <!-- Placeholder option -->
                                                                                            <option value=""
                                                                                                selected disabled>
                                                                                                Selecione um
                                                                                                lançamento padrão
                                                                                            </option>
                                                                                            @foreach ($lps as $lp)
                                                                                                <option
                                                                                                    value="{{ $lp->id }}"
                                                                                                    data-type="{{ $lp->type }}">
                                                                                                    {{ $lp->description }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-md-4">
                                                                                        <label for="descricao"
                                                                                            class="required form-label fw-semibold">Tipo
                                                                                            do Documento
                                                                                        </label>
                                                                                        <select
                                                                                            class="form-select form-select-solid"
                                                                                            data-control="select2"
                                                                                            placeholder="Tipo de Documento"
                                                                                            name="tipo_documento"
                                                                                            id="tipo_documento">
                                                                                            <option value="Pix"
                                                                                                {{ old('tipo_documento') == 'Pix' ? 'selected' : '' }}>
                                                                                                Pix
                                                                                            </option>
                                                                                            <option value="OUTR - Dafe"
                                                                                                {{ old('tipo_documento') == 'OUTR - Dafe' ? 'selected' : '' }}>
                                                                                                OUTR - Dafe</option>
                                                                                            <option
                                                                                                value="NF - Nota Fiscal"
                                                                                                {{ old('tipo_documento') == 'NF - Nota Fiscal' ? 'selected' : '' }}>
                                                                                                NF - Nota Fiscal
                                                                                            </option>
                                                                                            <option
                                                                                                value="CF - Cupom Fiscal"
                                                                                                {{ old('tipo_documento') == 'CF - Cupom Fiscal' ? 'selected' : '' }}>
                                                                                                CF - Cupom Fiscal
                                                                                            </option>
                                                                                            <option
                                                                                                value="DANF - Danfe"
                                                                                                {{ old('tipo_documento') == 'DANF - Danfe' ? 'selected' : '' }}>
                                                                                                DANF - Danfe</option>
                                                                                            <option
                                                                                                value="BOL - Boleto"
                                                                                                {{ old('tipo_documento') == 'BOL - Boleto' ? 'selected' : '' }}>
                                                                                                BOL - Boleto</option>
                                                                                            <option
                                                                                                value="REP - Repasse"
                                                                                                {{ old('tipo_documento') == 'REP - Repasse' ? 'selected' : '' }}>
                                                                                                REP - Repasse</option>
                                                                                            <option
                                                                                                value="CCRD - Cartão de Credito"
                                                                                                {{ old('tipo_documento') == 'CCRD - Cartão de Credito' ? 'selected' : '' }}>
                                                                                                CCRD - Cartão de Credito
                                                                                            </option>
                                                                                            <option
                                                                                                value="CDBT - Cartão de Debito"
                                                                                                {{ old('tipo_documento') == 'CDBT - Cartão de Debito' ? 'selected' : '' }}>
                                                                                                CDBT - Cartão de Debito
                                                                                            </option>
                                                                                            <option value="CH - Cheque"
                                                                                                {{ old('tipo_documento') == 'CH - Cheque' ? 'selected' : '' }}>
                                                                                                CH - Cheque</option>
                                                                                            <option
                                                                                                value="REC - Recibo"
                                                                                                {{ old('tipo_documento') == 'REC - Recibo' ? 'selected' : '' }}>
                                                                                                REC - Recibo</option>
                                                                                            <option
                                                                                                value="CARN - Carnê"
                                                                                                {{ old('tipo_documento') == 'CARN - Carnê' ? 'selected' : '' }}>
                                                                                                CARN - Carnê</option>
                                                                                            <option
                                                                                                value="FAT - Fatura"
                                                                                                {{ old('tipo_documento') == 'FAT - Fatura' ? 'selected' : '' }}>
                                                                                                FAT - Fatura</option>
                                                                                            <option
                                                                                                value="APOL - Apólice"
                                                                                                {{ old('tipo_documento') == 'APOL - Apólice' ? 'selected' : '' }}>
                                                                                                APOL - Apólice</option>
                                                                                            <option
                                                                                                value="DUPL - Duplicata"
                                                                                                {{ old('tipo_documento') == 'DUPL - Duplicata' ? 'selected' : '' }}>
                                                                                                DUPL - Duplicata
                                                                                            </option>
                                                                                            <option
                                                                                                value="TRIB - Tribunal"
                                                                                                {{ old('tipo_documento') == 'TRIB - Tribunal' ? 'selected' : '' }}>
                                                                                                TRIB - Tribunal</option>
                                                                                            <option value="Outros"
                                                                                                {{ old('tipo_documento') == 'Outros' ? 'selected' : '' }}>
                                                                                                Outros</option>
                                                                                            <option
                                                                                                value="T Banc - Transferência Bancaria"
                                                                                                {{ old('tipo_documento') == 'T Banc - Transferência Bancaria' ? 'selected' : '' }}>
                                                                                                T Banc - Transferência
                                                                                                Bancaria
                                                                                            </option>
                                                                                        </select>


                                                                                    </div>
                                                                                    {{-- @include('app.components.inputFile.inputFile') --}}
                                                                                </div>

                                                                                <!--begin::Footer-->

                                                                                <!--begin::Input group-->
                                                                                <div class="row mb-3">
                                                                                    <div class="col-md-12">
                                                                                        <!--begin::Input group-->
                                                                                        <div class="d-flex flex-stack">
                                                                                            <!--begin::Label-->
                                                                                            <div class="me-5">
                                                                                                <label
                                                                                                    class="fs-12 fw-semibold form-label">Existe
                                                                                                    comprovação
                                                                                                    fiscal?</label>
                                                                                                <div
                                                                                                    class="fs-7 fw-semibold text-muted">
                                                                                                    Documentos que
                                                                                                    comprovam
                                                                                                    transações
                                                                                                    financeiras
                                                                                                </div>
                                                                                            </div>
                                                                                            <!--end::Label-->
                                                                                            <!--begin::Switch-->
                                                                                            <label
                                                                                                class="form-check form-switch form-check-custom form-check-solid">
                                                                                                <!-- Checkbox para enviar 1 quando marcado -->
                                                                                                <input type="hidden"
                                                                                                    name="comprovacao_fiscal"
                                                                                                    value="0">

                                                                                                <input
                                                                                                    class="form-check-input"
                                                                                                    type="checkbox"
                                                                                                    name="comprovacao_fiscal"
                                                                                                    value="1" />
                                                                                                <span
                                                                                                    class="form-check-label fw-semibold text-muted">Possui
                                                                                                    Nota</span>
                                                                                            </label>
                                                                                            <!--end::Switch-->
                                                                                        </div>
                                                                                        <!--end::Input group-->
                                                                                        <!--end::Footer-->
                                                                                    </div>
                                                                                    <!--end::Label-->
                                                                                </div>
                                                                                <!--end::Input group-->
                                                                            </form>
                                                                        </div>
                                                                        <!-- Aba Transferência -->
                                                                        <div class="tab-pane fade"
                                                                            id="transferencia-{{ $conciliacao->id }}-pane"
                                                                            role="tabpanel"
                                                                            aria-labelledby="transferencia-{{ $conciliacao->id }}-tab">
                                                                            <p>Formulário de transferência...</p>
                                                                        </div>
                                                                        <!-- Aba Buscar/Criar Vários -->
                                                                        <div class="tab-pane fade"
                                                                            id="buscar-criar-{{ $conciliacao->id }}-pane"
                                                                            role="tabpanel"
                                                                            aria-labelledby="buscar-criar-{{ $conciliacao->id }}-tab">
                                                                            <p>Formulário para buscar/criar múltiplos
                                                                                lançamentos...</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end::Body-->
                                                            </div>
                                                            <!--end::Engage widget 11-->
                                                        </div>
                                                        <!--end::Col-->
                                                    @endif
                                                    <div class="separator separator-dashed border-muted my-5">
                                                    </div>
                                                </div>
                                                <!--end::Row-->
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Aba de Informações -->
                            <div class="tab-pane fade show" id="kt_tab_pane_informacao" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- Informações básicas do banco -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4>Dados Bancários</h4>
                                                <p><strong>Banco:</strong> {{ $entidade->nome }}</p>
                                                <p><strong>Agência:</strong> {{ $entidade->agencia }}</p>
                                                <p><strong>Conta:</strong> {{ $entidade->conta }}</p>
                                                <p><strong>Saldo Atual:</strong> R$
                                                    {{ number_format($entidade->saldo_atual, 2, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
</x-tenant-app-layout>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2 com placeholder
        $('.lancamento_padrao_banco').select2({
            placeholder: "Selecione um lançamento padrão", // Texto do placeholder
            allowClear: true // Permite limpar a seleção
        });

        // Filtrar as opções de cada select, para mostrar só 'entrada' OU 'saida'
        $('.lancamento_padrao_banco').each(function() {
            const tipoLancamento = $(this).closest('.row').find('.tipo-lancamento').val();

            $(this).find('option').each(function() {
                // 'data-type' em cada <option> do Lançamento
                const optType = $(this).data('type');

                // Se não coincide com o tipo da linha (entrada vs. saída), removemos
                if (optType !== tipoLancamento && $(this).val() !== '') {
                    $(this).remove();
                }
            });
        });
    });
</script>
