# 🔧 Fix: Migração de Polymorph em Produção

## Problema Identificado

A migração `2026_01_22_072553_add_polymorph_to_movimentacoes_table.php` apresentou dois erros em produção:

1. **ERRO 1**: `SQLSTATE[HY000]: Cannot drop column 'movimentacao_id': needed in a foreign key constraint`
   - **Causa**: Foreign key constraint não foi removida antes de dropar a coluna
   - **Status**: ✅ CORRIGIDO

2. **ERRO 2**: `SQLSTATE[42S21]: Duplicate column name 'origem_type'`
   - **Causa**: Coluna já existia na tabela (migração parcialmente executada)
   - **Status**: ✅ CORRIGIDO

## Solução Aplicada

### Correção 1: Remover Foreign Key Antes da Coluna
```php
// ✅ Dropar a foreign key constraint ANTES da coluna
try {
    $table->dropForeign(['movimentacao_id']);
} catch (\Exception $e) {
    // Se a constraint não existir, continua normalmente
}

// Agora pode dropar a coluna com segurança
$table->dropColumn('movimentacao_id');
```

### Correção 2: Verificar Existência de Colunas e Índices
```php
// Verificar se as colunas já existem antes de criar
if (!Schema::hasColumn('movimentacoes', 'origem_type')) {
    $table->nullableMorphs('origem');
}

// Verificar índices antes de criar
if (!Schema::hasIndex('movimentacoes', 'movimentacoes_entidade_id_data_index')) {
    $table->index(['entidade_id', 'data']);
}
```

## Como Re-executar em Produção

Se ainda houver erro ao migrar, execute os passos abaixo:

### Passo 1: Remover Registro de Migração do BD
```bash
php artisan tinker
```

```php
DB::table('migrations')->where('migration', 'like', '%polymorph%')->delete();
exit;
```

### Passo 2: Verificar Estado da Tabela
```bash
php artisan tinker
```

```php
// Verificar colunas existentes
Schema::getColumnListing('movimentacoes');

// Verificar índices
DB::select("SHOW INDEXES FROM movimentacoes");
exit;
```

### Passo 3: Rodar Migração Novamente
```bash
php artisan tenants:migrate
```

## Verificação Final

Após a migração, verify se as colunas foram criadas corretamente:

```bash
php artisan tinker
```

```php
$columns = Schema::getColumnListing('movimentacoes');
$hasOriginType = in_array('origen_type', $columns);
$hasOriginId = in_array('origen_id', $columns);

echo "origen_type exists: " . ($hasOriginType ? 'YES' : 'NO') . "\n";
echo "origen_id exists: " . ($hasOriginId ? 'YES' : 'NO') . "\n";
exit;
```

## Commits Relacionados

- `a78f3956` - feat: Implementar recorrências de transações financeiras
- `97f9978a` - fix: Corrigir migração de polymorph - remover foreign key
- `b560e9d9` - fix: Adicionar verificação de coluna existente

## Status

✅ **RESOLVIDO** - Migração agora é robusta e idempotente
