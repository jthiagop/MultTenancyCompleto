# Estrutura de Diretórios - JavaScript/TypeScript

Esta é a estrutura organizacional para os arquivos JavaScript/TypeScript da aplicação.

## 📁 Estrutura

```
resources/js/
├── app.tsx                 # Arquivo principal de inicialização
├── Components/             # Componentes Shadcn (Botões, Inputs, Selects, etc.)
│   └── index.ts
├── Layouts/                # Layouts da aplicação (Sidebar, Navbar, Footer)
│   └── index.ts
├── Pages/                  # Views que correspondem às rotas
│   └── index.ts
└── lib/                    # Utilitários do Shadcn
    ├── index.ts
    └── utils.ts            # Funções utilitárias (cn, formatCurrency, etc.)
```

## 📦 Diretórios

### `Components/`
Aqui ficam todos os componentes reutilizáveis do Shadcn UI:
- Botões
- Inputs
- Selects
- Modals
- Dropdowns
- etc.

**Exemplo de uso:**
```typescript
// Components/Button.tsx
export const Button = ({ children, variant = 'default' }) => {
    return <button className={cn('btn', variant)}>{children}</button>;
};
```

### `Layouts/`
Contém os layouts principais da aplicação:
- `Sidebar.tsx` - Barra lateral de navegação
- `Navbar.tsx` - Barra de navegação superior
- `Footer.tsx` - Rodapé
- etc.

**Exemplo de uso:**
```typescript
// Layouts/Sidebar.tsx
export const Sidebar = () => {
    return <aside>...</aside>;
};
```

### `Pages/`
Views que correspondem às rotas da aplicação:
- `Dashboard.tsx` - Página inicial
- `Caixa.tsx` - Página de caixa
- `Configuracoes.tsx` - Página de configurações
- etc.

**Exemplo de uso:**
```typescript
// Pages/Caixa.tsx
export const Caixa = {
    init: () => {
        // Inicialização específica da página
    }
};
```

### `lib/`
Utilitários e helpers compartilhados:
- `utils.ts` - Funções utilitárias (cn, formatCurrency, formatDate, debounce)
- Outros utilitários do Shadcn

**Exemplo de uso:**
```typescript
import { cn, formatCurrency, formatDate } from '@/lib/utils';

// Combinar classes CSS
const className = cn('px-4 py-2', isActive && 'bg-blue-500');

// Formatar valores
const price = formatCurrency(1234.56); // "R$ 1.234,56"
const date = formatDate(new Date()); // "01/01/2024"
```

## 🚀 Inicialização

O arquivo `app.tsx` é o ponto de entrada principal que:
1. Importa e inicializa o Alpine.js
2. Carrega todos os componentes
3. Carrega todos os layouts
4. Carrega todas as páginas baseado na rota atual

## 📋 Dependências Necessárias

Para usar os utilitários em `lib/utils.ts`, você precisará instalar:

```bash
npm install clsx tailwind-merge
```

Ou com yarn:
```bash
yarn add clsx tailwind-merge
```

## 🔧 Configuração do TypeScript

Certifique-se de que seu `tsconfig.json` está configurado com os paths corretos:

```json
{
  "compilerOptions": {
    "baseUrl": ".",
    "paths": {
      "@/*": ["./resources/js/*"]
    }
  }
}
```

## 📝 Notas

- O diretório `lib/` será expandido automaticamente quando você usar o Shadcn CLI
- Cada diretório tem um arquivo `index.ts` para facilitar imports
- Os arquivos `.gitkeep` garantem que diretórios vazios sejam versionados
