# 🏗️ Sistema Robusto de Setup de Tenants

## 📋 Visão Geral

Este sistema garante que **todos os tenants** sejam criados e configurados corretamente, evitando problemas como:
- ✅ Tabelas faltantes
- ✅ Colunas não criadas
- ✅ Seeds não executados
- ✅ Dados essenciais ausentes

## 🔧 Componentes do Sistema

### 1. **RobustTenantSetupJob**
**Arquivo:** `app/Jobs/RobustTenantSetupJob.php`

Job que executa automaticamente quando um novo tenant é criado:

```php
// Executado automaticamente no JobPipeline
JobPipeline::make([
    Jobs\CreateDatabase::class,
    Jobs\MigrateDatabase::class,
    \App\Jobs\RobustTenantSetupJob::class, // Job robusto
])->send(function (Events\TenantCreated $event) {
    return $event->tenant;
})->shouldBeQueued(false);
```

**Funcionalidades:**
- 🔄 Executa todas as migrations pendentes
- 📋 Verifica existência de tabelas essenciais
- 🔧 Adiciona colunas faltantes automaticamente
- 🌱 Executa seeds se necessário
- 👤 Cria dados essenciais (usuário, empresa)

### 2. **FixTenantDatabase Command**
**Arquivo:** `app/Console/Commands/FixTenantDatabase.php`

Comando para corrigir tenants existentes:

```bash
# Corrigir todos os tenants
php artisan tenant:fix --all

# Corrigir tenant específico
php artisan tenant:fix --tenant=ID_DO_TENANT
```

**Funcionalidades:**
- 🔍 Verifica todos os tenants existentes
- 🔧 Corrige problemas automaticamente
- 📊 Mostra progresso em tempo real
- 📝 Logs detalhados de correções

### 3. **EnsureTenantSetup Middleware**
**Arquivo:** `app/Http/Middleware/EnsureTenantSetup.php`

Middleware que verifica automaticamente cada requisição:

```php
// Executado automaticamente em todas as requisições web
$middleware->appendToGroup('web', [
    \App\Http\Middleware\EnsureTenantSetup::class,
]);
```

**Funcionalidades:**
- 🔍 Verifica setup do tenant em cada requisição
- 🔧 Corrige problemas automaticamente
- 🔄 Redireciona após correção
- 📝 Logs de correções automáticas

## 📊 Tabelas Verificadas

O sistema verifica automaticamente estas tabelas essenciais:

```php
$requiredTables = [
    'users',              // Usuários do sistema
    'companies',          // Empresas/Filiais
    'roles',             // Papéis de usuário
    'permissions',       // Permissões
    'model_has_roles',   // Relacionamento usuário-papel
    'model_has_permissions', // Relacionamento usuário-permissão
    'company_user',      // Relacionamento empresa-usuário
    'chart_of_accounts', // Plano de contas
    'account_mappings',  // Mapeamentos contábeis
    'lancamento_padraos', // Lançamentos padrão
    'banks',             // Bancos
    'caixas',            // Caixas
    'transacoes_financeiras', // Transações
    'anexos',            // Anexos
    'patrimonios',       // Patrimônios
    'fieis',             // Fiéis
    'escrituras',        // Escrituras
    'cemiterios',        // Cemitérios
    'sepolturas',        // Sepulturas
    'avaliadores'        // Avaliadores
];
```

## 🔧 Colunas Verificadas

### Tabela `roles`
- ✅ `description` (text, nullable)

### Tabela `users`
- ✅ `company_id` (unsignedBigInteger, nullable)
- ✅ `avatar` (string, nullable)
- ✅ `status` (enum: active/inactive)

### Tabela `companies`
- ✅ `type` (enum: matriz/filial)
- ✅ `parent_id` (unsignedBigInteger, nullable)
- ✅ `status` (enum: active/inactive)
- ✅ `tags` (json, nullable)
- ✅ `created_by` (unsignedBigInteger, nullable)
- ✅ `updated_by` (unsignedBigInteger, nullable)

## 🌱 Seeds Automáticos

O sistema executa automaticamente:

```php
// TenantDatabaseSeeder
Role::firstOrCreate(['name' => 'global'], ['description' => 'Acesso global']);
Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrador']);
Role::firstOrCreate(['name' => 'admin_user'], ['description' => 'Admin local']);
Role::firstOrCreate(['name' => 'user'], ['description' => 'Usuário comum']);
Role::firstOrCreate(['name' => 'sub_user'], ['description' => 'Usuário limitado']);
```

## 👤 Dados Essenciais Criados

### Usuário Principal
```php
User::create([
    'name' => 'Administrador',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'avatar' => '1253525',
    'status' => 'active'
]);
```

### Empresa Principal
```php
Company::create([
    'name' => 'Empresa Principal',
    'type' => 'matriz',
    'parent_id' => null,
    'status' => 'active',
    'tags' => json_encode(['principal', 'matriz']),
    'created_by' => null,
    'updated_by' => null,
]);
```

## 🚀 Como Usar

### 1. **Criação de Novo Tenant**
O sistema funciona automaticamente:
```php
// Ao criar um tenant, tudo é configurado automaticamente
$tenant = Tenant::create([
    'name' => 'Nova Empresa',
    'email' => 'admin@empresa.com',
    'password' => 'senha123'
]);
```

### 2. **Corrigir Tenants Existentes**
```bash
# Corrigir todos os tenants
php artisan tenant:fix --all

# Corrigir tenant específico
php artisan tenant:fix --tenant=63515c9e-caf6-4b18-b005-b9af17e392b2
```

### 3. **Verificação Automática**
O middleware verifica automaticamente em cada requisição e corrige problemas.

## 📝 Logs

O sistema gera logs detalhados:

```
[2025-09-02 21:50:00] local.INFO: Iniciando setup robusto para tenant: 63515c9e-caf6-4b18-b005-b9af17e392b2
[2025-09-02 21:50:01] local.INFO: Verificando migrations...
[2025-09-02 21:50:02] local.INFO: Verificando existência das tabelas...
[2025-09-02 21:50:03] local.INFO: Verificando colunas necessárias...
[2025-09-02 21:50:04] local.INFO: Adicionando coluna description à tabela roles...
[2025-09-02 21:50:05] local.INFO: Verificando necessidade de seeds...
[2025-09-02 21:50:06] local.INFO: Criando dados essenciais...
[2025-09-02 21:50:07] local.INFO: Setup robusto concluído para tenant: 63515c9e-caf6-4b18-b005-b9af17e392b2
```

## 🎯 Benefícios

1. **✅ Zero Problemas de Setup**: Tenants sempre funcionais
2. **✅ Correção Automática**: Problemas corrigidos sem intervenção
3. **✅ Logs Detalhados**: Rastreamento completo de correções
4. **✅ Flexibilidade**: Funciona para novos e existentes tenants
5. **✅ Performance**: Verificações rápidas e eficientes
6. **✅ Segurança**: Dados essenciais sempre presentes

## 🔄 Fluxo de Funcionamento

```
1. Tenant Criado
   ↓
2. RobustTenantSetupJob Executado
   ↓
3. Migrations Executadas
   ↓
4. Tabelas Verificadas
   ↓
5. Colunas Verificadas
   ↓
6. Seeds Executados
   ↓
7. Dados Essenciais Criados
   ↓
8. Tenant Funcional ✅
```

## 🛠️ Manutenção

### Adicionar Nova Tabela
1. Adicionar na lista `$requiredTables` no job
2. Criar migration correspondente
3. Adicionar no mapeamento `$migrationMap`

### Adicionar Nova Coluna
1. Adicionar na verificação `ensureRequiredColumns()`
2. Implementar lógica de criação
3. Testar com comando `tenant:fix`

### Modificar Seeds
1. Editar `TenantDatabaseSeeder.php`
2. Testar com comando `tenant:fix`

## 🎉 Resultado

Com este sistema, **nunca mais** teremos problemas de:
- ❌ "Column not found"
- ❌ "Table doesn't exist"
- ❌ "Seeds not run"
- ❌ "Missing essential data"

**Todos os tenants serão sempre funcionais e completos!** 🚀

