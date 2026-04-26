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
import { MembrosTable } from '@/pages/secretaria/components/membros-table';
import { MembroFormSheet } from '@/pages/secretaria/components/membro-form-sheet';

export function SecretariaPage() {
  const navigate = useNavigate();
  const { canSecretaryIndex } = useAppData();
  const [refreshKey, setRefreshKey] = useState(0);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  // Redireciona se não tiver permissão
  if (canSecretaryIndex === false) {
    navigate('/dashboard', { replace: true });
    return null;
  }

  function handleEdit(id: number) {
    setEditingId(id);
    setSheetOpen(true);
  }

  function handleNew() {
    setEditingId(null);
    setSheetOpen(true);
  }

  function handleSaved() {
    setRefreshKey((k) => k + 1);
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Secretaria</ToolbarPageTitle>
          <ToolbarDescription>Gestão de membros religiosos da fraternidade</ToolbarDescription>
        </ToolbarHeading>
        <ToolbarActions>
          <Button size="sm" onClick={handleNew}>
            <UserPlus className="size-4" />
            Novo Membro
          </Button>
        </ToolbarActions>
      </Toolbar>

      <MembrosTable
        refreshKey={refreshKey}
        onEdit={handleEdit}
      />

      <MembroFormSheet
        open={sheetOpen}
        onOpenChange={setSheetOpen}
        editingId={editingId}
        onSaved={handleSaved}
      />
    </div>
  );
}

