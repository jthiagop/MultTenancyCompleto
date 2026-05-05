import { useState } from 'react';
import { AlertTriangle, Loader2, Trash2 } from 'lucide-react';
import {
  AlertDialog,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { useAppData } from '@/hooks/useAppData';
import { notify } from '@/lib/notify';
import type { IDizimo } from '@/hooks/useDizimos';

export interface DeleteDizimoDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  dizimo: IDizimo | null;
  onDeleted: () => void;
}

/**
 * Confirmação de exclusão de um lançamento de Dízimo/Doação.
 * Quando `integrado_financeiro=true`, exibe aviso de que a movimentação e a
 * transação financeira também serão removidas (e o saldo da conta ajustado).
 */
export function DeleteDizimoDialog({
  open,
  onOpenChange,
  dizimo,
  onDeleted,
}: DeleteDizimoDialogProps) {
  const { csrfToken } = useAppData();
  const [loading, setLoading] = useState(false);

  async function handleConfirm() {
    if (!dizimo || !csrfToken) return;

    setLoading(true);
    try {
      const res = await fetch(`/api/cadastros/dizimos/${dizimo.id}`, {
        method: 'DELETE',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      if (res.ok || res.status === 204) {
        notify.success(
          'Lançamento excluído!',
          `${dizimo.tipo} de ${dizimo.fiel?.nome_completo ?? 'fiel'} foi removido.`,
        );
        onOpenChange(false);
        onDeleted();
        return;
      }

      const result = (await res.json().catch(() => ({}))) as { message?: string };
      notify.error(
        'Não foi possível excluir',
        result.message ?? 'Verifique sua conexão e tente novamente.',
      );
    } catch {
      notify.networkError();
    } finally {
      setLoading(false);
    }
  }

  const integrado = dizimo?.integrado_financeiro ?? false;

  return (
    <AlertDialog open={open} onOpenChange={onOpenChange}>
      <AlertDialogContent className="max-w-md">
        <AlertDialogHeader>
          <div className="flex items-center gap-3 mb-1">
            <span className="flex size-10 items-center justify-center rounded-full bg-destructive/10 shrink-0">
              <AlertTriangle className="size-5 text-destructive" />
            </span>
            <AlertDialogTitle className="text-base">Excluir lançamento</AlertDialogTitle>
          </div>
          <AlertDialogDescription asChild>
            <div className="space-y-2 text-sm text-muted-foreground">
              <p>
                Você está prestes a excluir o lançamento{' '}
                <span className="font-semibold text-foreground">
                  #{dizimo?.id} · {dizimo?.tipo}
                </span>
                {dizimo?.fiel ? (
                  <>
                    {' '}de{' '}
                    <span className="font-semibold text-foreground">
                      {dizimo.fiel.nome_completo}
                    </span>
                  </>
                ) : null}
                {dizimo?.data_pagamento_formatada ? (
                  <> em <span className="tabular-nums">{dizimo.data_pagamento_formatada}</span></>
                ) : null}
                {' '}— valor{' '}
                <span className="font-semibold tabular-nums text-foreground">
                  {dizimo?.valor_formatado}
                </span>
                .
              </p>
              {integrado && (
                <p className="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/40 dark:text-amber-200">
                  Este lançamento está integrado ao financeiro. Ao excluí-lo, o saldo da conta será ajustado automaticamente.
                </p>
              )}
              <p>Esta ação <strong>não pode ser desfeita</strong>.</p>
            </div>
          </AlertDialogDescription>
        </AlertDialogHeader>

        <AlertDialogFooter className="gap-2 sm:gap-0">
          <AlertDialogCancel disabled={loading}>Cancelar</AlertDialogCancel>
          <Button
            variant="destructive"
            onClick={handleConfirm}
            disabled={loading}
            className="gap-2"
          >
            {loading ? <Loader2 className="size-4 animate-spin" /> : <Trash2 className="size-4" />}
            {loading ? 'Excluindo…' : 'Excluir permanentemente'}
          </Button>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
