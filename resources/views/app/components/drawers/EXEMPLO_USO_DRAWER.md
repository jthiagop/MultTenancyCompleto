# Exemplos de Uso do Componente `<x-tenant-drawer>`

## Exemplo 1: Drawer Simples

```blade
<x-tenant-drawer
    drawerId="kt_drawer_exemplo"
    title="Meu Drawer Simples"
    width="{default:'300px', 'md': '500px'}">
    
    <p>Conteúdo do drawer aqui</p>
    
</x-tenant-drawer>
```

## Exemplo 2: Drawer com Footer Customizado

```blade
<x-tenant-drawer
    drawerId="kt_drawer_com_footer"
    title="Drawer com Footer"
    width="{default:'400px', 'md': '600px'}">
    
    <div class="mb-7">
        <h5>Conteúdo Principal</h5>
        <p>Texto do conteúdo...</p>
    </div>
    
    <x-slot name="footer">
        <button class="btn btn-primary btn-sm">Salvar</button>
        <button class="btn btn-light btn-sm">Cancelar</button>
    </x-slot>
    
</x-tenant-drawer>
```

## Exemplo 3: Drawer com Toolbar Customizado

```blade
<x-tenant-drawer
    drawerId="kt_drawer_com_toolbar"
    title="Drawer com Toolbar"
    width="{default:'350px', 'md': '550px'}">
    
    <x-slot name="toolbar">
        <button class="btn btn-sm btn-icon btn-light" data-bs-toggle="tooltip" title="Ajuda">
            <i class="bi bi-question-circle"></i>
        </button>
    </x-slot>
    
    <p>Conteúdo com toolbar customizado</p>
    
</x-tenant-drawer>
```

## Exemplo 4: Drawer Completo (com Toolbar e Footer)

```blade
<x-tenant-drawer
    drawerId="kt_drawer_completo"
    title="Drawer Completo"
    width="{default:'400px', 'md': '700px'}"
    headerClass="bg-light-primary"
    bodyClass="p-10"
    footerClass="bg-light">
    
    <x-slot name="toolbar">
        <button class="btn btn-sm btn-icon btn-light" data-bs-toggle="tooltip" title="Configurações">
            <i class="bi bi-gear"></i>
        </button>
    </x-slot>
    
    <div class="mb-7">
        <h5 class="mb-4">Seção 1</h5>
        <p>Conteúdo da seção...</p>
    </div>
    
    <div class="separator separator-dashed mb-7"></div>
    
    <div class="mb-7">
        <h5 class="mb-4">Seção 2</h5>
        <p>Mais conteúdo...</p>
    </div>
    
    <x-slot name="footer">
        <button class="btn btn-primary btn-sm me-2">
            <i class="bi bi-check"></i> Salvar
        </button>
        <button class="btn btn-light btn-sm">
            <i class="bi bi-x"></i> Cancelar
        </button>
    </x-slot>
    
</x-tenant-drawer>
```

## Exemplo 5: Drawer sem Botão de Fechar

```blade
<x-tenant-drawer
    drawerId="kt_drawer_sem_fechar"
    title="Drawer Persistente"
    width="{default:'300px', 'md': '500px'}"
    :showCloseButton="false">
    
    <p>Este drawer não tem botão de fechar no header</p>
    
    <x-slot name="footer">
        <button class="btn btn-primary btn-sm" onclick="fecharDrawer()">Fechar</button>
    </x-slot>
    
</x-tenant-drawer>
```

## Exemplo 6: Drawer com IDs Customizados

```blade
<x-tenant-drawer
    drawerId="kt_drawer_custom"
    title="Drawer Customizado"
    width="{default:'300px', 'md': '500px'}"
    toggleButtonId="meu_botao_abrir"
    closeButtonId="meu_botao_fechar">
    
    <p>Drawer com IDs de botões customizados</p>
    
</x-tenant-drawer>

<!-- Botão para abrir o drawer -->
<button id="meu_botao_abrir" class="btn btn-primary">
    Abrir Drawer
</button>
```

## Parâmetros Disponíveis

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `drawerId` | string | **obrigatório** | ID único do drawer |
| `title` | string | `'Drawer'` | Título exibido no header |
| `width` | string | `"{default:'300px', 'md': '500px'}"` | Largura responsiva do drawer |
| `toggleButtonId` | string | `null` | ID do botão que abre o drawer (gerado automaticamente se não fornecido) |
| `closeButtonId` | string | `null` | ID do botão que fecha o drawer (gerado automaticamente se não fornecido) |
| `showCloseButton` | boolean | `true` | Se deve mostrar o botão de fechar no header |
| `headerClass` | string | `''` | Classes CSS adicionais para o header |
| `bodyClass` | string | `''` | Classes CSS adicionais para o body |
| `footerClass` | string | `''` | Classes CSS adicionais para o footer |
| `cardClass` | string | `'shadow-none rounded-0 w-100'` | Classes CSS para o card |

## Slots Disponíveis

- **Default Slot**: Conteúdo principal do drawer (obrigatório)
- **`footer` Slot**: Conteúdo do footer (opcional)
- **`toolbar` Slot**: Conteúdo adicional no header toolbar (opcional)

## IDs Gerados Automaticamente

O componente gera automaticamente os seguintes IDs baseados no `drawerId`:

- `{drawerId}_header` - Header do drawer
- `{drawerId}_body` - Body do drawer
- `{drawerId}_footer` - Footer do drawer
- `{drawerId}_scroll` - Container de scroll
- `{drawerId}_button` - Botão toggle (se não fornecido)
- `{drawerId}_close` - Botão close (se não fornecido)

