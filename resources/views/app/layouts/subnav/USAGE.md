# Como Usar a Navbar Terciária

A navbar terciária agora é totalmente flexível e controlada por parâmetros explícitos. Cada view pode escolher o que exibir.

## Parâmetros Disponíveis

- `subnavView` (string|null): Caminho da view para a subnav (título, breadcrumb, etc.)
- `toolbarView` (string|null): Caminho da view para a toolbar (botões de ação)
- `showBulkEditButton` (bool): Exibir botão de edição em massa (quando não há subnav)

## Exemplos de Uso

### Exemplo 1: Apenas Subnav (Título e Breadcrumb)

```blade
<x-tenant-app-layout>
    @include('app.layouts.subnav.projects', [
        'subnavView' => 'app.layouts.subnav.modules.financeiro'
    ])
    
    <!-- Conteúdo da página -->
</x-tenant-app-layout>
```

### Exemplo 2: Subnav + Toolbar

```blade
<x-tenant-app-layout>
    @include('app.layouts.subnav.projects', [
        'subnavView' => 'app.layouts.subnav.modules.financeiro',
        'toolbarView' => 'app.layouts.subnav.modules.toolbars.financeiro'
    ])
    
    <!-- Conteúdo da página -->
</x-tenant-app-layout>
```

### Exemplo 3: Apenas Toolbar

```blade
<x-tenant-app-layout>
    @include('app.layouts.subnav.projects', [
        'toolbarView' => 'app.layouts.subnav.modules.toolbars.financeiro'
    ])
    
    <!-- Conteúdo da página -->
</x-tenant-app-layout>
```

### Exemplo 4: Apenas Ações (sem subnav nem toolbar)

```blade
<x-tenant-app-layout>
    @include('app.layouts.subnav.projects', [
        'showBulkEditButton' => true
    ])
    
    <!-- Conteúdo da página -->
</x-tenant-app-layout>
```

### Exemplo 5: Detecção Automática (Compatibilidade)

Se você não passar nenhum parâmetro, o sistema tentará detectar automaticamente baseado no módulo atual:

```blade
<x-tenant-app-layout>
    @include('app.layouts.subnav.projects')
    
    <!-- Conteúdo da página -->
</x-tenant-app-layout>
```

## Vantagens

✅ **Controle Total**: Cada view decide o que exibir  
✅ **Flexibilidade**: Pode combinar subnav, toolbar e ações como quiser  
✅ **Simplicidade**: Código mais limpo e fácil de entender  
✅ **Compatibilidade**: Mantém detecção automática para código existente  

