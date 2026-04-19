import { useEffect, useState } from 'react';
import { Bell, BookText, Loader2 } from 'lucide-react';
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

interface ContabilidadeDialogProps {
  open: boolean;
  onOpenChange: (v: boolean) => void;
}

interface Entidade {
  id: number;
  label: string;
  tipo: string;
}

type Formato = 'txt' | 'csv';
type CampoData = 'data' | 'data_competencia';

function mesAnterior() {
  const hoje = new Date();
  const umMesAtras = new Date(hoje.getFullYear(), hoje.getMonth() - 1, hoje.getDate());
  const fmt = (d: Date) =>
    `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  return { inicio: fmt(umMesAtras), fim: fmt(hoje) };
}

/** Converte 'YYYY-MM-DD' → 'DD/MM/YYYY' esperado pelo LoteContabilExportService */
function isoToBr(iso: string): string {
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
}

export function ContabilidadeDialog({ open, onOpenChange }: ContabilidadeDialogProps) {
  const defaults = mesAnterior();
  const [dataInicial, setDataInicial] = useState(defaults.inicio);
  const [dataFinal, setDataFinal] = useState(defaults.fim);
  const [tipoConta, setTipoConta] = useState<'banco' | 'caixa'>('banco');
  const [entidadeId, setEntidadeId] = useState('');
  const [entidades, setEntidades] = useState<Entidade[]>([]);
  const [loadingContas, setLoadingContas] = useState(false);
  const [formato, setFormato] = useState<Formato>('txt');
  const [campoData, setCampoData] = useState<CampoData>('data');
  const [loading, setLoading] = useState(false);
  const { csrfToken } = useAppData();

  useEffect(() => {
    if (!open) return;
    setLoadingContas(true);
    setEntidadeId('');
    fetch('/financeiro/api/form-data?tipo=all', {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    })
      .then((r) => r.json())
      .then((data: { entidades?: Entidade[] }) => {
        const filtradas = (data.entidades ?? []).filter((e) => e.tipo === tipoConta);
        setEntidades(filtradas);
      })
      .catch(() => setEntidades([]))
      .finally(() => setLoadingContas(false));
  }, [open, tipoConta]);

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
    if (!entidadeId) {
      notify.error('Conta obrigatória', 'Selecione uma conta financeira.');
      return;
    }
    if (!csrfToken) return;

    setLoading(true);
    try {
      const res = await fetch('/relatorios/lote-contabil/exportar-async', {
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
          entidade_id: Number(entidadeId),
          formato,
          campo_data: campoData,
        }),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro', json.message ?? 'Não foi possível gerar o arquivo.');
        return;
      }
      onOpenChange(false);
      notify.success(
        'Contabilidade em processamento',
        `O arquivo ${formato.toUpperCase()} está sendo gerado e chegará nas suas notificações em instantes.`,
      );
    } catch {
      notify.error('Erro', 'Não foi possível gerar o arquivo contábil.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={handleClose}>
      <DialogContent className="max-w-2xl !top-[8%] !translate-y-0">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <BookText className="size-4 text-muted-foreground" />
            Exportar Contabilidade
          </DialogTitle>
          <DialogDescription>
            Gera o lote contábil para o Alterdata em formato TXT ou CSV.
          </DialogDescription>
        </DialogHeader>

        <DialogBody className="flex flex-col gap-5 py-2">
          {/* Formato + Regime de data */}
          <div className="grid grid-cols-2 gap-6">
            <div className="flex flex-col gap-2">
              <Label className="text-xs">Formato</Label>
              <div className="flex gap-4">
                {(['txt', 'csv'] as Formato[]).map((f) => (
                  <label key={f} className="flex items-center gap-2 cursor-pointer">
                    <input
                      type="radio"
                      name="contabil_formato"
                      value={f}
                      checked={formato === f}
                      onChange={() => setFormato(f)}
                      className="accent-blue-600"
                    />
                    <span className="text-sm uppercase">{f}</span>
                  </label>
                ))}
              </div>
            </div>

            <div className="flex flex-col gap-2">
              <Label className="text-xs">Regime de data</Label>
              <div className="flex gap-4">
                <label className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="contabil_campo_data"
                    value="data"
                    checked={campoData === 'data'}
                    onChange={() => setCampoData('data')}
                    className="accent-blue-600"
                  />
                  <span className="text-sm">Caixa (data)</span>
                </label>
                <label className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="contabil_campo_data"
                    value="data_competencia"
                    checked={campoData === 'data_competencia'}
                    onChange={() => setCampoData('data_competencia')}
                    className="accent-blue-600"
                  />
                  <span className="text-sm">Competência</span>
                </label>
              </div>
            </div>
          </div>

          {/* Tipo de conta */}
          <div className="flex flex-col gap-2">
            <Label className="text-xs">Tipo de conta</Label>
            <div className="flex gap-4">
              {(['banco', 'caixa'] as const).map((tipo) => (
                <label key={tipo} className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="contabil_tipo_conta"
                    value={tipo}
                    checked={tipoConta === tipo}
                    onChange={() => setTipoConta(tipo)}
                    className="accent-blue-600"
                  />
                  <span className="text-sm capitalize">{tipo}</span>
                </label>
              ))}
            </div>
          </div>

          {/* Conta financeira */}
          <div className="flex flex-col gap-2">
            <Label className="text-xs">
              Conta Financeira <span className="text-destructive">*</span>
            </Label>
            <Select
              value={entidadeId}
              onValueChange={setEntidadeId}
              disabled={loadingContas || entidades.length === 0}
            >
              <SelectTrigger>
                <SelectValue
                  placeholder={
                    loadingContas
                      ? 'Carregando contas...'
                      : entidades.length === 0
                        ? `Nenhuma conta de ${tipoConta} encontrada`
                        : 'Selecione a conta'
                  }
                />
              </SelectTrigger>
              <SelectContent>
                {entidades.map((e) => (
                  <SelectItem key={e.id} value={String(e.id)}>
                    {e.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

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

          {/* Info assíncrona */}
          <div className="flex gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-950/40">
            <Bell className="mt-0.5 size-4 shrink-0 text-amber-500" />
            <span className="text-xs text-amber-600/80 dark:text-amber-400/80">
              O arquivo será gerado em segundo plano e chegará nas suas notificações quando estiver pronto.
            </span>
          </div>
        </DialogBody>

        <DialogFooter>
          <Button variant="outline" onClick={handleClose} disabled={loading}>
            Cancelar
          </Button>
          <Button onClick={handleSubmit} disabled={loading || !entidadeId}>
            {loading ? (
              <>
                <Loader2 className="size-4 animate-spin" />
                Processando...
              </>
            ) : (
              <>
                <BookText className="size-4" />
                Gerar {formato.toUpperCase()}
              </>
            )}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
