import { useState } from 'react';
import {
  Toolbar,
  ToolbarActions,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { NovoLancamentoDropdown } from './components/novo-lancamento-dropdown';
import { LancamentoDrawer, type TipoLancamento } from './components/lancamento-drawer';
import { TransferenciaSheet } from './components/transferencia-sheet';
import { FinanceiroBreadcrumb } from '@/pages/financeiro/components/financeiro-breadcrumb';

export function BancoPage() {
  const [drawerTipo, setDrawerTipo] = useState<TipoLancamento | null>(null);
  const [transferenciaOpen, setTransferenciaOpen] = useState(false);

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Contas Bancárias</ToolbarPageTitle>
          <FinanceiroBreadcrumb currentLabel="Contas Bancárias" />
        </ToolbarHeading>
        <ToolbarActions>
          <NovoLancamentoDropdown
            onSelect={(tipo) => setDrawerTipo(tipo)}
            onTransferencia={() => setTransferenciaOpen(true)}
          />
        </ToolbarActions>
      </Toolbar>

      {/* TODO: implementar listagem das contas bancárias */}
      <div className="text-muted-foreground text-sm py-8 text-center">
        Em construção.
      </div>

      <LancamentoDrawer
        open={drawerTipo !== null}
        tipo={drawerTipo}
        onClose={() => setDrawerTipo(null)}
      />

      <TransferenciaSheet open={transferenciaOpen} onOpenChange={setTransferenciaOpen} />
    </div>
  );
}
