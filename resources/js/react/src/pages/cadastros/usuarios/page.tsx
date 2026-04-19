import { useState } from 'react';
import { UserPlus } from 'lucide-react';
import {
  Toolbar,
  ToolbarActions,
  ToolbarDescription,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Button } from '@/components/ui/button';
import { UsuariosTable } from '@/pages/cadastros/usuarios/components/usuarios-table';
import { UsuarioFormSheet } from '@/pages/cadastros/usuarios/components/usuario-form-sheet';
import { ResetPasswordSheet } from '@/pages/cadastros/usuarios/components/reset-password-sheet';

export function UsuariosPage() {
  const [refreshKey, setRefreshKey] = useState(0);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [resetPasswordUser, setResetPasswordUser] = useState<{ id: number; name: string; email: string } | null>(null);

  function handleEdit(id: number) {
    setEditingId(id);
    setSheetOpen(true);
  }

  function handleSheetOpenChange(open: boolean) {
    setSheetOpen(open);
    if (!open) setEditingId(null);
  }

  function handleResetPassword(id: number, name: string, email: string) {
    setResetPasswordUser({ id, name, email });
  }

  function handleResetPasswordSheetChange(open: boolean) {
    if (!open) setResetPasswordUser(null);
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Usuários</ToolbarPageTitle>
          <ToolbarDescription>Gerenciamento de usuários e acessos do sistema</ToolbarDescription>
        </ToolbarHeading>
        <ToolbarActions>
          <Button
            size="sm"
            className="bg-blue-600 hover:bg-blue-700 text-white border-0"
            onClick={() => { setEditingId(null); setSheetOpen(true); }}
          >
            <UserPlus className="size-4" />
            Novo Usuário
          </Button>
        </ToolbarActions>
      </Toolbar>

      <UsuariosTable refreshKey={refreshKey} onEdit={handleEdit} onResetPassword={handleResetPassword} />

      <UsuarioFormSheet
        open={sheetOpen}
        onOpenChange={handleSheetOpenChange}
        onSaved={() => setRefreshKey((k) => k + 1)}
        editingId={editingId}
      />

      <ResetPasswordSheet
        open={resetPasswordUser !== null}
        onOpenChange={handleResetPasswordSheetChange}
        userId={resetPasswordUser?.id ?? null}
        userName={resetPasswordUser?.name}
        userEmail={resetPasswordUser?.email}
      />
    </div>
  );
}

