@if($bankConfig)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const oauthBtn = document.getElementById('btn-test-oauth');
        const extratoBtn = document.getElementById('btn-test-extrato');

        // Teste OAuth
        if (oauthBtn) {
            oauthBtn.addEventListener('click', function() {
                const btn = this;
                const originalHtml = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testando...';

                fetch('{{ route("bank-config.test-connection") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        bank_config_id: '{{ $bankConfig->id }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'OAuth Funcionando!',
                            html: `<p>${data.message}</p><small class="text-muted">Token: ${data.token_preview}</small>`,
                            confirmButtonText: 'Ótimo!'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Falha no OAuth',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao testar OAuth: ' + error.message,
                        confirmButtonText: 'OK'
                    });
                });
            });
        }

        // Teste Extrato
        if (extratoBtn) {
            extratoBtn.addEventListener('click', function() {
                const btn = this;
                const originalHtml = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Consultando...';

                fetch('{{ route("bank-config.test-extrato") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        bank_config_id: '{{ $bankConfig->id }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    
                    if (data.success) {
                        const info = data.data;
                        // Formatar valores se existirem
                        const sdIni = info.saldo_inicial ? `Saldo Inicial: ${info.saldo_inicial}` : '';
                        const sdFim = info.saldo_final ? `Saldo Final: ${info.saldo_final}` : '';

                        Swal.fire({
                            icon: 'success',
                            title: 'Teste de Extrato OK!',
                            html: `
                                <div class="text-start fs-6 ms-4">
                                    <p class="mb-1"><strong>Agência/Conta:</strong> ${info.agencia} / ${info.conta}</p>
                                    <p class="mb-1"><strong>Período:</strong> ${info.periodo}</p>
                                    <p class="mb-1"><strong>Lançamentos Encontrados:</strong> ${info.total_lancamentos}</p>
                                    <hr class="my-2">
                                    ${sdIni ? `<p class="mb-1 text-muted">${sdIni}</p>` : ''}
                                    ${sdFim ? `<p class="mb-0 fw-bold text-success">${sdFim}</p>` : ''}
                                </div>
                            `,
                            confirmButtonText: 'Fechar'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao Consultar',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de Sistema',
                        text: 'Falha na requisição: ' + error.message,
                        confirmButtonText: 'OK'
                    });
                });
            });
        }
    });
</script>
@endif
