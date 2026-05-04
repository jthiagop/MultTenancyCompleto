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

export interface DeleteFielDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  fielId: number | null;
  fielNome: string | null;
  onDeleted: () => void;
}

export function DeleteFielDialog({
  open,
  onOpenChange,
  fielId,
  fielNome,
  onDeleted,
}: DeleteFielDialogProps) {
  const { csrfToken } = useAppData();
  const [loading, setLoading] = useState(false);

  async function handleConfirm() {
    if (!fielId || !csrfToken) return;

    setLoading(true);
    try {
      const res = await fetch(`/api/cadastros/fieis/${fielId}`, {
        method: 'DELETE',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      if (res.ok || res.status === 204) {
        notify.success('Fiel excluído!', `${fielNome ?? 'Fiel'} foi removido com sucesso.`);
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

  return (
    <AlertDialog open={open} onOpenChange={onOpenChange}>
      <AlertDialogContent className="max-w-md">
        <AlertDialogHeader>
          <div className="flex items-center gap-3 mb-1">
            <span className="flex size-10 items-center justify-center rounded-full bg-destructive/10 shrink-0">
              <AlertTriangle className="size-5 text-destructive" />
            </span>
            <AlertDialogTitle className="text-base">Excluir fiel</AlertDialogTitle>
          </div>
          <AlertDialogDescription asChild>
            <div className="space-y-2 text-sm text-muted-foreground">
              <p>
                Você está prestes a excluir permanentemente o fiel{' '}
                <span className="font-semibold text-foreground">
                  {fielNome ?? '—'}
                </span>
                .
              </p>
              <p>
                Todos os dados vinculados — endereço, contatos, dados de dízimo e dados
                complementares — serão removidos e <strong>não poderão ser recuperados</strong>.
              </p>
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
            {loading ? (
              <Loader2 className="size-4 animate-spin" />
            ) : (
              <Trash2 className="size-4" />
            )}
            {loading ? 'Excluindo…' : 'Excluir permanentemente'}
          </Button>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
