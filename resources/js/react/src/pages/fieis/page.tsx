import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { UserPlus } from 'lucide-react';
import {
  Toolbar,
  ToolbarActions,
  ToolbarDescription,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Button } from '@/components/ui/button';
import { useAppData } from '@/hooks/useAppData';
import { CadastroFielSheet } from '@/pages/fieis/components/cadastro-fiel-sheet';

/**
 * Cadastro de fiéis — listagem e formulários serão migrados do Blade gradualmente.
 * Rota Laravel nomeada: fieis.index → GET /app/fieis
 */
export function FieisPage() {
  const navigate = useNavigate();
  const { canFieisIndex } = useAppData();
  const [cadastroFielOpen, setCadastroFielOpen] = useState(false);

  function handleFielSaved() {}

  if (canFieisIndex === false) {
    navigate('/dashboard', { replace: true });
    return null;
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Cadastro de fiéis</ToolbarPageTitle>
          <ToolbarDescription>
            Módulo em migração para o painel React. Em breve: listagem, filtros e cadastro aqui.
          </ToolbarDescription>
        </ToolbarHeading>
        <ToolbarActions>
          <Button
            type="button"
            size="sm"
            className="bg-blue-600 hover:bg-blue-700 text-white border-0 shadow-none"
            onClick={() => setCadastroFielOpen(true)}
          >
            <UserPlus className="size-4" />
            Cadastro de fiel
          </Button>
        </ToolbarActions>
      </Toolbar>

      <CadastroFielSheet
        open={cadastroFielOpen}
        onOpenChange={setCadastroFielOpen}
        onSaved={handleFielSaved}
      />
    </div>
  );
}
