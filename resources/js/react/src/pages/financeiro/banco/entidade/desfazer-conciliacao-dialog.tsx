import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Loader2 } from 'lucide-react';
import { useAppData } from '@/hooks/useAppData';
import { notify } from '@/lib/notify';

type Modo = 'apenas_desfazer' | 'voltar_aberto' | 'excluir_lancamento';

interface DesfazerConciliacaoDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  bankStatementId: number | null;
  descricaoBanco: string;
  onSuccess: () => void;
}

export function DesfazerConciliacaoDialog({
  open,
  onOpenChange,
  bankStatementId,
  descricaoBanco,
  onSuccess,
}: DesfazerConciliacaoDialogProps) {
  const { csrfToken } = useAppData();
  const [loadingModo, setLoadingModo] = useState<Modo | null>(null);

  const isLoading = loadingModo !== null;

  async function executar(modo: Modo) {
    if (bankStatementId == null || !csrfToken || isLoading) return;
    setLoadingModo(modo);
    try {
      const res = await fetch(`/relatorios/conciliacao/${bankStatementId}/desfazer`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ modo }),
      });
      const json = (await res.json()) as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        throw new Error(json.message ?? 'Falha ao desfazer');
      }
      notify.success('Conciliação', json.message ?? 'Operação concluída.');
      onOpenChange(false);
      onSuccess();
    } catch (e) {
      notify.error('Erro', e instanceof Error ? e.message : 'Não foi possível desfazer a conciliação.');
    } finally {
      setLoadingModo(null);
    }
  }

  return (
    <Dialog
      open={open}
      onOpenChange={(o) => {
        if (!isLoading) onOpenChange(o);
      }}
    >
      <DialogContent className="max-w-2xl">
        <DialogHeader>
          <DialogTitle>O que você quer fazer?</DialogTitle>
          <DialogDescription className="text-sm leading-relaxed">
            Lançamento selecionado:{' '}
            <span className="font-medium text-foreground">
              &ldquo;{descricaoBanco || `#${bankStatementId ?? '—'}`}&rdquo;
            </span>
            .<br />
            Escolhendo qualquer ação abaixo, o lançamento bancário voltará para{' '}
            <strong>Conciliações pendentes</strong>.
          </DialogDescription>
        </DialogHeader>

        <div className="grid grid-cols-1 gap-4 pt-2 sm:grid-cols-3">
          {/* Card 1 — Apenas desfazer */}
          <div className="flex flex-col justify-between rounded-lg border border-border p-4">
            <div className="space-y-2">
              <h3 className="text-sm font-semibold leading-snug">Apenas desfazer</h3>
              <p className="text-xs leading-relaxed text-muted-foreground">
                O lançamento do sistema permanecerá com a situação atual (Recebido / Pago).
              </p>
              <p className="text-xs leading-relaxed text-muted-foreground">
                O saldo da conta <strong>não será alterado</strong>.
              </p>
            </div>
            <Button
              type="button"
              variant="outline"
              size="sm"
              className="mt-4 w-full"
              disabled={isLoading}
              onClick={() => executar('apenas_desfazer')}
            >
              {loadingModo === 'apenas_desfazer' ? (
                <Loader2 className="mr-2 size-4 animate-spin" aria-hidden />
              ) : null}
              Apenas desfazer
            </Button>
          </div>

          {/* Card 2 — Voltar para "em aberto" */}
          <div className="flex flex-col justify-between rounded-lg border border-border p-4">
            <div className="space-y-2">
              <h3 className="text-sm font-semibold leading-snug">
                Desfazer e voltar para &ldquo;em aberto&rdquo;
              </h3>
              <p className="text-xs leading-relaxed text-muted-foreground">
                A situação do lançamento voltará para <strong>Em aberto</strong>.
              </p>
              <p className="text-xs leading-relaxed text-muted-foreground">
                O saldo da conta será <strong>recalculado</strong>, desconsiderando este lançamento.
              </p>
            </div>
            <Button
              type="button"
              variant="outline"
              size="sm"
              className="mt-4 w-full"
              disabled={isLoading}
              onClick={() => executar('voltar_aberto')}
            >
              {loadingModo === 'voltar_aberto' ? (
                <Loader2 className="mr-2 size-4 animate-spin" aria-hidden />
              ) : null}
              Voltar para &ldquo;em aberto&rdquo;
            </Button>
          </div>

          {/* Card 3 — Excluir lançamento */}
          <div className="flex flex-col justify-between rounded-lg border border-destructive/40 bg-destructive/[0.03] p-4">
            <div className="space-y-2">
              <h3 className="text-sm font-semibold leading-snug">
                Desfazer e excluir lançamento
              </h3>
              <p className="text-xs leading-relaxed text-muted-foreground">
                O lançamento do sistema será <strong>excluído</strong>.
              </p>
              <p className="text-xs leading-relaxed text-muted-foreground">
                O saldo da conta será <strong>recalculado</strong>.
              </p>
            </div>
            <Button
              type="button"
              variant="destructive"
              size="sm"
              className="mt-4 w-full"
              disabled={isLoading}
              onClick={() => executar('excluir_lancamento')}
            >
              {loadingModo === 'excluir_lancamento' ? (
                <Loader2 className="mr-2 size-4 animate-spin" aria-hidden />
              ) : null}
              Excluir lançamento
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
