                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade" id="kt_tab_pane_movimentacao" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Movimentação</h5>

                                        @if ($transacoes->isEmpty())
                                            <p class="text-muted">Nenhuma movimentação encontrada.</p>
                                        @else
                                            <!-- Início do Accordion -->
                                            <div class="accordion" id="movimentacaoAccordion">

                                                @foreach ($transacoesPorDia as $dia => $listaTransacoes)
                                                    @php
                                                        $dataCarbon = \Carbon\Carbon::parse($dia);
                                                        // Exemplo de saldo final (ajuste conforme sua lógica)
                                                        $saldoBanco = 6160.77;
                                                        $saldoContaAzul = 5663.27;
                                                        // Exemplo de conciliações pendentes no dia
                                                        $qtdPendencias = 3;
                                                    @endphp

                                                    <div class="accordion-item">
                                                        <!-- Cabeçalho do Accordion -->
                                                        <h2 class="accordion-header" id="heading-{{ $dia }}">
                                                            <button class="accordion-button fs-4 fw-semibold collapsed"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-{{ $dia }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse-{{ $dia }}">
                                                                <!-- Exibe a data e o dia da semana -->
                                                                {{ $dataCarbon->format('d/m/Y') }}
                                                                ({{ $dataCarbon->translatedFormat('l') }})
                                                            </button>
                                                        </h2>

                                                        <!-- Corpo do Accordion -->
                                                        <div id="collapse-{{ $dia }}"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="heading-{{ $dia }}"
                                                            data-bs-parent="#movimentacaoAccordion">

                                                            <div class="accordion-body">

                                                                <!-- Alerta de Pendências ou Mensagem de Conciliação -->
                                                                @if ($qtdPendencias > 0)
                                                                    <div
                                                                        class="alert alert-warning d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <strong>{{ $qtdPendencias }} conciliações
                                                                                pendentes neste dia.</strong>
                                                                            <br>
                                                                            Efetue as conciliações para acompanhar suas
                                                                            movimentações corretamente.
                                                                        </div>
                                                                        <!-- Botões "Expandir tudo" / "Recolher tudo" (opcional) -->
                                                                        <div>
                                                                            <a href="#"
                                                                                class="btn btn-sm btn-light">Expandir
                                                                                tudo</a>
                                                                            <a href="#"
                                                                                class="btn btn-sm btn-light">Recolher
                                                                                tudo</a>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="alert alert-info d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <strong>Todos os lançamentos estão
                                                                                conciliados.</strong>
                                                                            <br>
                                                                            Nenhuma pendência encontrada para
                                                                            {{ $dataCarbon->format('d/m/Y') }}.
                                                                        </div>
                                                                        <div>
                                                                            <a href="#"
                                                                                class="btn btn-sm btn-light">Expandir
                                                                                tudo</a>
                                                                            <a href="#"
                                                                                class="btn btn-sm btn-light">Recolher
                                                                                tudo</a>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <!-- Tabela de Lançamentos do Dia -->
                                                                <table class="table table-bordered mb-3">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Descrição</th>
                                                                            <th>Valor</th>
                                                                            <th>Tipo</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($listaTransacoes as $transacao)
                                                                            <tr>
                                                                                <td>{{ $transacao->descricao }}</td>
                                                                                <td>
                                                                                    R$
                                                                                    {{ number_format($transacao->valor, 2, ',', '.') }}
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

                                                                <!-- Saldo final do dia (exemplo) -->
                                                                <div class="text-end">
                                                                    <small class="text-muted">
                                                                        Saldo final do dia no Banco:
                                                                        <strong>R$
                                                                            {{ number_format($saldoBanco, 2, ',', '.') }}</strong>
                                                                        | Dominus:
                                                                        <strong>R$
                                                                            {{ number_format($saldoContaAzul, 2, ',', '.') }}</strong>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Fim do Corpo do Accordion -->
                                                    </div>
                                                    <!-- Fim accordion-item -->
                                                @endforeach

                                            </div>
                                            <!-- Fim do Accordion -->
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Aba de Conciliações Pendentes -->
