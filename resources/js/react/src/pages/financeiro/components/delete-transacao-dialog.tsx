import { useState } from 'react';
import { Loader2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { type ITransacao } from '@/hooks/useTransacoes';

// ── Tipos ─────────────────────────────────────────────────────────────────────

export type DeleteScope = 'single' | 'all';

/**
 * Define o que acontece com os registros rateio intercompany ao excluir:
 * - parent_only : exclui apenas o pai (filhos tornam-se independentes)
 * - all         : exclui pai e todos os filhos em aberto
 * - children_only: exclui apenas os filhos em aberto, mantém o pai
 */
export type RateioScope = 'parent_only' | 'all' | 'children_only';

export interface DeleteTransacaoDialogProps {
  open: boolean;
  onOpenChange: (v: boolean) => void;
  transacao: ITransacao | null;
  /** Chamado com o escopo de parcelas/recorrência e o escopo de rateio intercompany. */
  onConfirm: (scope: DeleteScope, rateioScope: RateioScope) => void;
  loading: boolean;
}

// ── Componente ────────────────────────────────────────────────────────────────

export function DeleteTransacaoDialog({
  open,
  onOpenChange,
  transacao,
  onConfirm,
  loading,
}: DeleteTransacaoDialogProps) {
  const [scope, setScope] = useState<DeleteScope>('single');
  const [rateioScope, setRateioScope] = useState<RateioScope>('parent_only');

  if (!transacao) return null;

  const { is_parcelado, is_recorrente, is_rateio_origem, is_rateio_filho, parcela_info, recorrencia_info } =
    transacao;

  const hasScope = is_parcelado || is_recorrente;

  function handleConfirm(chosenScope: DeleteScope, chosenRateioScope: RateioScope) {
    onConfirm(chosenScope, chosenRateioScope);
  }

  // ── Título e descrição dinâmicos ────────────────────────────────────────────
  const title = hasScope
    ? is_parcelado
      ? 'Excluir parcela'
      : 'Excluir lançamento recorrente'
    : 'Excluir lançamento';

  const description = hasScope
    ? is_parcelado
      ? `Parcela ${parcela_info ?? ''} · Escolha como excluir este lançamento parcelado.`
      : `Recorrência ${recorrencia_info ?? ''} · Escolha como excluir este lançamento recorrente.`
    : 'Esta ação não pode ser desfeita. O lançamento será removido permanentemente.';

  // ── Seção de escopo (parcelado / recorrente) ────────────────────────────────
  const scopeSection = hasScope ? (
    <div className={`grid grid-cols-1 gap-4 ${is_rateio_origem ? 'sm:grid-cols-2' : 'sm:grid-cols-2'}`}>
      {/* Card: apenas este */}
      <div
        className={`flex flex-col justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
          scope === 'single'
            ? 'border-primary bg-primary/5'
            : 'border-border hover:bg-muted/30'
        }`}
        onClick={() => setScope('single')}
      >
        <div className="space-y-2">
          <h3 className="text-sm font-semibold leading-snug">
            {is_parcelado ? `Apenas esta parcela ${parcela_info ? `(${parcela_info})` : ''}` : 'Apenas este lançamento'}
          </h3>
          <p className="text-xs leading-relaxed text-muted-foreground">
            {is_parcelado
              ? 'As demais parcelas não serão afetadas.'
              : 'As demais ocorrências não serão afetadas.'}
          </p>
        </div>
      </div>

      {/* Card: todos */}
      <div
        className={`flex flex-col justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
          scope === 'all'
            ? 'border-primary bg-primary/5'
            : 'border-border hover:bg-muted/30'
        }`}
        onClick={() => setScope('all')}
      >
        <div className="space-y-2">
          <h3 className="text-sm font-semibold leading-snug">
            {is_parcelado ? 'Todas as parcelas' : 'Todas as ocorrências'}
          </h3>
          <p className="text-xs leading-relaxed text-muted-foreground">
            Apenas as que <strong>não estão pagas ou recebidas</strong> serão removidas.
          </p>
          <p className="text-xs leading-relaxed text-muted-foreground">
            Lançamentos já quitados permanecerão intactos.
          </p>
        </div>
      </div>
    </div>
  ) : null;

  const isRateio = is_rateio_origem || is_rateio_filho;

  // ── Seção de rateio intercompany ────────────────────────────────────────────
  const rateioSection = isRateio ? (
    <div className="space-y-3">
      <p className="text-sm font-medium text-foreground">
        Lançamentos intercompany (filiais)
      </p>
      <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
        {/* Card: apenas o pai */}
        <div
          className={`flex flex-col rounded-lg border p-4 cursor-pointer transition-colors ${
            rateioScope === 'parent_only'
              ? 'border-primary bg-primary/5'
              : 'border-border hover:bg-muted/30'
          }`}
          onClick={() => setRateioScope('parent_only')}
        >
          <div className="space-y-2">
            <h3 className="text-sm font-semibold leading-snug">Apenas o registro pai</h3>
            <p className="text-xs leading-relaxed text-muted-foreground">
              Exclui somente o lançamento da matriz. Os registros nas filiais tornam-se
              independentes.
            </p>
          </div>
        </div>

        {/* Card: pai e filhos */}
        <div
          className={`flex flex-col rounded-lg border p-4 cursor-pointer transition-colors ${
            rateioScope === 'all'
              ? 'border-destructive bg-destructive/[0.03]'
              : 'border-border hover:bg-muted/30'
          }`}
          onClick={() => setRateioScope('all')}
        >
          <div className="space-y-2">
            <h3 className="text-sm font-semibold leading-snug">Pai e todos os filhos</h3>
            <p className="text-xs leading-relaxed text-muted-foreground">
              Remove o lançamento da matriz e os registros nas filiais que estão{' '}
              <strong>em aberto</strong>. Os já pagos/recebidos tornam-se independentes.
            </p>
          </div>
        </div>

        {/* Card: apenas os filhos */}
        <div
          className={`flex flex-col rounded-lg border p-4 cursor-pointer transition-colors ${
            rateioScope === 'children_only'
              ? 'border-primary bg-primary/5'
              : 'border-border hover:bg-muted/30'
          }`}
          onClick={() => setRateioScope('children_only')}
        >
          <div className="space-y-2">
            <h3 className="text-sm font-semibold leading-snug">Apenas os registros filhos</h3>
            <p className="text-xs leading-relaxed text-muted-foreground">
              Mantém o lançamento da matriz. Remove apenas os registros nas filiais que estão{' '}
              <strong>em aberto</strong>.
            </p>
          </div>
        </div>
      </div>
    </div>
  ) : null;

  // ── Botão de confirmação ────────────────────────────────────────────────────
  const confirmLabel = isRateio && rateioScope === 'children_only'
    ? 'Excluir apenas os filhos'
    : !hasScope
      ? 'Excluir lançamento'
      : scope === 'all'
        ? is_parcelado
          ? 'Excluir parcelas'
          : 'Excluir ocorrências'
        : 'Excluir apenas este';

  return (
    <Dialog
      open={open}
      onOpenChange={(o) => {
        if (!loading) {
          onOpenChange(o);
          // Reset state ao fechar
          if (!o) {
            setScope('single');
            setRateioScope('parent_only');
          }
        }
      }}
    >
      <DialogContent className="max-w-2xl">
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
          <DialogDescription className="text-sm leading-relaxed">
            {description}
          </DialogDescription>
        </DialogHeader>

        <div className="space-y-5 pt-1">
          {scopeSection}
          {rateioSection}

          <div className="flex justify-end gap-2 pt-1">
            <Button
              type="button"
              variant="outline"
              size="sm"
              disabled={loading}
              onClick={() => {
                onOpenChange(false);
                setScope('single');
                setRateioScope('parent_only');
              }}
            >
              Cancelar
            </Button>
            <Button
              type="button"
              variant="destructive"
              size="sm"
              disabled={loading}
              onClick={() => handleConfirm(scope, rateioScope)}
            >
              {loading && <Loader2 className="mr-2 size-4 animate-spin" aria-hidden />}
              {confirmLabel}
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
