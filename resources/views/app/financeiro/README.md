# Página de Financeiro - Estrutura Organizacional

## Visão Geral

A página de financeiro foi reorganizada em componentes modulares para melhor manutenibilidade e legibilidade do código. Cada componente tem uma responsabilidade específica e pode ser reutilizado ou modificado independentemente.

## Estrutura de Arquivos

```
resources/views/app/financeiro/
├── index.blade.php                    # Arquivo principal
├── components/                        # Diretório de componentes
│   ├── header.blade.php              # Cabeçalho com título e breadcrumb
│   ├── financial-modules.blade.php   # Cards de Caixa e Banco
│   ├── tabs-navigation.blade.php     # Navegação por tabs (Receitas/Despesas)
│   ├── receitas-container.blade.php  # Container de receitas
│   ├── despesas-container.blade.php  # Container de despesas
│   └── assets.blade.php              # Assets e scripts
└── README.md                          # Esta documentação
```

## Componentes

### 1. Header (`header.blade.php`)
- **Responsabilidade**: Cabeçalho da página com título e breadcrumb
- **Conteúdo**: 
  - Título "Lançamentos Financeiros"
  - Breadcrumb de navegação
  - Menu de ações (Lançar Caixa/Banco)

### 2. Financial Modules (`financial-modules.blade.php`)
- **Responsabilidade**: Cards dos módulos financeiros
- **Conteúdo**:
  - Card de Lançamento de Caixa
  - Card de Lançamentos Bancários
  - Contadores de pendências

### 3. Tabs Navigation (`tabs-navigation.blade.php`)
- **Responsabilidade**: Navegação principal entre Receitas e Despesas
- **Conteúdo**:
  - Tabs de Receitas e Despesas
  - Barra de pesquisa
  - Seletor de período
  - Botão "Novo" com menu dropdown

### 4. Receitas Container (`receitas-container.blade.php`)
- **Responsabilidade**: Conteúdo das receitas
- **Conteúdo**:
  - Sub-tabs (Em Aberto, A Vencer, Total do Período)
  - Tabelas de dados das receitas
  - Valores formatados

### 5. Despesas Container (`despesas-container.blade.php`)
- **Responsabilidade**: Conteúdo das despesas
- **Conteúdo**:
  - Sub-tabs (Em Aberto, Realizadas, Total do Período)
  - Tabelas de dados das despesas
  - Valores formatados

### 6. Assets (`assets.blade.php`)
- **Responsabilidade**: Recursos externos e scripts
- **Conteúdo**:
  - CSS do Kendo UI
  - jQuery
  - Scripts do Kendo
  - DataTables
  - Scripts customizados

## Arquivo Principal (`index.blade.php`)

O arquivo principal agora é muito mais limpo e organizado, contendo apenas:
- Inclusão dos assets
- Layout principal
- Inclusões dos componentes modulares
- Modais

## Benefícios da Reorganização

1. **Manutenibilidade**: Cada componente pode ser modificado independentemente
2. **Reutilização**: Componentes podem ser reutilizados em outras páginas
3. **Legibilidade**: Código mais limpo e fácil de entender
4. **Testabilidade**: Componentes isolados são mais fáceis de testar
5. **Colaboração**: Diferentes desenvolvedores podem trabalhar em componentes diferentes

## Como Usar

Para modificar um componente específico, edite o arquivo correspondente em `components/`. Para adicionar novos componentes, crie um novo arquivo no diretório `components/` e inclua-o no arquivo principal usando `@include()`.

## Variáveis Necessárias

Os seguintes dados devem ser passados do controller:
- `$caixaPendentes`
- `$bancoPendentes`
- `$valorTotal`
- `$TotalreceitasAVencer`
- `$valorDespesaTotal`
- `$valorDespesasRealizadas`
- `$receitasEmAberto`
- `$receitasAVencer`
- `$despesasEmAberto`
- `$despesasRealizadas`
