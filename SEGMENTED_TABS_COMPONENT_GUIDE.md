# Componente: Segmented Tabs Toolbar

## Descrição
Componente reutilizável que renderiza uma faixa de tabs segmentados com contadores (estilo segmentação), toolbar de ações e tab panes.

## Localização
`resources/views/components/tenant/segmented-tabs-toolbar.blade.php`

## Props

| Prop | Tipo | Padrão | Descrição |
|------|------|--------|-----------|
| `id` | string | `'segmented-tabs'` | Prefixo para IDs únicos (tabs, panes, etc) |
| `tabs` | array | `[]` | Array de abas com estrutura: `[{ key, label, count, colorClass, paneId }, ...]` |
| `active` | string | `null` | Key da aba ativa por padrão |

## Estrutura do Array `tabs`

Cada item do array deve ter:
```php
[
    'key' => 'all',              // Identificador único (usado em classes e IDs)
    'label' => 'Todos',          // Texto exibido
    'count' => 68,               // Número exibido em grande
    'colorClass' => 'tab-all',   // Classe CSS customizada (tab-all, tab-received, tab-paid)
    'paneId' => 'pane-all'       // ID da aba pane (opcional, gerado automaticamente se não provided)
]
```

## Slots

### actionsLeft
Conteúdo renderizado à esquerda da toolbar (botões, dropdowns).

```blade
<x-slot:actionsLeft>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            Selecionar lançamentos
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Todos</a></li>
            <li><a class="dropdown-item" href="#">Nenhum</a></li>
        </ul>
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary">Conciliar</button>
    <button type="button" class="btn btn-sm btn-outline-primary">Editar</button>
    <button type="button" class="btn btn-sm btn-outline-secondary">Desvincular</button>
    <button type="button" class="btn btn-sm btn-outline-danger">Ignorar</button>
</x-slot>
```

### actionsRight
Conteúdo renderizado à direita da toolbar.

```blade
<x-slot:actionsRight>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            Ordenar
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Data (recente)</a></li>
            <li><a class="dropdown-item" href="#">Data (antiga)</a></li>
            <li><a class="dropdown-item" href="#">Valor (maior)</a></li>
            <li><a class="dropdown-item" href="#">Valor (menor)</a></li>
        </ul>
    </div>
</x-slot>
```

### panes
Tab panes para cada aba.

```blade
<x-slot:panes>
    <div class="tab-pane fade show active" id="pane-all" role="tabpanel">
        <!-- Conteúdo da aba "Todos" -->
    </div>
    <div class="tab-pane fade" id="pane-received" role="tabpanel">
        <!-- Conteúdo da aba "Recebimentos" -->
    </div>
    <div class="tab-pane fade" id="pane-paid" role="tabpanel">
        <!-- Conteúdo da aba "Pagamentos" -->
    </div>
</x-slot>
```

## Exemplo Completo

```blade
@php
    $tabs = [
        [
            'key' => 'all',
            'label' => 'Todos',
            'count' => 68,
        ],
        [
            'key' => 'received',
            'label' => 'Recebimentos',
            'count' => 56,
        ],
        [
            'key' => 'paid',
            'label' => 'Pagamentos',
            'count' => 12,
        ],
    ];
@endphp

<x-tenant.segmented-tabs-toolbar :tabs="$tabs" active="all" id="conciliacao">
    <x-slot:actionsLeft>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Selecionar lançamentos
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Todos</a></li>
                <li><a class="dropdown-item" href="#">Nenhum</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Pendentes</a></li>
                <li><a class="dropdown-item" href="#">Conciliados</a></li>
            </ul>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary">Conciliar</button>
        <button type="button" class="btn btn-sm btn-outline-primary">Editar</button>
        <button type="button" class="btn btn-sm btn-outline-secondary">Desvincular</button>
        <button type="button" class="btn btn-sm btn-outline-danger">Ignorar</button>
    </x-slot>

    <x-slot:actionsRight>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Ordenar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Data (recente)</a></li>
                <li><a class="dropdown-item" href="#">Data (antiga)</a></li>
                <li><a class="dropdown-item" href="#">Valor (maior)</a></li>
                <li><a class="dropdown-item" href="#">Valor (menor)</a></li>
            </ul>
        </div>
    </x-slot>

    <x-slot:panes>
        <div class="tab-pane fade show active" id="pane-all" role="tabpanel" aria-labelledby="conciliacao-tab-all">
            <div class="p-4">
                <!-- Conteúdo da aba "Todos" -->
                <p>Mostrando todos os 68 lançamentos...</p>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-received" role="tabpanel" aria-labelledby="conciliacao-tab-received">
            <div class="p-4">
                <!-- Conteúdo da aba "Recebimentos" -->
                <p>Mostrando 56 recebimentos...</p>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-paid" role="tabpanel" aria-labelledby="conciliacao-tab-paid">
            <div class="p-4">
                <!-- Conteúdo da aba "Pagamentos" -->
                <p>Mostrando 12 pagamentos...</p>
            </div>
        </div>
    </x-slot>
</x-tenant.segmented-tabs-toolbar>
```

## Recursos

✅ **Tabs Segmentados**
- Contadores grandes e visíveis
- Separadores verticais entre abas
- Borda inferior colorida para aba ativa
- Cores diferentes por tipo (azul/verde/vermelho)

✅ **Toolbar**
- Botões à esquerda (ações principais)
- Dropdown à direita (ordenação)
- Responsiva com flex-wrap

✅ **Acessibilidade**
- `role="tablist"`, `role="tab"` em tabs
- `aria-controls`, `aria-selected`
- IDs únicos garantidos com prefixo customizável

✅ **Bootstrap 5 + Metronic**
- Sem dependências externas (Alpine, React)
- Usa apenas classes Bootstrap padrão
- CSS customizado mínimo (variáveis de cor)

## Customização

Para mudar as cores dos contadores:
```css
.tab-all.active .segmented-tab-count { color: #3b82f6; }      /* Azul */
.tab-received.active .segmented-tab-count { color: #10b981; } /* Verde */
.tab-paid.active .segmented-tab-count { color: #ef4444; }     /* Vermelho */
```

Para mudar a borda inferior:
```css
.tab-all.active { border-bottom-color: #3b82f6; }
.tab-received.active { border-bottom-color: #10b981; }
.tab-paid.active { border-bottom-color: #ef4444; }
```
