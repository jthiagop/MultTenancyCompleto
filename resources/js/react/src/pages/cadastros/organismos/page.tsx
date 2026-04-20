import { useState } from 'react';
import { Navigate } from 'react-router-dom';
import { Plus } from 'lucide-react';
import {
  Toolbar,
  ToolbarActions,
  ToolbarDescription,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Button } from '@/components/ui/button';
import { useAppData } from '@/hooks/useAppData';
import { OrganismosTable } from './components/organismos-table';
import { OrganismoFormSheet } from './components/organismo-form-sheet';

export function OrganismosPage() {
  const { canCompanyIndex } = useAppData();
  const [refreshKey, setRefreshKey] = useState(0);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  // Guarda de rota: apenas usuários com permissão company.index podem acessar
  if (canCompanyIndex === false) {
    return <Navigate to="/dashboard" replace />;
  }

  function handleEdit(id: number) {
    setEditingId(id);
    setSheetOpen(true);
  }

  function handleCreate() {
    setEditingId(null);
    setSheetOpen(true);
  }

  function handleView(id: number) {
    // Mantém compatibilidade com a rota blade existente até migrarmos a view.
    window.location.assign(`/company/${id}`);
  }

  function handleSheetOpenChange(open: boolean) {
    setSheetOpen(open);
    if (!open) setEditingId(null);
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Organismos</ToolbarPageTitle>
          <ToolbarDescription>
            Administração de organismos da fraternidade (matrizes e filiais).
          </ToolbarDescription>
        </ToolbarHeading>
        <ToolbarActions>
          <Button
            size="sm"
            className="bg-blue-600 hover:bg-blue-700 text-white border-0"
            onClick={handleCreate}
          >
            <Plus className="size-4" />
            Novo Organismo
          </Button>
        </ToolbarActions>
      </Toolbar>

      <OrganismosTable
        refreshKey={refreshKey}
        onView={handleView}
        onEdit={handleEdit}
      />

      <OrganismoFormSheet
        open={sheetOpen}
        onOpenChange={handleSheetOpenChange}
        onSaved={() => setRefreshKey((k) => k + 1)}
        editingId={editingId}
      />
    </div>
  );
}
