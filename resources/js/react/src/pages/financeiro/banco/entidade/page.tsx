import { useParams } from 'react-router-dom';
import { History, Info, ListChecks, ArrowLeftRight } from 'lucide-react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { ConciliacoesPendentesTab } from '@/pages/financeiro/banco/entidade/conciliacoes-pendentes-tab';
import { EntidadePageHeader } from '@/pages/financeiro/banco/entidade/entidade-page-header';
import { MovimentacoesTab } from '@/pages/financeiro/banco/entidade/movimentacoes-tab';
import { HistoricoConciliacoesTab } from '@/pages/financeiro/banco/entidade/historico-conciliacoes-tab';

export function EntidadePage() {
  const { id } = useParams<{ id: string }>();

  return (
    <div className="container">
      <EntidadePageHeader entidadeId={id} />

      <Tabs defaultValue="conciliacoes">
        {/* Cabeçalho: tabs + ações */}
        <div className="flex items-center justify-between border-b border-border pb-0 mb-6">
          <TabsList variant="line" size="md">
            <TabsTrigger value="conciliacoes">
              <ListChecks className="text-amber-500" />
              Conciliações Pendentes
            </TabsTrigger>
            <TabsTrigger value="movimentacoes">
              <ArrowLeftRight className="text-blue-500" />
              Movimentações
            </TabsTrigger>
            <TabsTrigger value="informacoes">
              <Info className="text-slate-500" />
              Informações
            </TabsTrigger>
            <TabsTrigger value="historico">
              <History className="text-purple-500" />
              Histórico
            </TabsTrigger>
          </TabsList>
        </div>

        {/* Conteúdo das abas */}
        <TabsContent value="conciliacoes">
          <ConciliacoesPendentesTab entidadeId={id} />
        </TabsContent>

        <TabsContent value="movimentacoes">
          <MovimentacoesTab entidadeId={id} />
        </TabsContent>

        <TabsContent value="informacoes">
          <div className="p-8 text-center text-muted-foreground border rounded-xl bg-card">
            Aba de Informações (Em desenvolvimento para a conta {id})
          </div>
        </TabsContent>

        <TabsContent value="historico">
          <HistoricoConciliacoesTab entidadeId={id} />
        </TabsContent>
      </Tabs>
    </div>
  );
}
