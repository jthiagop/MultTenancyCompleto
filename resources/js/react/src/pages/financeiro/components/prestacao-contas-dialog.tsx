import { useState } from 'react';
import { Bell, FileText, Loader2 } from 'lucide-react';
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

interface PrestacaoContasDialogProps {
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

const SITUACOES = [
  { value: 'pago',         label: 'Pago' },
  { value: 'recebido',     label: 'Recebido' },
  { value: 'em_aberto',    label: 'Em aberto' },
  { value: 'pago_parcial', label: 'Pago parcial' },
  { value: 'atrasado',     label: 'Atrasado' },
];

export function PrestacaoContasDialog({ open, onOpenChange }: PrestacaoContasDialogProps) {
  const defaults = mesAnterior();
  const [dataInicial, setDataInicial] = useState(defaults.inicio);
  const [dataFinal, setDataFinal] = useState(defaults.fim);
  const [modelo, setModelo] = useState<'horizontal' | 'vertical'>('horizontal');
  const [tipoData, setTipoData] = useState<'competencia' | 'pagamento'>('competencia');
  const [tipoValor, setTipoValor] = useState<'previsto' | 'pago'>('previsto');
  const [situacoes, setSituacoes] = useState<string[]>([]);
  const [comprovacaoFiscal, setComprovacaoFiscal] = useState(false);
  const [loading, setLoading] = useState(false);
  const { csrfToken } = useAppData();

  function toggleSituacao(value: string) {
    setSituacoes((prev) =>
      prev.includes(value) ? prev.filter((s) => s !== value) : [...prev, value],
    );
  }

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
      const body: Record<string, unknown> = {
        data_inicial: isoToBr(dataInicial),
        data_final: isoToBr(dataFinal),
        modelo,
        tipo_data: tipoData,
        tipo_valor: tipoValor,
        comprovacao_fiscal: comprovacaoFiscal ? 1 : 0,
      };
      if (situacoes.length > 0) body['situacoes'] = situacoes;

      const res = await fetch('/relatorios/prestacao-de-contas/pdf-async', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(body),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro', json.message ?? 'Não foi possível gerar a prestação de contas.');
        return;
      }
      onOpenChange(false);
      notify.pdfLoading('Prestação em processamento', 'O PDF está sendo gerado e chegará nas suas notificações em instantes.');
    } catch {
      notify.error('Erro', 'Não foi possível gerar a prestação de contas.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={handleClose}>
      <DialogContent className="max-w-2xl !top-[8%] !translate-y-0 min-h-[380px]">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <FileText className="size-4 text-muted-foreground" />
            Prestação de Contas
          </DialogTitle>
          <DialogDescription>
            Configure os parâmetros para gerar o relatório de prestação de contas.
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

          {/* Opções em grid 3 colunas */}
          <div className="grid grid-cols-3 gap-4">
            {/* Modelo */}
            <div className="flex flex-col gap-2">
              <Label className="text-xs">Modelo</Label>
              <div className="flex gap-3">
                {(['horizontal', 'vertical'] as const).map((v) => (
                  <label key={v} className="flex items-center gap-1.5 cursor-pointer">
                    <input
                      type="radio"
                      value={v}
                      checked={modelo === v}
                      onChange={() => setModelo(v)}
                      className="accent-blue-600"
                    />
                    <span className="text-sm capitalize">{v}</span>
                  </label>
                ))}
              </div>
            </div>

            {/* Tipo de data */}
            <div className="flex flex-col gap-2">
              <Label className="text-xs">Regime de data</Label>
              <div className="flex gap-3">
                {[{ value: 'competencia', label: 'Competência' }, { value: 'pagamento', label: 'Pagamento' }].map((v) => (
                  <label key={v.value} className="flex items-center gap-1.5 cursor-pointer">
                    <input
                      type="radio"
                      value={v.value}
                      checked={tipoData === v.value}
                      onChange={() => setTipoData(v.value as 'competencia' | 'pagamento')}
                      className="accent-blue-600"
                    />
                    <span className="text-sm">{v.label}</span>
                  </label>
                ))}
              </div>
            </div>

            {/* Tipo de valor */}
            <div className="flex flex-col gap-2">
              <Label className="text-xs">Tipo de valor</Label>
              <div className="flex gap-3">
                {[{ value: 'previsto', label: 'Previsto' }, { value: 'pago', label: 'Pago' }].map((v) => (
                  <label key={v.value} className="flex items-center gap-1.5 cursor-pointer">
                    <input
                      type="radio"
                      value={v.value}
                      checked={tipoValor === v.value}
                      onChange={() => setTipoValor(v.value as 'previsto' | 'pago')}
                      className="accent-blue-600"
                    />
                    <span className="text-sm">{v.label}</span>
                  </label>
                ))}
              </div>
            </div>
          </div>

          {/* Situações */}
          <div className="flex flex-col gap-2">
            <Label className="text-xs">Situações (opcional — sem seleção = todas)</Label>
            <div className="flex flex-wrap gap-2">
              {SITUACOES.map((s) => (
                <label
                  key={s.value}
                  className={`flex items-center gap-1.5 cursor-pointer rounded-md border px-3 py-1.5 text-xs transition-colors ${
                    situacoes.includes(s.value)
                      ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300'
                      : 'border-border bg-muted/30 text-muted-foreground'
                  }`}
                >
                  <input
                    type="checkbox"
                    className="sr-only"
                    checked={situacoes.includes(s.value)}
                    onChange={() => toggleSituacao(s.value)}
                  />
                  {s.label}
                </label>
              ))}
            </div>
          </div>

          {/* Comprovação fiscal + info */}
          <div className="flex items-start justify-between gap-4">
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={comprovacaoFiscal}
                onChange={(e) => setComprovacaoFiscal(e.target.checked)}
                className="accent-blue-600 size-4"
              />
              <span className="text-sm">Incluir comprovação fiscal</span>
            </label>

            <div className="flex gap-2 rounded-lg border border-amber-200 bg-amber-50 p-2.5 dark:border-amber-800 dark:bg-amber-950/40">
              <Bell className="mt-0.5 size-3.5 shrink-0 text-amber-500" />
              <span className="text-[11px] text-amber-600/80 dark:text-amber-400/80">
                PDF gerado em segundo plano. Você receberá uma notificação quando estiver pronto.
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
                Gerar Prestação
              </>
            )}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
