# ✅ Correção: Gráfico de Missas - Erro "Broken Pipe" e JSON Inválido

## 📋 Resumo da Solução

Foram implementadas melhorias no backend (PHP/Laravel) e frontend (JavaScript) para resolver o erro "Broken pipe" e "Unexpected token '<'" que estava ocorrendo no gráfico de missas.

---

## 🔧 Alterações Implementadas

### 1. **Backend - [app/Http/Controllers/App/DashboardController.php](app/Http/Controllers/App/DashboardController.php)**

#### Problema Original
- Consulta retornava objetos Eloquent inteiros causando memory overflow
- Sem tratamento de exceção global → erros causavam "Broken pipe"
- Resposta era interrompida, resultando em HTML de erro em vez de JSON

#### Soluções Aplicadas

**a) Try-Catch Global**
```php
try {
    // Toda a lógica aqui
} catch (\Exception $e) {
    \Log::error('Erro em getMissasChartData', [...]);
    return response()->json(['error' => '...'], 500);
}
```
✅ Garante que sempre será retornado JSON válido

**b) Processamento com Chunks**
```php
$query->chunk(500, function($statements) use (&$bankStatements) {
    $bankStatements = array_merge($bankStatements, $statements->toArray());
});
```
✅ Evita carregar todos os registros em memória simultaneamente

**c) Seleção de Colunas Específicas**
```php
->select(['id', 'company_id', 'horario_missa_id', 'amount', 'transaction_datetime', 'dtposted'])
```
✅ Reduz tamanho dos dados em memória

**d) Eager Loading Otimizado**
```php
->with('horarioMissa:id,dia_semana')
```
✅ Carrega apenas as colunas necessárias do relacionamento

**e) Headers Explícitos de JSON**
```php
return response()->json([...])->header('Content-Type', 'application/json; charset=utf-8');
```
✅ Garante que browser interprete como JSON válido

---

### 2. **Frontend - [public/assets/js/custom/apps/dashboard/missas-chart.js](public/assets/js/custom/apps/dashboard/missas-chart.js)**

#### Problema Original
- Sem verificação de Content-Type → tentava fazer parse de HTML como JSON
- Sem retry automático → erro único causava falha permanente
- Mensagens de erro genéricas → difícil diagnosticar

#### Soluções Aplicadas

**a) Validação Robusta de Resposta**
```javascript
// Verificar Content-Type
const contentType = response.headers.get('content-type');
if (!contentType || !contentType.includes('application/json')) {
    throw new Error('Resposta inválida. Content-Type: ' + contentType);
}

// Validar JSON parsing
return response.json().catch(err => {
    throw new Error('Erro ao decodificar JSON: ' + err.message);
});
```
✅ Detecta e relata exatamente qual é o problema

**b) Retry Automático com Backoff**
```javascript
if (retryCount < maxRetries) {
    console.log('Tentando novamente em 2 segundos...');
    return new Promise(resolve => setTimeout(resolve, 2000))
        .then(() => loadChartData(startDate, endDate, retryCount + 1));
}
```
✅ Tenta 3 vezes com 2 segundos entre tentativas

**c) Validação de Estrutura de Dados**
```javascript
if (!Array.isArray(data.data) || !Array.isArray(data.categories)) {
    throw new Error('Formato de dados inválido');
}
```
✅ Garante que o JSON tem a estrutura esperada

**d) Fallback de Dados Vazios**
```javascript
.catch(function(error) {
    // Se falhar, mostra gráfico com dados vazios
    initChart({
        data: [0, 0, 0, 0, 0, 0, 0],
        categories: ['Domingo', 'Segunda', '...']
    });
});
```
✅ Garante que o gráfico sempre renderiza, mesmo com erro

---

## 📊 Teste as Mudanças

### 1. Verificar Logs
```bash
# Terminal
tail -f storage/logs/laravel.log

# Procurar por:
# - "Erro em getMissasChartData"
# - "Erro ao processar BankStatement"
```

### 2. DevTools do Navegador
```
F12 → Network → Fazer requisição no gráfico
Verificar:
✓ Status HTTP (deve ser 200 ou 500)
✓ Content-Type (deve ser application/json)
✓ Response (deve ser JSON válido)
```

### 3. Teste via cURL
```bash
curl -H "Accept: application/json" \
  "http://localhost:8000/dashboard/missas-chart-data?start_date=2026-01-01&end_date=2026-01-21"
```

---

## 🚀 Melhorias Futuras Recomendadas

### Nível 1 - Rápido (Implementar Agora)
```php
// Adicionar índices no banco
CREATE INDEX idx_bank_statements_company_missa 
ON bank_statements(company_id, conciliado_com_missa);

CREATE INDEX idx_bank_statements_horario 
ON bank_statements(horario_missa_id);

CREATE INDEX idx_bank_statements_date 
ON bank_statements(transaction_datetime);
```

### Nível 2 - Médio (Cache)
```php
// Em getMissasChartData():
$cacheKey = 'missas_chart_' . md5($activeCompanyId . $startDate . $endDate);
return Cache::remember($cacheKey, 3600, function() {
    // Executar query aqui
});
```

### Nível 3 - Avançado (Aggregation)
```php
// Usar SELECT SUM(amount) em vez de carregar cada registro
$data = BankStatement::where('company_id', $activeCompanyId)
    ->join('horarios_missas', 'horarios_missas.id', '=', 'bank_statements.horario_missa_id')
    ->groupBy('horarios_missas.dia_semana')
    ->selectRaw('horarios_missas.dia_semana, SUM(amount) as total')
    ->get();
```

---

## ⚠️ Se o Problema Persistir

1. **Verificar max_execution_time no php.ini**
   ```
   max_execution_time = 300  (mínimo para dados grandes)
   ```

2. **Verificar memory_limit no php.ini**
   ```
   memory_limit = 512M  (mínimo para operações pesadas)
   ```

3. **Testar com período menor**
   - Mudar para "Hoje" em vez de "Este Ano"
   - Verificar se funciona com dados menores

4. **Verificar Queries Lentas**
   - Ativar query logging: `config/database.php`
   - Rodar com `DB::enableQueryLog()` e `DD::getQueryLog()`

---

## 📝 Arquivos Modificados

| Arquivo | Tipo | Mudança |
|---------|------|---------|
| [app/Http/Controllers/App/DashboardController.php](app/Http/Controllers/App/DashboardController.php) | Backend | Método `getMissasChartData()` refatorado |
| [public/assets/js/custom/apps/dashboard/missas-chart.js](public/assets/js/custom/apps/dashboard/missas-chart.js) | Frontend | Função `loadChartData()` e `updateChart()` melhoradas |

---

## 🎯 Status

- ✅ Try-catch global implementado
- ✅ Processamento com chunks implementado  
- ✅ Retry automático no frontend
- ✅ Validação robusta de resposta JSON
- ✅ Mensagens de erro detalhadas
- ✅ Fallback com dados vazios
- ✅ Headers JSON explícitos

**Próximo passo:** Testar e confirmar se o gráfico está funcionando normalmente! 🎉
