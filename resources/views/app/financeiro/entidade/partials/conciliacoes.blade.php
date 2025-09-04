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
                                                            @if ($entidade->bank && $entidade->bank->logo_path)
                                                                {{-- Usa o caminho do logo salvo no banco de dados --}}
                                                                <img src="{{ asset($entidade->bank->logo_path) }}"
                                                                    alt="{{ $entidade->bank->name }}" />
                                                            @else
                                                                {{-- Fallback: Mostra as iniciais do nome da entidade se não houver logo --}}
                                                                <span
                                                                    class="symbol-label bg-light-primary text-primary fs-6 fw-bold">{{ strtoupper(substr($entidade->nome, 0, 1)) }}</span>
                                                            @endif
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Details-->
                                                        <div class="ms-5">
                                                            <!--begin::Desc-->
                                                            <span class="text-muted fw-semibold mb-3">Lançamentos
                                                                Importantes</span>
                                                            <!--end::Desc-->
                                                            <!--begin::Name-->
                                                            <div class="d-flex align-items-center">
                                                                <a class="text-dark fw-bold text-hover-primary fs-5 me-4">{{ $entidade->nome }}</a>
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
                                            <!-- Exemplo de exibição de transações -->
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
                                                                                <span class="badge badge-success">✅
                                                                                    Conciliado</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'pendente')
                                                                                <span class="badge badge-warning">⏳
                                                                                    Pendente</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'parcial')
                                                                                <span class="badge badge-info">🟡
                                                                                    Parcial</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'divergente')
                                                                                <span class="badge badge-danger">❌
                                                                                    Divergente</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'ignorado')
                                                                                <span class="badge badge-secondary">🚫
                                                                                    Ignorado</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'ajustado')
                                                                                <span class="badge badge-primary">🔧
                                                                                    Ajustado</span>
                                                                            @elseif($conciliacao->status_conciliacao == 'em análise')
                                                                                <span class="badge badge-dark">🔍
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
                                                                <div
                                                                    class="card-body d-flex align-items-center justify-content-center h-100">
                                                                    <!-- Centraliza horizontal e verticalmente -->
                                                                    <button
                                                                        class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                                                        type="submit"
                                                                        form="form-{{ $conciliacao->id }}">
                                                                        <span class="fs-1 me-2">🫱🏻‍🫲🏽</span>
                                                                        <!-- Emoji -->
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
                                                                <div id="viewData-{{ $conciliacao->id }}">
                                                                    <form id="form-{{ $conciliacao->id }}"
                                                                        action="{{ route('conciliacao.pivot') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="bank_statement_id"
                                                                            value="{{ $conciliacao->id }}">
                                                                        <input type="hidden"
                                                                            name="transacao_financeira_id"
                                                                            value="{{ $transacaoSugerida->id }}">
                                                                        <input type="hidden" name="valor"
                                                                            value="{{ $transacaoSugerida->valor }}">

                                                                        <div
                                                                            class="card card-flush border border-hover-primary py-4 p-7 rounded flex-row-fluid overflow-hidden mb-3 h-xl-100">
                                                                            <span
                                                                                style="background-color: #fff3cd; padding: 6px 12px; border-radius: 4px; border-left: 4px solid #ffc107; color: black;">
                                                                                ⚠️ <strong>Encontramos um lançamento que
                                                                                    parece corresponder:</strong>
                                                                            </span>
                                                                            <hr>

                                                                            <div class="d-flex flex-stack pb-3">
                                                                                <div class="d-flex">
                                                                                    <div class="">
                                                                                        <div
                                                                                            class="d-flex align-items-center">
                                                                                            <a href="#"
                                                                                                class="text-dark fw-bold text-hover-primary fs-5 me-4">
                                                                                                {{ \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('d/m/Y') }}
                                                                                            </a>
                                                                                        </div>
                                                                                        <span
                                                                                            class="text-muted fw-semibold mb-3">
                                                                                            @if ($transacaoSugerida->tipo == 'entrada')
                                                                                                <span
                                                                                                    style="color: green;">Receita</span>
                                                                                            @elseif($transacaoSugerida->tipo == 'saida')
                                                                                                <span
                                                                                                    class="text-danger">Despesa</span>
                                                                                            @else
                                                                                                <span>Tipo não
                                                                                                    identificado</span>
                                                                                            @endif
                                                                                        </span>
                                                                                    </div>
                                                                                </div>

                                                                                <div clas="d-flex">
                                                                                    <div class="text-end pb-3">
                                                                                        @if ($transacaoSugerida->tipo == 'entrada')
                                                                                            <span class="fw-bold fs-5"
                                                                                                style="color: green;">
                                                                                                R$
                                                                                                {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                                                        @elseif($transacaoSugerida->tipo == 'saida')
                                                                                            <span
                                                                                                class="fw-bold fs-5 text-danger">R$
                                                                                                {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="p-0">
                                                                                <p
                                                                                    class="text-gray-700 fw-semibold fs-6 mb-4">
                                                                                    <strong>Descrição:</strong>
                                                                                    {{ $transacaoSugerida->descricao }}
                                                                                </p>

                                                                                <div class="d-flex flex-stack">
                                                                                    <div
                                                                                        class="d-flex flex-column mw-200px">
                                                                                        <div
                                                                                            class="d-flex align-items-center mb-2">
                                                                                            <span
                                                                                                class="text-gray-700 fs-6 fw-semibold me-2">{{ $percentualConciliado }}%</span>
                                                                                            <span
                                                                                                class="text-muted fs-8">Conciliação
                                                                                                Bancária</span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="progress h-6px w-200px">
                                                                                            <div class="progress-bar bg-primary"
                                                                                                role="progressbar"
                                                                                                style="width: {{ $percentualConciliado }}%"
                                                                                                aria-valuenow="{{ $percentualConciliado }}"
                                                                                                aria-valuemin="0"
                                                                                                aria-valuemax="100">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="d-flex gap-2">
                                                                                        <a href="#"
                                                                                            class="btn btn-sm btn-primary"
                                                                                            onclick="toggleEdit({{ $conciliacao->id }})">✏️
                                                                                            Editar</a>
                                                                                        <a href="#"
                                                                                            class="btn btn-sm btn-warning">⛓️‍💥
                                                                                            Desvincular</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>

                                                                <!-- Formulário de Edição -->
                                                                <div id="editForm-{{ $conciliacao->id }}"
                                                                    class="d-none">
                                                                    <div
                                                                        class="card card-flush py-4 flex-row-fluid overflow-hidden p-5 border">
                                                                        <div
                                                                            class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-4 mb-10">
                                                                            <div class="d-flex flex-column pe-3">
                                                                                <!-- Ícone de atenção (opcional) -->
                                                                                <span
                                                                                    class="svg-icon svg-icon-2hx svg-icon-warning me-4 mb-2">
                                                                                    ⚠️ <!-- Ícone do FontAwesome -->
                                                                                    <span
                                                                                        class="fs-6 fw-bold">Atenção:</span>
                                                                                </span>
                                                                                <!-- Mensagem principal -->
                                                                                <span class="fs-6">Os dados do
                                                                                    lançamento devem ser
                                                                                    <strong>iguais</strong> aos da
                                                                                    conciliação.</span>
                                                                            </div>
                                                                        </div>
                                                                        <form
                                                                            id="formularioEdicao-{{ $conciliacao->id }}"
                                                                            action="{{ route('conciliacao.update', $transacaoSugerida->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('PUT')

                                                                            <div class="row mb-3">
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        for="data_competencia-{{ $conciliacao->id }}"
                                                                                        class="required form-label fw-semibold">Data</label>
                                                                                    <input type="date"
                                                                                        class="form-control"
                                                                                        id="data_competencia-{{ $conciliacao->id }}"
                                                                                        name="data_competencia"
                                                                                        value="{{ old('data_competencia', \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('Y-m-d')) }}">
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        for="valor2-{{ $conciliacao->id }}"
                                                                                        class="required form-label fw-semibold">Valor</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="valor2-{{ $conciliacao->id }}"
                                                                                        name="valor"
                                                                                        value="{{ old('valor', number_format($transacaoSugerida->valor, 2, ',', '.')) }}">
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-3">
                                                                                <div class="col-md-4">
                                                                                    <input type="hidden"
                                                                                        name="bank_statement_id"
                                                                                        value="{{ $conciliacao->id }}">
                                                                                    <label
                                                                                        for="numero_documento-{{ $conciliacao->id }}"
                                                                                        class="required form-label fw-semibold">Código</label>
                                                                                    <input type="text"
                                                                                        name="numero_documento"
                                                                                        class="form-control"
                                                                                        id="numero_documento-{{ $conciliacao->id }}"
                                                                                        value="{{ old('numero_documento', $conciliacao->checknum) }}">
                                                                                </div>

                                                                                <div class="col-md-8 mb-5">
                                                                                    <label
                                                                                        for="descricao-{{ $conciliacao->id }}"
                                                                                        class="required form-label fw-semibold">Descrição</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="descricao-{{ $conciliacao->id }}"
                                                                                        name="descricao"
                                                                                        value="{{ old('descricao', $transacaoSugerida->descricao) }}">
                                                                                </div>
                                                                            </div>

                                                                            <div class="d-flex gap-2">
                                                                                <a href="#"
                                                                                    class="btn btn-sm btn-success"
                                                                                    onclick="document.getElementById('formularioEdicao-{{ $conciliacao->id }}').submit();">
                                                                                    💾 Salvar
                                                                                </a>
                                                                                <a href="#"
                                                                                    class="btn btn-sm btn-secondary"
                                                                                    onclick="toggleEdit({{ $conciliacao->id }})">❌
                                                                                    Cancelar</a>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                                <!-- Script para alternar entre visualização e edição -->
                                                                <script>
                                                                    function toggleEdit(id) {
                                                                        var viewDiv = document.getElementById('viewData-' + id);
                                                                        var editDiv = document.getElementById('editForm-' + id);

                                                                        if (editDiv.classList.contains('d-none')) {
                                                                            viewDiv.classList.add('d-none');
                                                                            editDiv.classList.remove('d-none');

                                                                            // Mantém a posição do scroll no local do formulário
                                                                            editDiv.scrollIntoView({
                                                                                behavior: 'smooth',
                                                                                block: 'nearest'
                                                                            });
                                                                        } else {
                                                                            editDiv.classList.add('d-none');
                                                                            viewDiv.classList.remove('d-none');

                                                                            // Mantém a posição no mesmo lugar ao voltar para a visualização
                                                                            viewDiv.scrollIntoView({
                                                                                behavior: 'smooth',
                                                                                block: 'nearest'
                                                                            });
                                                                        }
                                                                    }
                                                                </script>


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
                                                                <div
                                                                    class="card-body d-flex align-items-center justify-content-center h-100">
                                                                    <button
                                                                        class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                                                        type="submit" form="{{ $conciliacao->id }}">
                                                                        <span class="fs-1 me-2">🫱🏻‍🫲🏽</span>
                                                                        <!-- Emoji com tamanho ajustado -->
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
                                                                                    <!-- Campo Descrição -->
                                                                                    <div class="col-md-6">
                                                                                        <label for="descricao"
                                                                                            class="required form-label fw-semibold">Descrição</label>
                                                                                        <input type="text"
                                                                                            value="{{ old('descricao', $conciliacao->memo) }}"
                                                                                            class="form-control @error('descricao') is-invalid @enderror"
                                                                                            name="descricao2"
                                                                                            placeholder="Ex: PAYMENT - Fulano">

                                                                                        <!-- Exibir erro abaixo do campo -->
                                                                                        @error('descricao')
                                                                                            <div class="invalid-feedback">
                                                                                                {{ $message }}
                                                                                            </div>
                                                                                        @enderror
                                                                                    </div>

                                                                                    <!-- Campo Centro de Custo -->
                                                                                    <div class="col-md-6">

                                                                                        <label for="categoria"
                                                                                            class="required form-label fw-semibold">Centro de Custo</label>
                                                                                        <select name="cost_center_id"
                                                                                            id="banco_id"
                                                                                            class="form-select form-select-solid @error('cost_center_id') is-invalid @enderror"
                                                                                            data-control="select2"
                                                                                            data-dropdown-css-class="auto"
                                                                                            data-placeholder="Selecione o Centro de Custo">
                                                                                            @foreach ($centrosAtivos as $centrosAtivo)
                                                                                                <option
                                                                                                    value="{{ $centrosAtivo->id }}"
                                                                                                    {{ old('cost_center_id') == $centrosAtivo->id }}>
                                                                                                    {{ $centrosAtivo->name }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>

                                                                                        <!-- Exibir erro abaixo do campo -->
                                                                                        @error('cost_center_id')
                                                                                            <div class="invalid-feedback">
                                                                                                {{ $message }}
                                                                                            </div>
                                                                                        @enderror
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
                                                                                            value="Conciliação Bancária">
                                                                                        <input type="hidden"
                                                                                            name="entidade_id"
                                                                                            class=""
                                                                                            value="{{ $entidade->id }}">
                                                                                        <input type="hidden"
                                                                                            name="bank_statement_id"
                                                                                            value="{{ $conciliacao->id }}">


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
                                                                                <!-- Exemplo para UM item de conciliação -->
                                                                                <div class="row mb-3">
                                                                                    <div class="col-md-12 mb-5">
                                                                                        <!--begin::Input group-->
                                                                                        <div class="d-flex flex-stack">
                                                                                            <!--begin::Label-->
                                                                                            <div class="me-5">
                                                                                                <label
                                                                                                    class="fs-12 fw-semibold form-label">
                                                                                                    Existe comprovação
                                                                                                    fiscal para
                                                                                                    {{ $conciliacao->id }}?
                                                                                                </label>
                                                                                                <div
                                                                                                    class="fs-7 fw-semibold text-muted">
                                                                                                    Documentos que
                                                                                                    comprovam transações
                                                                                                    financeiras
                                                                                                </div>
                                                                                            </div>
                                                                                            <!--end::Label-->

                                                                                            <!--begin::Switch-->
                                                                                            <label
                                                                                                class="form-check form-switch form-check-custom form-check-solid">
                                                                                                <span
                                                                                                    class="form-check-label fw-semibold text-muted">Possui
                                                                                                    Nota</span>

                                                                                                <!-- Hidden default 0 -->
                                                                                                <input type="hidden"
                                                                                                    name="comprovacao_fiscal"
                                                                                                    value="0">

                                                                                                <!-- Checkbox -->
                                                                                                <input
                                                                                                    class="form-check-input"
                                                                                                    type="checkbox"
                                                                                                    name="comprovacao_fiscal"
                                                                                                    id="comprovacaoFiscalCheckbox_{{ $conciliacao->id }}"
                                                                                                    value="1" />
                                                                                                <span
                                                                                                    class="form-check-label fw-semibold text-muted">
                                                                                                    Possui Nota?
                                                                                                </span>
                                                                                            </label>
                                                                                            <!--end::Switch-->
                                                                                        </div>
                                                                                        <!--end::Input group-->
                                                                                    </div>

                                                                                    <!-- Aqui entra o container que só aparece caso o checkbox seja marcado -->
                                                                                    <div class="col-md-12"
                                                                                        id="anexoInputContainer_{{ $conciliacao->id }}"
                                                                                        style="display: none;">
                                                                                        <input type="file"
                                                                                            name="files_{{ $conciliacao->id }}[]"
                                                                                            id="photos_{{ $conciliacao->id }}" />

                                                                                        <script>
                                                                                            // Inicializa o KendoUpload nesse ID específico
                                                                                            $(document).ready(function() {
                                                                                                $("#photos_{{ $conciliacao->id }}").kendoUpload({
                                                                                                    async: {
                                                                                                        removeUrl: "{{ url('/remove') }}",
                                                                                                        removeField: "path",
                                                                                                        withCredentials: false
                                                                                                    },
                                                                                                    multiple: true,
                                                                                                    validation: {
                                                                                                        allowedExtensions: ["jpg", "jpeg", "png", "pdf", "page"],
                                                                                                        maxFileSize: 5242880, // 5 MB
                                                                                                        minFileSize: 1024 // 1 KB
                                                                                                    },
                                                                                                    localization: {
                                                                                                        uploadSuccess: "Upload bem-sucedido!",
                                                                                                        uploadFail: "Falha no upload",
                                                                                                        invalidFileExtension: "Tipo de arquivo não permitido",
                                                                                                        invalidMaxFileSize: "O arquivo é muito grande",
                                                                                                        invalidMinFileSize: "O arquivo é muito pequeno",
                                                                                                        select: "Anexar Arquivos"
                                                                                                    }
                                                                                                });
                                                                                            });
                                                                                        </script>
                                                                                    </div>
                                                                                </div>

                                                                                <script>
                                                                                    document.addEventListener('DOMContentLoaded', function() {
                                                                                        const checkbox = document.getElementById('comprovacaoFiscalCheckbox_{{ $conciliacao->id }}');
                                                                                        const anexoInputContainer = document.getElementById('anexoInputContainer_{{ $conciliacao->id }}');

                                                                                        checkbox.addEventListener('change', function() {
                                                                                            if (this.checked) {
                                                                                                anexoInputContainer.style.display = 'block';
                                                                                            } else {
                                                                                                anexoInputContainer.style.display = 'none';
                                                                                            }
                                                                                        });
                                                                                    });
                                                                                </script>

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

                                            <!-- Botões de paginação -->
                                            {{ $conciliacoesPendentes->links('pagination::bootstrap-5') }}

                                        @endif
                                    </div>
                                </div>
                            </div>

