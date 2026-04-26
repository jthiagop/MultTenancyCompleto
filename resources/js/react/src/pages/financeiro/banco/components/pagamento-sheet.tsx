import { useCallback, useEffect, useMemo, useState, ReactNode } from 'react';
import { notify } from '@/lib/notify';
import { useAppData } from '@/hooks/useAppData';
import { useFormSelectData } from '@/hooks/useFormSelectData';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { DatePicker } from '@/components/ui/date-picker';
import { CurrencyInput } from '@/components/common/masked-input';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SearchSelect } from '@/components/common/search-select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Loader2, Pencil, History } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { cn } from '@/lib/utils';

// ── Tipos ──────────────────────────────────────────────────────────────────────

interface PagamentoHistorico {
  id: number;
  valor: number;
  juros: number;
  multa: number;
  desconto: number;
  valor_total: number;
  data_pagamento: string | null;
  forma_pagamento: string;
  conta_pagamento: string;
}

interface TransacaoDetalhe {
  id: number;
  tipo: 'receita' | 'despesa';
  descricao: string;
  valor: number;
  valor_restante: number;
  data_competencia: string | null;
  data_vencimento: string | null;
  entidade_id: string;
  entidade_nome: string | null;
  parceiro_id: string | null;
  parceiro_nome: string | null;
  lancamento_padrao_id: string | null;
  categoria_nome: string | null;
  cost_center_id: string | null;
  centro_custo_nome: string | null;
  numero_documento: string;
  parcelamento: string | null;
  recorrencia_id: string | null;
  situacao: string;
  pagamentos?: PagamentoHistorico[];
}

type EditableField =
  | 'categoria'
  | 'centro_custo'
  | 'descricao'
  | 'parceiro'
  | 'data_competencia'
  | 'data_vencimento'
  | 'numero_documento';

interface EditDialogState {
  field: EditableField;
  initialValue: string;
}

interface PagamentoSheetProps {
  open: boolean;
  transacaoId: string | null;
  onClose: () => void;
  onSaved?: () => void;
}

// ── Helpers ────────────────────────────────────────────────────────────────────

function todayIso(): string {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

function parseCurrency(v: string): number {
  if (!v) return 0;
  return parseFloat(v.replace(/\./g, '').replace(',', '.')) || 0;
}

function fmtBrl(n: number): string {
  return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fmtDate(iso: string | null): string {
  if (!iso) return '—';
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
}

// ── Componente ─────────────────────────────────────────────────────────────────

export function PagamentoSheet({ open, transacaoId, onClose, onSaved }: PagamentoSheetProps) {
  const { csrfToken } = useAppData();

  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [transacao, setTransacao] = useState<TransacaoDetalhe | null>(null);

  // Form state
  const [dataPagamento, setDataPagamento] = useState(todayIso());
  const [valorPago, setValorPago] = useState('');
  const [juros, setJuros] = useState('0,00');
  const [multa, setMulta] = useState('0,00');
  const [desconto, setDesconto] = useState('0,00');
  const [formaPagamento, setFormaPagamento] = useState('');
  const [contaPagamentoId, setContaPagamentoId] = useState('');

  // Dados de select
  const { data: selectData } = useFormSelectData(transacao?.tipo ?? 'receita');
  const entidadeOpts = useMemo(
    () => selectData.entidades.map((e) => ({ value: e.id, label: e.label })),
    [selectData.entidades],
  );
  const formaOpts = useMemo(
    () => selectData.formasPagamento.map((f) => ({ value: f.id, label: f.nome })),
    [selectData.formasPagamento],
  );
  const categoriaOpts = useMemo(
    () =>
      selectData.categorias.map((c) => ({
        value: c.id,
        label: c.codigo ? `${c.codigo} — ${c.description}` : c.description,
      })),
    [selectData.categorias],
  );
  const centroCustoOpts = useMemo(
    () =>
      selectData.centrosCusto.map((cc) => ({
        value: cc.id,
        label: cc.code ? `${cc.code} — ${cc.name}` : cc.name,
      })),
    [selectData.centrosCusto],
  );
  const parceiroOpts = useMemo(
    () => selectData.parceiros.map((p) => ({ value: p.id, label: p.nome })),
    [selectData.parceiros],
  );

  // Rótulo dinâmico (Cliente para receita, Fornecedor para despesa).
  const parceiroLabel = transacao?.tipo === 'receita' ? 'Cliente' : 'Fornecedor';

  // Cálculos
  const valorPagoNum = parseCurrency(valorPago);
  const jurosNum = parseCurrency(juros);
  const multaNum = parseCurrency(multa);
  const descontoNum = parseCurrency(desconto);
  const totalPago = Math.max(0, valorPagoNum + jurosNum + multaNum - descontoNum);
  const valorParcela = transacao ? (transacao.valor_restante ?? transacao.valor) : 0;
  const valorEmAberto = Math.max(0, valorParcela - valorPagoNum - jurosNum - multaNum);
  const valorExcedido = valorPagoNum > valorParcela + 0.01;

  // Carrega dados da transação ao abrir
  const loadTransacao = useCallback(async () => {
    if (!transacaoId) return;
    setLoading(true);
    try {
      const res = await fetch(`/app/financeiro/banco/lancamento/${transacaoId}`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('Falha ao carregar');
      const d = await res.json();
      setTransacao(d);

      const vr = d.valor_restante ?? d.valor;
      setValorPago(fmtBrl(vr));
      setDataPagamento(todayIso());
      setJuros('0,00');
      setMulta('0,00');
      setDesconto('0,00');
      setFormaPagamento('');
      setContaPagamentoId(d.entidade_id || '');
    } catch {
      notify.error('Erro', 'Não foi possível carregar os dados do lançamento.');
      onClose();
    } finally {
      setLoading(false);
    }
  }, [transacaoId, onClose]);

  useEffect(() => {
    if (open && transacaoId) {
      loadTransacao();
    }
    if (!open) {
      setTransacao(null);
    }
  }, [open, transacaoId, loadTransacao]);

  // ── Edição inline (Pencil): Categoria · Centro de custo · Descrição ────
  const [editing, setEditing] = useState<EditDialogState | null>(null);
  const [savingEdit, setSavingEdit] = useState(false);

  const openEdit = useCallback((field: EditableField, initialValue: string | null | undefined) => {
    setEditing({ field, initialValue: initialValue ?? '' });
  }, []);

  const closeEdit = useCallback(() => {
    if (savingEdit) return;
    setEditing(null);
  }, [savingEdit]);

  const persistEdit = useCallback(
    async (field: EditableField, value: string) => {
      if (!transacaoId) return;
      const payload: Record<string, unknown> = {};
      switch (field) {
        case 'descricao':
          payload.descricao = value.trim();
          break;
        case 'categoria':
          payload.lancamento_padrao_id = value ? parseInt(value, 10) : null;
          break;
        case 'centro_custo':
          payload.cost_center_id = value ? parseInt(value, 10) : null;
          break;
        case 'parceiro':
          payload.parceiro_id = value ? parseInt(value, 10) : null;
          break;
        case 'data_competencia':
          if (!value) {
            notify.error('Data inválida', 'Informe uma data de competência.');
            return;
          }
          payload.data_competencia = value;
          break;
        case 'data_vencimento':
          payload.data_vencimento = value || null;
          break;
        case 'numero_documento':
          payload.numero_documento = value.trim() || null;
          break;
      }

      setSavingEdit(true);
      try {
        const res = await fetch(`/app/financeiro/banco/lancamento/${transacaoId}/quick-update`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken ?? '',
          },
          credentials: 'same-origin',
          body: JSON.stringify(payload),
        });
        const json = await res.json().catch(() => ({}));

        if (!res.ok) {
          if (res.status === 422 && json.errors) {
            const msgs = Object.values(json.errors as Record<string, string[]>).flat();
            notify.error('Dados inválidos', msgs.join('\n'));
          } else {
            notify.error('Erro', json.message ?? 'Não foi possível atualizar o lançamento.');
          }
          return;
        }

        notify.success('Atualizado!', json.message ?? 'Lançamento atualizado com sucesso.');
        setEditing(null);
        await loadTransacao();
      } catch {
        notify.error('Erro de rede', 'Verifique sua conexão e tente novamente.');
      } finally {
        setSavingEdit(false);
      }
    },
    [transacaoId, csrfToken, loadTransacao],
  );

  // Salvar pagamento
  async function handleConfirmar() {
    if (!transacaoId) return;
    setSaving(true);
    try {
      const payload = {
        valor_pago: parseCurrency(valorPago),
        data_pagamento: dataPagamento,
        juros: parseCurrency(juros),
        multa: parseCurrency(multa),
        desconto: parseCurrency(desconto),
        forma_pagamento: formaPagamento,
        conta_pagamento_id: contaPagamentoId ? parseInt(contaPagamentoId, 10) : null,
      };

      const res = await fetch(`/app/financeiro/banco/lancamento/${transacaoId}/pagamento`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken ?? '',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });

      const json = await res.json();

      if (!res.ok) {
        if (res.status === 422 && json.errors) {
          const msgs = Object.values(json.errors as Record<string, string[]>).flat();
          notify.error('Dados inválidos', msgs.join('\n'));
        } else {
          notify.error('Erro', json.message ?? 'Não foi possível registrar o pagamento.');
        }
        return;
      }

      notify.success('Pagamento registrado!', json.message ?? 'O pagamento foi registrado com sucesso.');
      onSaved?.();
      onClose();
    } catch {
      notify.error('Erro de rede', 'Verifique sua conexão e tente novamente.');
    } finally {
      setSaving(false);
    }
  }

  const isReceita = transacao?.tipo === 'receita';

  return (
    <>
    <Sheet open={open} onOpenChange={(v) => !v && onClose()}>
      <SheetContent
        side="right"
        className="flex flex-col p-0 gap-0 border-l sm:max-w-none w-full"
        aria-describedby={undefined}
        onOpenAutoFocus={(e) => e.preventDefault()}
        onInteractOutside={(e) => e.preventDefault()}
      >
        {/* Header */}
        <SheetHeader className="flex flex-row items-center justify-between px-5 py-3.5 border-b border-border shrink-0 space-y-0">
          <SheetTitle className="text-base font-semibold">Informar pagamento</SheetTitle>
        </SheetHeader>

        {/* Body */}
        <SheetBody className="p-0 flex-1 overflow-hidden bg-muted">
          <ScrollArea className="h-full">
            <div className="p-5 space-y-5">
              {loading ? (
                <div className="flex items-center justify-center py-20">
                  <Loader2 className="size-6 animate-spin text-muted-foreground" />
                </div>
              ) : transacao ? (
                <>
                  {/* ── Informações do lançamento (readonly + edição rápida) ─ */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-[38px] bg-accent/50">
                      <CardTitle className="text-2sm">Informações do lançamento</CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                      <div className="grid grid-cols-3 gap-x-6 gap-y-4 text-sm">
                        <InfoField
                          label={parceiroLabel}
                          value={transacao.parceiro_nome}
                          editable
                          onEdit={() => openEdit('parceiro', transacao.parceiro_id)}
                        />
                        <InfoField
                          label="Cód. referência"
                          value={transacao.numero_documento}
                          editable
                          onEdit={() => openEdit('numero_documento', transacao.numero_documento)}
                        />
                        <InfoField
                          label="Data de competência"
                          value={fmtDate(transacao.data_competencia)}
                          editable
                          onEdit={() => openEdit('data_competencia', transacao.data_competencia)}
                        />
                        <InfoField
                          label="Categoria"
                          value={transacao.categoria_nome}
                          editable
                          onEdit={() => openEdit('categoria', transacao.lancamento_padrao_id)}
                        />
                        <InfoField
                          label="Centro de custo"
                          value={transacao.centro_custo_nome}
                          editable
                          onEdit={() => openEdit('centro_custo', transacao.cost_center_id)}
                        />
                        <InfoField
                          label="Recorrente"
                          value={transacao.recorrencia_id ? 'Sim' : 'Não'}
                        />
                      </div>

                      <div className="border-t border-border mt-4 pt-4 grid grid-cols-4 gap-x-6 text-sm">
                        <InfoField
                          label="Vencimento"
                          value={fmtDate(transacao.data_vencimento)}
                          editable
                          onEdit={() => openEdit('data_vencimento', transacao.data_vencimento)}
                        />
                        <InfoField label="Parcela" value={transacao.parcelamento || 'À vista'} />
                        <InfoField
                          label="Descrição"
                          value={transacao.descricao}
                          editable
                          onEdit={() => openEdit('descricao', transacao.descricao)}
                        />
                        <div className="text-right">
                          <span className="text-muted-foreground block text-sm">Valor total</span>
                          <span className="text-xl font-bold">R$ {fmtBrl(transacao.valor)}</span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>

                  {/* ── Histórico de pagamentos (se houver) ────────────── */}
                  {transacao.pagamentos && transacao.pagamentos.length > 0 && (
                    <Card className="rounded-md">
                      <CardHeader className="min-h-[38px] bg-accent/50">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <History className="size-3.5" />
                          Histórico de pagamentos
                          <Badge variant="secondary" className="ml-auto text-xs">
                            {transacao.pagamentos.length} pagamento{transacao.pagamentos.length > 1 ? 's' : ''}
                          </Badge>
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="p-0">
                        <table className="w-full text-xs">
                          <thead>
                            <tr className="border-b border-border bg-muted/40">
                              <th className="text-left px-4 py-2 font-medium text-muted-foreground">Data</th>
                              <th className="text-right px-4 py-2 font-medium text-muted-foreground">Valor</th>
                              <th className="text-right px-4 py-2 font-medium text-muted-foreground">Juros</th>
                              <th className="text-right px-4 py-2 font-medium text-muted-foreground">Multa</th>
                              <th className="text-right px-4 py-2 font-medium text-muted-foreground">Desconto</th>
                              <th className="text-right px-4 py-2 font-medium text-muted-foreground">Total</th>
                            </tr>
                          </thead>
                          <tbody>
                            {transacao.pagamentos.map((p) => (
                              <tr key={p.id} className="border-b border-border last:border-0">
                                <td className="px-4 py-2">{fmtDate(p.data_pagamento)}</td>
                                <td className="px-4 py-2 text-right">R$ {fmtBrl(p.valor)}</td>
                                <td className="px-4 py-2 text-right">{p.juros > 0 ? `R$ ${fmtBrl(p.juros)}` : '—'}</td>
                                <td className="px-4 py-2 text-right">{p.multa > 0 ? `R$ ${fmtBrl(p.multa)}` : '—'}</td>
                                <td className="px-4 py-2 text-right">{p.desconto > 0 ? `R$ ${fmtBrl(p.desconto)}` : '—'}</td>
                                <td className="px-4 py-2 text-right font-semibold">R$ {fmtBrl(p.valor_total)}</td>
                              </tr>
                            ))}
                          </tbody>
                          <tfoot>
                            <tr className="bg-muted/30">
                              <td className="px-4 py-2 font-semibold">Total pago</td>
                              <td colSpan={4} />
                              <td className="px-4 py-2 text-right font-bold">
                                R$ {fmtBrl(transacao.pagamentos.reduce((s, p) => s + p.valor_total, 0))}
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </CardContent>
                    </Card>
                  )}

                  {/* ── Informações do pagamento (editável) ─────────────── */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-[38px] bg-accent/50">
                      <CardTitle className="text-2sm">Informações do pagamento</CardTitle>
                    </CardHeader>
                    <CardContent className="p-4 space-y-4">
                      <p className="text-xs text-muted-foreground">
                        Você pode fazer o pagamento total ou parcial do saldo da parcela.
                        O valor restante ficará em aberto.
                      </p>

                      {/* Linha 1: Valor da parcela, Data, Forma, Conta */}
                      <div className="grid grid-cols-4 gap-3">
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">Valor da parcela</Label>
                          <div className="relative">
                            <span className="absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium">R$</span>
                            <input
                              readOnly
                              className="flex h-9 w-full rounded-md border border-input bg-muted px-3 pl-8 text-sm"
                              value={fmtBrl(valorParcela)}
                            />
                          </div>
                        </div>
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">
                            Data do pagamento <span className="text-destructive">*</span>
                          </Label>
                          <DatePicker value={dataPagamento} onChange={setDataPagamento} />
                        </div>
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">Forma de pagamento</Label>
                          <Select
                            value={formaPagamento}
                            onValueChange={setFormaPagamento}
                            searchable
                            searchPlaceholder="Buscar forma de pagamento..."
                          >
                            <SelectTrigger className="h-9 text-sm">
                              <SelectValue placeholder="Selecione..." />
                            </SelectTrigger>
                            <SelectContent>
                              {formaOpts.map((o) => (
                                <SelectItem key={o.value} value={o.value}>
                                  {o.label}
                                </SelectItem>
                              ))}
                            </SelectContent>
                          </Select>
                        </div>
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">
                            Conta <span className="text-destructive">*</span>
                          </Label>
                          <SearchSelect
                            options={entidadeOpts}
                            value={contaPagamentoId}
                            onValueChange={setContaPagamentoId}
                            placeholder="Selecione..."
                          />
                        </div>
                      </div>

                      {/* Linha 2: Valor pago, Juros, Multa, Desconto */}
                      <div className="grid grid-cols-4 gap-3">
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">
                            Valor pago <span className="text-destructive">*</span>
                          </Label>
                          <div className="relative">
                            <span className="absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium">R$</span>
                            <CurrencyInput
                              className="pl-8 h-9 text-sm"
                              value={valorPago}
                              onMaskedChange={setValorPago}
                            />
                          </div>
                        </div>
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">
                            Juros <span className="text-destructive">*</span>
                          </Label>
                          <div className="relative">
                            <span className="absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium">R$</span>
                            <CurrencyInput
                              className="pl-8 h-9 text-sm"
                              value={juros}
                              onMaskedChange={setJuros}
                            />
                          </div>
                        </div>
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">
                            Multa <span className="text-destructive">*</span>
                          </Label>
                          <div className="relative">
                            <span className="absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium">R$</span>
                            <CurrencyInput
                              className="pl-8 h-9 text-sm"
                              value={multa}
                              onMaskedChange={setMulta}
                            />
                          </div>
                        </div>
                        <div className="flex flex-col gap-2">
                          <Label className="text-sm">
                            Desconto <span className="text-destructive">*</span>
                          </Label>
                          <div className="relative">
                            <span className="absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium">R$</span>
                            <CurrencyInput
                              className="pl-8 h-9 text-sm"
                              value={desconto}
                              onMaskedChange={setDesconto}
                            />
                          </div>
                        </div>
                      </div>

                      {/* Validação */}
                      {valorExcedido && (
                        <p className="text-sm text-destructive font-medium">
                          O valor pago não pode ser maior que o valor da parcela (R$ {fmtBrl(valorParcela)}).
                        </p>
                      )}

                      {/* Totais */}
                      <div className="flex items-end justify-end gap-8 pt-3 border-t border-border">
                        {valorEmAberto > 0.01 && !valorExcedido && (
                          <div className="text-right">
                            <span className="text-xs text-muted-foreground">Valor em Aberto</span>
                            <p className="text-xl font-bold text-amber-600">R$ {fmtBrl(valorEmAberto)}</p>
                          </div>
                        )}
                        <div className="text-right">
                          <span className="text-xs text-muted-foreground">Total Pago</span>
                          <p className="text-xl font-bold">R$ {fmtBrl(totalPago)}</p>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                </>
              ) : null}
            </div>
          </ScrollArea>
        </SheetBody>

        {/* Footer */}
        <SheetFooter className="flex flex-row items-center justify-between px-5 py-3 border-t border-border shrink-0 bg-background">
          <Button variant="outline" onClick={onClose}>
            Cancelar
          </Button>
          <Button
            className={isReceita ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white'}
            disabled={saving || loading || !transacao || valorExcedido || valorPagoNum <= 0}
            onClick={handleConfirmar}
          >
            {saving && <Loader2 className="size-4 animate-spin mr-1.5" />}
            Confirmar pagamento
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>

    <EditValueDialog
      state={editing}
      saving={savingEdit}
      onClose={closeEdit}
      onSave={persistEdit}
      categoriaOpts={categoriaOpts}
      centroCustoOpts={centroCustoOpts}
      parceiroOpts={parceiroOpts}
      parceiroLabel={parceiroLabel}
    />
    </>
  );
}

// ── Componentes auxiliares ─────────────────────────────────────────────────────

interface InfoFieldProps {
  label: string;
  value: ReactNode;
  editable?: boolean;
  onEdit?: () => void;
}

function InfoField({ label, value, editable, onEdit }: InfoFieldProps) {
  const display = value || '—';
  return (
    <div>
      <span className="text-muted-foreground block text-sm">{label}</span>
      {editable ? (
        <button
          type="button"
          onClick={onEdit}
          className={cn(
            'group inline-flex items-center gap-1.5 font-medium text-sm text-left',
            'hover:text-blue-600 transition-colors',
          )}
        >
          <span className="truncate">{display}</span>
          <Pencil className="size-3.5 text-blue-600 shrink-0" />
        </button>
      ) : (
        <span className="font-medium text-sm">{display}</span>
      )}
    </div>
  );
}

interface EditValueDialogProps {
  state: EditDialogState | null;
  saving: boolean;
  onClose: () => void;
  onSave: (field: EditableField, value: string) => Promise<void> | void;
  categoriaOpts: { value: string; label: string }[];
  centroCustoOpts: { value: string; label: string }[];
  parceiroOpts: { value: string; label: string }[];
  parceiroLabel: string;
}

function EditValueDialog({
  state,
  saving,
  onClose,
  onSave,
  categoriaOpts,
  centroCustoOpts,
  parceiroOpts,
  parceiroLabel,
}: EditValueDialogProps) {
  const [draft, setDraft] = useState('');

  // Sincroniza o estado interno toda vez que o diálogo é aberto / muda de campo.
  useEffect(() => {
    setDraft(state?.initialValue ?? '');
  }, [state]);

  if (!state) {
    return (
      <Dialog open={false} onOpenChange={() => onClose()}>
        <DialogContent />
      </Dialog>
    );
  }

  const fieldMeta: Record<EditableField, { title: string; description: string }> = {
    categoria:        { title: 'Alterar categoria',         description: 'Selecione a nova categoria do lançamento.' },
    centro_custo:     { title: 'Alterar centro de custo',   description: 'Selecione o centro de custo do lançamento.' },
    descricao:        { title: 'Alterar descrição',         description: 'Edite a descrição livre do lançamento.' },
    parceiro:         { title: `Alterar ${parceiroLabel.toLowerCase()}`, description: `Selecione o ${parceiroLabel.toLowerCase()} do lançamento.` },
    data_competencia: { title: 'Alterar data de competência', description: 'Defina a nova data de competência do lançamento.' },
    data_vencimento:  { title: 'Alterar vencimento',         description: 'Defina o novo vencimento do lançamento.' },
    numero_documento: { title: 'Alterar cód. referência',    description: 'Edite o código de referência (nº documento).' },
  };

  const meta = fieldMeta[state.field];

  return (
    <Dialog
      open
      onOpenChange={(v) => {
        if (!v) onClose();
      }}
    >
      <DialogContent className="sm:max-w-md" aria-describedby={undefined}>
        <DialogHeader>
          <DialogTitle>{meta.title}</DialogTitle>
        </DialogHeader>

        <div className="px-5 py-4 space-y-3">
          <p className="text-xs text-muted-foreground">{meta.description}</p>

          {state.field === 'categoria' && (
            <div className="flex flex-col gap-2">
              <Label className="text-sm">Categoria</Label>
              <SearchSelect
                options={categoriaOpts}
                value={draft}
                onValueChange={setDraft}
                placeholder="Selecione uma categoria..."
              />
            </div>
          )}

          {state.field === 'centro_custo' && (
            <div className="flex flex-col gap-2">
              <Label className="text-sm">Centro de custo</Label>
              <SearchSelect
                options={centroCustoOpts}
                value={draft}
                onValueChange={setDraft}
                placeholder="Selecione um centro de custo..."
              />
            </div>
          )}

          {state.field === 'parceiro' && (
            <div className="flex flex-col gap-2">
              <Label className="text-sm">{parceiroLabel}</Label>
              <SearchSelect
                options={parceiroOpts}
                value={draft}
                onValueChange={setDraft}
                placeholder={`Selecione um ${parceiroLabel.toLowerCase()}...`}
              />
            </div>
          )}

          {state.field === 'descricao' && (
            <div className="flex flex-col gap-2">
              <Label className="text-sm">Descrição</Label>
              <Input
                value={draft}
                onChange={(e) => setDraft(e.target.value)}
                placeholder="Descrição do lançamento"
                maxLength={255}
                autoFocus
              />
            </div>
          )}

          {state.field === 'numero_documento' && (
            <div className="flex flex-col gap-2">
              <Label className="text-sm">Cód. referência</Label>
              <Input
                value={draft}
                onChange={(e) => setDraft(e.target.value)}
                placeholder="Nº do documento ou referência"
                maxLength={100}
                autoFocus
              />
            </div>
          )}

          {state.field === 'data_competencia' && (
            <div className="flex flex-col gap-2">
              <Label className="text-sm">
                Data de competência <span className="text-destructive">*</span>
              </Label>
              <DatePicker value={draft} onChange={(v) => setDraft(v ?? '')} />
            </div>
          )}

          {state.field === 'data_vencimento' && (
            <div className="flex flex-col gap-2">
              <Label className="text-sm">Vencimento</Label>
              <DatePicker value={draft} onChange={(v) => setDraft(v ?? '')} />
            </div>
          )}
        </div>

        <DialogFooter className="px-5 py-3 border-t border-border">
          <Button variant="outline" onClick={onClose} disabled={saving}>
            Cancelar
          </Button>
          <Button
            onClick={() => onSave(state.field, draft)}
            disabled={
              saving ||
              (state.field === 'descricao' && draft.trim().length === 0) ||
              (state.field === 'data_competencia' && !draft)
            }
            className="bg-blue-600 hover:bg-blue-700 text-white"
          >
            {saving && <Loader2 className="size-4 animate-spin mr-1.5" />}
            Salvar
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
