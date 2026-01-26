# 📋 Guia de Migração: Descontinuar `saldo_atual` Estático

**Status:** Preparado para migração futura  
**Impacto:** Baixo (accessor `saldo_dinamico` já implementado)

---

## 🎯 Objetivo

Remover completamente o campo `saldo_atual` da tabela `entidades_financeiras` quando todos os sistemas migrarem para usar `saldo_dinamico`.

---

## 📊 Timeline Sugerido

### Fase 1: Implementação (✅ CONCLUÍDA)
- [x] Criar método `calculateBalance()`
- [x] Criar accessor `saldo_dinamico`
- [x] Remover modificações diretas de `saldo_atual`
- [x] Atualizar views para usar `saldo_dinamico`

### Fase 2: Validação (Próxima)
- [ ] Testar em ambiente de staging
- [ ] Validar cálculos para 100+ entidades
- [ ] Verificar performance em queries pesadas
- [ ] Confirmar logs e auditoria

### Fase 3: Deprecação (Opcional - 3-6 meses)
- [ ] Marcar `saldo_atual` como deprecated
- [ ] Manter sincronização manual (para compatibilidade)
- [ ] Documentar transição para clientes/parceiros

### Fase 4: Remoção (Opcional - 6-12 meses)
- [ ] Criar migration para dropar coluna
- [ ] Backup completo antes
- [ ] Testar rollback

---

## 🔄 Processo de Migração (Se Decidir Remover)

### Step 1: Criar Migration
```php
// database/migrations/[timestamp]_make_saldo_atual_nullable.php

Schema::table('entidades_financeiras', function (Blueprint $table) {
    $table->decimal('saldo_atual', 15, 2)->nullable()->change();
});
```

### Step 2: Sincronizar Dados
```php
// database/seeders/SyncSaldoAtualSeeder.php

public function run()
{
    // ✅ Sincronizar saldo_atual com saldo_dinamico (backup)
    foreach (EntidadeFinanceira::all() as $entidade) {
        $entidade->update([
            'saldo_atual' => $entidade->calculateBalance()
        ]);
    }
}
```

### Step 3: Validar Integridade
```sql
-- Verificar que todos os saldos estão sincronizados
SELECT e.id, e.saldo_atual,
  (e.saldo_inicial + 
   COALESCE(SUM(CASE WHEN m.tipo='entrada' THEN m.valor ELSE -m.valor END), 0) +
   COALESCE(SUM(CASE WHEN t.tipo='entrada' THEN t.valor ELSE -t.valor END), 0)) as saldo_calculado
FROM entidades_financeiras e
LEFT JOIN movimentacoes m ON e.id = m.entidade_id
LEFT JOIN transacoes_financeiras t ON e.id = t.entidade_id
GROUP BY e.id
HAVING e.saldo_atual != saldo_calculado;
-- Resultado esperado: 0 linhas (todos sincronizados)
```

### Step 4: Dropar Coluna
```php
// database/migrations/[timestamp]_drop_saldo_atual_column.php

Schema::table('entidades_financeiras', function (Blueprint $table) {
    $table->dropColumn('saldo_atual');
});
```

---

## ⚠️ Considerações de Compatibilidade

### APIs que Usam `saldo_atual`
```php
// Buscar endpoints que usam saldo_atual
grep -r "saldo_atual" app/Http --include="*.php"

// Retornar saldo_dinamico também para compatibilidade
return response()->json([
    'entidade' => $entidade,
    'saldo_atual' => $entidade->saldo_dinamico,        // Para compatibilidade
    'saldo_dinamico' => $entidade->saldo_dinamico,     // Novo
]);
```

### Clientes/Integrações Externas
```
Se há APIs consumindo `saldo_atual`, oferecer deprecation notice:

Cabeçalho HTTP:
Deprecation: true
Sunset: Sun, 31 Dec 2026 23:59:59 GMT
Link: <https://docs.dominus.com/migration/saldo-dinamico>; rel="deprecation"
```

---

## 📊 Performance Impact

### Antes (BD Query)
```
SELECT saldo_atual FROM entidades_financeiras WHERE id = ?
Tempo: 1ms (acesso direto)
```

### Depois (Cálculo)
```
SELECT saldo_inicial FROM entidades_financeiras WHERE id = ?
SUM(CASE...) FROM movimentacoes WHERE entidade_id = ?
SUM(CASE...) FROM transacoes_financeiras WHERE entidade_id = ?
Tempo: 5-15ms (3 queries)
```

### Otimização (Com Índices)
```sql
CREATE INDEX idx_movimentacoes_entidade_tipo ON movimentacoes(entidade_id, tipo);
CREATE INDEX idx_transacoes_entidade_tipo ON transacoes_financeiras(entidade_id, tipo);
-- Reduz para: 3-5ms
```

### Otimização (Com Caching)
```php
cache()->remember("saldo_{$id}", 3600, fn() => calculateBalance());
// Reduz para: <1ms (hit), 5-10ms (miss)
```

---

## 🧪 Testes Recomendados

### Unit Tests
```php
test('calculateBalance retorna saldo correto', function () {
    $entidade = EntidadeFinanceira::create(['saldo_inicial' => 100]);
    
    Movimentacao::create(['entidade_id' => $entidade->id, 'tipo' => 'entrada', 'valor' => 50]);
    Movimentacao::create(['entidade_id' => $entidade->id, 'tipo' => 'saida', 'valor' => 20]);
    
    expect($entidade->fresh()->saldo_dinamico)->toBe(130);
});
```

### Integration Tests
```php
test('saldo_dinamico atualiza após conciliação', function () {
    $entidade = EntidadeFinanceira::create(['saldo_inicial' => 500]);
    $bankStatement = BankStatement::create(['entidade_financeira_id' => $entidade->id, 'amount' => 100]);
    $transacao = TransacaoFinanceira::create(['entidade_id' => $entidade->id, 'tipo' => 'entrada', 'valor' => 100]);
    
    $bankStatement->conciliarCom($transacao, 10000); // 100 reais em centavos
    
    expect($entidade->fresh()->saldo_dinamico)->toBe(600);
});
```

---

## 📝 Checklist de Migração

- [ ] Criar issue de deprecation
- [ ] Notificar clientes/parceiros
- [ ] Testar em staging (1 mês)
- [ ] Sync de dados com seed
- [ ] Validar integridade com SQL
- [ ] Criar migration de dropar
- [ ] Testar rollback
- [ ] Deploy em prod
- [ ] Monitorar por 2 semanas
- [ ] Arquivar migration antiga

---

## 🔗 Referências

- [Migration File](../database/migrations/)
- [Model EntidadeFinanceira](../app/Models/EntidadeFinanceira.php)
- [Teste de Integridade](./TESTE_SALDO_DINAMICO.md)

---

**Status:** Preparado, aguardando aprovação para Fase 2.
