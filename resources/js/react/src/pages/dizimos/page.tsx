import { useCallback, useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import { Plus } from 'lucide-react';
import {
  Toolbar,
  ToolbarActions,
  ToolbarDescription,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Button } from '@/components/ui/button';
import { useAppData } from '@/hooks/useAppData';
import type { IDizimo } from '@/hooks/useDizimos';
import { DizimosTable } from '@/pages/dizimos/components/dizimos-table';
import { LancamentoDizimoDrawer } from '@/pages/dizimos/components/lancamento-dizimo-drawer';
import { DeleteDizimoDialog } from '@/pages/dizimos/components/delete-dizimo-dialog';

/**
 * Dízimo e Doações — listagem + lançamento (Sheet) + exclusão.
 * Rota Laravel nomeada: dizimos.index → GET /app/dizimos
 */
export function DizimosPage() {
  const {
    canDizimosIndex,
    canDizimosCreate,
    canDizimosEdit,
    canDizimosDelete,
    modules,
    hasAdminRole,
    hasGlobalRole,
  } = useAppData();

  const seesDizimosInModules = modules?.some((m) => m.key === 'dizimos') ?? false;
  const principalAccess = hasAdminRole === true || hasGlobalRole === true;

  // Permissões com fallback: admin/global liberam tudo; demais respeitam flags
  // Spatie. Quando a flag explícita não estiver presente (cache antigo do
  // __APP_DATA__) caímos no acesso da página + role administrativa.
  const allowCreate = principalAccess || canDizimosCreate === true || canDizimosCreate === undefined;
  const allowEdit   = principalAccess || canDizimosEdit   === true || canDizimosEdit   === undefined;
  const allowDelete = principalAccess || canDizimosDelete === true || canDizimosDelete === undefined;

  const [refreshKey, setRefreshKey] = useState(0);

  // Drawer de criação/edição
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  // Diálogo de exclusão
  const [deleteOpen, setDeleteOpen] = useState(false);
  const [deleteTarget, setDeleteTarget] = useState<IDizimo | null>(null);

  const handleSaved = useCallback(() => {
    setRefreshKey((k) => k + 1);
  }, []);

  const openCreate = useCallback(() => {
    setEditingId(null);
    setDrawerOpen(true);
  }, []);

  const handleEdit = useCallback((id: number) => {
    setEditingId(id);
    setDrawerOpen(true);
  }, []);

  const handleDelete = useCallback((dizimo: IDizimo) => {
    setDeleteTarget(dizimo);
    setDeleteOpen(true);
  }, []);

  const handleDrawerOpenChange = useCallback((open: boolean) => {
    setDrawerOpen(open);
    if (!open) setEditingId(null);
  }, []);

  // Guarda principal: só após os hooks — redireciona se nem flag, nem módulo, nem role principal.
  if (!principalAccess && canDizimosIndex === false && !seesDizimosInModules) {
    return <Navigate to="/dashboard" replace />;
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Dízimo e Doações</ToolbarPageTitle>
          <ToolbarDescription>
            Registre dízimos, doações e ofertas — com integração ao financeiro.
          </ToolbarDescription>
          <Breadcrumb>
            <BreadcrumbList>
              <BreadcrumbItem>
                <BreadcrumbLink asChild>
                  <Link to="/dashboard">Home</Link>
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbPage>Dízimo e Doações</BreadcrumbPage>
              </BreadcrumbItem>
            </BreadcrumbList>
          </Breadcrumb>
        </ToolbarHeading>
        {allowCreate && (
          <ToolbarActions>
            <Button
              type="button"
              size="sm"
              className="bg-blue-600 hover:bg-blue-700 text-white border-0 shadow-none"
              onClick={openCreate}
            >
              <Plus className="size-4" />
              Novo lançamento
            </Button>
          </ToolbarActions>
        )}
      </Toolbar>

      <DizimosTable
        refreshKey={refreshKey}
        onEdit={handleEdit}
        onDelete={handleDelete}
        canEdit={allowEdit}
        canDelete={allowDelete}
      />

      <LancamentoDizimoDrawer
        open={drawerOpen}
        onOpenChange={handleDrawerOpenChange}
        editingId={editingId}
        onSaved={handleSaved}
      />

      <DeleteDizimoDialog
        open={deleteOpen}
        onOpenChange={setDeleteOpen}
        dizimo={deleteTarget}
        onDeleted={handleSaved}
      />
    </div>
  );
}
