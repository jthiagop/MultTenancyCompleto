<?php

namespace App\Notifications;

use App\Channels\WhatsappChannel;
use App\Channels\WhatsappNotifiable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notificação enviada no dia do vencimento de um lançamento marcado como "Agendado".
 *
 * Disparada pelo LancamentoAgendadoNotificacaoJob, que é agendado no momento
 * do save quando o campo `agendado = true`.
 */
class LancamentoAgendadoNotification extends Notification implements WhatsappNotifiable
{
    use Queueable;

    public function __construct(
        protected string  $descricao,
        protected float   $valor,
        protected string  $tipo,          // 'entrada' | 'saida'
        protected string  $vencimento,    // Y-m-d
        protected ?int    $transacaoId  = null,
        protected ?int    $companyId    = null,
        protected ?int    $triggeredBy  = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsappChannel::class];
    }

    public function toWhatsapp(object $notifiable): string
    {
        $isEntrada  = $this->tipo === 'entrada';
        $tipoLabel  = $isEntrada ? 'Recebimento' : 'Pagamento';
        $emoji      = $isEntrada ? '💰' : '💸';
        $valorFmt   = 'R$ ' . number_format($this->valor, 2, ',', '.');
        $vencFmt    = \Carbon\Carbon::parse($this->vencimento)->format('d/m/Y');

        return implode("\n", [
            "{$emoji} *Lançamento Agendado — Vence Hoje*",
            "",
            "*{$tipoLabel}:* {$valorFmt}",
            "📋 _{$this->descricao}_",
            "📅 Vencimento: {$vencFmt}",
            "",
            "Acesse o sistema para confirmar a baixa do lançamento.",
        ]);
    }

    /**
     * Mapeamento das 4 variáveis do template `lancamento_agendado_aviso`
     * registrado e aprovado na Meta:
     *
     *   Body do template (UTILITY, pt_BR):
     *
     *     Olá! Você tem um {{1}} agendado que vence hoje:
     *
     *     *Valor:* R$ {{2}}
     *     *Descrição:* _{{3}}_
     *     *Vencimento:* {{4}}
     *
     *     Acesse o sistema para confirmar a baixa do lançamento.
     *
     * Argumentos:
     *   {{1}} = "pagamento" | "recebimento" (lowercase, casa com a frase do body)
     *   {{2}} = valor sem o "R$" (a Meta valida que variáveis não tenham
     *          símbolos especiais que poluam o aprovador)
     *   {{3}} = descrição do lançamento
     *   {{4}} = data dd/mm/YYYY
     *
     * @return array<string,mixed>
     */
    public function toWhatsappTemplate(object $notifiable): array
    {
        $isEntrada = $this->tipo === 'entrada';
        $tipoVar   = $isEntrada ? 'recebimento' : 'pagamento';
        $valorVar  = number_format($this->valor, 2, ',', '.');
        $vencVar   = \Carbon\Carbon::parse($this->vencimento)->format('d/m/Y');

        return [
            'name'     => config('services.meta.whatsapp_templates.lancamento_agendado', 'lancamento_agendado_aviso'),
            'language' => config('services.meta.whatsapp_template_language', 'pt_BR'),
            'components' => [
                [
                    'type'       => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => $tipoVar],
                        ['type' => 'text', 'text' => $valorVar],
                        ['type' => 'text', 'text' => $this->descricao],
                        ['type' => 'text', 'text' => $vencVar],
                    ],
                ],
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        $isEntrada = $this->tipo === 'entrada';
        $valorFmt  = 'R$ ' . number_format($this->valor, 2, ',', '.');
        $tipoLabel = $isEntrada ? 'recebimento' : 'pagamento';

        $venc = \Carbon\Carbon::parse($this->vencimento);

        return [
            'icon'                => 'ki-calendar-tick',
            'color'               => $isEntrada ? 'success' : 'warning',
            'title'               => 'Lançamento Agendado — Vence Hoje',
            // Mensagem em formato curto: o front extrai a descrição entre aspas
            // para mostrar no card visual e o resto vai como contexto.
            'message'             => "'{$this->descricao}' ({$valorFmt}) vence hoje.",
            'action_url'          => null,
            'target'              => '_self',
            'tipo'                => 'lancamento_agendado',
            'categoria'           => 'financeiro',
            'sub_tipo'            => $isEntrada ? 'receita' : 'despesa',
            'acao'                => 'vencimento',
            // urgência permite ao front aplicar o calendário amarelo (item-10)
            'urgencia'            => 'hoje',
            // valor + datas alimentam o card de calendário do front
            'valor'               => $this->valor,
            'data_vencimento'     => $venc->format('d/m/Y'),
            'data_vencimento_iso' => $venc->format('Y-m-d'),
            'transacao_id'        => $this->transacaoId,
            'company_id'          => $this->companyId,
            'triggered_by'        => $this->triggeredBy,
        ];
    }
}
