import { useState, useRef, useCallback } from 'react';
import { CloudUpload, FileUp, Loader2 } from 'lucide-react';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

const ALLOWED_TYPES = [
  'application/pdf',
  'image/png',
  'image/jpeg',
  'image/jpg',
  'image/webp',
];
const MAX_FILE_SIZE = 10 * 1024 * 1024;

interface UploadZoneProps {
  onUploaded: (documentoId: number) => void;
}

export function UploadZone({ onUploaded }: UploadZoneProps) {
  const [dragover, setDragover] = useState(false);
  const [uploading, setUploading] = useState(false);
  const inputRef = useRef<HTMLInputElement>(null);
  const dragCounter = useRef(0);

  const validateFiles = useCallback((files: File[]) => {
    const valid: File[] = [];
    const errors: string[] = [];

    for (const file of files) {
      if (!ALLOWED_TYPES.includes(file.type)) {
        errors.push(`"${file.name}" — tipo não permitido`);
      } else if (file.size > MAX_FILE_SIZE) {
        errors.push(`"${file.name}" — excede 10 MB`);
      } else {
        valid.push(file);
      }
    }

    if (errors.length > 0) {
      notify.warning('Arquivos ignorados', errors.join('\n'));
    }

    return valid;
  }, []);

  const uploadFile = useCallback(
    async (file: File) => {
      setUploading(true);
      try {
        const base64 = await fileToBase64(file);
        const csrfEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');

        const res = await fetch('/financeiro/domusia/extract', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfEl?.content ?? '',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({
            base64_content: base64,
            mime_type: file.type,
            filename: file.name,
            canal_origem: 'upload',
          }),
        });

        if (!res.ok) {
          const err = await res.json().catch(() => null);
          throw new Error(err?.message ?? `Erro ${res.status}`);
        }

        const data = await res.json();

        if (data.success && data.documento_id) {
          notify.success('Documento processado', `${data.nome_sugerido ?? file.name} foi analisado pela IA.`);
          onUploaded(data.documento_id);
        } else {
          throw new Error(data.message ?? 'Erro ao processar');
        }
      } catch (err: unknown) {
        const msg = err instanceof Error ? err.message : 'Erro desconhecido';
        notify.error('Falha no upload', msg);
      } finally {
        setUploading(false);
      }
    },
    [onUploaded],
  );

  const handleFiles = useCallback(
    (files: File[]) => {
      const valid = validateFiles(files);
      if (valid.length > 0) {
        uploadFile(valid[0]);
      }
    },
    [validateFiles, uploadFile],
  );

  return (
    <div className="rounded-lg border border-border bg-card p-4">
      <div
        role="button"
        tabIndex={0}
        onClick={() => !uploading && inputRef.current?.click()}
        onKeyDown={(e) => {
          if ((e.key === 'Enter' || e.key === ' ') && !uploading) {
            e.preventDefault();
            inputRef.current?.click();
          }
        }}
        onDragEnter={(e) => {
          e.preventDefault();
          dragCounter.current++;
          setDragover(true);
        }}
        onDragOver={(e) => e.preventDefault()}
        onDragLeave={() => {
          dragCounter.current--;
          if (dragCounter.current === 0) setDragover(false);
        }}
        onDrop={(e) => {
          e.preventDefault();
          dragCounter.current = 0;
          setDragover(false);
          handleFiles(Array.from(e.dataTransfer.files));
        }}
        className={cn(
          'flex items-center justify-center gap-3 rounded-lg border-2 border-dashed p-6 transition-all cursor-pointer outline-none',
          'focus-visible:ring-2 focus-visible:ring-ring',
          dragover
            ? 'border-primary bg-primary/5 scale-[1.01]'
            : 'border-muted-foreground/25 hover:bg-muted/50',
          uploading && 'pointer-events-none opacity-60',
        )}
      >
        {uploading ? (
          <>
            <Loader2 className="size-6 text-primary animate-spin" />
            <span className="text-sm font-medium text-primary">Processando documento…</span>
          </>
        ) : dragover ? (
          <>
            <FileUp className="size-6 text-primary" />
            <span className="text-sm font-semibold text-primary">Solte os arquivos aqui</span>
          </>
        ) : (
          <>
            <CloudUpload className="size-6 text-primary" />
            <span className="text-sm font-semibold text-primary">
              Clique ou arraste arquivos para importar
            </span>
          </>
        )}
        <input
          ref={inputRef}
          type="file"
          accept=".pdf,.png,.jpg,.jpeg,.webp"
          multiple
          className="hidden"
          onChange={(e) => {
            if (e.target.files) handleFiles(Array.from(e.target.files));
            e.target.value = '';
          }}
        />
      </div>
      <p className="text-center text-muted-foreground text-xs mt-2">
        PDF e imagens de até 10 MB
      </p>
    </div>
  );
}

function fileToBase64(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => {
      const result = reader.result as string;
      resolve(result.split(',')[1]);
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}
