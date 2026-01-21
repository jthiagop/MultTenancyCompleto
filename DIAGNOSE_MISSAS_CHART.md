# Diagnóstico do Gráfico de Missas

## Problema Original
- **Erro HTTP**: "Broken pipe" - conexão interrompida no meio da resposta
- **Erro JS**: "Unexpected token '<'" - recebendo HTML em vez de JSON

## Causa Provável
O servidor estava caindo durante a execução da query, retornando uma página de erro HTML (que começa com `<br /><b>`) em vez de JSON válido.

## Soluções Implementadas

### 1. Backend (DashboardController.php)
✅ **Try-Catch Global**: Adicionado try-catch em toda a função para capturar exceções
✅ **Processamento por Chunks**: Mudado de `.get()` para `.chunk(500)` para evitar memory overflow
✅ **Seleção de Colunas**: Adicionado `select()` para retornar apenas colunas necessárias
✅ **Try-Catch Interno**: Adicionado tratamento individual para cada statement processado
✅ **Headers Explícitos**: Adicionado header JSON charset explícito na resposta
✅ **Logs Detalhados**: Adicionados logs para cada erro encontrado

### 2. Frontend (missas-chart.js)
✅ **Retry Automático**: Implementado retry automático (2 tentativas) com delay de 2s
✅ **Validação de Content-Type**: Verificação se resposta é realmente JSON
✅ **Mensagens de Erro Detalhadas**: Erros agora mostram o que exatamente aconteceu
✅ **Fallback de Dados**: Se falhar, mostra gráfico com valores zero
✅ **Tratamento de Parsing**: Try-catch específico para erro de JSON parsing

## Como Diagnosticar Problemas

### 1. Verificar Logs do Laravel
```bash
tail -f storage/logs/laravel.log
```
Procure por linhas com:
- `[ERROR] Erro em getMissasChartData`
- `[WARNING] Erro ao processar BankStatement`

### 2. Abrir Developer Tools do Navegador
- Pressione `F12`
- Ir para aba "Network"
- Fazer requisição para `/dashboard/missas-chart-data`
- Verificar:
  - Status HTTP da resposta
  - Content-Type do header
  - Corpo da resposta (deve ser JSON válido)

### 3. Testar via curl
```bash
curl -H "Accept: application/json" "http://localhost:8000/dashboard/missas-chart-data"
```

### 4. Verificar Configurações do PHP
- Timeout de execução: `php.ini` → `max_execution_time`
- Memory limit: `php.ini` → `memory_limit`
- Output buffering: `php.ini` → `output_buffering`

## Se o Problema Persistir

### Possíveis Causas Adicionais
1. **Database**: Query muito lenta
   - Solução: Adicionar índices em `bank_statements` e `horario_missa`
   
2. **Memory**: Array muito grande em memória
   - Solução: Já implementado com chunks de 500 registros
   
3. **PHP Timeout**: Script executando por mais de `max_execution_time`
   - Solução: Aumentar em php.ini ou .htaccess

4. **Relacionamentos N+1**: Lazy loading de relationships
   - Solução: Já implementado eager loading com `->with()`

## Próximas Otimizações Recomendadas

1. **Adicionar Cache**
   ```php
   Cache::remember('missas_chart_' . $activeCompanyId . '_' . $startDate . '_' . $endDate, 3600, function() {
       // query aqui
   });
   ```

2. **Paginação no Backend**
   - Limitar resultados por dia da semana

3. **Índices no Banco**
   ```sql
   CREATE INDEX idx_bank_statements_company ON bank_statements(company_id);
   CREATE INDEX idx_bank_statements_missa ON bank_statements(horario_missa_id);
   CREATE INDEX idx_bank_statements_date ON bank_statements(transaction_datetime);
   ```

## Checklist de Verificação

- [ ] Verificar logs do Laravel
- [ ] Abrir DevTools e testar requisição
- [ ] Confirmar que resposta é JSON válido
- [ ] Verificar memory_limit do PHP
- [ ] Verificar max_execution_time do PHP
- [ ] Testar em período pequeno (ex: 1 dia)
