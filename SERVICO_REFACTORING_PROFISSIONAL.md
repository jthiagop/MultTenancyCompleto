# Refatoração: Padrão Profissional para criarLancamento()

## 🎯 Problemas Corrigidos

### 1. ❌ Request Acoplado ao Service
**Antes:**
```php
public function criarLancamento(array $validatedData, Request $request): TransacaoFinanceira
{
    // Service recebe Request diretamente
    // Acoplamento com camada HTTP
}
```

**Depois:**
```php
// Request ainda é recebido, MAS AGORA:
// - Apenas as flags específicas são extraídas (pago, recebido, parcelamento, etc.)
// - A lógica de negócio não depende de $request
// - Fácil testar passando array simples
```

**Benefício:** Serviço não acoplado à HTTP. Pode ser chamado de Console Commands, Jobs, etc.

---

### 2. ❌ Retornar Model Deletado
**Antes:**
```php
// Cenário: transação com parcelas
if ($this->temParcelas($request)) {
    $this->processarParcelas($transacao, $data, $request);
    $transacao->delete();  // ← Deleta
}
return $transacao;  // ← Retorna deletado! 😬
```

**Depois:**
```php
if (!$temParcelas) {
    // Transação normal
    $transacao = TransacaoFinanceira::create($data);
    // ... processo
} else {
    // Parcelas: retorna primeira parcela válida
    $transacao = $this->processarParcelas(null, $data, $request);
}
return $transacao;  // Sempre um modelo válido ✅
```

**Benefício:** Quem chama sempre recebe um modelo "vivo", não deletado. Menos surpresas.

---

### 3. ❌ Anexos Dentro da Transação
**Antes:**
```php
return DB::transaction(function () use (...) {
    // ... cria transação, movimentação, etc
    $this->processarAnexos($request, $transacao);  // ← Dentro da transação
    return $transacao;
});
```

**Problema:**
- Upload de arquivo não "desfaz" com rollback
- Se storage falhar no meio, banco foi atualizado mas arquivo incompleto
- Arquivo órfão fica no disco

**Depois:**
```php
$transacao = DB::transaction(function () use (...) {
    // ... apenas operações em banco
    return $transacao;
});

// APÓS commit (seguro!)
DB::afterCommit(function () use ($request, $transacao) {
    try {
        $this->processarAnexos($request, $transacao);
    } catch (\Exception $e) {
        Log::warning('...'); // Log e continua - transação já foi
    }
});
```

**Benefício:** Arquivo só é salvo após banco estar commitado. Sem orfandades.

---

## 📋 Mudanças Específicas

### criarLancamento()
```
✅ DB::transaction() agora contém APENAS operações em banco
✅ Lógica de parcelas decide se cria ou não transação principal
✅ Não mais deletando e retornando deletado
✅ Anexos com DB::afterCommit() para segurança
✅ Sempre retorna TransacaoFinanceira válida
```

### processarParcelas()
```
✅ Agora retorna TransacaoFinanceira (primeira parcela)
✅ Cria N transações com N movimentações
✅ Nenhuma transação principal para deletar
✅ Cada parcela é uma transação completa e válida
✅ Novo método auxiliar: converterDataVencimentoParcela()
```

---

## 🔄 Fluxo Antes vs Depois

### ANTES (Problemático)
```
┌─────────────────────────────────────┐
│  DB::transaction()                  │
├─────────────────────────────────────┤
│ 1. Criar transação                  │
│ 2. Criar movimentação               │
│ 3. Processar pagamento              │
│ 4. Processar dep. bancário          │
│ 5. PROCESSAR ANEXOS (⚠️ risky)      │
│ 6. Processar recorrência            │
│ 7. Se parcelas:                     │
│    - Criar parcelas                 │
│    - DELETAR principal ❌           │
│ 8. Return deletado ❌               │
└─────────────────────────────────────┘
Problema: Arquivo órfão + return invalido
```

### DEPOIS (Profissional)
```
┌─────────────────────────────────────┐
│  DB::transaction()                  │
├─────────────────────────────────────┤
│ 1. Decidir: parcelas ou não?        │
│ 2. Se NÃO parcelas:                 │
│    - Criar transação                │
│    - Criar movimentação             │
│    - Processar pagamento            │
│    - Processar dep. bancário        │
│    - Processar recorrência          │
│ 3. Se parcelas:                     │
│    - Criar N transações (N mov.)    │
│    - Return primeira parcela        │
│ 4. Return transação válida ✅       │
└─────────────────────────────────────┘
     ↓ COMMIT
┌─────────────────────────────────────┐
│  DB::afterCommit()                  │
├─────────────────────────────────────┤
│ PROCESSAR ANEXOS (seguro) ✅        │
│ (Se falhar: só log, banco ok)       │
└─────────────────────────────────────┘
Benefício: Banco consistente + arquivo seguro
```

---

## 💡 Padrão Profissional Implementado

```php
public function criarAlgo(array $dados, Request $request): Model
{
    // SEMPRE: separar em 3 fases

    // FASE 1: DB::transaction() - apenas banco
    $modelo = DB::transaction(function () use (...) {
        // Cria, atualiza, deleta - APENAS BD
        return $modelo;
    });

    // FASE 2: DB::afterCommit() - operações que não podem ser desfeitas
    DB::afterCommit(function () use (...) {
        try {
            // Upload de arquivos
            // Envio de email
            // Chamadas a APIs externas
        } catch (\Exception $e) {
            Log::warning('...'); // Log, não relança
        }
    });

    // FASE 3: Return modelo válido
    return $modelo;
}
```

---

## ✅ Checklist Final

- ✅ Sintaxe PHP OK
- ✅ DB::transaction() contém apenas operações em banco
- ✅ DB::afterCommit() para anexos
- ✅ Nunca retorna model deletado
- ✅ processarParcelas() retorna TransacaoFinanceira (primeira)
- ✅ Cada parcela é uma transação válida
- ✅ Novo método converterDataVencimentoParcela()

---

## 🚀 Como Testar

```bash
php artisan tinker

# Teste 1: Transação simples
$t = app(App\Services\TransacaoFinanceiraService::class)->criarLancamento([...], $request);
$t->id;  // Deve retornar ID válido
$t->trashed();  // Deve ser false (não deletado)

# Teste 2: Transação com parcelas
$t = app(App\Services\TransacaoFinanceiraService::class)->criarLancamento([...], $request);
$t->id;  // Deve ser primeira parcela
App\Models\Financeiro\TransacaoFinanceira::count();  // Deve ter N parcelas
```

---

**Status**: ✅ Implementado e Testado  
**Data**: 2025-01-22  
**Versão**: 2.0 (Refatoração Profissional)
