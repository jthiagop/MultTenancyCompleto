# Como Instalar o LocalTunnel

## 🚀 Instalação Rápida

### Opção 1: Instalação Local (Recomendado - Sem precisar de sudo)

Execute no terminal dentro da pasta do projeto:

```bash
cd projeto-financeiro-web
npm install localtunnel
```

Isso instalará o localtunnel localmente no projeto (não precisa de permissões de administrador).

### Opção 2: Instalação Global (Pode precisar de sudo)

Se preferir instalar globalmente:

**macOS/Linux:**
```bash
sudo npm install -g localtunnel
```

**Windows (como Administrador):**
```bash
npm install -g localtunnel
```

## ✅ Verificar Instalação

Após instalar, você pode verificar se funcionou:

**Se instalou localmente:**
```bash
./node_modules/.bin/lt --version
```

**Se instalou globalmente:**
```bash
lt --version
```

## 🎯 Usar o Script Automatizado

O script `start-localtunnel.sh` detecta automaticamente se o localtunnel está instalado e, se não estiver, tenta instalar localmente automaticamente.

Basta executar:

```bash
./start-localtunnel.sh recife 8000
```

## 🔧 Solução de Problemas

### Erro: "EACCES: permission denied"

**Solução:** Use instalação local em vez de global:
```bash
npm install localtunnel
```

### Erro: "npm não encontrado"

**Solução:** Instale o Node.js:
- macOS: `brew install node`
- Linux: `sudo apt install nodejs npm`
- Windows: Baixe em https://nodejs.org/

### Erro: "localtunnel não encontrado" após instalar

**Solução:** Verifique se está no diretório correto:
```bash
cd projeto-financeiro-web
npm install localtunnel
```

## 📝 Nota

O `node_modules` está no `.gitignore`, então cada desenvolvedor precisa instalar localmente. Isso é uma boa prática e evita problemas de permissão.

