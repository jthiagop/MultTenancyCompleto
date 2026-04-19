import {
  Toolbar,
  ToolbarDescription,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { ModulesGrid } from '@/components/layouts/layout-1/components/modules-grid';
import { useAppData } from '@/hooks/useAppData';

function saudacaoPorHorario(): 'Bom dia' | 'Boa tarde' | 'Boa noite' {
  const h = new Date().getHours();
  if (h >= 5 && h < 12) return 'Bom dia';
  if (h >= 12 && h < 18) return 'Boa tarde';
  return 'Boa noite';
}

export function Layout1Page() {
  const { user } = useAppData();

  const firstName = user.name.split(' ')[0];
  const saudacao = saudacaoPorHorario();

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>{`Olá, ${firstName}! 👋 ${saudacao}`}</ToolbarPageTitle>
          <ToolbarDescription>Selecione um módulo para começar</ToolbarDescription>
        </ToolbarHeading>
      </Toolbar>

      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 lg:gap-7.5">
        <ModulesGrid />
      </div>
    </div>
  );
}