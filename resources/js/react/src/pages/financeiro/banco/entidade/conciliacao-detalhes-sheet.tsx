import { useEffect, useState } from 'react';
import { Loader2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Sheet, SheetBody, SheetContent, SheetDescription, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { notify } from '@/lib/notify';

function formatBRLReais(v: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);
}

/** Resposta JSON de GET relatorios/conciliacao/{id}/detalhes */
export interface DetalhesConciliacaoJson {
  id: number;
  transacao_id?: number | null;
  descricao?: string;
  tipo?: string;
  valor?: number;
  status_conciliacao?: string;
  data_extrato_formatada?: string;
  data_conciliacao_formatada?: string;
  data_competencia_formatada?: string;
  lancamento_padrao?: string;
  centro_custo?: string;
  entidade_financeira?: string;
  arquivo_ofx?: string;
  data_importacao_ofx_formatada?: string;
  historico_complementar?: string | null;
  tipo_documento?: string;
  numero_documento?: string;
  origem?: string;
  created_by_name?: string;
  created_at_formatado?: string;
  updated_by_name?: string;
  updated_at_formatado?: string;
}

const STATUS_CONC_DETALHES: Record<string, string> = {
  ok: 'Conciliado',
  ignorado: 'Ignorado',
  pendente: 'Pendente',
  divergente: 'Divergente',
  parcial: 'Parcial',
};

export interface ConciliacaoDetalhesSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  /** `BankStatement::id` — mesmo id usado em GET/POST de conciliação */
  bankStatementId: number | null;
}

export function ConciliacaoDetalhesSheet({ open, onOpenChange, bankStatementId }: ConciliacaoDetalhesSheetProps) {
  const [loading, setLoading] = useState(false);
  const [detalhes, setDetalhes] = useState<DetalhesConciliacaoJson | null>(null);

  useEffect(() => {
    if (!open) {
      setDetalhes(null);
      setLoading(false);
      return;
    }
    if (bankStatementId == null || bankStatementId <= 0) return;

    let cancelled = false;
    setLoading(true);
    setDetalhes(null);

    (async () => {
      try {
        const res = await fetch(`/relatorios/conciliacao/${bankStatementId}/detalhes`, {
          headers: { Accept: 'application/json' },
          credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('Erro ao carregar detalhes');
        const data = (await res.json()) as DetalhesConciliacaoJson;
        if (!cancelled) setDetalhes(data);
      } catch {
        notify.error('Erro', 'Não foi possível carregar os detalhes da conciliação.');
      } finally {
        if (!cancelled) setLoading(false);
      }
    })();

    return () => {
      cancelled = true;
    };
  }, [open, bankStatementId]);

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="w-full sm:max-w-lg sm:rounded-s-xl">
        <SheetHeader>
          <SheetTitle>Detalhes da conciliação</SheetTitle>
          <SheetDescription className="sr-only">
            Dados do extrato bancário e da transação financeira vinculada.
          </SheetDescription>
        </SheetHeader>
        <SheetBody>
          {loading && (
            <div className="flex items-center gap-2 py-8 text-muted-foreground">
              <Loader2 className="size-5 animate-spin" />
              Carregando…
            </div>
          )}
          {!loading && detalhes && (
            <ScrollArea className="max-h-[calc(100vh-8rem)] pr-3">
              <dl className="space-y-3 text-sm">
                <div>
                  <dt className="text-muted-foreground">ID extrato (bank statement)</dt>
                  <dd className="font-mono font-medium">#{detalhes.id}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Status</dt>
                  <dd>
                    <Badge variant="outline">
                      {STATUS_CONC_DETALHES[detalhes.status_conciliacao ?? ''] ??
                        detalhes.status_conciliacao ??
                        '—'}
                    </Badge>
                  </dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Tipo</dt>
                  <dd className="font-medium">{detalhes.tipo === 'entrada' ? 'Entrada' : 'Saída'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Valor</dt>
                  <dd className="font-semibold tabular-nums">{formatBRLReais(Number(detalhes.valor ?? 0))}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Descrição</dt>
                  <dd>{detalhes.descricao ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Data extrato</dt>
                  <dd>{detalhes.data_extrato_formatada ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Data conciliação</dt>
                  <dd>{detalhes.data_conciliacao_formatada ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Competência</dt>
                  <dd>{detalhes.data_competencia_formatada ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Transação</dt>
                  <dd className="font-mono">
                    {detalhes.transacao_id != null ? `#${detalhes.transacao_id}` : '—'}
                  </dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Documento</dt>
                  <dd>
                    {detalhes.tipo_documento && detalhes.tipo_documento !== '-'
                      ? `${detalhes.tipo_documento} · ${detalhes.numero_documento ?? '—'}`
                      : (detalhes.numero_documento ?? '—')}
                  </dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Origem</dt>
                  <dd>{detalhes.origem && detalhes.origem !== '-' ? detalhes.origem : '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Categoria</dt>
                  <dd>{detalhes.lancamento_padrao ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Centro de custo</dt>
                  <dd>{detalhes.centro_custo ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Entidade</dt>
                  <dd>{detalhes.entidade_financeira ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Arquivo OFX</dt>
                  <dd>{detalhes.arquivo_ofx ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Importação OFX</dt>
                  <dd>{detalhes.data_importacao_ofx_formatada ?? '—'}</dd>
                </div>
                <div>
                  <dt className="text-muted-foreground">Histórico complementar</dt>
                  <dd className="whitespace-pre-wrap">{detalhes.historico_complementar ?? '—'}</dd>
                </div>
                <div className="border-t border-border pt-3 text-xs text-muted-foreground">
                  <p>
                    Criado: {detalhes.created_by_name ?? '—'} em {detalhes.created_at_formatado ?? '—'}
                  </p>
                  <p className="mt-1">
                    Atualizado: {detalhes.updated_by_name ?? '—'} em {detalhes.updated_at_formatado ?? '—'}
                  </p>
                </div>
              </dl>
            </ScrollArea>
          )}
        </SheetBody>
      </SheetContent>
    </Sheet>
  );
}
