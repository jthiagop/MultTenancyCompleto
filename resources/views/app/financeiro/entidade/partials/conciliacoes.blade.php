                            <div class="tab-pane fade show active" id="kt_tab_pane_conciliacoes" role="tabpanel">
                                <div class="card mt-5">
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
                                                                <img src="{{ $entidade->bank->logo_path }}"
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
                                                                <a
                                                                    class="text-dark fw-bold text-hover-primary fs-5 me-4">{{ $entidade->nome }}</a>
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
                                                                        action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="bank_statement_id"
                                                                            value="{{ $conciliacao->id }}">
                                                                        @if ($transacaoSugerida)
                                                                        <input type="hidden"
                                                                            name="transacao_financeira_id"
                                                                            value="{{ $transacaoSugerida->id }}">
                                                                        <input type="hidden" name="valor"
                                                                            value="{{ $transacaoSugerida->valor }}">
                                                                        @endif

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
                                                                                                {{ $transacaoSugerida ? \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('d/m/Y') : 'N/A' }}
                                                                                            </a>
                                                                                        </div>
                                                                                        <span
                                                                                            class="text-muted fw-semibold mb-3">
                                                                                            @if ($transacaoSugerida && $transacaoSugerida->tipo == 'entrada')
                                                                                                <span
                                                                                                    style="color: green;">Receita</span>
                                                                                            @elseif($transacaoSugerida && $transacaoSugerida->tipo == 'saida')
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
                                                                                        @if ($transacaoSugerida && $transacaoSugerida->tipo == 'entrada')
                                                                                            <span class="fw-bold fs-5"
                                                                                                style="color: green;">
                                                                                                R$
                                                                                                {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                                                        @elseif($transacaoSugerida && $transacaoSugerida->tipo == 'saida')
                                                                                            <span
                                                                                                class="fw-bold fs-5 text-danger">R$
                                                                                                {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                                                        @else
                                                                                            <span
                                                                                                class="fw-bold fs-5 text-muted">R$
                                                                                                {{ number_format($conciliacao->amount, 2, ',', '.') }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="p-0">
                                                                                <p
                                                                                    class="text-gray-700 fw-semibold fs-6 mb-4">
                                                                                    <strong>Descrição:</strong>
                                                                                    {{ $transacaoSugerida->descricao ?? 'Nenhuma descrição disponível' }}
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
                                                                            action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}"
                                                                            method="POST">
                                                                            @csrf

                                                                            <div class="row mb-3">
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        for="data_competencia-{{ $conciliacao->id }}"
                                                                                        class="required form-label fw-semibold">Data</label>
                                                                                    <input type="date"
                                                                                        class="form-control"
                                                                                        id="data_competencia-{{ $conciliacao->id }}"
                                                                                        name="data_competencia"
                                                                                        value="{{ old('data_competencia', $transacaoSugerida ? \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        for="valor2-{{ $conciliacao->id }}"
                                                                                        class="required form-label fw-semibold">Valor</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="valor2-{{ $conciliacao->id }}"
                                                                                        name="valor"
                                                                                        value="{{ old('valor', $transacaoSugerida ? number_format($transacaoSugerida->valor, 2, ',', '.') : number_format($conciliacao->amount, 2, ',', '.')) }}">
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-3">
                                                                                <div class="col-md-4">
                                                                                    <input type="hidden"
                                                                                        name="bank_statement_id"
                                                                                        value="{{ $conciliacao->id }}">
                                                                                    @if ($transacaoSugerida)
                                                                                    <input type="hidden"
                                                                                        name="transacao_financeira_id"
                                                                                        value="{{ $transacaoSugerida->id }}">
                                                                                    <input type="hidden"
                                                                                        name="valor_conciliado"
                                                                                        value="{{ $transacaoSugerida->valor }}">
                                                                                    @endif
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
                                                                                        value="{{ old('descricao', $transacaoSugerida->descricao ?? $conciliacao->memo) }}">
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
                                                                        type="button"
                                                                        id="btn-conciliar-{{ $conciliacao->id }}">
                                                                        <span class="fs-1 me-2">🫱🏻‍🫲🏽</span>
                                                                        <!-- Emoji com tamanho ajustado -->
                                                                        <span
                                                                            id="btn-conciliar-text-{{ $conciliacao->id }}">Conciliar</span>
                                                                        <!-- Texto -->
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
                                                                                action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}"
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
                                                                                            class="required form-label fw-semibold">Centro
                                                                                            de Custo</label>
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
                                                                                        @if ($transacaoSugerida)
                                                                                        <input type="hidden"
                                                                                            name="transacao_financeira_id"
                                                                                            value="{{ $transacaoSugerida->id }}">
                                                                                        <input type="hidden"
                                                                                            name="valor_conciliado"
                                                                                            value="{{ $transacaoSugerida->valor }}">
                                                                                        @endif


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
                                                                                                    fiscal
                                                                                                    para{{ $conciliacao->id }}?
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
                                                                                        <x-anexos-input name="anexos" :anexosExistentes="[]" />
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
                                                                            <form
                                                                                id="form-transferencia-{{ $conciliacao->id }}"
                                                                                action="{{ route('conciliacao.transferir') }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <input type="hidden"
                                                                                    name="bank_statement_id"
                                                                                    value="{{ $conciliacao->id }}">
                                                                                <input type="hidden"
                                                                                    name="entidade_origem_id"
                                                                                    value="{{ $entidade->id }}">

                                                                                <div class="row mb-5">
                                                                                    <!-- Conta de Origem (readonly) -->
                                                                                    <div class="col-md-6">
                                                                                        <label
                                                                                            class="form-label fw-semibold required">Conta
                                                                                            de Origem</label>
                                                                                        <input type="text"
                                                                                            class="form-control form-control-solid"
                                                                                            value="{{ $entidade->nome }}"
                                                                                            readonly disabled>
                                                                                        <div class="form-text">Conta
                                                                                            atual sendo conciliada</div>
                                                                                    </div>

                                                                                    <!-- Conta de Destino -->
                                                                                    <div class="col-md-6">
                                                                                        <label
                                                                                            for="entidade_destino_id_{{ $conciliacao->id }}"
                                                                                            class="form-label fw-semibold required">Conta
                                                                                            de Destino</label>
                                                                                        <select
                                                                                            name="entidade_destino_id"
                                                                                            id="entidade_destino_id_{{ $conciliacao->id }}"
                                                                                            class="form-select form-select-solid"
                                                                                            data-control="select2"
                                                                                            data-placeholder="Selecione a conta de destino"
                                                                                            required>
                                                                                            <option value="">
                                                                                                Carregando contas...
                                                                                            </option>
                                                                                        </select>
                                                                                        <div class="form-text">
                                                                                            Selecione para onde
                                                                                            transferir o valor</div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-5">
                                                                                    <!-- Valor da Transferência -->
                                                                                    <div class="col-md-6">
                                                                                        <label
                                                                                            for="valor_transferencia_{{ $conciliacao->id }}"
                                                                                            class="form-label fw-semibold required">Valor
                                                                                            da Transferência</label>
                                                                                        <div class="input-group">
                                                                                            <span
                                                                                                class="input-group-text">R$</span>
                                                                                            <input type="text"
                                                                                                id="valor_transferencia_{{ $conciliacao->id }}"
                                                                                                class="form-control form-control-solid"
                                                                                                value="{{ number_format(abs($conciliacao->amount), 2, ',', '.') }}"
                                                                                                placeholder="0,00"
                                                                                                readonly
                                                                                                style="background-color: #f1f1f1; cursor: not-allowed;"
                                                                                                disabled>
                                                                                            <!-- Campo hidden com valor numérico puro para validação e envio -->
                                                                                            <input type="hidden"
                                                                                                name="valor"
                                                                                                value="{{ abs($conciliacao->amount) }}">
                                                                                        </div>
                                                                                        <div class="form-text">Valor a
                                                                                            ser transferido entre as
                                                                                            contas (não editável)</div>
                                                                                    </div>

                                                                                    <!-- Data da Transferência -->
                                                                                    <div class="col-md-6">
                                                                                        <label
                                                                                            for="data_transferencia_{{ $conciliacao->id }}"
                                                                                            class="form-label fw-semibold required">Data
                                                                                            da Transferência</label>
                                                                                        <input type="date"
                                                                                            name="data_transferencia"
                                                                                            id="data_transferencia_{{ $conciliacao->id }}"
                                                                                            class="form-control form-control-solid"
                                                                                            value="{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}"
                                                                                            readonly
                                                                                            style="background-color: #f1f1f1; cursor: not-allowed;"
                                                                                            required>
                                                                                        <div class="form-text">Data do
                                                                                            lançamento bancário (não
                                                                                            editável)</div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-5">
                                                                                    <!-- Lançamento Padrão -->
                                                                                    <div class="col-md-6">
                                                                                        <label
                                                                                            for="lancamento_padrao_id_transferencia_{{ $conciliacao->id }}"
                                                                                            class="form-label fw-semibold required">Lançamento
                                                                                            Padrão</label>
                                                                                        <select
                                                                                            name="lancamento_padrao_id"
                                                                                            id="lancamento_padrao_id_transferencia_{{ $conciliacao->id }}"
                                                                                            class="form-select form-select-solid"
                                                                                            data-control="select2"
                                                                                            data-placeholder="Selecione o lançamento padrão"
                                                                                            required>
                                                                                            <option value="">
                                                                                                Selecione...</option>
                                                                                            @foreach ($lps as $lp)
                                                                                                @if (
                                                                                                    $lp->type === 'ambos' ||
                                                                                                        str_contains(strtolower($lp->description), 'transferência') ||
                                                                                                        str_contains(strtolower($lp->description), 'transferencia'))
                                                                                                    <option
                                                                                                        value="{{ $lp->id }}"
                                                                                                        {{ old('lancamento_padrao_id') == $lp->id ? 'selected' : '' }}>
                                                                                                        {{ $lp->description }}
                                                                                                    </option>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </select>
                                                                                        <div class="form-text">
                                                                                            Selecione um lançamento
                                                                                            padrão do tipo "Ambos" ou
                                                                                            relacionado a transferências
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-- Descrição -->
                                                                                    <div class="col-md-6">
                                                                                        <label
                                                                                            for="descricao_transferencia_{{ $conciliacao->id }}"
                                                                                            class="form-label fw-semibold">Descrição</label>
                                                                                        <textarea name="descricao" id="descricao_transferencia_{{ $conciliacao->id }}"
                                                                                            class="form-control form-control-solid" rows="3"
                                                                                            placeholder="Ex: Transferência automática entre contas - {{ $conciliacao->memo }}">{{ $conciliacao->memo ? 'Transferência: ' . $conciliacao->memo : '' }}</textarea>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Botão removido - usando o botão "Conciliar" centralizado -->
                                                                            </form>

                                                                            <script>
                                                                                $(document).ready(function() {
                                                                                    // Carrega as contas disponíveis ao abrir a aba
                                                                                    const conciliacaoId = {{ $conciliacao->id }};
                                                                                    const entidadeOrigemId = {{ $entidade->id }};
                                                                                    const selectDestino = $('#entidade_destino_id_' + conciliacaoId);

                                                                                    // Carrega contas quando a aba é mostrada
                                                                                    $('#transferencia-' + conciliacaoId + '-tab').on('shown.bs.tab', function() {
                                                                                        if (selectDestino.find('option').length <= 1) {
                                                                                            carregarContasDisponiveis(conciliacaoId, entidadeOrigemId);
                                                                                        }
                                                                                    });

                                                                                    // Função para carregar contas disponíveis
                                                                                    function carregarContasDisponiveis(conciliacaoId, entidadeOrigemId) {
                                                                                        selectDestino.html('<option value="">Carregando...</option>');

                                                                                        $.ajax({
                                                                                            url: '{{ route('conciliacao.contas-disponiveis') }}',
                                                                                            method: 'GET',
                                                                                            data: {
                                                                                                entidade_origem_id: entidadeOrigemId,
                                                                                                bank_statement_id: conciliacaoId
                                                                                            },
                                                                                            success: function(response) {
                                                                                                if (response.success && response.contas) {
                                                                                                    selectDestino.html(
                                                                                                    '<option value="">Selecione a conta de destino</option>');

                                                                                                    response.contas.forEach(function(conta) {
                                                                                                        const option = $('<option></option>')
                                                                                                            .attr('value', conta.id)
                                                                                                            .text(conta.nome + (conta.account_type ? ' - ' + conta
                                                                                                                .account_type_label : ''));
                                                                                                        selectDestino.append(option);
                                                                                                    });

                                                                                                    // Inicializa Select2
                                                                                                    if (selectDestino.hasClass('select2-hidden-accessible')) {
                                                                                                        selectDestino.select2('destroy');
                                                                                                    }
                                                                                                    selectDestino.select2({
                                                                                                        placeholder: "Selecione a conta de destino",
                                                                                                        allowClear: true
                                                                                                    });
                                                                                                } else {
                                                                                                    selectDestino.html('<option value="">Nenhuma conta disponível</option>');
                                                                                                }
                                                                                            },
                                                                                            error: function(xhr) {
                                                                                                console.error('Erro ao carregar contas:', xhr);
                                                                                                selectDestino.html('<option value="">Erro ao carregar contas</option>');
                                                                                            }
                                                                                        });
                                                                                    }

                                                                                    // Campo de valor não é mais editável, então não precisa de máscara
                                                                                });

                                                                                // Controla o comportamento do botão "Conciliar" baseado na aba ativa
                                                                                $(document).ready(function() {
                                                                                    const conciliacaoId = {{ $conciliacao->id }};
                                                                                    const btnConciliar = $('#btn-conciliar-' + conciliacaoId);
                                                                                    const btnConciliarText = $('#btn-conciliar-text-' + conciliacaoId);
                                                                                    const formNovoLancamento = $('#' + conciliacaoId);
                                                                                    const formTransferencia = $('#form-transferencia-' + conciliacaoId);

                                                                                    // Função para atualizar o botão baseado na aba ativa
                                                                                    function atualizarBotaoConciliar() {
                                                                                        // Verifica qual aba está ativa usando múltiplas formas
                                                                                        const tabNovoLancamento = $('#novo-lancamento-' + conciliacaoId + '-tab');
                                                                                        const tabTransferencia = $('#transferencia-' + conciliacaoId + '-tab');

                                                                                        const abaNovoLancamento = $('#novo-lancamento-' + conciliacaoId + '-pane');
                                                                                        const abaTransferencia = $('#transferencia-' + conciliacaoId + '-pane');

                                                                                        // Verifica qual tab button está ativo usando múltiplas formas
                                                                                        const tabNovoLancamentoAtivo = tabNovoLancamento.hasClass('active') ||
                                                                                            tabNovoLancamento.attr('aria-selected') === 'true' ||
                                                                                            (abaNovoLancamento.hasClass('active') && abaNovoLancamento.hasClass('show'));

                                                                                        const tabTransferenciaAtivo = tabTransferencia.hasClass('active') ||
                                                                                            tabTransferencia.attr('aria-selected') === 'true' ||
                                                                                            (abaTransferencia.hasClass('active') && abaTransferencia.hasClass('show')) ||
                                                                                            abaTransferencia.hasClass('show');

                                                                                        // Prioriza Transferência se ambas estiverem ativas (não deveria acontecer, mas por segurança)
                                                                                        if (tabTransferenciaAtivo) {
                                                                                            // Aba "Transferência" está ativa
                                                                                            btnConciliarText.text('Realizar Transferência');
                                                                                            btnConciliar.attr('form', 'form-transferencia-' + conciliacaoId);
                                                                                        } else if (tabNovoLancamentoAtivo) {
                                                                                            // Aba "Novo Lançamento" está ativa
                                                                                            btnConciliarText.text('Conciliar');
                                                                                            btnConciliar.attr('form', conciliacaoId);
                                                                                        } else {
                                                                                            // Outra aba (Buscar/Criar vários)
                                                                                            btnConciliarText.text('Conciliar');
                                                                                            btnConciliar.removeAttr('form');
                                                                                        }
                                                                                    }

                                                                                    // Atualiza quando uma aba é mostrada (evento do Bootstrap)
                                                                                    $('#novo-lancamento-' + conciliacaoId + '-tab, #transferencia-' + conciliacaoId +
                                                                                        '-tab, #buscar-criar-' + conciliacaoId + '-tab').on('shown.bs.tab', function(e) {
                                                                                        atualizarBotaoConciliar();
                                                                                    });

                                                                                    // Atualiza quando a aba é clicada (com pequeno delay para garantir que Bootstrap processou)
                                                                                    $('#novo-lancamento-' + conciliacaoId + '-tab, #transferencia-' + conciliacaoId +
                                                                                        '-tab, #buscar-criar-' + conciliacaoId + '-tab').on('click', function(e) {
                                                                                        // Usa um pequeno delay para garantir que o Bootstrap processou a mudança
                                                                                        setTimeout(function() {
                                                                                            atualizarBotaoConciliar();
                                                                                        }, 50);
                                                                                    });

                                                                                    // Inicializa o botão na carga da página
                                                                                    atualizarBotaoConciliar();

                                                                                    // Converte o botão para submit quando clicado
                                                                                    btnConciliar.on('click', function(e) {
                                                                                        e.preventDefault();

                                                                                        // Atualiza o botão antes de submeter (garante que está correto)
                                                                                        atualizarBotaoConciliar();

                                                                                        const formId = $(this).attr('form');

                                                                                        if (formId) {
                                                                                            const form = $('#' + formId);

                                                                                            if (form.length) {
                                                                                                // Sincroniza Select2 antes de validar
                                                                                                form.find('select[data-control="select2"]').each(function() {
                                                                                                    const $select = $(this);
                                                                                                    if ($select.hasClass('select2-hidden-accessible')) {
                                                                                                        const selectedValue = $select.val();
                                                                                                        $select.data('select2').$container.find(
                                                                                                            '.select2-selection__rendered').attr('title', $select.find(
                                                                                                            'option:selected').text());
                                                                                                    }
                                                                                                });

                                                                                                // Valida o formulário antes de submeter
                                                                                                if (form[0].checkValidity()) {
                                                                                                    form.submit();
                                                                                                } else {
                                                                                                    // Mostra quais campos estão inválidos
                                                                                                    const invalidFields = form[0].querySelectorAll(':invalid');
                                                                                                    console.log('Campos inválidos:', Array.from(invalidFields).map(f => ({
                                                                                                        id: f.id,
                                                                                                        name: f.name,
                                                                                                        validationMessage: f.validationMessage
                                                                                                    })));
                                                                                                    form[0].reportValidity();
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    });
                                                                                });
                                                                            </script>
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
