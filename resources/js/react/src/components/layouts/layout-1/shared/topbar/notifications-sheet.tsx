import { ReactNode, useMemo } from 'react';
import {
  Archive,
  BellOff,
  CheckCheck,
  Download,
  ExternalLink,
  GitFork,
  Loader2,
  MoreHorizontal,
  RefreshCw,
  Trash2,
} from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  Sheet,
  SheetBody,
  SheetClose,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from '@/components/ui/sheet';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Badge } from '@/components/ui/badge';
import { useNotifications, INotification } from '@/hooks/useNotifications';
import { toAbsoluteUrl } from '@/lib/helpers';
import { cn } from '@/lib/utils';

// ── Mapa de classes por cor ───────────────────────────────────────────────────
const colorClass: Record<string, string> = {
  success:   'bg-success/10 text-success border-success/20',
  danger:    'bg-destructive/10 text-destructive border-destructive/20',
  warning:   'bg-warning/10 text-warning border-warning/20',
  info:      'bg-info/10 text-info border-info/20',
  primary:   'bg-primary/10 text-primary border-primary/20',
  secondary: 'bg-muted text-muted-foreground',
};

// classes do calendário por urgência / tipo (padrão item-10)
const urgenciaCalendar: Record<string, { header: string; text: string; border: string }> = {
  atrasado: {
    header: 'bg-destructive/10 border-b border-b-destructive/20',
    text:   'text-destructive',
    border: 'border-destructive/30',
  },
  hoje: {
    header: 'bg-yellow-400/15 border-b border-b-yellow-400/30',
    text:   'text-yellow-500',
    border: 'border-yellow-400/40',
  },
  amanha: {
    header: 'bg-info/10 border-b border-b-info/20',
    text:   'text-info',
    border: 'border-info/30',
  },
  semana: {
    header: 'bg-primary/10 border-b border-b-primary/20',
    text:   'text-primary',
    border: 'border-primary/30',
  },
  rateio: {
    header: 'bg-blue-500/10 border-b border-b-blue-500/20',
    text:   'text-blue-500',
    border: 'border-blue-500/30',
  },
  receita: {
    header: 'bg-success/10 border-b border-b-success/20',
    text:   'text-success',
    border: 'border-success/30',
  },
  despesa: {
    header: 'bg-destructive/10 border-b border-b-destructive/20',
    text:   'text-destructive',
    border: 'border-destructive/30',
  },
};

const MESES = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

// ─────────────────────────────────────────────────────────────────────────────
// Helpers de formatação
// ─────────────────────────────────────────────────────────────────────────────

function fmtCurrency(value: number | null | undefined): string | null {
  if (value == null || Number.isNaN(value)) return null;
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value);
}

/** Extrai a descrição entre aspas simples na mensagem (usada por várias notificações). */
function extractDescription(message?: string | null): string | null {
  if (!message) return null;
  const m = message.match(/'([^']+)'/);
  return m ? m[1] : null;
}

/**
 * Mapa de extensão/tipo → ícone em /media/file-types/.
 * Se o tipo não estiver mapeado, cai em "text.svg" como fallback genérico.
 */
const FILE_TYPE_ICONS: Record<string, string> = {
  PDF:  'pdf.svg',
  OFX:  'text.svg',
  XLS:  'excel.svg',
  XLSX: 'excel.svg',
  CSV:  'excel.svg',
  DOC:  'doc.svg',
  DOCX: 'doc.svg',
  TXT:  'text.svg',
  ZIP:  'iso.svg',
  XML:  'text.svg',
  JSON: 'js.svg',
};

function fileTypeIcon(fileType?: string | null): string {
  if (!fileType) return toAbsoluteUrl('/media/file-types/text.svg');
  const key = String(fileType).trim().toUpperCase();
  return toAbsoluteUrl(`/media/file-types/${FILE_TYPE_ICONS[key] ?? 'text.svg'}`);
}

/**
 * Frase curta (sem valor / descrição) que vai LOGO após o nome do remetente,
 * no padrão item-10 (ex.: "Nova Hawthorne sent you an meeting invation").
 *
 * O card visual abaixo do header é responsável por mostrar o conteúdo
 * (descrição, valor, data). Por isso o header NUNCA repete esses dados.
 */
function buildAcaoTexto(n: INotification): string {
  const tipo = n.tipo;

  // Frases CURTAS (espelhando item-10: "sent you an meeting invation").
  // O card visual abaixo do header já mostra urgência, data e valor,
  // portanto não repetimos esses dados na frase de ação.
  if (tipo === 'conta_vencendo') {
    switch (n.urgencia) {
      case 'atrasado': return 'tem uma conta atrasada';
      case 'hoje':     return 'tem conta vencendo hoje';
      case 'amanha':   return 'tem conta vencendo amanhã';
      case 'semana':   return 'tem conta a vencer';
      default:         return 'tem conta a vencer';
    }
  }

  if (tipo === 'lancamento_agendado') {
    return n.sub_tipo === 'receita'
      ? 'agendou um recebimento'
      : 'agendou um pagamento';
  }

  if (tipo === 'lancamento_financeiro') {
    const isReceita = n.sub_tipo === 'receita';
    switch (n.acao) {
      case 'criado':      return isReceita ? 'lançou uma receita' : 'lançou uma despesa';
      case 'atualizado':  return isReceita ? 'atualizou uma receita' : 'atualizou uma despesa';
      case 'pago':        return 'registrou um pagamento';
      case 'recebido':    return 'registrou um recebimento';
      default:            return 'movimentou o financeiro';
    }
  }

  if (tipo === 'rateio_recebido')  return 'lançou um rateio';
  if (tipo === 'repasse_criado')   return 'criou um repasse';
  if (tipo === 'relatorio_gerado') return 'gerou um relatório';
  if (tipo === 'aviso_sistema')    return 'enviou um aviso';

  // Fallback: usa o título da notificação, em minúsculas.
  return (n.title || 'enviou uma notificação').toLowerCase();
}

// ─────────────────────────────────────────────────────────────────────────────
// Subcomponentes
// ─────────────────────────────────────────────────────────────────────────────

function DateCard({
  dateIso,
  variant,
}: {
  dateIso: string;
  variant: keyof typeof urgenciaCalendar;
}) {
  const d = new Date(dateIso + 'T00:00:00');
  const mes = MESES[d.getMonth()];
  const dia = String(d.getDate()).padStart(2, '0');
  const cal = urgenciaCalendar[variant] ?? urgenciaCalendar.semana;

  return (
    <div className={cn('border rounded-lg shrink-0', cal.border)}>
      <div className={cn('flex items-center justify-center rounded-t-lg px-2 py-1', cal.header)}>
        <span className={cn('text-[10px] font-semibold uppercase tracking-wide', cal.text)}>
          {mes}
        </span>
      </div>
      <div className="flex items-center justify-center w-10 h-9">
        <span className="font-semibold text-sm text-foreground tracking-tight">{dia}</span>
      </div>
    </div>
  );
}

function SenderAvatar({ triggeredBy }: { triggeredBy?: INotification['triggered_by'] }) {
  const initials = triggeredBy?.name
    ? triggeredBy.name.split(' ').slice(0, 2).map((w) => w[0]).join('').toUpperCase()
    : '?';

  return (
    <Avatar className="size-9 shrink-0">
      {triggeredBy?.avatar && (
        <AvatarImage src={triggeredBy.avatar} alt={triggeredBy.name} />
      )}
      <AvatarFallback className="text-xs font-semibold">{initials}</AvatarFallback>
    </Avatar>
  );
}

// ─── Card: conta vencendo / lançamento agendado (padrão item-10) ─────────────
function ContaVencendoCard({ n }: { n: INotification }) {
  // `lancamento_agendado` é sempre "hoje" mesmo quando o backend antigo não
  // setava o campo `urgencia`. Preserva o comportamento esperado do item-10.
  const urgenciaResolved = (n.urgencia
    ?? (n.tipo === 'lancamento_agendado' ? 'hoje' : 'semana')) as keyof typeof urgenciaCalendar;

  const subTipoTone = n.sub_tipo === 'receita' ? 'text-success' : 'text-destructive';
  const valorFmt    = fmtCurrency(n.valor);
  const descricao   = extractDescription(n.message) ?? n.title;

  // Fallback do calendário para hoje quando o payload não trouxer a data.
  // Mantém o card sempre coerente com o padrão item-10.
  const dateIso = n.data_vencimento_iso ?? new Date().toISOString().slice(0, 10);

  const urgenciaLabel: Record<string, string> = {
    atrasado: 'Em atraso',
    hoje:     'Vence hoje',
    amanha:   'Vence amanhã',
    semana:   'Vence em breve',
  };

  const urgenciaBadge: Record<string, string> = {
    atrasado: 'danger',
    hoje:     'warning',
    amanha:   'info',
    semana:   'info',
  };

  return (
    <Card className="shadow-none p-2.5 rounded-lg bg-muted/70">
      <div className="flex items-center gap-2.5">
        <DateCard dateIso={dateIso} variant={urgenciaResolved} />
        <div className="flex flex-col gap-1 min-w-0 flex-1">
          <span className="text-xs font-medium text-foreground truncate" title={descricao}>
            {descricao}
          </span>
          <div className="flex items-center gap-2 flex-wrap">
            {valorFmt && (
              <span className={cn('text-xs font-semibold', subTipoTone)}>
                {n.sub_tipo === 'receita' ? '+ ' : '- '}
                {valorFmt}
              </span>
            )}
            <span
              className={cn(
                'text-[10px] font-semibold px-1.5 py-0.5 rounded-full border',
                colorClass[urgenciaBadge[urgenciaResolved] ?? 'info'],
              )}
            >
              {urgenciaLabel[urgenciaResolved] ?? 'A vencer'}
            </span>
          </div>
        </div>
      </div>
    </Card>
  );
}

// ─── Card: lançamento criado / atualizado / pago / recebido ──────────────────
function LancamentoCard({ n }: { n: INotification }) {
  const isReceita = n.sub_tipo === 'receita';
  const valorFmt  = fmtCurrency(n.valor);
  const descricao = extractDescription(n.message) ?? n.title;

  const dateIso = n.data_vencimento_iso ?? new Date().toISOString().slice(0, 10);
  const variant: keyof typeof urgenciaCalendar = isReceita ? 'receita' : 'despesa';

  const acaoLabel: Record<string, string> = {
    criado:     'Criado',
    atualizado: 'Atualizado',
    pago:       'Pago',
    recebido:   'Recebido',
    vencimento: 'Vencimento',
  };

  return (
    <Card className="shadow-none p-2.5 rounded-lg bg-muted/70">
      <div className="flex items-center gap-2.5">
        <DateCard dateIso={dateIso} variant={variant} />
        <div className="flex flex-col gap-1 min-w-0 flex-1">
          <span className="text-xs font-medium text-foreground truncate" title={descricao}>
            {descricao}
          </span>
          <div className="flex items-center gap-2 flex-wrap">
            {valorFmt && (
              <span className={cn('text-xs font-semibold', isReceita ? 'text-success' : 'text-destructive')}>
                {isReceita ? '+ ' : '- '}
                {valorFmt}
              </span>
            )}
            {n.acao && (
              <span
                className={cn(
                  'text-[10px] font-semibold px-1.5 py-0.5 rounded-full border',
                  colorClass[isReceita ? 'success' : 'danger'],
                )}
              >
                {acaoLabel[n.acao] ?? n.acao}
              </span>
            )}
          </div>
        </div>
      </div>
    </Card>
  );
}

// ─── Card: rateio intercompany ───────────────────────────────────────────────
function RateioCard({ n }: { n: INotification }) {
  const valorFmt  = fmtCurrency(n.valor);
  const descricao = extractDescription(n.message) ?? n.title;
  const dateIso   = n.data_vencimento_iso ?? new Date().toISOString().slice(0, 10);

  return (
    <Card className="shadow-none p-2.5 rounded-lg bg-muted/70">
      <div className="flex items-center gap-2.5">
        <DateCard dateIso={dateIso} variant="rateio" />
        <div className="flex flex-col gap-1.5 min-w-0 flex-1">
          <span className="flex items-center gap-1.5 text-xs font-medium text-foreground truncate" title={descricao}>
            <GitFork className="size-3 shrink-0 text-blue-500" />
            {descricao}
          </span>
          <div className="flex items-center gap-2 flex-wrap">
            {valorFmt && (
              <span className="text-xs font-semibold text-destructive">- {valorFmt}</span>
            )}
            {n.nome_matriz && (
              <span className="text-[10px] font-semibold px-1.5 py-0.5 rounded-full border bg-blue-500/10 text-blue-600 border-blue-500/20 dark:text-blue-400">
                {n.nome_matriz}
              </span>
            )}
          </div>
        </div>
      </div>
    </Card>
  );
}

// ─── Card: relatório / arquivo de download (padrão item-8) ───────────────────
function RelatorioCard({ n }: { n: INotification }) {
  const iconSrc = fileTypeIcon(n.file_type);
  const fileLabel = n.file_type ? n.file_type.toUpperCase() : 'Arquivo';

  return (
    <Card className="shadow-none flex items-center justify-between flex-row gap-1.5 p-2.5 rounded-lg bg-muted/70">
      <div className="flex items-center gap-2 min-w-0 flex-1">
        <img src={iconSrc} className="h-7 shrink-0" alt={fileLabel} />
        <div className="flex flex-col min-w-0">
          <span className="font-medium text-secondary-foreground text-xs truncate" title={n.title}>
            {n.title}
          </span>
          <div className="flex items-center gap-1.5 text-[10px] text-muted-foreground">
            <span className="font-medium uppercase">{fileLabel}</span>
            {n.file_size && (
              <>
                <span className="size-1 rounded-full bg-muted-foreground/30" />
                <span>{n.file_size}</span>
              </>
            )}
            {n.expires_in && (
              <>
                <span className="size-1 rounded-full bg-muted-foreground/30" />
                <span>Expira em {n.expires_in}</span>
              </>
            )}
          </div>
        </div>
      </div>
      {n.action_url && (
        <a
          href={n.action_url}
          target={n.target ?? '_blank'}
          rel="noopener noreferrer"
          className="shrink-0"
          aria-label="Baixar arquivo"
        >
          <Button
            size="sm"
            variant="outline"
            className="gap-1 h-7 text-xs border-primary/30 bg-primary/5 text-primary hover:bg-primary/10"
          >
            <Download className="size-3" />
            Baixar
          </Button>
        </a>
      )}
    </Card>
  );
}

// ─── Card: aviso de sistema (texto simples) ──────────────────────────────────
function AvisoCard({ n }: { n: INotification }) {
  return (
    <Card className="shadow-none p-2.5 rounded-lg bg-muted/70">
      <p className="text-xs text-secondary-foreground leading-relaxed line-clamp-3">
        {n.message}
      </p>
    </Card>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Item de notificação (padrão item-10)
// ─────────────────────────────────────────────────────────────────────────────

function NotificationItem({
  n,
  onRead,
  onRemove,
}: {
  n: INotification;
  onRead: (id: string) => void;
  onRemove: (id: string) => void;
}) {
  const navigate          = useNavigate();
  const isUnread          = !n.read_at;
  const isContaVencendo   = n.tipo === 'conta_vencendo' || n.tipo === 'lancamento_agendado';
  const isLancamento      = n.tipo === 'lancamento_financeiro';
  const isRateio          = n.tipo === 'rateio_recebido';
  const isRelatorio       = n.tipo === 'relatorio_gerado';
  const isAviso           = n.tipo === 'aviso_sistema';

  const senderName = n.triggered_by?.name
    ?? (isRateio ? (n.nome_matriz ?? 'Matriz') : 'Sistema');
  const acaoTexto  = buildAcaoTexto(n);

  const podePagar = (isContaVencendo || isRateio || (isLancamento && (n.acao === 'criado' || n.acao === 'atualizado')))
    && n.transacao_id != null;

  const pagamentoUrl = n.transacao_id
    ? `/financeiro?pagamento=${n.transacao_id}`
    : '/financeiro';

  const categoriaLabel = n.categoria === 'financeiro'
    ? 'Financeiro'
    : n.categoria === 'sistema'
      ? 'Sistema'
      : 'Geral';

  return (
    <div
      className={cn(
        'flex gap-2.5 px-5 py-4 relative group/item transition-colors',
        isUnread && 'bg-primary/5 hover:bg-primary/10',
      )}
    >
      {/* Dot de não lido */}
      {isUnread && (
        <span className="absolute top-4 end-4 size-1.5 rounded-full bg-primary" />
      )}

      <SenderAvatar triggeredBy={n.triggered_by} />

      <div className="flex flex-col gap-2.5 grow min-w-0">
        {/* Linha 1: Nome · ação curta · menu */}
        <div className="flex flex-col gap-0.5">
          <div className="flex items-start justify-between gap-1">
            {/* text-balance distribui melhor em quebras inevitáveis (nomes longos);
                pe-1 garante respiro entre o texto e o botão de menu (•••). */}
            <p className="text-sm leading-snug pe-1 text-balance">
              <span className="font-semibold text-mono">{senderName}</span>
              {' '}
              <span className="text-secondary-foreground">{acaoTexto}</span>
            </p>
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button
                  variant="ghost"
                  size="sm"
                  mode="icon"
                  className="shrink-0 -mt-0.5 h-6 w-6"
                >
                  <MoreHorizontal className="size-3.5" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-40">
                {isUnread && (
                  <DropdownMenuItem onClick={() => onRead(n.id)}>
                    <CheckCheck className="size-3.5" />
                    Marcar como lida
                  </DropdownMenuItem>
                )}
                <DropdownMenuItem
                  className="text-destructive focus:text-destructive"
                  onClick={() => onRemove(n.id)}
                >
                  <Trash2 className="size-3.5" />
                  Remover
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>

          <span className="flex items-center text-xs text-muted-foreground">
            {n.created_at}
            <span className="rounded-full size-1 bg-muted-foreground/30 mx-1.5" />
            {categoriaLabel}
          </span>
        </div>

        {/* Card interno por tipo (padrão item-10 / item-8) */}
        {isContaVencendo  && <ContaVencendoCard n={n} />}
        {isLancamento     && <LancamentoCard    n={n} />}
        {isRateio         && <RateioCard        n={n} />}
        {isRelatorio      && <RelatorioCard     n={n} />}
        {isAviso          && <AvisoCard         n={n} />}
        {!isContaVencendo && !isLancamento && !isRateio && !isRelatorio && !isAviso && (
          <AvisoCard n={n} />
        )}

        {/* Botão de ação no rodapé do item (financeiro com transação) */}
        {podePagar && (
          <div className="flex gap-2">
            <SheetClose asChild>
              <Button
                size="sm"
                variant="outline"
                className="gap-1.5 h-7 text-xs"
                onClick={() => navigate(pagamentoUrl)}
              >
                <ExternalLink className="size-3" />
                {n.sub_tipo === 'receita' ? 'Informar recebimento' : 'Informar pagamento'}
              </Button>
            </SheetClose>
          </div>
        )}
      </div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Lista
// ─────────────────────────────────────────────────────────────────────────────

function NotificationList({
  items,
  onRead,
  onRemove,
}: {
  items: INotification[];
  onRead: (id: string) => void;
  onRemove: (id: string) => void;
}) {
  if (items.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center gap-2 py-16 text-muted-foreground">
        <BellOff className="size-8 opacity-40" />
        <span className="text-sm">Nenhuma notificação</span>
      </div>
    );
  }

  return (
    <div className="flex flex-col group/list divide-y divide-border">
      {items.map((n) => (
        <div key={n.id} className="group">
          <NotificationItem n={n} onRead={onRead} onRemove={onRemove} />
        </div>
      ))}
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet principal
// ─────────────────────────────────────────────────────────────────────────────

export function NotificationsSheet({ trigger }: { trigger: ReactNode }) {
  const {
    notifications,
    unreadCount,
    loading,
    load,
    markAsRead,
    markAllAsRead,
    remove,
    archiveRead,
  } = useNotifications(30_000);

  const financeiro = useMemo(
    () => notifications.filter((n) => n.categoria === 'financeiro'),
    [notifications],
  );

  const sistema = useMemo(
    () => notifications.filter((n) => n.categoria === 'sistema' || n.categoria === 'geral'),
    [notifications],
  );

  const unreadFinanceiro = financeiro.filter((n) => !n.read_at).length;
  const unreadSistema    = sistema.filter((n) => !n.read_at).length;

  return (
    <Sheet>
      <SheetTrigger asChild>
        <span className="relative inline-flex">
          {trigger}
          {unreadCount > 0 && (
            <span className="pointer-events-none absolute -top-1 -end-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-destructive px-1 text-[10px] font-semibold leading-none text-destructive-foreground">
              {unreadCount > 99 ? '99+' : unreadCount}
            </span>
          )}
        </span>
      </SheetTrigger>
      <SheetContent
        className="gap-0 sm:w-[500px] inset-5 start-auto h-auto rounded-lg p-0 sm:max-w-none **:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5"
        aria-describedby={undefined}
      >
        <SheetHeader className="mb-0 border-b border-border">
          <div className="flex items-center justify-between px-5 py-3.5">
            <div className="flex items-center gap-2">
              <SheetTitle className="text-base">Notificações</SheetTitle>
              {unreadCount > 0 && (
                <Badge variant="destructive" className="h-5 min-w-5 px-1.5 text-xs">
                  {unreadCount > 99 ? '99+' : unreadCount}
                </Badge>
              )}
            </div>
          </div>
        </SheetHeader>

        <SheetBody className="grow p-0">
          {loading && notifications.length === 0 ? (
            <div className="flex items-center justify-center py-20">
              <Loader2 className="size-6 animate-spin text-muted-foreground" />
            </div>
          ) : (
            <Tabs defaultValue="all" className="w-full">
              <TabsList variant="line" className="w-full px-5">
                <TabsTrigger value="all" className="gap-1.5">
                  Todas
                  {unreadCount > 0 && (
                    <span className="w-1.5 h-1.5 rounded-full bg-primary" />
                  )}
                </TabsTrigger>

                <TabsTrigger value="financeiro" className="gap-1.5">
                  Financeiro
                  {unreadFinanceiro > 0 && (
                    <span
                      className={cn(
                        'inline-flex items-center justify-center rounded-full text-[10px] font-semibold min-w-4 h-4 px-1',
                        colorClass.danger,
                      )}
                    >
                      {unreadFinanceiro}
                    </span>
                  )}
                </TabsTrigger>

                <TabsTrigger value="sistema" className="gap-1.5">
                  Sistema
                  {unreadSistema > 0 && (
                    <span className="w-1.5 h-1.5 rounded-full bg-warning" />
                  )}
                </TabsTrigger>
              </TabsList>

              <ScrollArea className="h-[calc(100vh-13rem)] items-stretch">
                <TabsContent value="all" className="mt-0 flex-1">
                  <NotificationList items={notifications} onRead={markAsRead} onRemove={remove} />
                </TabsContent>
                <TabsContent value="financeiro" className="mt-0">
                  <NotificationList items={financeiro} onRead={markAsRead} onRemove={remove} />
                </TabsContent>
                <TabsContent value="sistema" className="mt-0">
                  <NotificationList items={sistema} onRead={markAsRead} onRemove={remove} />
                </TabsContent>
              </ScrollArea>
            </Tabs>
          )}
        </SheetBody>

        <SheetFooter className="border-t border-border p-4 grid grid-cols-3 gap-2">
          <Button variant="outline" size="sm" className="gap-1.5" onClick={() => void archiveRead()}>
            <Archive className="size-3.5" />
            Arquivar lidas
          </Button>
          <Button
            variant="outline"
            size="sm"
            className="gap-1.5"
            onClick={() => void markAllAsRead()}
            disabled={unreadCount === 0}
          >
            <CheckCheck className="size-3.5" />
            Marcar tudo lido
          </Button>
          <Button
            variant="outline"
            size="sm"
            className="gap-1.5"
            onClick={() => void load()}
            disabled={loading}
          >
            <RefreshCw className={cn('size-3.5', loading && 'animate-spin')} />
            Atualizar
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
