# Gerenciamento de URL do Ngrok

## Problema

O ngrok gera URLs temporárias que mudam quando você reinicia o serviço. Isso significa que você precisa atualizar a URL do webhook no painel da Meta toda vez que o ngrok reiniciar.

## Soluções

### Opção 1: Ngrok com Domínio Reservado (Recomendado para Produção)

Com plano pago do ngrok, você pode reservar um domínio:

```bash
ngrok http 8000 --domain=seu-dominio-reservado.ngrok-free.app
```

Isso garante que a URL não mude.

### Opção 2: Usar Variável de Ambiente

Criar uma variável de ambiente para a URL do webhook e atualizar automaticamente:

```env
NGROK_URL=https://53e347c6079e.ngrok-free.app
```

### Opção 3: Script para Atualizar Automaticamente (Futuro)

Criar um script que:
1. Detecta quando o ngrok reinicia
2. Obtém a nova URL
3. Atualiza automaticamente no painel da Meta via API

### Opção 4: Usar LocalTunnel (Alternativa Gratuita)

O LocalTunnel também muda URLs, mas você pode usar um subdomínio fixo se disponível:

```bash
lt --port 8000 --subdomain recife
```

## Como Atualizar Manualmente

Quando o ngrok reiniciar e a URL mudar:

1. **Obter nova URL do ngrok:**
   - Acesse: http://127.0.0.1:4040 (Interface web do ngrok)
   - Copie a nova URL (ex: `https://abc123.ngrok-free.app`)

2. **Atualizar no painel da Meta:**
   - Acesse: https://developers.facebook.com/
   - Vá em: Seu App → WhatsApp → Webhooks
   - Atualize a **Callback URL** para: `https://abc123.ngrok-free.app/webhooks/meta/whatsapp`
   - Clique em **"Verificar e Salvar"**

3. **Verificar se funcionou:**
   - Os logs devem mostrar: `"Webhook verificado com sucesso"`

## Importante

- **Em desenvolvimento:** Use ngrok/localTunnel e atualize manualmente quando necessário
- **Em produção:** Use um domínio fixo (não ngrok) ou ngrok com domínio reservado
- **URL do webhook não afeta o roteamento:** O sistema roteia pelo `phone_number_id`, não pela URL

## Dica

Mantenha o ngrok rodando em um terminal separado e não feche enquanto estiver testando. Se precisar reiniciar, atualize a URL no painel da Meta imediatamente.

