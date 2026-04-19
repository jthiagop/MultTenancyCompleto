# Notificação WhatsApp para Usuários

## Visão Geral

O sistema permite enviar notificações via WhatsApp para usuários que tenham seu número de telefone vinculado. A integração utiliza a **Meta WhatsApp Business API** e funciona de forma complementar ao canal de notificações interno (`database`).

O envio é **silencioso e opcional**: se o usuário não tiver vínculo ativo, a mensagem é descartada sem erros.

---

## Fluxo de Vinculação

Antes de receber mensagens, o usuário precisa vincular seu número:

1. No sistema, acessa a tela de integração WhatsApp
2. Um QR Code é gerado contendo um link `wa.me` com um UUID único
3. O usuário escaneia o QR Code ou clica no link, abrindo o WhatsApp com a mensagem pré-preenchida
4. Ao enviar a mensagem, o webhook da Meta recebe o número (`wa_id`) e marca o vínculo como `active` na tabela `whatsapp_auth_requests` (banco central)

A partir daí, o usuário está elegível para receber mensagens automáticas.

---

## Arquitetura

### Arquivos criados

| Arquivo | Responsabilidade |
|---|---|
| `app/Channels/WhatsappChannel.php` | Canal de notificação — busca o vínculo e chama a API Meta |
| `app/Channels/WhatsappNotifiable.php` | Interface contrato — garante tipagem para o método `toWhatsapp()` |

### Arquivo modificado

| Arquivo | Alteração |
|---|---|
| `app/Notifications/RateioRecebidoNotification.php` | Adicionado `WhatsappChannel::class` no `via()` e implementado `toWhatsapp()` |

---

## Como Funciona o Canal (`WhatsappChannel`)

```
$user->notify($notification)
        │
        ├── canal 'database'  → salva na tabela notifications (painel interno)
        │
        └── canal WhatsappChannel
                │
                ├── Verifica se $notification implementa WhatsappNotifiable
                ├── Busca WhatsappAuthRequest (banco central)
                │       where tenant_id = tenant atual
                │       where user_id   = id do usuário
                │       where status    = 'active'
                │       where wa_id     IS NOT NULL
                │
                ├── Se não encontrar → descarta silenciosamente
                │
                └── Se encontrar → chama Meta API
                        POST /{phone_number_id}/messages
                        { to: wa_id, type: 'text', body: toWhatsapp() }
```

---

## Notificação de Rateio (`RateioRecebidoNotification`)

### Quando é disparada

Ao criar um rateio intercompany, o `RateioService::criarAcertoIntercompany()` chama `NotificacaoFinanceiraService::notificarEmpresa()`, que envia a notificação para **todos os usuários financeiros da filial** com a permissão `financeiro.index`.

### Mensagem enviada via WhatsApp

```
💸 *Cobrança de Rateio*

*Igreja Matriz* lançou um rateio de *R$ 1.500,00*
📋 _Material de Escritório_

Acesse o sistema para verificar o lançamento em Contas a Pagar.
```

O texto usa a formatação nativa do WhatsApp:
- `*texto*` → **negrito**
- `_texto_` → _itálico_

---

## Como Adicionar WhatsApp em Outras Notificações

1. Implementar a interface `WhatsappNotifiable`
2. Adicionar `WhatsappChannel::class` no array `via()`
3. Implementar o método `toWhatsapp()`

```php
use App\Channels\WhatsappChannel;
use App\Channels\WhatsappNotifiable;
use Illuminate\Notifications\Notification;

class MinhaNotificacao extends Notification implements WhatsappNotifiable
{
    public function via(object $notifiable): array
    {
        return ['database', WhatsappChannel::class];
    }

    public function toWhatsapp(object $notifiable): string
    {
        return "✅ *Título da Notificação*\n\nDescrição da mensagem aqui.";
    }

    public function toArray(object $notifiable): array
    {
        // payload para o painel interno...
    }
}
```

---

## Configuração Necessária (`.env`)

```env
META_PHONE_ID=seu_phone_number_id
META_WHATSAPP_TOKEN=seu_access_token_permanente
META_WHATSAPP_NUMBER=5511999999999
META_VERIFY_TOKEN=seu_verify_token
META_APP_SECRET=seu_app_secret
```

Essas variáveis são lidas via `config/services.php` sob a chave `services.meta.*`.

---

## Observações

- **Multi-tenant**: o canal filtra o vínculo por `tenant_id`, garantindo que usuários de tenants diferentes não se confundam.
- **Banco central**: a tabela `whatsapp_auth_requests` fica no banco `mysql` (central), fora do tenant. O canal usa `WhatsappAuthRequest::on('mysql')` explicitamente.
- **Falhas silenciosas**: erros na API Meta são logados via `Log::error()` mas não propagam exceções, evitando que uma falha de WhatsApp quebre o fluxo principal da aplicação.
- **Usuários sem vínculo**: simplesmente não recebem a mensagem. Não há tentativa de envio nem log desnecessário.
