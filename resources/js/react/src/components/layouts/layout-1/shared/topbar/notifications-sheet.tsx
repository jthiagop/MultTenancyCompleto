import { ReactNode, useMemo } from 'react';
import {
  Archive,
  BellOff,
  CheckCheck,
  Download,
  ExternalLink,
  FileText,
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

// classes do calendário por urgência / tipo
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
};

// Nomes dos meses em português abreviados
const MESES = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

// ── Card de data (estilo item-10) ─────────────────────────────────────────────
function DateCard({
  dateIso,
  urgencia,
}: {
  dateIso: string;
  urgencia: 'atrasado' | 'hoje' | 'amanha' | 'semana' | 'rateio';
}) {
  const d = new Date(dateIso + 'T00:00:00');
  const mes = MESES[d.getMonth()];
  const dia = String(d.getDate()).padStart(2, '0');
  const cal = urgenciaCalendar[urgencia] ?? urgenciaCalendar.semana;

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

// ── Avatar do remetente ──────────────────────────────────────────────────────
function SenderAvatar({
  triggeredBy,
}: {
  triggeredBy?: INotification['triggered_by'];
}) {
  const initials = triggeredBy?.name
    ? triggeredBy.name.split(' ').slice(0, 2).map((w) => w[0]).join('').toUpperCase()
    : '?';

  return (
    <Avatar className="size-9 shrink-0">
      {triggeredBy?.avatar && (
        <AvatarImage src={triggeredBy.avatar} alt={triggeredBy.name} />
      )}
      <AvatarFallback className="text-xs font-semibold">
        {initials}
      </AvatarFallback>
    </Avatar>
  );
}

// ── Card interno: calendário + descrição + valor ──────────────────────────────
function FinanceiroCardInner({ n }: { n: INotification }) {
  const urgencia     = (n.urgencia ?? 'semana') as 'atrasado' | 'hoje' | 'amanha' | 'semana';
  const subTipoColor = n.sub_tipo === 'receita' ? 'text-success' : 'text-destructive';
  const valorFmt     = n.valor != null
    ? new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n.valor)
    : null;

  const urgenciaLabel: Record<string, string> = {
    atrasado: 'Em atraso',
    hoje:     'Vence hoje',
    amanha:   'Vence amanhã',
    semana:   'Vence em breve',
  };

  return (
    <Card className="shadow-none p-2.5 rounded-lg bg-muted/70">
      <div className="flex items-center gap-2.5">
        {n.data_vencimento_iso && (
          <DateCard dateIso={n.data_vencimento_iso} urgencia={urgencia} />
        )}
        <div className="flex flex-col gap-1 min-w-0 flex-1">
          <span className="text-xs font-medium text-foreground truncate">
            {n.message.replace(/^'([^']+)'.*/, '$1')}
          </span>
          <div className="flex items-center gap-2">
            {valorFmt && (
              <span className={cn('text-xs font-semibold', subTipoColor)}>
                {valorFmt}
              </span>
            )}
            <span
              className={cn(
                'text-[10px] font-semibold px-1.5 py-0.5 rounded-full border',
                colorClass[urgencia === 'atrasado' ? 'danger' : urgencia === 'hoje' ? 'warning' : 'info'],
              )}
            >
              {urgenciaLabel[urgencia]}
            </span>
          </div>
        </div>
      </div>
    </Card>
  );
}

// ── Card de rateio intercompany (data em azul, padrão item-10) ───────────────
function RateioCardInner({ n }: { n: INotification }) {
  const valorFmt = n.valor != null
    ? new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n.valor)
    : null;

  // Extrai só a descrição entre aspas simples, se houver
  const descricao = n.message.match(/'([^']+)'/)?.[1] ?? n.message;

  // Fallback para hoje se não vier data de vencimento
  const dateIso = n.data_vencimento_iso ?? new Date().toISOString().slice(0, 10);

  return (
    <Card className="shadow-none p-2.5 rounded-lg bg-muted/70">
      <div className="flex items-center gap-2.5">
        {/* Widget mês/dia em azul — mesmo padrão do item-10 */}
        <DateCard dateIso={dateIso} urgencia="rateio" />

        <div className="flex flex-col gap-1.5 min-w-0 flex-1">
          {/* Ícone de rateio antes do título */}
          <span className="flex items-center gap-1.5 text-xs font-medium text-foreground truncate">
            <GitFork className="size-3 shrink-0 text-blue-500" />
            {descricao}
          </span>
          <div className="flex items-center gap-2 flex-wrap">
            {valorFmt && (
              <span className="text-xs font-semibold text-destructive">{valorFmt}</span>
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

// ── Card de relatório gerado (PDF com link de download) ─────────────────────
function RelatorioCardInner({ n }: { n: INotification }) {
  return (
    <Card className="shadow-none flex items-center flex-row gap-2.5 p-2.5 rounded-lg bg-muted/70">
      <div className="flex items-center justify-center size-9 shrink-0 rounded bg-red-100 dark:bg-red-950/40">
        <FileText className="size-5 text-red-500" />
      </div>
      <div className="flex flex-col gap-0.5 grow min-w-0">
        <span className="text-xs font-medium text-foreground truncate" title={n.title}>
          {n.title.length > 45 ? n.title.slice(0, 45) + '…' : n.title}
        </span>
        <div className="flex items-center gap-1.5 text-[10px] text-muted-foreground">
          {n.file_type && (
            <span className="font-medium uppercase">{n.file_type}</span>
          )}
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
      {n.action_url && (
        <a href={n.action_url} target="_blank" rel="noopener noreferrer">
          <Button size="sm" variant="outline" className="gap-1 h-7 text-xs shrink-0 border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-400 dark:hover:bg-blue-900/50">
            <Download className="size-3" />
            Baixar
          </Button>
        </a>
      )}
    </Card>
  );
}

// ── Item de notificação (padrão item-10) ──────────────────────────────────────
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
  const isContaVencendo   = n.tipo === 'conta_vencendo';
  const isRateioRecebido  = n.tipo === 'rateio_recebido';
  const isRelatorioGerado = n.tipo === 'relatorio_gerado';
  const podePagar         = (isContaVencendo || isRateioRecebido || n.acao === 'criado' || n.acao === 'atualizado')
    && n.transacao_id != null
    && !isRelatorioGerado;

  const pagamentoUrl = n.transacao_id
    ? `/financeiro?pagamento=${n.transacao_id}`
    : '/financeiro';

  // Texto de ação inline com o nome do remetente
  const valorInline = n.valor != null
    ? new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n.valor)
    : null;

  const acaoTexto = isContaVencendo
    ? n.title.toLowerCase()
    : isRateioRecebido
      ? `lançou um rateio de ${valorInline ?? 'valor não informado'} para esta unidade`
      : n.message.replace(/^'[^']+'[^,]*,?\s*/, '').replace(/\.$/, '').toLowerCase();

  const senderName = n.triggered_by?.name ?? (isRateioRecebido ? (n.nome_matriz ?? 'Matriz') : 'Sistema');

  return (
    <div
      className="flex gap-2.5 px-5 py-4 relative group/item transition-colors"
    >
      {/* Dot de não lido */}
      {isUnread && (
        <span className="absolute top-4 end-4 size-1.5 rounded-full bg-primary" />
      )}

      {/* Avatar do remetente */}
      <SenderAvatar triggeredBy={n.triggered_by} />

      {/* Coluna principal */}
      <div className="flex flex-col gap-2.5 grow min-w-0">

        {/* Linha 1: Nome · ação · tempo · menu */}
        <div className="flex flex-col gap-0.5">
          <div className="flex items-start justify-between gap-1">
            <p className="text-sm leading-snug">
              <span className="font-semibold text-mono">{senderName}</span>
              {' '}
              <span className="text-secondary-foreground">{acaoTexto}</span>
            </p>
            {/* Dropdown de ações */}
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

          {/* Tempo · categoria */}
          <span className="flex items-center text-xs text-muted-foreground">
            {n.created_at}
            <span className="rounded-full size-1 bg-muted-foreground/30 mx-1.5" />
            {n.categoria === 'financeiro' ? 'Financeiro' : 'Sistema'}
          </span>
        </div>

        {/* Card interno: vencimento, rateio, relatório ou mensagem genérica */}
        {isContaVencendo ? (
          <FinanceiroCardInner n={n} />
        ) : isRateioRecebido ? (
          <RateioCardInner n={n} />
        ) : isRelatorioGerado ? (
          <RelatorioCardInner n={n} />
        ) : (
          <p className="text-xs text-muted-foreground leading-relaxed line-clamp-2">
            {n.message}
          </p>
        )}

        {/* Botões de ação no rodapé */}
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
                Informar pagamento
              </Button>
            </SheetClose>
          </div>
        )}
      </div>
    </div>
  );
}

// ── Lista ─────────────────────────────────────────────────────────────────────
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

// ── Sheet principal ───────────────────────────────────────────────────────────
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
      <SheetContent className="gap-0 sm:w-[500px] inset-5 start-auto h-auto rounded-lg p-0 sm:max-w-none **:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5" aria-describedby={undefined}>
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
