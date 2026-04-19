import { Link, Navigate } from 'react-router-dom';
import { useCallback, useEffect, useMemo, useState } from 'react';
import {
  ColumnDef,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  PaginationState,
  RowSelectionState,
  SortingState,
  useReactTable,
} from '@tanstack/react-table';
import { ArrowLeftRight, BookOpen, CircleDollarSign, FileDown, GitBranch, LayoutList, Pencil, PlusCircle, Search, Tag, Trash2, TrendingDown, TrendingUp, X } from 'lucide-react';
import { cn } from '@/lib/utils';
import {
  Toolbar,
  ToolbarActions,
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
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Card, CardFooter, CardHeader, CardTable } from '@/components/ui/card';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridPagination } from '@/components/ui/data-grid-pagination';
import {
  DataGridTable,
  DataGridTableRowSelect,
  DataGridTableRowSelectAll,
} from '@/components/ui/data-grid-table';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useAppData } from '@/hooks/useAppData';
import { PlanoContaFormSheet } from './components/plano-conta-form-sheet';
import { CategoriaFormSheet } from './components/categoria-form-sheet';
import type { PlanoContaOption } from './components/categoria-form-sheet';

interface CategoriaRow {
  id: number;
  descricao: string;
  tipo: string;
  categoria: string;
  is_active: boolean;
  contaDebito: string;
  contaCredito: string;
  conta_debito_id: number | null;
  conta_credito_id: number | null;
}

interface PlanoContaRow {
  id: number;
  code: string;
  name: string;
  type: string;
  is_analytical: boolean;
  is_deductible: boolean;
}

function stripHtml(value: string | null | undefined): string {
  if (!value) return '';

  if (typeof window !== 'undefined' && 'DOMParser' in window) {
    const doc = new DOMParser().parseFromString(value, 'text/html');
    return (doc.body.textContent ?? '').replace(/\s+/g, ' ').trim();
  }

  return value.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
}

function tipoPlanoContaLabel(tipo: string): string {
  const labels: Record<string, string> = {
    ativo: 'Ativo',
    passivo: 'Passivo',
    patrimonio_liquido: 'Patrimônio Líquido',
    receita: 'Receita',
    despesa: 'Despesa',
  };

  return labels[tipo] ?? tipo;
}

type PlanoViewMode = 'list' | 'tree';

const TIPO_BADGE_COLORS: Record<string, string> = {
  ativo: 'bg-blue-50 text-blue-700 border-blue-200',
  passivo: 'bg-red-50 text-red-700 border-red-200',
  patrimonio_liquido: 'bg-purple-50 text-purple-700 border-purple-200',
  receita: 'bg-emerald-50 text-emerald-700 border-emerald-200',
  despesa: 'bg-amber-50 text-amber-700 border-amber-200',
};

const TIPO_CATEGORIA_BADGE: Record<string, { label: string; dot: string; cls: string; Icon: React.ElementType }> = {
  entrada:          { label: 'Entrada',         dot: 'bg-emerald-500', cls: 'bg-emerald-50 text-emerald-700 border-emerald-200', Icon: TrendingUp },
  saida:            { label: 'Saída',            dot: 'bg-red-500',     cls: 'bg-red-50 text-red-700 border-red-200',             Icon: TrendingDown },
  ambos:            { label: 'Ambos',            dot: 'bg-blue-500',    cls: 'bg-blue-50 text-blue-700 border-blue-200',          Icon: CircleDollarSign },
  transferencia:    { label: 'Transferência',    dot: 'bg-violet-500',  cls: 'bg-violet-50 text-violet-700 border-violet-200',    Icon: ArrowLeftRight },
  somente_contabil: { label: 'Somente Contábil', dot: 'bg-amber-500',   cls: 'bg-amber-50 text-amber-700 border-amber-200',       Icon: BookOpen },
};

function normalizeTipo(raw: string): string {
  return raw
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/\s+/g, '_');
}

type CategoriaStatusFilter = 'all' | 'active' | 'inactive';

const STATUS_FILTER_OPTIONS: Array<{
  value: CategoriaStatusFilter;
  label: string;
  dot?: string;
  cls?: string;
}> = [
  { value: 'all',      label: 'Todos' },
  { value: 'active',   label: 'Ativa',   dot: 'bg-emerald-500', cls: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
  { value: 'inactive', label: 'Inativa', dot: 'bg-muted-foreground', cls: 'bg-muted text-muted-foreground border-border' },
];

function PlanoContaTreeView({ contas, loading }: { contas: PlanoContaRow[]; loading: boolean }) {
  const sorted = useMemo(
    () => [...contas].sort((a, b) => a.code.localeCompare(b.code, undefined, { numeric: true })),
    [contas],
  );

  if (loading) {
    return (
      <div className="p-4 space-y-2">
        {Array.from({ length: 10 }).map((_, i) => (
          <Skeleton
            key={i}
            className={cn('h-9 rounded-md', i % 3 !== 0 ? 'ml-6 w-[calc(100%-24px)]' : 'w-full')}
          />
        ))}
      </div>
    );
  }

  if (contas.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center py-16 text-muted-foreground">
        <GitBranch className="size-10 mb-3 opacity-30" />
        <p className="text-sm">Nenhuma conta contábil carregada.</p>
      </div>
    );
  }

  return (
    <div className="divide-y divide-border/40">
      {sorted.map((conta) => {
        const depth = conta.code.split('.').length - 1;
        const isRoot = depth === 0;
        return (
          <div
            key={conta.id}
            className={cn(
              'flex items-center gap-3 py-2.5 pr-4 text-sm transition-colors',
              isRoot ? 'bg-muted/40 font-semibold' : 'hover:bg-muted/20',
            )}
            style={{ paddingLeft: `${16 + depth * 20}px` }}
          >
            {depth > 0 && (
              <span className="text-muted-foreground/40 shrink-0 select-none">└</span>
            )}
            <span className="font-mono text-xs text-muted-foreground w-28 shrink-0">
              {conta.code}
            </span>
            <span className={cn('flex-1 truncate', isRoot ? 'text-foreground' : 'text-muted-foreground')}>
              {conta.name}
            </span>
            <span
              className={cn(
                'text-[11px] px-2 py-0.5 rounded-full border font-medium shrink-0',
                TIPO_BADGE_COLORS[conta.type] ?? 'bg-muted text-muted-foreground border-border',
              )}
            >
              {tipoPlanoContaLabel(conta.type)}
            </span>
          </div>
        );
      })}
    </div>
  );
}

function tableCellSkeleton() {
  return (
    <div className="flex items-center gap-3">
      <Skeleton className="h-9 w-9 rounded-full" />
      <div className="space-y-2">
        <Skeleton className="h-3.5 w-[140px]" />
        <Skeleton className="h-3.5 w-[100px]" />
      </div>
    </div>
  );
}

function mapAccount(c: {
  id: number;
  code: string;
  name: string;
  type: string;
  is_analytical?: unknown;
  is_deductible?: unknown;
}): PlanoContaRow {
  return {
    id: Number(c.id),
    code: c.code,
    name: c.name,
    type: c.type,
    is_analytical: Boolean(c.is_analytical),
    is_deductible: Boolean(c.is_deductible),
  };
}

export function ContabilidadePage() {
  const { canContabilidadeIndex, companyId, companies } = useAppData();
  const activeCompany = useMemo(
    () => companies.find((c) => c.id === companyId) ?? null,
    [companies, companyId],
  );
  const [activeTab, setActiveTab] = useState('categorias');
  const [categorias, setCategorias] = useState<CategoriaRow[]>([]);
  const [planoContas, setPlanoContas] = useState<PlanoContaRow[]>([]);
  const [loadingCategorias, setLoadingCategorias] = useState(true);
  const [loadingPlanoContas, setLoadingPlanoContas] = useState(true);
  const [categoriasError, setCategoriasError] = useState<string | null>(null);
  const [planoContasError, setPlanoContasError] = useState<string | null>(null);
  const [categoriasPagination, setCategoriasPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 10 });
  const [categoriasSorting, setCategoriasSorting] = useState<SortingState>([{ id: 'descricao', desc: false }]);
  const [categoriasSelection, setCategoriasSelection] = useState<RowSelectionState>({});
  const [planoPagination, setPlanoPagination] = useState<PaginationState>({ pageIndex: 0, pageSize: 10 });
  const [planoSorting, setPlanoSorting] = useState<SortingState>([{ id: 'code', desc: false }]);
  const [planoSelection, setPlanoSelection] = useState<RowSelectionState>({});

  // Sheet de criação / edição de Plano de Conta
  const [sheetPlanoContaOpen, setSheetPlanoContaOpen] = useState(false);
  const [editingPlantoConta, setEditingPlantoConta] = useState<PlanoContaRow | null>(null);

  // Sheet de criação / edição de Categoria
  const [sheetCategoriaOpen, setSheetCategoriaOpen] = useState(false);
  const [editingCategoria, setEditingCategoria] = useState<number | null>(null);

  // Toggle de visualização do Plano de Contas
  const [planoViewMode, setPlanoViewMode] = useState<PlanoViewMode>(
    () => (localStorage.getItem('plano-view-mode') as PlanoViewMode | null) ?? 'list',
  );

  const handlePlanoViewMode = useCallback((mode: PlanoViewMode) => {
    setPlanoViewMode(mode);
    localStorage.setItem('plano-view-mode', mode);
  }, []);

  // Busca e filtros nas Categorias
  const [categoriasSearch, setCategoriasSearch] = useState('');
  const [categoriasFilterTipo, setCategoriasFilterTipo] = useState<string | null>(null);
  const [categoriasFilterStatus, setCategoriasFilterStatus] = useState<CategoriaStatusFilter>('all');

  // Busca no Plano de Contas
  const [planoSearch, setPlanoSearch] = useState('');

  const reloadPlanoContas = useCallback(async () => {
    setLoadingPlanoContas(true);
    try {
      const res = await fetch('/contabilidade/plano-contas/table-data', {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (res.ok) {
        const json = await res.json();
        setPlanoContas(Array.isArray(json.accounts) ? json.accounts.map(mapAccount) : []);
      }
    } finally {
      setLoadingPlanoContas(false);
    }
  }, []);

  const handleDeletePlantoConta = useCallback(
    async (id: number) => {
      if (!window.confirm('Deseja realmente excluir esta conta contábil?')) return;
      try {
        const token =
          (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
        const res = await fetch(`/contabilidade/plano-contas/${id}`, {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token,
          },
          credentials: 'same-origin',
        });
        if (res.ok) {
          void reloadPlanoContas();
        }
      } catch {
        // silently fail
      }
    },
    [reloadPlanoContas],
  );

  const reloadCategorias = useCallback(async () => {
    setLoadingCategorias(true);
    setCategoriasError(null);
    try {
      const res = await fetch('/contabilidade/categorias/data', {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = (await res.json()) as {
        data?: Array<{
          id: number;
          descricao: string;
          tipo: string;
          categoria: string;
          is_active?: boolean;
          contaDebito: string;
          contaCredito: string;
          conta_debito_id?: number | null;
          conta_credito_id?: number | null;
        }>;
      };
      setCategorias(
        Array.isArray(json.data)
          ? json.data.map((item) => ({
              id: Number(item.id ?? 0),
              descricao: stripHtml(item.descricao),
              tipo: stripHtml(item.tipo),
              categoria: stripHtml(item.categoria),
              is_active: item.is_active ?? true,
              contaDebito: stripHtml(item.contaDebito),
              contaCredito: stripHtml(item.contaCredito),
              conta_debito_id: item.conta_debito_id ?? null,
              conta_credito_id: item.conta_credito_id ?? null,
            }))
          : [],
      );
    } catch {
      setCategorias([]);
      setCategoriasError('Não foi possível carregar as categorias.');
    } finally {
      setLoadingCategorias(false);
    }
  }, []);

  useEffect(() => {
    void reloadCategorias();
  }, [reloadCategorias]);

  useEffect(() => {
    let cancelled = false;

    async function loadPlanoContas() {
      setLoadingPlanoContas(true);
      setPlanoContasError(null);

      try {
        const res = await fetch('/contabilidade/plano-contas/table-data', {
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        });

        if (!res.ok) {
          throw new Error(`HTTP ${res.status}`);
        }

        const json = (await res.json()) as {
          accounts?: Array<{ id: number; code: string; name: string; type: string }>;
        };

        if (!cancelled) {
          setPlanoContas(Array.isArray(json.accounts) ? json.accounts.map(mapAccount) : []);
        }
      } catch {
        if (!cancelled) {
          setPlanoContas([]);
          setPlanoContasError('Não foi possível carregar o plano de contas.');
        }
      } finally {
        if (!cancelled) {
          setLoadingPlanoContas(false);
        }
      }
    }

    void loadPlanoContas();

    return () => {
      cancelled = true;
    };
  }, []);

  const categoriasVazias = useMemo(
    () => !loadingCategorias && !categoriasError && categorias.length === 0,
    [categorias, categoriasError, loadingCategorias],
  );

  const planoContasVazio = useMemo(
    () => !loadingPlanoContas && !planoContasError && planoContas.length === 0,
    [loadingPlanoContas, planoContas, planoContasError],
  );

  const filteredCategorias = useMemo(() => {
    let result = categorias;
    const q = categoriasSearch.trim().toLowerCase();
    if (q) {
      result = result.filter(
        (c) =>
          c.descricao.toLowerCase().includes(q) ||
          c.categoria.toLowerCase().includes(q) ||
          normalizeTipo(c.tipo).includes(q) ||
          c.tipo.toLowerCase().includes(q),
      );
    }
    if (categoriasFilterTipo) {
      result = result.filter((c) => normalizeTipo(c.tipo) === categoriasFilterTipo);
    }
    if (categoriasFilterStatus === 'active') {
      result = result.filter((c) => c.is_active);
    } else if (categoriasFilterStatus === 'inactive') {
      result = result.filter((c) => !c.is_active);
    }
    return result;
  }, [categorias, categoriasSearch, categoriasFilterTipo, categoriasFilterStatus]);

  const hasActiveCategoriaFilters = categoriasFilterTipo !== null || categoriasFilterStatus !== 'all';

  useEffect(() => {
    setCategoriasPagination((p) => ({ ...p, pageIndex: 0 }));
  }, [categoriasSearch, categoriasFilterTipo, categoriasFilterStatus]);

  const filteredPlanoContas = useMemo(() => {
    const q = planoSearch.trim().toLowerCase();
    if (!q) return planoContas;
    return planoContas.filter(
      (c) => c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q),
    );
  }, [planoContas, planoSearch]);

  const handleExportPDF = useCallback(() => {
    const win = window.open('', '_blank');
    if (!win) return;

    const now = new Date().toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });

    const typeColors: Record<string, string> = {
      ativo: '#1d4ed8',
      passivo: '#b91c1c',
      patrimonio_liquido: '#7c3aed',
      receita: '#059669',
      despesa: '#d97706',
    };

    const rows = filteredPlanoContas
      .map((conta) => {
        const color = typeColors[conta.type] ?? '#64748b';
        return `
        <tr>
          <td class="code">${conta.code}</td>
          <td>${conta.name}</td>
          <td style="color:${color};font-weight:600">${tipoPlanoContaLabel(conta.type)}</td>
          <td class="center">${conta.is_analytical ? '<span class="badge green">Anal\u00edtica</span>' : '<span class="badge sky">Sint\u00e9tica</span>'}</td>
          <td class="center">${conta.is_deductible ? '<span class="badge violet">Sim</span>' : '\u2014'}</td>
        </tr>`;
      })
      .join('');

    // Cabeçalho da entidade
    const logoHtml = activeCompany?.avatar_url
      ? `<img src="${activeCompany.avatar_url}" alt="Logo" style="max-height:64px;max-width:120px;object-fit:contain;" />`
      : `<div style="width:56px;height:56px;border-radius:50%;background:#1e293b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;">${(activeCompany?.name ?? 'E').charAt(0).toUpperCase()}</div>`;

    const companyName = activeCompany?.name ?? '';
    const razaoSocial = activeCompany?.razao_social ?? '';
    const cnpj = activeCompany?.cnpj ?? '';

    win.document.write(`<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Plano de Contas</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; padding: 24px; }
    .entity-header { display: flex; align-items: center; gap: 16px; padding-bottom: 12px; border-bottom: 2px solid #1e293b; margin-bottom: 16px; }
    .entity-logo { flex-shrink: 0; }
    .entity-info { flex: 1; }
    .entity-info h2 { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
    .entity-info .razao { font-size: 11px; color: #475569; margin-bottom: 2px; }
    .entity-info .cnpj { font-size: 10px; color: #64748b; }
    .report-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 12px; }
    .report-header h1 { font-size: 14px; font-weight: 700; }
    .report-header p { font-size: 10px; color: #64748b; text-align: right; }
    table { width: 100%; border-collapse: collapse; margin-top: 4px; }
    th { background: #1e293b; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; }
    th.center, td.center { text-align: center; }
    td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    td.code { font-family: monospace; font-size: 10px; color: #475569; white-space: nowrap; }
    tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 1px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 600; border: 1px solid; }
    .badge.green { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
    .badge.sky { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
    .badge.violet { background: #f5f3ff; color: #5b21b6; border-color: #ddd6fe; }
    footer { margin-top: 16px; text-align: right; font-size: 9px; color: #94a3b8; }
    @media print { body { padding: 0; } }
  </style>
</head>
<body>
  <div class="entity-header">
    <div class="entity-logo">${logoHtml}</div>
    <div class="entity-info">
      <h2>${companyName}</h2>
      ${razaoSocial ? `<div class="razao">${razaoSocial}</div>` : ''}
      ${cnpj ? `<div class="cnpj">CNPJ: ${cnpj}</div>` : ''}
    </div>
  </div>
  <div class="report-header">
    <div>
      <h1>Plano de Contas</h1>
      <p style="margin-top:2px;color:#475569">${filteredPlanoContas.length} conta${filteredPlanoContas.length !== 1 ? 's' : ''}</p>
    </div>
    <p>Emitido em ${now}</p>
  </div>
  <table>
    <thead>
      <tr>
        <th style="width:110px">C\u00f3digo</th>
        <th>Conta</th>
        <th style="width:140px">Tipo</th>
        <th class="center" style="width:100px">Classifica\u00e7\u00e3o</th>
        <th class="center" style="width:80px">Dedut\u00edvel</th>
      </tr>
    </thead>
    <tbody>${rows}</tbody>
  </table>
  <footer>Plano de Contas &mdash; Dominus Sistema</footer>
</body>
</html>`);

    win.document.close();
    win.focus();
    win.print();
  }, [filteredPlanoContas, activeCompany]);

  const handleDeleteCategoria = useCallback(async (id: number) => {
    if (!window.confirm('Deseja realmente excluir esta categoria?')) return;
    try {
      const token =
        (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
      const res = await fetch(`/contabilidade/categorias/${id}`, {
        method: 'DELETE',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token,
        },
        credentials: 'same-origin',
      });
      if (res.ok) void reloadCategorias();
    } catch {
      // silently fail
    }
  }, [reloadCategorias]);

  const categoriasColumns = useMemo<ColumnDef<CategoriaRow>[]>(
    () => [
      {
        accessorKey: 'id',
        header: () => <DataGridTableRowSelectAll />,
        cell: ({ row }) => <DataGridTableRowSelect row={row} />,
        enableSorting: false,
        enableHiding: false,
        size: 51,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'descricao',
        accessorKey: 'descricao',
        header: ({ column }) => <DataGridColumnHeader title="Descrição" column={column} />,
        cell: ({ row }) => {
          const badge = TIPO_CATEGORIA_BADGE[normalizeTipo(row.original.tipo)];
          return (
            <span className="flex items-center gap-2">
              {badge && (
                <span className={cn('size-2 rounded-full shrink-0', badge.dot)} />
              )}
              <span className="font-medium text-foreground truncate">{row.original.descricao || '-'}</span>
            </span>
          );
        },
        enableSorting: true,
        size: 260,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'categoria',
        accessorKey: 'categoria',
        header: ({ column }) => <DataGridColumnHeader title="Agrupador" column={column} />,
        cell: ({ row }) =>
          row.original.categoria ? (
            <span className="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-md border bg-muted/50 text-muted-foreground font-medium">
              <Tag className="size-2.5 shrink-0 opacity-60" />
              {row.original.categoria}
            </span>
          ) : (
            <span className="text-xs text-muted-foreground/40">—</span>
          ),
        enableSorting: true,
        size: 180,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'tipo',
        accessorKey: 'tipo',
        header: ({ column }) => <DataGridColumnHeader title="Tipo" column={column} />,
        cell: ({ row }) => {
          const key = normalizeTipo(row.original.tipo);
          const badge = TIPO_CATEGORIA_BADGE[key];
          if (!badge) return <span className="text-xs text-muted-foreground">{row.original.tipo || '—'}</span>;
          return (
            <span className={cn('inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-full border font-medium whitespace-nowrap', badge.cls)}>
              <badge.Icon className="size-3 shrink-0" />
              {badge.label}
            </span>
          );
        },
        enableSorting: true,
        size: 160,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'contaDebito',
        accessorKey: 'contaDebito',
        header: ({ column }) => <DataGridColumnHeader title="Conta Débito" column={column} />,
        cell: ({ row }) =>
          row.original.contaDebito ? (
            <span className="text-xs font-mono text-muted-foreground truncate block max-w-[200px]" title={row.original.contaDebito}>
              {row.original.contaDebito}
            </span>
          ) : (
            <span className="text-xs text-muted-foreground/40">—</span>
          ),
        enableSorting: true,
        size: 220,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'contaCredito',
        accessorKey: 'contaCredito',
        header: ({ column }) => <DataGridColumnHeader title="Conta Crédito" column={column} />,
        cell: ({ row }) =>
          row.original.contaCredito ? (
            <span className="text-xs font-mono text-muted-foreground truncate block max-w-[200px]" title={row.original.contaCredito}>
              {row.original.contaCredito}
            </span>
          ) : (
            <span className="text-xs text-muted-foreground/40">—</span>
          ),
        enableSorting: true,
        size: 220,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'is_active',
        header: ({ column }) => <DataGridColumnHeader title="Status" column={column} />,
        accessorFn: (row) => (row.is_active ? 'Ativa' : 'Inativa'),
        cell: ({ row }) =>
          row.original.is_active ? (
            <span className="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full border font-medium bg-emerald-50 text-emerald-700 border-emerald-200">
              <span className="size-1.5 rounded-full bg-emerald-500" />
              Ativa
            </span>
          ) : (
            <span className="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full border font-medium bg-muted text-muted-foreground border-border">
              <span className="size-1.5 rounded-full bg-muted-foreground" />
              Inativa
            </span>
          ),
        enableSorting: true,
        size: 90,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'actions',
        header: () => <span className="text-xs font-medium">Ações</span>,
        cell: ({ row }) => (
          <div className="flex items-center gap-0.5">
            <Button
              size="icon"
              variant="ghost"
              className="size-7 text-muted-foreground hover:text-foreground"
              title="Editar"
              onClick={() => {
                setEditingCategoria(row.original.id);
                setSheetCategoriaOpen(true);
              }}
            >
              <Pencil className="size-3.5" />
            </Button>
            <Button
              size="icon"
              variant="ghost"
              className="size-7 text-muted-foreground hover:text-destructive"
              title="Excluir"
              onClick={() => void handleDeleteCategoria(row.original.id)}
            >
              <Trash2 className="size-3.5" />
            </Button>
          </div>
        ),
        enableSorting: false,
        enableHiding: false,
        size: 80,
      },
    ],
    [handleDeleteCategoria],
  );

  const planoColumns = useMemo<ColumnDef<PlanoContaRow>[]>(
    () => [
      {
        accessorKey: 'id',
        header: () => <DataGridTableRowSelectAll />,
        cell: ({ row }) => <DataGridTableRowSelect row={row} />,
        enableSorting: false,
        enableHiding: false,
        size: 51,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'code',
        accessorKey: 'code',
        header: ({ column }) => <DataGridColumnHeader title="Código" column={column} />,
        cell: ({ row }) => <span className="text-foreground font-medium">{row.original.code}</span>,
        enableSorting: true,
        size: 140,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'name',
        accessorKey: 'name',
        header: ({ column }) => <DataGridColumnHeader title="Conta" column={column} />,
        cell: ({ row }) => <span className="text-muted-foreground">{row.original.name}</span>,
        enableSorting: true,
        size: 260,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'type',
        accessorKey: 'type',
        header: ({ column }) => <DataGridColumnHeader title="Tipo" column={column} />,
        cell: ({ row }) => {
          const type = row.original.type;
          const dotColor: Record<string, string> = {
            ativo: 'bg-blue-500',
            passivo: 'bg-red-500',
            patrimonio_liquido: 'bg-purple-500',
            receita: 'bg-emerald-500',
            despesa: 'bg-amber-500',
          };
          return (
            <span className="flex items-center gap-1.5">
              <span className={cn('size-2 rounded-full shrink-0', dotColor[type] ?? 'bg-muted-foreground')} />
              <span className="text-muted-foreground">{tipoPlanoContaLabel(type)}</span>
            </span>
          );
        },
        enableSorting: true,
        size: 180,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'classificacao',
        header: ({ column }) => <DataGridColumnHeader title="Classificação" column={column} />,
        accessorFn: (row) => (row.is_analytical ? 'Analítica' : 'Sintética'),
        cell: ({ row }) => (
          <span
            className={cn(
              'inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full border font-medium',
              row.original.is_analytical
                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                : 'bg-sky-50 text-sky-700 border-sky-200',
            )}
          >
            <span
              className={cn(
                'size-1.5 rounded-full',
                row.original.is_analytical ? 'bg-emerald-500' : 'bg-sky-500',
              )}
            />
            {row.original.is_analytical ? 'Analítica' : 'Sintética'}
          </span>
        ),
        enableSorting: true,
        size: 130,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'is_deductible',
        header: ({ column }) => <DataGridColumnHeader title="Dedutível" column={column} />,
        accessorFn: (row) => (row.is_deductible ? 'Sim' : 'Não'),
        cell: ({ row }) =>
          row.original.is_deductible ? (
            <span className="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full border font-medium bg-violet-50 text-violet-700 border-violet-200">
              <span className="size-1.5 rounded-full bg-violet-500" />
              Dedutível
            </span>
          ) : (
            <span className="text-xs text-muted-foreground/60">—</span>
          ),
        enableSorting: true,
        size: 110,
        meta: { skeleton: tableCellSkeleton() },
      },
      {
        id: 'actions',
        header: () => <span className="text-xs font-medium">Ações</span>,
        cell: ({ row }) => (
          <div className="flex items-center gap-0.5">
            <Button
              size="icon"
              variant="ghost"
              className="size-7 text-muted-foreground hover:text-foreground"
              title="Editar"
              onClick={() => {
                setEditingPlantoConta(row.original);
                setSheetPlanoContaOpen(true);
              }}
            >
              <Pencil className="size-3.5" />
            </Button>
            <Button
              size="icon"
              variant="ghost"
              className="size-7 text-muted-foreground hover:text-destructive"
              title="Excluir"
              onClick={() => void handleDeletePlantoConta(row.original.id)}
            >
              <Trash2 className="size-3.5" />
            </Button>
          </div>
        ),
        enableSorting: false,
        enableHiding: false,
        size: 80,
      },
    ],
    [handleDeletePlantoConta],
  );

  const categoriasTable = useReactTable({
    columns: categoriasColumns,
    data: filteredCategorias,
    getRowId: (row) => String(row.id),
    state: { pagination: categoriasPagination, sorting: categoriasSorting, rowSelection: categoriasSelection },
    onPaginationChange: setCategoriasPagination,
    onSortingChange: setCategoriasSorting,
    onRowSelectionChange: setCategoriasSelection,
    enableRowSelection: true,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
  });

  const planoTable = useReactTable({
    columns: planoColumns,
    data: filteredPlanoContas,
    getRowId: (row) => String(row.id),
    state: { pagination: planoPagination, sorting: planoSorting, rowSelection: planoSelection },
    onPaginationChange: setPlanoPagination,
    onSortingChange: setPlanoSorting,
    onRowSelectionChange: setPlanoSelection,
    enableRowSelection: true,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
  });

  if (!canContabilidadeIndex) {
    return <Navigate to="/dashboard" replace />;
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Contabilidade</ToolbarPageTitle>
          <Breadcrumb>
            <BreadcrumbList>
              <BreadcrumbItem>
                <BreadcrumbLink asChild>
                  <Link to="/dashboard">Home</Link>
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbLink asChild>
                  <Link to="/contabilidade">Contabilidade</Link>
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbPage>Contabilidade</BreadcrumbPage>
              </BreadcrumbItem>
            </BreadcrumbList>
          </Breadcrumb>
        </ToolbarHeading>
        {activeTab === 'categorias' && (
          <ToolbarActions>
            <Button
              size="sm"
              onClick={() => {
                setEditingCategoria(null);
                setSheetCategoriaOpen(true);
              }}
            >
              <PlusCircle className="size-4" />
              Nova Categoria
            </Button>
          </ToolbarActions>
        )}
        {activeTab === 'plano-conta' && (
          <ToolbarActions>

            <Button size="sm" onClick={() => setSheetPlanoContaOpen(true)}>
              <PlusCircle className="size-4" />
              Nova Conta
            </Button>
            <Button
              size="sm"
              variant="outline"
              onClick={handleExportPDF}
              disabled={filteredPlanoContas.length === 0}
            >
              <FileDown className="size-4" />
              Exportar PDF
            </Button>
          </ToolbarActions>
        )}
      </Toolbar>


      <Card>
        <Tabs value={activeTab} onValueChange={setActiveTab}>
          <CardHeader className="flex-col items-stretch justify-start gap-0 border-b border-border pt-4 pb-0 min-h-0 px-0 bg-accent/50">
            <TabsList variant="line" className="w-full justify-start gap-6 px-5">
              <TabsTrigger value="categorias">Categorias</TabsTrigger>
              <TabsTrigger value="plano-conta">Plano de Conta</TabsTrigger>
            </TabsList>
          </CardHeader>

          <TabsContent value="categorias" className="mt-0">
            {/* Barra de controle */}
            <div className="flex items-center gap-2 px-4 py-2.5 border-b border-border/50 flex-wrap">
              {/* Search */}
              <div className="relative w-56 shrink-0">
                <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
                <Input
                  value={categoriasSearch}
                  onChange={(e) => setCategoriasSearch(e.target.value)}
                  placeholder="Buscar…"
                  className="h-8 pl-8 pr-8 text-sm bg-muted/40 border-border/50 focus-visible:bg-background"
                />
                {categoriasSearch && (
                  <button
                    type="button"
                    onClick={() => setCategoriasSearch('')}
                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                  >
                    <X className="size-3.5" />
                  </button>
                )}
              </div>

              {/* Divisor vertical */}
              <div className="w-px h-5 bg-border shrink-0" />

              {/* Chips: Tipo */}
              <div className="flex items-center gap-1.5 flex-wrap">
                <span className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground shrink-0">Tipo</span>
                <button
                  type="button"
                  onClick={() => setCategoriasFilterTipo(null)}
                  className={cn(
                    'h-6 px-2.5 rounded-full text-xs font-medium border transition-all',
                    categoriasFilterTipo === null
                      ? 'bg-foreground text-background border-foreground'
                      : 'bg-background text-muted-foreground border-border hover:border-foreground/30 hover:text-foreground',
                  )}
                >
                  Todos
                </button>
                {Object.entries(TIPO_CATEGORIA_BADGE).map(([key, cfg]) => {
                  const isSelected = categoriasFilterTipo === key;
                  return (
                    <button
                      key={key}
                      type="button"
                      onClick={() => setCategoriasFilterTipo(isSelected ? null : key)}
                      className={cn(
                        'h-6 px-2.5 rounded-full text-xs font-medium border transition-all inline-flex items-center gap-1',
                        isSelected
                          ? cn(cfg.cls, 'ring-1 ring-current/30')
                          : 'bg-background text-muted-foreground border-border hover:border-foreground/30 hover:text-foreground',
                      )}
                    >
                      <cfg.Icon className="size-3 shrink-0" />
                      {cfg.label}
                    </button>
                  );
                })}
              </div>

              {/* Divisor vertical */}
              <div className="w-px h-5 bg-border shrink-0" />

              {/* Chips: Status */}
              <div className="flex items-center gap-1.5">
                <span className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground shrink-0">Status</span>
                {STATUS_FILTER_OPTIONS.map((opt) => {
                  const isSelected = categoriasFilterStatus === opt.value;
                  return (
                    <button
                      key={opt.value}
                      type="button"
                      onClick={() => setCategoriasFilterStatus(opt.value)}
                      className={cn(
                        'h-6 px-2.5 rounded-full text-xs font-medium border transition-all inline-flex items-center gap-1.5',
                        isSelected
                          ? opt.cls
                            ? cn(opt.cls, 'ring-1 ring-current/30')
                            : 'bg-foreground text-background border-foreground'
                          : 'bg-background text-muted-foreground border-border hover:border-foreground/30 hover:text-foreground',
                      )}
                    >
                      {opt.dot && <span className={cn('size-1.5 rounded-full shrink-0', opt.dot)} />}
                      {opt.label}
                    </button>
                  );
                })}
              </div>

              <div className="flex-1" />

              {/* Limpar filtros + contador */}
              {hasActiveCategoriaFilters && (
                <button
                  type="button"
                  onClick={() => { setCategoriasFilterTipo(null); setCategoriasFilterStatus('all'); }}
                  className="flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground transition-colors shrink-0"
                >
                  <X className="size-3" />
                  Limpar
                </button>
              )}
              {!loadingCategorias && (
                <span className="text-xs text-muted-foreground shrink-0">
                  {(categoriasSearch || hasActiveCategoriaFilters)
                    ? `${filteredCategorias.length} de ${categorias.length}`
                    : `${categorias.length} categoria${categorias.length !== 1 ? 's' : ''}`}
                </span>
              )}
            </div>

            <DataGrid
              table={categoriasTable}
              recordCount={filteredCategorias.length}
              isLoading={loadingCategorias}
              emptyMessage={
                categoriasVazias
                  ? 'Nenhuma categoria cadastrada.'
                  : 'Nenhuma categoria corresponde aos filtros aplicados.'
              }
              tableLayout={{ columnsPinnable: true, columnsMovable: true, columnsVisibility: true, cellBorder: true, width: 'auto' }}
            >
              {categoriasError && (
                <div className="px-4 py-2 text-sm text-destructive bg-destructive/10">{categoriasError}</div>
              )}
              <CardTable>
                <ScrollArea>
                  <DataGridTable />
                  <ScrollBar orientation="horizontal" />
                </ScrollArea>
              </CardTable>
              <CardFooter>
                <DataGridPagination />
              </CardFooter>
            </DataGrid>
          </TabsContent>

          <TabsContent value="plano-conta" className="mt-0">
            {/* Barra de controle: busca + toggle de visualização */}
            <div className="flex items-center gap-3 px-4 py-2.5 border-b border-border/50">
              {/* Search */}
              <div className="relative flex-1 max-w-xs">
                <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
                <Input
                  value={planoSearch}
                  onChange={(e) => setPlanoSearch(e.target.value)}
                  placeholder="Buscar por código ou nome..."
                  className="h-8 pl-8 pr-8 text-sm bg-muted/40 border-border/50 focus-visible:bg-background"
                />
                {planoSearch && (
                  <button
                    type="button"
                    onClick={() => setPlanoSearch('')}
                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                  >
                    <X className="size-3.5" />
                  </button>
                )}
              </div>

              <div className="flex-1" />

              {/* Contador */}
              {!loadingPlanoContas && (
                <span className="text-xs text-muted-foreground shrink-0">
                  {planoSearch
                    ? `${filteredPlanoContas.length} de ${planoContas.length}`
                    : `${planoContas.length} conta${planoContas.length !== 1 ? 's' : ''}`}
                </span>
              )}

              {/* Toggle */}
              <div className="flex items-center gap-0.5 p-0.5 bg-muted rounded-lg shrink-0">
                <button
                  type="button"
                  onClick={() => handlePlanoViewMode('list')}
                  className={cn(
                    'flex items-center gap-1.5 h-7 px-2.5 rounded-md text-xs font-medium transition-all',
                    planoViewMode === 'list'
                      ? 'bg-background text-foreground shadow-sm'
                      : 'text-muted-foreground hover:text-foreground',
                  )}
                >
                  <LayoutList className="size-3.5" />
                  Lista
                </button>
                <button
                  type="button"
                  onClick={() => handlePlanoViewMode('tree')}
                  className={cn(
                    'flex items-center gap-1.5 h-7 px-2.5 rounded-md text-xs font-medium transition-all',
                    planoViewMode === 'tree'
                      ? 'bg-background text-foreground shadow-sm'
                      : 'text-muted-foreground hover:text-foreground',
                  )}
                >
                  <GitBranch className="size-3.5" />
                  Hierarquia
                </button>
              </div>
            </div>

            {planoViewMode === 'list' ? (
              <DataGrid
                table={planoTable}
                recordCount={filteredPlanoContas.length}
                isLoading={loadingPlanoContas}
                emptyMessage={planoContasVazio ? 'Nenhuma conta contábil carregada.' : 'Sem dados.'}
                tableLayout={{ columnsPinnable: true, columnsMovable: true, columnsVisibility: true, cellBorder: true, width: 'auto' }}
              >
                {planoContasError && (
                  <div className="px-4 py-2 text-sm text-destructive bg-destructive/10">{planoContasError}</div>
                )}
                <CardTable>
                  <ScrollArea>
                    <DataGridTable />
                    <ScrollBar orientation="horizontal" />
                  </ScrollArea>
                </CardTable>
                <CardFooter>
                  <DataGridPagination />
                </CardFooter>
              </DataGrid>
            ) : (
              <CardTable>
                {planoContasError && (
                  <div className="px-4 py-2 text-sm text-destructive bg-destructive/10">{planoContasError}</div>
                )}
                <ScrollArea className="max-h-[600px]">
                  <PlanoContaTreeView contas={filteredPlanoContas} loading={loadingPlanoContas} />
                  <ScrollBar orientation="vertical" />
                </ScrollArea>
              </CardTable>
            )}
          </TabsContent>
        </Tabs>
      </Card>

      {/* Sheet de criação / edição de Plano de Conta */}
      <PlanoContaFormSheet
        open={sheetPlanoContaOpen}
        onOpenChange={(open) => {
          setSheetPlanoContaOpen(open);
          if (!open) setEditingPlantoConta(null);
        }}
        planoContas={planoContas}
        editingItem={editingPlantoConta}
        onSuccess={reloadPlanoContas}
      />

      {/* Sheet de criação / edição de Categoria */}
      <CategoriaFormSheet
        open={sheetCategoriaOpen}
        onOpenChange={(open) => {
          setSheetCategoriaOpen(open);
          if (!open) setEditingCategoria(null);
        }}
        editingId={editingCategoria}
        planoContasList={planoContas as PlanoContaOption[]}
        onSuccess={reloadCategorias}
      />
    </div>
  );
}
