import { useState } from 'react';
import { Bell, FileText, GitMerge, Loader2 } from 'lucide-react';
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { notify } from '@/lib/notify';

interface ConciliacaoRelatorioDialogProps {
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

function isoToBr(iso: string): string {
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
}

const STATUS_OPTIONS = [
  { value: 'todos',       label: 'Todos' },
  { value: 'ok',          label: 'Conciliado' },
  { value: 'pendente',    label: 'Pendente' },
  { value: 'parcial',     label: 'Parcial' },
  { value: 'divergente',  label: 'Divergente' },
  { value: 'ignorado',    label: 'Ignorado' },
];

export function ConciliacaoRelatorioDialog({ open, onOpenChange }: ConciliacaoRelatorioDialogProps) {
  const defaults = mesAnterior();
  const [dataInicial, setDataInicial] = useState(defaults.inicio);
  const [dataFinal, setDataFinal] = useState(defaults.fim);
  const [status, setStatus] = useState('pendente');
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
      const res = await fetch('/relatorios/conciliacao-bancaria/pdf-async', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          data_inicial: isoToBr(dataInicial),
          data_final: isoToBr(dataFinal),
          status,
        }),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro', json.message ?? 'Não foi possível gerar o relatório de conciliação.');
        return;
      }
      onOpenChange(false);
      notify.pdfLoading('Conciliação em processamento', 'O PDF está sendo gerado e chegará nas suas notificações em instantes.');
    } catch {
      notify.error('Erro', 'Não foi possível gerar o relatório de conciliação.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={handleClose}>
      <DialogContent className="max-w-prose !top-[8%] !translate-y-0 min-h-[300px]">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <GitMerge className="size-4 text-muted-foreground" />
            Conciliação Bancária
          </DialogTitle>
          <DialogDescription>
            Gere o relatório de conciliação bancária por período e status.
          </DialogDescription>
        </DialogHeader>

        <DialogBody className="flex flex-col gap-5 py-2">
          {/* Período */}
          <div className="grid grid-cols-2 gap-4">
            <div className="flex flex-col gap-2">
              <Label className="text-xs">
                Período Inicial <span className="text-destructive">*</span>
              </Label>
              <DatePicker value={dataInicial} onChange={setDataInicial} placeholder="dd/mm/aaaa" />
            </div>
            <div className="flex flex-col gap-2">
              <Label className="text-xs">
                Período Final <span className="text-destructive">*</span>
              </Label>
              <DatePicker value={dataFinal} onChange={setDataFinal} placeholder="dd/mm/aaaa" />
            </div>
          </div>

          {/* Status */}
          <div className="flex flex-col gap-2">
            <Label className="text-xs">Status de conciliação</Label>
            <Select value={status} onValueChange={setStatus}>
              <SelectTrigger>
                <SelectValue placeholder="Selecione o status" />
              </SelectTrigger>
              <SelectContent>
                {STATUS_OPTIONS.map((opt) => (
                  <SelectItem key={opt.value} value={opt.value}>
                    {opt.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* Info assíncrona */}
          <div className="flex gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-950/40">
            <Bell className="mt-0.5 size-4 shrink-0 text-amber-500" />
            <span className="text-xs text-amber-600/80 dark:text-amber-400/80">
              O PDF será gerado em segundo plano e chegará nas suas notificações quando estiver pronto. O arquivo fica disponível por 24 horas.
            </span>
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
                Gerar Relatório
              </>
            )}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
