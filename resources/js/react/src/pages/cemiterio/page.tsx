import { useState, useEffect } from 'react';
import {
  Toolbar,
  ToolbarActions,
  ToolbarDescription,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { PlusCircle, UserRoundPlus, Building2, CheckCircle2, Users, AlertCircle, Info } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { TumulosTable } from '@/pages/cemiterio/components/tumulos-table';
import { DifuntosTable } from '@/pages/cemiterio/components/difuntos-table';
import { DifuntoFormSheet } from '@/pages/cemiterio/components/difunto-form-sheet';
import { TumuloFormSheet } from '@/pages/cemiterio/components/tumulo-form-sheet';
import { CobrancasTab } from './components/cobrancas-tab';

export function CemiterioPage() {
  const [refreshKey, setRefreshKey] = useState(0);
  const [activeTab, setActiveTab] = useState('difuntos');
  const [novoDifuntoOpen, setNovoDifuntoOpen] = useState(false);
  const [editingDifuntoId, setEditingDifuntoId] = useState<number | null>(null);
  const [cloneDifuntoId, setCloneDifuntoId] = useState<number | null>(null);
  const [editingTumuloId, setEditingTumuloId] = useState<number | null>(null);
  const [tumuloSheetOpen, setTumuloSheetOpen] = useState(false);
  const [stats, setStats] = useState({ total: 0, disponiveis: 0, ocupadas: 0, em_aberto: 0 });

  useEffect(() => {
    fetch('/cemiterio/stats', { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
      .then((r) => r.ok ? r.json() : null)
      .then((json) => { if (json?.success) setStats(json.data); })
      .catch(() => {});
  }, [refreshKey]);

  function handleSaved() {
    setRefreshKey((k) => k + 1);
  }

  function handleEditDifunto(id: number) {
    setEditingDifuntoId(id);
    setCloneDifuntoId(null);
    setNovoDifuntoOpen(true);
  }

  function handleCloneDifunto(id: number) {
    setEditingDifuntoId(null);
    setCloneDifuntoId(id);
    setNovoDifuntoOpen(true);
  }

  function handleEditTumulo(id: number) {
    setEditingTumuloId(id);
    setTumuloSheetOpen(true);
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Cemitério</ToolbarPageTitle>
          <ToolbarDescription>
            Gestão de túmulos, difuntos e histórico de ocupações
          </ToolbarDescription>
        </ToolbarHeading>
        <ToolbarActions>
          {activeTab === 'difuntos' ? (
            <Button size="sm" onClick={() => setNovoDifuntoOpen(true)}>
              <UserRoundPlus className="size-4" />
              Novo Difunto
            </Button>
          ) : activeTab === 'tumulos' ? (
            <Button size="sm" onClick={() => { setEditingTumuloId(null); setTumuloSheetOpen(true); }}>
              <PlusCircle className="size-4" />
              Novo Túmulo
            </Button>
          ) : null}        </ToolbarActions>
      </Toolbar>

      <div className="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
        <Card className="py-3">
          <CardContent className="flex items-center gap-3 px-4 py-0">
            <div className="flex size-9 shrink-0 items-center justify-center rounded-full bg-muted">
              <Building2 className="size-4 text-muted-foreground" />
            </div>
            <div className="flex-1">
              <p className="text-xs text-muted-foreground flex items-center gap-1">
                Total Túmulos
                <Tooltip>
                  <TooltipTrigger asChild>
                    <Info className="size-3 cursor-default opacity-50 hover:opacity-100 transition-opacity" />
                  </TooltipTrigger>
                  <TooltipContent>Número total de túmulos cadastrados no cemitério</TooltipContent>
                </Tooltip>
              </p>
              <p className="text-xl font-bold">{stats.total}</p>
            </div>
          </CardContent>
        </Card>
        <Card className="py-3">
          <CardContent className="flex items-center gap-3 px-4 py-0">
            <div className="flex size-9 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-950">
              <CheckCircle2 className="size-4 text-green-600" />
            </div>
            <div className="flex-1">
              <p className="text-xs text-muted-foreground flex items-center gap-1">
                Disponíveis
                <Tooltip>
                  <TooltipTrigger asChild>
                    <Info className="size-3 cursor-default opacity-50 hover:opacity-100 transition-opacity" />
                  </TooltipTrigger>
                  <TooltipContent>Túmulos sem ocupação ativa — prontos para receber um difunto</TooltipContent>
                </Tooltip>
              </p>
              <p className="text-xl font-bold text-green-600">{stats.disponiveis}</p>
            </div>
          </CardContent>
        </Card>
        <Card className="py-3">
          <CardContent className="flex items-center gap-3 px-4 py-0">
            <div className="flex size-9 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950">
              <Users className="size-4 text-blue-600" />
            </div>
            <div className="flex-1">
              <p className="text-xs text-muted-foreground flex items-center gap-1">
                Ocupadas
                <Tooltip>
                  <TooltipTrigger asChild>
                    <Info className="size-3 cursor-default opacity-50 hover:opacity-100 transition-opacity" />
                  </TooltipTrigger>
                  <TooltipContent>Túmulos com ao menos um difunto atualmente sepultado</TooltipContent>
                </Tooltip>
              </p>
              <p className="text-xl font-bold text-blue-600">{stats.ocupadas}</p>
            </div>
          </CardContent>
        </Card>
        <Card className="py-3">
          <CardContent className="flex items-center gap-3 px-4 py-0">
            <div className="flex size-9 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-950">
              <AlertCircle className="size-4 text-amber-600" />
            </div>
            <div className="flex-1">
              <p className="text-xs text-muted-foreground flex items-center gap-1">
                Cobranças Abertas
                <Tooltip>
                  <TooltipTrigger asChild>
                    <Info className="size-3 cursor-default opacity-50 hover:opacity-100 transition-opacity" />
                  </TooltipTrigger>
                  <TooltipContent>Cobranças com situação &quot;em aberto&quot; ou &quot;atrasado&quot; vinculadas ao cemitério</TooltipContent>
                </Tooltip>
              </p>
              <p className="text-xl font-bold text-amber-600">{stats.em_aberto}</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <Tabs value={activeTab} onValueChange={setActiveTab}>
          <CardHeader className="flex-col items-stretch justify-start gap-0 border-b border-border pt-4 pb-0 min-h-0 px-0">
            <TabsList variant="line" className="w-full justify-start gap-6 px-5">
              <TabsTrigger value="difuntos">Difuntos</TabsTrigger>
              <TabsTrigger value="tumulos">Túmulos</TabsTrigger>
              <TabsTrigger value="cobrancas">Cobranças</TabsTrigger>
            </TabsList>
          </CardHeader>

          <TabsContent value="difuntos" className="mt-0">
            <DifuntosTable refreshKey={refreshKey} onSaved={handleSaved} onEdit={handleEditDifunto} onClone={handleCloneDifunto} />
          </TabsContent>

          <TabsContent value="tumulos" className="mt-0">
            <TumulosTable refreshKey={refreshKey} onSaved={handleSaved} onEdit={handleEditTumulo} />
          </TabsContent>

          <TabsContent value="cobrancas" className="mt-0">
            <CobrancasTab refreshKey={refreshKey} onSaved={handleSaved} />
          </TabsContent>
        </Tabs>
      </Card>

      <DifuntoFormSheet
        open={novoDifuntoOpen}
        onOpenChange={(v) => {
          setNovoDifuntoOpen(v);
          if (!v) { setEditingDifuntoId(null); setCloneDifuntoId(null); }
        }}
        onSaved={handleSaved}
        editingId={editingDifuntoId}
        cloneFromId={cloneDifuntoId}
      />

      <TumuloFormSheet
        open={tumuloSheetOpen}
        onOpenChange={(v) => {
          setTumuloSheetOpen(v);
          if (!v) setEditingTumuloId(null);
        }}
        onSaved={handleSaved}
        editingId={editingTumuloId}
      />
    </div>
  );
}

