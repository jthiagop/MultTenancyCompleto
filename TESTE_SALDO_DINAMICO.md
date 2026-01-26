# 🧪 Teste: Saldo Dinâmico com Valores Absolutos

## ✅ Alterações Implementadas

### Fase 1: Remoção de Modificações Diretas de `saldo_atual`
- [x] `EntidadeFinanceiraController::desfazerConciliacao()` - Removido +/- do saldo
- [x] `ConciliacaoController::update()` - Removido +/- do saldo (2 locais)
- [x] `TransacaoFinanceiraController::destroy()` - Removido +/- do saldo
- [x] `BankStatement::conciliarCom()` - Removido +/- do saldo

### Fase 2: Implementação de Cálculo Dinâmico
- [x] `EntidadeFinanceira::calculateBalance()` - Método que calcula saldo dinamicamente
- [x] `EntidadeFinanceira::saldo_dinamico` - Accessor para usar em views

### Fase 3: Validação de Valores Absolutos
- [x] `NotaFiscalImportController::parseValor()` - Adicionado `abs()` para garantir valores positivos

---

## 📊 Fórmula de Cálculo de Saldo

```
saldo_dinamico = saldo_inicial + Σ(entradas) - Σ(saidas)

Onde:
- Valores em movimentacoes: sempre positivos (abs)
- Valores em transacoes_financeiras: sempre positivos (abs)
- Coluna tipo: 'entrada' ou 'saida' define a operação
```

---

## 🧪 Casos de Teste a Validar

### Teste 1: Criar Entidade e Conciliar Entrada
```
1. Criar EntidadeFinanceira com saldo_inicial = 0
2. Conciliar entrada de 5.00
3. Verificar: saldo_dinamico deve ser 5.00
4. Desfazer conciliação
5. Verificar: saldo_dinamico deve voltar a 0
```

### Teste 2: Múltiplas Operações
```
1. Criar EntidadeFinanceira com saldo_inicial = 100
2. Adicionar entrada de 50.00
3. Adicionar saída de 20.00
4. Verificar: saldo_dinamico = 100 + 50 - 20 = 130
5. Deletar a saída
6. Verificar: saldo_dinamico = 100 + 50 = 150
```

### Teste 3: Transferência Entre Contas
```
1. Criar Entidade A com saldo_inicial = 500
2. Criar Entidade B com saldo_inicial = 0
3. Transferir 100 de A para B (via update)
4. Verificar:
   - A: saldo_dinamico = 500 - 100 = 400
   - B: saldo_dinamico = 0 + 100 = 100
```

### Teste 4: Valores Negativos não Salvos
```
1. Tentar criar movimentacao com valor = -50
2. Verificar: sistema converte para abs(50)
3. Campo tipo define se é entrada ou saida
4. Valor armazenado: 50 (positivo)
```

---

## 📝 Comandos para Testar Manualmente

### Via Artisan Tinker:
```php
php artisan tinker

// Teste 1: Criar entidade e verificar saldo
$entidade = EntidadeFinanceira::create([
    'nome' => 'Teste',
    'tipo' => 'caixa',
    'saldo_inicial' => 100,
    'company_id' => 1
]);

echo "Saldo Inicial: " . $entidade->saldo_inicial;
echo "Saldo Dinâmico: " . $entidade->saldo_dinamico; // Deve ser 100

// Teste 2: Adicionar entrada
Movimentacao::create([
    'entidade_id' => $entidade->id,
    'tipo' => 'entrada',
    'valor' => 50,
    'company_id' => 1
]);

echo "Saldo Dinâmico após entrada: " . $entidade->fresh()->saldo_dinamico; // Deve ser 150

// Teste 3: Adicionar saída
Movimentacao::create([
    'entidade_id' => $entidade->id,
    'tipo' => 'saida',
    'valor' => 20,
    'company_id' => 1
]);

echo "Saldo Dinâmico após saída: " . $entidade->fresh()->saldo_dinamico; // Deve ser 130

// Teste 4: Verificar que valores são absolutos
Movimentacao::where('entidade_id', $entidade->id)->get();
// Todos os valores devem ser positivos, tipo define operação
```

---

## 🔍 Verificação de Banco de Dados

```sql
-- Verificar que não há valores negativos em movimentacoes
SELECT id, entidade_id, tipo, valor 
FROM movimentacoes 
WHERE valor < 0 
AND entidade_id = [ID];
-- Resultado esperado: 0 linhas

-- Verificar que não há valores negativos em transacoes_financeiras
SELECT id, entidade_id, tipo, valor 
FROM transacoes_financeiras 
WHERE valor < 0 
AND entidade_id = [ID];
-- Resultado esperado: 0 linhas

-- Calcular saldo dinamicamente (simular o método)
SELECT 
  (SELECT saldo_inicial FROM entidades_financeiras WHERE id = [ID]) +
  (SELECT COALESCE(SUM(CASE WHEN tipo='entrada' THEN valor ELSE -valor END), 0) 
   FROM movimentacoes WHERE entidade_id = [ID]) +
  (SELECT COALESCE(SUM(CASE WHEN tipo='entrada' THEN valor ELSE -valor END), 0) 
   FROM transacoes_financeiras WHERE entidade_id = [ID]) as saldo_calculado;
```

---

## 🚀 Próximas Fases (Se Necessário)

### Fase 4: Atualizar Views
- [ ] Remover inputs editáveis de `saldo_atual`
- [ ] Usar `{{ $entidade->saldo_dinamico }}` em listagens
- [ ] Atualizar JavaScript para recalcular saldo após operações

### Fase 5: Performance (Se Necessário)
- [ ] Adicionar índices em (entidade_id, tipo) em movimentacoes
- [ ] Adicionar índices em (entidade_id, tipo) em transacoes_financeiras
- [ ] Considerar caching de saldo (se chamadas forem frequentes)

---

## ✅ Checklist de Implementação

- [x] Remover modificações diretas de saldo_atual
- [x] Criar método calculateBalance()
- [x] Criar accessor saldo_dinamico
- [x] Garantir valores absolutos
- [ ] Testar casos de uso
- [ ] Atualizar views (próxima fase)
- [ ] Validar performance (próxima fase)

---

**Status:** ✅ Fase 1-3 Completas | ⏳ Aguardando Testes
