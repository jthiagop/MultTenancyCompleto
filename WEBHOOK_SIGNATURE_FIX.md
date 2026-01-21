# Correção da Validação de Assinatura do Webhook

## Problema

A validação da assinatura do webhook está falhando com o erro:
```
Assinatura do webhook inválida {"expected":"...","received":"..."}
```

## Causas Possíveis

1. **META_APP_SECRET incorreto ou não configurado**
   - O `META_APP_SECRET` no `.env` pode estar diferente do configurado no painel da Meta
   - Verifique no painel: https://developers.facebook.com/ → Seu App → Configurações → Básico → App Secret

2. **Payload sendo modificado antes da validação**
   - Middlewares do Laravel podem estar modificando o body da requisição
   - O Laravel pode estar decodificando o JSON automaticamente

3. **Header em formato diferente**
   - O header `X-Hub-Signature-256` pode vir com ou sem o prefixo `sha256=`

## Soluções Implementadas

### 1. Melhorias na Validação

O método `validateWebhookSignature()` foi melhorado para:
- **Usar apenas `$request->getContent()`** para obter o payload RAW original
- **NÃO reconstruir o JSON** (evita mudanças na ordem das chaves ou espaçamento que invalidariam o hash)
- Tratar o header com ou sem prefixo `sha256=`
- Adicionar logs detalhados para debug

**IMPORTANTE:** Sempre use `$request->getContent()` para validação de assinatura, pois qualquer modificação no payload (ordem de chaves, espaçamento, etc.) invalidaria o hash.

### 2. Opção para Desabilitar Validação (Desenvolvimento)

Adicione no `.env`:
```env
META_SKIP_SIGNATURE_VALIDATION=true
```

Isso permite testar o webhook sem validação de assinatura (apenas em desenvolvimento).

## Como Corrigir

### Opção 1: Verificar e Corrigir o META_APP_SECRET

1. Acesse: https://developers.facebook.com/
2. Vá em: Seu App → Configurações → Básico
3. Copie o **App Secret**
4. Adicione no `.env`:
   ```env
   META_APP_SECRET=seu_app_secret_aqui
   ```
5. Limpe o cache:
   ```bash
   php artisan config:clear
   ```

### Opção 2: Desabilitar Validação Temporariamente (Desenvolvimento)

Se você estiver apenas testando e não precisa de validação agora:

1. Adicione no `.env`:
   ```env
   META_SKIP_SIGNATURE_VALIDATION=true
   ```

2. Limpe o cache:
   ```bash
   php artisan config:clear
   ```

**⚠️ IMPORTANTE:** Nunca desabilite a validação em produção!

### Opção 3: Verificar se o Payload está Sendo Modificado

Se o problema persistir, verifique os logs para ver:
- O tamanho do payload
- Se o payload está vazio
- O formato do header recebido

Os logs agora incluem informações detalhadas sobre a validação.

## Verificação

Após corrigir, teste enviando uma mensagem via WhatsApp. Os logs devem mostrar:
- `Assinatura do webhook validada com sucesso` (se válida)
- Ou detalhes do erro (se ainda inválida)

## Notas Importantes

- A validação de assinatura é uma **medida de segurança importante**
- Em produção, sempre mantenha a validação habilitada
- O `META_APP_SECRET` deve ser mantido em segredo
- Não commite o `.env` com o App Secret no repositório

