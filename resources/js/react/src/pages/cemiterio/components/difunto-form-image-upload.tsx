import { useCallback, useEffect, useRef, useState } from 'react';
import {
  closestCenter,
  DndContext,
  type DragEndEvent,
  DragOverlay,
  type DragStartEvent,
  PointerSensor,
  useSensor,
  useSensors,
} from '@dnd-kit/core';
import {
  arrayMove,
  rectSortingStrategy,
  SortableContext,
  useSortable,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import {
  CircleX,
  CloudUpload,
  GripVertical,
  ImageIcon,
  TriangleAlert,
  XIcon,
} from 'lucide-react';
import {
  Alert,
  AlertContent,
  AlertDescription,
  AlertIcon,
  AlertTitle,
  AlertToolbar,
} from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';

export interface DifuntoImageFile {
  id: string;
  file?: File;
  preview: string;
  progress: number;
  status: 'uploading' | 'completed' | 'error' | 'existing';
  error?: string;
  isExisting?: boolean;
  originalUrl?: string;
}

interface DifuntoFormImageUploadProps {
  maxFiles?: number;
  maxSize?: number;
  accept?: string;
  className?: string;
  initialImages?: { id: string; url: string; name?: string }[];
  onImagesChange?: (images: DifuntoImageFile[]) => void;
  onUploadComplete?: (images: DifuntoImageFile[]) => void;
  onStateChange?: (kept: string[], newFiles: File[]) => void;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// ── Sortable Item ─────────────────────────────────────────────────────────────

function SortableImageItem({
  imageFile,
  onRemove,
}: {
  imageFile: DifuntoImageFile;
  onRemove: (id: string) => void;
}) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id: imageFile.id });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition: transition || 'transform 200ms ease',
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      className={cn(
        'flex items-center justify-center rounded-md bg-accent/50 shadow-none shrink-0 relative group border border-border',
        isDragging && 'opacity-50 z-50 cursor-grabbing transition-none',
      )}
    >
      <img
        src={imageFile.preview}
        alt={imageFile.file?.name ?? 'Imagem'}
        className="h-30 w-full object-cover rounded-md pointer-events-none"
      />

      {/* Drag Handle */}
      <Button
        type="button"
        {...attributes}
        {...listeners}
        variant="outline"
        size="icon"
        className="shadow-sm absolute top-2 start-2 size-6 opacity-0 group-hover:opacity-100 cursor-grab active:cursor-grabbing rounded-full"
      >
        <GripVertical className="size-3.5" />
      </Button>

      {/* Remove Button */}
      <Button
        type="button"
        onClick={() => onRemove(imageFile.id)}
        variant="outline"
        size="icon"
        className="shadow-sm absolute top-2 end-2 size-6 opacity-0 group-hover:opacity-100 rounded-full"
      >
        <XIcon className="size-3.5" />
      </Button>
    </div>
  );
}

// ── Main Component ────────────────────────────────────────────────────────────

export function DifuntoFormImageUpload({
  maxFiles = 5,
  maxSize = 5 * 1024 * 1024,
  accept = 'image/*',
  className,
  initialImages,
  onImagesChange,
  onUploadComplete,
  onStateChange,
}: DifuntoFormImageUploadProps) {
  const [allImages, setAllImages] = useState<DifuntoImageFile[]>([]);
  const [isDragging, setIsDragging] = useState(false);
  const [errors, setErrors] = useState<string[]>([]);
  const [activeId, setActiveId] = useState<string | null>(null);

  // DnD sensors — ativa após 3px de movimentação para não conflitar com click
  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { distance: 3 } }),
  );

  // Limpa itens undefined (segurança)
  useEffect(() => {
    setAllImages((prev) => prev.filter((item) => item && item.id));
  }, []);

  // Carrega imagens iniciais (modo edição)
  useEffect(() => {
    if (!initialImages || initialImages.length === 0) return;
    setAllImages(
      initialImages.map((img) => ({
        id: img.id,
        preview: img.url,
        progress: 100,
        status: 'existing' as const,
        isExisting: true,
        originalUrl: img.id,
        file: undefined,
      })),
    );
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // Notifica o pai sempre que allImages mudar (ref evita loop infinito)
  const onStateChangeRef = useRef(onStateChange);
  useEffect(() => { onStateChangeRef.current = onStateChange; });
  useEffect(() => {
    const cb = onStateChangeRef.current;
    if (!cb) return;
    const kept = allImages
      .filter((img) => img.isExisting && img.originalUrl)
      .map((img) => img.originalUrl as string);
    const newFiles = allImages
      .filter((img) => !img.isExisting && img.file)
      .map((img) => img.file as File);
    cb(kept, newFiles);
  }, [allImages]);

  // Cursor grabbing global durante drag
  useEffect(() => {
    document.body.style.cursor = activeId ? 'grabbing' : '';
    return () => { document.body.style.cursor = ''; };
  }, [activeId]);

  const validateFile = useCallback(
    (file: File, currentCount: number): string | null => {
      if (!file.type.startsWith('image/')) return 'O arquivo deve ser uma imagem.';
      if (file.size > maxSize) return `Tamanho máximo: ${formatBytes(maxSize)}.`;
      if (currentCount >= maxFiles) return `Máximo de ${maxFiles} imagem(ns) permitidas.`;
      return null;
    },
    [maxFiles, maxSize],
  );

  const simulateUpload = useCallback(
    (imageFile: DifuntoImageFile) => {
      let progress = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress >= 100) {
          progress = 100;
          clearInterval(interval);
          setAllImages((prev) => {
            const updated = prev.map((img) =>
              img.id === imageFile.id
                ? { ...img, progress: 100, status: 'completed' as const }
                : img,
            );
            if (updated.every((img) => img.status === 'completed')) {
              onUploadComplete?.(updated);
            }
            return updated;
          });
        } else {
          setAllImages((prev) =>
            prev.map((img) =>
              img.id === imageFile.id ? { ...img, progress } : img,
            ),
          );
        }
      }, 100);
    },
    [onUploadComplete],
  );

  const addImages = useCallback(
    (files: FileList | File[]) => {
      setAllImages((prev) => {
        const newImages: DifuntoImageFile[] = [];
        const newErrors: string[] = [];

        Array.from(files).forEach((file) => {
          const error = validateFile(file, prev.length + newImages.length);
          if (error) {
            newErrors.push(`${file.name}: ${error}`);
            return;
          }
          newImages.push({
            id: `${Date.now()}-${Math.random()}`,
            file,
            preview: URL.createObjectURL(file),
            progress: 0,
            status: 'uploading',
          });
        });

        if (newErrors.length > 0) {
          setErrors((e) => [...e, ...newErrors]);
        }

        if (newImages.length === 0) return prev;

        const updated = [...prev, ...newImages];
        onImagesChange?.(updated);
        newImages.forEach((img) => simulateUpload(img));
        return updated;
      });
    },
    [validateFile, simulateUpload, onImagesChange],
  );

  const removeImage = useCallback((id: string) => {
    setAllImages((prev) => {
      const img = prev.find((i) => i.id === id);
      if (img && !img.isExisting) URL.revokeObjectURL(img.preview);
      return prev.filter((i) => i.id !== id);
    });
  }, []);

  // DnD reorder handlers
  const handleDragStart = (event: DragStartEvent) => {
    setActiveId(event.active.id as string);
  };

  const handleDragEnd = (event: DragEndEvent) => {
    const { active, over } = event;
    setActiveId(null);
    if (active.id !== over?.id) {
      setAllImages((prev) => {
        const oldIndex = prev.findIndex((item) => item.id === active.id);
        const newIndex = prev.findIndex((item) => item.id === over?.id);
        if (oldIndex !== -1 && newIndex !== -1) {
          return arrayMove(prev, oldIndex, newIndex);
        }
        return prev;
      });
    }
  };

  // Drop-zone handlers (para soltar arquivos do SO)
  const handleDragEnter = useCallback((e: React.DragEvent) => {
    e.preventDefault(); e.stopPropagation(); setIsDragging(true);
  }, []);
  const handleDragLeave = useCallback((e: React.DragEvent) => {
    e.preventDefault(); e.stopPropagation(); setIsDragging(false);
  }, []);
  const handleDragOver = useCallback((e: React.DragEvent) => {
    e.preventDefault(); e.stopPropagation();
  }, []);
  const handleDrop = useCallback(
    (e: React.DragEvent) => {
      e.preventDefault(); e.stopPropagation(); setIsDragging(false);
      if (e.dataTransfer.files.length > 0) addImages(e.dataTransfer.files);
    },
    [addImages],
  );

  const openFileDialog = useCallback(() => {
    const input = document.createElement('input');
    input.type = 'file';
    input.multiple = true;
    input.accept = accept;
    input.onchange = (e) => {
      const target = e.target as HTMLInputElement;
      if (target.files) addImages(target.files);
    };
    input.click();
  }, [accept, addImages]);

  const sortableItems = allImages.filter((item) => item && item.id);

  return (
    <div className={cn('w-full max-w-4xl', className)}>

      {/* ── Grid de imagens com DnD ── */}
      <div className="mb-6">
        <DndContext
          sensors={sensors}
          collisionDetection={closestCenter}
          onDragStart={handleDragStart}
          onDragEnd={handleDragEnd}
        >
          <SortableContext
            items={sortableItems.map((item) => item.id)}
            strategy={rectSortingStrategy}
          >
            <div className="grid grid-cols-2 gap-2.5 transition-all duration-200 ease-in-out">
              {sortableItems.map((item) => (
                <SortableImageItem
                  key={item.id}
                  imageFile={item}
                  onRemove={removeImage}
                />
              ))}
            </div>
          </SortableContext>

          <DragOverlay>
            {activeId ? (
              <div className="flex items-center justify-center rounded-md bg-accent/40 shadow-lg border border-border opacity-90">
                <img
                  src={sortableItems.find((i) => i.id === activeId)?.preview ?? ''}
                  className="h-30 w-full object-cover rounded-md pointer-events-none"
                  alt="Arrastando"
                />
              </div>
            ) : null}
          </DragOverlay>
        </DndContext>

        {/* Cards de progresso de upload */}
        {allImages.length > 0 && (
          <div className="mt-6 space-y-3">
            {allImages.filter((img) => !img.isExisting).map((imageFile) => (
              <Card key={imageFile.id} className="bg-accent/20 shadow-none rounded-md">
                <CardContent className="flex items-center gap-2 p-3">
                  <div className="flex items-center justify-center size-8 rounded-md border border-border shrink-0">
                    <ImageIcon className="size-4 text-muted-foreground" />
                  </div>
                  <div className="flex flex-col gap-1.5 w-full">
                    <div className="flex items-center justify-between gap-2.5 -mt-2 w-full">
                      <div className="flex items-center gap-2.5">
                        <span className="text-xs text-foreground font-medium leading-none">
                          {imageFile.file?.name ?? 'Imagem'}
                        </span>
                        <span className="text-xs text-muted-foreground font-normal leading-none">
                          {imageFile.file ? formatBytes(imageFile.file.size) : ''}
                        </span>
                        {imageFile.status === 'uploading' && (
                          <p className="text-xs text-muted-foreground">
                            Enviando… {Math.round(imageFile.progress)}%
                          </p>
                        )}
                      </div>
                      <Button
                        type="button"
                        onClick={() => removeImage(imageFile.id)}
                        variant="ghost"
                        size="icon"
                        className="size-6"
                      >
                        <CircleX className="size-3.5" />
                      </Button>
                    </div>
                    <Progress
                      value={imageFile.progress}
                      className={cn('h-1 transition-all duration-300', '[&>div]:bg-zinc-950')}
                    />
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>

      {/* ── Zona de drop de arquivo ── */}
      <Card
        className={cn(
          'border-dashed shadow-none rounded-md bg-accent/20 transition-colors',
          isDragging
            ? 'border-primary bg-primary/5'
            : 'border-muted-foreground/25 hover:border-muted-foreground/50',
        )}
        onDragEnter={handleDragEnter}
        onDragLeave={handleDragLeave}
        onDragOver={handleDragOver}
        onDrop={handleDrop}
      >
        <CardContent className="text-center">
          <div className="flex items-center justify-center size-8 rounded-full border border-border mx-auto mb-3">
            <CloudUpload className="size-4" />
          </div>
          <h3 className="text-2sm text-foreground font-semibold mb-0.5">
            Escolha um arquivo ou arraste aqui.
          </h3>
          <span className="text-xs text-secondary-foreground font-normal block mb-3">
            JPEG, PNG — máx. {formatBytes(maxSize)}.
          </span>
          <Button type="button" size="sm" variant="outline" onClick={openFileDialog}>
            Selecionar arquivo
          </Button>
        </CardContent>
      </Card>

      {/* ── Erros ── */}
      {errors.length > 0 && (
        <Alert variant="destructive" appearance="light" className="mt-5">
          <AlertIcon>
            <TriangleAlert />
          </AlertIcon>
          <AlertContent>
            <AlertTitle>Erro(s) no upload</AlertTitle>
            <AlertDescription>
              {errors.map((error, index) => (
                <p key={index} className="last:mb-0">
                  {error}
                </p>
              ))}
            </AlertDescription>
          </AlertContent>
          <AlertToolbar>
            <Button
              type="button"
              variant="ghost"
              size="icon"
              className="size-6 shrink-0"
              onClick={() => setErrors([])}
              aria-label="Fechar"
            >
              <XIcon className="size-3.5" />
            </Button>
          </AlertToolbar>
        </Alert>
      )}
    </div>
  );
}
