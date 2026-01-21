# Guia de Uso do LocalTunnel para WhatsApp Webhook

Este guia explica como usar o LocalTunnel para expor seu servidor local e testar a integração do WhatsApp com a Meta.

## 📋 Pré-requisitos

1. **Node.js e npm instalados**
   ```bash
   node --version
   npm --version
   ```

2. **Instalar LocalTunnel**

   **Opção A: Instalação Local (Recomendado - não precisa de sudo)**
   ```bash
   npm install localtunnel
   ```
   O script `start-localtunnel.sh` instalará automaticamente se não estiver instalado.

   **Opção B: Instalação Global (pode precisar de sudo)**
   ```bash
   sudo npm install -g localtunnel
   ```
   Ou no macOS com Homebrew:
   ```bash
   npm install -g localtunnel
   ```

## 🚀 Como Usar

### Passo 1: Iniciar o servidor Laravel

Certifique-se de que o servidor Laravel está rodando na porta desejada (padrão: 8000):

```bash
php artisan serve
# ou
php artisan serve --port=8000
```

### Passo 2: Iniciar o LocalTunnel

#### Opção A: Usar o script automatizado

```bash
./start-localtunnel.sh recife 8000
```

Onde:
- `recife` = subdomínio desejado (deve corresponder ao subdomínio do seu tenant)
- `8000` = porta do servidor Laravel

#### Opção B: Comando manual

```bash
lt --port 8000 --subdomain recife
```

### Passo 3: Configurar o Webhook no Painel da Meta

1. Acesse o [Meta for Developers](https://developers.facebook.com/)
2. Vá até seu app do WhatsApp Business
3. Navegue até **Webhooks** nas configurações
4. Configure a URL do webhook:
   ```
   https://recife.loca.lt/whatsapp/webhook
   ```
   ⚠️ **Importante**: Substitua `recife` pelo subdomínio que você usou no passo 2

5. Configure o **Verify Token** (deve ser o mesmo do seu `.env`):
   ```
   META_VERIFY_TOKEN=Thaigo
   ```

6. Clique em **Verify and Save**

## 🔍 Como Funciona

### Identificação do Tenant

O sistema identifica automaticamente o tenant pelo subdomínio do LocalTunnel:

- **LocalTunnel URL**: `https://recife.loca.lt`
- **Subdomínio extraído**: `recife`
- **Sistema busca**: Tenant com domínio que contenha "recife" (ex: `recife.localhost`)

### Fluxo de Requisições

1. **Meta envia requisição** → `https://recife.loca.lt/whatsapp/webhook`
2. **LocalTunnel redireciona** → `http://localhost:8000/whatsapp/webhook`
3. **Laravel identifica tenant** → Pelo subdomínio "recife"
4. **Webhook processa** → Verifica (GET) ou processa mensagem (POST)

## 📝 Exemplo Completo

```bash
# Terminal 1: Iniciar Laravel
cd projeto-financeiro-web
php artisan serve --port=8000

# Terminal 2: Iniciar LocalTunnel
./start-localtunnel.sh recife 8000

# Saída esperada:
# 🚀 Iniciando localtunnel...
# 📡 Subdomínio: recife
# 🔌 Porta: 8000
# 
# ✅ localtunnel encontrado: lt
# 
# 🌐 Seu túnel será criado em: https://recife.loca.lt
# 
# 📋 Configure o webhook no painel da Meta com:
#    https://recife.loca.lt/whatsapp/webhook
```

## ⚠️ Limitações e Observações

1. **Subdomínio deve corresponder ao tenant**
   - Se seu tenant usa `recife.localhost`, use `recife` no LocalTunnel
   - O sistema busca automaticamente pelo subdomínio

2. **URLs temporárias**
   - O LocalTunnel gera URLs temporárias que podem mudar
   - Para desenvolvimento, isso é aceitável
   - Para produção, use um serviço com domínio fixo

3. **HTTPS automático**
   - O LocalTunnel fornece HTTPS automaticamente
   - A Meta requer HTTPS para webhooks

4. **Túnel ativo apenas enquanto o comando está rodando**
   - Se você fechar o terminal, o túnel será encerrado
   - Você precisará configurar o webhook novamente com a nova URL

## 🔧 Troubleshooting

### Erro: "localtunnel não encontrado"
```bash
npm install -g localtunnel
```

### Erro: "Subdomain already in use"
- Alguém já está usando esse subdomínio
- Escolha outro subdomínio ou aguarde alguns minutos

### Tenant não identificado
- Verifique se o subdomínio do LocalTunnel corresponde ao domínio do tenant
- Verifique os logs: `storage/logs/laravel.log`
- Procure por: "Webhook recebido do host" e "Tenant inicializado"

### Webhook não recebe mensagens
1. Verifique se o túnel está ativo
2. Verifique se a URL está correta no painel da Meta
3. Teste a URL manualmente: `https://recife.loca.lt/whatsapp/webhook?hub.mode=subscribe&hub.verify_token=Thaigo&hub.challenge=test`

## 📚 Recursos Adicionais

- [Documentação LocalTunnel](https://github.com/localtunnel/localtunnel)
- [Meta WhatsApp Business API](https://developers.facebook.com/docs/whatsapp)

## 🆚 Alternativas

Se o LocalTunnel não funcionar para você, considere:

1. **ngrok** (com plano pago para subdomínios customizados)
2. **Cloudflare Tunnel** (gratuito, mas requer configuração)
3. **Serveo** (SSH tunnel, gratuito)

