# 🌱 Seeder de Formas de Pagamento

## 📋 Visão Geral

Este seeder cria automaticamente **20 formas de pagamento padrão** em todos os tenants do sistema, incluindo as principais formas de pagamento utilizadas no mercado brasileiro.

## 🎯 Formas de Pagamento Criadas

### 💳 **Cartões e Pagamentos Eletrônicos**
- **Pix** - Pagamento instantâneo via PIX
- **Cartão de crédito via outros bancos** - 3.99% de taxa
- **Cartão de débito via outros bancos** - 1.99% de taxa
- **Carteira Digital** - 1.50% de taxa
- **Crédito virtual** - Sem taxa
- **Crédito da loja** - Sem taxa

### 🏦 **Pagamentos Bancários**
- **Boleto Via outros bancos** - R$ 3,50 de taxa
- **Transferência bancária** - Sem taxa
- **Depósito bancário** - Sem taxa
- **Débito Automático** - Sem taxa

### 💰 **Pagamentos Tradicionais**
- **Dinheiro** - Sem taxa
- **Cheque** - Sem taxa
- **Cashback** - 2.50% de taxa

### 🎁 **Vales e Benefícios**
- **Vale-alimentação** - Sem taxa
- **Vale-refeição** - Sem taxa
- **Vale-combustível** - Sem taxa
- **Vale-presente** - Sem taxa
- **Programa de fidelidade** - Sem taxa

### 📊 **Outros**
- **Outros** - Sem taxa
- **Sem pagamento** - Sem taxa

## 🚀 Como Executar

### 1. **Para Todos os Tenants**
```bash
php artisan tenant:seed-formas-pagamento --all
```

### 2. **Para um Tenant Específico**
```bash
php artisan tenant:seed-formas-pagamento --tenant=ID_DO_TENANT
```

### 3. **Para Novos Tenants**
O seeder é executado automaticamente quando um novo tenant é criado através do `TenantDatabaseSeeder`.

## 📊 Estrutura dos Dados

Cada forma de pagamento inclui:

```php
[
    'nome' => 'Nome da Forma de Pagamento',
    'codigo' => 'CODIGO_UNICO',
    'ativo' => true,
    'tipo_taxa' => 'valor_fixo' | 'porcentagem',
    'taxa' => 0.00, // Valor em reais ou porcentagem
    'prazo_liberacao' => 0, // Dias para liberação
    'metodo_integracao' => 'API Gateway',
    'observacao' => 'Descrição detalhada'
]
```

## 🔧 Configurações por Tipo

### **Taxas por Porcentagem**
- Cartão de crédito: 3.99%
- Cartão de débito: 1.99%
- Carteira Digital: 1.50%
- Cashback: 2.50%

### **Taxas Fixas**
- Boleto: R$ 3,50
- Outros: R$ 0,00

### **Prazos de Liberação**
- PIX: 0 dias (instantâneo)
- Cartão de crédito: 30 dias
- Boleto: 3 dias
- Transferência: 1 dia
- Cheque: 5 dias
- Outros: 0 dias

## 🛡️ Proteções do Seeder

### **Evita Duplicatas**
```php
FormasPagamento::firstOrCreate(
    ['codigo' => $forma['codigo']], // Verifica se já existe
    $forma // Cria apenas se não existir
);
```

### **Logs Detalhados**
```bash
[2025-01-07 10:30:00] local.INFO: Formas de pagamento padrão criadas com sucesso!
```

## 📈 Benefícios

1. **✅ Padronização**: Todas as formas de pagamento padrão disponíveis
2. **✅ Facilidade**: Não precisa cadastrar manualmente
3. **✅ Consistência**: Dados padronizados em todos os tenants
4. **✅ Flexibilidade**: Pode ser personalizado posteriormente
5. **✅ Segurança**: Evita duplicatas e erros de digitação

## 🔄 Atualizações

Para adicionar novas formas de pagamento:

1. **Edite o seeder** `database/seeders/FormasPagamentoSeeder.php`
2. **Adicione os novos dados** no array `$formasPagamento`
3. **Execute o comando** para atualizar os tenants existentes

## 📝 Exemplo de Uso

```bash
# Executar em todos os tenants
php artisan tenant:seed-formas-pagamento --all

# Saída esperada:
# 🌱 Iniciando seed de formas de pagamento...
# Encontrados 5 tenants para processar.
# [████████████████████] 100%
# ✅ Seed de formas de pagamento concluído!
```

## 🎯 Resultado Final

Após executar o seeder, todos os tenants terão:

- ✅ **20 formas de pagamento** disponíveis
- ✅ **Taxas configuradas** corretamente
- ✅ **Prazos de liberação** definidos
- ✅ **Métodos de integração** especificados
- ✅ **Observações detalhadas** para cada forma

Agora você pode usar essas formas de pagamento ao criar receitas e despesas no sistema! 🚀
