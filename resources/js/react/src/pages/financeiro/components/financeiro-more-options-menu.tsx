import { useRef } from 'react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { EllipsisVertical, Settings2, RefreshCw, CircleHelp } from 'lucide-react';
import { useAppData } from '@/hooks/useAppData';

/** Rotas tenant (sem prefixo /app), alinhadas a main-card.blade.php */
const HREF = {
  extrato: '/bank-statements',
  domusia: '/financeiro/domusia',
  horarioMissa: '/company/edit?tab=horario-missas',
  categorias: '/lancamentoPadrao',
  centros: '/costCenter',
  formasPagamento: '/formas-pagamento',
  formasRecebimento: '/formas-recebimento',
  entidades: '/app/financeiro/entidades',
  recalcularAction: '/financeiro/recalcular-saldos',
} as const;

export function FinanceiroMoreOptionsMenu() {
  const { csrfToken, hasAdminRole, hasGlobalRole } = useAppData();
  const formRef = useRef<HTMLFormElement>(null);

  function submitRecalcular() {
    if (
      !confirm(
        '⚠️ Deseja sincronizar todos os saldos com as movimentações?',
      )
    ) {
      return;
    }
    formRef.current?.requestSubmit();
  }

  return (
    <>
      <form
        ref={formRef}
        method="post"
        action={HREF.recalcularAction}
        className="hidden"
        aria-hidden
      >
        <input type="hidden" name="_token" value={csrfToken} />
      </form>

      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="outline" mode="icon" aria-label="Mais opções">
            <EllipsisVertical />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" className="w-56">
          <DropdownMenuLabel className="text-[0.65rem] font-semibold uppercase tracking-wide text-muted-foreground">
            Menu financeiro
          </DropdownMenuLabel>
          <DropdownMenuItem asChild>
            <a href={HREF.extrato}>Extrato</a>
          </DropdownMenuItem>
          <DropdownMenuItem asChild>
            <a
              href={HREF.domusia}
              className="flex items-center justify-between gap-2"
              title="Faça o lançamento de receitas e despesas com a ajuda da IA"
            >
              <span>Domus IA</span>
              <CircleHelp className="size-3.5 shrink-0 text-muted-foreground" aria-hidden />
            </a>
          </DropdownMenuItem>
          <DropdownMenuItem asChild>
            <a href={HREF.horarioMissa}>Horário de Missa</a>
          </DropdownMenuItem>

          <DropdownMenuSub>
            <DropdownMenuSubTrigger>
              <Settings2 className="size-4" />
              Configurações
            </DropdownMenuSubTrigger>
            <DropdownMenuSubContent className="w-52">
              <DropdownMenuItem asChild>
                <a href={HREF.categorias}>Categorias financeiras</a>
              </DropdownMenuItem>
              <DropdownMenuItem asChild>
                <a href={HREF.centros}>Centro de custos</a>
              </DropdownMenuItem>
              <DropdownMenuItem asChild>
                <a href={HREF.formasPagamento}>Formas de pagamento</a>
              </DropdownMenuItem>
              {hasGlobalRole ? (
                <DropdownMenuItem asChild>
                  <a href={HREF.formasRecebimento}>Formas de recebimento</a>
                </DropdownMenuItem>
              ) : null}
              <DropdownMenuItem asChild>
                <a href={HREF.entidades}>Entidades financeiras</a>
              </DropdownMenuItem>
            </DropdownMenuSubContent>
          </DropdownMenuSub>

          {hasGlobalRole ? (
            <>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onSelect={(e) => {
                  e.preventDefault();
                  submitRecalcular();
                }}
              >
                <RefreshCw className="size-4" />
                <span>Recalcular saldo</span>
                <CircleHelp
                  className="ms-auto size-3.5 text-muted-foreground"
                  aria-label="Sincroniza cache de saldos com movimentações (somente superadmin)"
                  aria-hidden
                />
              </DropdownMenuItem>
            </>
          ) : null}
        </DropdownMenuContent>
      </DropdownMenu>
    </>
  );
}
