import { useCallback, useEffect, useId, useRef, useState } from 'react';
import { ExternalLink, FileText, Link2, Loader2, Paperclip, Trash2, Upload } from 'lucide-react';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

/** Mesmos tipos do Blade `anexos-input.blade.php`. */
export const LANCAMENTO_TIPOS_ANEXO = [
  'Boleto',
  'Nota Fiscal',
  'NF-e (XML)',
  'Fatura',
  'Recibo',
  'Comprovante',
  'Contrato',
  'DARF',
  'Guia',
  'Planilha',
  'Outros',
] as const;

const ACCEPT_ATTR =
  '.jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.csv,.xml,.txt,.odt,.ods';

const MAX_BYTES = 10 * 1024 * 1024;

export interface LancamentoStagedAnexo {
  id: string;
  file: File;
  tipoAnexo: string;
  descricao: string;
}

/** Anexo já persistido (GET `/app/financeiro/banco/lancamento/{id}` → `anexos`). */
export interface LancamentoExistingAnexo {
  id: number;
  nome: string;
  url: string;
  forma_anexo: string;
  tipo_anexo: string | null;
  descricao: string | null;
}

export function parseLancamentoExistingAnexosApi(raw: unknown): LancamentoExistingAnexo[] {
  if (!Array.isArray(raw)) return [];
  const out: LancamentoExistingAnexo[] = [];
  for (const x of raw) {
    if (!x || typeof x !== 'object') continue;
    const o = x as Record<string, unknown>;
    const id = Number(o.id);
    if (!Number.isFinite(id)) continue;
    out.push({
      id,
      nome: String(o.nome ?? ''),
      url: String(o.url ?? '#'),
      forma_anexo: String(o.forma_anexo ?? 'arquivo'),
      tipo_anexo: o.tipo_anexo != null ? String(o.tipo_anexo) : null,
      descricao: o.descricao != null ? String(o.descricao) : null,
    });
  }
  return out;
}

function newId(): string {
  return typeof crypto !== 'undefined' && crypto.randomUUID
    ? crypto.randomUUID()
    : `anx-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;
}

function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 KB';
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1048576) return `${Math.round(bytes / 1024)} KB`;
  return `${(bytes / 1048576).toFixed(2)} MB`;
}

function truncateName(name: string, max = 40): string {
  if (name.length <= max) return name;
  return `${name.slice(0, Math.max(0, max - 3))}...`;
}

const extPattern = /\.(jpe?g|png|gif|pdf|docx?|xlsx?|csv|xml|txt|odt|ods)$/i;

function validateFile(file: File): string | null {
  if (file.size > MAX_BYTES) {
    return `Arquivo muito grande (${formatFileSize(file.size)}). Máximo: 10 MB.`;
  }
  const name = file.name.toLowerCase();
  if (!extPattern.test(name)) {
    return 'Tipo de arquivo não permitido. Use PDF, imagens ou documentos listados na ajuda.';
  }
  return null;
}

function mergeFiles(
  current: LancamentoStagedAnexo[],
  list: File[],
): LancamentoStagedAnexo[] {
  const next = [...current];
  for (const file of list) {
    const err = validateFile(file);
    if (err) {
      notify.error('Anexo', err);
      continue;
    }
    next.push({
      id: newId(),
      file,
      tipoAnexo: '',
      descricao: '',
    });
  }
  return next;
}

export interface LancamentoAnexosInputProps {
  value: LancamentoStagedAnexo[];
  onChange: (rows: LancamentoStagedAnexo[]) => void;
  disabled?: boolean;
  /** Anexos já salvos (modo edição); cada item abre em nova aba ao clicar. */
  existingAnexos?: LancamentoExistingAnexo[];
  /** Exclui no servidor e atualiza a lista (só edição). */
  onDeleteExistingAnexo?: (id: number) => Promise<boolean>;
}

export function LancamentoAnexosInput({
  value,
  onChange,
  disabled,
  existingAnexos = [],
  onDeleteExistingAnexo,
}: LancamentoAnexosInputProps) {
  const inputId = useId();
  const fileRef = useRef<HTMLInputElement>(null);
  const valueRef = useRef(value);
  const [dragOver, setDragOver] = useState(false);
  const [pendingDeleteId, setPendingDeleteId] = useState<number | null>(null);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  useEffect(() => {
    valueRef.current = value;
  }, [value]);

  const addFiles = useCallback(
    (files: FileList | File[] | null) => {
      if (!files || disabled) return;
      const arr = Array.from(files);
      if (arr.length === 0) return;
      onChange(mergeFiles(valueRef.current, arr));
      if (fileRef.current) fileRef.current.value = '';
    },
    [disabled, onChange],
  );

  const onDrop = useCallback(
    (e: React.DragEvent) => {
      e.preventDefault();
      e.stopPropagation();
      setDragOver(false);
      if (disabled) return;
      addFiles(e.dataTransfer.files);
    },
    [addFiles, disabled],
  );

  const updateRow = useCallback(
    (id: string, patch: Partial<Pick<LancamentoStagedAnexo, 'tipoAnexo' | 'descricao'>>) => {
      onChange(valueRef.current.map((r) => (r.id === id ? { ...r, ...patch } : r)));
    },
    [onChange],
  );

  const removeRow = useCallback(
    (id: string) => {
      onChange(valueRef.current.filter((r) => r.id !== id));
    },
    [onChange],
  );

  async function confirmDeleteExisting() {
    if (pendingDeleteId == null || !onDeleteExistingAnexo) return;
    const id = pendingDeleteId;
    setDeletingId(id);
    try {
      await onDeleteExistingAnexo(id);
    } finally {
      setDeletingId(null);
      setPendingDeleteId(null);
    }
  }

  return (
    <div className="space-y-4">
      {existingAnexos.length > 0 && (
        <div className="space-y-3">
          <div>
            <p className="text-sm font-medium">Anexos já enviados</p>
            <p className="text-xs text-muted-foreground mt-0.5">
              Clique no nome para abrir em nova aba. Use a lixeira para remover o arquivo no servidor.
            </p>
          </div>

          <div className="hidden sm:grid sm:grid-cols-12 gap-2 text-xs font-medium text-muted-foreground px-1">
            <span className="sm:col-span-4">Anexo</span>
            <span className="sm:col-span-3">Tipo</span>
            <span className="sm:col-span-4">Descrição</span>
            <span className="sm:col-span-1" />
          </div>

          {existingAnexos.map((a) => (
            <div
              key={a.id}
              className="rounded-lg border border-border bg-card p-3 sm:p-4 space-y-3 sm:space-y-0 sm:grid sm:grid-cols-12 sm:gap-2 sm:items-center"
            >
              <div className="sm:col-span-4 flex items-start gap-2 min-w-0">
                {a.forma_anexo === 'link' ? (
                  <Link2 className="size-4 shrink-0 text-primary mt-0.5" />
                ) : (
                  <FileText className="size-4 shrink-0 text-primary mt-0.5" />
                )}
                <div className="min-w-0 text-left">
                  <a
                    href={a.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline max-w-full group"
                  >
                    <span className="truncate" title={a.nome || a.url}>
                      {truncateName(a.nome || a.url, 48)}
                    </span>
                    <ExternalLink className="size-3.5 shrink-0 text-muted-foreground group-hover:text-primary" />
                  </a>
                </div>
              </div>

              <div className="sm:col-span-3">
                <Label className="text-xs sm:sr-only text-muted-foreground">Tipo</Label>
                <p className="text-sm text-foreground sm:pt-0 pt-0.5">
                  {a.tipo_anexo ? (
                    <span className="inline-flex rounded-md bg-muted px-2 py-0.5 text-xs font-medium">
                      {a.tipo_anexo}
                    </span>
                  ) : (
                    <span className="text-xs text-muted-foreground">—</span>
                  )}
                </p>
              </div>

              <div className="sm:col-span-4 min-w-0">
                <Label className="text-xs sm:sr-only text-muted-foreground">Descrição</Label>
                <p className="text-sm text-muted-foreground break-words sm:pt-0 pt-0.5">
                  {a.descricao?.trim() ? a.descricao : '—'}
                </p>
              </div>

              <div className="sm:col-span-1 flex sm:justify-center justify-end">
                {onDeleteExistingAnexo ? (
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="size-9 text-muted-foreground hover:text-destructive"
                    disabled={disabled || deletingId === a.id}
                    onClick={() => setPendingDeleteId(a.id)}
                    aria-label="Excluir anexo"
                  >
                    {deletingId === a.id ? (
                      <Loader2 className="size-4 animate-spin" />
                    ) : (
                      <Trash2 className="size-4" />
                    )}
                  </Button>
                ) : null}
              </div>
            </div>
          ))}
        </div>
      )}

      <AlertDialog open={pendingDeleteId !== null} onOpenChange={(o) => !o && setPendingDeleteId(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Excluir anexo?</AlertDialogTitle>
            <AlertDialogDescription>
              O arquivo será removido do servidor. Esta ação não pode ser desfeita.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={deletingId !== null}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              className="bg-destructive hover:bg-destructive/90 text-white"
              disabled={deletingId !== null}
              onClick={(e) => {
                e.preventDefault();
                void confirmDeleteExisting();
              }}
            >
              {deletingId !== null ? (
                <>
                  <Loader2 className="size-4 animate-spin inline-block mr-2" />
                  Excluindo…
                </>
              ) : (
                'Excluir'
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      <div
        role="button"
        tabIndex={0}
        onKeyDown={(e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            if (!disabled) fileRef.current?.click();
          }
        }}
        onDragEnter={(e) => {
          e.preventDefault();
          if (!disabled) setDragOver(true);
        }}
        onDragLeave={(e) => {
          e.preventDefault();
          if (!e.currentTarget.contains(e.relatedTarget as Node)) setDragOver(false);
        }}
        onDragOver={(e) => e.preventDefault()}
        onDrop={onDrop}
        onClick={() => !disabled && fileRef.current?.click()}
        className={cn(
          'border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center gap-2 text-center cursor-pointer transition-colors',
          dragOver ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/40 hover:bg-muted/40',
          disabled && 'pointer-events-none opacity-50',
        )}
      >
        <Paperclip className="size-7 text-muted-foreground" />
        <p className="text-sm font-medium">Arraste arquivos ou clique para selecionar</p>
        <p className="text-xs text-muted-foreground max-w-md">
          PDF, imagens, Office, XML, CSV, ODT/ODS (máx. 10 MB por arquivo)
        </p>
        <input
          ref={fileRef}
          id={inputId}
          type="file"
          multiple
          accept={ACCEPT_ATTR}
          className="sr-only"
          disabled={disabled}
          onChange={(e) => addFiles(e.target.files)}
        />
        <Button
          type="button"
          variant="outline"
          size="sm"
          className="gap-1.5"
          disabled={disabled}
          onClick={(e) => {
            e.stopPropagation();
            fileRef.current?.click();
          }}
        >
          <Upload className="size-3.5" />
          Selecionar arquivos
        </Button>
      </div>

      {value.length > 0 && (
        <div className="space-y-3">
          <div className="hidden sm:grid sm:grid-cols-12 gap-2 text-xs font-medium text-muted-foreground px-1">
            <span className="sm:col-span-4">Anexo</span>
            <span className="sm:col-span-3">Tipo</span>
            <span className="sm:col-span-4">Descrição</span>
            <span className="sm:col-span-1" />
          </div>

          {value.map((row) => (
            <div
              key={row.id}
              className="rounded-lg border border-border bg-card p-3 sm:p-4 space-y-3 sm:space-y-0 sm:grid sm:grid-cols-12 sm:gap-2 sm:items-end"
            >
              <div className="sm:col-span-4 flex items-start gap-2 min-w-0">
                <FileText className="size-4 shrink-0 text-primary mt-0.5" />
                <div className="min-w-0 text-left">
                  <p className="text-sm font-medium truncate" title={row.file.name}>
                    {truncateName(row.file.name)}
                  </p>
                  <p className="text-xs text-muted-foreground">{formatFileSize(row.file.size)}</p>
                </div>
              </div>

              <div className="sm:col-span-3 space-y-1">
                <Label className="text-xs sm:sr-only">Tipo de anexo</Label>
                <Select
                  value={row.tipoAnexo || '__empty__'}
                  onValueChange={(v) => updateRow(row.id, { tipoAnexo: v === '__empty__' ? '' : v })}
                  disabled={disabled}
                >
                  <SelectTrigger className="h-9">
                    <SelectValue placeholder="Selecione" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="__empty__">—</SelectItem>
                    {LANCAMENTO_TIPOS_ANEXO.map((t) => (
                      <SelectItem key={t} value={t}>
                        {t}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div className="sm:col-span-4 space-y-1">
                <Label className="text-xs sm:sr-only">Descrição</Label>
                <Input
                  className="h-9"
                  placeholder="Descrição do anexo"
                  value={row.descricao}
                  onChange={(e) => updateRow(row.id, { descricao: e.target.value })}
                  disabled={disabled}
                  maxLength={500}
                />
              </div>

              <div className="sm:col-span-1 flex sm:justify-center">
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="size-9 text-muted-foreground hover:text-destructive"
                  disabled={disabled}
                  onClick={() => removeRow(row.id)}
                  aria-label="Remover anexo"
                >
                  <Trash2 className="size-4" />
                </Button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
