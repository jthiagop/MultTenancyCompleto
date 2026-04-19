/**
 * Elementos compartilhados entre ReceitasTable, DespesasTable e ExtratoTable.
 * Centraliza helpers, tipos, células de coluna e o menu de ações com dialog de escopo.
 */
import { useState } from 'react';
import { Row } from '@tanstack/react-table';
import {
  ArrowLeftRight,
  Banknote,
  ChevronDown,
  Coins,
  Eye,
  GitFork,
  Receipt,
  Landmark,
  Layers,
  Pencil,
  RefreshCw,
  Repeat2,
  RotateCcw,
  ScanLine,
  Send,
  Settings2,
  Tag,
  Trash2,
} from 'lucide-react';
import { financeiroToolbarSoftBlueClass } from '@/lib/financeiro-toolbar-accent';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { CardToolbar } from '@/components/ui/card';
import { DataGridColumnVisibility } from '@/components/ui/data-grid-column-visibility';
import { useDataGrid } from '@/components/ui/data-grid';
import { type ITransacao } from '@/hooks/useTransacoes';
import { useAppData } from '@/hooks/useAppData';
import { notify } from '@/lib/notify';
import { DeleteTransacaoDialog, type DeleteScope, type RateioScope } from '@/pages/financeiro/components/delete-transacao-dialog';
import type { TipoLancamento } from '@/pages/financeiro/banco/components/lancamento-drawer';

// ── Tipos públicos ────────────────────────────────────────────────────────────

export type SituacaoColor = 'warning' | 'destructive' | 'secondary' | 'success';
export type EditScope = 'single' | 'all';

export interface EditOptions {
  /** Obrigatório no extrato; opcional nas tabelas de receita/despesa. */
  tipo?: TipoLancamento;
  /** Escopo da edição: apenas este registro ou todos os vinculados. */
  scope?: EditScope;
}

/** Assinatura unificada para o callback onEdit das três tabelas. */
export type OnEditTransacao = (id: string, options?: EditOptions) => void;

/** Callback para abrir o sheet de pagamento. */
export type OnInformarPagamento = (id: string) => void;

/** Callback para abrir o painel de detalhes da transação. */
export type OnOpenTransacaoDetalhes = (id: string) => void;

/** Callback para gerar recibo ou abrir PDF (conforme existência do recibo). */
export type OnOpenRecibo = (id: string) => void;

// ── Helpers ───────────────────────────────────────────────────────────────────

export const fmtCurrency = (v: number) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);

export const SITUACAO_VARIANT: Record<string, SituacaoColor> = {
  em_aberto:    'warning',
  atrasado:     'destructive',
  previsto:     'secondary',
  pago_parcial: 'secondary',
  pago:         'success',
  recebido:     'success',
};

// ── DescricaoCell ─────────────────────────────────────────────────────────────

/** Célula de descrição com badges de recorrência, parcelamento, transferência e rateio. */
export function DescricaoCell({ row }: { row: Row<ITransacao> }) {
  const {
    descricao,
    parceiro,
    is_parcelado,
    parcela_info,
    is_recorrente,
    recorrencia_info,
    is_transferencia,
    is_rateio_origem,
    is_rateio_filho,
  } = row.original;
  return (
    <div className="flex flex-col gap-0.5 min-w-0">
      <div className="flex items-center gap-1.5 min-w-0">
        {is_recorrente && (
          <Tooltip>
            <TooltipTrigger asChild>
              <span className="inline-flex items-center gap-1 shrink-0 cursor-default">
                <Repeat2 className="size-3.5 text-sky-500" />
                {recorrencia_info && (
                  <span className="text-[0.7rem] font-medium text-sky-500">{recorrencia_info}</span>
                )}
              </span>
            </TooltipTrigger>
            <TooltipContent>Recorrente{recorrencia_info ? ` · ${recorrencia_info}` : ''}</TooltipContent>
          </Tooltip>
        )}
        {is_parcelado && !is_recorrente && (
          <Tooltip>
            <TooltipTrigger asChild>
              <span className="inline-flex items-center gap-1 shrink-0 cursor-default">
                <Layers className="size-3.5 text-violet-500" />
                {parcela_info && (
                  <span className="text-[0.7rem] font-medium text-violet-500">{parcela_info}</span>
                )}
              </span>
            </TooltipTrigger>
            <TooltipContent>Parcelado{parcela_info ? ` · ${parcela_info}` : ''}</TooltipContent>
          </Tooltip>
        )}
        {is_transferencia && !is_parcelado && !is_recorrente && (
          <Tooltip>
            <TooltipTrigger asChild>
              <span className="inline-flex shrink-0 cursor-default">
                <ArrowLeftRight className="size-3.5 text-amber-500" />
              </span>
            </TooltipTrigger>
            <TooltipContent>Transferência entre contas</TooltipContent>
          </Tooltip>
        )}
        {is_rateio_origem && (
          <Tooltip>
            <TooltipTrigger asChild>
              <span className="inline-flex shrink-0 cursor-default">
                <GitFork className="size-3.5 text-orange-500" />
              </span>
            </TooltipTrigger>
            <TooltipContent>Rateio — origem (lançamento rateado para filiais)</TooltipContent>
          </Tooltip>
        )}
        {is_rateio_filho && !is_rateio_origem && (
          <Tooltip>
            <TooltipTrigger asChild>
              <span className="inline-flex shrink-0 cursor-default">
                <GitFork className="size-3.5 rotate-180 text-orange-400" />
              </span>
            </TooltipTrigger>
            <TooltipContent>Rateio — recebido via rateio entre fraternidades</TooltipContent>
          </Tooltip>
        )}
        <span className="font-medium truncate">{descricao}</span>
      </div>
      {parceiro && (
        <span className="text-[0.7rem] text-muted-foreground truncate pl-0.5">{parceiro}</span>
      )}
    </div>
  );
}

// ── OrigemCell ────────────────────────────────────────────────────────────────

type OrigemConfig = { icon: React.ReactNode; label: string; className: string };

function resolveOrigem(origem: string): OrigemConfig {
  const n = origem.toLowerCase();
  if (n.startsWith('banco')) {
    return {
      icon: <Landmark className="size-3.5 shrink-0 text-blue-500" />,
      label: origem,
      className: 'text-blue-600 dark:text-blue-400',
    };
  }
  if (n === 'caixa') {
    return {
      icon: <Coins className="size-3.5 shrink-0 text-amber-500" />,
      label: 'Caixa',
      className: 'text-amber-600 dark:text-amber-400',
    };
  }
  if (n === 'repasse') {
    return {
      icon: <Send className="size-3.5 shrink-0 text-purple-500" />,
      label: 'Repasse',
      className: 'text-purple-600 dark:text-purple-400',
    };
  }
  if (n === 'rateio') {
    return {
      icon: <GitFork className="size-3.5 shrink-0 text-orange-500" />,
      label: 'Rateio',
      className: 'text-orange-600 dark:text-orange-400',
    };
  }
  if (n === 'conciliacao_bancaria') {
    return {
      icon: <ScanLine className="size-3.5 shrink-0 text-teal-500" />,
      label: 'Conciliação',
      className: 'text-teal-600 dark:text-teal-400',
    };
  }
  return {
    icon: <Tag className="size-3.5 shrink-0 text-muted-foreground" />,
    label: origem || '—',
    className: 'text-muted-foreground',
  };
}

/** Célula de origem com ícone colorido por tipo de lançamento. */
export function OrigemCell({
  origem,
  origemNome,
  origemAgencia,
  origemConta,
}: {
  origem: string;
  origemNome?: string | null;
  origemAgencia?: string | null;
  origemConta?: string | null;
}) {
  const { icon, label, className } = resolveOrigem(origem ?? '');
  const n = (origem ?? '').toLowerCase();
  const isBanco = n.startsWith('banco');
  const isCaixa = n === 'caixa';
  const displayLabel = (isBanco || isCaixa) && origemNome ? origemNome : label;

  const subtexto = isBanco
    ? [origemAgencia ? `Ag. ${origemAgencia}` : null, origemConta ? `Cc. ${origemConta}` : null]
        .filter(Boolean)
        .join(' · ')
    : null;

  return (
    <div className="flex items-center gap-1.5 min-w-0">
      {icon}
      <div className="flex flex-col min-w-0">
        <span className={cn('text-sm truncate leading-tight', className)}>{displayLabel}</span>
        {subtexto && (
          <span className="text-[0.68rem] text-muted-foreground truncate leading-tight">
            {subtexto}
          </span>
        )}
      </div>
    </div>
  );
}

// ── Dialog de escopo de edição ────────────────────────────────────────────────

interface EditScopeDialogProps {
  open: boolean;
  onOpenChange: (v: boolean) => void;
  tipo: 'parcela' | 'recorrencia';
  onConfirm: (scope: EditScope) => void;
}

function EditScopeDialog({ open, onOpenChange, tipo, onConfirm }: EditScopeDialogProps) {
  const isParcela = tipo === 'parcela';

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-sm !top-[15%] !translate-y-0">
        <DialogHeader>
          <DialogTitle>{isParcela ? 'Editar parcela' : 'Editar recorrência'}</DialogTitle>
          <DialogDescription>
            {isParcela
              ? 'Como deseja editar este lançamento parcelado?'
              : 'Como deseja editar este lançamento recorrente?'}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter className="flex-row gap-2 sm:justify-start">
          <Button
            variant="outline"
            className="flex-1"
            onClick={() => { onOpenChange(false); onConfirm('single'); }}
          >
            {isParcela ? 'Apenas esta parcela' : 'Apenas este lançamento'}
          </Button>
          <Button
            className="flex-1 bg-blue-600 hover:bg-blue-700 text-white border-0"
            onClick={() => { onOpenChange(false); onConfirm('all'); }}
          >
            {isParcela ? 'Todas as parcelas' : 'Todos os lançamentos'}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

// ── Dialog de confirmação de inversão de tipo ─────────────────────────────────

interface InverterTipoDialogProps {
  open: boolean;
  onOpenChange: (v: boolean) => void;
  label: string;
  onConfirm: () => void;
  loading: boolean;
}

function InverterTipoDialog({ open, onOpenChange, label, onConfirm, loading }: InverterTipoDialogProps) {
  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-sm !top-[15%] !translate-y-0">
        <DialogHeader>
          <DialogTitle>{label}</DialogTitle>
          <DialogDescription>
            O tipo será invertido (Receita ↔ Despesa). Parcelas filhas também serão convertidas e o saldo da conta será recalculado.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter className="flex-row gap-2 sm:justify-start">
          <Button
            variant="outline"
            className="flex-1"
            onClick={() => onOpenChange(false)}
            disabled={loading}
          >
            Cancelar
          </Button>
          <Button
            className="flex-1 bg-violet-600 hover:bg-violet-100 text-white border-0"
            onClick={onConfirm}
            disabled={loading}
          >
            {loading ? 'Convertendo...' : 'Confirmar'}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

// ── TransacaoActionsCell ──────────────────────────────────────────────────────

/**
 * Célula de ações compartilhada.
 *
 * - Lançamento simples  → "Editar Lançamento"
 * - Parcelado           → "Editar Parcela" + dialog perguntando escopo
 * - Recorrente          → "Editar Recorrência" + dialog perguntando escopo
 *
 * Anexos já salvos aparecem na aba **Anexos** do `LancamentoDrawer` (links clicáveis), não neste menu.
 */
export function TransacaoActionsCell({
  row,
  onEdit,
  onInformarPagamento,
  onOpenDetalhes,
  onOpenRecibo,
  onDeleted,
}: {
  row: Row<ITransacao>;
  onEdit: OnEditTransacao;
  onInformarPagamento?: OnInformarPagamento;
  onOpenDetalhes?: OnOpenTransacaoDetalhes;
  onOpenRecibo?: OnOpenRecibo;
  onDeleted?: () => void;
}) {
  const [dialogOpen, setDialogOpen] = useState(false);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [deleteLoading, setDeleteLoading] = useState(false);
  const [invertDialogOpen, setInvertDialogOpen] = useState(false);
  const [invertLoading, setInvertLoading] = useState(false);
  const [openLoading, setOpenLoading] = useState(false);
  const { csrfToken } = useAppData();
  const { is_parcelado, is_recorrente, situacao } = row.original;

  const podePagar = ['em_aberto', 'atrasado', 'previsto', 'pago_parcial'].includes(situacao);
  const podeDefinirAberto = ['pago', 'recebido'].includes(situacao);

  const labelInverter = row.original.tipo === 'entrada' ? 'Converter para Despesa' : 'Converter para Receita';

  const scopeTipo: 'parcela' | 'recorrencia' | null =
    is_parcelado && !is_recorrente ? 'parcela' : is_recorrente ? 'recorrencia' : null;

  const editLabel =
    scopeTipo === 'parcela'
      ? 'Editar Parcela'
      : scopeTipo === 'recorrencia'
        ? 'Editar Recorrência'
        : 'Editar Lançamento';

  const tipo: TipoLancamento = row.original.tipo === 'entrada' ? 'receita' : 'despesa';

  function handleEditClick() {
    if (scopeTipo) {
      setDialogOpen(true);
    } else {
      onEdit(String(row.original.id), { tipo });
    }
  }

  function handleScopeConfirm(scope: EditScope) {
    onEdit(String(row.original.id), { tipo, scope });
  }

  async function handleDefinirAberto() {
    if (!csrfToken) return;
    setOpenLoading(true);
    try {
      const res = await fetch('/banco/mark-as-open', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ id: row.original.id }),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro ao reverter', json.message ?? '');
        return;
      }
      notify.success('Revertido!', json.message ?? 'Transação marcada como Em Aberto.');
      onDeleted?.();
    } catch {
      notify.networkError();
    } finally {
      setOpenLoading(false);
    }
  }

  async function handleInverterTipo() {
    if (!csrfToken) return;
    setInvertLoading(true);
    try {
      const res = await fetch('/banco/reverse-type', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ id: row.original.id }),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro ao converter', json.message ?? '');
        return;
      }
      notify.success('Tipo convertido!', json.message ?? 'Tipo alterado com sucesso.');
      setInvertDialogOpen(false);
      onDeleted?.();
    } catch {
      notify.networkError();
    } finally {
      setInvertLoading(false);
    }
  }

  async function handleDelete(scope: DeleteScope, rateioScope: RateioScope) {
    if (!csrfToken) return;
    setDeleteLoading(true);
    try {
      const res = await fetch(`/transacoes-financeiras/${row.original.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ scope, rateio_scope: rateioScope }),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro ao excluir', json.message ?? '');
        return;
      }
      notify.success('Excluído!', json.message ?? 'Lançamento removido com sucesso.');
      setDeleteDialogOpen(false);
      onDeleted?.();
    } catch {
      notify.networkError();
    } finally {
      setDeleteLoading(false);
    }
  }

  return (
    <>
      <div data-row-click-ignore="true" className="inline-flex" onClick={(e) => e.stopPropagation()}>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button size="sm" variant="outline" className="h-7 gap-1.5 px-2.5 text-xs border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-400 dark:hover:bg-blue-900/50">
              Ação
              <ChevronDown className="size-3.5" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent side="bottom" align="end">
            <DropdownMenuItem
              onClick={() => {
                onOpenDetalhes?.(String(row.original.id));
              }}
            >
              <Eye className="size-4 text-muted-foreground" />
              Ver detalhes
            </DropdownMenuItem>
            {onOpenRecibo && (
              <DropdownMenuItem
                onClick={() => {
                  onOpenRecibo(String(row.original.id));
                }}
              >
                <Receipt className="size-4 text-slate-400" />
                Recibo
              </DropdownMenuItem>
            )}
          {podePagar && onInformarPagamento && (
            <DropdownMenuItem onClick={() => onInformarPagamento(String(row.original.id))}>
              <Banknote className="size-4 text-green-600" />
              Informar Pagamento
            </DropdownMenuItem>
          )}
          <DropdownMenuItem onClick={handleEditClick}>
            <Pencil className="size-4 text-blue-500" />
            {editLabel}
          </DropdownMenuItem>
          {podeDefinirAberto && (
            <DropdownMenuItem onClick={handleDefinirAberto} disabled={openLoading}>
              <RotateCcw className="size-4 text-orange-500" />
              Definir como Em Aberto
            </DropdownMenuItem>
          )}
          <DropdownMenuItem onClick={() => setInvertDialogOpen(true)}>
            <ArrowLeftRight className="size-4 text-violet-500" />
            {labelInverter}
          </DropdownMenuItem>
          <DropdownMenuSeparator />
            <DropdownMenuItem variant="destructive" onClick={() => setDeleteDialogOpen(true)}>
              <Trash2 className="size-4" />
              Excluir
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>

        {/* Dialogs renderizados DENTRO do wrapper stopPropagation para evitar que
            eventos sintéticos do React bublam pela árvore virtual até o handler
            da linha e abram o sheet de detalhes acidentalmente. */}
        {scopeTipo && (
          <EditScopeDialog
            open={dialogOpen}
            onOpenChange={setDialogOpen}
            tipo={scopeTipo}
            onConfirm={handleScopeConfirm}
          />
        )}

        <InverterTipoDialog
          open={invertDialogOpen}
          onOpenChange={setInvertDialogOpen}
          label={labelInverter}
          onConfirm={handleInverterTipo}
          loading={invertLoading}
        />

        <DeleteTransacaoDialog
          open={deleteDialogOpen}
          onOpenChange={setDeleteDialogOpen}
          transacao={row.original}
          onConfirm={handleDelete}
          loading={deleteLoading}
        />
      </div>
    </>
  );
}

// ── TableToolbarBase ──────────────────────────────────────────────────────────

/** Barra de ferramentas padrão com botão de recarregar e visibilidade de colunas. */
export function TableToolbarBase({ loading, refetch }: { loading: boolean; refetch: () => void }) {
  const { table } = useDataGrid();
  return (
    <CardToolbar>
      <Button
        variant="outline"
        size="sm"
        className={cn(financeiroToolbarSoftBlueClass, 'h-10 min-w-10 px-3')}
        onClick={refetch}
        disabled={loading}
        aria-label="Atualizar"
      >
        <RefreshCw className={`size-4 ${loading ? 'animate-spin' : ''}`} />
      </Button>
      <DataGridColumnVisibility
        table={table}
        trigger={
          <Button variant="outline" size="sm" className={cn(financeiroToolbarSoftBlueClass, 'h-10 min-w-36 gap-2 px-4')}>
            <Settings2 className="size-4" />
            Colunas
          </Button>
        }
      />
    </CardToolbar>
  );
}
