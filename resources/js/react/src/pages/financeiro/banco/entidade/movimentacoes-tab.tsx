import {
  useMemo,
  useState,
  type KeyboardEvent,
  type MouseEvent,
  type ReactNode,
} from 'react';
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { defaultPeriod } from '@/components/ui/period-picker';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { DesfazerConciliacaoDialog } from '@/pages/financeiro/banco/entidade/desfazer-conciliacao-dialog';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableFooter,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from '@/components/ui/tooltip';
import { AlertCircle, ChevronDown, Eye, HelpCircle, Loader2, MoreHorizontal, Undo2 } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { DiaMovimentacaoApi } from '@/hooks/useMovimentacoesConciliadas';
import { useMovimentacoesConciliadas } from '@/hooks/useMovimentacoesConciliadas';
import { FinanceiroTransacaoTableCardHeader } from '@/pages/financeiro/components/financeiro-transacao-table-card-header';
import { ConciliacaoDetalhesSheet } from '@/pages/financeiro/banco/entidade/conciliacao-detalhes-sheet';

function formatBRL(cents: number) {
  const v = cents / 100;
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);
}

function formatBRLSigned(cents: number) {
  const sign = cents < 0 ? '-' : '';
  return sign + formatBRL(Math.abs(cents));
}

/** Evita `<button>` dentro de `AccordionTrigger` (também é um button) e não alterna o acordeão ao clicar. */
function stopAccordionBubble(e: MouseEvent | KeyboardEvent) {
  e.stopPropagation();
}

const TT = {
  filtrosDias: (
    <>
      <p className="font-medium">Filtros por dia</p>
      <ul className="mt-1.5 list-disc space-y-1 pl-4 text-[11px] leading-relaxed">
        <li>
          <strong>Tudo:</strong> todos os dias com movimentações no período.
        </li>
        <li>
          <strong>Sem pendências:</strong> dias em que não há conciliação parcial nem divergente.
        </li>
        <li>
          <strong>Com pendências:</strong> dias com pelo menos uma movimentação ainda parcial ou divergente entre
          extrato e sistema.
        </li>
      </ul>
    </>
  ),
  pendencia:
    'Pendência: neste dia existe conciliação parcial ou divergente — o extrato do banco e os lançamentos do sistema ainda não fecham (valores ou vínculos em aberto).',
  diferencaSaldo:
    'Diferença entre o total conciliado no sistema e o total do extrato do banco neste dia. Zero significa que os totais coincidem.',
  diferencaLinha:
    'Por linha: valor conciliado no sistema menos a parte do extrato atribuída a este vínculo. Pode ser diferente de zero quando há rateio entre vários lançamentos, conciliação parcial ou convenção de sinal (crédito/débito) distinta entre banco e sistema.',
  sistema:
    'Total dos valores dos lançamentos financeiros já vinculados ao extrato (valor conciliado), somados no dia.',
  banco: 'Total proporcional ao valor das linhas do extrato bancário neste dia.',
  captionMovimentacoes:
    'Cada linha compara o lançamento no sistema com o movimento do extrato. Os valores costumam coincidir; quando não coincidem, a coluna «Diferença» mostra o desvio. O valor do banco pode ser uma parte proporcional do extrato se um mesmo extrato estiver dividido entre vários lançamentos.',
  colConciliacao:
    'Como o lançamento foi pareado com o extrato: manual (usuário), automático (regras) ou pendente de conclusão.',
  manual: 'Conciliação feita manualmente entre extrato e lançamento.',
  automatico: 'Conciliação automática (por exemplo, regras ou correspondência automática).',
  pendenteLinha: 'Conciliação ainda não finalizada ou aguardando ação.',
} as const;

function InfoHintButton({ label, children }: { label: string; children: ReactNode }) {
  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <Button
          type="button"
          variant="outline"
          size="icon"
          className="size-7 shrink-0 text-muted-foreground hover:text-foreground"
          aria-label={label}
        >
          <HelpCircle className="size-3.5" aria-hidden />
        </Button>
      </TooltipTrigger>
      <TooltipContent side="top" align="start" className="max-w-xs text-left">
        <div className="text-xs leading-relaxed">{children}</div>
      </TooltipContent>
    </Tooltip>
  );
}

type ConciliacaoTipo = 'manual' | 'automatico' | 'pendente';

interface LinhaMovimentacao {
  id: string;
  /** `BankStatement::id` — usado em GET/POST `relatorios/conciliacao/{id}/…` */
  bankStatementId: number;
  transacaoFinanceiraId?: number;
  descricaoSistema: string;
  subcategoria?: string;
  valorSistemaCents: number;
  descricaoBanco: string;
  valorBancoCents: number;
  conciliacao: ConciliacaoTipo;
}

interface DiaAgrupado {
  /** yyyy-mm-dd — valor único do Accordion */
  id: string;
  dataLabel: string;
  diaSemana: string;
  /** Diferença acumulada do dia (sistema vs banco) — 0 = sem alerta */
  diferencaCents: number;
  saldoSistemaCents: number;
  saldoBancoCents: number;
  temPendencia: boolean;
  linhas: LinhaMovimentacao[];
}

function mapDiaApi(d: DiaMovimentacaoApi): DiaAgrupado {
  return {
    id: d.id,
    dataLabel: d.data_label,
    diaSemana: d.dia_semana,
    diferencaCents: d.diferenca_cents,
    saldoSistemaCents: d.saldo_sistema_cents,
    saldoBancoCents: d.saldo_banco_cents,
    temPendencia: d.tem_pendencia,
    linhas: d.linhas.map((l) => ({
      id: l.id,
      bankStatementId: l.bank_statement_id ?? 0,
      transacaoFinanceiraId: l.transacao_financeira_id,
      descricaoSistema: l.descricao_sistema,
      subcategoria: l.subcategoria ?? undefined,
      valorSistemaCents: l.valor_sistema_cents,
      descricaoBanco: l.descricao_banco,
      valorBancoCents: l.valor_banco_cents,
      conciliacao: l.conciliacao,
    })),
  };
}

function badgeConciliacao(tipo: ConciliacaoTipo) {
  const map: Record<ConciliacaoTipo, { label: string; className: string; hint: string }> = {
    manual: { label: 'Manual', className: 'bg-sky-500/15 text-sky-700 border-sky-500/30', hint: TT.manual },
    automatico: {
      label: 'Automático',
      className: 'bg-emerald-500/15 text-emerald-700 border-emerald-500/30',
      hint: TT.automatico,
    },
    pendente: { label: 'Pendente', className: 'bg-amber-500/15 text-amber-800 border-amber-500/30', hint: TT.pendenteLinha },
  };
  const m = map[tipo];
  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <span className="inline-flex cursor-help">
          <Badge variant="outline" className={cn('text-xs font-normal', m.className)}>
            {m.label}
          </Badge>
        </span>
      </TooltipTrigger>
      <TooltipContent side="top" className="max-w-[240px]">
        <p className="text-xs leading-relaxed">{m.hint}</p>
      </TooltipContent>
    </Tooltip>
  );
}

interface MovimentacoesTabProps {
  entidadeId: string | undefined;
}

export function MovimentacoesTab({ entidadeId }: MovimentacoesTabProps) {
  const [filtroSubTab, setFiltroSubTab] = useState<'tudo' | 'sem_pendencias' | 'com_pendencias'>('tudo');
  const [period, setPeriod] = useState(defaultPeriod);
  const [searchQuery, setSearchQuery] = useState('');

  const [detalhesSheetOpen, setDetalhesSheetOpen] = useState(false);
  const [detalhesBankStatementId, setDetalhesBankStatementId] = useState<number | null>(null);
  const [desfazerInfo, setDesfazerInfo] = useState<{ id: number; descricao: string } | null>(null);

  const { dias: diasApi, loading, error, refresh } = useMovimentacoesConciliadas(entidadeId, period);

  const diasBase = useMemo(() => diasApi.map(mapDiaApi), [diasApi]);

  const diasComBusca = useMemo(() => {
    const q = searchQuery.trim().toLowerCase();
    if (!q) return diasBase;
    return diasBase
      .map((dia) => {
        const linhas = dia.linhas.filter(
          (l) =>
            l.descricaoSistema.toLowerCase().includes(q) ||
            l.descricaoBanco.toLowerCase().includes(q) ||
            (l.subcategoria?.toLowerCase().includes(q) ?? false),
        );
        if (linhas.length === 0) return null;
        const saldoSistema = linhas.reduce((s, l) => s + l.valorSistemaCents, 0);
        const saldoBanco = linhas.reduce((s, l) => s + l.valorBancoCents, 0);
        return {
          ...dia,
          linhas,
          saldoSistemaCents: saldoSistema,
          saldoBancoCents: saldoBanco,
          diferencaCents: saldoSistema - saldoBanco,
        };
      })
      .filter((d): d is DiaAgrupado => d !== null);
  }, [diasBase, searchQuery]);

  const contagem = useMemo(() => {
    const total = diasComBusca.length;
    const sem = diasComBusca.filter((d) => !d.temPendencia).length;
    const com = diasComBusca.filter((d) => d.temPendencia).length;
    return { total, sem, com };
  }, [diasComBusca]);

  const diasFiltrados = useMemo(() => {
    if (filtroSubTab === 'sem_pendencias') return diasComBusca.filter((d) => !d.temPendencia);
    if (filtroSubTab === 'com_pendencias') return diasComBusca.filter((d) => d.temPendencia);
    return diasComBusca;
  }, [diasComBusca, filtroSubTab]);

  const defaultOpen = diasFiltrados[0]?.id;

  return (
    <div className="space-y-4">
      <Card>
        <FinanceiroTransacaoTableCardHeader
          period={period}
          onPeriodChange={setPeriod}
          searchQuery={searchQuery}
          onSearchChange={setSearchQuery}
          searchPlaceholder="Buscar descrição (sistema ou banco)…"
          loading={loading}
          refetch={refresh}
          toolbarMode="refresh-only"
        />
        <CardContent className="space-y-4 pt-0">
      <div className="flex flex-wrap items-center gap-2">
        <Tabs value={filtroSubTab} onValueChange={(v) => setFiltroSubTab(v as typeof filtroSubTab)}>
          <TabsList variant="line" size="sm" className="mb-0 w-full min-w-0 flex-1 justify-start sm:w-auto">
            <TabsTrigger value="tudo" className="gap-1.5">
              Tudo
              <span className="text-muted-foreground tabular-nums">({contagem.total})</span>
            </TabsTrigger>
            <TabsTrigger value="sem_pendencias" className="gap-1.5">
              Dias sem pendências
              <span className="text-muted-foreground tabular-nums">({contagem.sem})</span>
            </TabsTrigger>
            <TabsTrigger value="com_pendencias" className="gap-1.5">
              Dias com pendências
              <span className="text-muted-foreground tabular-nums">({contagem.com})</span>
            </TabsTrigger>
          </TabsList>
        </Tabs>
        <InfoHintButton label="Ajuda: filtros por dia">{TT.filtrosDias}</InfoHintButton>
      </div>

      <div className="flex flex-wrap items-start gap-2 text-xs text-muted-foreground">
        <p className="min-w-0 flex-1">
          Conta <span className="font-mono text-foreground">{entidadeId ?? '—'}</span> — apenas lançamentos já pareados com o
          extrato (conciliados), filtrados pelo período acima.
        </p>
        <InfoHintButton label="Ajuda: o que aparece nesta lista">
          <p>
            Lista apenas movimentações do extrato <strong>já vinculadas</strong> a lançamentos financeiros. Use o período e a
            busca para localizar lançamentos; os filtros de dia ajudam a focar onde ainda há pendências de conciliação.
          </p>
        </InfoHintButton>
      </div>

      {error && (
        <div className="rounded-lg border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm text-destructive">
          {error}
        </div>
      )}

      {loading && (
        <div className="flex items-center justify-center gap-2 rounded-xl border border-dashed border-border py-16 text-muted-foreground">
          <Loader2 className="size-5 animate-spin" aria-hidden />
          <span>Carregando movimentações conciliadas…</span>
        </div>
      )}

      {!loading && !error && diasFiltrados.length > 0 && (
      <Accordion
        key={`${filtroSubTab}-${defaultOpen ?? 'none'}`}
        variant="outline"
        type="single"
        collapsible
        defaultValue={defaultOpen}
        className="space-y-3"
      >
        {diasFiltrados.map((dia) => {
          const totalSistemaLinhas = dia.linhas.reduce((s, l) => s + l.valorSistemaCents, 0);
          const totalBancoLinhas = dia.linhas.reduce((s, l) => s + l.valorBancoCents, 0);
          const diferencaRodape =
            dia.linhas.length > 0 ? totalSistemaLinhas - totalBancoLinhas : dia.diferencaCents;

          return (
            <AccordionItem key={dia.id} value={dia.id}>
              <AccordionTrigger className="rounded-lg px-4 py-4 hover:bg-muted/40 hover:no-underline [&>svg]:shrink-0">
                <div className="flex w-full min-w-0 flex-col gap-3 text-left lg:flex-row lg:items-stretch lg:gap-6">
                  <div className="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-start sm:gap-3">
                    <div className="flex min-w-0 flex-shrink-0 flex-col gap-0.5">
                      <div className="flex flex-wrap items-center gap-2">
                        <span className="text-base font-semibold tracking-tight">{dia.dataLabel}</span>
                        {dia.temPendencia && (
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <span className="inline-flex cursor-help">
                                <Badge
                                  variant="outline"
                                  className="h-5 gap-1 border-amber-500/40 bg-amber-500/10 px-1.5 text-[10px] font-medium uppercase text-amber-800 dark:text-amber-200"
                                >
                                  <AlertCircle className="size-3" aria-hidden />
                                  Pendência
                                </Badge>
                              </span>
                            </TooltipTrigger>
                            <TooltipContent side="top" className="max-w-xs">
                              <p className="text-xs leading-relaxed">{TT.pendencia}</p>
                            </TooltipContent>
                          </Tooltip>
                        )}
                      </div>
                      <span className="text-xs text-muted-foreground">{dia.diaSemana}</span>
                    </div>
                    {dia.diferencaCents !== 0 && (
                      <div className="flex min-w-0 flex-1 items-center gap-1.5 lg:max-w-md">
                        <p
                          className="min-w-0 truncate text-sm text-amber-600 dark:text-amber-500"
                          title={`Saldo com diferença de ${formatBRLSigned(dia.diferencaCents)}`}
                        >
                          Saldo com diferença de {formatBRLSigned(dia.diferencaCents)}
                        </p>
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <span
                              role="button"
                              tabIndex={0}
                              className={cn(
                                buttonVariants({ variant: 'outline', size: 'icon' }),
                                'size-7 shrink-0 text-amber-700 dark:text-amber-400',
                              )}
                              aria-label="O que é saldo com diferença?"
                              onClick={stopAccordionBubble}
                              onKeyDown={(e) => {
                                if (e.key === ' ') e.preventDefault();
                                if (e.key === 'Enter' || e.key === ' ') stopAccordionBubble(e);
                              }}
                            >
                              <HelpCircle className="size-3.5" aria-hidden />
                            </span>
                          </TooltipTrigger>
                          <TooltipContent side="top" className="max-w-xs">
                            <p className="text-xs leading-relaxed">{TT.diferencaSaldo}</p>
                          </TooltipContent>
                        </Tooltip>
                      </div>
                    )}
                  </div>
                  <div className="flex flex-shrink-0 flex-wrap gap-2 sm:justify-end lg:ml-auto">
                    <div className="rounded-md bg-muted/40 px-3 py-1.5 text-right ring-1 ring-border/60">
                      <div className="flex items-center justify-end gap-1">
                        <div className="text-[10px] font-medium uppercase tracking-wide text-muted-foreground">Sistema</div>
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <span
                              role="button"
                              tabIndex={0}
                              className="inline-flex rounded p-0.5 text-muted-foreground hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                              aria-label="Ajuda: total Sistema"
                              onClick={stopAccordionBubble}
                              onKeyDown={(e) => {
                                if (e.key === ' ') e.preventDefault();
                                if (e.key === 'Enter' || e.key === ' ') stopAccordionBubble(e);
                              }}
                            >
                              <HelpCircle className="size-3" aria-hidden />
                            </span>
                          </TooltipTrigger>
                          <TooltipContent side="top" className="max-w-xs">
                            <p className="text-xs leading-relaxed">{TT.sistema}</p>
                          </TooltipContent>
                        </Tooltip>
                      </div>
                      <div className="text-sm font-semibold tabular-nums">{formatBRL(dia.saldoSistemaCents)}</div>
                    </div>
                    <div className="rounded-md bg-muted/40 px-3 py-1.5 text-right ring-1 ring-border/60">
                      <div className="flex items-center justify-end gap-1">
                        <div className="text-[10px] font-medium uppercase tracking-wide text-muted-foreground">Banco</div>
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <span
                              role="button"
                              tabIndex={0}
                              className="inline-flex rounded p-0.5 text-muted-foreground hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                              aria-label="Ajuda: total Banco"
                              onClick={stopAccordionBubble}
                              onKeyDown={(e) => {
                                if (e.key === ' ') {
                                  e.preventDefault();
                                }
                                if (e.key === 'Enter' || e.key === ' ') stopAccordionBubble(e);
                              }}
                            >
                              <HelpCircle className="size-3" aria-hidden />
                            </span>
                          </TooltipTrigger>
                          <TooltipContent side="top" className="max-w-xs">
                            <p className="text-xs leading-relaxed">{TT.banco}</p>
                          </TooltipContent>
                        </Tooltip>
                      </div>
                      <div className="text-sm font-semibold tabular-nums">{formatBRL(dia.saldoBancoCents)}</div>
                    </div>
                  </div>
                </div>
              </AccordionTrigger>
              <AccordionContent className="pb-0">
                <div className="border-t border-border px-2 pb-4 pt-0 sm:px-4">
                  {dia.linhas.length === 0 ? (
                    <div className="space-y-3 py-8 text-center">
                      <p className="text-sm text-muted-foreground">
                        Saldo divergente neste dia, sem lançamentos pareados para exibir na grade.
                      </p>
                      {dia.diferencaCents !== 0 && (
                        <p className="text-base font-semibold tabular-nums text-amber-600 dark:text-amber-500">
                          Diferença do dia: {formatBRLSigned(dia.diferencaCents)}
                        </p>
                      )}
                    </div>
                  ) : (
                    <Table className="min-w-[880px] caption-top">
                      <TableCaption className="border-b border-border bg-muted/20 px-3 py-2.5 text-left text-xs leading-relaxed text-muted-foreground">
                        {TT.captionMovimentacoes}
                      </TableCaption>
                      <TableHeader>
                        <TableRow className="border-b-0 hover:bg-transparent">
                          <TableHead
                            colSpan={2}
                            className="border-b border-border bg-primary/[0.06] text-center text-xs font-semibold uppercase tracking-wide text-foreground dark:bg-primary/10"
                          >
                            Sistema (lançamento)
                          </TableHead>
                          <TableHead
                            colSpan={2}
                            className="border-b border-l border-border bg-muted/50 text-center text-xs font-semibold uppercase tracking-wide text-foreground"
                          >
                            Banco (extrato)
                          </TableHead>
                          <TableHead
                            rowSpan={2}
                            className="border-b border-l border-border bg-muted/30 align-middle text-center text-xs font-semibold"
                          >
                            <span className="inline-flex flex-col items-center gap-0.5">
                              <span>Diferença</span>
                              <span className="max-w-[7rem] text-[10px] font-normal normal-case leading-tight text-muted-foreground">
                                (linha)
                              </span>
                              <Tooltip>
                                <TooltipTrigger asChild>
                                  <button
                                    type="button"
                                    className="mt-0.5 inline-flex rounded p-0.5 text-muted-foreground hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    aria-label="Ajuda: coluna Diferença"
                                  >
                                    <HelpCircle className="size-3" aria-hidden />
                                  </button>
                                </TooltipTrigger>
                                <TooltipContent side="top" className="max-w-xs">
                                  <p className="text-xs leading-relaxed">{TT.diferencaLinha}</p>
                                </TooltipContent>
                              </Tooltip>
                            </span>
                          </TableHead>
                          <TableHead rowSpan={2} className="align-bottom text-xs font-semibold">
                            <span className="inline-flex items-center gap-1">
                              Conciliação
                              <Tooltip>
                                <TooltipTrigger asChild>
                                  <button
                                    type="button"
                                    className="inline-flex rounded p-0.5 text-muted-foreground hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    aria-label="Ajuda: coluna Conciliação"
                                  >
                                    <HelpCircle className="size-3.5" aria-hidden />
                                  </button>
                                </TooltipTrigger>
                                <TooltipContent side="top" className="max-w-xs">
                                  <p className="text-xs leading-relaxed">{TT.colConciliacao}</p>
                                </TooltipContent>
                              </Tooltip>
                            </span>
                          </TableHead>
                          <TableHead rowSpan={2} className="align-bottom text-right text-xs font-semibold">
                            Ações
                          </TableHead>
                        </TableRow>
                        <TableRow className="hover:bg-transparent">
                          <TableHead className="min-w-[170px] bg-primary/[0.04] pt-2 text-xs font-medium dark:bg-primary/[0.07]">
                            Descrição
                          </TableHead>
                          <TableHead className="w-[108px] bg-primary/[0.04] pt-2 text-right text-xs font-medium dark:bg-primary/[0.07]">
                            Valor conciliado
                          </TableHead>
                          <TableHead className="min-w-[170px] border-l border-border bg-muted/30 pt-2 text-xs font-medium">
                            Descrição extrato
                          </TableHead>
                          <TableHead className="w-[108px] bg-muted/30 pt-2 text-right text-xs font-medium">
                            Valor no extrato
                            <span className="mt-0.5 block text-[10px] font-normal text-muted-foreground">
                              (rateio)
                            </span>
                          </TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        {dia.linhas.map((linha, idx) => {
                          const diffLinha = linha.valorSistemaCents - linha.valorBancoCents;
                          return (
                          <TableRow
                            key={linha.id}
                            className={cn(idx % 2 === 1 && 'bg-muted/20', '[&:has(td):hover]:bg-muted/40')}
                          >
                            <TableCell className="bg-primary/[0.03] dark:bg-primary/[0.05]">
                              <div className="font-medium">{linha.descricaoSistema}</div>
                              {linha.subcategoria ? (
                                <div className="text-xs text-muted-foreground">{linha.subcategoria}</div>
                              ) : null}
                            </TableCell>
                            <TableCell
                              className={cn(
                                'bg-primary/[0.03] text-right tabular-nums font-medium dark:bg-primary/[0.05]',
                                linha.valorSistemaCents < 0 ? 'text-destructive' : 'text-emerald-600',
                              )}
                            >
                              {formatBRLSigned(linha.valorSistemaCents)}
                            </TableCell>
                            <TableCell className="border-l border-border text-muted-foreground">
                              {linha.descricaoBanco}
                            </TableCell>
                            <TableCell
                              className={cn(
                                'text-right tabular-nums',
                                linha.valorBancoCents < 0 ? 'text-destructive' : 'text-emerald-600',
                              )}
                            >
                              {formatBRLSigned(linha.valorBancoCents)}
                            </TableCell>
                            <TableCell
                              className={cn(
                                'border-l border-border text-center tabular-nums text-sm font-medium',
                                diffLinha === 0
                                  ? 'text-muted-foreground'
                                  : diffLinha > 0
                                    ? 'text-amber-700 dark:text-amber-500'
                                    : 'text-sky-700 dark:text-sky-400',
                              )}
                            >
                              {diffLinha === 0 ? (
                                <span className="text-muted-foreground">—</span>
                              ) : (
                                formatBRLSigned(diffLinha)
                              )}
                            </TableCell>
                            <TableCell>{badgeConciliacao(linha.conciliacao)}</TableCell>
                            <TableCell className="text-right">
                              <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                  <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    className="h-8 gap-1 px-2 sm:px-3"
                                    aria-label="Ações"
                                    disabled={linha.bankStatementId <= 0}
                                  >
                                    <MoreHorizontal className="size-4 sm:hidden" aria-hidden />
                                    <span className="hidden sm:inline">Ações</span>
                                    <ChevronDown className="hidden size-3.5 opacity-60 sm:inline" aria-hidden />
                                  </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" className="w-52">
                                  <DropdownMenuItem
                                    className="gap-2"
                                    disabled={linha.bankStatementId <= 0}
                                    onSelect={() => {
                                      setDetalhesBankStatementId(linha.bankStatementId);
                                      setDetalhesSheetOpen(true);
                                    }}
                                  >
                                    <Eye className="size-4 shrink-0" aria-hidden />
                                    Ver detalhes
                                  </DropdownMenuItem>
                                  <DropdownMenuItem
                                    className="gap-2 text-destructive focus:text-destructive"
                                    disabled={linha.bankStatementId <= 0}
                                    onSelect={() => setDesfazerInfo({ id: linha.bankStatementId, descricao: linha.descricaoBanco })}
                                  >
                                    <Undo2 className="size-4 shrink-0" aria-hidden />
                                    Desfazer conciliação
                                  </DropdownMenuItem>
                                </DropdownMenuContent>
                              </DropdownMenu>
                            </TableCell>
                          </TableRow>
                          );
                        })}
                      </TableBody>
                      <TableFooter>
                        <TableRow className="bg-muted/50 hover:bg-muted/50">
                          <TableCell className="bg-primary/[0.04] font-medium dark:bg-primary/[0.06]">
                            Totais do dia — {dia.dataLabel}
                          </TableCell>
                          <TableCell className="bg-primary/[0.04] text-right text-base font-semibold tabular-nums dark:bg-primary/[0.06]">
                            {formatBRL(totalSistemaLinhas)}
                          </TableCell>
                          <TableCell className="border-l border-border text-muted-foreground" />
                          <TableCell className="text-right text-base font-semibold tabular-nums">
                            {formatBRL(totalBancoLinhas)}
                          </TableCell>
                          <TableCell className="border-l border-border text-center">
                            <span className="mr-1 inline-flex items-center gap-1 text-xs font-medium text-muted-foreground">
                              Σ dif.
                              <Tooltip>
                                <TooltipTrigger asChild>
                                  <button
                                    type="button"
                                    className="inline-flex rounded p-0.5 text-muted-foreground hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    aria-label="Ajuda: total diferença do dia"
                                  >
                                    <HelpCircle className="size-3" aria-hidden />
                                  </button>
                                </TooltipTrigger>
                                <TooltipContent side="top" className="max-w-xs">
                                  <p className="text-xs leading-relaxed">{TT.diferencaSaldo}</p>
                                </TooltipContent>
                              </Tooltip>
                            </span>
                            <span
                              className={cn(
                                'block text-base font-semibold tabular-nums',
                                diferencaRodape !== 0 ? 'text-amber-600' : 'text-foreground',
                              )}
                            >
                              {formatBRLSigned(diferencaRodape)}
                            </span>
                          </TableCell>
                          <TableCell className="text-muted-foreground" />
                          <TableCell />
                        </TableRow>
                      </TableFooter>
                    </Table>
                  )}
                </div>
              </AccordionContent>
            </AccordionItem>
          );
        })}
      </Accordion>
      )}

      {!loading && !error && diasBase.length === 0 && (
        <div className="rounded-xl border border-dashed border-border py-12 text-center text-sm text-muted-foreground">
          Nenhuma movimentação conciliada no período selecionado para esta conta.
        </div>
      )}

      {!loading &&
        !error &&
        diasBase.length > 0 &&
        diasComBusca.length === 0 &&
        searchQuery.trim().length > 0 && (
          <div className="rounded-xl border border-dashed border-border py-12 text-center text-sm text-muted-foreground">
            Nenhum resultado para a busca. Tente outro termo ou limpe o campo de busca.
          </div>
        )}

      {!loading && !error && diasComBusca.length > 0 && diasFiltrados.length === 0 && (
        <div className="rounded-xl border border-dashed border-border py-12 text-center text-sm text-muted-foreground">
          Nenhum dia neste filtro.
        </div>
      )}
        </CardContent>
      </Card>

      <ConciliacaoDetalhesSheet
        open={detalhesSheetOpen}
        onOpenChange={(o) => {
          setDetalhesSheetOpen(o);
          if (!o) setDetalhesBankStatementId(null);
        }}
        bankStatementId={detalhesBankStatementId}
      />

      <DesfazerConciliacaoDialog
        open={desfazerInfo !== null}
        onOpenChange={(o) => { if (!o) setDesfazerInfo(null); }}
        bankStatementId={desfazerInfo?.id ?? null}
        descricaoBanco={desfazerInfo?.descricao ?? ''}
        onSuccess={async () => { await refresh(); }}
      />
    </div>
  );
}
