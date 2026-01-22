# Instruções de Teste: Relacionamento Polimórfico Eloquent

## Status Atual

✅ **Refatoração Completa**
- Models configurados com relacionamentos `morphOne()` e `morphTo()`
- Service implementado com padrão Eloquent
- Controller refatorado para usar Eloquent
- Todos os testes de sintaxe PHP passaram
- Relacionamentos confirmados com método_exists()

## Testes Básicos (Tinker)

### 1. Verificar Migração
```bash
php artisan tinker

# Verificar tabela movimentacoes
\DB::table('movimentacoes')->first();

# Verificar colunas polimórficas
\DB::table('movimentacoes')->select('origem_type', 'origem_id')->first();
```

### 2. Criar uma Transação e Verificar Movimentação

```bash
php artisan tinker

# Criar transação
\$transacao = App\Models\Financeiro\TransacaoFinanceira::create([
    'company_id' => 1,
    'data_competencia' => '2025-01-22',
    'data_vencimento' => '2025-02-22',
    'entidade_id' => 1,
    'tipo' => 'saida',
    'valor' => 100.00,
    'descricao' => 'Teste Eloquent',
    'situacao' => 'em_aberto',
]);

echo "Transação criada: " . \$transacao->id . PHP_EOL;

# Criar movimentação via Eloquent
\$mov = \$transacao->movimentacao()->create([
    'entidade_id' => 1,
    'tipo' => 'saida',
    'valor' => 100.00,
    'data' => '2025-01-22',
    'descricao' => 'Teste',
    'company_id' => 1,
]);

echo "Movimentação criada: " . \$mov->id . PHP_EOL;
echo "origem_type: " . \$mov->origem_type . PHP_EOL;
echo "origem_id: " . \$mov->origem_id . PHP_EOL;
```

**Resultado Esperado:**
- `origem_type` = `App\Models\Financeiro\TransacaoFinanceira`
- `origem_id` = ID da transação

### 3. Acessar Movimentação Através da Transação

```bash
# Carregar novamente
\$transacao = App\Models\Financeiro\TransacaoFinanceira::find(1);

# Acessar movimentação via relacionamento
\$movimentacao = \$transacao->movimentacao;

echo "Movimentação acessada: " . \$movimentacao->id . PHP_EOL;
echo "Valor: " . \$movimentacao->valor . PHP_EOL;
```

### 4. Acessar Transação Através da Movimentação

```bash
# Carregar movimentação
\$movimentacao = App\Models\Movimentacao::find(1);

# Acessar transação via morphTo
\$transacao = \$movimentacao->origem;

echo "Transação via morphTo: " . \$transacao->id . PHP_EOL;
echo "Classe: " . get_class(\$transacao) . PHP_EOL;
```

## Testes Funcionais

### 1. Testar Criação de Transação via Store

```bash
# Fazer POST request para criar transação
POST /transacoes-financeiras

Body (form-data):
- company_id: 1
- data_competencia: 22/01/2025
- data_vencimento: 22/02/2025
- entidade_id: 1
- tipo: saida
- valor: 250.00
- descricao: Teste de Criação
- lancamento_padrao_id: 1
- cost_center_id: 1
- tipo_documento: NF
- numero_documento: 12345
- origem: Manual
- situacao: em_aberto
```

**Verificar:**
- Transação foi criada
- Movimentação foi criada automaticamente
- `origem_type` e `origem_id` estão corretos
- Sem erro em `movimentacao_id`

### 2. Testar Exclusão em Cascata

```bash
# Deletar transação
\$transacao = App\Models\Financeiro\TransacaoFinanceira::find(1);
\$transacao->delete();

# Verificar se movimentação também foi deletada
\$mov = App\Models\Movimentacao::find(1);
// Deveria ser null (soft-deleted)
```

### 3. Testar Parcelas

```bash
# Criar transação com parcelas
POST /transacoes-financeiras

Body:
- ... (dados da transação)
- parcelamento: 3x
- parcelas[0][valor]: 100.00
- parcelas[0][vencimento]: 22/01/2025
- parcelas[1][valor]: 100.00
- parcelas[1][vencimento]: 22/02/2025
- parcelas[2][valor]: 100.00
- parcelas[2][vencimento]: 22/03/2025
```

**Verificar:**
- 3 transações foram criadas
- 3 movimentações foram criadas
- Cada movimentação está vinculada à sua transação via `origem_type` e `origem_id`
- Transação principal foi deletada

## Logs de Verificação

Se houver erro, verificar:

1. **Sintaxe PHP:**
   ```bash
   php -l app/Http/Controllers/App/BancoController.php
   php -l app/Services/TransacaoFinanceiraService.php
   php -l app/Models/Financeiro/TransacaoFinanceira.php
   php -l app/Models/Movimentacao.php
   ```

2. **Logs de Erro:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verificar Migration:**
   ```bash
   php artisan migrate:status
   php artisan tenants:migrate --list
   ```

## Checklist Final

- [ ] Sintaxe PHP OK (sem erros)
- [ ] Relacionamentos definidos (morphOne/morphTo)
- [ ] Transação criada com sucesso
- [ ] Movimentação criada automaticamente
- [ ] `origem_type` e `origem_id` corretos
- [ ] Acesso bidireccional funcionando
- [ ] Exclusão em cascata funcionando
- [ ] Parcelas criadas corretamente
- [ ] Sem erros em `movimentacao_id` no fillable

## Status de Implementação

| Item | Status | Detalhes |
|------|--------|----------|
| Migration | ✅ | Coluna polimórfica criada e testada |
| Models | ✅ | Relacionamentos configurados |
| Service | ✅ | Padrão Eloquent implementado |
| Controller | ✅ | Refatorado para usar Eloquent |
| Parcelas | ✅ | Usa Eloquent para criar |
| Recorrência | ✅ | Processada no Service |
| Testes | ⏳ | Aguardando execução manual |

## Próximas Ações (Opcional)

1. **Deprecate `movimentacao_id`**: Avisar que a coluna será removida em futuro
2. **Refatorar RecurrenceService**: Usar Eloquent ao invés de SQL manual
3. **Refatorar Code gerado**: Remover referências a `movimentacao_id` em código novo
4. **Testes Automatizados**: Criar testes PHPUnit para polimorfismo

---
**Última Atualização**: 2025-01-22  
**Versão**: 1.0 - Implementação Completa
