import { useCallback, useEffect, useRef, useState } from 'react';
import type { OrigemSugestao } from '@/components/ui/suggestion-star';

// ── Tipos exportados ─────────────────────────────────────────────────────────

export interface ConfiancaCampos {
  lancamento_padrao_id: number;
  cost_center_id: number;
  tipo_documento: number;
  descricao: number;
  parceiro_id: number;
}

export interface SugestaoState {
  lancamento_padrao_id: string | null;
  cost_center_id: string | null;
  tipo_documento: string | null;
  descricao: string | null;
  parceiro_id: string | null;
  confianca: number;
  origem: OrigemSugestao | null;
  confianca_campos: ConfiancaCampos;
}

export const ZERO_CONFIANCA: ConfiancaCampos = {
  lancamento_padrao_id: 0,
  cost_center_id: 0,
  tipo_documento: 0,
  descricao: 0,
  parceiro_id: 0,
};

const EMPTY_SUG: SugestaoState = {
  lancamento_padrao_id: null,
  cost_center_id: null,
  tipo_documento: null,
  descricao: null,
  parceiro_id: null,
  confianca: 0,
  origem: null,
  confianca_campos: { ...ZERO_CONFIANCA },
};

// ── Parsers puros ────────────────────────────────────────────────────────────

function numFromSugestao(v: unknown): number | null {
  if (typeof v === 'number' && !Number.isNaN(v)) return v;
  if (typeof v === 'string' && v !== '' && !Number.isNaN(Number(v))) return Number(v);
  return null;
}

function strFromSugestao(v: unknown): string | null {
  return typeof v === 'string' && v.length > 0 ? v : null;
}

export function parseConfiancaCampos(raw: unknown): ConfiancaCampos {
  if (raw && typeof raw === 'object' && !Array.isArray(raw)) {
    const o = raw as Record<string, unknown>;
    return {
      lancamento_padrao_id: typeof o.lancamento_padrao_id === 'number' ? o.lancamento_padrao_id : 0,
      cost_center_id: typeof o.cost_center_id === 'number' ? o.cost_center_id : 0,
      tipo_documento: typeof o.tipo_documento === 'number' ? o.tipo_documento : 0,
      descricao: typeof o.descricao === 'number' ? o.descricao : 0,
      parceiro_id: typeof o.parceiro_id === 'number' ? o.parceiro_id : 0,
    };
  }
  return { ...ZERO_CONFIANCA };
}

export function buildInitialSug(sug: Record<string, unknown> | null): SugestaoState {
  if (!sug) return { ...EMPTY_SUG };
  const confianca = typeof sug.confianca === 'number' ? sug.confianca : 0;
  const origem = typeof sug.origem_sugestao === 'string' ? sug.origem_sugestao : null;
  const lpId = numFromSugestao(sug.lancamento_padrao_id);
  const ccId = numFromSugestao(sug.cost_center_id);
  const cc = sug.confianca_campos;
  const confiancaCampos = cc
    ? parseConfiancaCampos(cc)
    : confianca > 0
      ? {
          lancamento_padrao_id: confianca,
          cost_center_id: confianca,
          tipo_documento: confianca,
          descricao: confianca,
          parceiro_id: confianca,
        }
      : { ...ZERO_CONFIANCA };

  return {
    lancamento_padrao_id: lpId != null ? String(lpId) : null,
    cost_center_id: ccId != null ? String(ccId) : null,
    tipo_documento: strFromSugestao(sug.tipo_documento),
    descricao: strFromSugestao(sug.descricao_sugerida) ?? strFromSugestao(sug.descricao),
    parceiro_id: numFromSugestao(sug.parceiro_id) != null ? String(numFromSugestao(sug.parceiro_id)) : null,
    confianca,
    origem,
    confianca_campos: confiancaCampos,
  };
}

// ── Hook ─────────────────────────────────────────────────────────────────────

export type SugestaoFieldKey = 'lancamento_padrao_id' | 'cost_center_id' | 'tipo_documento' | 'descricao' | 'parceiro_id';

interface UseSugestaoOptions {
  enabled: boolean;
  descricao: string;
  parceiroId: string;
  valor: number;
  initialSugestao?: Record<string, unknown> | null;
  onApply?: (sug: SugestaoState, manualEdits: Set<string>) => void;
}

export function useSugestao({
  enabled,
  descricao,
  parceiroId,
  valor,
  initialSugestao,
  onApply,
}: UseSugestaoOptions) {
  const [sugState, setSugState] = useState<SugestaoState>(
    () => buildInitialSug(initialSugestao ?? null),
  );
  const manualEdits = useRef<Set<string>>(new Set());
  const debounceRef = useRef<ReturnType<typeof setTimeout>>(undefined);
  const fetchAbortRef = useRef<AbortController>(undefined);
  const onApplyRef = useRef(onApply);
  onApplyRef.current = onApply;

  const markManualEdit = useCallback((key: string) => {
    manualEdits.current.add(key);
  }, []);

  const reset = useCallback(() => {
    setSugState({ ...EMPTY_SUG });
    manualEdits.current.clear();
    clearTimeout(debounceRef.current);
    fetchAbortRef.current?.abort();
  }, []);

  // Fetch debounced
  useEffect(() => {
    if (!enabled) return;
    if (!descricao && !parceiroId) return;

    clearTimeout(debounceRef.current);
    debounceRef.current = setTimeout(async () => {
      fetchAbortRef.current?.abort();
      const ctrl = new AbortController();
      fetchAbortRef.current = ctrl;

      const params = new URLSearchParams();
      if (parceiroId) params.set('parceiro_id', parceiroId);
      if (descricao) params.set('descricao', descricao);
      if (valor) params.set('valor', String(valor));

      try {
        const res = await fetch(`/banco/sugestao?${params}`, {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
          signal: ctrl.signal,
        });
        if (!res.ok) return;
        const data = await res.json();
        if (typeof data?.confianca !== 'number' || data.confianca < 50) return;

        const newSug = buildInitialSug(data);
        setSugState(newSug);
        onApplyRef.current?.(newSug, manualEdits.current);
      } catch {
        /* abort ou erro de rede */
      }
    }, 1000);

    return () => clearTimeout(debounceRef.current);
  }, [enabled, descricao, parceiroId, valor]);

  // Cleanup ao desmontar
  useEffect(() => {
    return () => {
      clearTimeout(debounceRef.current);
      fetchAbortRef.current?.abort();
    };
  }, []);

  return {
    sugState,
    setSugState,
    confiancaCampos: sugState.confianca_campos,
    markManualEdit,
    reset,
  };
}
