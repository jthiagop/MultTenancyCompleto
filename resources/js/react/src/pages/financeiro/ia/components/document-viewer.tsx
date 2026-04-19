import { useState, useRef, useCallback, useEffect } from 'react';
import {
  TransformWrapper,
  TransformComponent,
  useControls,
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
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import { Card } from '@/components/ui/card';
import type { DomusDocument } from './document-list';

interface DocumentViewerProps {
  doc: DomusDocument | null;
  onDeleted: () => void;
}

export function DocumentViewer({ doc, onDeleted }: DocumentViewerProps) {
  const [loading, setLoading] = useState(false);
  const [rotation, setRotation] = useState(0);
  const wrapperRef = useRef<{ resetTransform: () => void } | null>(null);

  const isPdf = doc?.mime_type === 'application/pdf';
  const isImage = doc?.mime_type?.startsWith('image/');
  const fileUrl = doc?.file_url ?? (doc ? `/financeiro/domusia/file/${doc.id}` : null);

  useEffect(() => {
    setRotation(0);
    if (doc) setLoading(true);
  }, [doc?.id]);

  const rotateLeft = useCallback(() => setRotation((r) => (r - 90 + 360) % 360), []);
  const rotateRight = useCallback(() => setRotation((r) => (r + 90) % 360), []);

  const handleDelete = useCallback(async () => {
    if (!doc) return;
    if (!confirm('Tem certeza que deseja excluir este documento?')) return;

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
      onDeleted();
    } catch {
      notify.error('Erro ao excluir documento');
    }
  }, [doc, onDeleted]);

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
          <PdfToolbar onDelete={handleDelete} />
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
          ref={wrapperRef as never}
          initialScale={1}
          minScale={0.25}
          maxScale={5}
          centerOnInit
          doubleClick={{ mode: 'toggle', step: 1 }}
          wheel={{ step: 0.08 }}
          panning={{ velocityDisabled: true }}
          key={doc.id}
        >
          <ImageToolbar
            rotation={rotation}
            onRotateLeft={rotateLeft}
            onRotateRight={rotateRight}
            onDelete={handleDelete}
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
    </Card>
  );
}

function ImageToolbar({
  rotation,
  onRotateLeft,
  onRotateRight,
  onDelete,
}: {
  rotation: number;
  onRotateLeft: () => void;
  onRotateRight: () => void;
  onDelete: () => void;
}) {
  const { zoomIn, zoomOut, resetTransform, instance } = useControls();
  const scale = instance?.transformState?.scale ?? 1;
  const zoomPct = Math.round(scale * 100);

  return (
    <div className="flex items-center justify-between px-4 py-2.5 border-b border-border bg-muted/30">
      <h3 className="text-sm font-semibold flex items-center gap-2">
        <FileSearch className="size-4 text-primary" />
        Visualização
      </h3>
      <div className="flex items-center gap-1">
        <ToolbarBtn onClick={() => zoomOut(0.3)} title="Diminuir Zoom"><ZoomOut /></ToolbarBtn>
        <span className="text-xs font-semibold text-muted-foreground tabular-nums min-w-[40px] text-center">
          {zoomPct}%
        </span>
        <ToolbarBtn onClick={() => zoomIn(0.3)} title="Aumentar Zoom"><ZoomIn /></ToolbarBtn>
        <ToolbarBtn onClick={() => resetTransform()} title="Ajustar"><Maximize2 /></ToolbarBtn>
        <div className="w-px h-4 bg-border mx-1" />
        <ToolbarBtn onClick={onRotateLeft} title="Girar Esquerda"><RotateCcw /></ToolbarBtn>
        <ToolbarBtn onClick={onRotateRight} title="Girar Direita"><RotateCw /></ToolbarBtn>
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
  children,
}: {
  onClick: () => void;
  title: string;
  className?: string;
  children: React.ReactNode;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      title={title}
      className={cn(
        'flex items-center justify-center size-7 rounded-md text-muted-foreground hover:text-foreground hover:bg-muted transition-colors',
        '[&>svg]:size-3.5',
        className,
      )}
    >
      {children}
    </button>
  );
}
