import {
  useCallback,
  useEffect,
  useLayoutEffect,
  useReducer,
  useRef,
  useState,
  type MutableRefObject,
} from 'react';
import {
  parseLancamentoExistingAnexosApi,
  type LancamentoExistingAnexo,
  type LancamentoStagedAnexo,
} from './lancamento-anexos-input';
import { notify } from '@/lib/notify';
import { useSugestao, type SugestaoState } from '@/hooks/useSugestao';
import type { LancamentoPrefill, TipoLancamento } from './lancamento-drawer';
import type { ParceiroOption } from '@/hooks/useFormSelectData';

// ── Tipos ────────────────────────────────────────────────────────────────────

export interface RateioItem {
  id: string;
  filial_id: string;
  centro_custo_id: string;
  lancamento_padrao_id: string;
  percentual: string;
  valor: string;
}

/** Linha da tabela de parcelas (2x ou mais) — espelha o modal Blade / payload `parcelas`. */
export interface ParcelaLinha {
  vencimento: string;
  valor: string;
  percentual: string;
  /** Auto-preenchido com a Entidade Financeira do formulário principal. */
  contaPagamentoId: string;
  descricao: string;
  agendado: boolean;
}

export interface LancamentoFormState {
  fornecedor: string;
  dataCompetencia: string;
  descricao: string;
  valor: string;
  entidade: string;
  categoria: string;
  centroCusto: string;
  formaPagamento: string;
  numeroDocumento: string;
  parcelamento: string;
  vencimento: string;
  repetir: boolean;
  recebidoPago: boolean;
  agendado: boolean;
  historico: string;
  rateios: RateioItem[];
  // Recorrência
  configuracaoRecorrencia: string;
  diaCobranca: string;
  recorrenciaIntervalo: string;
  recorrenciaFrequencia: string;
  recorrenciaOcorrencias: string;
  /** Parcelamento 1x (sem Pago/Recebido): previsão e encargos — espelha o modal Blade */
  previsaoPagamento: string;
  juros: string;
  multa: string;
  desconto: string;
  /** 2x+: dias entre o 1º vencimento e os seguintes (UI alinhada ao print; distinto do Blade antigo por mês). */
  parcelasIntervaloDias: string;
  parcelasLinhas: ParcelaLinha[];
  domusDocumentoId?: number;
  /** Preenchidos quando o fornecedor será criado no save (valor de parceiro = `__novo__`) */
  novoParceiroNome: string;
  novoParceiroCnpj: string;
}

type FormAction =
  | { type: 'SET_FIELD'; field: keyof LancamentoFormState; value: LancamentoFormState[keyof LancamentoFormState] }
  | { type: 'LOAD'; payload: Partial<LancamentoFormState> }
  | { type: 'RESET' }
  | { type: 'ADD_RATEIO' }
  | { type: 'REMOVE_RATEIO'; index: number }
  | { type: 'SET_RATEIO_FIELD'; index: number; field: keyof RateioItem; value: string }
  | { type: 'DISTRIBUTE_RATEIO' }
  | { type: 'CLEAR_PARCELAS_IF_ANY' }
  | {
      type: 'REBUILD_PARCELAS';
      n: number;
      intervalo: number;
      vencimento: string;
      valorTotal: number;
      descricao: string;
      entidadeId: string;
    }
  | { type: 'SET_PARCELA_FIELD'; index: number; field: keyof ParcelaLinha; value: ParcelaLinha[keyof ParcelaLinha] }
  | { type: 'SET_PARCELA_VALOR'; index: number; masked: string; valorTotal: number };

/** @deprecated use todayIso() — mantido apenas para compatibilidade com código não-dateTimePicker */
export function todayStr(): string {
  const d = new Date();
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  return `${dd}/${mm}/${d.getFullYear()}`;
}

const INITIAL_STATE: LancamentoFormState = {
  fornecedor: '',
  dataCompetencia: todayIso(),
  descricao: '',
  valor: '',
  entidade: '',
  categoria: '',
  centroCusto: '',
  formaPagamento: '',
  numeroDocumento: '',
  parcelamento: 'avista',
  vencimento: todayIso(),
  repetir: false,
  recebidoPago: false,
  agendado: false,
  historico: '',
  rateios: [],
  configuracaoRecorrencia: '',
  diaCobranca: '1',
  recorrenciaIntervalo: '1',
  recorrenciaFrequencia: 'mensal',
  recorrenciaOcorrencias: '12',
  previsaoPagamento: todayIso(),
  juros: '',
  multa: '',
  desconto: '',
  parcelasIntervaloDias: '30',
  parcelasLinhas: [],
  domusDocumentoId: undefined,
  novoParceiroNome: '',
  novoParceiroCnpj: '',
};

let _rateioCounter = 0;
function nextRateioId(): string {
  return `r_${Date.now()}_${++_rateioCounter}`;
}

function formReducer(state: LancamentoFormState, action: FormAction): LancamentoFormState {
  switch (action.type) {
    case 'SET_FIELD':
      return { ...state, [action.field]: action.value };
    case 'LOAD':
      return { ...state, ...action.payload };
    case 'RESET':
      return { ...INITIAL_STATE };
    case 'ADD_RATEIO':
      return {
        ...state,
        rateios: [
          ...state.rateios,
          { id: nextRateioId(), filial_id: '', centro_custo_id: '', lancamento_padrao_id: '', percentual: '', valor: '' },
        ],
      };
    case 'REMOVE_RATEIO':
      return { ...state, rateios: state.rateios.filter((_, i) => i !== action.index) };
    case 'SET_RATEIO_FIELD': {
      const rateios = [...state.rateios];
      rateios[action.index] = { ...rateios[action.index], [action.field]: action.value };
      return { ...state, rateios };
    }
    case 'DISTRIBUTE_RATEIO': {
      const total = parseCurrency(state.valor);
      const count = state.rateios.length;
      if (count === 0 || total <= 0) return state;
      const perLine = Math.floor((total / count) * 100) / 100;
      const pct = Math.floor((100 / count) * 100) / 100;
      const updated = state.rateios.map((r, i) => {
        const isLast = i === count - 1;
        const lineVal = isLast ? +(total - perLine * (count - 1)).toFixed(2) : perLine;
        const linePct = isLast ? +(100 - pct * (count - 1)).toFixed(2) : pct;
        return {
          ...r,
          valor: lineVal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
          percentual: linePct.toFixed(2).replace('.', ','),
        };
      });
      return { ...state, rateios: updated };
    }
    case 'CLEAR_PARCELAS_IF_ANY':
      if (state.parcelasLinhas.length === 0) return state;
      return { ...state, parcelasLinhas: [] };
    case 'REBUILD_PARCELAS': {
      const { n, intervalo, vencimento, valorTotal, descricao, entidadeId } = action;
      const built = buildParcelasLinhas(n, vencimento, intervalo, valorTotal, descricao, entidadeId);
      return { ...state, parcelasLinhas: mergeMetaParcelas(state.parcelasLinhas, built) };
    }
    case 'SET_PARCELA_FIELD': {
      const parcelasLinhas = [...state.parcelasLinhas];
      const row = parcelasLinhas[action.index];
      if (!row) return state;
      parcelasLinhas[action.index] = { ...row, [action.field]: action.value };
      return { ...state, parcelasLinhas };
    }
    case 'SET_PARCELA_VALOR': {
      const parcelasLinhas = [...state.parcelasLinhas];
      const row = parcelasLinhas[action.index];
      if (!row) return state;
      const v = parseCurrency(action.masked);
      const pct = action.valorTotal > 0 ? (v / action.valorTotal) * 100 : 0;
      parcelasLinhas[action.index] = {
        ...row,
        valor: action.masked,
        percentual: pct.toFixed(2).replace('.', ','),
      };
      return { ...state, parcelasLinhas };
    }
  }
}

/** "1.234,56" -> 1234.56 */
export function parseCurrency(v: string): number {
  if (!v) return 0;
  return parseFloat(v.replace(/\./g, '').replace(',', '.')) || 0;
}

/** Ex.: `2x` → 2; `1x` / à vista → null (não exibe tabela de parcelas). */
export function parcelamentoQuantidadeParcelas(parcelamento: string): number | null {
  const p = (parcelamento ?? '').trim();
  const m = /^(\d+)x$/i.exec(p);
  if (!m) return null;
  const n = parseInt(m[1], 10);
  return n >= 2 ? n : null;
}

/**
 * Soma `days` a uma data em formato ISO (YYYY-MM-DD) e retorna ISO.
 * Aceita também dd/mm/yyyy (legado) e converte internamente.
 * O DatePicker sempre emite/recebe ISO — não usar formato BR nos estados.
 */
function addDaysIso(dateStr: string, days: number): string {
  const s = (dateStr ?? '').trim();
  let iso = s;

  // Converte BR → ISO se necessário (retrocompatibilidade)
  if (/^\d{2}\/\d{2}\/\d{4}$/.test(s)) {
    const [d, m, y] = s.split('/');
    iso = `${y}-${m}-${d}`;
  }

  if (!/^\d{4}-\d{2}-\d{2}$/.test(iso)) return s || todayIso();

  const dt = new Date(iso + 'T00:00:00');
  if (Number.isNaN(dt.getTime())) return iso || todayIso();

  dt.setDate(dt.getDate() + days);
  const y = dt.getFullYear();
  const m = String(dt.getMonth() + 1).padStart(2, '0');
  const d = String(dt.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

export function todayIso(): string {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

/** Normaliza qualquer formato de data para ISO YYYY-MM-DD. Retorna '' se inválido. */
function normalizeToIso(dateStr: string): string {
  const s = (dateStr ?? '').trim();
  if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
  if (/^\d{2}\/\d{2}\/\d{4}$/.test(s)) {
    const [d, m, y] = s.split('/');
    return `${y}-${m}-${d}`;
  }
  return '';
}

/**
 * Mescla parcelas reconstruídas com o estado anterior.
 *
 * Quando `prev` já tem dados (carregados do servidor ou editados pelo usuário),
 * todos os campos são preservados para não sobrescrever edições nem dados da API.
 * Apenas linhas novas (sem correspondente em `prev`) recebem os valores calculados.
 */
function mergeMetaParcelas(prev: ParcelaLinha[], next: ParcelaLinha[]): ParcelaLinha[] {
  return next.map((line, i) => {
    const o = prev[i];
    if (!o) return line;
    return {
      vencimento:       o.vencimento       || line.vencimento,
      valor:            o.valor            || line.valor,
      percentual:       o.percentual       || line.percentual,
      contaPagamentoId: o.contaPagamentoId || line.contaPagamentoId,
      descricao:        o.descricao !== '' ? o.descricao : line.descricao,
      agendado:         o.agendado,
    };
  });
}

/**
 * Gera as linhas de parcelas com datas em formato ISO (YYYY-MM-DD).
 * O 1º vencimento = primeiroVencimento; os seguintes somam `intervaloDias`.
 * `entidadeId` é copiado automaticamente para `contaPagamentoId` de cada linha.
 */
function buildParcelasLinhas(
  n: number,
  primeiroVencimento: string,
  intervaloDias: number,
  valorTotal: number,
  descricaoBase: string,
  entidadeId: string,
): ParcelaLinha[] {
  const intervalo = Math.max(1, intervaloDias);
  const per = n > 0 ? valorTotal / n : 0;
  const pct = n > 0 ? 100 / n : 0;
  const lines: ParcelaLinha[] = [];
  let venc = normalizeToIso(primeiroVencimento) || todayIso();
  const base = descricaoBase.trim();
  for (let i = 1; i <= n; i++) {
    const valorLinha = i === n ? +(valorTotal - per * (n - 1)).toFixed(2) : +per.toFixed(2);
    const pctLinha = i === n ? +(100 - pct * (n - 1)).toFixed(2) : +pct.toFixed(2);
    lines.push({
      vencimento: venc,
      valor: valorLinha.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
      percentual: pctLinha.toFixed(2).replace('.', ','),
      contaPagamentoId: entidadeId,
      descricao: base ? `${base} ${i}/${n}` : `${i}/${n}`,
      agendado: false,
    });
    venc = addDaysIso(venc, intervalo);
  }
  return lines;
}

// Mapeamento backend key -> form field name
const SUG_FIELD_MAP: Record<string, keyof LancamentoFormState> = {
  lancamento_padrao_id: 'categoria',
  cost_center_id: 'centroCusto',
  tipo_documento: 'formaPagamento',
  descricao: 'descricao',
};

// ── Hook ─────────────────────────────────────────────────────────────────────

export interface LancamentoSaveResult {
  id?: string | number;
  domus_documento_id?: number | null;
}

interface UseLancamentoFormOptions {
  open: boolean;
  tipo: TipoLancamento | null;
  editId?: string | null;
  prefill?: LancamentoPrefill | null;
  /** Lista de parceiros (com cnpj/cpf) para match automático após Domus IA */
  parceirosForMatch?: ParceiroOption[];
  csrfToken: string;
  onSaved?: (result?: LancamentoSaveResult) => void;
  onClose: () => void;
  /** Anexos pendentes (arquivos) lidos no momento do save — evita closure obsoleta nos diálogos. */
  stagedAnexosRef?: MutableRefObject<LancamentoStagedAnexo[]>;
  onClearStagedAnexos?: () => void;
}

/** Monta campos do lançamento no formato multipart esperado pelo Laravel (StoreTransacaoFinanceiraRequest). */
function appendLancamentoPayloadToFormData(fd: FormData, payload: Record<string, unknown>): void {
  const nestedKeys = new Set(['rateios', 'parcelas']);

  for (const [key, val] of Object.entries(payload)) {
    if (nestedKeys.has(key)) continue;
    if (val === undefined) continue;
    if (val === null) {
      fd.append(key, '');
      continue;
    }
    if (typeof val === 'boolean') {
      fd.append(key, val ? '1' : '0');
      continue;
    }
    if (typeof val === 'number' && Number.isFinite(val)) {
      fd.append(key, String(val));
      continue;
    }
    if (typeof val === 'object') continue;
    fd.append(key, String(val));
  }

  const rateios = payload.rateios;
  if (Array.isArray(rateios)) {
    rateios.forEach((r, i) => {
      if (!r || typeof r !== 'object') return;
      for (const [rk, rv] of Object.entries(r as Record<string, unknown>)) {
        if (rv === undefined || rv === null) continue;
        fd.append(`rateios[${i}][${rk}]`, String(rv));
      }
    });
  }

  const parcelas = payload.parcelas;
  if (parcelas && typeof parcelas === 'object' && !Array.isArray(parcelas)) {
    for (const [pk, row] of Object.entries(parcelas as Record<string, Record<string, unknown>>)) {
      if (!row || typeof row !== 'object') continue;
      for (const [rk, rv] of Object.entries(row)) {
        if (rv === undefined) continue;
        if (rv === null) {
          fd.append(`parcelas[${pk}][${rk}]`, '');
          continue;
        }
        if (typeof rv === 'boolean') {
          fd.append(`parcelas[${pk}][${rk}]`, rv ? '1' : '0');
          continue;
        }
        fd.append(`parcelas[${pk}][${rk}]`, String(rv));
      }
    }
  }
}

export function buildLancamentoFormDataWithAnexos(
  payload: Record<string, unknown>,
  stagedAnexos: LancamentoStagedAnexo[],
): FormData {
  const fd = new FormData();
  appendLancamentoPayloadToFormData(fd, payload);
  stagedAnexos.forEach((row, i) => {
    fd.append(`anexos[${i}][forma_anexo]`, 'arquivo');
    fd.append(`anexos[${i}][tipo_anexo]`, row.tipoAnexo ?? '');
    fd.append(`anexos[${i}][descricao]`, row.descricao ?? '');
    fd.append(`anexos[${i}][arquivo]`, row.file, row.file.name);
  });
  return fd;
}

function onlyDigits(s: string | null | undefined): string {
  return (s ?? '').replace(/\D/g, '');
}

export function useLancamentoForm({
  open,
  tipo,
  editId,
  prefill,
  parceirosForMatch = [],
  csrfToken,
  onSaved,
  onClose,
  stagedAnexosRef,
  onClearStagedAnexos,
}: UseLancamentoFormOptions) {
  const isEdit = !!editId;
  const [form, dispatch] = useReducer(formReducer, INITIAL_STATE);
  const [fieldErrors, setFieldErrorsRaw] = useReducer(
    (_: Record<string, string>, v: Record<string, string>) => v,
    {},
  );
  const savingRef = useRef(false);
  const [, forceRender] = useReducer((x: number) => x + 1, 0);
  const loadingEditRef = useRef(false);
  const formRef = useRef(form);
  formRef.current = form;

  const [existingAnexos, setExistingAnexos] = useState<LancamentoExistingAnexo[]>([]);

  const deleteExistingAnexo = useCallback(
    async (anexoId: number): Promise<boolean> => {
      if (!isEdit) return false;
      try {
        const res = await fetch(`/app/financeiro/banco/lancamento/anexo/${anexoId}`, {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken ?? '',
          },
          credentials: 'same-origin',
        });
        const json = (await res.json().catch(() => ({}))) as { success?: boolean; message?: string };
        if (!res.ok || !json.success) {
          notify.error('Exclusão', json.message ?? 'Não foi possível excluir o anexo.');
          return false;
        }
        setExistingAnexos((prev) => prev.filter((a) => a.id !== anexoId));
        notify.success('Anexo excluído', json.message ?? 'O anexo foi removido.');
        return true;
      } catch {
        notify.networkError(() => deleteExistingAnexo(anexoId));
        return false;
      }
    },
    [isEdit, csrfToken],
  );

  const setField = useCallback(
    <K extends keyof LancamentoFormState>(field: K, value: LancamentoFormState[K]) => {
      dispatch({ type: 'SET_FIELD', field, value: value as LancamentoFormState[keyof LancamentoFormState] });
    },
    [],
  );

  const setParcelaField = useCallback(
    (index: number, field: keyof ParcelaLinha, value: ParcelaLinha[keyof ParcelaLinha]) => {
      dispatch({ type: 'SET_PARCELA_FIELD', index, field, value });
    },
    [],
  );

  const setParcelaValor = useCallback((index: number, masked: string, valorTotal: number) => {
    dispatch({ type: 'SET_PARCELA_VALOR', index, masked, valorTotal });
  }, []);

  const [debouncedDescricao, setDebouncedDescricao] = useState('');

  useEffect(() => {
    const id = window.setTimeout(() => setDebouncedDescricao(form.descricao), 400);
    return () => window.clearTimeout(id);
  }, [form.descricao]);

  /** useLayoutEffect: linhas existem no mesmo ciclo ao trocar para 2x+ (evita card “vazio”). */
  useLayoutEffect(() => {
    const n = parcelamentoQuantidadeParcelas(form.parcelamento);
    if (form.repetir || !n) {
      dispatch({ type: 'CLEAR_PARCELAS_IF_ANY' });
      return;
    }
    const intervalo = parseInt(form.parcelasIntervaloDias, 10) || 30;
    dispatch({
      type: 'REBUILD_PARCELAS',
      n,
      intervalo,
      vencimento: form.vencimento,
      valorTotal: parseCurrency(form.valor),
      descricao: debouncedDescricao,
      entidadeId: form.entidade,
    });
  }, [
    form.repetir,
    form.parcelamento,
    form.parcelasIntervaloDias,
    form.vencimento,
    form.valor,
    form.entidade,
    debouncedDescricao,
  ]);

  const resetFormBase = useCallback(() => {
    dispatch({ type: 'RESET' });
    setFieldErrorsRaw({});
  }, []);

  // ── Sugestões da IA ────────────────────────────────────────────────────
  const handleSugestaoApply = useCallback(
    (sug: SugestaoState, manualEditsSet: Set<string>) => {
      const current = formRef.current;
      for (const [sugKey, formField] of Object.entries(SUG_FIELD_MAP)) {
        const sugValue = sug[sugKey as keyof SugestaoState];
        if (sugValue && typeof sugValue === 'string' && !manualEditsSet.has(sugKey)) {
          const currentVal = current[formField];
          if (!currentVal || currentVal === '') {
            dispatch({ type: 'SET_FIELD', field: formField, value: sugValue });
          }
        }
      }
    },
    [],
  );

  const {
    sugState,
    confiancaCampos,
    markManualEdit,
    reset: sugReset,
  } = useSugestao({
    enabled: open && !isEdit,
    descricao: form.descricao,
    parceiroId: form.fornecedor,
    valor: parseCurrency(form.valor),
    onApply: handleSugestaoApply,
  });

  const resetForm = useCallback(() => {
    resetFormBase();
    sugReset();
  }, [resetFormBase, sugReset]);

  // Aplica prefill (dados da IA) ao abrir em modo novo — `parceiroDocumento` / `parceiroNomeIa` são tratados no efeito seguinte
  useEffect(() => {
    if (!open || editId || !prefill) return;
    const { parceiroDocumento: _doc, parceiroNomeIa: _nome, ...rest } = prefill;
    dispatch({ type: 'RESET' });
    dispatch({ type: 'LOAD', payload: rest });
  }, [open, editId, prefill]);

  // Match automático de fornecedor/cliente por CNPJ/CPF (igual `matchFornecedorByCNPJ` no drawer Domus Blade)
  useEffect(() => {
    if (!open || editId || !prefill?.parceiroDocumento || parceirosForMatch.length === 0) return;
    const doc = onlyDigits(prefill.parceiroDocumento);
    if (!doc) return;
    const nome = (prefill.parceiroNomeIa ?? '').trim();
    const wantNatureza = tipo === 'receita' ? 'cliente' : 'fornecedor';

    const match = parceirosForMatch.find((p) => {
      const nat = p.natureza;
      if (nat && nat !== wantNatureza && nat !== 'ambos') return false;
      const pCnpj = onlyDigits(p.cnpj);
      const pCpf = onlyDigits(p.cpf);
      return (pCnpj && pCnpj === doc) || (pCpf && pCpf === doc);
    });

    if (match) {
      setField('fornecedor', match.id);
      setField('novoParceiroNome', '');
      setField('novoParceiroCnpj', '');
      return;
    }

    if (nome) {
      setField('fornecedor', '__novo__');
      setField('novoParceiroNome', nome);
      setField('novoParceiroCnpj', doc);
    }
  }, [open, editId, tipo, prefill?.parceiroDocumento, prefill?.parceiroNomeIa, parceirosForMatch, setField]);

  useEffect(() => {
    if (!open) setExistingAnexos([]);
  }, [open]);

  // Carrega dados ao abrir em modo edição
  useEffect(() => {
    if (!open || !editId) return;
    setExistingAnexos([]);
    loadingEditRef.current = true;
    forceRender();

    fetch(`/app/financeiro/banco/lancamento/${editId}`, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    })
      .then((r) => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then((d) => {
        // Mapeia parcelas vindas da API → ParcelaLinha[]
        const parcelasLinhas: ParcelaLinha[] = Array.isArray(d.parcelas) && d.parcelas.length > 0
          ? d.parcelas.map((p: {
              vencimento: string | null;
              valor: number;
              percentual: number;
              conta_pagamento_id: string | null;
              descricao: string;
              agendado: boolean;
            }) => ({
              vencimento: p.vencimento ?? '',
              valor: Number(p.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
              percentual: Number(p.percentual).toFixed(2).replace('.', ','),
              contaPagamentoId: p.conta_pagamento_id ?? '',
              descricao: p.descricao ?? '',
              agendado: !!p.agendado,
            }))
          : [];

        dispatch({
          type: 'LOAD',
          payload: {
            descricao: d.descricao ?? '',
            dataCompetencia: d.data_competencia ?? '',
            vencimento: d.data_vencimento ?? '',
            entidade: d.entidade_id ?? '',
            fornecedor: d.parceiro_id ?? '',
            categoria: d.lancamento_padrao_id ?? '',
            centroCusto: d.cost_center_id ?? '',
            formaPagamento: d.tipo_documento ?? '',
            numeroDocumento: d.numero_documento ?? '',
            historico: d.historico_complementar ?? '',
            recebidoPago: d.recebido_pago ?? false,
            agendado: !!d.agendado,
            valor: d.valor
              ? Number(d.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
              : '',
            juros:
              d.juros != null && !Number.isNaN(Number(d.juros)) && Number(d.juros) !== 0
                ? Number(d.juros).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : '',
            multa:
              d.multa != null && !Number.isNaN(Number(d.multa)) && Number(d.multa) !== 0
                ? Number(d.multa).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : '',
            desconto:
              d.desconto != null && !Number.isNaN(Number(d.desconto)) && Number(d.desconto) !== 0
                ? Number(d.desconto).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                : '',
            previsaoPagamento: d.data_vencimento ?? d.previsao_pagamento ?? '',
            // Parcelamento
            parcelamento: d.parcelamento ?? 'avista',
            ...(parcelasLinhas.length > 0 ? { parcelasLinhas } : {}),
            // Recorrência
            ...(d.recorrencia ? (() => {
              const vencDay = d.data_vencimento ? String(new Date(d.data_vencimento + 'T12:00:00').getDate()) : '1';
              return {
                repetir: true,
                configuracaoRecorrencia: d.recorrencia.id ?? '',
                diaCobranca: vencDay,
                recorrenciaIntervalo: String(d.recorrencia.intervalo_repeticao ?? 1),
                recorrenciaFrequencia: d.recorrencia.frequencia ?? 'mensal',
                recorrenciaOcorrencias: String(d.recorrencia.total_ocorrencias ?? 12),
              };
            })() : {}),
          },
        });
        setExistingAnexos(parseLancamentoExistingAnexosApi(d.anexos));
      })
      .catch(() => notify.error('Não foi possível carregar o lançamento', 'Verifique sua conexão e tente novamente.'))
      .finally(() => {
        loadingEditRef.current = false;
        forceRender();
      });
  }, [open, editId]);

  const addRateio = useCallback(() => dispatch({ type: 'ADD_RATEIO' }), []);
  const removeRateio = useCallback((index: number) => dispatch({ type: 'REMOVE_RATEIO', index }), []);
  const setRateioField = useCallback(
    (index: number, field: keyof RateioItem, value: string) =>
      dispatch({ type: 'SET_RATEIO_FIELD', index, field, value }),
    [],
  );
  const distributeRateio = useCallback(() => dispatch({ type: 'DISTRIBUTE_RATEIO' }), []);

  function validate(): Record<string, string> {
    const errors: Record<string, string> = {};
    if (!form.descricao.trim()) errors.descricao = 'Descrição é obrigatória.';
    if (form.fornecedor === '__novo__') {
      if (!form.novoParceiroNome.trim()) {
        errors.parceiro_id = 'Informe o nome do parceiro ou escolha um cadastrado.';
      }
      if (!onlyDigits(form.novoParceiroCnpj)) {
        errors.parceiro_id = 'CNPJ/CPF do novo parceiro é obrigatório para cadastro automático.';
      }
    }
    if (!form.entidade) errors.entidade_id = 'Entidade Financeira é obrigatória.';
    if (!form.dataCompetencia) errors.data_competencia = 'Data de Competência é obrigatória.';
    if (!form.categoria) errors.lancamento_padrao_id = 'Categoria é obrigatória.';
    if (!form.formaPagamento) errors.tipo_documento = 'Forma de Pagamento é obrigatória.';
    const valorNum = parseCurrency(form.valor);
    if (valorNum <= 0) errors.valor = 'Informe um valor maior que zero.';

    if (form.rateios.length > 0) {
      const somaRateios = form.rateios.reduce((acc, r) => acc + parseCurrency(r.valor), 0);
      const diff = Math.abs(somaRateios - valorNum);
      if (diff > 0.01) {
        errors.rateios = `A soma dos rateios deve ser igual ao valor total. Diferença: R$ ${diff.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
      }
      for (let i = 0; i < form.rateios.length; i++) {
        if (!form.rateios[i].filial_id) errors[`rateios.${i}.filial_id`] = 'Filial obrigatória.';
        if (parseCurrency(form.rateios[i].valor) <= 0) errors[`rateios.${i}.valor`] = 'Valor obrigatório.';
      }
    }

    if (form.repetir) {
      if (!form.diaCobranca) errors.dia_cobranca = 'Dia de Cobrança é obrigatório.';
      if (!form.vencimento) errors.vencimento = '1º Vencimento é obrigatório.';
      const cfg = form.configuracaoRecorrencia?.trim() ?? '';
      if (!cfg || !/^\d+$/.test(cfg)) {
        errors.configuracao_recorrencia =
          'Selecione uma configuração de recorrência ou crie uma nova com o botão Nova.';
      }
    }

    const showPrevisao =
      !form.repetir && form.parcelamento === '1x' && !form.recebidoPago;
    if (showPrevisao) {
      if (!form.previsaoPagamento?.trim()) {
        errors.previsao_pagamento = 'Previsão de pagamento é obrigatória.';
      }
    }

    const qtdParcelas = parcelamentoQuantidadeParcelas(form.parcelamento);
    if (!form.repetir && qtdParcelas) {
      const intervalo = parseInt(form.parcelasIntervaloDias, 10);
      if (!intervalo || intervalo < 1) {
        errors.intervalo_parcelas_dias = 'Informe o intervalo entre parcelas (mínimo 1 dia).';
      }
      if (form.parcelasLinhas.length !== qtdParcelas) {
        errors.parcelas = 'As linhas de parcelas não correspondem ao parcelamento selecionado.';
      }
      let somaParcelas = 0;
      form.parcelasLinhas.forEach((linha, i) => {
        if (!linha.vencimento?.trim()) {
          errors[`parcelas.${i}.vencimento`] = 'Vencimento obrigatório.';
        }
        const v = parseCurrency(linha.valor);
        if (v <= 0) {
          errors[`parcelas.${i}.valor`] = 'Valor deve ser maior que zero.';
        }
        somaParcelas += v;
      });
      if (form.parcelasLinhas.length > 0 && Math.abs(somaParcelas - valorNum) > 0.02) {
        errors.parcelas =
          errors.parcelas ??
          'A soma dos valores das parcelas deve ser igual ao valor total do lançamento.';
      }
    }

    return errors;
  }

  function toPayload() {
    const base: Record<string, unknown> = {
      tipo: tipo === 'receita' ? 'receita' : 'despesa',
      descricao: form.descricao.trim(),
      valor: parseCurrency(form.valor),
      data_competencia: form.dataCompetencia,
      data_vencimento: form.vencimento || undefined,
      entidade_id: form.entidade,
      lancamento_padrao_id: form.categoria || undefined,
      cost_center_id: form.centroCusto || undefined,
      tipo_documento: form.formaPagamento || undefined,
      numero_documento: form.numeroDocumento || undefined,
      historico_complementar: form.historico || undefined,
      recebido_pago: form.recebidoPago,
      agendado: form.agendado,
      domus_documento_id: form.domusDocumentoId || undefined,
    };

    if (
      form.fornecedor === '__novo__' &&
      form.novoParceiroCnpj &&
      form.novoParceiroNome.trim()
    ) {
      base.fornecedor_id = '__novo__';
      base.novo_parceiro_nome = form.novoParceiroNome.trim();
      base.novo_parceiro_cnpj = form.novoParceiroCnpj;
    } else if (form.fornecedor && form.fornecedor !== '__novo__') {
      base.parceiro_id = form.fornecedor;
    }

    if (form.rateios.length > 0) {
      base.rateios = form.rateios.map((r) => ({
        filial_id: r.filial_id,
        centro_custo_id: r.centro_custo_id || null,
        lancamento_padrao_id: r.lancamento_padrao_id || null,
        valor: parseCurrency(r.valor),
        percentual: parseFloat(r.percentual.replace(',', '.')) || 0,
      }));
    }

    if (form.repetir) {
      base.repetir_lancamento = 1;
      base.dia_cobranca = form.diaCobranca;
      base.vencimento = form.vencimento;
      base.configuracao_recorrencia = form.configuracaoRecorrencia.trim();
      base.intervalo_repeticao = parseInt(form.recorrenciaIntervalo, 10) || 1;
      base.frequencia = form.recorrenciaFrequencia || 'mensal';
      base.apos_ocorrencias = parseInt(form.recorrenciaOcorrencias, 10) || 12;
    }

    const showPrevisao =
      !form.repetir && form.parcelamento === '1x' && !form.recebidoPago;
    if (showPrevisao) {
      const j = parseCurrency(form.juros);
      const m = parseCurrency(form.multa);
      const desc = parseCurrency(form.desconto);
      const valorNum = parseCurrency(form.valor);
      base.previsao_pagamento = form.previsaoPagamento.trim();
      base.juros = j;
      base.multa = m;
      base.desconto = desc;
      base.valor_a_pagar = Math.max(0, valorNum + j + m - desc);
    }

    const qtdParcelas = parcelamentoQuantidadeParcelas(form.parcelamento);
    if (!form.repetir && qtdParcelas && form.parcelasLinhas.length === qtdParcelas) {
      base.parcelamento = form.parcelamento;
      base.intervalo_parcelas_dias = parseInt(form.parcelasIntervaloDias, 10) || 30;
      const parcelasPayload: Record<string, Record<string, unknown>> = {};
      form.parcelasLinhas.forEach((linha, idx) => {
        const num = String(idx + 1);
        const pctRaw = String(linha.percentual ?? '');
        const pct = parseFloat(pctRaw.replace(/\./g, '').replace(',', '.')) || 0;
        parcelasPayload[num] = {
          vencimento: linha.vencimento.trim(),
          valor: parseCurrency(linha.valor),
          percentual: pct,
          conta_pagamento_id: linha.contaPagamentoId || null,
          descricao: linha.descricao.trim() || undefined,
          agendado: linha.agendado,
        };
      });
      base.parcelas = parcelasPayload;
    }

    return base;
  }

  async function handleSave(mode: 'close' | 'clear' | 'clone' = 'close') {
    setFieldErrorsRaw({});

    const errors = validate();
    if (Object.keys(errors).length > 0) {
      setFieldErrorsRaw(errors);
      requestAnimationFrame(() => {
        const firstKey = Object.keys(errors)[0];
        const el = document.querySelector(`[data-field="${firstKey}"]`);
        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      });
      return;
    }

    savingRef.current = true;
    forceRender();

    try {
      const url = isEdit ? `/app/financeiro/banco/lancamento/${editId}` : '/app/financeiro/banco/lancamento';
      const method = isEdit ? 'PUT' : 'POST';

      const payload = toPayload();
      const staged = stagedAnexosRef?.current?.filter((r) => r.file) ?? [];
      const useMultipart = staged.length > 0;

      const res = await fetch(url, {
        method,
        headers: {
          ...(useMultipart ? {} : { 'Content-Type': 'application/json' }),
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken ?? '',
        },
        credentials: 'same-origin',
        body: useMultipart ? buildLancamentoFormDataWithAnexos(payload, staged) : JSON.stringify(payload),
      });

      const json = await res.json();

      if (!res.ok) {
        if (res.status === 422 && json.errors) {
          const mapped: Record<string, string> = {};
          for (const [key, msgs] of Object.entries(json.errors as Record<string, string[]>)) {
            mapped[key] = msgs[0];
          }
          setFieldErrorsRaw(mapped);
          notify.error('Dados inválidos', 'Verifique os campos destacados em vermelho.');
        } else {
          notify.error('Erro ao salvar', json.message ?? 'Não foi possível salvar o lançamento.');
        }
        return;
      }

      notify.success(
        isEdit ? 'Lançamento atualizado!' : 'Lançamento criado!',
        json.message ?? (isEdit ? 'As alterações foram salvas com sucesso.' : 'O lançamento foi registrado com sucesso.'),
      );
      if (!isEdit) {
        onSaved?.({
          id: json.id,
          domus_documento_id: json.domus_documento_id ?? null,
        });
      } else {
        onSaved?.({ id: json.id ?? editId });
      }

      onClearStagedAnexos?.();

      if (mode === 'clone') {
        // Mantém dados no form, só limpa o valor para o próximo lançamento
      } else {
        resetForm();
      }
      if (mode === 'close') onClose();
    } catch {
      notify.networkError(() => handleSave(mode));
    } finally {
      savingRef.current = false;
      forceRender();
    }
  }

  return {
    form,
    setField,
    fieldErrors,
    saving: savingRef.current,
    loadingEdit: loadingEditRef.current,
    isEdit,
    handleSave,
    resetForm,
    showRecebidoPago: form.parcelamento === 'avista' || form.parcelamento === '1x',
    sugState,
    confiancaCampos,
    markManualEdit,
    addRateio,
    removeRateio,
    setRateioField,
    distributeRateio,
    setParcelaField,
    setParcelaValor,
    existingAnexos,
    deleteExistingAnexo,
  };
}
