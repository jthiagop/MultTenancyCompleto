import { useState, useRef, useCallback, useEffect } from 'react';
import {
  TransformWrapper,
  TransformComponent,
  useControls,
  type ReactZoomPanPinchRef,
} from 'react-zoom-pan-pinch';
import {
  ZoomIn,
  ZoomOut,
  Maximize2,
  RotateCcw,
  RotateCw,
  Trash2,
  Loader2,
  FileSearch,
  AlertTriangle,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import { Card } from '@/components/ui/card';
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
import type { DomusDocument } from './document-list';

interface DocumentViewerProps {
  doc: DomusDocument | null;
  onDeleted: () => void;
}

const MIN_SCALE = 0.2;
const MAX_SCALE = 8;
const ZOOM_STEP = 0.4;

export function DocumentViewer({ doc, onDeleted }: DocumentViewerProps) {
  const [loading, setLoading] = useState(false);
  const [rotation, setRotation] = useState(0);
  // Escala atual do react-zoom-pan-pinch — capturada via onTransformed e
  // exibida no toolbar. Antes era lida de `instance.transformState` que
  // não faz parte do tipo público (TS error).
  const [scale, setScale] = useState(1);
  const [confirmDeleteOpen, setConfirmDeleteOpen] = useState(false);
  const [deleting, setDeleting] = useState(false);
  const wrapperRef = useRef<ReactZoomPanPinchRef | null>(null);

  const isPdf = doc?.mime_type === 'application/pdf';
  const isImage = doc?.mime_type?.startsWith('image/');
  const fileUrl = doc?.file_url ?? (doc ? `/financeiro/domusia/file/${doc.id}` : null);

  useEffect(() => {
    setRotation(0);
    setScale(1);
    setConfirmDeleteOpen(false);
    if (doc) setLoading(true);
  }, [doc?.id]);

  const rotateLeft = useCallback(() => setRotation((r) => (r - 90 + 360) % 360), []);
  const rotateRight = useCallback(() => setRotation((r) => (r + 90) % 360), []);

  // Atalhos de teclado para a imagem ativa (apenas quando há imagem).
  // + / = zoom in, - / _ zoom out, 0 reset, r/R rotaciona.
  useEffect(() => {
    if (!isImage) return;
    const handler = (e: KeyboardEvent) => {
      const target = e.target as HTMLElement | null;
      if (target && /^(INPUT|TEXTAREA|SELECT)$/.test(target.tagName)) return;
      if (target?.isContentEditable) return;

      const ref = wrapperRef.current;
      if (!ref) return;

      switch (e.key) {
        case '+':
        case '=':
          e.preventDefault();
          ref.zoomIn(ZOOM_STEP);
          break;
        case '-':
        case '_':
          e.preventDefault();
          ref.zoomOut(ZOOM_STEP);
          break;
        case '0':
          e.preventDefault();
          ref.resetTransform();
          break;
        case 'r':
          e.preventDefault();
          rotateRight();
          break;
        case 'R':
          e.preventDefault();
          rotateLeft();
          break;
      }
    };
    window.addEventListener('keydown', handler);
    return () => window.removeEventListener('keydown', handler);
  }, [isImage, rotateLeft, rotateRight]);

  const requestDelete = useCallback(() => {
    if (!doc) return;
    setConfirmDeleteOpen(true);
  }, [doc]);

  const confirmDelete = useCallback(async () => {
    if (!doc || deleting) return;
    setDeleting(true);
    try {
      const csrfEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
      const res = await fetch(`/financeiro/domusia/${doc.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfEl?.content ?? '',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });
      if (!res.ok) throw new Error();
      notify.success('Documento excluído');
      setConfirmDeleteOpen(false);
      onDeleted();
    } catch {
      notify.error('Erro ao excluir documento');
      // Mantém o dialog aberto para o usuário tentar novamente.
    } finally {
      setDeleting(false);
    }
  }, [doc, deleting, onDeleted]);

  if (!doc) {
    return (
      <Card className="flex flex-col items-center justify-center h-[600px] bg-muted/30 gap-3">
        <FileSearch className="size-12 text-muted-foreground/30" />
        <p className="text-muted-foreground font-medium">Nenhum documento selecionado</p>
        <p className="text-xs text-muted-foreground/70">
          Clique em um documento da lista para visualizá-lo
        </p>
      </Card>
    );
  }

  return (
    <Card className="overflow-hidden relative">
      {isPdf && fileUrl ? (
        <>
          <PdfToolbar onDelete={requestDelete} />
          <div
            className="relative h-[600px] bg-[#2d2d2d]"
            style={{
              backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0)',
              backgroundSize: '20px 20px',
            }}
          >
            {loading && <LoadingOverlay />}
            <iframe
              src={`${fileUrl}#toolbar=1&navpanes=1&scrollbar=1&zoom=page-width`}
              className="w-full h-full border-0"
              onLoad={() => setLoading(false)}
            />
          </div>
        </>
      ) : isImage && fileUrl ? (
        <TransformWrapper
          ref={wrapperRef}
          initialScale={1}
          minScale={MIN_SCALE}
          maxScale={MAX_SCALE}
          centerOnInit
          centerZoomedOut
          smooth
          doubleClick={{ mode: 'zoomIn', step: 0.7, animationTime: 200 }}
          wheel={{ step: 0.12 }}
          pinch={{ step: 5 }}
          panning={{ velocityDisabled: true }}
          onTransform={(_ref: ReactZoomPanPinchRef, state: { scale: number }) =>
            setScale(state.scale)
          }
          key={doc.id}
        >
          <ImageToolbar
            rotation={rotation}
            scale={scale}
            onRotateLeft={rotateLeft}
            onRotateRight={rotateRight}
            onDelete={requestDelete}
          />
          <div
            className="relative h-[600px] bg-[#2d2d2d] overflow-hidden"
            style={{
              backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0)',
              backgroundSize: '20px 20px',
            }}
          >
            {loading && <LoadingOverlay />}
            <TransformComponent
              wrapperStyle={{ width: '100%', height: '100%' }}
              contentStyle={{
                width: '100%',
                height: '100%',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                cursor: scale > 1 ? 'grab' : 'zoom-in',
              }}
            >
              <img
                src={fileUrl}
                alt={doc.nome_arquivo}
                draggable={false}
                className="max-w-full max-h-[600px] select-none object-contain"
                style={{
                  transform: `rotate(${rotation}deg)`,
                  transition: 'transform 0.3s ease',
                }}
                onLoad={() => setLoading(false)}
                onError={() => setLoading(false)}
              />
            </TransformComponent>
          </div>
        </TransformWrapper>
      ) : null}

      <DeleteDocumentDialog
        open={confirmDeleteOpen}
        onOpenChange={(open) => {
          if (!deleting) setConfirmDeleteOpen(open);
        }}
        deleting={deleting}
        filename={doc?.nome_arquivo ?? null}
        onConfirm={confirmDelete}
      />
    </Card>
  );
}

function ImageToolbar({
  rotation: _rotation,
  scale,
  onRotateLeft,
  onRotateRight,
  onDelete,
}: {
  rotation: number;
  scale: number;
  onRotateLeft: () => void;
  onRotateRight: () => void;
  onDelete: () => void;
}) {
  const { zoomIn, zoomOut, resetTransform } = useControls();
  const zoomPct = Math.round(scale * 100);
  const atMin = scale <= MIN_SCALE + 0.001;
  const atMax = scale >= MAX_SCALE - 0.001;

  return (
    <div className="flex items-center justify-between px-4 py-2.5 border-b border-border bg-muted/30">
      <h3 className="text-sm font-semibold flex items-center gap-2">
        <FileSearch className="size-4 text-primary" />
        Visualização
        <span className="hidden lg:inline text-[10px] font-normal text-muted-foreground/70 ml-1">
          (atalhos: + / − / 0 / R)
        </span>
      </h3>
      <div className="flex items-center gap-1">
        <ToolbarBtn
          onClick={() => zoomOut(ZOOM_STEP)}
          title="Diminuir zoom (−)"
          disabled={atMin}
        >
          <ZoomOut />
        </ToolbarBtn>
        <button
          type="button"
          onClick={() => resetTransform()}
          title="Voltar para 100% (0)"
          className="text-xs font-semibold text-muted-foreground hover:text-foreground tabular-nums min-w-[44px] text-center px-1.5 py-0.5 rounded hover:bg-muted transition-colors"
        >
          {zoomPct}%
        </button>
        <ToolbarBtn
          onClick={() => zoomIn(ZOOM_STEP)}
          title="Aumentar zoom (+)"
          disabled={atMax}
        >
          <ZoomIn />
        </ToolbarBtn>
        <ToolbarBtn onClick={() => resetTransform()} title="Ajustar à tela (0)">
          <Maximize2 />
        </ToolbarBtn>
        <div className="w-px h-4 bg-border mx-1" />
        <ToolbarBtn onClick={onRotateLeft} title="Girar esquerda (Shift+R)">
          <RotateCcw />
        </ToolbarBtn>
        <ToolbarBtn onClick={onRotateRight} title="Girar direita (R)">
          <RotateCw />
        </ToolbarBtn>
        <div className="w-px h-4 bg-border mx-1" />
        <ToolbarBtn onClick={onDelete} title="Excluir" className="text-destructive hover:bg-destructive/10">
          <Trash2 />
        </ToolbarBtn>
      </div>
    </div>
  );
}

function PdfToolbar({ onDelete }: { onDelete: () => void }) {
  return (
    <div className="flex items-center justify-between px-4 py-2.5 border-b border-border bg-muted/30">
      <h3 className="text-sm font-semibold flex items-center gap-2">
        <FileSearch className="size-4 text-primary" />
        Visualização
      </h3>
      <div className="flex items-center gap-1">
        <ToolbarBtn onClick={onDelete} title="Excluir" className="text-destructive hover:bg-destructive/10">
          <Trash2 />
        </ToolbarBtn>
      </div>
    </div>
  );
}

function LoadingOverlay() {
  return (
    <div className="absolute inset-0 z-10 flex flex-col items-center justify-center bg-[#2d2d2d]/85 backdrop-blur-sm">
      <Loader2 className="size-8 text-blue-500 animate-spin mb-3" />
      <div className="flex flex-col gap-2 w-64">
        <div className="h-2.5 rounded bg-white/10 animate-pulse w-4/5 mx-auto" />
        <div className="h-2.5 rounded bg-white/10 animate-pulse w-full mx-auto" />
        <div className="h-2.5 rounded bg-white/10 animate-pulse w-3/5 mx-auto" />
      </div>
    </div>
  );
}

function ToolbarBtn({
  onClick,
  title,
  className,
  disabled,
  children,
}: {
  onClick: () => void;
  title: string;
  className?: string;
  disabled?: boolean;
  children: React.ReactNode;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      title={title}
      disabled={disabled}
      className={cn(
        'flex items-center justify-center size-7 rounded-md text-muted-foreground hover:text-foreground hover:bg-muted transition-colors',
        'disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-muted-foreground',
        '[&>svg]:size-3.5',
        className,
      )}
    >
      {children}
    </button>
  );
}

function DeleteDocumentDialog({
  open,
  onOpenChange,
  deleting,
  filename,
  onConfirm,
}: {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  deleting: boolean;
  filename: string | null;
  onConfirm: () => void;
}) {
  return (
    <AlertDialog open={open} onOpenChange={onOpenChange}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <div className="flex items-start gap-3">
            <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertTriangle className="size-5" />
            </span>
            <div className="flex-1 min-w-0">
              <AlertDialogTitle>Excluir documento?</AlertDialogTitle>
              <AlertDialogDescription className="mt-1.5">
                Esta ação não pode ser desfeita. O documento e os dados extraídos
                pela IA serão removidos permanentemente.
              </AlertDialogDescription>
            </div>
          </div>
        </AlertDialogHeader>

        {filename && (
          <div className="rounded-md border border-border bg-muted/40 px-3 py-2">
            <div className="text-[11px] uppercase tracking-wide text-muted-foreground mb-0.5">
              Arquivo
            </div>
            <div className="text-sm font-medium truncate" title={filename}>
              {filename}
            </div>
          </div>
        )}

        <AlertDialogFooter>
          <AlertDialogCancel disabled={deleting}>Cancelar</AlertDialogCancel>
          <AlertDialogAction
            variant="destructive"
            disabled={deleting}
            onClick={(e) => {
              // Impede o fechamento automático do Radix para que o dialog
              // permaneça aberto enquanto a requisição está em voo.
              e.preventDefault();
              onConfirm();
            }}
          >
            {deleting ? (
              <>
                <Loader2 className="size-4 animate-spin" />
                Excluindo...
              </>
            ) : (
              <>
                <Trash2 className="size-4" />
                Excluir
              </>
            )}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
