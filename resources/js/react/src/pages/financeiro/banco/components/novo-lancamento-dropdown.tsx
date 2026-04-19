import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '../../../../components/ui/dropdown-menu';
import { Button } from '../../../../components/ui/button';
import { ChevronDown, PlusCircle } from 'lucide-react';
import type { TipoLancamento } from './lancamento-drawer';

interface NovoLancamentoDropdownProps {
  onSelect: (tipo: TipoLancamento) => void;
  onTransferencia: () => void;
}

export function NovoLancamentoDropdown({ onSelect, onTransferencia }: NovoLancamentoDropdownProps) {
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button size="sm">
          <PlusCircle className="size-4" />
          Novo Lançamento
          <ChevronDown className="size-3.5 opacity-70" />
        </Button>
      </DropdownMenuTrigger>

      <DropdownMenuContent align="end" className="w-52">
        <div className="px-2 pt-1.5 pb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">
          O que deseja criar?
        </div>

        <DropdownMenuItem onClick={() => onSelect('receita')}>
          <i className="fa-regular fa-circle-up text-success" />
          Nova Receita
        </DropdownMenuItem>

        <DropdownMenuItem onClick={() => onSelect('despesa')}>
          <i className="fa-regular fa-circle-down text-danger" />
          Nova Despesa
        </DropdownMenuItem>

        <DropdownMenuSeparator />

        <DropdownMenuItem onClick={() => onTransferencia()}>
          <i className="bi bi-arrow-left-right text-info" />
          Transferência
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
