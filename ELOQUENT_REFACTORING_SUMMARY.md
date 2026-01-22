# Refatoração: Relacionamento Polimórfico com Eloquent

## Resumo Executivo

Implementação completa de relacionamentos polimórficos usando Eloquent para conectar `TransacaoFinanceira` com `Movimentacao`, removendo a necessidade de atribuição manual de `movimentacao_id`.

## Objetivo

Transformar a tabela `Movimentacao` em um verdadeiro **Livro Razão (General Ledger)** que pode estar associado a múltiplas entidades (não apenas `TransacaoFinanceira`), usando o padrão polimórfico do Laravel.

## Mudanças Implementadas

### 1. **Models**

#### `TransacaoFinanceira` (`app/Models/Financeiro/TransacaoFinanceira.php`)
- ✅ Removida `movimentacao_id` da `$fillable` array (evita atribuição manual)
- ✅ Relacionamento `morphOne()` já estava configurado:
  ```php
  public function movimentacao()
  {
      return $this->morphOne(Movimentacao::class, 'origem');
  }
  ```

#### `Movimentacao` (`app/Models/Movimentacao.php`)
- ✅ Removida auto-referência `movimentacao_id` da `$fillable` array
- ✅ Relacionamento `MorphTo` para acessar o modelo original:
  ```php
  public function origem(): MorphTo
  {
      return $this->morphTo();
  }
  ```

### 2. **Service: TransacaoFinanceiraService**

#### `criarLancamento()` 
- Padrão: Criar TransacaoFinanceira PRIMEIRO, depois usar Eloquent para criar Movimentacao
- Código:
  ```php
  $transacao = TransacaoFinanceira::create($data);
  $movimentacao = $transacao->movimentacao()->create($this->prepararDadosMovimentacao($data));
  ```

#### `prepararDadosMovimentacao()`
- Novo método que prepara dados para Eloquent criar a movimentacao
- Retorna array com campos necessários (Eloquent gerencia origem_type e origem_id automaticamente)

#### `processarLancamentoPadrao()`
- Atualizado para usar: `$transacao->movimentacao()->create($dados)`
- Eloquent define automaticamente `origem_type` e `origem_id`

### 3. **Controller: BancoController**

#### `movimentacao()` (Método Privado)
- Refatorado para usar Eloquent: `$transacao->movimentacao()->create([...])`
- Mais limpo e segue padrão do Laravel
- Eloquent gerencia os campos polimórficos automaticamente

#### `criarParcelas()`
- Removida atribuição manual de `movimentacao_id`
- Padrão:
  ```php
  $transacaoParcela = TransacaoFinanceira::create($dadosParcela);
  $transacaoParcela->movimentacao()->create([...]);
  ```

#### `destroyRecurrenceSeries()`
- Atualizado para usar relacionamento: `$trans->movimentacao`
- Ao invés de: `Movimentacao::find($trans->movimentacao_id)`

### 4. **Migration**

#### `2026_01_22_072553_add_polymorph_to_movimentacoes_table.php`
- ✅ Já implementada com sucesso
- Adiciona campos `origem_type` (VARCHAR) e `origem_id` (BIGINT)
- Cria índice automático via `nullableMorphs()`
- Status: **Migrado com sucesso** ✅

## Padrão de Uso

### ❌ ANTIGO (Não recomendado):
```php
// Manual - erro-prone
$movimentacao = Movimentacao::create([
    'entidade_id' => $data['entidade_id'],
    'tipo' => 'entrada',
    'valor' => $data['valor'],
    'origem_type' => TransacaoFinanceira::class,
    'origem_id' => $transacao->id,  // Sempre lembrar de adicionar
    // ... outros campos
]);
$data['movimentacao_id'] = $movimentacao->id;
$transacao = TransacaoFinanceira::create($data);
```

### ✅ NOVO (Recomendado):
```php
// Eloquent - seguro e automático
$transacao = TransacaoFinanceira::create($data);
$movimentacao = $transacao->movimentacao()->create([
    'entidade_id' => $data['entidade_id'],
    'tipo' => 'entrada',
    'valor' => $data['valor'],
    // ... outros campos
    // origem_type e origem_id são gerenciados automaticamente!
]);
```

### Acessando a Relação:
```php
// Para acessar a movimentação de uma transação
$transacao = TransacaoFinanceira::find($id);
$movimentacao = $transacao->movimentacao;  // Usa relacionamento

// Para acessar a transação de uma movimentação
$movimentacao = Movimentacao::find($id);
$transacao = $movimentacao->origem;  // Acessa TransacaoFinanceira automaticamente
```

## Benefícios

1. **Segurança**: Eloquent garante que `origem_type` e `origem_id` sempre sejam consistentes
2. **Flexibilidade**: Movimentacao pode estar associada a qualquer modelo (TransacaoFinanceira, Dízimo, Patrimônio, etc.)
3. **Limpeza**: Menos atribuições manuais, menos erros
4. **Legibilidade**: Código mais claro e intuitivo
5. **Manutenibilidade**: Padrão Laravel reduz necessidade de documentação

## Compatibilidade

- ✅ Coluna `movimentacao_id` ainda existe em `transacoes_financeiras` para compatibilidade com código legado
- ✅ Código existente que lê `movimentacao_id` continua funcionando
- ⚠️ Código novo deveria evitar usar `movimentacao_id` diretamente

## Próximos Passos (Opcional)

Para limpeza completa em futuro (após refatorar todo código legado):
1. Remover coluna `movimentacao_id` de `transacoes_financeiras`
2. Atualizar código legado que ainda usa `movimentacao_id` para usar relacionamento

## Arquivos Modificados

- ✅ `/app/Models/Financeiro/TransacaoFinanceira.php` - Removido `movimentacao_id` da fillable
- ✅ `/app/Models/Movimentacao.php` - Removido auto-referência da fillable
- ✅ `/app/Http/Controllers/App/BancoController.php` - Refatorado para Eloquent (3 métodos)
- ✅ `/app/Services/TransacaoFinanceiraService.php` - Usa Eloquent para criar movimentações

## Testes Recomendados

1. Criar uma nova transação financeira e verificar se movimentação é criada corretamente
2. Verificar se `origen_type` = `App\Models\Financeiro\TransacaoFinanceira`
3. Verificar se `origen_id` = `transacao->id`
4. Acessar movimentação via: `$transacao->movimentacao`
5. Testar exclusão de transação (deve deletar movimentação associada)

## Comando de Teste Rápido

```bash
# No Tinker
$transacao = App\Models\Financeiro\TransacaoFinanceira::latest()->first();
$movimentacao = $transacao->movimentacao;  // Deveria retornar a movimentação
echo "Origem Type: " . $movimentacao->origem_type;  // App\Models\Financeiro\TransacaoFinanceira
echo "Origem ID: " . $movimentacao->origem_id;      // $transacao->id
```

---
**Data**: 2025-01-22  
**Status**: ✅ Implementado e Pronto para Teste
