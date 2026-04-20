import { useCallback, useRef, useState } from 'react';
import { AlarmClockCheck, AlertCircle, Clock, CloudUpload, FileCheck2, Link2, Paperclip, TriangleAlert, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogBody,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Switch } from '@/components/ui/switch';
import { useAppData } from '@/hooks/useAppData';
import { notify } from '@/lib/notify';
import { cn } from '@/lib/utils';
import importIllustration from '@/assets/import.png';

const ALLOWED_EXTENSION = '.ofx';
const ALLOWED_MIMETYPES = ['application/x-ofx', 'text/x-ofx', 'application/vnd.intu.qbo'];

function validarArquivo(file: File): string | null {
  if (!file.name.toLowerCase().endsWith(ALLOWED_EXTENSION)) {
    return `Extensão inválida. Use apenas arquivos ${ALLOWED_EXTENSION}.`;
  }
  if (file.type && !ALLOWED_MIMETYPES.includes(file.type)) {
    console.warn(`MIME type inesperado: ${file.type}`);
  }
  return null;
}

interface Props {
  onImported?: (entidadeId: number | null) => void;
}

export function ConciliacaoOFXDialog({ onImported }: Props) {
  const { csrfToken, hasHorariosMissa } = useAppData();

  const [open, setOpen] = useState(false);
  const [dragging, setDragging] = useState(false);
  const [file, setFile] = useState<File | null>(null);
  const [fileError, setFileError] = useState<string | null>(null);
  const [usarHorarios, setUsarHorarios] = useState(false);
  const [loading, setLoading] = useState(false);
  const [serverWarning, setServerWarning] = useState<string | null>(null);

  const inputRef = useRef<HTMLInputElement>(null);

  function reset() {
    setFile(null);
    setFileError(null);
    setUsarHorarios(false);
    setDragging(false);
    setServerWarning(null);
  }

  function handleFile(incoming: File | null) {
    if (!incoming) {
      setFile(null);
      setFileError(null);
      return;
    }
    const erro = validarArquivo(incoming);
    if (erro) {
      setFile(null);
      setFileError(erro);
      return;
    }
    setFile(incoming);
    setFileError(null);
  }

  function handleInputChange(e: React.ChangeEvent<HTMLInputElement>) {
    handleFile(e.target.files?.[0] ?? null);
  }

  const handleDrop = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setDragging(false);
    handleFile(e.dataTransfer.files?.[0] ?? null);
  }, []);

  function handleDragOver(e: React.DragEvent<HTMLDivElement>) {
    e.preventDefault();
    setDragging(true);
  }

  function handleDragLeave() {
    setDragging(false);
  }

  function handleSwitchChange(checked: boolean) {
    if (checked && !hasHorariosMissa) {
      notify.warning(
        'Sem horários cadastrados',
        'Cadastre os horários de missa para usar esta opção.',
        {
          action: {
            label: <span className="inline-flex items-center gap-1.5"><Clock className="size-3.5" />Cadastrar</span>,
            onClick: () => window.location.assign('/app/fraternidade?tab=horarios-missas'),
          },
          duration: 8000,
        },
      );
      return;
    }
    setUsarHorarios(checked);
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    if (usarHorarios) formData.append('usar_horarios_missa', '1');

    setServerWarning(null);
    setLoading(true);
    try {
      const res = await fetch('/upload-ofx', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        const msg = data.message ?? 'Não foi possível importar o arquivo.';
        const isWarning = data.type === 'warning';
        // Exibe no dialog como alerta inline
        setServerWarning(msg);
        // Toast: warning para avisos, error para falhas técnicas
        if (isWarning) {
          notify.warning('Atenção', msg);
        } else {
          notify.error('Erro na importação', msg);
        }
      } else {
        notify.success('Extrato importado!', data.message);
        setOpen(false);
        reset();
        onImported?.(data.entidade_id ?? null);
      }
    } catch {
      notify.networkError();
    } finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={(v) => { setOpen(v); if (!v) reset(); }}>
      <DialogTrigger asChild>
        <Button size="md" variant="outline">
          <Link2 className="size-4" />
          Conciliação Bancária
        </Button>
      </DialogTrigger>

      <DialogContent className="sm:max-w-2xl">
        <form onSubmit={handleSubmit}>
          <DialogHeader variant="shaded">
            <DialogTitle>Importar extrato em formato OFX</DialogTitle>
          </DialogHeader>
          <DialogBody>
          <div className="flex items-center gap-6 pb-5 mb-5 border-b border-border">
            <img
              src={importIllustration}
              alt=""
              aria-hidden="true"
              className="w-32 h-32 shrink-0 select-none pointer-events-none object-contain"
            />
            <ol className="flex-1 space-y-3 text-sm">
              <li className="flex gap-3">
                <span className="shrink-0 flex items-center justify-center size-6 rounded-full bg-primary text-primary-foreground text-xs font-bold">1</span>
                <span className="text-foreground">Acesse o site do seu banco e exporte seu extrato no formato <strong>OFX</strong>.</span>
              </li>
              <li className="flex gap-3">
                <span className="shrink-0 flex items-center justify-center size-6 rounded-full bg-primary text-primary-foreground text-xs font-bold">2</span>
                <span className="text-foreground">Após salvar o arquivo no seu computador, importe-o abaixo.</span>
              </li>
            </ol>
          </div>
          <div className="my-5">
            <div
              onDrop={handleDrop}
              onDragOver={handleDragOver}
              onDragLeave={handleDragLeave}
              onClick={() => inputRef.current?.click()}
              className={cn(
                'flex flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed px-6 py-10 cursor-pointer transition-colors select-none',
                dragging
                  ? 'border-green-500 bg-green-50 dark:bg-green-950/30'
                  : file
                    ? 'border-primary/50 bg-primary/5'
                    : 'border-border hover:border-primary/50 hover:bg-muted/40',
              )}
            >
              <input
                ref={inputRef}
                type="file"
                accept=".ofx"
                className="hidden"
                onChange={handleInputChange}
              />

              {file ? (
                <>
                  <FileCheck2 className="size-10 text-primary" />
                  <div className="text-center">
                    <p className="text-sm font-medium text-primary">{file.name}</p>
                    <p className="text-xs text-muted-foreground mt-0.5">
                      {(file.size / 1024).toFixed(1)} KB — clique para trocar
                    </p>
                  </div>
                </>
              ) : (
                <>
                  <CloudUpload className="size-10 text-muted-foreground" />
                  <div className="text-center">
                    <label className="inline-flex items-center gap-1.5 text-sm font-medium text-primary cursor-pointer">
                      <Paperclip className="size-3.5" />
                      Escolha um arquivo
                    </label>
                    <p className="text-xs text-muted-foreground mt-1">
                      ou arraste e solte aqui · apenas <strong>.ofx</strong>
                    </p>
                  </div>
                </>
              )}
            </div>

            {fileError && (
              <p className="mt-2 text-xs text-destructive flex items-center gap-1.5">
                <TriangleAlert className="size-3.5 shrink-0" />
                {fileError}
              </p>
            )}
          </div>

          {/* ── Switch horários de missa ── */}
          <div className="flex items-center justify-between rounded-lg border border-border px-4 py-3">
            <div>
              <p className="text-sm font-medium leading-tight flex items-center gap-1.5">
                <AlarmClockCheck className="size-4 text-muted-foreground" />
                Utilizar Horários de Missa
              </p>
              <p className="text-xs text-muted-foreground mt-0.5">
                Lançamento conforme o horário de missa registrado.
              </p>
            </div>
            <Switch
              checked={usarHorarios}
              onCheckedChange={handleSwitchChange}
            />
          </div>

          {/* ── Alerta de retorno do servidor ── */}
          {serverWarning && (
            <div className="mt-4 flex items-start gap-3 rounded-lg border border-amber-400 bg-amber-50 dark:bg-amber-950/30 px-4 py-3">
              <AlertCircle className="size-5 text-amber-500 shrink-0 mt-0.5" />
              <div className="flex-1 text-sm text-amber-700 dark:text-amber-300 space-y-0.5">
                {serverWarning.split('\n').map((line, i) => (
                  <p key={i} className={i === 0 ? 'font-medium' : 'text-xs'}>{line}</p>
                ))}
              </div>
              <button
                type="button"
                onClick={() => setServerWarning(null)}
                className="text-amber-500 hover:text-amber-700"
              >
                <X className="size-4" />
              </button>
            </div>
          )}
          </DialogBody>
          <DialogFooter>
            <DialogClose asChild>
              <Button type="button" variant="outline" disabled={loading}>
                Cancelar
              </Button>
            </DialogClose>
            <Button type="submit" disabled={!file || loading}>
              {loading ? (
                <>
                  <span className="size-4 rounded-full border-2 border-white/30 border-t-white animate-spin" />
                  Importando...
                </>
              ) : (
                'Importar Extrato'
              )}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
