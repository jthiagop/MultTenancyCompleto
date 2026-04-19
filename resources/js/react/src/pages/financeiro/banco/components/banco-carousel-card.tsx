import { useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { Building2, Flag, Landmark, Pencil, Wallet } from 'lucide-react';
import Autoplay from 'embla-carousel-autoplay';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Dialog,
  DialogBody,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip';
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from '@/components/ui/carousel';
import {
  Item,
  ItemContent,
  ItemDescription,
  ItemMedia,
  ItemTitle,
} from '@/components/ui/item';
import { useEntidades, type IEntidade } from '@/hooks/useEntidades';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';

const ACCOUNT_BADGE: Record<string, string> = {
  corrente:       'bg-blue-50  text-blue-700  dark:bg-blue-950/40  dark:text-blue-300',
  poupanca:       'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-300',
  aplicacao:      'bg-sky-50   text-sky-700   dark:bg-sky-950/40   dark:text-sky-300',
  renda_fixa:     'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
  tesouro_direto: 'bg-gray-100 text-gray-600  dark:bg-gray-800     dark:text-gray-300',
  caixa:          'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
};

function formatBRL(value: number) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2,
  }).format(value);
}

function EntidadeSkeleton() {
  return (
    <div className="flex items-center gap-3 p-3 rounded-lg border border-border min-w-[280px]">
      <Skeleton className="size-10 rounded-lg shrink-0" />
      <div className="flex-1 space-y-2">
        <Skeleton className="h-4 w-32" />
        <Skeleton className="h-3 w-24" />
      </div>
      <Skeleton className="h-4 w-20" />
    </div>
  );
}

function RenameDialog({
  open,
  onOpenChange,
  entidadeId,
  currentName,
  onRenamed,
}: {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  entidadeId: number;
  currentName: string;
  onRenamed: (newName: string) => void;
}) {
  const [nome, setNome] = useState(currentName);
  const [saving, setSaving] = useState(false);
  const { csrfToken } = useAppData();

  const handleSave = async () => {
    if (!nome.trim() || nome === currentName) return;
    setSaving(true);
    try {
      const res = await fetch(`/relatorios/entidades/${entidadeId}/renomear`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ nome: nome.trim() }),
        credentials: 'same-origin',
      });
      if (!res.ok) return;
      const json = await res.json();
      if (json.success) {
        onRenamed(json.nome ?? nome.trim());
        onOpenChange(false);
      }
    } catch {
      // silently ignore network errors
    } finally {
      setSaving(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="w-full max-w-sm">
        <DialogHeader>
          <DialogTitle>Renomear Entidade</DialogTitle>
          <DialogDescription>Informe o novo nome para a entidade financeira.</DialogDescription>
        </DialogHeader>
        <DialogBody>
          <Input
            value={nome}
            onChange={(e) => setNome(e.target.value)}
            placeholder="Nome da entidade"
            onKeyDown={(e) => { if (e.key === 'Enter') handleSave(); }}
            autoFocus
          />
        </DialogBody>
        <DialogFooter>
          <Button variant="outline" size="sm" onClick={() => onOpenChange(false)}>
            Cancelar
          </Button>
          <Button
            variant="primary"
            size="sm"
            onClick={handleSave}
            disabled={saving || !nome.trim() || nome === currentName}
          >
            {saving ? 'Salvando...' : 'Salvar'}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function EntidadeItem({
  entidade,
  onRenamed,
}: {
  entidade: IEntidade;
  onRenamed: (id: number, newName: string) => void;
}) {
  const [renameOpen, setRenameOpen] = useState(false);
  const badgeClass =
    ACCOUNT_BADGE[entidade.tipo === 'caixa' ? 'caixa' : (entidade.account_type ?? '')] ??
    ACCOUNT_BADGE.corrente;

  const hasPendencias = entidade.pendencias_conciliacao > 0;

  return (
    <>
      <Item variant="outline" className="group h-full" role="listitem">
        <Link
          to={`/financeiro/banco/entidade/${entidade.id}`}
          className="flex items-center gap-3 flex-1 min-w-0"
          title={`Abrir detalhes de ${entidade.nome}`}
        >
          <ItemMedia
            variant="image"
            className="size-10 bg-white/90 dark:bg-muted border border-border/50 p-1.5 shadow-sm"
          >
            {entidade.logo_url ? (
              <img
                src={entidade.logo_url}
                alt={entidade.banco_nome ?? entidade.nome}
                className="size-full object-contain mix-blend-multiply dark:mix-blend-normal"
                loading="lazy"
              />
            ) : entidade.tipo === 'caixa' ? (
              <Wallet className="size-5 text-emerald-600" />
            ) : (
              <Landmark className="size-5 text-blue-600" />
            )}
          </ItemMedia>

          <ItemContent>
            <div className="flex items-center gap-1.5">
              <ItemTitle className="line-clamp-1">{entidade.nome}</ItemTitle>
              {hasPendencias && (
                <TooltipProvider delayDuration={200}>
                  <Tooltip>
                    <TooltipTrigger asChild>
                      <span className="inline-flex items-center gap-1 shrink-0">
                        <Flag className="size-3 text-amber-500 fill-amber-500" />
                        <span className="text-[10px] font-semibold text-amber-600 dark:text-amber-400">
                          {entidade.pendencias_conciliacao}
                        </span>
                      </span>
                    </TooltipTrigger>
                    <TooltipContent side="top">
                      {entidade.pendencias_conciliacao} conciliaç{entidade.pendencias_conciliacao === 1 ? 'ão pendente' : 'ões pendentes'}
                    </TooltipContent>
                  </Tooltip>
                </TooltipProvider>
              )}
              <TooltipProvider delayDuration={200}>
                <Tooltip>
                  <TooltipTrigger asChild>
                    <button
                      type="button"
                      className="shrink-0 p-0.5 rounded text-muted-foreground hover:text-foreground opacity-0 group-hover:opacity-100 transition-all"
                      onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        setRenameOpen(true);
                      }}
                    >
                      <Pencil className="size-3" />
                    </button>
                  </TooltipTrigger>
                  <TooltipContent side="top">Renomear</TooltipContent>
                </Tooltip>
              </TooltipProvider>
            </div>
            <ItemDescription>
              {entidade.tipo === 'banco' && entidade.agencia ? (
                <>Ag {entidade.agencia} · Cc {entidade.conta}</>
              ) : (
                <span
                  className={cn(
                    'inline-flex items-center gap-1 text-[11px] font-medium rounded-full px-1.5 py-px',
                    badgeClass,
                  )}
                >
                  {entidade.tipo === 'caixa' ? (
                    <Wallet className="size-2.5" />
                  ) : (
                    <Building2 className="size-2.5" />
                  )}
                  {entidade.account_label}
                </span>
              )}
            </ItemDescription>
          </ItemContent>
        </Link>

        <ItemContent className="flex-none items-end gap-1 text-right">
          <span
            className={cn(
              'text-sm font-bold tabular-nums tracking-tight',
              entidade.saldo_negativo ? 'text-destructive' : 'text-foreground',
            )}
          >
            {formatBRL(entidade.saldo_atual)}
          </span>
          {entidade.tipo === 'banco' && entidade.agencia && (
            <span
              className={cn(
                'inline-flex items-center gap-1 text-[11px] font-medium rounded-full px-1.5 py-px',
                badgeClass,
              )}
            >
              <Building2 className="size-2.5" />
              {entidade.account_label}
            </span>
          )}
        </ItemContent>

      </Item>

      <RenameDialog
        open={renameOpen}
        onOpenChange={setRenameOpen}
        entidadeId={entidade.id}
        currentName={entidade.nome}
        onRenamed={(newName) => onRenamed(entidade.id, newName)}
      />
    </>
  );
}

export function BancoCarouselCard({ refreshKey }: { refreshKey?: number } = {}) {
  const { entidades, loading, error, refetch } = useEntidades(refreshKey);

  const autoplayPlugin = useRef(
    Autoplay({ delay: 9000, stopOnInteraction: true, stopOnMouseEnter: true }),
  );

  const handleRenamed = (_id: number, _newName: string) => {
    refetch();
  };

  if (loading) {
    return (
      <div className="flex gap-2">
        {[1, 2, 3].map((i) => (
          <EntidadeSkeleton key={i} />
        ))}
      </div>
    );
  }

  if (error || entidades.length === 0) {
    return null;
  }

  return (
    <Carousel
      opts={{ align: 'start', loop: entidades.length > 3 }}
      plugins={[autoplayPlugin.current]}
      className="w-full"
      onMouseEnter={autoplayPlugin.current.stop}
      onMouseLeave={autoplayPlugin.current.reset}
    >
      <div className="relative flex items-center gap-1">
        {entidades.length > 3 && (
          <CarouselPrevious className="static translate-y-0 size-7 shrink-0 bg-background/80 hover:bg-background shadow-sm border-border/50" />
        )}

        <CarouselContent className="-ml-2">
          {entidades.map((ent) => (
            <CarouselItem key={ent.id} className="pl-2 basis-auto min-w-[360px] max-w-[460px]">
              <EntidadeItem entidade={ent} onRenamed={handleRenamed} />
            </CarouselItem>
          ))}
        </CarouselContent>

        {entidades.length > 3 && (
          <CarouselNext className="static translate-y-0 size-7 shrink-0 bg-background/80 hover:bg-background shadow-sm border-border/50" />
        )}
      </div>
    </Carousel>
  );
}
