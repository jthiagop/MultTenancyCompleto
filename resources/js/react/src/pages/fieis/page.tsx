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
import { FieisChartsPanel } from '@/pages/fieis/components/fieis-charts-panel';
import { DeleteFielDialog } from '@/pages/fieis/components/delete-fiel-dialog';
import { FieisTable } from '@/pages/fieis/components/fieis-table';

/**
 * Cadastro de fiéis — listagem (DataGrid) + cadastro/edição/exclusão.
 * Rota Laravel nomeada: fieis.index → GET /app/fieis
 */
export function FieisPage() {
  const navigate = useNavigate();
  const { canFieisIndex } = useAppData();

  const [refreshKey, setRefreshKey] = useState(0);

  // ── Sheet de cadastro / edição ────────────────────────────────────────────
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  // ── Diálogo de exclusão ───────────────────────────────────────────────────
  const [deleteOpen, setDeleteOpen] = useState(false);
  const [deleteTarget, setDeleteTarget] = useState<{ id: number; nome: string } | null>(null);

  function handleFielSaved() {
    setRefreshKey((k) => k + 1);
  }

  function openCreate() {
    setEditingId(null);
    setSheetOpen(true);
  }

  function handleEdit(id: number) {
    setEditingId(id);
    setSheetOpen(true);
  }

  function handleView(id: number) {
    window.location.href = `/relatorios/fieis/${id}/edit`;
  }

  function handleDelete(id: number, nome: string) {
    setDeleteTarget({ id, nome });
    setDeleteOpen(true);
  }

  function handleSheetOpenChange(open: boolean) {
    setSheetOpen(open);
    if (!open) setEditingId(null);
  }

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
            Gerencie os fiéis cadastrados no organismo ativo.
          </ToolbarDescription>
        </ToolbarHeading>
        <ToolbarActions>
          <Button
            type="button"
            size="sm"
            className="bg-blue-600 hover:bg-blue-700 text-white border-0 shadow-none"
            onClick={openCreate}
          >
            <UserPlus className="size-4" />
            Cadastro de fiel
          </Button>
        </ToolbarActions>
      </Toolbar>

      <div className="mb-4">
        <FieisChartsPanel refreshKey={refreshKey} />
      </div>

      <FieisTable
        refreshKey={refreshKey}
        onEdit={handleEdit}
        onView={handleView}
        onDelete={handleDelete}
      />

      <CadastroFielSheet
        open={sheetOpen}
        onOpenChange={handleSheetOpenChange}
        onSaved={handleFielSaved}
        editingId={editingId}
      />

      <DeleteFielDialog
        open={deleteOpen}
        onOpenChange={setDeleteOpen}
        fielId={deleteTarget?.id ?? null}
        fielNome={deleteTarget?.nome ?? null}
        onDeleted={handleFielSaved}
      />
    </div>
  );
}
