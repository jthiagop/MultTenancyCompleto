import { useEffect, useState, useCallback, useMemo } from 'react';
import { FileText, FileImage, Upload, FolderOpen, MessageCircle } from 'lucide-react';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import {
  Item,
  ItemContent,
  ItemDescription,
  ItemGroup,
  ItemMedia,
  ItemTitle,
} from '@/components/ui/item';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

export interface DomusDocument {
  id: number;
  nome_arquivo: string;
  mime_type: string;
  tipo_documento: string | null;
  status: string;
  file_url: string | null;
  canal_origem: string;
  user_name: string | null;
  created_at: string;
  base64_content?: string;
  estabelecimento_nome?: string | null;
  valor_total?: number | null;
}

interface DocumentListProps {
  refreshKey: number;
  filterType: string;
  selectedId: number | null;
  onSelect: (doc: DomusDocument) => void;
}

export function DocumentList({ refreshKey, filterType, selectedId, onSelect }: DocumentListProps) {
  const [docs, setDocs] = useState<DomusDocument[]>([]);
  const [loading, setLoading] = useState(true);

  const loadDocs = useCallback(async () => {
    setLoading(true);
    try {
      const res = await fetch('/financeiro/domusia/list', {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok) throw new Error(`Erro ${res.status}`);
      const data = await res.json();
      setDocs(data.documentos ?? data.data ?? []);
    } catch {
      notify.error('Erro ao carregar documentos');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadDocs();
  }, [loadDocs, refreshKey]);

  const filtered = useMemo(
    () => (filterType ? docs.filter((d) => d.tipo_documento === filterType) : docs),
    [docs, filterType],
  );

  if (loading) {
    return (
      <ItemGroup className="gap-3">
        {Array.from({ length: 5 }).map((_, i) => (
          <Item key={i} variant="outline" className="animate-pulse">
            <ItemMedia>
              <Skeleton className="size-full rounded-lg" />
            </ItemMedia>
            <ItemContent>
              <Skeleton className="h-4 w-3/4" />
              <Skeleton className="h-3 w-1/2" />
            </ItemContent>
          </Item>
        ))}
      </ItemGroup>
    );
  }

  if (filtered.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center py-10 text-muted-foreground gap-2">
        <FolderOpen className="size-10 opacity-40" />
        <span className="text-sm">Nenhum documento encontrado</span>
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-2">
        <span className="text-xs font-semibold text-muted-foreground">Pendentes</span>
        <Badge variant="secondary" className="text-xs">
          {filtered.length}
        </Badge>
      </div>
      <ScrollArea className="h-[750px]">
        <ItemGroup className="gap-1.5 pr-2">
          {filtered.map((doc) => (
            <DocumentItem
              key={doc.id}
              doc={doc}
              selected={doc.id === selectedId}
              onClick={() => onSelect(doc)}
            />
          ))}
        </ItemGroup>
      </ScrollArea>
    </div>
  );
}

function DocumentItem({
  doc,
  selected,
  onClick,
}: {
  doc: DomusDocument;
  selected: boolean;
  onClick: () => void;
}) {
  const isPdf = doc.mime_type === 'application/pdf';
  const isWhatsApp = doc.canal_origem === 'whatsapp';
  const statusLabel = doc.status === 'processado' ? 'Processado' : 'Pendente';
  const statusClass =
    doc.status === 'processado'
      ? 'bg-green-500/10 text-green-600 border-green-500/20'
      : 'bg-yellow-500/10 text-yellow-600 border-yellow-500/20';

  return (
    <Item
      variant="outline"
      role="button"
      tabIndex={0}
      onClick={onClick}
      onKeyDown={(e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          onClick();
        }
      }}
      className={cn(
        'cursor-pointer transition-all relative',
        'hover:-translate-y-px hover:shadow-sm',
        selected && 'ring-2 ring-primary border-primary bg-primary/5 shadow-sm',
      )}
    >
      {/* Ícone do arquivo com badge de canal */}
      <ItemMedia variant="icon" className={cn(
        'relative overflow-visible',
        isPdf ? 'bg-destructive/10' : 'bg-primary/10',
      )}>
        {isPdf ? (
          <FileText className="size-4 text-destructive" />
        ) : (
          <FileImage className="size-4 text-primary" />
        )}
        <span
          className={cn(
            'absolute -top-1.5 -left-1.5 flex items-center justify-center size-4 rounded-full border-2 border-background',
            isWhatsApp ? 'bg-green-500' : 'bg-primary',
          )}
        >
          {isWhatsApp ? (
            <MessageCircle className="size-2 text-white" />
          ) : (
            <Upload className="size-2 text-white" />
          )}
        </span>
      </ItemMedia>

      {/* Conteúdo principal */}
      <ItemContent>
        <ItemTitle className="line-clamp-1">
          {doc.nome_arquivo}
        </ItemTitle>
        <ItemDescription>
          {doc.created_at}
          <span className="mx-1">·</span>
          {doc.user_name ?? 'Usuário'}
        </ItemDescription>
      </ItemContent>

      {/* Status + tipo */}
      <ItemContent className="flex-none items-end text-right gap-1">
        <Badge variant="outline" className={cn('text-[10px] py-0 px-1.5', statusClass)}>
          {statusLabel}
        </Badge>
        {doc.tipo_documento && (
          <ItemDescription className="text-[10px]">
            {doc.tipo_documento}
          </ItemDescription>
        )}
      </ItemContent>
    </Item>
  );
}
