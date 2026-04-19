import { useState } from 'react';
import { Bell, FileText, Info, Loader2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useAppData } from '@/hooks/useAppData';
import {
  Dialog,
  DialogBody,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { DatePicker } from '@/components/ui/date-picker';
import { notify } from '@/lib/notify';

interface BoletimFinanceiroDialogProps {
  open: boolean;
  onOpenChange: (v: boolean) => void;
}

function mesAnterior() {
  const hoje = new Date();
  const umMesAtras = new Date(hoje.getFullYear(), hoje.getMonth() - 1, hoje.getDate());
  const fmt = (d: Date) =>
    `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  return { inicio: fmt(umMesAtras), fim: fmt(hoje) };
}

export function BoletimFinanceiroDialog({ open, onOpenChange }: BoletimFinanceiroDialogProps) {
  const defaults = mesAnterior();
  const [dataInicial, setDataInicial] = useState(defaults.inicio);
  const [dataFinal, setDataFinal] = useState(defaults.fim);
  const [loading, setLoading] = useState(false);
  const { csrfToken } = useAppData();

  function handleClose() {
    if (loading) return;
    onOpenChange(false);
  }

  async function handleSubmit() {
    if (!dataInicial || !dataFinal) {
      notify.error('Campos obrigatórios', 'Preencha o período inicial e final.');
      return;
    }
    if (dataInicial > dataFinal) {
      notify.error('Período inválido', 'A data inicial não pode ser maior que a data final.');
      return;
    }
    if (!csrfToken) return;

    setLoading(true);
    try {
      const res = await fetch('/relatorios/boletim-financeiro/pdf-async', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ data_inicial: dataInicial, data_final: dataFinal }),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro', json.message ?? 'Não foi possível gerar o boletim.');
        return;
      }
      onOpenChange(false);
      notify.pdfLoading('Boletim em processamento', 'O PDF está sendo gerado e chegará nas suas notificações em instantes.');
    } catch {
      notify.error('Erro', 'Não foi possível gerar o boletim.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={handleClose}>
      <DialogContent className="max-w-prose !top-[8%] !translate-y-0 min-h-[320px]">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <FileText className="size-4 text-muted-foreground" />
            Boletim Financeiro
          </DialogTitle>
          <DialogDescription>
            Selecione o período para gerar o resumo das movimentações financeiras.
          </DialogDescription>
        </DialogHeader>

        <DialogBody className="flex flex-col gap-6 py-2">
          <div className="grid grid-cols-2 gap-4">
            <div className="flex flex-col gap-2">
              <Label className="text-xs">
                Período Inicial <span className="text-destructive">*</span>
              </Label>
              <DatePicker
                value={dataInicial}
                onChange={setDataInicial}
                placeholder="dd/mm/aaaa"
              />
            </div>
            <div className="flex flex-col gap-2">
              <Label className="text-xs">
                Período Final <span className="text-destructive">*</span>
              </Label>
              <DatePicker
                value={dataFinal}
                onChange={setDataFinal}
                placeholder="dd/mm/aaaa"
              />
            </div>
          </div>

          <div className="flex gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/40">
            <Info className="mt-0.5 size-4 shrink-0 text-blue-500" />
            <div className="flex flex-col gap-0.5">
              <span className="text-sm font-medium text-blue-700 dark:text-blue-300">
                Informações do Boletim
              </span>
              <span className="text-xs text-blue-600/80 dark:text-blue-400/80">
                O boletim apresentará um resumo completo das movimentações financeiras no período selecionado, incluindo entradas, saídas e saldo.
              </span>
            </div>
          </div>

          <div className="flex gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/40">
            <Bell className="mt-0.5 size-4 shrink-0 text-amber-500" />
            <div className="flex flex-col gap-0.5">
              <span className="text-sm font-medium text-amber-700 dark:text-amber-300">
                Geração assíncrona
              </span>
              <span className="text-xs text-amber-600/80 dark:text-amber-400/80">
                O PDF será gerado em segundo plano. Quando estiver pronto, você receberá uma notificação com o link para download. O arquivo fica disponível por 24 horas.
              </span>
            </div>
          </div>
        </DialogBody>

        <DialogFooter>
          <Button variant="outline" onClick={handleClose} disabled={loading}>
            Cancelar
          </Button>
          <Button
            className="bg-blue-600 hover:bg-blue-700 text-white border-0"
            onClick={handleSubmit}
            disabled={loading}
          >
            {loading ? (
              <>
                <Loader2 className="size-4 animate-spin" />
                Aguarde...
              </>
            ) : (
              <>
                <FileText className="size-4" />
                Gerar Boletim
              </>
            )}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
