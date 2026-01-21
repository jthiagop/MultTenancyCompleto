# Guia de Instalação - Inertia.js + React + Shadcn

Este guia documenta a configuração do Inertia.js com React e Shadcn no projeto Laravel.

## 📦 Instalação de Dependências

Execute os seguintes comandos para instalar todas as dependências necessárias:

```bash
# Instalar pacotes do Inertia.js e React
npm install @inertiajs/react @inertiajs/inertia react react-dom

# Instalar dependências do Shadcn
npm install clsx tailwind-merge

# Instalar plugin do Vite para React
npm install --save-dev @vitejs/plugin-react @types/react @types/react-dom typescript
```

Ou usando yarn:

```bash
yarn add @inertiajs/react @inertiajs/inertia react react-dom clsx tailwind-merge
yarn add -D @vitejs/plugin-react @types/react @types/react-dom typescript
```

## 📁 Estrutura Criada

```
resources/js/
├── app-inertia.tsx          # Ponto de entrada do Inertia.js
├── Components/              # Componentes Shadcn
├── Layouts/
│   └── AppLayout.tsx        # Layout principal do Inertia
├── Pages/
│   ├── Welcome.tsx          # Página de exemplo
│   └── Dashboard.tsx        # Página de dashboard
└── lib/
    └── utils.ts             # Utilitários (já criado anteriormente)
```

## ⚙️ Configurações Realizadas

### 1. Middleware do Inertia
- ✅ Criado: `app/Http/Middleware/HandleInertiaRequests.php`
- ✅ Registrado no `bootstrap/app.php`

### 2. Root Template
- ✅ Criado: `resources/views/app.blade.php`

### 3. Vite Config
- ✅ Adicionado plugin React
- ✅ Configurado alias `@` para `resources/js`
- ✅ Adicionado `app-inertia.tsx` aos inputs

### 4. TypeScript
- ✅ Configurado `tsconfig.json` com paths e alias

## 🚀 Como Usar

### 1. Instalar dependências (se ainda não fez)

```bash
npm install
# ou
yarn install
```

### 2. Criar uma rota Inertia no Laravel

No arquivo `routes/web.php` ou `routes/tenant.php`:

```php
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'auth' => [
            'user' => auth()->user(),
        ],
    ]);
})->middleware(['auth'])->name('dashboard');
```

### 3. Rodar o Vite

```bash
npm run dev
# ou
yarn dev
```

### 4. Acessar a aplicação

Acesse a rota configurada no navegador. A página React será renderizada através do Inertia.js!

## 📝 Criando Novas Páginas

1. **Crie o componente React** em `resources/js/Pages/`:

```tsx
// resources/js/Pages/MinhaPagina.tsx
import AppLayout from '@/Layouts/AppLayout';
import { Head } from '@inertiajs/react';

export default function MinhaPagina() {
    return (
        <AppLayout title="Minha Página">
            <Head title="Minha Página" />
            <div>
                <h1>Minha Página</h1>
            </div>
        </AppLayout>
    );
}
```

2. **Crie a rota** no Laravel:

```php
Route::get('/minha-pagina', function () {
    return Inertia::render('MinhaPagina');
})->name('minha-pagina');
```

## 🎨 Usando Shadcn Components

Quando instalar componentes do Shadcn, eles vão para `resources/js/Components/`:

```tsx
import { Button } from '@/Components/Button';
import { Input } from '@/Components/Input';

export default function MinhaPagina() {
    return (
        <AppLayout>
            <Button>Clique aqui</Button>
            <Input placeholder="Digite algo..." />
        </AppLayout>
    );
}
```

## 🔗 Links Úteis

- [Documentação do Inertia.js](https://inertiajs.com/)
- [Documentação do React](https://react.dev/)
- [Documentação do Shadcn UI](https://ui.shadcn.com/)

## ⚠️ Notas Importantes

1. **O arquivo `app.tsx` antigo** ainda existe para o Alpine.js. O Inertia usa `app-inertia.tsx`.

2. **Você pode usar ambos** (Alpine.js em algumas páginas e Inertia.js em outras) ao mesmo tempo.

3. **Para usar Inertia em uma rota**, você deve retornar `Inertia::render()` no controller.

4. **Para páginas Blade tradicionais**, continue usando o `app.js` normal.

## 🐛 Troubleshooting

### Erro: "Module not found: Can't resolve '@inertiajs/react'"
- Execute `npm install` novamente

### Erro: "Cannot find module '@/Layouts/AppLayout'"
- Verifique se o alias `@` está configurado no `vite.config.js` e `tsconfig.json`

### Página em branco
- Verifique o console do navegador para erros
- Verifique se o Vite está rodando (`npm run dev`)
- Verifique se a rota retorna `Inertia::render()`

