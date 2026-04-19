import { ReactNode } from 'react';
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ScrollArea } from '@/components/ui/scroll-area';
import { getDataGridColumnVisibilityIcon, getDataGridColumnVisibilityLabel } from '@/lib/data-grid-column-display';
import { Table } from '@tanstack/react-table';

function DataGridColumnVisibility<TData>({ table, trigger }: { table: Table<TData>; trigger: ReactNode }) {
  const columns = table
    .getAllColumns()
    .filter((column) => typeof column.accessorFn !== 'undefined' && column.getCanHide());

  const visibleCount = columns.filter((c) => c.getIsVisible()).length;

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>{trigger}</DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="min-w-64 p-0">
        <div className="px-3 py-2.5 border-b border-border">
          <p className="text-sm font-semibold">Personalizar colunas</p>
          <p className="text-xs text-muted-foreground mt-0.5">
            <span className="font-medium">{visibleCount}/{columns.length}</span> colunas selecionadas
          </p>
        </div>
        <ScrollArea className="max-h-[min(18rem,55vh)]" viewportClassName="max-h-[inherit]">
          <div className="py-1">
            {columns.map((column) => {
              const Icon = getDataGridColumnVisibilityIcon(column.id);
              const label = getDataGridColumnVisibilityLabel(column.id, column.columnDef.meta?.headerTitle);
              return (
                <DropdownMenuCheckboxItem
                  key={column.id}
                  className="gap-2"
                  checked={column.getIsVisible()}
                  onSelect={(event) => event.preventDefault()}
                  onCheckedChange={(value) => column.toggleVisibility(!!value)}
                >
                  <Icon aria-hidden className="size-4 shrink-0 text-muted-foreground" />
                  <span className="min-w-0 flex-1 truncate">{label}</span>
                </DropdownMenuCheckboxItem>
              );
            })}
          </div>
        </ScrollArea>
        <div className="px-3 py-2 border-t border-border">
          <button
            className="text-xs text-primary hover:underline flex items-center gap-1"
            onClick={() => columns.forEach((c) => c.toggleVisibility(true))}
          >
            ↺ Restaurar padrão
          </button>
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}

export { DataGridColumnVisibility };
