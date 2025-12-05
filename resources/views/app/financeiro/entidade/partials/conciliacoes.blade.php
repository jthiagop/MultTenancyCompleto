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
                                    <img src="{{ $entidade->bank->logo_path }}" alt="{{ $entidade->bank->name }}" />
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
                                <img src="/assets/media/avatars/300-35.png" alt="" />
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
                                    <a class="text-dark fw-bold text-hover-primary fs-5 me-4">Dominus Sistema</a>
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
                                                        <span class="text-dark fw-bold text-hover-primary fs-5 me-4">
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
                                                    <span class="text-muted fw-semibold mb-3">
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
                                                <p class="text-gray-700 fw-semibold fs-6 mb-4">
                                                    {{ $conciliacao->memo }}
                                                </p>
                                            </div>
                                            <div>
                                                @if ($conciliacao->status_conciliacao == 'ok')
                                                    <span class="badge badge-success">✅
                                                        Conciliado</span>
                                                @elseif($conciliacao->status_conciliacao == 'pendente')
                                                    <span class="badge badge-warning">⏳
                                                        <span class="text-black">Pendente</span>
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
                                            </div>

                                            <div class="d-flex flex-column">
                                                <div class="separator separator-dashed border-muted my-5">
                                                </div>
                                                <div class="d-flex flex-stack">

                                                    <div class="d-flex flex-column mw-200px">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="text-gray-700 fs-6 fw-semibold me-2">
                                                                Importado via OFX
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Botão "Ignorar" (se houver essa funcionalidade) -->
                                                    <form action="{{ route('conciliacao.ignorar', $conciliacao->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-secondary">
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
                                    <div class="card-body d-flex align-items-center justify-content-center h-100">
                                        <!-- Centraliza horizontal e verticalmente -->
                                        <button class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                            type="submit" form="form-{{ $conciliacao->id }}">
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
                                                <input type="hidden" name="transacao_financeira_id"
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
                                                            <div class="d-flex align-items-center">
                                                                <a href="#"
                                                                    class="text-dark fw-bold text-hover-primary fs-5 me-4">
                                                                    {{ $transacaoSugerida ? \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('d/m/Y') : 'N/A' }}
                                                                </a>
                                                            </div>
                                                            <span class="text-muted fw-semibold mb-3">
                                                                @if ($transacaoSugerida && $transacaoSugerida->tipo == 'entrada')
                                                                    <span style="color: green;">Receita</span>
                                                                @elseif($transacaoSugerida && $transacaoSugerida->tipo == 'saida')
                                                                    <span class="text-danger">Despesa</span>
                                                                @else
                                                                    <span>Tipo não
                                                                        identificado</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex">
                                                        <div class="text-end pb-3">
                                                            @if ($transacaoSugerida && $transacaoSugerida->tipo == 'entrada')
                                                                <span class="fw-bold fs-5" style="color: green;">
                                                                    R$
                                                                    {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                            @elseif($transacaoSugerida && $transacaoSugerida->tipo == 'saida')
                                                                <span class="fw-bold fs-5 text-danger">R$
                                                                    {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                            @else
                                                                <span class="fw-bold fs-5 text-muted">R$
                                                                    {{ number_format($conciliacao->amount, 2, ',', '.') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="p-0">
                                                    <p class="text-gray-700 fw-semibold fs-6 mb-4">
                                                        <strong>Descrição:</strong>
                                                        {{ $transacaoSugerida->descricao ?? 'Nenhuma descrição disponível' }}
                                                    </p>

                                                    <div class="d-flex flex-stack">
                                                        <div class="d-flex flex-column mw-200px">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <span
                                                                    class="text-gray-700 fs-6 fw-semibold me-2">{{ $percentualConciliado }}%</span>
                                                                <span class="text-muted fs-8">Conciliação
                                                                    Bancária</span>
                                                            </div>
                                                            <div class="progress h-6px w-200px">
                                                                <div class="progress-bar bg-primary"
                                                                    role="progressbar"
                                                                    style="width: {{ $percentualConciliado }}%"
                                                                    aria-valuenow="{{ $percentualConciliado }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2">
                                                            <a href="#" class="btn btn-sm btn-primary"
                                                                onclick="toggleEdit({{ $conciliacao->id }})">✏️
                                                                Editar</a>
                                                            <a href="#" class="btn btn-sm btn-warning">⛓️‍💥
                                                                Desvincular</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Formulário de Edição -->
                                    <div id="editForm-{{ $conciliacao->id }}" class="d-none">
                                        <div class="card card-flush py-4 flex-row-fluid overflow-hidden p-5 border">
                                            <div
                                                class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-4 mb-10">
                                                <div class="d-flex flex-column pe-3">
                                                    <!-- Ícone de atenção (opcional) -->
                                                    <span class="svg-icon svg-icon-2hx svg-icon-warning me-4 mb-2">
                                                        ⚠️ <!-- Ícone do FontAwesome -->
                                                        <span class="fs-6 fw-bold">Atenção:</span>
                                                    </span>
                                                    <!-- Mensagem principal -->
                                                    <span class="fs-6">Os dados do
                                                        lançamento devem ser
                                                        <strong>iguais</strong> aos da
                                                        conciliação.</span>
                                                </div>
                                            </div>
                                            <form id="formularioEdicao-{{ $conciliacao->id }}"
                                                action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}"
                                                method="POST">
                                                @csrf

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="data_competencia-{{ $conciliacao->id }}"
                                                            class="required form-label fw-semibold">Data</label>
                                                        <input type="date" class="form-control"
                                                            id="data_competencia-{{ $conciliacao->id }}"
                                                            name="data_competencia"
                                                            value="{{ old('data_competencia', $transacaoSugerida ? \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="valor2-{{ $conciliacao->id }}"
                                                            class="required form-label fw-semibold">Valor</label>
                                                        <input type="text" class="form-control"
                                                            id="valor2-{{ $conciliacao->id }}" name="valor"
                                                            value="{{ old('valor', $transacaoSugerida ? number_format($transacaoSugerida->valor, 2, ',', '.') : number_format($conciliacao->amount, 2, ',', '.')) }}">
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <input type="hidden" name="bank_statement_id"
                                                            value="{{ $conciliacao->id }}">
                                                        @if ($transacaoSugerida)
                                                            <input type="hidden" name="transacao_financeira_id"
                                                                value="{{ $transacaoSugerida->id }}">
                                                            <input type="hidden" name="valor_conciliado"
                                                                value="{{ $transacaoSugerida->valor }}">
                                                        @endif
                                                        <label for="numero_documento-{{ $conciliacao->id }}"
                                                            class="required form-label fw-semibold">Código</label>
                                                        <input type="text" name="numero_documento"
                                                            class="form-control"
                                                            id="numero_documento-{{ $conciliacao->id }}"
                                                            value="{{ old('numero_documento', $conciliacao->checknum) }}">
                                                    </div>

                                                    <div class="col-md-8 mb-5">
                                                        <label for="descricao-{{ $conciliacao->id }}"
                                                            class="required form-label fw-semibold">Descrição</label>
                                                        <input type="text" class="form-control"
                                                            id="descricao-{{ $conciliacao->id }}" name="descricao"
                                                            value="{{ old('descricao', $transacaoSugerida->descricao ?? $conciliacao->memo) }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2">
                                                    <a href="#" class="btn btn-sm btn-success"
                                                        onclick="document.getElementById('formularioEdicao-{{ $conciliacao->id }}').submit();">
                                                        💾 Salvar
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-secondary"
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
                                    <div class="card-body d-flex align-items-center justify-content-center h-100">
                                        <button class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                            type="button" id="btn-conciliar-{{ $conciliacao->id }}">
                                            <span class="fs-1 me-2">🫱🏻‍🫲🏽</span>
                                            <!-- Emoji com tamanho ajustado -->
                                            <span id="btn-conciliar-text-{{ $conciliacao->id }}">Conciliar</span>
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
                                    <div class="card card-flush  flex-row-fluid overflow-hidden h-xl-100 ">
                                        <!-- Aqui suas abas para criar / buscar lançamento -->
                                        <ul class="nav nav-tabs" id="lancamentoTab{{ $conciliacao->id }}"
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
                                                <button class="nav-link" id="buscar-criar-{{ $conciliacao->id }}-tab"
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
                                                        <span class="fs-6 fw-bold">Lançamento não encontrado
                                                            automaticamente:</span>
                                                        <span class="fs-6">Crie um novo ao alimentar o formulário e
                                                            clicando no botão conciliar.</span>
                                                    </div>

                                                    <!-- Botão de fechar -->
                                                    <button type="button"
                                                        class="btn-close position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 ms-sm-auto"
                                                        data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                                <form id="{{ $conciliacao->id }}" class="row"
                                                    action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}"
                                                    method="POST">
                                                    @csrf
                                                    <!-- Container onde o formulário será renderizado via JSON -->
                                                    <div id="form-container-{{ $conciliacao->id }}"></div>

                                                    <!-- Container de anexos (aparece quando checkbox é marcado) -->
                                                    <div class="col-md-12" id="anexoInputContainer_{{ $conciliacao->id }}" style="display: none;">
                                                        <x-anexos-input name="anexos" :anexosExistentes="[]" />
                                                    </div>
                                                </form>

                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const conciliacaoId = {{ $conciliacao->id }};

                                                        // Estrutura JSON para o formulário "Novo Lançamento"
                                                        const formConfigNovoLancamento = {
                                                            conciliacaoId: conciliacaoId,
                                                            hiddenFields: [
                                                                { name: 'tipo', value: '{{ $conciliacao->amount > 0 ? 'entrada' : 'saida' }}', class: 'tipo-lancamento' },
                                                                { name: 'valor', value: '{{ $conciliacao->amount }}' },
                                                                { name: 'data_competencia', value: '{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}' },
                                                                { name: 'numero_documento', value: '{{ $conciliacao->checknum }}' },
                                                                { name: 'descricao', value: '{{ $conciliacao->memo }}' },
                                                                { name: 'origem', value: 'Conciliação Bancária' },
                                                                { name: 'entidade_id', value: '{{ $entidade->id }}' },
                                                                { name: 'bank_statement_id', value: '{{ $conciliacao->id }}' }
                                                                @if ($transacaoSugerida)
                                                                ,{ name: 'transacao_financeira_id', value: '{{ $transacaoSugerida->id }}' }
                                                                ,{ name: 'valor_conciliado', value: '{{ $transacaoSugerida->valor }}' }
                                                                @endif
                                                            ],
                                                            fields: [
                                                                {
                                                                    type: 'text',
                                                                    name: 'descricao2',
                                                                    id: 'descricao_' + conciliacaoId,
                                                                    label: 'Descrição',
                                                                    required: true,
                                                                    col: 'col-md-6',
                                                                    value: '{{ old('descricao', $conciliacao->memo) }}',
                                                                    placeholder: 'Ex: PAYMENT - Fulano',
                                                                    hasError: {{ $errors->has('descricao') ? 'true' : 'false' }},
                                                                    error: '{{ $errors->first('descricao') }}'
                                                                },
                                                                {
                                                                    type: 'select',
                                                                    name: 'cost_center_id',
                                                                    id: 'cost_center_id_' + conciliacaoId,
                                                                    label: 'Centro de Custo',
                                                                    required: true,
                                                                    col: 'col-md-6',
                                                                    placeholder: 'Selecione o Centro de Custo',
                                                                    allowClear: true,
                                                                    options: [
                                                                        @foreach ($centrosAtivos as $centro)
                                                                        {
                                                                            id: {{ $centro->id }},
                                                                            name: '{{ $centro->name }}',
                                                                            selected: {{ old('cost_center_id') == $centro->id ? 'true' : 'false' }}
                                                                        }{{ !$loop->last ? ',' : '' }}
                                                                        @endforeach
                                                                    ],
                                                                    hasError: {{ $errors->has('cost_center_id') ? 'true' : 'false' }},
                                                                    error: '{{ $errors->first('cost_center_id') }}'
                                                                },
                                                                {
                                                                    type: 'select',
                                                                    name: 'lancamento_padrao_id',
                                                                    id: 'lancamento_padrao_id_' + conciliacaoId,
                                                                    label: 'Lançamento Padrão',
                                                                    required: true,
                                                                    col: 'col-md-8',
                                                                    placeholder: 'Selecione o Lançamento Padrão',
                                                                    options: [
                                                                        @foreach ($lps as $lp)
                                                                        {
                                                                            id: {{ $lp->id }},
                                                                            description: '{{ $lp->description }}',
                                                                            dataType: '{{ $lp->type }}'
                                                                        }{{ !$loop->last ? ',' : '' }}
                                                                        @endforeach
                                                                    ],
                                                                    class: 'lancamento_padrao_banco'
                                                                },
                                                                {
                                                                    type: 'select',
                                                                    name: 'tipo_documento',
                                                                    id: 'tipo_documento_' + conciliacaoId,
                                                                    label: 'Tipo do Documento',
                                                                    required: true,
                                                                    col: 'col-md-4',
                                                                    placeholder: 'Tipo de Documento',
                                                                    options: [
                                                                        { value: 'Pix', text: 'Pix', selected: {{ old('tipo_documento') == 'Pix' ? 'true' : 'false' }} },
                                                                        { value: 'OUTR - Dafe', text: 'OUTR - Dafe', selected: {{ old('tipo_documento') == 'OUTR - Dafe' ? 'true' : 'false' }} },
                                                                        { value: 'NF - Nota Fiscal', text: 'NF - Nota Fiscal', selected: {{ old('tipo_documento') == 'NF - Nota Fiscal' ? 'true' : 'false' }} },
                                                                        { value: 'CF - Cupom Fiscal', text: 'CF - Cupom Fiscal', selected: {{ old('tipo_documento') == 'CF - Cupom Fiscal' ? 'true' : 'false' }} },
                                                                        { value: 'DANF - Danfe', text: 'DANF - Danfe', selected: {{ old('tipo_documento') == 'DANF - Danfe' ? 'true' : 'false' }} },
                                                                        { value: 'BOL - Boleto', text: 'BOL - Boleto', selected: {{ old('tipo_documento') == 'BOL - Boleto' ? 'true' : 'false' }} },
                                                                        { value: 'REP - Repasse', text: 'REP - Repasse', selected: {{ old('tipo_documento') == 'REP - Repasse' ? 'true' : 'false' }} },
                                                                        { value: 'CCRD - Cartão de Credito', text: 'CCRD - Cartão de Credito', selected: {{ old('tipo_documento') == 'CCRD - Cartão de Credito' ? 'true' : 'false' }} },
                                                                        { value: 'CDBT - Cartão de Debito', text: 'CDBT - Cartão de Debito', selected: {{ old('tipo_documento') == 'CDBT - Cartão de Debito' ? 'true' : 'false' }} },
                                                                        { value: 'CH - Cheque', text: 'CH - Cheque', selected: {{ old('tipo_documento') == 'CH - Cheque' ? 'true' : 'false' }} },
                                                                        { value: 'REC - Recibo', text: 'REC - Recibo', selected: {{ old('tipo_documento') == 'REC - Recibo' ? 'true' : 'false' }} },
                                                                        { value: 'CARN - Carnê', text: 'CARN - Carnê', selected: {{ old('tipo_documento') == 'CARN - Carnê' ? 'true' : 'false' }} },
                                                                        { value: 'FAT - Fatura', text: 'FAT - Fatura', selected: {{ old('tipo_documento') == 'FAT - Fatura' ? 'true' : 'false' }} },
                                                                        { value: 'APOL - Apólice', text: 'APOL - Apólice', selected: {{ old('tipo_documento') == 'APOL - Apólice' ? 'true' : 'false' }} },
                                                                        { value: 'DUPL - Duplicata', text: 'DUPL - Duplicata', selected: {{ old('tipo_documento') == 'DUPL - Duplicata' ? 'true' : 'false' }} },
                                                                        { value: 'TRIB - Tribunal', text: 'TRIB - Tribunal', selected: {{ old('tipo_documento') == 'TRIB - Tribunal' ? 'true' : 'false' }} },
                                                                        { value: 'Outros', text: 'Outros', selected: {{ old('tipo_documento') == 'Outros' ? 'true' : 'false' }} },
                                                                        { value: 'T Banc - Transferência Bancaria', text: 'T Banc - Transferência Bancaria', selected: {{ old('tipo_documento') == 'T Banc - Transferência Bancaria' ? 'true' : 'false' }} }
                                                                    ]
                                                                },
                                                                {
                                                                    type: 'checkbox',
                                                                    name: 'comprovacao_fiscal',
                                                                    id: 'comprovacaoFiscalCheckbox_' + conciliacaoId,
                                                                    label: 'Existe comprovação fiscal para o lançamento ' + conciliacaoId + '?',
                                                                    checkboxLabel: 'Possui Nota?',
                                                                    col: 'col-md-12',
                                                                    conditional: 'anexos',
                                                                    newRow: true
                                                                }
                                                            ]
                                                        };

                                                        // Renderiza o formulário
                                                        renderFormFromJSON(formConfigNovoLancamento, 'form-container-' + conciliacaoId);

                                                        // Event listener para checkbox de comprovação fiscal
                                                        setTimeout(() => {
                                                            const checkbox = document.getElementById('comprovacaoFiscalCheckbox_' + conciliacaoId);
                                                            const anexoInputContainer = document.getElementById('anexoInputContainer_' + conciliacaoId);

                                                            if (checkbox && anexoInputContainer) {
                                                                checkbox.addEventListener('change', function() {
                                                                    if (this.checked) {
                                                                        anexoInputContainer.style.display = 'block';
                                                                    } else {
                                                                        anexoInputContainer.style.display = 'none';
                                                                    }
                                                                });
                                                            }
                                                        }, 300);
                                                    });
                                                </script>
                                            </div>
                                            <!-- Aba Transferência -->
                                            <div class="tab-pane fade"
                                                id="transferencia-{{ $conciliacao->id }}-pane" role="tabpanel"
                                                aria-labelledby="transferencia-{{ $conciliacao->id }}-tab">
                                                <form id="form-transferencia-{{ $conciliacao->id }}"
                                                    action="{{ route('conciliacao.transferir') }}" method="POST">
                                                    @csrf
                                                    <!-- Container onde o formulário será renderizado via JSON -->
                                                    <div id="form-transferencia-container-{{ $conciliacao->id }}"></div>
                                                </form>

                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const conciliacaoId = {{ $conciliacao->id }};
                                                        const entidadeOrigemId = {{ $entidade->id }};

                                                        // Estrutura JSON para o formulário "Transferência"
                                                        const formConfigTransferencia = {
                                                            conciliacaoId: conciliacaoId,
                                                            hiddenFields: [
                                                                { name: 'bank_statement_id', value: '{{ $conciliacao->id }}' },
                                                                { name: 'entidade_origem_id', value: '{{ $entidade->id }}' },
                                                                { name: 'checknum', value: '{{ $conciliacao->checknum ?? '' }}' },
                                                                { name: 'valor', value: '{{ abs($conciliacao->amount) }}' },
                                                                { name: 'data_transferencia', value: '{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}' }
                                                            ],
                                                            fields: [
                                                                {
                                                                    type: 'select',
                                                                    name: 'entidade_destino_id',
                                                                    id: 'entidade_destino_id_' + conciliacaoId,
                                                                    label: 'Conta de Destino',
                                                                    required: true,
                                                                    col: 'col-md-12',
                                                                    placeholder: 'Selecione a conta de destino',
                                                                    options: [
                                                                        { value: '', text: 'Carregando contas...' }
                                                                    ],
                                                                    helpText: 'Selecione para onde transferir o valor',
                                                                    loadViaAjax: true
                                                                },
                                                                {
                                                                    type: 'select',
                                                                    name: 'lancamento_padrao_id',
                                                                    id: 'lancamento_padrao_id_transferencia_' + conciliacaoId,
                                                                    label: 'Lançamento Padrão',
                                                                    required: true,
                                                                    col: 'col-md-6',
                                                                    placeholder: 'Selecione o lançamento padrão',
                                                                    options: [
                                                                        @foreach ($lps as $lp)
                                                                        @if ($lp->type === 'ambos' || str_contains(strtolower($lp->description), 'transferência') || str_contains(strtolower($lp->description), 'transferencia'))
                                                                        {
                                                                            id: {{ $lp->id }},
                                                                            description: '{{ $lp->id }} - {{ $lp->description }}',
                                                                            selected: {{ old('lancamento_padrao_id') == $lp->id ? 'true' : 'false' }}
                                                                        }{{ !$loop->last ? ',' : '' }}
                                                                        @endif
                                                                        @endforeach
                                                                    ]
                                                                },
                                                                {
                                                                    type: 'select',
                                                                    name: 'cost_center_id',
                                                                    id: 'cost_center_id_transferencia_' + conciliacaoId,
                                                                    label: 'Centro de Custo',
                                                                    required: false,
                                                                    col: 'col-md-6',
                                                                    placeholder: 'Selecione o Centro de Custo',
                                                                    allowClear: true,
                                                                    options: [
                                                                        @foreach ($centrosAtivos as $centro)
                                                                        {
                                                                            id: {{ $centro->id }},
                                                                            name: '{{ $centro->name }}',
                                                                            selected: {{ old('cost_center_id') == $centro->id ? 'true' : 'false' }}
                                                                        }{{ !$loop->last ? ',' : '' }}
                                                                        @endforeach
                                                                    ],
                                                                    hasError: {{ $errors->has('cost_center_id') ? 'true' : 'false' }},
                                                                    error: '{{ $errors->first('cost_center_id') }}'
                                                                },
                                                                {
                                                                    type: 'textarea',
                                                                    name: 'descricao',
                                                                    id: 'descricao_transferencia_' + conciliacaoId,
                                                                    label: 'Descrição',
                                                                    required: false,
                                                                    col: 'col-md-12',
                                                                    rows: 3,
                                                                    value: '{{ $conciliacao->memo ? 'Transferência: ' . $conciliacao->memo : '' }}',
                                                                    placeholder: 'Ex: Transferência automática entre contas - {{ $conciliacao->memo }}',
                                                                    newRow: true
                                                                }
                                                            ]
                                                        };

                                                        // Renderiza o formulário
                                                        renderFormFromJSON(formConfigTransferencia, 'form-transferencia-container-' + conciliacaoId);

                                                        // Adiciona help text após renderização
                                                        setTimeout(() => {
                                                            const lancamentoSelect = document.getElementById('lancamento_padrao_id_transferencia_' + conciliacaoId);
                                                            if (lancamentoSelect && lancamentoSelect.parentElement) {
                                                                const helpText = document.createElement('div');
                                                                helpText.className = 'form-text';
                                                                helpText.textContent = 'Selecione um lançamento padrão do tipo "Ambos" ou relacionado a transferências';
                                                                lancamentoSelect.parentElement.appendChild(helpText);
                                                            }
                                                        }, 200);
                                                    });

                                                    $(document).ready(function() {
                                                        // Carrega as contas disponíveis ao abrir a aba
                                                        const conciliacaoId = {{ $conciliacao->id }};
                                                        const entidadeOrigemId = {{ $entidade->id }};

                                                        // Função para carregar contas disponíveis
                                                        function carregarContasDisponiveis(conciliacaoId, entidadeOrigemId) {
                                                            const selectDestino = $('#entidade_destino_id_' + conciliacaoId);

                                                            if (!selectDestino.length) {
                                                                // Se o select ainda não existe, tenta novamente após um delay
                                                                setTimeout(() => carregarContasDisponiveis(conciliacaoId, entidadeOrigemId), 200);
                                                                return;
                                                            }

                                                            selectDestino.html('<option value="">Carregando...</option>');

                                                            $.ajax({
                                                                url: '{{ route('conciliacao.contas-disponiveis') }}',
                                                                method: 'GET',
                                                                data: {
                                                                    entidade_origem_id: entidadeOrigemId,
                                                                    bank_statement_id: conciliacaoId
                                                                },
                                                                success: function(response) {
                                                                    console.log('Resposta do servidor:', response); // Debug

                                                                    if (response.success && response.contas && response.contas.length > 0) {
                                                                        selectDestino.html(
                                                                            '<option value="">Selecione a conta de destino</option>');

                                                                        response.contas.forEach(function(conta) {
                                                                            const optionText = conta.nome + (conta.account_type_label ? ' - ' + conta.account_type_label : '');
                                                                            const option = $('<option></option>')
                                                                                .attr('value', conta.id)
                                                                                .text(optionText);
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
                                                                        console.warn('Nenhuma conta encontrada. Total:', response.total || 0); // Debug
                                                                    }
                                                                },
                                                                error: function(xhr) {
                                                                    console.error('Erro ao carregar contas:', xhr);
                                                                    console.error('Response:', xhr.responseJSON || xhr.responseText);
                                                                    selectDestino.html('<option value="">Erro ao carregar contas</option>');
                                                                }
                                                            });
                                                        }

                                                        const selectDestino = $('#entidade_destino_id_' + conciliacaoId);

                                                        // Carrega contas quando a aba é mostrada
                                                        $('#transferencia-' + conciliacaoId + '-tab').on('shown.bs.tab', function() {
                                                            // Aguarda a renderização do formulário
                                                            setTimeout(function() {
                                                                const selectDestino = $('#entidade_destino_id_' + conciliacaoId);
                                                                if (selectDestino.length) {
                                                                    // Verifica se já tem opções carregadas (mais de 1 = placeholder + opções)
                                                                    const optionCount = selectDestino.find('option').length;
                                                                    const hasOptions = selectDestino.find('option:not([value=""])').length > 0;
                                                                    if (optionCount <= 1 || !hasOptions) {
                                                                        carregarContasDisponiveis(conciliacaoId, entidadeOrigemId);
                                                                    }
                                                                }
                                                            }, 400);
                                                        });

                                                        // Também tenta carregar quando o formulário é renderizado
                                                        setTimeout(function() {
                                                            const selectDestino = $('#entidade_destino_id_' + conciliacaoId);
                                                            if (selectDestino.length) {
                                                                const optionCount = selectDestino.find('option:not([value=""])').length;
                                                                if (optionCount === 0) {
                                                                    carregarContasDisponiveis(conciliacaoId, entidadeOrigemId);
                                                                }
                                                            }
                                                        }, 600);

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

                                                            // Sempre mantém o texto "Conciliar" independente da aba
                                                            btnConciliarText.text('Conciliar');

                                                            // Prioriza Transferência se ambas estiverem ativas (não deveria acontecer, mas por segurança)
                                                            if (tabTransferenciaAtivo) {
                                                                // Aba "Transferência" está ativa
                                                                btnConciliar.attr('form', 'form-transferencia-' + conciliacaoId);
                                                            } else if (tabNovoLancamentoAtivo) {
                                                                // Aba "Novo Lançamento" está ativa
                                                                btnConciliar.attr('form', conciliacaoId);
                                                            } else {
                                                                // Outra aba (Buscar/Criar vários)
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
                                            <div class="tab-pane fade" id="buscar-criar-{{ $conciliacao->id }}-pane"
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

<script>
    /**
     * Função para renderizar formulários baseados em estrutura JSON
     */
    function renderFormFromJSON(formConfig, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        let html = '';

        // Renderiza campos hidden primeiro (fora das rows)
        if (formConfig.hiddenFields) {
            formConfig.hiddenFields.forEach(field => {
                const value = field.value || '';
                const escapedValue = value.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                html += `<input type="hidden" name="${field.name}" value="${escapedValue}" ${field.class ? `class="${field.class}"` : ''}>`;
            });
        }

        // Agrupa campos por row baseado em newRow ou quando a soma das colunas excede 12
        const rows = [];
        let currentRow = [];
        let currentRowSize = 0;

        formConfig.fields.forEach((field, index) => {
            // Checkbox sempre em row separada
            if (field.type === 'checkbox') {
                if (currentRow.length > 0) {
                    rows.push([...currentRow]);
                    currentRow = [];
                    currentRowSize = 0;
                }
                rows.push([field]);
                return;
            }

            // Se o campo marca newRow ou se adicionar este campo excederia 12 colunas, fecha a row atual
            const colSize = parseInt(field.col?.match(/col-md-(\d+)/)?.[1] || '12');

            if (field.newRow || (currentRow.length > 0 && currentRowSize + colSize > 12)) {
                if (currentRow.length > 0) {
                    rows.push([...currentRow]);
                }
                currentRow = [];
                currentRowSize = 0;
            }

            currentRow.push(field);
            currentRowSize += colSize;
        });
        if (currentRow.length > 0) {
            rows.push(currentRow);
        }

        // Renderiza cada row
        rows.forEach(row => {
            // Se for checkbox, não precisa de row wrapper
            if (row.length === 1 && row[0].type === 'checkbox') {
                html += renderField(row[0], formConfig);
            } else {
                html += '<div class="row mb-3">';
                row.forEach(field => {
                    html += renderField(field, formConfig);
                });
                html += '</div>';
            }
        });

        container.innerHTML = html;

        // Inicializa Select2 para campos select
        setTimeout(() => {
            container.querySelectorAll('select[data-control="select2"]').forEach(select => {
                if (typeof KTSelect2 !== 'undefined') {
                    new KTSelect2(select);
                } else if (typeof $(select).select2 !== 'undefined') {
                    $(select).select2();
                }
            });

            // Processa campos com loadViaAjax
            formConfig.fields.forEach(field => {
                if (field.loadViaAjax && field.type === 'select') {
                    const fieldId = field.id || `${field.name}_${formConfig.conciliacaoId || ''}`;
                    const selectElement = document.getElementById(fieldId);

                    if (selectElement && field.name === 'entidade_destino_id') {
                        // Carrega contas disponíveis para transferência
                        const conciliacaoId = formConfig.conciliacaoId;
                        const entidadeOrigemId = formConfig.hiddenFields?.find(f => f.name === 'entidade_origem_id')?.value;

                        if (conciliacaoId && entidadeOrigemId) {
                            carregarContasDisponiveisAjax(selectElement, conciliacaoId, entidadeOrigemId);
                        }
                    }
                }
            });
        }, 100);
    }

    /**
     * Função auxiliar para carregar contas disponíveis via AJAX
     */
    function carregarContasDisponiveisAjax(selectElement, conciliacaoId, entidadeOrigemId) {
        if (!selectElement) return;

        const $select = $(selectElement);
        $select.html('<option value="">Carregando contas...</option>');

        $.ajax({
            url: '{{ route('conciliacao.contas-disponiveis') }}',
            method: 'GET',
            data: {
                entidade_origem_id: entidadeOrigemId,
                bank_statement_id: conciliacaoId
            },
            success: function(response) {
                console.log('Resposta do servidor:', response); // Debug

                if (response.success && response.contas && response.contas.length > 0) {
                    $select.html('<option value="">Selecione a conta de destino</option>');

                    response.contas.forEach(function(conta) {
                        const optionText = conta.nome + (conta.account_type_label ? ' - ' + conta.account_type_label : '');
                        const option = $('<option></option>')
                            .attr('value', conta.id)
                            .text(optionText);
                        $select.append(option);
                    });

                    // Reinicializa Select2
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                    $select.select2({
                        placeholder: "Selecione a conta de destino",
                        allowClear: true
                    });
                } else {
                    $select.html('<option value="">Nenhuma conta disponível</option>');
                    console.warn('Nenhuma conta encontrada. Total:', response.total || 0); // Debug
                }
            },
            error: function(xhr) {
                console.error('Erro ao carregar contas:', xhr);
                $select.html('<option value="">Erro ao carregar contas</option>');
            }
        });
    }

    /**
     * Renderiza um campo individual baseado em sua configuração
     */
    function renderField(field, formConfig) {
        const fieldId = field.id || `${field.name}_${formConfig.conciliacaoId || ''}`;
        const colClass = field.col || 'col-md-12';
        const requiredClass = field.required ? 'required' : '';
        const errorClass = field.hasError ? 'is-invalid' : '';

        // Para checkbox, usa estrutura especial
        if (field.type === 'checkbox') {
            let html = '<div class="d-flex flex-column">';
            html += '<div class="col-md-12 p-0 m-0 mb-5">';
            html += '<div class="d-flex flex-row align-items-center">';
            if (field.label) {
                html += '<div class="me-5">';
                html += `<label class="fs-6 fw-semibold form-label mb-0">${field.label}</label>`;
                html += '</div>';
            }
            html += '<label class="form-check form-switch form-check-custom form-check-solid mb-0">';
            html += `<input type="hidden" name="${field.name}" value="0">`;
            html += `<input type="checkbox"
                id="${fieldId}"
                name="${field.name}"
                class="form-check-input"
                value="1"
                ${field.checked ? 'checked' : ''}>`;
            if (field.checkboxLabel) {
                html += `<span class="form-check-label fw-semibold text-muted">${field.checkboxLabel}</span>`;
            }
            html += '</label>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            return html;
        }

        let html = `<div class="${colClass}">`;

        // Label
        if (field.label && field.type !== 'hidden') {
            html += `<label for="${fieldId}" class="${requiredClass} form-label fw-semibold">${field.label}</label>`;
        }

        // Campo baseado no tipo
        switch (field.type) {
            case 'text':
                const textValue = (field.value || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                html += `<input type="text"
                    id="${fieldId}"
                    name="${field.name}"
                    class="form-control ${errorClass}"
                    value="${textValue}"
                    placeholder="${field.placeholder || ''}"
                    ${field.required ? 'required' : ''}>`;
                break;

            case 'textarea':
                const textareaValue = (field.value || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                html += `<textarea
                    id="${fieldId}"
                    name="${field.name}"
                    class="form-control form-control-solid ${errorClass}"
                    rows="${field.rows || 3}"
                    placeholder="${field.placeholder || ''}"
                    ${field.required ? 'required' : ''}>${textareaValue}</textarea>`;
                break;

            case 'select':
                html += `<select
                    id="${fieldId}"
                    name="${field.name}"
                    class="form-select form-select-solid ${errorClass}"
                    data-control="select2"
                    data-placeholder="${field.placeholder || 'Selecione...'}"
                    ${field.allowClear ? 'data-allow-clear="true"' : ''}
                    ${field.required ? 'required' : ''}>`;

                if (field.placeholder) {
                    html += `<option value="">${field.placeholder}</option>`;
                }

                if (field.options) {
                    field.options.forEach(option => {
                        const selected = option.selected ? 'selected' : '';
                        const optionValue = option.value !== undefined ? option.value : option.id;
                        const optionText = option.text || option.name || option.description || '';
                        html += `<option value="${optionValue}" ${selected} ${option.dataType ? `data-type="${option.dataType}"` : ''}>${optionText}</option>`;
                    });
                }
                html += '</select>';

                if (field.helpText) {
                    html += `<div class="form-text">${field.helpText}</div>`;
                }
                break;


            case 'hidden':
                html += `<input type="hidden"
                    id="${fieldId}"
                    name="${field.name}"
                    value="${field.value || ''}"
                    ${field.class ? `class="${field.class}"` : ''}>`;
                break;
        }

        // Mensagem de erro
        if (field.error) {
            html += `<div class="invalid-feedback">${field.error}</div>`;
        }

        html += '</div>';
        return html;
    }
</script>
