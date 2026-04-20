import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { FilePenLine, ClipboardList, ChevronDown, Landmark } from 'lucide-react';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
  Combobox,
  ComboboxContent,
  ComboboxEmpty,
  ComboboxInput,
  ComboboxItem,
  ComboboxList,
  ComboboxTrigger,
  ComboboxValue,
} from '@/components/ui/combobox';
import { Badge } from '@/components/ui/badge';
import { SuggestionStar } from '@/components/ui/suggestion-star';
import { CategoriaRow } from '../components/categoria-row';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import { useSugestao, buildInitialSug, type SugestaoState } from '@/hooks/useSugestao';
import type {
  ConciliacaoFormOptions,
  ConciliacaoFormEntidadeBanco,
  ConciliacaoFormParceiro,
  ConciliacaoItem,
} from '@/hooks/useConciliacoesPendentes';

// ── helpers ───────────────────────────────────────────────────────────────

function readSugestao(row: ConciliacaoItem): Record<string, unknown> | null {
  const s = row.sugestao;
  if (s && typeof s === 'object' && !Array.isArray(s)) {
    return s as Record<string, unknown>;
  }
  return null;
}

const MEMO_NOISE = [
  'PIX ENVIADO', 'PIX RECEBIDO', 'TRANSF. TITULARIDADE',
  'PGTO COMPRA', 'PGTO ', 'PAYMENT ', 'TED ', 'DOC ',
];

function cleanMemo(raw: string | null): string {
  if (!raw) return '';
  let cleaned = raw.toUpperCase();
  for (const noise of MEMO_NOISE) {
    cleaned = cleaned.split(noise).join('');
  }
  return cleaned.trim().slice(0, 100);
}

const SELECT_EMPTY = '__none__';
const ENTIDADE_BANCO_EMPTY_ID = -999_999;
const PARCEIRO_EMPTY_ID = -999_998;

// ── Component ─────────────────────────────────────────────────────────────

interface ConciliacaoNovoLancamentoFormProps {
  row: ConciliacaoItem;
  entidadeId: number;
  formOptions: ConciliacaoFormOptions;
  csrfToken: string;
  formId: string;
  onSuccess: () => Promise<void>;
  setSubmitting: (v: boolean) => void;
  headerExtra?: React.ReactNode;
}

export function ConciliacaoNovoLancamentoForm({
  row,
  entidadeId,
  formOptions,
  csrfToken,
  formId,
  onSuccess,
  setSubmitting,
  headerExtra,
}: ConciliacaoNovoLancamentoFormProps) {
  const s = row.statement;
  const rawSug = readSugestao(row);
  const tipoTx = s.amount_cents >= 0 ? 'entrada' : 'saida';

  // ── Derived option lists ──────────────────────────────────────────────
  const lpsFiltered = useMemo(() => {
    return formOptions.lancamentos_padrao
      .filter((lp) => lp.type === tipoTx || lp.type === 'ambos')
      .sort((a, b) => a.description.localeCompare(b.description, 'pt-BR'));
  }, [formOptions.lancamentos_padrao, tipoTx]);

  const parceirosFiltrados = useMemo(() => {
    const permitidos = tipoTx === 'saida' ? ['fornecedor', 'ambos'] : ['cliente', 'ambos'];
    return formOptions.parceiros.filter((p) => permitidos.includes(p.natureza));
  }, [formOptions.parceiros, tipoTx]);

  const entidadesBancoComVazio = useMemo((): ConciliacaoFormEntidadeBanco[] => {
    const empty: ConciliacaoFormEntidadeBanco = { id: ENTIDADE_BANCO_EMPTY_ID, nome: 'Selecione…', tipo: '' };
    return [empty, ...formOptions.entidades_banco];
  }, [formOptions.entidades_banco]);

  const parceirosComVazio = useMemo((): ConciliacaoFormParceiro[] => {
    const empty: ConciliacaoFormParceiro = { id: PARCEIRO_EMPTY_ID, nome: '—', natureza: 'ambos' };
    return [empty, ...parceirosFiltrados];
  }, [parceirosFiltrados]);

  // ── Initial field values (from initial suggestion) ─────────────────────
  const initialSugState = useMemo(() => buildInitialSug(rawSug), [rawSug]);

  const initialLp = useMemo(() => {
    if (initialSugState.lancamento_padrao_id && lpsFiltered.some((lp) => String(lp.id) === initialSugState.lancamento_padrao_id)) {
      return initialSugState.lancamento_padrao_id;
    }
    return lpsFiltered[0]?.id != null ? String(lpsFiltered[0].id) : '';
  }, [initialSugState.lancamento_padrao_id, lpsFiltered]);

  const initialCc = useMemo(() => {
    if (initialSugState.cost_center_id && formOptions.centros.some((c) => String(c.id) === initialSugState.cost_center_id)) {
      return initialSugState.cost_center_id;
    }
    return formOptions.centros[0]?.id != null ? String(formOptions.centros[0].id) : '';
  }, [initialSugState.cost_center_id, formOptions.centros]);

  const initialForma = useMemo(() => {
    if (initialSugState.tipo_documento && formOptions.formas_pagamento.some((f) => f.codigo === initialSugState.tipo_documento)) {
      return initialSugState.tipo_documento;
    }
    return formOptions.formas_pagamento[0]?.codigo ?? '';
  }, [initialSugState.tipo_documento, formOptions.formas_pagamento]);

  const initialDescricao = useMemo(
    () => initialSugState.descricao ?? (cleanMemo(s.memo) || s.memo || ''),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [],
  );

  const initialParceiro = useMemo(() => {
    if (initialSugState.parceiro_id && parceirosFiltrados.some((p) => String(p.id) === initialSugState.parceiro_id)) {
      return initialSugState.parceiro_id;
    }
    return '';
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const lpItemToString = useMemo(
    () => (lp: (typeof lpsFiltered)[number]) => {
      const idx = lpsFiltered.findIndex((x) => x.id === lp.id);
      return `${idx + 1}. ${lp.description}`;
    },
    [lpsFiltered],
  );

  // ── Form state ────────────────────────────────────────────────────────
  const [descricao2, setDescricao2] = useState(initialDescricao);
  const [costCenterId, setCostCenterId] = useState(initialCc);
  const [lancamentoPadraoId, setLancamentoPadraoId] = useState(initialLp);
  const [tipoDocumento, setTipoDocumento] = useState(initialForma);
  const [fornecedorId, setFornecedorId] = useState(initialParceiro);
  const [numeroDocumento, setNumeroDocumento] = useState(() => s.checknum ?? '');
  const [comprovacaoFiscal, setComprovacaoFiscal] = useState(false);
  const [entidadeBancoId, setEntidadeBancoId] = useState('');
  const [anexo, setAnexo] = useState<File | null>(null);
  const [extrasOpen, setExtrasOpen] = useState(false);
  const submitLocked = useRef(false);

  // Sync initial values from suggestion on mount
  useEffect(() => { setCostCenterId(initialCc); }, [initialCc]);
  useEffect(() => { setLancamentoPadraoId(initialLp); }, [initialLp]);
  useEffect(() => { setTipoDocumento(initialForma); }, [initialForma]);

  // ── Sugestões da IA (hook reutilizável) ────────────────────────────────
  const valorDecimal = Math.abs(s.amount_cents) / 100;

  const handleSugestaoApply = useCallback(
    (sug: SugestaoState, manualEditsSet: Set<string>) => {
      if (sug.lancamento_padrao_id && !manualEditsSet.has('lancamento_padrao_id')) setLancamentoPadraoId(sug.lancamento_padrao_id);
      if (sug.cost_center_id && !manualEditsSet.has('cost_center_id')) setCostCenterId(sug.cost_center_id);
      if (sug.tipo_documento && !manualEditsSet.has('tipo_documento')) setTipoDocumento(sug.tipo_documento);
      if (sug.parceiro_id && !manualEditsSet.has('parceiro_id')) setFornecedorId(sug.parceiro_id);
      if (sug.descricao && !manualEditsSet.has('descricao')) setDescricao2(sug.descricao);
    },
    [],
  );

  const { sugState, confiancaCampos, markManualEdit } = useSugestao({
    enabled: true,
    descricao: descricao2,
    parceiroId: fornecedorId,
    valor: valorDecimal,
    initialSugestao: rawSug,
    onApply: handleSugestaoApply,
  });

  // ── Manual change handlers ─────────────────────────────────────────────
  function onDescricaoChange(v: string) {
    markManualEdit('descricao');
    setDescricao2(v);
  }
  function onLpChange(k: string | null) {
    markManualEdit('lancamento_padrao_id');
    setLancamentoPadraoId(k ?? '');
  }
  function onCcChange(k: string | null) {
    markManualEdit('cost_center_id');
    setCostCenterId(k ?? '');
  }
  function onFormaChange(k: string | null) {
    markManualEdit('tipo_documento');
    setTipoDocumento(k ?? '');
  }
  function onParceiroChange(k: string | null) {
    markManualEdit('parceiro_id');
    setFornecedorId(k === SELECT_EMPTY ? '' : k ?? '');
  }

  // ── Derived ───────────────────────────────────────────────────────────
  const depositoId = formOptions.deposito_lancamento_padrao_id;
  const precisaBancoDeposito =
    depositoId != null && lancamentoPadraoId !== '' && Number(lancamentoPadraoId) === depositoId;


  // ── Submit ────────────────────────────────────────────────────────────
  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!csrfToken || submitLocked.current) return;
    submitLocked.current = true;
    setSubmitting(true);

    if (precisaBancoDeposito && !entidadeBancoId) {
      notify.error('Banco de depósito obrigatório', 'Selecione o banco de depósito para esta categoria.');
      submitLocked.current = false;
      setSubmitting(false);
      return;
    }

    const descricaoBase = (s.memo && s.memo.trim()) ? s.memo : (descricao2.trim() || 'Conciliação');
    const fd = new FormData();
    fd.append('tipo', tipoTx);
    fd.append('valor', String(valorDecimal));
    fd.append('data_competencia', s.dtposted ?? '');
    fd.append('numero_documento', numeroDocumento.trim() || (s.checknum ?? ''));
    fd.append('descricao', descricaoBase);
    fd.append('descricao2', descricao2.trim() || descricaoBase);
    fd.append('origem', 'Conciliação Bancária');
    fd.append('entidade_id', String(entidadeId));
    fd.append('bank_statement_id', String(s.id));
    fd.append('cost_center_id', costCenterId);
    fd.append('lancamento_padrao_id', lancamentoPadraoId);
    fd.append('tipo_documento', tipoDocumento);
    fd.append('comprovacao_fiscal', comprovacaoFiscal ? '1' : '0');
    if (fornecedorId) fd.append('fornecedor_id', fornecedorId);
    if (precisaBancoDeposito && entidadeBancoId) fd.append('entidade_banco_id', entidadeBancoId);
    if (anexo) fd.append('anexo', anexo);

    // Feedback: enviar sugestão original para o backend comparar
    if (sugState.origem) {
      fd.append('sug_origem', sugState.origem);
      fd.append('sug_confianca', String(sugState.confianca));
      if (sugState.lancamento_padrao_id) fd.append('sug_lancamento_padrao_id', sugState.lancamento_padrao_id);
      if (sugState.cost_center_id) fd.append('sug_cost_center_id', sugState.cost_center_id);
      if (sugState.tipo_documento) fd.append('sug_tipo_documento', sugState.tipo_documento);
      if (sugState.descricao) fd.append('sug_descricao', sugState.descricao);
      if (sugState.parceiro_id) fd.append('sug_parceiro_id', sugState.parceiro_id);
    }

    try {
      const res = await fetch('/conciliacao/conciliar', {
        method: 'POST',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        body: fd,
        credentials: 'same-origin',
      });
      const json = (await res.json()) as { success?: boolean; message?: string; errors?: Record<string, string[]> };
      if (res.status === 422 && json.errors) { notify.validationErrors(json.errors); return; }
      if (!res.ok || !json.success) { notify.error('Não foi possível conciliar', json.message ?? 'Verifique os dados e tente novamente.'); return; }
      notify.success('Conciliado!', json.message ?? 'Lançamento criado e vinculado ao extrato.');
      await onSuccess();
    } catch {
      notify.networkError(() => (document.getElementById(formId) as HTMLFormElement | null)?.requestSubmit());
    } finally {
      submitLocked.current = false;
      setSubmitting(false);
    }
  }

  // ── Render ────────────────────────────────────────────────────────────
  const comboboxTriggerClass =
    'w-full justify-between font-normal h-8.5 px-3 text-[0.8125rem] border-input shadow-xs';

  const cc = confiancaCampos;
  const starBase = { origem: sugState.origem };

  return (
    <Card className="h-full min-h-[200px] border-dashed border-primary/20">
      <CardHeader className="space-y-2 px-5 pb-3 pt-4">
        {headerExtra ?? (
          <Badge variant="secondary" className="inline-flex items-center gap-1.5">
            <FilePenLine className="size-3.5 opacity-90" aria-hidden />
            Novo lançamento
          </Badge>
        )}
      </CardHeader>
      <CardContent className="px-5 pb-5">
        <form id={formId} className="space-y-4" onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
            {/* ── Descrição ─────────────────────────────────────────── */}
            <div className="space-y-1.5 sm:col-span-3">
              <Label htmlFor={`desc2-${s.id}`}>Descrição</Label>
              <div className="relative">
                <Input
                  id={`desc2-${s.id}`}
                  value={descricao2}
                  onChange={(e) => onDescricaoChange(e.target.value)}
                  required
                  placeholder="Ex.: PAYMENT - Fornecedor"
                  className="pr-18"
                />
                <SuggestionStar
                  currentValue={descricao2}
                  suggestedValue={sugState.descricao}
                  placement="absolute"
                  confianca={cc.descricao}
                  {...starBase}
                />
              </div>
            </div>

            {/* ── Lançamento padrão ─────────────────────────────────── */}
            <div className="space-y-1.5 sm:col-span-3">
              <Label htmlFor={`lp-${s.id}`}>Lançamento padrão</Label>
              <Combobox
                items={lpsFiltered}
                valueKey={lancamentoPadraoId || null}
                onValueChangeKey={onLpChange}
                getItemKey={(lp) => String(lp.id)}
                itemToString={lpItemToString}
              >
                <ComboboxTrigger
                  render={
                    <Button id={`lp-${s.id}`} type="button" variant="outline" className={comboboxTriggerClass}>
                      <ComboboxValue />
                      <span className="inline-flex shrink-0 items-center gap-1.5">
                        <SuggestionStar
                          currentValue={lancamentoPadraoId}
                          suggestedValue={sugState.lancamento_padrao_id}
                          confianca={cc.lancamento_padrao_id}
                          {...starBase}
                        />
                        <ChevronDown className="h-4 w-4 shrink-0 opacity-50" aria-hidden />
                      </span>
                    </Button>
                  }
                />
                <ComboboxContent>
                  <ComboboxInput showTrigger={false} placeholder="Buscar lançamento…" />
                  <ComboboxEmpty>Nenhum lançamento encontrado.</ComboboxEmpty>
                  <ComboboxList itemsType={lpsFiltered}>
                    {(lp) => (
                      <ComboboxItem value={lp}>
                        <CategoriaRow
                          categoria={{
                            id: lp.id,
                            codigo: lp.codigo,
                            description: lp.description,
                            type: lp.type,
                            scope: lp.scope,
                            company_ids: lp.company_ids,
                          }}
                          className="w-full"
                        />
                      </ComboboxItem>
                    )}
                  </ComboboxList>
                </ComboboxContent>
              </Combobox>
            </div>

            {/* ── Centro de custo ───────────────────────────────────── */}
            <div className="space-y-1.5 sm:col-span-2">
              <Label htmlFor={`cc-${s.id}`}>Centro de custo</Label>
              <Combobox
                items={formOptions.centros}
                valueKey={costCenterId || null}
                onValueChangeKey={onCcChange}
                getItemKey={(c) => String(c.id)}
                itemToString={(c) => c.name}
              >
                <ComboboxTrigger
                  render={
                    <Button id={`cc-${s.id}`} type="button" variant="outline" className={comboboxTriggerClass}>
                      <ComboboxValue />
                      <span className="inline-flex shrink-0 items-center gap-1.5">
                        <SuggestionStar
                          currentValue={costCenterId}
                          suggestedValue={sugState.cost_center_id}
                          confianca={cc.cost_center_id}
                          {...starBase}
                        />
                        <ChevronDown className="h-4 w-4 shrink-0 opacity-50" aria-hidden />
                      </span>
                    </Button>
                  }
                />
                <ComboboxContent>
                  <ComboboxInput showTrigger={false} placeholder="Buscar centro de custo…" />
                  <ComboboxEmpty>Nenhum centro encontrado.</ComboboxEmpty>
                  <ComboboxList itemsType={formOptions.centros}>
                    {(c) => <ComboboxItem value={c}>{c.name}</ComboboxItem>}
                  </ComboboxList>
                </ComboboxContent>
              </Combobox>
            </div>

            {/* ── Forma de pagamento ────────────────────────────────── */}
            <div className="space-y-1.5 sm:col-span-1">
              <Label htmlFor={`forma-${s.id}`}>Forma de pagamento</Label>
              <Combobox
                items={formOptions.formas_pagamento}
                valueKey={tipoDocumento || null}
                onValueChangeKey={onFormaChange}
                getItemKey={(f) => f.codigo}
                itemToString={(f) => `${f.id} — ${f.nome}`}
              >
                <ComboboxTrigger
                  render={
                    <Button id={`forma-${s.id}`} type="button" variant="outline" className={comboboxTriggerClass}>
                      <ComboboxValue />
                      <span className="inline-flex shrink-0 items-center gap-1.5">
                        <SuggestionStar
                          currentValue={tipoDocumento}
                          suggestedValue={sugState.tipo_documento}
                          confianca={cc.tipo_documento}
                          {...starBase}
                        />
                        <ChevronDown className="h-4 w-4 shrink-0 opacity-50" aria-hidden />
                      </span>
                    </Button>
                  }
                />
                <ComboboxContent>
                  <ComboboxInput showTrigger={false} placeholder="Buscar forma…" />
                  <ComboboxEmpty>Nenhuma forma encontrada.</ComboboxEmpty>
                  <ComboboxList itemsType={formOptions.formas_pagamento}>
                    {(f) => <ComboboxItem value={f}>{f.id} — {f.nome}</ComboboxItem>}
                  </ComboboxList>
                </ComboboxContent>
              </Combobox>
            </div>
          </div>

          {/* ── Banco de depósito ──────────────────────────────────── */}
          {precisaBancoDeposito && (
            <div className="space-y-1.5 rounded-md border border-amber-500/40 bg-amber-500/5 p-3">
              <Label htmlFor={`ebanco-${s.id}`} className="inline-flex items-center gap-2">
                <Landmark className="size-4 text-amber-700 dark:text-amber-300 shrink-0" aria-hidden />
                Banco de depósito
              </Label>
              <Combobox
                items={entidadesBancoComVazio}
                valueKey={entidadeBancoId || SELECT_EMPTY}
                onValueChangeKey={(k) => setEntidadeBancoId(k === SELECT_EMPTY ? '' : k ?? '')}
                getItemKey={(e) => (e.id === ENTIDADE_BANCO_EMPTY_ID ? SELECT_EMPTY : String(e.id))}
                itemToString={(e) => `${e.nome}${e.tipo ? ` (${e.tipo})` : ''}`}
              >
                <ComboboxTrigger
                  render={
                    <Button id={`ebanco-${s.id}`} type="button" variant="outline" className={comboboxTriggerClass}>
                      <ComboboxValue />
                      <ChevronDown className="h-4 w-4 shrink-0 opacity-50" aria-hidden />
                    </Button>
                  }
                />
                <ComboboxContent>
                  <ComboboxInput showTrigger={false} placeholder="Buscar banco…" />
                  <ComboboxEmpty>Nenhum banco encontrado.</ComboboxEmpty>
                  <ComboboxList itemsType={entidadesBancoComVazio}>
                    {(b) => <ComboboxItem value={b}>{b.nome}{b.tipo ? ` (${b.tipo})` : ''}</ComboboxItem>}
                  </ComboboxList>
                </ComboboxContent>
              </Combobox>
            </div>
          )}

          {/* ── Extras (toggle) ────────────────────────────────────── */}
          <button
            type="button"
            className={cn(
              'flex w-full items-center justify-between gap-2 rounded-lg border px-3 py-2 text-left text-sm transition-colors',
              extrasOpen ? 'border-primary/40 bg-muted/30' : 'border-border hover:bg-muted/20',
            )}
            onClick={() => setExtrasOpen(!extrasOpen)}
          >
            <span className="inline-flex items-center gap-2 min-w-0">
              <ClipboardList className="size-4 shrink-0 text-sky-600 dark:text-sky-400 opacity-90" aria-hidden />
              <span className="truncate">
                Completar informações <span className="text-muted-foreground">(opcional)</span>
              </span>
            </span>
            <ChevronDown
              className={cn('size-4 shrink-0 text-muted-foreground transition-transform', extrasOpen && 'rotate-180')}
              aria-hidden
            />
          </button>

          {extrasOpen && (
            <div className="grid gap-3 sm:grid-cols-2 border-t pt-3">
              {/* ── Fornecedor / Cliente ───────────────────────────── */}
              <div className="space-y-1.5 sm:col-span-2">
                <Label htmlFor={`forn-${s.id}`}>{tipoTx === 'saida' ? 'Fornecedor' : 'Cliente'}</Label>
                <Combobox
                  items={parceirosComVazio}
                  valueKey={fornecedorId || SELECT_EMPTY}
                  onValueChangeKey={onParceiroChange}
                  getItemKey={(p) => (p.id === PARCEIRO_EMPTY_ID ? SELECT_EMPTY : String(p.id))}
                  itemToString={(p) => p.nome}
                >
                  <ComboboxTrigger
                    render={
                      <Button id={`forn-${s.id}`} type="button" variant="outline" className={comboboxTriggerClass}>
                        <ComboboxValue />
                        <span className="inline-flex shrink-0 items-center gap-1.5">
                          <SuggestionStar
                            currentValue={fornecedorId}
                            suggestedValue={sugState.parceiro_id}
                            confianca={cc.parceiro_id}
                            {...starBase}
                          />
                          <ChevronDown className="h-4 w-4 shrink-0 opacity-50" aria-hidden />
                        </span>
                      </Button>
                    }
                  />
                  <ComboboxContent>
                    <ComboboxInput showTrigger={false} placeholder="Buscar…" />
                    <ComboboxEmpty>Nenhum cadastro encontrado.</ComboboxEmpty>
                    <ComboboxList itemsType={parceirosComVazio}>
                      {(p) => <ComboboxItem value={p}>{p.nome}</ComboboxItem>}
                    </ComboboxList>
                  </ComboboxContent>
                </Combobox>
              </div>
              <div className="space-y-1.5">
                <Label htmlFor={`ndoc-${s.id}`}>Nº documento</Label>
                <Input
                  id={`ndoc-${s.id}`}
                  value={numeroDocumento}
                  onChange={(e) => setNumeroDocumento(e.target.value)}
                />
              </div>
              <div className="flex items-center gap-2 sm:col-span-2 pt-1">
                <input
                  type="checkbox"
                  id={`nf-${s.id}`}
                  checked={comprovacaoFiscal}
                  onChange={(e) => setComprovacaoFiscal(e.target.checked)}
                  className="rounded border-input"
                />
                <Label htmlFor={`nf-${s.id}`} className="font-normal cursor-pointer">
                  Existe comprovação fiscal?
                </Label>
              </div>
              <div className="space-y-1.5 sm:col-span-2">
                <Label htmlFor={`anexo-${s.id}`}>Anexo</Label>
                <Input
                  id={`anexo-${s.id}`}
                  type="file"
                  accept=".pdf,.jpg,.jpeg,.png,.ofx"
                  onChange={(e) => setAnexo(e.target.files?.[0] ?? null)}
                />
              </div>
            </div>
          )}
        </form>
      </CardContent>
    </Card>
  );
}
