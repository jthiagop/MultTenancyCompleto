import { useState, useCallback, useMemo } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import {
  ToolbarHeading,
} from '@/components/layouts/layout-1/components/toolbar';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { ArrowLeft, FileSearch, Sparkles, Link2 } from 'lucide-react';
import { notify } from '@/lib/notify';
import '@/components/ui/ai-button.css';

import { UploadZone } from './components/upload-zone';
import { DocumentFilter } from './components/document-filter';
import { DocumentList, type DomusDocument } from './components/document-list';
import { DocumentViewer } from './components/document-viewer';
import {
  ExtractedData,
  type DadosExtraidos,
  type CreateLancamentoPayload,
} from './components/extracted-data';
import { IntegracoesTab } from './components/integracoes-tab';
import {
  LancamentoDrawer,
  type TipoLancamento,
  type LancamentoPrefill,
  type LancamentoDocumentPreview,
} from '@/pages/financeiro/banco/components/lancamento-drawer';
import { FinanceiroBreadcrumb } from '@/pages/financeiro/components/financeiro-breadcrumb';

export function DominusIAPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const tabValue = searchParams.get('tab') === 'integracoes' ? 'integracoes' : 'pendentes';

  const [selectedDoc, setSelectedDoc] = useState<DomusDocument | null>(null);
  const [extractedData, setExtractedData] = useState<DadosExtraidos | null>(null);
  const [extractedLoading, setExtractedLoading] = useState(false);
  const [filterType, setFilterType] = useState('');
  const [refreshKey, setRefreshKey] = useState(0);

  // Drawer de lançamento
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [drawerTipo, setDrawerTipo] = useState<TipoLancamento | null>(null);
  const [drawerPrefill, setDrawerPrefill] = useState<LancamentoPrefill | null>(null);

  const fetchDocumentDetails = useCallback(async (docId: number) => {
    setExtractedLoading(true);
    setExtractedData(null);
    try {
      const res = await fetch(`/financeiro/domusia/${docId}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok) {
        notify.error('Erro ao carregar documento', `Não foi possível obter os dados (HTTP ${res.status}).`);
        return;
      }
      const json = await res.json();
      const doc = json.data ?? json;

      if (doc.dados_extraidos) {
        const parsed = typeof doc.dados_extraidos === 'string'
          ? JSON.parse(doc.dados_extraidos)
          : doc.dados_extraidos;
        setExtractedData(parsed);
      }
    } catch {
      notify.error('Erro ao carregar dados extraídos');
    } finally {
      setExtractedLoading(false);
    }
  }, []);

  const handleDocumentSelect = useCallback((doc: DomusDocument) => {
    setSelectedDoc(doc);
    setExtractedData(null);
    if (doc.status === 'processado') {
      fetchDocumentDetails(doc.id);
    }
  }, [fetchDocumentDetails]);

  const handleUploaded = useCallback((documentoId: number) => {
    setRefreshKey((k) => k + 1);
    fetchDocumentDetails(documentoId);
  }, [fetchDocumentDetails]);

  const handleDocDeleted = useCallback(() => {
    setSelectedDoc(null);
    setExtractedData(null);
    setRefreshKey((k) => k + 1);
  }, []);

  const handleCreateLancamento = useCallback((payload: CreateLancamentoPayload) => {
    setDrawerTipo(payload.tipo === 'receita' ? 'receita' : 'despesa');
    setDrawerPrefill({
      descricao: payload.descricao,
      valor: payload.valor,
      dataCompetencia: payload.dataCompetencia,
      vencimento: payload.vencimento,
      formaPagamento: payload.formaPagamento,
      numeroDocumento: payload.numeroDocumento,
      juros: payload.juros,
      multa: payload.multa,
      desconto: payload.desconto,
      domusDocumentoId: selectedDoc?.id,
      historico: payload.historico,
      parceiroDocumento: payload.parceiroDocumento,
      parceiroNomeIa: payload.parceiroNomeIa,
    });
    setDrawerOpen(true);
  }, [selectedDoc]);

  const handleDrawerClose = useCallback(() => {
    setDrawerOpen(false);
    setDrawerTipo(null);
    setDrawerPrefill(null);
  }, []);

  const documentPreview = useMemo<LancamentoDocumentPreview | null>(() => {
    if (!selectedDoc) return null;
    const url = selectedDoc.file_url ?? `/financeiro/domusia/file/${selectedDoc.id}`;
    return {
      url,
      mimeType: selectedDoc.mime_type,
      filename: selectedDoc.nome_arquivo,
    };
  }, [selectedDoc]);

  return (
    <div className="container">
      <Tabs
        value={tabValue}
        onValueChange={(v) => {
          if (v === 'pendentes') {
            setSearchParams({}, { replace: true });
          } else {
            setSearchParams({ tab: v }, { replace: true });
          }
        }}
      >
        <div className="flex w-full flex-wrap items-center justify-between gap-4 border-b border-border pb-4 mb-6">
            <ToolbarHeading>
              <div className="flex items-center gap-2 min-w-0">
                <Button asChild size="sm" variant="ghost" mode="icon" className="size-8 shrink-0">
                  <Link to="/financeiro">
                    <ArrowLeft className="size-4" />
                  </Link>
                </Button>
                <div className="min-w-0">
                  <div className="flex items-center gap-2 text-base font-semibold">
                    <Sparkles className="size-5 ai-btn-icon shrink-0" />
                    <span className="bg-linear-to-r from-violet-500 via-purple-500 to-fuchsia-500 bg-clip-text text-transparent">
                      Dominus IA
                    </span>
                  </div>
                  <FinanceiroBreadcrumb currentLabel="Dominus IA" />
                </div>
              </div>
            </ToolbarHeading>
            <TabsList variant="line" size="md" className="shrink-0">
              <TabsTrigger value="pendentes">
                <FileSearch className="size-4 text-purple-500" />
                Pendentes
              </TabsTrigger>
              <TabsTrigger value="integracoes">
                <Link2 className="size-4 text-blue-500" />
                Integrações
              </TabsTrigger>
            </TabsList>
        </div>

        <TabsContent value="pendentes" className="mt-0">
          <div className="flex flex-col lg:flex-row gap-5">
            {/* Sidebar */}
            <div className="w-full lg:w-[420px] shrink-0 flex flex-col gap-5">
              <UploadZone onUploaded={handleUploaded} />
              <DocumentFilter value={filterType} onChange={setFilterType} />
              <DocumentList
                refreshKey={refreshKey}
                filterType={filterType}
                selectedId={selectedDoc?.id ?? null}
                onSelect={handleDocumentSelect}
              />
            </div>

            {/* Main content */}
            <div className="flex-1 flex flex-col gap-5 min-w-0">
              <DocumentViewer doc={selectedDoc} onDeleted={handleDocDeleted} />
              {(extractedData || extractedLoading) && (
                <ExtractedData
                  data={extractedData ?? {}}
                  loading={extractedLoading}
                  onCreateLancamento={handleCreateLancamento}
                />
              )}
            </div>
          </div>
        </TabsContent>

        <TabsContent value="integracoes" className="mt-0">
          <IntegracoesTab />
        </TabsContent>
      </Tabs>

      <LancamentoDrawer
        open={drawerOpen}
        tipo={drawerTipo}
        prefill={drawerPrefill}
        documentPreview={documentPreview}
        onClose={handleDrawerClose}
        onSaved={(result) => {
          handleDrawerClose();
          setRefreshKey((k) => k + 1);
          if (result?.domus_documento_id != null) {
            setSelectedDoc(null);
            setExtractedData(null);
          }
        }}
      />
    </div>
  );
}
