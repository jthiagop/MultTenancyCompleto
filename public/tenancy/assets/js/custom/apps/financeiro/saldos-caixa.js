"use strict";

var KTSaldosCaixa = function() {
    var table;
    var meses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    // Formatar valor monetário
    var formatarValor = function(valor) {
        if (!valor) valor = 0;
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor);
    };

    // Popular tabela com os dados
    var popularTabela = function(dados) {
        var table = document.getElementById('kt_customers_table');
        if (!table) {
            console.error('Tabela kt_customers_table não encontrada');
            return;
        }
        
        var tbody = table.querySelector('tbody');
        if (!tbody) {
            console.error('Tbody não encontrado na tabela');
            return;
        }
        
        tbody.innerHTML = '';

        for (var mes = 1; mes <= 12; mes++) {
            var dadosMes = dados[mes] || {
                saldo_anterior: 0,
                entradas: 0,
                saidas: 0,
                saldo_atual: 0
            };

            var row = document.createElement('tr');
            
            var tdMes = document.createElement('td');
            tdMes.className = 'text-gray-800';
            tdMes.textContent = meses[mes - 1];
            row.appendChild(tdMes);
            
            var tdSaldoAnterior = document.createElement('td');
            tdSaldoAnterior.textContent = formatarValor(dadosMes.saldo_anterior);
            row.appendChild(tdSaldoAnterior);
            
            var tdEntradas = document.createElement('td');
            tdEntradas.className = 'text-success';
            tdEntradas.textContent = formatarValor(dadosMes.entradas);
            row.appendChild(tdEntradas);
            
            var tdSaidas = document.createElement('td');
            tdSaidas.className = 'text-danger';
            tdSaidas.textContent = formatarValor(dadosMes.saidas);
            row.appendChild(tdSaidas);
            
            var tdSaldoAtual = document.createElement('td');
            tdSaldoAtual.className = 'text-gray-800 fw-bold';
            tdSaldoAtual.textContent = formatarValor(dadosMes.saldo_atual);
            row.appendChild(tdSaldoAtual);
            
            tbody.appendChild(row);
        }
    };

    // Buscar dados do servidor
    var buscarDados = function() {
        var anoInput = document.getElementById('filtro_ano');
        var codigoInput = document.getElementById('filtro_codigo');
        var centroCustoSelect = document.getElementById('filtro_centro_custo');
        
        var ano = (anoInput && anoInput.value) ? anoInput.value : new Date().getFullYear();
        var codigo = (codigoInput && codigoInput.value) ? codigoInput.value : '';
        var centroCustoId = (centroCustoSelect && centroCustoSelect.value) ? centroCustoSelect.value : '';

        // Mostrar loading
        var table = document.getElementById('kt_customers_table');
        if (table) {
            var tbody = table.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center"><span class="spinner-border spinner-border-sm"></span> Carregando dados...</td></tr>';
            }
        }

        // Usar fetch API ao invés de jQuery para evitar dependências
        var url = window.saldosCaixaRoute || '/financeiro/saldos-mensais';
        var params = new URLSearchParams({
            ano: ano,
            codigo: codigo,
            centro_custo_id: centroCustoId
        });
        
        fetch(url + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            return response.json();
        })
        .then(function(response) {
            var table = document.getElementById('kt_customers_table');
            var tbody = table ? table.querySelector('tbody') : null;
            
            if (response.success && response.data) {
                popularTabela(response.data);
            } else {
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Erro ao carregar dados</td></tr>';
                }
            }
        })
        .catch(function(error) {
            console.error('Erro ao buscar saldos:', error);
            var table = document.getElementById('kt_customers_table');
            var tbody = table ? table.querySelector('tbody') : null;
            
            var mensagem = 'Erro ao carregar dados: ' + error.message;
            
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">' + mensagem + '</td></tr>';
            }
        });
    };

    // Inicializar
    var init = function() {
        // Verificar se os elementos existem
        var anoInput = document.getElementById('filtro_ano');
        var btnGerar = document.getElementById('btn_gerar_saldos');
        var table = document.getElementById('kt_customers_table');
        
        if (!table) {
            console.warn('Tabela kt_customers_table não encontrada. Aguardando...');
            setTimeout(init, 500);
            return;
        }
        
        // Definir ano padrão
        if (anoInput && !anoInput.value) {
            anoInput.value = new Date().getFullYear();
        }

        // Evento do botão Gerar
        if (btnGerar) {
            btnGerar.addEventListener('click', function(e) {
                e.preventDefault();
                buscarDados();
            });
        }

        // Buscar dados ao carregar a página
        buscarDados();
    };

    return {
        init: init,
        buscarDados: buscarDados
    };
}();

// Inicializar quando o documento estiver pronto
(function() {
    function initialize() {
        if (document.getElementById('kt_customers_table')) {
            KTSaldosCaixa.init();
        } else {
            // Tentar novamente após um delay
            setTimeout(initialize, 100);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        // DOM já está pronto
        initialize();
    }
})();

