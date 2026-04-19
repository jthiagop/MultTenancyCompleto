import { useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import {
  Toolbar,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { PlusCircle, ChevronDown, FileText, TrendingUp, TrendingDown, ArrowLeftRight, BookText } from 'lucide-react';
import { AIButton } from '@/components/ui/ai-button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ReceitasTable } from '@/pages/financeiro/components/receitas-table';
import { DespesasTable } from '@/pages/financeiro/components/despesas-table';
import { ExtratoTable } from '@/pages/financeiro/components/extrato-table';
import { LancamentoDrawer, type TipoLancamento } from '@/pages/financeiro/banco/components/lancamento-drawer';
import { TransferenciaSheet } from '@/pages/financeiro/banco/components/transferencia-sheet';
import { PagamentoSheet } from '@/pages/financeiro/banco/components/pagamento-sheet';
import { TransacaoDetalhesSheet } from '@/pages/financeiro/banco/components/transacao-detalhes-sheet';
import {
  buildGerarReciboContext,
  GerarReciboDialog,
  type GerarReciboContext,
} from '@/pages/financeiro/banco/components/gerar-recibo-dialog';
import { notify } from '@/lib/notify';
import { ConciliacaoOFXDialog } from '@/pages/financeiro/banco/components/conciliacao-ofx-dialog';
import { BancoCarouselCard } from '@/pages/financeiro/banco/components/banco-carousel-card';
import type { OnEditTransacao } from '@/pages/financeiro/components/transacao-table-shared';
import { FinanceiroMoreOptionsMenu } from '@/pages/financeiro/components/financeiro-more-options-menu';
import { BoletimFinanceiroDialog } from '@/pages/financeiro/components/boletim-financeiro-dialog';
import { ExtratoPdfDialog } from '@/pages/financeiro/components/extrato-pdf-dialog';
import { PrestacaoContasDialog } from '@/pages/financeiro/components/prestacao-contas-dialog';
import { ConciliacaoRelatorioDialog } from '@/pages/financeiro/components/conciliacao-relatorio-dialog';
import { OFXExportDialog } from '@/pages/financeiro/components/ofx-export-dialog';
import { ContabilidadeDialog } from '@/pages/financeiro/components/contabilidade-dialog';
import { FinanceiroBreadcrumb } from '@/pages/financeiro/components/financeiro-breadcrumb';

interface EditState {
  id: string;
  tipo: TipoLancamento;
}

export function FinanceiroPage() {
  const [drawerTipo, setDrawerTipo] = useState<TipoLancamento | null>(null);
  const [transferenciaOpen, setTransferenciaOpen] = useState(false);
  const [editState, setEditState] = useState<EditState | null>(null);
  const [pagamentoId, setPagamentoId] = useState<string | null>(null);
  const [detalhesId, setDetalhesId] = useState<string | null>(null);
  const [reciboDialogOpen, setReciboDialogOpen] = useState(false);
  const [reciboDialogId, setReciboDialogId] = useState<string | null>(null);
  const [reciboDialogCtx, setReciboDialogCtx] = useState<GerarReciboContext | null>(null);
  const [refreshKey, setRefreshKey] = useState(0);
  const navigate = useNavigate();
  const [boletimOpen, setBoletimOpen] = useState(false);
  const [extratoOpen, setExtratoOpen] = useState(false);
  const [prestacaoOpen, setPrestacaoOpen] = useState(false);
  const [conciliacaoOpen, setConciliacaoOpen] = useState(false);
  const [ofxOpen, setOfxOpen] = useState(false);
  const [contabilidadeOpen, setContabilidadeOpen] = useState(false);
  const [searchParams, setSearchParams] = useSearchParams();

  // Abre o PagamentoSheet automaticamente quando vindo de uma notificação (?pagamento=X)
  useEffect(() => {
    const pagamentoId = searchParams.get('pagamento');
    if (pagamentoId) {
      setPagamentoId(pagamentoId);
      setSearchParams({}, { replace: true });
    }
  }, [searchParams, setSearchParams]);

  function handleSaved() {
    setRefreshKey((k) => k + 1);
  }

  function handleOfxImported(entidadeId: number | null) {
    setRefreshKey((k) => k + 1);
    if (entidadeId) {
      navigate(`/financeiro/banco/entidade/${entidadeId}`);
    }
  }

  function handleInformarPagamento(id: string) {
    setPagamentoId(id);
  }

  function handleOpenDetalhes(id: string) {
    setDetalhesId(id);
  }

  async function handleOpenRecibo(id: string) {
    try {
      const res = await fetch(`/financeiro/transacao/${id}/detalhes`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('fetch');
      const data = (await res.json()) as {
        recibo?: { id: number } | null;
        id: number;
        tipo: string;
        descricao: string | null;
        historico_complementar?: string | null;
        valor: number;
        data_competencia_formatada: string | null;
        parceiro?: GerarReciboContext['parceiro'];
      };
      if (data.recibo?.id) {
        window.open(`/relatorios/recibo/imprimir/${data.recibo.id}`, '_blank', 'noopener,noreferrer');
        notify.success('Recibo', 'Abrindo o PDF…');
        return;
      }
      setReciboDialogCtx(buildGerarReciboContext(data));
      setReciboDialogId(id);
      setReciboDialogOpen(true);
    } catch {
      notify.error('Erro', 'Não foi possível carregar os dados para o recibo.');
    }
  }

  const handleEditReceita: OnEditTransacao = (id, options) => {
    setEditState({ id, tipo: options?.tipo ?? 'receita' });
  };

  const handleEditDespesa: OnEditTransacao = (id, options) => {
    setEditState({ id, tipo: options?.tipo ?? 'despesa' });
  };

  const handleEditExtrato: OnEditTransacao = (id, options) => {
    setEditState({ id, tipo: options?.tipo ?? 'receita' });
  };

  function handleDrawerClose() {
    setDrawerTipo(null);
    setEditState(null);
  }

  return (
    <div className="container">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Gestão financeira</ToolbarPageTitle>
          <FinanceiroBreadcrumb currentLabel="Gestão financeira" />
        </ToolbarHeading>
                 {/* Botões de ação */}
          <div className="flex items-center gap-2 pb-1">
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button size="md" className="bg-blue-600 hover:bg-blue-700 text-white border-0">
                  <PlusCircle className="size-4" />
                  Novo Lançamento
                  <ChevronDown className="size-3.5 opacity-70" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-52">
                <div className="px-2 pt-1.5 pb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">
                  O que deseja criar?
                </div>
                <DropdownMenuItem onClick={() => setDrawerTipo('receita')}>
                  <TrendingUp className="text-green-500" />
                  Nova Receita
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => setDrawerTipo('despesa')}>
                  <TrendingDown className="text-red-500" />
                  Nova Despesa
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => setTransferenciaOpen(true)}>
                  <ArrowLeftRight className="text-blue-500" />
                  Transferência
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <ConciliacaoOFXDialog onImported={handleOfxImported} />

            <AIButton to="/financeiro/ia">
              Dominus IA
            </AIButton>

            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button size="md" variant="outline">
                  <FileText className="size-4" />
                  Relatórios
                  <ChevronDown className="size-3.5 opacity-70" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-52">
                <DropdownMenuItem onClick={() => setBoletimOpen(true)}>
                  <FileText className="size-4 text-blue-500" />
                  Boletim Financeiro
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => setExtratoOpen(true)}>
                  Extrato Financeiro
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => setPrestacaoOpen(true)}>
                  Prestação de Contas
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => setConciliacaoOpen(true)}>
                  Conciliação Bancária
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => setOfxOpen(true)}>
                  Exportar OFX
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => setContabilidadeOpen(true)}>
                  Contabilidade (TXT/CSV)
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <FinanceiroMoreOptionsMenu />
          </div>
      </Toolbar>

      <Tabs value={(['receitas', 'despesas', 'extrato'].includes(searchParams.get('tab') ?? '') ? searchParams.get('tab')! : 'receitas')}
            onValueChange={(v) => setSearchParams({ tab: v }, { replace: true })}>
        {/* Cabeçalho: tabs + ações */}
        <div className="flex items-center justify-between border-b border-border pb-0 mb-6">
          <TabsList variant="line" size="md">
            <TabsTrigger value="receitas">
              <TrendingUp className="text-green-500" />
              Receitas
            </TabsTrigger>
            <TabsTrigger value="despesas">
              <TrendingDown className="text-red-500" />
              Despesas
            </TabsTrigger>
            <TabsTrigger value="extrato">
              <BookText className="text-blue-500" />
              Extrato
            </TabsTrigger>
          </TabsList>

 
        </div>

        <div className="mb-6">
          <BancoCarouselCard refreshKey={refreshKey} />
        </div>

        {/* Conteúdo das abas */}
        <TabsContent value="receitas">
          <ReceitasTable
            refreshKey={refreshKey}
            onEdit={handleEditReceita}
            onInformarPagamento={handleInformarPagamento}
            onOpenDetalhes={handleOpenDetalhes}
            onOpenRecibo={handleOpenRecibo}
          />
        </TabsContent>

        <TabsContent value="despesas">
          <DespesasTable
            refreshKey={refreshKey}
            onEdit={handleEditDespesa}
            onInformarPagamento={handleInformarPagamento}
            onOpenDetalhes={handleOpenDetalhes}
            onOpenRecibo={handleOpenRecibo}
          />
        </TabsContent>

        <TabsContent value="extrato">
          <ExtratoTable
            refreshKey={refreshKey}
            onEdit={handleEditExtrato}
            onInformarPagamento={handleInformarPagamento}
            onOpenDetalhes={handleOpenDetalhes}
            onOpenRecibo={handleOpenRecibo}
          />
        </TabsContent>
      </Tabs>

      <LancamentoDrawer
        open={drawerTipo !== null || editState !== null}
        tipo={editState ? editState.tipo : drawerTipo}
        onClose={handleDrawerClose}
        onSaved={handleSaved}
        editId={editState?.id ?? null}
      />

      <TransferenciaSheet
        open={transferenciaOpen}
        onOpenChange={setTransferenciaOpen}
        onSuccess={handleSaved}
      />

      <PagamentoSheet
        open={pagamentoId !== null}
        transacaoId={pagamentoId}
        onClose={() => setPagamentoId(null)}
        onSaved={handleSaved}
      />

      <TransacaoDetalhesSheet
        open={detalhesId !== null}
        transacaoId={detalhesId}
        onClose={() => setDetalhesId(null)}
        onEdit={(id, opts) => {
          setDetalhesId(null);
          const tipo = opts?.tipo ?? 'receita';
          setEditState({ id, tipo });
        }}
      />

      <BoletimFinanceiroDialog open={boletimOpen} onOpenChange={setBoletimOpen} />
      <ExtratoPdfDialog open={extratoOpen} onOpenChange={setExtratoOpen} />
      <PrestacaoContasDialog open={prestacaoOpen} onOpenChange={setPrestacaoOpen} />
      <ConciliacaoRelatorioDialog open={conciliacaoOpen} onOpenChange={setConciliacaoOpen} />
      <OFXExportDialog open={ofxOpen} onOpenChange={setOfxOpen} />
      <ContabilidadeDialog open={contabilidadeOpen} onOpenChange={setContabilidadeOpen} />

      {reciboDialogId && reciboDialogCtx && (
        <GerarReciboDialog
          open={reciboDialogOpen}
          onOpenChange={(v) => {
            setReciboDialogOpen(v);
            if (!v) {
              setReciboDialogId(null);
              setReciboDialogCtx(null);
            }
          }}
          transacaoId={reciboDialogId}
          context={reciboDialogCtx}
          onGerado={handleSaved}
        />
      )}
    </div>
  );
}
