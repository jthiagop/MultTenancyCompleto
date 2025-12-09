# Navbars Secundária e Terciária - Documentação de Uso

Este documento explica como usar as navbars secundária e terciária seguindo o padrão Metronic.

## Localização

O partial está localizado em: `resources/views/app/layouts/subnav/projects.blade.php`

## Uso Básico

### Opção 1: Incluir diretamente na página

```blade
<x-tenant-app-layout>
    @php
        $showSubnav = true;
        $activeTab = 'projects';
    @endphp
    
    @include('app.layouts.subnav.projects', [
        'activeTab' => $activeTab,
        'showAccountDropdown' => true,
        'showToolsDropdown' => true
    ])
    
    <!-- Conteúdo da página -->
    <div class="app-container container-fluid px-4">
        <!-- Seu conteúdo aqui -->
    </div>
</x-tenant-app-layout>
```

### Opção 2: Baseado em rota

```blade
<x-tenant-app-layout>
    @if(in_array(Route::currentRouteName(), ['projects.index', 'projects.create', 'customers.index']))
        @include('app.layouts.subnav.projects', [
            'activeTab' => Route::currentRouteName() === 'customers.index' ? 'customers' : 'projects',
            'showAccountDropdown' => true,
            'showToolsDropdown' => true
        ])
    @endif
    
    <!-- Conteúdo da página -->
    <div class="app-container container-fluid px-4">
        <!-- Seu conteúdo aqui -->
    </div>
</x-tenant-app-layout>
```

### Opção 3: Via Controller

No seu controller:

```php
public function index()
{
    return view('app.projects.index', [
        'showSubnav' => true,
        'activeTab' => 'projects'
    ]);
}
```

Na view:

```blade
<x-tenant-app-layout>
    @if(isset($showSubnav) && $showSubnav === true)
        @include('app.layouts.subnav.projects', [
            'activeTab' => $activeTab ?? 'projects',
            'showAccountDropdown' => true,
            'showToolsDropdown' => true
        ])
    @endif
    
    <!-- Conteúdo da página -->
    <div class="app-container container-fluid px-4">
        <!-- Seu conteúdo aqui -->
    </div>
</x-tenant-app-layout>
```

## Parâmetros Disponíveis

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `activeTab` | string | `'projects'` | Define qual aba está ativa: `'account'`, `'projects'`, ou `'customers'` |
| `showAccountDropdown` | boolean | `true` | Mostra/oculta o dropdown Account |
| `showToolsDropdown` | boolean | `true` | Mostra/oculta o dropdown Tools |

## Estrutura das Navbars

### Navbar Secundária
- **Background**: `bg-body-dark` (mais escuro que o header principal)
- **Altura**: 60px
- **Elementos à esquerda**: Account dropdown, Tabs (Projects/Customers), Botão "+ Add New"
- **Elementos à direita**: Botão "+ Extensions", Tools dropdown
- **Responsivo**: Menu mobile drawer em telas pequenas

### Navbar Terciária
- **Background**: `bg-white` com suporte a dark mode
- **Altura**: 50px
- **Elementos**: Views, My Widgets, Hide Fields, Filter, Sort, Search
- **Responsivo**: Scroll horizontal em telas pequenas

## Customização

### Alterar aba ativa

```blade
@include('app.layouts.subnav.projects', [
    'activeTab' => 'customers'
])
```

### Ocultar dropdowns

```blade
@include('app.layouts.subnav.projects', [
    'activeTab' => 'projects',
    'showAccountDropdown' => false,
    'showToolsDropdown' => false
])
```

## Integração com Layout Principal

As navbars devem ser incluídas **logo após o header principal** e **antes do conteúdo da página**. O container principal (`app-container container-fluid px-4`) garante o alinhamento correto com o header.

## Suporte a Dark Mode

As navbars suportam automaticamente o dark mode através das classes:
- Navbar secundária: `bg-body-dark dark:bg-gray-900`
- Navbar terciária: `bg-white dark:bg-gray-800`

## Responsividade

- **Desktop (lg+)**: Todas as funcionalidades visíveis
- **Tablet/Mobile (< lg)**: 
  - Navbar secundária: Menu hamburger com drawer
  - Navbar terciária: Scroll horizontal suave

## Notas Importantes

1. **NÃO modifique** `navigation.blade.php` (header principal)
2. Use apenas classes Metronic/Bootstrap
3. Mantenha consistência visual com o header existente
4. O partial é reutilizável em outras páginas seguindo o mesmo padrão

