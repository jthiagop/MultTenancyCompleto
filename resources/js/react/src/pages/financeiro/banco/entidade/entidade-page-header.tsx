import { useNavigate } from 'react-router-dom';
import { ArrowLeft, Landmark, Wallet } from 'lucide-react';
import {
  Toolbar,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FinanceiroBreadcrumb } from '@/pages/financeiro/components/financeiro-breadcrumb';
import { useEntidades, type IEntidade } from '@/hooks/useEntidades';
import { cn } from '@/lib/utils';

function agenciaContaLine(e: IEntidade): string | null {
  if (e.tipo === 'banco' && e.agencia) {
    const cc = e.conta ?? '—';
    return `Ag ${e.agencia} · Cc ${cc}`;
  }
  if (e.tipo === 'caixa') {
    return e.account_label || null;
  }
  return null;
}

function EntidadeSelectDisplay({ entidade, className }: { entidade: IEntidade; className?: string }) {
  const sub = agenciaContaLine(entidade);

  return (
    <div className={cn('flex min-w-0 flex-1 items-center gap-2.5', className)}>
      <div className="size-9 shrink-0 overflow-hidden rounded-md border border-border/50 bg-background p-1">
        {entidade.logo_url ? (
          <img
            src={entidade.logo_url}
            alt=""
            className="size-full object-contain mix-blend-multiply dark:mix-blend-normal"
            loading="lazy"
          />
        ) : (
          <div className="flex size-full items-center justify-center">
            {entidade.tipo === 'caixa' ? (
              <Wallet className="size-4 text-emerald-600" />
            ) : (
              <Landmark className="size-4 text-blue-600" />
            )}
          </div>
        )}
      </div>
      <div className="min-w-0 flex-1 text-left">
        <p className="truncate text-sm font-medium leading-tight text-foreground">{entidade.nome}</p>
        {sub ? (
          <p className="truncate text-[11px] text-muted-foreground leading-snug">{sub}</p>
        ) : null}
      </div>
    </div>
  );
}

interface EntidadePageHeaderProps {
  entidadeId: string | undefined;
}

const ENTIDADE_SELECT_ID = 'entidade-page-select';

export function EntidadePageHeader({ entidadeId }: EntidadePageHeaderProps) {
  const navigate = useNavigate();
  const { entidades, loading: loadingEntidades } = useEntidades();

  return (
    <Toolbar>
      <div className="flex w-full flex-col gap-4 xl:flex-row xl:items-center xl:justify-between xl:gap-6">
        <div className="flex min-w-0 flex-wrap items-center gap-3">
          <Button
            variant="outline"
            size="icon"
            onClick={() => navigate('/financeiro')}
            className="size-9 shrink-0 rounded-full"
            aria-label="Voltar"
          >
            <ArrowLeft className="size-4" />
          </Button>

          <ToolbarHeading>
            <ToolbarPageTitle>Detalhes da Conta</ToolbarPageTitle>
            <FinanceiroBreadcrumb currentLabel="Detalhes da Conta" />
          </ToolbarHeading>
        </div>

        <div className="flex w-full flex-col gap-1.5 sm:w-auto sm:min-w-70 sm:max-w-md xl:shrink-0">
          <Label
            htmlFor={ENTIDADE_SELECT_ID}
            className="text-sm font-medium text-muted-foreground"
          >
            Conta
          </Label>
          <Select
            indicatorPosition="right"
            value={entidadeId ?? undefined}
            onValueChange={(v) => navigate(`/financeiro/banco/entidade/${v}`)}
            disabled={loadingEntidades || entidades.length === 0}
          >
            <SelectTrigger
              id={ENTIDADE_SELECT_ID}
              size="lg"
              className="h-auto min-h-11 w-full py-2 [&>span]:flex [&>span]:min-w-0 [&>span]:flex-1 [&>span]:items-center"
            >
              <SelectValue placeholder="Selecione um banco" />
            </SelectTrigger>
            <SelectContent position="popper" className="min-w-(--radix-select-trigger-width)">
              {entidades.map((e) => (
                <SelectItem key={e.id} value={String(e.id)} className="cursor-pointer py-2">
                  <EntidadeSelectDisplay entidade={e} />
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
      </div>
    </Toolbar>
  );
}
