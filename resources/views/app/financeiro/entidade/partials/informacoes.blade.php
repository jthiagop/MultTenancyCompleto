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
