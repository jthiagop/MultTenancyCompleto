import { useCallback, useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react';
import { notify } from '@/lib/notify';
import { useFormSelectData, type ParceiroOption } from '@/hooks/useFormSelectData';
import { useAppData } from '@/hooks/useAppData';
import { ParceiroQuickCreateSheet } from './parceiro-quick-create-sheet';
import {
  useLancamentoForm,
  parseCurrency,
  todayIso,
  parcelamentoQuantidadeParcelas,
  type LancamentoFormState,
  type ParcelaLinha,
  type RateioItem,
} from './useLancamentoForm';
import {
  LancamentoAnexosInput,
  type LancamentoExistingAnexo,
  type LancamentoStagedAnexo,
} from './lancamento-anexos-input';
import type { LancamentoSaveResult } from './useLancamentoForm';
import type { FormSelectData } from '@/hooks/useFormSelectData';
import type { SugestaoState, ConfiancaCampos } from '@/hooks/useSugestao';
import { SuggestionStar } from '@/components/ui/suggestion-star';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import {
  Dialog,
  DialogBody,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  Sheet,
  SheetBody,
  SheetClose,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/common/masked-input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { SearchSelect, type SearchSelectOption } from '@/components/common/search-select';
import { SearchSelectButton } from '@/components/common/search-select-button';
import { Card, CardContent, CardHeader, CardTitle, CardToolbar } from '@/components/ui/card';
import { ButtonGroup } from '@/components/ui/button-group';
import { Switch } from '@/components/ui/switch';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import {
  TransformWrapper,
  TransformComponent,
  useControls,
} from 'react-zoom-pan-pinch';
import {
  ArrowDownCircle,
  ArrowUpCircle,
  CalendarCheck,
  ChevronUp,
  ClipboardList,
  Copy,
  Equal,
  Eraser,
  FileSearch,
  History,
  Info,
  Loader2,
  Maximize2,
  Paperclip,
  PieChart,
  Plus,
  Repeat,
  RotateCcw,
  RotateCw,
  Save,
  Table2,
  Wallet,
  X,
  ZoomIn,
  ZoomOut,
} from 'lucide-react';

export type TipoLancamento = 'receita' | 'despesa';

export interface LancamentoPrefill {
  descricao?: string;
  valor?: string;
  dataCompetencia?: string;
  vencimento?: string;
  formaPagamento?: string;
  numeroDocumento?: string;
  historico?: string;
  juros?: string;
  multa?: string;
  desconto?: string;
  domusDocumentoId?: number;
  /**
   * CNPJ ou CPF do estabelecimento (só dígitos) — match com parceiros ou cadastro automático ao salvar
   * (espelha `matchFornecedorByCNPJ` do drawer Domus IA Blade).
   */
  parceiroDocumento?: string;
  /** Nome do estabelecimento extraído pela IA — usado se não existir parceiro com o mesmo documento */
  parceiroNomeIa?: string;
}

export interface LancamentoDocumentPreview {
  url: string;
  mimeType: string;
  filename?: string;
}

interface LancamentoDrawerProps {
  open: boolean;
  tipo: TipoLancamento | null;
  onClose: () => void;
  onSaved?: (result?: LancamentoSaveResult) => void;
  editId?: string | null;
  prefill?: LancamentoPrefill | null;
  documentPreview?: LancamentoDocumentPreview | null;
}

const TITULO: Record<TipoLancamento, string> = {
  receita: 'Nova Receita',
  despesa: 'Nova Despesa',
};

const TITULO_EDIT: Record<TipoLancamento, string> = {
  receita: 'Editar Receita',
  despesa: 'Editar Despesa',
};

const ICON: Record<TipoLancamento, React.ReactNode> = {
  receita: <ArrowUpCircle className="size-5 text-green-600" />,
  despesa: <ArrowDownCircle className="size-5 text-red-500" />,
};

const PARCELAMENTO_OPTIONS = [
  { value: 'avista', label: 'À Vista' },
  ...Array.from({ length: 12 }, (_, i) => ({ value: `${i + 1}x`, label: `${i + 1}x` })),
];

const DIA_COBRANCA_OPTIONS = [
  ...Array.from({ length: 30 }, (_, i) => ({ value: String(i + 1), label: `${i + 1}º dia do mês` })),
  { value: 'ultimo', label: 'Último dia do mês' },
];

const FREQUENCIA_OPTIONS = [
  { value: 'diario', label: 'Dia(s)' },
  { value: 'semanal', label: 'Semana(s)' },
  { value: 'mensal', label: 'Mês(es)' },
  { value: 'anual', label: 'Ano(s)' },
];

interface RecorrenciaConfig {
  id: string;
  nome: string;
  intervalo_repeticao: number;
  frequencia: string;
  total_ocorrencias: number;
}

function useRecorrencias(enabled: boolean) {
  const [configs, setConfigs] = useState<RecorrenciaConfig[]>([]);
  const [loading, setLoading] = useState(false);
  const fetched = useRef(false);

  useEffect(() => {
    if (!enabled || fetched.current) return;
    fetched.current = true;
    setLoading(true);
    fetch('/recorrencias', {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    })
      .then((r) => r.json())
      .then((json) => {
        if (json.success && Array.isArray(json.data)) {
          setConfigs(
            json.data.map((item: Record<string, unknown>) => ({
              id: String(item.id),
              nome: String(item.nome ?? ''),
              intervalo_repeticao: Number(item.intervalo_repeticao),
              frequencia: String(item.frequencia),
              total_ocorrencias: Number(item.total_ocorrencias),
            })),
          );
        }
      })
      .catch(() => { })
      .finally(() => setLoading(false));
  }, [enabled]);

  const addLocal = useCallback((c: RecorrenciaConfig) => {
    setConfigs((prev) => {
      const id = String(c.id);
      if (prev.some((x) => String(x.id) === id)) return prev;
      return [...prev, c];
    });
  }, []);

  return { configs, loading, addLocal };
}

// ── Subcomponentes de Card ───────────────────────────────────────────────────

interface CardProps {
  form: LancamentoFormState;
  setField: <K extends keyof LancamentoFormState>(field: K, value: LancamentoFormState[K]) => void;
  fieldErrors: Record<string, string>;
  data: FormSelectData;
  loading: boolean;
  tipo: TipoLancamento | null;
}

interface SugProps {
  sugState: SugestaoState;
  confiancaCampos: ConfiancaCampos;
  markManualEdit: (key: string) => void;
}

function FieldError({ error }: { error?: string }) {
  if (!error) return null;
  return <p className="text-xs text-destructive">{error}</p>;
}

function LancamentoInfoCard({
  form, setField, fieldErrors, data, loading, tipo,
  parceiroOptions,
  onAddParceiro,
  sugState, confiancaCampos, markManualEdit,
  rateioAtivo, onRateioChange,
}: CardProps & SugProps & {
  parceiroOptions: SearchSelectOption[];
  onAddParceiro: () => void;
  rateioAtivo: boolean;
  onRateioChange: (v: boolean) => void;
}) {
  const isReceita = tipo === 'receita';
  const starBase = { origem: sugState.origem };

  return (
    <Card className="rounded-lg">
      <CardHeader className="min-h-[50px] bg-accent/50">
        <CardTitle className="text-2sm flex items-center gap-1.5">
          <ClipboardList className="size-3.5 text-muted-foreground" />
          Informações do Lançamento
        </CardTitle>
      </CardHeader>
      <CardContent className="pt-4">
        <div className="grid grid-cols-12 gap-4">
          <div className="col-span-3 flex flex-col gap-2" data-field="parceiro_id">
            <Label htmlFor="fornecedor_id" className="text-xs font-medium">
              {isReceita ? 'Cliente' : 'Fornecedor'}
            </Label>
            <SearchSelectButton
              id="fornecedor_id"
              popoverModal={false}
              options={parceiroOptions}
              value={form.fornecedor}
              onValueChange={(v) => {
                setField('fornecedor', v);
                if (v !== '__novo__') {
                  setField('novoParceiroNome', '');
                  setField('novoParceiroCnpj', '');
                }
              }}
              placeholder={isReceita ? 'Selecione um cliente' : 'Selecione um fornecedor'}
              emptyListMessage={
                isReceita
                  ? 'Nenhum cliente cadastrado. Use o botão abaixo para adicionar.'
                  : 'Nenhum fornecedor cadastrado. Use o botão abaixo para adicionar.'
              }
              disabled={loading}
              replaceApplyButton={{
                label: isReceita ? '+ Adicionar Cliente' : '+ Adicionar Fornecedor',
                onClick: ({ close }) => { close(); onAddParceiro(); },
              }}
              suggestionStar={
                <SuggestionStar
                  currentValue={form.fornecedor}
                  suggestedValue={sugState.parceiro_id}
                  confianca={confiancaCampos.parceiro_id}
                  {...starBase}
                />
              }
            />
          </div>

          <div className="col-span-2 flex flex-col gap-2" data-field="data_competencia">
            <Label className="text-xs">
              Data de Competência <span className="text-destructive">*</span>
            </Label>
            <DatePicker
              value={form.dataCompetencia}
              onChange={(v) => {
                setField('dataCompetencia', v);
                setField('vencimento', v);
              }}
              placeholder="dd/mm/aaaa"
              aria-invalid={!!fieldErrors.data_competencia}
            />
            <FieldError error={fieldErrors.data_competencia} />
          </div>

          <div className="col-span-5 flex flex-col gap-2" data-field="descricao">
            <Label className="text-xs">
              Descrição <span className="text-destructive">*</span>
            </Label>
            <div className="relative">
              <Input
                placeholder="Informe a descrição"
                value={form.descricao}
                aria-invalid={!!fieldErrors.descricao}
                className="pr-10"
                onChange={(e) => {
                  markManualEdit('descricao');
                  setField('descricao', e.target.value);
                }}
              />
              <SuggestionStar
                currentValue={form.descricao}
                suggestedValue={sugState.descricao}
                confianca={confiancaCampos.descricao}
                placement="absolute"
                {...starBase}
              />
            </div>
            <FieldError error={fieldErrors.descricao} />
          </div>

          <div className="col-span-2 flex flex-col gap-2" data-field="valor">
            <Label className="text-xs">
              Valor <span className="text-destructive">*</span>
            </Label>
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">
                R$
              </span>
              <CurrencyInput
                className="pl-8"
                value={form.valor}
                onMaskedChange={(v) => setField('valor', v)}
                aria-invalid={!!fieldErrors.valor}
              />
            </div>
            <FieldError error={fieldErrors.valor} />
          </div>



          <div className="col-span-3 flex flex-col gap-2" data-field="lancamento_padrao_id">
            <Label className="text-xs">
              Categoria <span className="text-destructive">*</span>
            </Label>
            <SearchSelect
              popoverModal={false}
              options={data.categorias.map((c) => ({ value: String(c.id), label: c.description }))}
              value={form.categoria}
              onValueChange={(v) => {
                markManualEdit('lancamento_padrao_id');
                setField('categoria', v);
              }}
              placeholder="Selecione uma categoria..."
              disabled={loading}
              suggestionStar={
                <SuggestionStar
                  currentValue={form.categoria}
                  suggestedValue={sugState.lancamento_padrao_id}
                  confianca={confiancaCampos.lancamento_padrao_id}
                  {...starBase}
                />
              }
            />
            <FieldError error={fieldErrors.lancamento_padrao_id} />
          </div>

          <div className="col-span-3 flex flex-col gap-2" data-field="cost_center_id">
            <Label className="text-xs">Centro de Custo</Label>
            <SearchSelect
              popoverModal={false}
              options={data.centrosCusto.map((c) => ({ value: String(c.id), label: `${c.code} – ${c.name}` }))}
              value={form.centroCusto}
              onValueChange={(v) => {
                markManualEdit('cost_center_id');
                setField('centroCusto', v);
              }}
              placeholder="Selecione um centro..."
              disabled={loading}
              suggestionStar={
                <SuggestionStar
                  currentValue={form.centroCusto}
                  suggestedValue={sugState.cost_center_id}
                  confianca={confiancaCampos.cost_center_id}
                  {...starBase}
                />
              }
            />
          </div>

          <div className="col-span-5 flex flex-col gap-2" data-field="entidade_id">
            <Label className="text-xs">
              Entidade Financeira <span className="text-destructive">*</span>
            </Label>
            <SearchSelect
              popoverModal={false}
              options={data.entidades.map((e) => ({
                value: e.id,
                label: e.label,
                icon: e.logo
                  ?? (e.tipo === 'caixa'
                    ? '/tenancy/assets/media/svg/bancos/fraternidadecaixa.svg'
                    : '/tenancy/assets/media/svg/bancos/default.svg'),
              }))}
              value={form.entidade}
              onValueChange={(v) => setField('entidade', v)}
              placeholder="Selecione a conta..."
              disabled={loading}
            />
            <FieldError error={fieldErrors.entidade_id} />
          </div>

          <div className="col-span-3 flex flex-col gap-2" data-field="tipo_documento">
            <Label className="text-xs">
              Forma de Pagamento <span className="text-destructive">*</span>
            </Label>
            <SearchSelect
              popoverModal={false}
              options={data.formasPagamento.map((f) => ({ value: f.codigo, label: f.nome }))}
              value={form.formaPagamento}
              onValueChange={(v) => {
                markManualEdit('tipo_documento');
                setField('formaPagamento', v);
              }}
              placeholder="Selecione a forma..."
              disabled={loading}
              suggestionStar={
                <SuggestionStar
                  currentValue={form.formaPagamento}
                  suggestedValue={sugState.tipo_documento}
                  confianca={confiancaCampos.tipo_documento}
                  {...starBase}
                />
              }
            />
            <FieldError error={fieldErrors.tipo_documento} />
          </div>

          <div className="col-span-2 flex flex-col gap-2" data-field="numero_documento">
            <Label className="text-xs">Nº do Documento</Label>
            <Input
              placeholder="Ex: NF-0001"
              value={form.numeroDocumento}
              onChange={(e) => setField('numeroDocumento', e.target.value)}
            />
          </div>
          {!isReceita && (
            <div className="col-span-1 flex flex-col gap-2 items-start justify-end">
              <div className="flex items-center gap-1">
                <Label htmlFor="rateio-toggle" className="text-xs">Habilitar rateio</Label>
                <Tooltip>
                  <TooltipTrigger asChild>
                    <button type="button" className="text-muted-foreground hover:text-foreground shrink-0" aria-label="Sobre habilitar rateio">
                      <Info className="size-3.5" />
                    </button>
                  </TooltipTrigger>
                  <TooltipContent side="top" className="max-w-[220px] text-center">
                    Ativa o rateio deste lançamento entre filiais. Ao habilitar, defina a distribuição por valor ou percentual em cada linha.
                  </TooltipContent>
                </Tooltip>
              </div>
              <Switch
                id="rateio-toggle"
                size="lg"
                checked={rateioAtivo}
                onCheckedChange={onRateioChange}
              />
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
}

function LancamentoCondicaoCard({
  form, setField, tipo, fieldErrors, csrfToken,
}: Pick<CardProps, 'form' | 'setField' | 'tipo' | 'fieldErrors'> & {
  showRecebidoPago: boolean;
  csrfToken: string;
}) {
  const isReceita = tipo === 'receita';
  const showPagamento = !form.repetir && (form.parcelamento === 'avista' || form.parcelamento === '1x');
  const multiParcelas = !form.repetir && parcelamentoQuantidadeParcelas(form.parcelamento) !== null;

  const { configs, addLocal } = useRecorrencias(form.repetir);
  const [recDialogOpen, setRecDialogOpen] = useState(false);
  const [recSaving, setRecSaving] = useState(false);

  const configOptions = useMemo(
    () => configs.map((c) => ({ value: String(c.id), label: c.nome })),
    [configs],
  );

  const handleCreateRecorrencia = useCallback(async () => {
    const intervalo = parseInt(form.recorrenciaIntervalo, 10) || 1;
    const freq = form.recorrenciaFrequencia;
    const ocorr = parseInt(form.recorrenciaOcorrencias, 10) || 12;

    const duplicataLocal = configs.find(
      (c) => c.intervalo_repeticao === intervalo && c.frequencia === freq && c.total_ocorrencias === ocorr,
    );
    if (duplicataLocal) {
      notify.warning(
        'Configuração já cadastrada',
        'Esta combinação já existe para a empresa. Selecionamos a configuração existente.',
      );
      setField('configuracaoRecorrencia', String(duplicataLocal.id));
      setRecDialogOpen(false);
      return;
    }

    setRecSaving(true);
    try {
      const res = await fetch('/recorrencias', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken ?? '',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          intervalo_repeticao: intervalo,
          frequencia: freq,
          apos_ocorrencias: ocorr,
        }),
      });

      const json = await res.json();

      if (res.status === 422 && json.data?.id != null) {
        const row: RecorrenciaConfig = {
          id: String(json.data.id),
          nome: String(json.data.nome ?? ''),
          intervalo_repeticao: Number(json.data.intervalo_repeticao),
          frequencia: String(json.data.frequencia),
          total_ocorrencias: Number(json.data.total_ocorrencias),
        };
        addLocal(row);
        setField('configuracaoRecorrencia', String(json.data.id));
        notify.warning(
          'Configuração já cadastrada',
          json.message ?? 'Esta combinação já existe para a empresa. Selecionamos a configuração existente.',
        );
        setRecDialogOpen(false);
        return;
      }

      if (json.success && json.data) {
        addLocal({
          id: String(json.data.id),
          nome: String(json.data.nome ?? ''),
          intervalo_repeticao: Number(json.data.intervalo_repeticao),
          frequencia: String(json.data.frequencia),
          total_ocorrencias: Number(json.data.total_ocorrencias),
        });
        setField('configuracaoRecorrencia', String(json.data.id));
        setRecDialogOpen(false);
        notify.success('Recorrência criada', json.message ?? 'Configuração salva com sucesso.');
      }
    } catch {
      notify.error('Erro ao salvar', 'Não foi possível criar a configuração de recorrência.');
    } finally {
      setRecSaving(false);
    }
  }, [
    configs,
    form.recorrenciaIntervalo,
    form.recorrenciaFrequencia,
    form.recorrenciaOcorrencias,
    csrfToken,
    addLocal,
    setField,
  ]);

  return (
    <>
      <Card className="rounded-lg">
        <CardHeader className="min-h-[50px] bg-accent/50">
          <CardTitle className="text-2sm flex items-center gap-1.5">
            <CalendarCheck className="size-3.5 text-muted-foreground" />
            Condição de Pagamento
          </CardTitle>
          <CardToolbar>
            <div className="flex items-center gap-2">
              <Switch
                size="lg"
                checked={form.repetir}
                onCheckedChange={(v) => {
                  setField('repetir', v);
                  if (!v) {
                    setField('configuracaoRecorrencia', '');
                    setField('recebidoPago', false);
                  }
                }}
                id="switch-repetir"
              />
              <Label htmlFor="switch-repetir" className="text-xs cursor-pointer">
                Repetir lançamento?
              </Label>
              <Tooltip>
                <TooltipTrigger asChild>
                  <button type="button" className="text-muted-foreground hover:text-foreground shrink-0" aria-label="Sobre repetir lançamento">
                    <Info className="size-3.5" />
                  </button>
                </TooltipTrigger>
                <TooltipContent side="bottom" className="max-w-[240px] text-center">
                  Quando ativado, o lançamento será repetido automaticamente conforme a configuração de recorrência selecionada (frequência e número de ocorrências).
                </TooltipContent>
              </Tooltip>
            </div>
          </CardToolbar>
        </CardHeader>
        <CardContent className="pt-4 space-y-4">
          {/* Linha de configuração de recorrência */}
          {form.repetir && (
            <div className="flex items-end gap-3" data-field="configuracao_recorrencia">
              <div className="flex-1 flex flex-col gap-2">
                <Label className="text-xs">
                  Configuração de Recorrência <span className="text-destructive">*</span>
                </Label>
                <SearchSelect
                  popoverModal={false}
                  options={configOptions}
                  value={form.configuracaoRecorrencia}
                  onValueChange={(v) => {
                    setField('configuracaoRecorrencia', v);
                    const cfg = configs.find((c) => String(c.id) === v);
                    if (cfg) {
                      setField('recorrenciaIntervalo', String(cfg.intervalo_repeticao));
                      setField('recorrenciaFrequencia', cfg.frequencia);
                      setField('recorrenciaOcorrencias', String(cfg.total_ocorrencias));
                    }
                  }}
                  placeholder="Selecione uma configuração..."
                />
                <FieldError error={fieldErrors.configuracao_recorrencia} />
              </div>
              <Button
                type="button"
                variant="outline"
                size="sm"
                className="h-[34px] gap-1.5"
                onClick={() => setRecDialogOpen(true)}
              >
                <Plus className="size-3.5" />
                Nova
              </Button>
            </div>
          )}

          <div className="grid grid-cols-12 gap-4">
            {/* Parcelamento (oculto quando repetir ativo) */}
            {!form.repetir && (
              <div className="col-span-3 flex flex-col gap-2">
                <Label className="text-xs">
                  Parcelamento <span className="text-destructive">*</span>
                </Label>
                <SearchSelect
                  popoverModal={false}
                  options={PARCELAMENTO_OPTIONS}
                  value={form.parcelamento}
                  onValueChange={(v) => {
                    const was1x = form.parcelamento === '1x';
                    if (v !== '1x' && was1x) {
                      setField('juros', '');
                      setField('multa', '');
                      setField('desconto', '');
                      setField('previsaoPagamento', todayIso());
                    }
                    if (v === '1x') {
                      setField(
                        'previsaoPagamento',
                        form.vencimento?.trim() ? form.vencimento : todayIso(),
                      );
                    }
                    if (parcelamentoQuantidadeParcelas(v)) {
                      setField('recebidoPago', false);
                    }
                    setField('parcelamento', v);
                  }}
                  placeholder="Selecione..."
                />
              </div>
            )}

            {!form.repetir && multiParcelas && (
              <div className="col-span-3 flex flex-col gap-2" data-field="intervalo_parcelas_dias">
                <Label className="text-xs inline-flex items-center gap-1 flex-wrap">
                  Intervalo entre parcelas (dias) <span className="text-destructive">*</span>
                  <Tooltip>
                    <TooltipTrigger asChild>
                      <button
                        type="button"
                        className="text-muted-foreground hover:text-foreground shrink-0"
                        aria-label="Sobre o intervalo entre parcelas"
                      >
                        <Info className="size-3.5" />
                      </button>
                    </TooltipTrigger>
                    <TooltipContent side="top" className="max-w-xs">
                      Dias somados ao vencimento da parcela anterior para calcular as datas seguintes (ex.: 30 =
                      aproximadamente um mês).
                    </TooltipContent>
                  </Tooltip>
                </Label>
                <Input
                  type="number"
                  min={1}
                  max={366}
                  className="h-[34px]"
                  value={form.parcelasIntervaloDias}
                  onChange={(e) => setField('parcelasIntervaloDias', e.target.value)}
                />
                <FieldError error={fieldErrors.intervalo_parcelas_dias} />
              </div>
            )}

            {/* Dia de Cobrança (aparece quando repetir ativo) */}
            {form.repetir && (
              <div className="col-span-3 flex flex-col gap-2">
                <Label className="text-xs">
                  Dia de Cobrança <span className="text-destructive">*</span>
                </Label>
                <SearchSelect
                  popoverModal={false}
                  options={DIA_COBRANCA_OPTIONS}
                  value={form.diaCobranca}
                  onValueChange={(v) => setField('diaCobranca', v)}
                  placeholder="Selecione..."
                />
                <FieldError error={fieldErrors.dia_cobranca} />
              </div>
            )}

            <div className="col-span-3 flex flex-col gap-2">
              <Label className="text-xs">
                {form.repetir || multiParcelas ? '1º Vencimento' : 'Vencimento'}{' '}
                <span className="text-destructive">*</span>
              </Label>
              <DatePicker
                value={form.vencimento}
                onChange={(v) => setField('vencimento', v)}
                placeholder="dd/mm/aaaa"
                billingDayConstraint={form.repetir && form.diaCobranca ? form.diaCobranca : undefined}
              />
              <FieldError error={fieldErrors.vencimento} />
            </div>

            {/* Pago/Recebido + Agendado (oculto quando repetir ativo) */}
            {showPagamento && (
              <>
                <div className="col-span-3 flex items-end pb-1">
                  <div className="flex items-center gap-2">
                    <Switch
                      size="lg"
                      checked={form.recebidoPago}
                      onCheckedChange={(v) => setField('recebidoPago', v)}
                      id="switch-recebido-pago"
                    />
                    <Label htmlFor="switch-recebido-pago" className="text-xs cursor-pointer">
                      {isReceita ? 'Marcar como Recebido' : 'Marcar como Pago'}
                    </Label>
                    <Tooltip>
                      <TooltipTrigger asChild>
                        <button type="button" className="text-muted-foreground hover:text-foreground shrink-0" aria-label="Sobre marcar como pago/recebido">
                          <Info className="size-3.5" />
                        </button>
                      </TooltipTrigger>
                      <TooltipContent side="top" className="max-w-[240px] text-center">
                        {isReceita
                          ? 'Marca este lançamento como já recebido. A data de recebimento será registrada com base na previsão de pagamento.'
                          : 'Marca este lançamento como já pago. A data de pagamento será registrada com base na previsão de pagamento.'}
                      </TooltipContent>
                    </Tooltip>
                  </div>
                </div>
                <div className="col-span-3 flex items-end pb-1">
                  <div className="flex items-center gap-2">
                    <Switch
                      size="lg"
                      checked={form.agendado}
                      onCheckedChange={(v) => setField('agendado', v)}
                      id="switch-agendado"
                    />
                    <Label htmlFor="switch-agendado" className="text-xs cursor-pointer">Agendado</Label>
                    <Tooltip>
                      <TooltipTrigger asChild>
                        <button type="button" className="text-muted-foreground hover:text-foreground shrink-0" aria-label="Sobre agendado">
                          <Info className="size-3.5" />
                        </button>
                      </TooltipTrigger>
                      <TooltipContent side="top" className="max-w-[240px] text-center">
                        Indica que o {isReceita ? 'recebimento' : 'pagamento'} será realizado automaticamente na data de vencimento. Quando ativado, uma notificação será enviada via WhatsApp no dia do vencimento.
                      </TooltipContent>
                    </Tooltip>
                  </div>
                </div>
              </>
            )}
          </div>
        </CardContent>
      </Card>

      {/* Dialog para criar nova configuração de recorrência */}
      <Dialog open={recDialogOpen} onOpenChange={setRecDialogOpen}>
        <DialogContent className="sm:max-w-[440px]">
          <DialogHeader>
            <DialogTitle className="flex items-center gap-2">
              <Repeat className="size-4" />
              Nova Recorrência
            </DialogTitle>
            <DialogDescription className="sr-only">
              Defina o intervalo, a frequência e quantas ocorrências terá antes de salvar a configuração.
            </DialogDescription>
          </DialogHeader>
          <DialogBody className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="flex flex-col gap-2">
                <Label className="text-xs">
                  Repetir a cada <span className="text-destructive">*</span>
                </Label>
                <Input
                  type="number"
                  min={1}
                  value={form.recorrenciaIntervalo}
                  onChange={(e) => setField('recorrenciaIntervalo', e.target.value)}
                  placeholder="1"
                />
              </div>
              <div className="flex flex-col gap-2">
                <Label className="text-xs">
                  Frequência <span className="text-destructive">*</span>
                </Label>
                <SearchSelect
                  popoverModal
                  options={FREQUENCIA_OPTIONS}
                  value={form.recorrenciaFrequencia}
                  onValueChange={(v) => setField('recorrenciaFrequencia', v)}
                  placeholder="Selecione..."
                />
              </div>
            </div>

            <div className="border-t pt-4">
              <Label className="text-xs font-medium mb-3 block">Término da recorrência</Label>
              <div className="flex items-center gap-3">
                <span className="text-sm text-muted-foreground">Após</span>
                <Input
                  type="number"
                  min={1}
                  max={366}
                  className="w-24"
                  value={form.recorrenciaOcorrencias}
                  onChange={(e) => setField('recorrenciaOcorrencias', e.target.value)}
                  placeholder="12"
                />
                <span className="text-sm text-muted-foreground">ocorrências</span>
              </div>
            </div>
          </DialogBody>
          <DialogFooter>
            <Button variant="outline" onClick={() => setRecDialogOpen(false)}>
              Cancelar
            </Button>
            <Button onClick={handleCreateRecorrencia} disabled={recSaving}>
              {recSaving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
              {recSaving ? 'Salvando...' : 'Salvar'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  );
}

/** Parcelamento 1x + sem Pago/Recebido: espelha o accordion Blade «Previsão de pagamento». */
function LancamentoPrevisaoPagamentoCard({
  form,
  setField,
  fieldErrors,
  tipo,
}: Pick<CardProps, 'form' | 'setField' | 'fieldErrors'> & { tipo: TipoLancamento | null }) {
  const isReceita = tipo === 'receita';
  const valorAPagar = useMemo(() => {
    const valorNum = parseCurrency(form.valor);
    const j = parseCurrency(form.juros);
    const m = parseCurrency(form.multa);
    const desc = parseCurrency(form.desconto);
    return Math.max(0, valorNum + j + m - desc);
  }, [form.valor, form.juros, form.multa, form.desconto]);

  return (
    <Card className="rounded-lg">
      <CardHeader className="min-h-[50px] bg-accent/50 py-2">
        <CardTitle className="text-2sm flex items-center gap-1.5">
          <Wallet className="size-3.5 text-muted-foreground" />
          {isReceita ? 'Previsão de recebimento' : 'Previsão de pagamento'}
        </CardTitle>
      </CardHeader>
      <CardContent className="pt-4">
        <div className="grid grid-cols-12 gap-4">
          <div
            className="col-span-12 sm:col-span-6 lg:col-span-2 min-w-0 flex flex-col gap-2"
            data-field="previsao_pagamento"
          >
            <Label className="text-xs inline-flex items-center gap-1 flex-wrap">
              {isReceita ? 'Previsão de recebimento' : 'Previsão de pagamento'} <span className="text-destructive">*</span>
              <Tooltip>
                <TooltipTrigger asChild>
                  <button
                    type="button"
                    className="text-muted-foreground hover:text-foreground shrink-0"
                    aria-label={isReceita ? 'Sobre a previsão de recebimento' : 'Sobre a previsão de pagamento'}
                  >
                    <Info className="size-3.5" />
                  </button>
                </TooltipTrigger>
                <TooltipContent side="top" className="max-w-xs">
                  {isReceita
                    ? 'Data em que se prevê receber o valor (pode ser a mesma do vencimento).'
                    : 'Data em que se prevê efetuar o pagamento (pode ser a mesma do vencimento).'}
                </TooltipContent>
              </Tooltip>
            </Label>
            <DatePicker
              value={form.previsaoPagamento}
              onChange={(v) => setField('previsaoPagamento', v)}
              placeholder="dd/mm/aaaa"
            />
            <FieldError error={fieldErrors.previsao_pagamento} />
          </div>

          <div className="col-span-12 sm:col-span-6 lg:col-span-2 flex flex-col gap-2">
            <Label className="text-xs">
              Juros <span className="text-destructive">*</span>
            </Label>
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">
                R$
              </span>
              <CurrencyInput
                className="pl-8"
                value={form.juros}
                onMaskedChange={(v) => setField('juros', v)}
              />
            </div>
          </div>

          <div className="col-span-12 sm:col-span-6 lg:col-span-2 flex flex-col gap-2">
            <Label className="text-xs">
              Multa <span className="text-destructive">*</span>
            </Label>
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">
                R$
              </span>
              <CurrencyInput
                className="pl-8"
                value={form.multa}
                onMaskedChange={(v) => setField('multa', v)}
              />
            </div>
          </div>

          <div className="col-span-12 sm:col-span-6 lg:col-span-2 flex flex-col gap-2">
            <Label className="text-xs">
              Desconto <span className="text-destructive">*</span>
            </Label>
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">
                R$
              </span>
              <CurrencyInput
                className="pl-8"
                value={form.desconto}
                onMaskedChange={(v) => setField('desconto', v)}
              />
            </div>
          </div>

          <div className="col-span-12 sm:col-span-6 lg:col-span-2 flex flex-col gap-2">
            <Label className="text-xs inline-flex items-center gap-1 flex-wrap">
              {isReceita ? 'Valor a receber' : 'Valor a pagar'} <span className="text-destructive">*</span>
              <Tooltip>
                <TooltipTrigger asChild>
                  <button
                    type="button"
                    className="text-muted-foreground hover:text-foreground shrink-0"
                    aria-label={isReceita ? 'Sobre o valor a receber' : 'Sobre o valor a pagar'}
                  >
                    <Info className="size-3.5" />
                  </button>
                </TooltipTrigger>
                <TooltipContent side="top" className="max-w-xs">
                  {isReceita
                    ? 'Valor total a receber, calculado automaticamente: Valor + Juros + Multa − Desconto.'
                    : 'Valor total a pagar, calculado automaticamente: Valor + Juros + Multa − Desconto.'}
                </TooltipContent>
              </Tooltip>
            </Label>
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">
                R$
              </span>
              <Input readOnly className="pl-8 bg-muted" value={formatBRL(valorAPagar)} tabIndex={-1} />
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}

function formatBRL(v: number): string {
  return v.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/** 2x ou mais: tabela de parcelas + persistência em `parcelamentos` via API (como o modal Blade). */
function LancamentoParcelasCard({
  form,
  setParcelaField,
  setParcelaValor,
  fieldErrors,
}: {
  form: LancamentoFormState;
  setParcelaField: (index: number, field: keyof ParcelaLinha, value: ParcelaLinha[keyof ParcelaLinha]) => void;
  setParcelaValor: (index: number, masked: string, valorTotal: number) => void;
  fieldErrors: Record<string, string>;
}) {
  const n = parcelamentoQuantidadeParcelas(form.parcelamento);

  if (!n) return null;

  if (form.parcelasLinhas.length === 0) {
    return (
      <Card className="rounded-lg" data-field="parcelas">
        <CardHeader className="min-h-[50px] bg-accent/50 py-2">
          <CardTitle className="text-2sm flex items-center gap-1.5">
            <Table2 className="size-3.5 text-muted-foreground" />
            Parcelas
          </CardTitle>
        </CardHeader>
        <CardContent className="pt-4">
          <p className="text-sm text-muted-foreground">Montando as linhas de parcelas…</p>
        </CardContent>
      </Card>
    );
  }

  const valorTotal = parseCurrency(form.valor);

  return (
    <Card className="rounded-lg" data-field="parcelas">
      <CardHeader className="min-h-[50px] bg-accent/50 py-2">
        <CardTitle className="text-2sm flex items-center gap-1.5">
          <Table2 className="size-3.5 text-muted-foreground" />
          Parcelas
        </CardTitle>
      </CardHeader>
      <CardContent className="pt-4">
        {fieldErrors.parcelas && <p className="text-xs text-destructive mb-3">{fieldErrors.parcelas}</p>}
        <div className="overflow-x-auto -mx-1 px-1">
          <table className="w-full text-xs border-collapse">
            <colgroup>
              <col className="w-6" />
              <col className="w-[180px]" />
              <col className="w-[150px]" />
              <col className="w-[72px]" />
              <col />
              <col className="w-[90px]" />
            </colgroup>
            <thead>
              <tr className="text-left text-muted-foreground border-b border-border">
                <th className="py-2 pr-2 font-medium">#</th>
                <th className="py-2 pr-2 font-medium">
                  Vencimento <span className="text-destructive">*</span>
                </th>
                <th className="py-2 pr-2 font-medium">
                  Valor (R$) <span className="text-destructive">*</span>
                </th>
                <th className="py-2 pr-2 font-medium">%</th>
                <th className="py-2 pr-2 font-medium">Descrição</th>
                <th className="py-2 font-medium">Agendado</th>
              </tr>
            </thead>
            <tbody>
              {form.parcelasLinhas.map((linha, index) => (
                <tr key={index} className="border-b border-border/80 align-top">
                  <td className="py-2 pr-2 text-muted-foreground">{index + 1}</td>
                  <td className="py-2 pr-2" data-field={`parcelas.${index}.vencimento`}>
                    <DatePicker
                      value={linha.vencimento}
                      onChange={(v) => setParcelaField(index, 'vencimento', v)}
                      placeholder="dd/mm/aaaa"
                      size="lg"
                    />
                    <FieldError error={fieldErrors[`parcelas.${index}.vencimento`]} />
                  </td>
                  <td className="py-2 pr-2" data-field={`parcelas.${index}.valor`}>
                    <div className="relative">
                      <span className="absolute left-1.5 top-1/2 -translate-y-1/2 text-[10px] text-muted-foreground font-medium pointer-events-none">
                        R$
                      </span>
                      <CurrencyInput
                        className="pl-6 h-[30px] text-xs"
                        value={linha.valor}
                        onMaskedChange={(masked) => setParcelaValor(index, masked, valorTotal)}
                      />
                    </div>
                    <FieldError error={fieldErrors[`parcelas.${index}.valor`]} />
                  </td>
                  <td className="py-2 pr-2">
                    <Input readOnly className="h-[30px] text-xs bg-muted text-right px-1.5" value={linha.percentual} tabIndex={-1} />
                  </td>
                  <td className="py-2 pr-2">
                    <Input
                      className="h-[30px] text-xs w-full"
                      value={linha.descricao}
                      onChange={(e) => setParcelaField(index, 'descricao', e.target.value)}
                    />
                  </td>
                  <td className="py-2">
                    <div className="flex items-center gap-2 pt-1">
                      <Switch
                        size="sm"
                        checked={linha.agendado}
                        onCheckedChange={(v) => setParcelaField(index, 'agendado', v)}
                        id={`parcela-agendado-${index}`}
                      />
                      <Label htmlFor={`parcela-agendado-${index}`} className="text-xs cursor-pointer font-normal">
                        Agendado
                      </Label>
                      <Tooltip>
                        <TooltipTrigger asChild>
                          <button type="button" className="text-muted-foreground hover:text-foreground shrink-0" aria-label="Sobre parcela agendada">
                            <Info className="size-3.5" />
                          </button>
                        </TooltipTrigger>
                        <TooltipContent side="top" className="max-w-[220px] text-center">
                          Indica que esta parcela será paga automaticamente na data de vencimento via débito agendado.
                        </TooltipContent>
                      </Tooltip>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>
  );
}

interface RateioCardProps {
  form: LancamentoFormState;
  data: FormSelectData;
  fieldErrors: Record<string, string>;
  addRateio: () => void;
  removeRateio: (index: number) => void;
  setRateioField: (index: number, field: keyof RateioItem, value: string) => void;
  distributeRateio: () => void;
  gerarNasFiliais: boolean;
  onGerarNasFiliaisChange: (v: boolean) => void;
}

function LancamentoRateioCard({
  form, data, fieldErrors,
  addRateio, removeRateio, setRateioField, distributeRateio,
  gerarNasFiliais, onGerarNasFiliaisChange,
}: RateioCardProps) {
  const valorTotal = parseCurrency(form.valor);
  const totalRateado = form.rateios.reduce((acc, r) => acc + parseCurrency(r.valor), 0);
  const diferenca = valorTotal - totalRateado;
  const progress = valorTotal > 0 ? Math.min((totalRateado / valorTotal) * 100, 100) : 0;
  const isMatch = Math.abs(diferenca) <= 0.01;

  const filialOptions = useMemo(
    () => (data.filiais ?? []).map((f) => ({ value: String(f.id), label: f.name, icon: f.avatar_url })),
    [data.filiais],
  );
  const ccOptions = useMemo(
    () => (data.centrosCusto ?? []).map((c) => ({ value: String(c.id), label: `${c.code} – ${c.name}` })),
    [data.centrosCusto],
  );
  const catOptions = useMemo(
    () => (data.categorias ?? []).map((c) => ({ value: String(c.id), label: c.description })),
    [data.categorias],
  );

  const handleValorChange = useCallback(
    (index: number, masked: string) => {
      setRateioField(index, 'valor', masked);
      const newVal = parseCurrency(masked);
      const pct = valorTotal > 0 ? ((newVal / valorTotal) * 100) : 0;
      setRateioField(index, 'percentual', pct.toFixed(2).replace('.', ','));
    },
    [setRateioField, valorTotal],
  );

  const handlePercentualChange = useCallback(
    (index: number, raw: string) => {
      setRateioField(index, 'percentual', raw);
      const pct = parseFloat(raw.replace(',', '.')) || 0;
      const newVal = (pct / 100) * valorTotal;
      setRateioField(index, 'valor', formatBRL(Math.round(newVal * 100) / 100));
    },
    [setRateioField, valorTotal],
  );

  return (
    <Card className="rounded-lg">
      <CardHeader className="min-h-[50px] bg-accent/50">
        <CardTitle className="text-2sm flex items-center gap-1.5">
          <PieChart className="size-3.5 text-muted-foreground" />
          Rateio
        </CardTitle>
        <CardToolbar>
          <div className="flex items-center gap-3">
            <div className="flex items-center gap-1.5">
              <Switch
                id="rateio-filiais-switch"
                size="lg"
                checked={gerarNasFiliais}
                onCheckedChange={onGerarNasFiliaisChange}
              />
              <Label htmlFor="rateio-filiais-switch" className="text-xs cursor-pointer text-muted-foreground">
                Gerar nas filiais
              </Label>
              <Tooltip>
                <TooltipTrigger asChild>
                  <button
                    type="button"
                    className="text-muted-foreground hover:text-foreground shrink-0"
                    aria-label="Sobre gerar nas filiais"
                  >
                    <Info className="size-3.5" />
                  </button>
                </TooltipTrigger>
                <TooltipContent side="bottom" className="max-w-[240px] text-center">
                  Quando ativado, um lançamento de custo (Contas a Pagar) será criado automaticamente em cada filial selecionada. Quando desativado, o rateio fica registrado apenas na matriz.
                </TooltipContent>
              </Tooltip>
            </div>
          </div>
        </CardToolbar>
      </CardHeader>
      <CardContent className="pt-4 pb-4 space-y-3">
        {/* Barra de progresso */}
        <div className="space-y-1.5">
          <div className="flex items-center justify-between text-xs gap-2">
            <span className="text-muted-foreground shrink-0">
              Total rateado: <span className="font-medium text-foreground">R$ {formatBRL(totalRateado)}</span>
              {' / '}R$ {formatBRL(valorTotal)}
            </span>
            <div className="flex items-center gap-2">
              {!isMatch && form.rateios.length > 0 && (
                <span className="text-destructive font-medium">
                  {diferenca > 0 ? `Faltam R$ ${formatBRL(diferenca)}` : `Excede R$ ${formatBRL(Math.abs(diferenca))}`}
                </span>
              )}
              {isMatch && form.rateios.length > 0 && (
                <span className="text-green-600 font-medium">Rateio completo</span>
              )}
              <Tooltip>
                <TooltipTrigger asChild>
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    className="text-xs h-6 px-2 gap-1 border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 hover:border-blue-300 shrink-0"
                    onClick={distributeRateio}
                    disabled={form.rateios.length === 0}
                  >
                    <Equal className="size-3" />
                    Distribuir igualmente
                  </Button>
                </TooltipTrigger>
                <TooltipContent side="bottom" className="max-w-[220px] text-center">
                  Divide o valor total do lançamento em partes iguais entre todas as linhas de rateio.
                </TooltipContent>
              </Tooltip>
            </div>
          </div>
          <div className="w-full h-2 bg-muted rounded-full overflow-hidden">
            <div
              className={`h-full rounded-full transition-all duration-300 ${isMatch ? 'bg-green-500' : diferenca < 0 ? 'bg-destructive' : 'bg-blue-500'}`}
              style={{ width: `${Math.min(progress, 100)}%` }}
            />
          </div>
        </div>

        {fieldErrors.rateios && (
          <p className="text-xs text-destructive font-medium">{fieldErrors.rateios}</p>
        )}

        {/* Header das colunas */}
        {form.rateios.length > 0 && (
          <div className="grid grid-cols-12 gap-3 px-1">
            <div className="col-span-3"><Label className="text-xs font-medium">Filial <span className="text-destructive">*</span></Label></div>
            <div className="col-span-3"><Label className="text-xs font-medium">Centro de Custo</Label></div>
            <div className="col-span-2"><Label className="text-xs font-medium">Categoria</Label></div>
            <div className="col-span-1"><Label className="text-xs font-medium">% <span className="text-destructive">*</span></Label></div>
            <div className="col-span-2"><Label className="text-xs font-medium">Valor <span className="text-destructive">*</span></Label></div>
            <div className="col-span-1" />
          </div>
        )}

        {/* Linhas de rateio */}
        {form.rateios.map((rateio, index) => (
          <div key={rateio.id} className="grid grid-cols-12 gap-3 items-center">
            <div className="col-span-3">
              <SearchSelect
                popoverModal={false}
                options={filialOptions}
                value={rateio.filial_id}
                onValueChange={(v) => setRateioField(index, 'filial_id', v)}
                placeholder="Selecione..."
              />
            </div>
            <div className="col-span-3">
              <SearchSelect
                popoverModal={false}
                options={ccOptions}
                value={rateio.centro_custo_id}
                onValueChange={(v) => setRateioField(index, 'centro_custo_id', v)}
                placeholder="Selecione..."
              />
            </div>
            <div className="col-span-2">
              <SearchSelect
                popoverModal={false}
                options={catOptions}
                value={rateio.lancamento_padrao_id}
                onValueChange={(v) => setRateioField(index, 'lancamento_padrao_id', v)}
                placeholder="Selecione..."
              />
            </div>
            <div className="col-span-1">
              <div className="relative">
                <Input
                  className="pr-5 text-right text-xs"
                  value={rateio.percentual}
                  onChange={(e) => handlePercentualChange(index, e.target.value)}
                />
                <span className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-muted-foreground pointer-events-none select-none">%</span>
              </div>
            </div>
            <div className="col-span-2">
              <div className="relative">
                <span className="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-medium pointer-events-none select-none">R$</span>
                <CurrencyInput
                  className="pl-8 text-xs"
                  value={rateio.valor}
                  onMaskedChange={(masked) => handleValorChange(index, masked)}
                />
              </div>
            </div>
            <div className="col-span-1 flex justify-center">
              <Button
                type="button"
                variant="ghost"
                size="icon"
                className="size-7 shrink-0 text-muted-foreground hover:text-destructive"
                onClick={() => removeRateio(index)}
              >
                <X className="size-3.5" />
              </Button>
            </div>
          </div>
        ))}

        <div>
          <Button type="button" variant="outline" size="sm" onClick={addRateio}>
            + Adicionar Linha
          </Button>
        </div>
      </CardContent>
    </Card>
  );
}

function LancamentoHistoricoCard({
  form,
  setField,
  stagedAnexos,
  onStagedAnexosChange,
  existingAnexos,
  onDeleteExistingAnexo,
  disabled,
}: Pick<CardProps, 'form' | 'setField'> & {
  stagedAnexos: LancamentoStagedAnexo[];
  onStagedAnexosChange: (rows: LancamentoStagedAnexo[]) => void;
  existingAnexos: LancamentoExistingAnexo[];
  onDeleteExistingAnexo: (id: number) => Promise<boolean>;
  disabled?: boolean;
}) {
  const anexosCount = stagedAnexos.length + existingAnexos.length;
  return (
    <Card className="rounded-lg">
      <CardHeader className="min-h-[50px] bg-accent/50">
        <CardTitle className="text-2sm flex items-center gap-1.5">
          <History className="size-3.5 text-muted-foreground" />
          Histórico e Anexos
        </CardTitle>
      </CardHeader>
      <CardContent className="pt-0 px-0 pb-0">
        <Tabs defaultValue="historico">
          <div className="px-5 border-b border-border">
            <TabsList variant="line" size="sm">
              <TabsTrigger value="historico">Histórico Complementar</TabsTrigger>
              <TabsTrigger value="anexos">
                <Paperclip className="size-3" />
                Anexos
                {anexosCount > 0 ? (
                  <span className="ms-1 rounded-full bg-primary/15 px-1.5 py-px text-[10px] font-semibold tabular-nums">
                    {anexosCount}
                  </span>
                ) : null}
              </TabsTrigger>
            </TabsList>
          </div>

          <TabsContent value="historico" className="p-5">
            <Textarea
              placeholder="Mais detalhes sobre o lançamento..."
              maxLength={500}
              rows={3}
              value={form.historico}
              onChange={(e) => setField('historico', e.target.value)}
            />
            <p className="text-xs text-muted-foreground mt-1.5">
              {form.historico.length}/500 caracteres
            </p>
          </TabsContent>

          <TabsContent value="anexos" className="p-5">
            <LancamentoAnexosInput
              value={stagedAnexos}
              onChange={onStagedAnexosChange}
              disabled={disabled}
              existingAnexos={existingAnexos}
              onDeleteExistingAnexo={onDeleteExistingAnexo}
            />
          </TabsContent>
        </Tabs>
      </CardContent>
    </Card>
  );
}

// ── Painel do documento (viewer lateral dentro do drawer) ─────────────────────

function DrawerDocumentPanel({ preview }: { preview: LancamentoDocumentPreview }) {
  const [rotation, setRotation] = useState(0);
  const isPdf = preview.mimeType === 'application/pdf';

  return (
    <div className="w-[38%] shrink-0 border-r border-border bg-[#2d2d2d] relative flex flex-col">
      {/* Toolbar */}
      {!isPdf && (
        <TransformWrapper
          initialScale={1}
          minScale={0.25}
          maxScale={5}
          centerOnInit
          doubleClick={{ mode: 'toggle', step: 1 }}
          wheel={{ step: 0.08 }}
          panning={{ velocityDisabled: true }}
          key={preview.url}
        >
          <DrawerViewerToolbar
            onRotateLeft={() => setRotation((r) => (r - 90 + 360) % 360)}
            onRotateRight={() => setRotation((r) => (r + 90) % 360)}
          />
          <div
            className="flex-1 overflow-hidden relative"
            style={{
              backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0)',
              backgroundSize: '20px 20px',
            }}
          >
            <TransformComponent
              wrapperStyle={{ width: '100%', height: '100%' }}
              contentStyle={{
                width: '100%',
                height: '100%',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}
            >
              <img
                src={preview.url}
                alt={preview.filename ?? 'Documento'}
                draggable={false}
                className="max-w-full max-h-full select-none object-contain"
                style={{
                  transform: `rotate(${rotation}deg)`,
                  transition: 'transform 0.3s ease',
                }}
              />
            </TransformComponent>
          </div>
        </TransformWrapper>
      )}

      {isPdf && (
        <>
          <div className="flex items-center justify-between px-3 py-2 border-b border-white/10 shrink-0">
            <span className="text-xs font-semibold text-white/70 flex items-center gap-1.5">
              <FileSearch className="size-3.5" />
              Documento
            </span>
          </div>
          <iframe
            src={`${preview.url}#toolbar=1&navpanes=0&scrollbar=1&zoom=page-width`}
            className="flex-1 w-full border-0"
          />
        </>
      )}
    </div>
  );
}

function DrawerViewerToolbar({
  onRotateLeft,
  onRotateRight,
}: {
  onRotateLeft: () => void;
  onRotateRight: () => void;
}) {
  const { zoomIn, zoomOut, resetTransform, instance } = useControls();
  const scale = (instance as unknown as { transformState?: { scale: number } })?.transformState?.scale ?? 1;
  const zoomPct = Math.round(scale * 100);

  return (
    <div className="flex items-center justify-between px-3 py-2 border-b border-white/10 shrink-0">
      <span className="text-xs font-semibold text-white/70 flex items-center gap-1.5">
        <FileSearch className="size-3.5" />
        Documento
      </span>
      <div className="flex items-center gap-0.5">
        <ViewerBtn onClick={() => zoomOut(0.3)} title="Diminuir"><ZoomOut /></ViewerBtn>
        <span className="text-[10px] font-semibold text-white/50 tabular-nums min-w-[32px] text-center">
          {zoomPct}%
        </span>
        <ViewerBtn onClick={() => zoomIn(0.3)} title="Aumentar"><ZoomIn /></ViewerBtn>
        <ViewerBtn onClick={() => resetTransform()} title="Ajustar"><Maximize2 /></ViewerBtn>
        <div className="w-px h-3 bg-white/10 mx-0.5" />
        <ViewerBtn onClick={onRotateLeft} title="Girar Esquerda"><RotateCcw /></ViewerBtn>
        <ViewerBtn onClick={onRotateRight} title="Girar Direita"><RotateCw /></ViewerBtn>
      </div>
    </div>
  );
}

function ViewerBtn({
  onClick,
  title,
  children,
}: {
  onClick: () => void;
  title: string;
  children: React.ReactNode;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      title={title}
      className="flex items-center justify-center size-6 rounded text-white/50 hover:text-white hover:bg-white/10 transition-colors [&>svg]:size-3"
    >
      {children}
    </button>
  );
}

// ── Split Save Button ────────────────────────────────────────────────────────

function SplitSaveButton({
  saving, disabled, disabledReason, onSave, onSaveAndClone, onSaveAndClear,
}: {
  saving: boolean;
  disabled: boolean;
  disabledReason: string | null;
  onSave: () => void;
  onSaveAndClone: () => void;
  onSaveAndClear: () => void;
}) {
  return (
    <Tooltip>
      <TooltipTrigger asChild>
        {/* span necessário para o tooltip funcionar mesmo com botão disabled */}
        <span tabIndex={disabledReason ? 0 : undefined} className="inline-flex">
          <ButtonGroup>
            <Button
              className="bg-blue-600 hover:bg-blue-700 text-white border-0 rounded-e-none"
              onClick={onSave}
              disabled={disabled}
            >
              {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
              {saving ? 'Salvando…' : 'Salvar'}
            </Button>
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button
                  className="bg-blue-600 hover:bg-blue-700 text-white border-0 border-l border-l-blue-500 px-2 rounded-s-none"
                  disabled={disabled}
                >
                  <ChevronUp className="size-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" side="top" className="min-w-[180px]">
                <DropdownMenuItem onClick={onSaveAndClone}>
                  <Copy className="size-4" />
                  Salvar e Clonar
                </DropdownMenuItem>
                <DropdownMenuItem onClick={onSaveAndClear}>
                  <Eraser className="size-4" />
                  Salvar e Limpar
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </ButtonGroup>
        </span>
      </TooltipTrigger>
      {disabledReason && (
        <TooltipContent side="top" className="max-w-[260px] text-center">
          {disabledReason}
        </TooltipContent>
      )}
    </Tooltip>
  );
}

// ── Componente principal ─────────────────────────────────────────────────────

export function LancamentoDrawer({ open, tipo, onClose, onSaved, editId, prefill, documentPreview }: LancamentoDrawerProps) {
  const { data, loading } = useFormSelectData(tipo);
  const { csrfToken } = useAppData();

  const [stagedAnexos, setStagedAnexos] = useState<LancamentoStagedAnexo[]>([]);
  const stagedAnexosRef = useRef<LancamentoStagedAnexo[]>([]);
  stagedAnexosRef.current = stagedAnexos;

  useEffect(() => {
    if (!open) setStagedAnexos([]);
  }, [open]);

  const [parceiroSheetOpen, setParceiroSheetOpen] = useState(false);
  const [extraParceiros, setExtraParceiros] = useState<ParceiroOption[]>([]);

  const parceirosForSelect = useMemo(() => {
    const wantNatureza = tipo === 'receita' ? 'cliente' : 'fornecedor';
    const map = new Map<string, ParceiroOption>();
    for (const p of data.parceiros) map.set(p.id, p);
    for (const p of extraParceiros) {
      if (p.natureza === wantNatureza || p.natureza === 'ambos') map.set(p.id, p);
    }
    return Array.from(map.values());
  }, [data.parceiros, extraParceiros, tipo]);

  const {
    form, setField, fieldErrors,
    saving, loadingEdit, isEdit,
    handleSave, showRecebidoPago,
    sugState, confiancaCampos, markManualEdit,
    addRateio, removeRateio, setRateioField, distributeRateio,
    setParcelaField, setParcelaValor,
    existingAnexos,
    deleteExistingAnexo,
  } = useLancamentoForm({
    open,
    tipo,
    editId,
    prefill,
    parceirosForMatch: parceirosForSelect,
    csrfToken,
    onSaved,
    onClose,
    stagedAnexosRef,
    onClearStagedAnexos: () => setStagedAnexos([]),
  });
  const [rateioAtivo, setRateioAtivo] = useState(false);
  const [gerarNasFiliais, setGerarNasFiliais] = useState(true);
  const isReceita = tipo === 'receita';

  /**
   * Confirmação antes de salvar quando há dados destrutivos (parcelas ou recorrência).
   * `confirmType` determina qual mensagem é exibida.
   */
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [confirmType, setConfirmType] = useState<'parcelas' | 'recorrencia'>('parcelas');
  const [pendingSaveMode, setPendingSaveMode] = useState<'close' | 'clear' | 'clone'>('close');

  function requestSave(mode: 'close' | 'clear' | 'clone') {
    if (isEdit && parcelasCardVisivel) {
      setPendingSaveMode(mode);
      setConfirmType('parcelas');
      setConfirmOpen(true);
    } else if (isEdit && form.repetir) {
      setPendingSaveMode(mode);
      setConfirmType('recorrencia');
      setConfirmOpen(true);
    } else {
      handleSave(mode);
    }
  }

  /** Igual ao modal Blade: 1x + em aberto + sem recorrência — não depende de valor/descrição. */
  const previsaoPagamentoVisivel =
    !form.repetir && form.parcelamento === '1x' && !form.recebidoPago;

  const previsaoPagamentoAnchorRef = useRef<HTMLDivElement | null>(null);
  const previsaoPagamentoEraVisivelRef = useRef(false);

  useLayoutEffect(() => {
    if (!open) {
      previsaoPagamentoEraVisivelRef.current = false;
      return;
    }
    const agora = previsaoPagamentoVisivel;
    const antes = previsaoPagamentoEraVisivelRef.current;
    previsaoPagamentoEraVisivelRef.current = agora;
    if (agora && !antes && previsaoPagamentoAnchorRef.current) {
      previsaoPagamentoAnchorRef.current.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }, [open, previsaoPagamentoVisivel]);

  const rateioInvalido = useMemo(() => {
    if (!rateioAtivo || form.rateios.length === 0) return false;
    const valorTotal = parseCurrency(form.valor);
    const somaRateios = form.rateios.reduce((acc, r) => acc + parseCurrency(r.valor), 0);
    return Math.abs(somaRateios - valorTotal) > 0.01;
  }, [rateioAtivo, form.rateios, form.valor]);

  /** Mostrar assim que for 2x+ (sem exigir valor/descrição primeiro — o Blade escondia até preencher, o que parecia “não funcionar”). */
  const parcelasCardVisivel =
    !form.repetir && parcelamentoQuantidadeParcelas(form.parcelamento) !== null;

  const parcelasInvalido = useMemo(() => {
    const n = parcelamentoQuantidadeParcelas(form.parcelamento);
    if (form.repetir || !n) return false;
    const total = parseCurrency(form.valor);
    if (form.parcelasLinhas.length !== n) return true;
    const soma = form.parcelasLinhas.reduce((acc, l) => acc + parseCurrency(l.valor), 0);
    return Math.abs(soma - total) > 0.02;
  }, [form.repetir, form.parcelamento, form.parcelasLinhas, form.valor]);

  /** Motivo pelo qual o botão de salvar está desabilitado. `null` = pode salvar. */
  const saveDisabledReason = useMemo<string | null>(() => {
    if (saving) return 'Aguarde, salvando…';
    if (loadingEdit) return 'Carregando dados do lançamento…';
    if (rateioInvalido) {
      const valorTotal = parseCurrency(form.valor);
      const somaRateios = form.rateios.reduce((acc, r) => acc + parseCurrency(r.valor), 0);
      const diff = Math.abs(somaRateios - valorTotal).toFixed(2).replace('.', ',');
      return `Rateio com diferença de R$ ${diff} em relação ao valor total.`;
    }
    if (parcelasInvalido) {
      const n = parcelamentoQuantidadeParcelas(form.parcelamento);
      const total = parseCurrency(form.valor);
      const soma = form.parcelasLinhas.reduce((acc, l) => acc + parseCurrency(l.valor), 0);
      if (form.parcelasLinhas.length !== n) {
        return `Aguarde as parcelas serem montadas (${form.parcelasLinhas.length} de ${n}).`;
      }
      const diff = Math.abs(soma - total).toFixed(2).replace('.', ',');
      return `A soma das parcelas difere do valor total em R$ ${diff}.`;
    }
    return null;
  }, [saving, loadingEdit, rateioInvalido, parcelasInvalido, form.valor, form.rateios, form.parcelasLinhas, form.parcelamento]);

  const parceiroSearchOptions = useMemo<SearchSelectOption[]>(() => {
    const base = parceirosForSelect.map((p) => ({
      value: String(p.id),
      label: p.nome,
    }));
    if (form.fornecedor === '__novo__' && form.novoParceiroNome) {
      return [
        {
          value: '__novo__',
          label: `${form.novoParceiroNome} (Novo)`,
          hint: 'Será cadastrado ao salvar',
        },
        ...base,
      ];
    }
    return base;
  }, [parceirosForSelect, form.fornecedor, form.novoParceiroNome]);

  // Atalho Ctrl+S / Clg+S
  useEffect(() => {
    if (!open) return;
    function onKeyDown(e: KeyboardEvent) {
      if ((e.metaKey || e.ctrlKey) && e.key === 's') {
        e.preventDefault();
        handleSave('close');
      }
    }
    window.addEventListener('keydown', onKeyDown);
    return () => window.removeEventListener('keydown', onKeyDown);
  }, [open, handleSave]);

  return (
    <>
      <Sheet open={open} onOpenChange={(v) => !v && onClose()}>
        <SheetContent
          side="right"
          close={false}
          className="sm:max-w-none inset-3 start-auto w-auto rounded-xl border p-0 gap-0 flex flex-col"
          style={{ width: 'calc(100vw - 1.5rem)' }}
          aria-describedby={undefined}
        >
          <SheetHeader className="flex flex-row items-center justify-between px-5 py-3.5 border-b border-border shrink-0 space-y-0">
            <div className="flex items-center gap-2">
              {tipo && ICON[tipo]}
              <SheetTitle className="text-base font-semibold">
                {tipo ? (isEdit ? TITULO_EDIT[tipo] : TITULO[tipo]) : ''}
              </SheetTitle>
              {loadingEdit && <Loader2 className="size-4 animate-spin text-muted-foreground" />}
            </div>
            <SheetClose asChild>
              <Button variant="ghost" size="icon" onClick={onClose} aria-label="Fechar" className="size-8">
                <X className="size-4" />
              </Button>
            </SheetClose>
          </SheetHeader>

          <SheetBody className="p-0 flex-1 overflow-hidden bg-muted">
            <div className="flex h-full">
              {/* Viewer do documento (lado esquerdo) */}
              {documentPreview && (
                <DrawerDocumentPanel preview={documentPreview} />
              )}

              {/* Formulário (lado direito) */}
              <div className={documentPreview ? 'flex-1 min-w-0' : 'w-full'}>
                <ScrollArea className="h-full">
                  <div className="p-5 space-y-4">
                    <LancamentoInfoCard
                      form={form}
                      setField={setField}
                      fieldErrors={fieldErrors}
                      data={data}
                      loading={loading}
                      tipo={tipo}
                      parceiroOptions={parceiroSearchOptions}
                      onAddParceiro={() => setParceiroSheetOpen(true)}
                      sugState={sugState}
                      confiancaCampos={confiancaCampos}
                      markManualEdit={markManualEdit}
                      rateioAtivo={rateioAtivo}
                      onRateioChange={(v) => {
                        setRateioAtivo(v);
                        if (!v) setField('rateios', []);
                      }}
                    />

                    {rateioAtivo && (
                      <LancamentoRateioCard
                        form={form}
                        data={data}
                        fieldErrors={fieldErrors}
                        addRateio={addRateio}
                        removeRateio={removeRateio}
                        setRateioField={setRateioField}
                        distributeRateio={distributeRateio}
                        gerarNasFiliais={gerarNasFiliais}
                        onGerarNasFiliaisChange={setGerarNasFiliais}
                      />
                    )}

                    <LancamentoCondicaoCard
                      form={form}
                      setField={setField}
                      tipo={tipo}
                      fieldErrors={fieldErrors}
                      showRecebidoPago={showRecebidoPago}
                      csrfToken={csrfToken}
                    />

                    {parcelasCardVisivel && (
                      <LancamentoParcelasCard
                        form={form}
                        setParcelaField={setParcelaField}
                        setParcelaValor={setParcelaValor}
                        fieldErrors={fieldErrors}
                      />
                    )}

                    {previsaoPagamentoVisivel && (
                      <div ref={previsaoPagamentoAnchorRef} className="scroll-mt-4">
                        <LancamentoPrevisaoPagamentoCard
                          form={form}
                          setField={setField}
                          fieldErrors={fieldErrors}
                          tipo={tipo}
                        />
                      </div>
                    )}

                    <LancamentoHistoricoCard
                      form={form}
                      setField={setField}
                      stagedAnexos={stagedAnexos}
                      onStagedAnexosChange={setStagedAnexos}
                      existingAnexos={existingAnexos}
                      onDeleteExistingAnexo={deleteExistingAnexo}
                      disabled={saving || loadingEdit}
                    />
                  </div>
                </ScrollArea>
              </div>
            </div>
          </SheetBody>

          <SheetFooter className="flex-row justify-between border-t border-border px-5 py-3.5 shrink-0 gap-2 sm:space-x-0">
            <Button variant="ghost" onClick={onClose} disabled={saving}>
              Cancelar
            </Button>
            {isEdit ? (
              <Tooltip>
                <TooltipTrigger asChild>
                  <span tabIndex={saveDisabledReason ? 0 : undefined} className="inline-flex">
                    <Button
                      className="bg-blue-600 hover:bg-blue-700 text-white border-0"
                      onClick={() => requestSave('close')}
                      disabled={!!saveDisabledReason}
                    >
                      {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
                      {saving ? 'Salvando…' : 'Atualizar'}
                    </Button>
                  </span>
                </TooltipTrigger>
                {saveDisabledReason && (
                  <TooltipContent side="top" className="max-w-[260px] text-center">
                    {saveDisabledReason}
                  </TooltipContent>
                )}
              </Tooltip>
            ) : (
              <SplitSaveButton
                saving={saving}
                disabled={!!saveDisabledReason}
                disabledReason={saveDisabledReason}
                onSave={() => requestSave('close')}
                onSaveAndClone={() => requestSave('clone')}
                onSaveAndClear={() => requestSave('clear')}
              />
            )}
          </SheetFooter>
        </SheetContent>
      </Sheet>

      {/* ── Confirmação de substituição de parcelas ────────────────────────── */}
      <AlertDialog open={confirmOpen && confirmType === 'parcelas'} onOpenChange={setConfirmOpen}>
        <AlertDialogContent className="max-w-lg">
          <AlertDialogHeader>
            <AlertDialogTitle>Substituir parcelas existentes?</AlertDialogTitle>
            <AlertDialogDescription className="space-y-2">
              <span className="block">
                Ao confirmar, <strong>todas as parcelas existentes serão excluídas</strong> e
                recriadas com os dados informados no formulário.
              </span>
              <span className="block text-destructive font-medium">
                Esta ação não pode ser desfeita.
              </span>
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              className="bg-destructive hover:bg-destructive/90 text-white"
              onClick={() => handleSave(pendingSaveMode)}
            >
              Sim, substituir parcelas
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* ── Confirmação de atualização de recorrência ─────────────────────── */}
      <Dialog open={confirmOpen && confirmType === 'recorrencia'} onOpenChange={setConfirmOpen}>
        <DialogContent className="sm:max-w-sm !top-[15%] !translate-y-0">
          <DialogHeader>
            <DialogTitle>Atualizar recorrência?</DialogTitle>
            <DialogDescription>
              Lançamentos em aberto poderão ser <strong>excluídos</strong> conforme a nova configuração.
              <span className="block text-green-600 mt-1">Pagos/recebidos serão mantidos.</span>
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <DialogClose asChild>
              <Button variant="outline">Cancelar</Button>
            </DialogClose>
            <Button
              className="bg-destructive hover:bg-destructive/90 text-white"
              onClick={() => { setConfirmOpen(false); handleSave(pendingSaveMode); }}
            >
              Sim, atualizar
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <ParceiroQuickCreateSheet
        open={parceiroSheetOpen}
        onOpenChange={setParceiroSheetOpen}
        context={isReceita ? 'receita' : 'despesa'}
        onCreated={(p) => {
          setExtraParceiros((prev) => [...prev, { id: p.id, nome: p.nome, natureza: p.natureza }]);
          setField('fornecedor', p.id);
        }}
      />
    </>
  );
}

export type { LancamentoSaveResult } from './useLancamentoForm';
