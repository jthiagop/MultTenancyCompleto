import { useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  ColumnDef,
  getCoreRowModel,
  getFilteredRowModel,
  getSortedRowModel,
  SortingState,
  useReactTable,
} from '@tanstack/react-table';
import {
  Banknote,
  Building2,
  ChevronDown,
  ChevronsUpDown,
  CircleAlert,
  Coins,
  DollarSign,
  ExternalLink,
  Loader2,
  Pencil,
  Plus,
  RefreshCw,
  Trash2,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import {
  Toolbar,
  ToolbarActions,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardFooter, CardTable } from '@/components/ui/card';
import { DataGrid } from '@/components/ui/data-grid';
import { DataGridColumnHeader } from '@/components/ui/data-grid-column-header';
import { DataGridTable } from '@/components/ui/data-grid-table';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
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
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Stepper,
  StepperContent,
  StepperIndicator,
  StepperItem,
  StepperNav,
  StepperPanel,
  StepperSeparator,
  StepperTitle,
  StepperTrigger,
} from '@/components/ui/stepper';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useAppData } from '@/hooks/useAppData';
import { useEntidades, type IEntidade } from '@/hooks/useEntidades';
import { CurrencyInput } from '@/components/common/masked-input';
import { notify } from '@/lib/notify';
import { toast } from 'sonner';
import { financeiroToolbarSoftBlueClass } from '@/lib/financeiro-toolbar-accent';
import { FinanceiroBreadcrumb } from '@/pages/financeiro/components/financeiro-breadcrumb';

// ── Tipos locais ──────────────────────────────────────────────────────────────

interface IBank {
  id: number;
  name: string;
  code: string | null;
  logo_url: string | null;
}

interface IContaContabil {
  id: number;
  code: string;
  name: string;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

const fmtCurrency = (v: number) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);

const STATUS_LABEL: Record<string, string> = {
  ok:          'Conciliado',
  pendente:    'Pendente',
  divergente:  'Divergente',
  parcial:     'Parcial',
  'em análise': 'Em análise',
  ajustado:    'Ajustado',
  ignorado:    'Ignorado',
};

const STATUS_VARIANT: Record<string, 'success' | 'warning' | 'destructive' | 'secondary'> = {
  ok:          'success',
  pendente:    'warning',
  divergente:  'destructive',
  parcial:     'warning',
  'em análise': 'secondary',
  ajustado:    'secondary',
  ignorado:    'secondary',
};

const ACCOUNT_TYPE_LABELS: Record<string, string> = {
  corrente:       'Conta Corrente',
  poupanca:       'Poupança',
  aplicacao:      'Aplicação',
  renda_fixa:     'Renda Fixa',
  tesouro_direto: 'Tesouro Direto',
};

// ── AtualizarSaldoDialog ──────────────────────────────────────────────────────

function AtualizarSaldoDialog({
  entidade,
  open,
  onOpenChange,
  onUpdated,
}: {
  entidade: IEntidade | null;
  open: boolean;
  onOpenChange: (v: boolean) => void;
  onUpdated: () => void;
}) {
  const { csrfToken } = useAppData();
  // novoSaldo armazena o valor formatado pt-BR ex: "1.234,56"
  const [novoSaldo, setNovoSaldo] = useState('');

  useEffect(() => {
    if (open && entidade) {
      const cents = Math.round(entidade.saldo_inicial * 100);
      if (cents > 0) {
        setNovoSaldo(
          (cents / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
        );
      } else {
        setNovoSaldo('');
      }
    }
  }, [open, entidade]);

  function handleClose() {
    onOpenChange(false);
    setNovoSaldo('');
  }

  async function handleSubmit() {
    if (!entidade || !csrfToken) throw new Error('Dados inválidos.');
    // Converte "1.234,56" → 1234.56
    const valor = parseFloat(novoSaldo.replace(/\./g, '').replace(',', '.'));
    if (isNaN(valor)) throw new Error('Informe um valor numérico válido.');

    const res = await fetch(`/relatorios/entidades/${entidade.id}/saldo-inicial`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
      body: JSON.stringify({ saldo_inicial: valor }),
    });
    const json = await res.json() as { success?: boolean; message?: string };
    if (!res.ok || !json.success) throw new Error(json.message ?? 'Erro ao atualizar.');
    return json;
  }

  function handleConfirm() {
    toast.promise(handleSubmit(), {
      loading: 'Atualizando saldo inicial...',
      success: () => {
        handleClose();
        onUpdated();
        return 'Saldo inicial atualizado e recalculado com sucesso!';
      },
      error: (err: Error) => err.message ?? 'Erro ao atualizar saldo.',
    });
  }

  const saldoAtual = entidade ? fmtCurrency(entidade.saldo_inicial) : '—';

  return (
    <AlertDialog open={open} onOpenChange={(v) => { if (!v) handleClose(); }}>
      <AlertDialogContent className="sm:max-w-sm">
        <AlertDialogHeader>
          <AlertDialogTitle>Atualizar Valor Inicial</AlertDialogTitle>
          <AlertDialogDescription asChild>
            <div className="flex flex-col gap-3 mt-1">
              <p className="text-sm text-muted-foreground">
                Entidade: <span className="font-medium text-foreground">{entidade?.nome}</span>
              </p>
              <div className="flex items-center justify-between rounded-md bg-muted px-3 py-2 text-sm">
                <span className="text-muted-foreground">Saldo inicial atual</span>
                <span className="font-semibold tabular-nums">{saldoAtual}</span>
              </div>
              <div className="flex flex-col gap-1.5">
                <Label>Novo saldo inicial <span className="text-destructive">*</span></Label>
                <div className="relative">
                  <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium pointer-events-none select-none">
                    R$
                  </span>
                  <CurrencyInput
                    className="pl-8"
                    value={novoSaldo}
                    onMaskedChange={(v) => setNovoSaldo(v)}
                    autoFocus
                  />
                </div>
                <p className="text-xs text-muted-foreground">
                  O saldo atual será recalculado automaticamente após a alteração.
                </p>
              </div>
            </div>
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel onClick={handleClose}>Cancelar</AlertDialogCancel>
          <AlertDialogAction
            onClick={handleConfirm}
            disabled={!novoSaldo.trim() || isNaN(parseFloat(novoSaldo.replace(/\./g, '').replace(',', '.')))}
          >
            Confirmar
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}

// ── NovaEntidadeDialog ────────────────────────────────────────────────────────

function NovaEntidadeDialog({
  open,
  onOpenChange,
  onCreated,
}: {
  open: boolean;
  onOpenChange: (v: boolean) => void;
  onCreated: () => void;
}) {
  const { csrfToken } = useAppData();
  const [step, setStep] = useState(1);
  const [banks, setBanks] = useState<IBank[]>([]);
  const [loadingBanks, setLoadingBanks] = useState(false);
  const [contasContabeis, setContasContabeis] = useState<IContaContabil[]>([]);
  const [loadingContas, setLoadingContas] = useState(false);
  const [saving, setSaving] = useState(false);

  // Campos do formulário
  const [tipo, setTipo] = useState<'banco' | 'caixa'>('banco');
  const [bankId, setBankId] = useState('');
  const [nomeBanco, setNomeBanco] = useState('');
  const [nome, setNome] = useState('');
  const [agencia, setAgencia] = useState('');
  const [conta, setConta] = useState('');
  const [accountType, setAccountType] = useState('corrente');
  const [saldoInicial, setSaldoInicial] = useState('');
  const [contaContabilId, setContaContabilId] = useState('');
  const [contaPopoverOpen, setContaPopoverOpen] = useState(false);
  const [descricao, setDescricao] = useState('');

  // Carrega bancos ao abrir
  useEffect(() => {
    if (!open) return;
    setLoadingBanks(true);
    fetch('/app/financeiro/banco/banks', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json() as Promise<{ data: IBank[] }>)
      .then((j) => setBanks(j.data ?? []))
      .catch(() => setBanks([]))
      .finally(() => setLoadingBanks(false));
  }, [open]);

  // Carrega plano de contas na etapa 3
  useEffect(() => {
    if (step !== 3 || contasContabeis.length > 0) return;
    setLoadingContas(true);
    fetch('/app/financeiro/banco/contas-contabeis', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json() as Promise<{ data: IContaContabil[] }>)
      .then((j) => setContasContabeis(j.data ?? []))
      .catch(() => setContasContabeis([]))
      .finally(() => setLoadingContas(false));
  }, [step, contasContabeis.length]);

  // Validação por etapa
  const step2Valid = useMemo(() => {
    if (tipo === 'banco') return !!(bankId && agencia.trim() && conta.trim() && accountType);
    return !!(nome.trim());
  }, [tipo, bankId, agencia, conta, accountType, nome]);

  const step3Valid = useMemo(() => {
    const v = parseFloat(saldoInicial.replace(',', '.'));
    return !isNaN(v);
  }, [saldoInicial]);

  function resetForm() {
    setStep(1);
    setTipo('banco');
    setBankId('');
    setNomeBanco('');
    setNome('');
    setAgencia('');
    setConta('');
    setAccountType('corrente');
    setSaldoInicial('');
    setContaContabilId('');
    setDescricao('');
  }

  function handleClose() {
    onOpenChange(false);
    resetForm();
  }

  async function handleSubmit() {
    if (!csrfToken) return;
    const saldoNum = parseFloat(saldoInicial.replace(',', '.'));
    if (isNaN(saldoNum)) {
      notify.error('Saldo inválido', 'Informe um valor numérico.');
      return;
    }

    const payload: Record<string, unknown> = {
      tipo,
      saldo_inicial: saldoNum,
      descricao: descricao || null,
      conta_contabil_id: contaContabilId ? Number(contaContabilId) : null,
    };

    if (tipo === 'banco') {
      payload.bank_id      = Number(bankId);
      payload.nome_banco   = nomeBanco || null;
      payload.agencia      = agencia;
      payload.conta        = conta;
      payload.account_type = accountType;
    } else {
      payload.nome = nome;
    }

    setSaving(true);
    try {
      const res = await fetch('/app/financeiro/banco/entidades/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro ao criar', json.message ?? '');
        return;
      }
      notify.success('Entidade criada!', json.message ?? '');
      handleClose();
      onCreated();
    } catch {
      notify.networkError();
    } finally {
      setSaving(false);
    }
  }

  const selectedBank = banks.find((b) => String(b.id) === bankId);

  return (
    <Dialog open={open} onOpenChange={(v) => { if (!v) handleClose(); }}>
      <DialogContent className="sm:max-w-2xl gap-0 p-0 overflow-hidden" showCloseButton={false}>
        <DialogHeader className="px-6 pt-6 pb-4 border-b border-border">
          <DialogTitle>Nova Entidade Financeira</DialogTitle>
          <DialogDescription>
            Cadastre um banco ou caixa para movimentações financeiras.
          </DialogDescription>
        </DialogHeader>

        <Stepper value={step} onValueChange={setStep} className="flex flex-col">
          {/* Navegador de etapas */}
          <StepperNav className="px-6 py-4 border-b border-border bg-muted/30">
            <StepperItem step={1} completed={step > 1}>
              <StepperTrigger asChild>
                <span className="flex items-center gap-2 cursor-default select-none">
                  <StepperIndicator>1</StepperIndicator>
                  <StepperTitle>Tipo</StepperTitle>
                </span>
              </StepperTrigger>
              <StepperSeparator />
            </StepperItem>
            <StepperItem step={2} completed={step > 2} disabled={step < 2}>
              <StepperTrigger asChild>
                <span className="flex items-center gap-2 cursor-default select-none">
                  <StepperIndicator>2</StepperIndicator>
                  <StepperTitle>Dados</StepperTitle>
                </span>
              </StepperTrigger>
              <StepperSeparator />
            </StepperItem>
            <StepperItem step={3} disabled={step < 3}>
              <StepperTrigger asChild>
                <span className="flex items-center gap-2 cursor-default select-none">
                  <StepperIndicator>3</StepperIndicator>
                  <StepperTitle>Saldo</StepperTitle>
                </span>
              </StepperTrigger>
            </StepperItem>
          </StepperNav>

          {/* Conteúdo das etapas */}
          <StepperPanel className="px-6 py-6 min-h-65">

            {/* Etapa 1 — Tipo */}
            <StepperContent value={1}>
              <div className="grid grid-cols-2 gap-4">
                <button
                  type="button"
                  onClick={() => setTipo('banco')}
                  className={cn(
                    'flex flex-col items-start gap-3 p-5 rounded-lg border-2 text-left transition-colors',
                    tipo === 'banco'
                      ? 'border-primary bg-primary/5'
                      : 'border-border hover:border-muted-foreground/40 hover:bg-muted/30',
                  )}
                >
                  <Building2 className="size-8 text-primary" />
                  <div>
                    <p className="font-semibold text-sm">Banco</p>
                    <p className="text-xs text-muted-foreground mt-1">
                      Conecte sua conta bancária para manter o fluxo de caixa sempre conciliado.
                    </p>
                  </div>
                </button>
                <button
                  type="button"
                  onClick={() => setTipo('caixa')}
                  className={cn(
                    'flex flex-col items-start gap-3 p-5 rounded-lg border-2 text-left transition-colors',
                    tipo === 'caixa'
                      ? 'border-primary bg-primary/5'
                      : 'border-border hover:border-muted-foreground/40 hover:bg-muted/30',
                  )}
                >
                  <Coins className="size-8 text-primary" />
                  <div>
                    <p className="font-semibold text-sm">Caixa</p>
                    <p className="text-xs text-muted-foreground mt-1">
                      Registre entradas e saídas em dinheiro, como caixa físico ou fundo fixo.
                    </p>
                  </div>
                </button>
              </div>
            </StepperContent>

            {/* Etapa 2 — Dados */}
            <StepperContent value={2}>
              {tipo === 'banco' ? (
                <div className="flex flex-col gap-4">
                  {/* Select banco */}
                  <div className="flex flex-col gap-1.5">
                    <Label>Banco <span className="text-destructive">*</span></Label>
                    <div className="flex items-center gap-2">
                      {selectedBank?.logo_url && (
                        <img
                          src={selectedBank.logo_url}
                          className="h-7 w-7 rounded object-contain shrink-0"
                          alt=""
                        />
                      )}
                      {loadingBanks ? (
                        <div className="flex items-center gap-2 text-sm text-muted-foreground py-2">
                          <Loader2 className="size-4 animate-spin" /> Carregando bancos...
                        </div>
                      ) : (
                        <Select value={bankId} onValueChange={setBankId}>
                          <SelectTrigger className="flex-1">
                            <SelectValue placeholder="Selecione o banco" />
                          </SelectTrigger>
                          <SelectContent className="max-h-64">
                            {banks.map((b) => (
                              <SelectItem key={b.id} value={String(b.id)}>
                                {b.code ? `${b.code} — ` : ''}{b.name}
                              </SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                      )}
                    </div>
                  </div>
                  {/* Apelido */}
                  <div className="flex flex-col gap-1.5">
                    <Label>
                      Apelido da conta{' '}
                      <span className="text-xs text-muted-foreground">(opcional)</span>
                    </Label>
                    <Input
                      placeholder="Ex.: Conta Principal, Conta Salários..."
                      value={nomeBanco}
                      onChange={(e) => setNomeBanco(e.target.value)}
                    />
                    <p className="text-xs text-muted-foreground">
                      Se não preenchido, o nome será gerado automaticamente.
                    </p>
                  </div>
                  {/* Tipo de conta + Agência + Conta */}
                  <div className="grid grid-cols-3 gap-3">
                    <div className="flex flex-col gap-1.5">
                      <Label>Tipo de Conta <span className="text-destructive">*</span></Label>
                      <Select value={accountType} onValueChange={setAccountType}>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          {Object.entries(ACCOUNT_TYPE_LABELS).map(([k, v]) => (
                            <SelectItem key={k} value={k}>{v}</SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                    <div className="flex flex-col gap-1.5">
                      <Label>Agência <span className="text-destructive">*</span></Label>
                      <Input
                        placeholder="0001"
                        value={agencia}
                        onChange={(e) => setAgencia(e.target.value)}
                      />
                    </div>
                    <div className="flex flex-col gap-1.5">
                      <Label>Conta <span className="text-destructive">*</span></Label>
                      <Input
                        placeholder="12345-6"
                        value={conta}
                        onChange={(e) => setConta(e.target.value)}
                      />
                    </div>
                  </div>
                </div>
              ) : (
                <div className="flex flex-col gap-1.5">
                  <Label>Nome do Caixa <span className="text-destructive">*</span></Label>
                  <Input
                    placeholder="Ex.: Caixa Central, Fundo de Caixa..."
                    value={nome}
                    onChange={(e) => setNome(e.target.value)}
                    autoFocus
                  />
                </div>
              )}
            </StepperContent>

            {/* Etapa 3 — Saldo e Descrição */}
            <StepperContent value={3}>
              <div className="flex flex-col gap-4">
                {/* Saldo */}
                <div className="flex flex-col gap-1.5">
                  <Label>Saldo do dia Anterior <span className="text-destructive">*</span></Label>
                  <div className="flex items-center">
                    <span className="inline-flex items-center px-3 h-9 rounded-l-md border border-r-0 border-input bg-muted text-sm text-muted-foreground shrink-0">
                      R$
                    </span>
                    <Input
                      className="rounded-l-none"
                      type="text"
                      inputMode="decimal"
                      placeholder="0,00"
                      value={saldoInicial}
                      onChange={(e) => setSaldoInicial(e.target.value)}
                      autoFocus
                    />
                  </div>
                </div>
                {/* Conta Contábil */}
                <div className="flex flex-col gap-1.5">
                  <Label>
                    Conta Contábil{' '}
                    <span className="text-xs text-muted-foreground">(opcional)</span>
                  </Label>
                  {loadingContas ? (
                    <div className="flex items-center gap-2 text-sm text-muted-foreground py-2">
                      <Loader2 className="size-4 animate-spin" /> Carregando plano de contas...
                    </div>
                  ) : (
                    <Popover open={contaPopoverOpen} onOpenChange={setContaPopoverOpen}>
                      <PopoverTrigger asChild>
                        <button
                          type="button"
                          role="combobox"
                          aria-expanded={contaPopoverOpen}
                          className="flex h-8.5 w-full items-center justify-between rounded-md border border-input bg-background px-3 text-[0.8125rem] shadow-xs shadow-black/5 transition-shadow hover:bg-accent/50 focus-visible:border-ring focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                          {contaContabilId ? (
                            <span className="truncate">
                              {(() => { const c = contasContabeis.find((c) => String(c.id) === contaContabilId); return c ? `${c.code} — ${c.name}` : ''; })()}
                            </span>
                          ) : (
                            <span className="text-muted-foreground">Selecione a conta contábil...</span>
                          )}
                          <ChevronsUpDown className="ml-2 size-4 shrink-0 opacity-50" />
                        </button>
                      </PopoverTrigger>
                      <PopoverContent className="w-(--radix-popover-trigger-width) p-0" align="start">
                        <Command>
                          <CommandInput placeholder="Buscar conta contábil..." />
                          <CommandList>
                            <CommandEmpty>Nenhuma conta encontrada.</CommandEmpty>
                            <CommandGroup>
                              {contasContabeis.map((c) => (
                                <CommandItem
                                  key={c.id}
                                  value={`${c.code} ${c.name}`}
                                  onSelect={() => { setContaContabilId(String(c.id)); setContaPopoverOpen(false); }}
                                >
                                  {c.code} — {c.name}
                                </CommandItem>
                              ))}
                            </CommandGroup>
                          </CommandList>
                        </Command>
                      </PopoverContent>
                    </Popover>
                  )}
                  <p className="text-xs text-muted-foreground">
                    Vínculo contábil para exportação (De/Para).
                  </p>
                </div>
                {/* Descrição */}
                <div className="flex flex-col gap-1.5">
                  <Label>
                    Descrição{' '}
                    <span className="text-xs text-muted-foreground">(opcional)</span>
                  </Label>
                  <Textarea
                    placeholder="Observações..."
                    rows={2}
                    value={descricao}
                    onChange={(e) => setDescricao(e.target.value)}
                  />
                </div>
              </div>
            </StepperContent>

          </StepperPanel>
        </Stepper>

        {/* Rodapé de navegação */}
        <div className="flex items-center justify-between px-6 py-4 border-t border-border bg-muted/20">
          <Button
            type="button"
            variant="outline"
            size="sm"
            onClick={() => (step > 1 ? setStep(step - 1) : handleClose())}
            disabled={saving}
          >
            {step === 1 ? 'Cancelar' : 'Voltar'}
          </Button>
          {step < 3 ? (
            <Button
              type="button"
              size="sm"
              onClick={() => setStep(step + 1)}
              disabled={step === 2 && !step2Valid}
            >
              Próximo
            </Button>
          ) : (
            <Button
              type="button"
              size="sm"
              onClick={handleSubmit}
              disabled={saving || !step3Valid}
            >
              {saving
                ? <><Loader2 className="size-4 animate-spin mr-1" />Salvando...</>
                : 'Criar Entidade'}
            </Button>
          )}
        </div>
      </DialogContent>
    </Dialog>
  );
}
// ── DeleteEntidadeDialog ──────────────────────────────────────────────────────

function DeleteEntidadeDialog({
  entidade,
  open,
  onOpenChange,
  onDeleted,
}: {
  entidade: IEntidade | null;
  open: boolean;
  onOpenChange: (v: boolean) => void;
  onDeleted: () => void;
}) {
  const { csrfToken } = useAppData();
  const [loading, setLoading] = useState(false);

  async function handleDelete() {
    if (!entidade || !csrfToken) return;
    setLoading(true);
    try {
      const res = await fetch(`/relatorios/entidades/${entidade.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro ao excluir', json.message ?? '');
        return;
      }
      notify.success('Excluída!', json.message ?? 'Entidade removida com sucesso.');
      onOpenChange(false);
      onDeleted();
    } catch {
      notify.networkError();
    } finally {
      setLoading(false);
    }
  }

  return (
    <AlertDialog open={open} onOpenChange={onOpenChange}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Excluir entidade financeira?</AlertDialogTitle>
          <AlertDialogDescription>
            <span className="font-medium text-foreground">{entidade?.nome}</span> será excluída
            permanentemente. Esta ação não poderá ser desfeita. Só é possível excluir entidades
            sem transações vinculadas.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel disabled={loading}>Cancelar</AlertDialogCancel>
          <AlertDialogAction
            onClick={handleDelete}
            disabled={loading}
            className="bg-destructive hover:bg-destructive/90 text-white"
          >
            {loading ? <Loader2 className="size-4 animate-spin mr-1" /> : null}
            Excluir
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}

// ── EditEntidadeSheet ────────────────────────────────────────────────────────

function EditEntidadeSheet({
  entidade,
  open,
  onOpenChange,
  onUpdated,
}: {
  entidade: IEntidade | null;
  open: boolean;
  onOpenChange: (v: boolean) => void;
  onUpdated: () => void;
}) {
  const { csrfToken } = useAppData();
  const [banks, setBanks] = useState<IBank[]>([]);
  const [loadingBanks, setLoadingBanks] = useState(false);
  const [contasContabeis, setContasContabeis] = useState<IContaContabil[]>([]);
  const [loadingContas, setLoadingContas] = useState(false);
  const [saving, setSaving] = useState(false);

  // Campos do formulário
  const [bankId, setBankId] = useState('');
  const [bankPopoverOpen, setBankPopoverOpen] = useState(false);
  const [nomeBanco, setNomeBanco] = useState('');
  const [nome, setNome] = useState('');
  const [agencia, setAgencia] = useState('');
  const [conta, setConta] = useState('');
  const [accountType, setAccountType] = useState('corrente');
  const [contaContabilId, setContaContabilId] = useState('');
  const [contaPopoverOpen, setContaPopoverOpen] = useState(false);
  const [descricao, setDescricao] = useState('');

  // Pré-popula com os dados da entidade ao abrir
  useEffect(() => {
    if (!open || !entidade) return;
    setNome(entidade.nome ?? '');
    setNomeBanco(entidade.banco_nome ?? '');
    setAgencia(entidade.agencia ?? '');
    setConta(entidade.conta ?? '');
    setAccountType(entidade.account_type ?? 'corrente');
    setContaContabilId('');
    setDescricao('');
    setBankId('');
  }, [open, entidade]);

  // Carrega bancos ao abrir (somente banco)
  useEffect(() => {
    if (!open || entidade?.tipo !== 'banco') return;
    setLoadingBanks(true);
    fetch('/app/financeiro/banco/banks', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json() as Promise<{ data: IBank[] }>)
      .then((j) => {
        const list = j.data ?? [];
        setBanks(list);
        // Tenta encontrar o banco atual pelo nome
        if (entidade?.banco_nome) {
          const match = list.find((b) => b.name === entidade.banco_nome);
          if (match) setBankId(String(match.id));
        }
      })
      .catch(() => setBanks([]))
      .finally(() => setLoadingBanks(false));
  }, [open, entidade]);

  // Carrega plano de contas
  useEffect(() => {
    if (!open || contasContabeis.length > 0) return;
    setLoadingContas(true);
    fetch('/app/financeiro/banco/contas-contabeis', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json() as Promise<{ data: IContaContabil[] }>)
      .then((j) => setContasContabeis(j.data ?? []))
      .catch(() => setContasContabeis([]))
      .finally(() => setLoadingContas(false));
  }, [open, contasContabeis.length]);

  const isValid = useMemo(() => {
    if (!entidade) return false;
    if (entidade.tipo === 'banco') return !!(bankId && agencia.trim() && conta.trim() && accountType);
    return !!(nome.trim());
  }, [entidade, bankId, agencia, conta, accountType, nome]);

  function handleClose() {
    onOpenChange(false);
  }

  async function handleSubmit() {
    if (!entidade || !csrfToken) return;

    const payload: Record<string, unknown> = {
      descricao: descricao || null,
      conta_contabil_id: contaContabilId ? Number(contaContabilId) : null,
    };

    if (entidade.tipo === 'banco') {
      payload.bank_id      = Number(bankId);
      payload.nome_banco   = nomeBanco || null;
      payload.agencia      = agencia;
      payload.conta        = conta;
      payload.account_type = accountType;
    } else {
      payload.nome = nome;
    }

    setSaving(true);
    try {
      const res = await fetch(`/relatorios/entidades/${entidade.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });
      const json = await res.json() as { success?: boolean; message?: string };
      if (!res.ok || !json.success) {
        notify.error('Erro ao atualizar', json.message ?? '');
        return;
      }
      notify.success('Entidade atualizada!', json.message ?? '');
      handleClose();
      onUpdated();
    } catch {
      notify.networkError();
    } finally {
      setSaving(false);
    }
  }

  const selectedBank = banks.find((b) => String(b.id) === bankId);

  return (
    <Sheet open={open} onOpenChange={(v) => { if (!v) handleClose(); }}>
      <SheetContent side="right" className="sm:max-w-md flex flex-col gap-0 p-0">
        <SheetHeader className="px-6 pt-6 pb-4 border-b border-border">
          <SheetTitle>Editar Entidade Financeira</SheetTitle>
          <SheetDescription>
            Atualize os dados de <span className="font-medium text-foreground">{entidade?.nome}</span>.
          </SheetDescription>
        </SheetHeader>

        <SheetBody className="px-6 py-6 flex flex-col gap-5 overflow-y-auto flex-1">
          {entidade?.tipo === 'banco' ? (
            <>
              {/* Combobox banco com busca */}
              <div className="flex flex-col gap-1.5">
                <Label>Banco <span className="text-destructive">*</span></Label>
                {loadingBanks ? (
                  <div className="flex items-center gap-2 text-sm text-muted-foreground py-2">
                    <Loader2 className="size-4 animate-spin" /> Carregando bancos...
                  </div>
                ) : (
                  <Popover open={bankPopoverOpen} onOpenChange={setBankPopoverOpen}>
                    <PopoverTrigger asChild>
                      <button
                        type="button"
                        role="combobox"
                        aria-expanded={bankPopoverOpen}
                        className="flex h-8.5 w-full items-center justify-between rounded-md border border-input bg-background px-3 text-[0.8125rem] shadow-xs shadow-black/5 transition-shadow hover:bg-accent/50 focus-visible:border-ring focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 disabled:cursor-not-allowed disabled:opacity-50"
                      >
                        {selectedBank ? (
                          <span className="flex items-center gap-2 min-w-0">
                            {selectedBank.logo_url && (
                              <img
                                src={selectedBank.logo_url}
                                className="h-5 w-5 rounded object-contain shrink-0"
                                alt=""
                              />
                            )}
                            <span className="truncate">
                              {selectedBank.code ? `${selectedBank.code} — ` : ''}{selectedBank.name}
                            </span>
                          </span>
                        ) : (
                          <span className="text-muted-foreground">Selecione o banco</span>
                        )}
                        <ChevronsUpDown className="ml-2 size-4 shrink-0 opacity-50" />
                      </button>
                    </PopoverTrigger>
                    <PopoverContent className="w-(--radix-popover-trigger-width) p-0" align="start">
                      <Command>
                        <CommandInput placeholder="Buscar banco..." />
                        <CommandList>
                          <CommandEmpty>Nenhum banco encontrado.</CommandEmpty>
                          <CommandGroup>
                            {banks.map((b) => (
                              <CommandItem
                                key={b.id}
                                value={`${b.code ?? ''} ${b.name}`}
                                onSelect={() => {
                                  setBankId(String(b.id));
                                  setBankPopoverOpen(false);
                                }}
                              >
                                {b.logo_url ? (
                                  <img
                                    src={b.logo_url}
                                    className="h-5 w-5 rounded object-contain shrink-0"
                                    alt=""
                                  />
                                ) : (
                                  <span className="h-5 w-5 rounded bg-muted shrink-0 inline-block" />
                                )}
                                {b.code ? `${b.code} — ` : ''}{b.name}
                              </CommandItem>
                            ))}
                          </CommandGroup>
                        </CommandList>
                      </Command>
                    </PopoverContent>
                  </Popover>
                )}
              </div>
              {/* Apelido */}
              <div className="flex flex-col gap-1.5">
                <Label>
                  Apelido da conta{' '}
                  <span className="text-xs text-muted-foreground">(opcional)</span>
                </Label>
                <Input
                  placeholder="Ex.: Conta Principal, Conta Salários..."
                  value={nomeBanco}
                  onChange={(e) => setNomeBanco(e.target.value)}
                />
              </div>
              {/* Tipo de conta + Agência + Conta */}
              <div className="grid grid-cols-3 gap-3">
                <div className="flex flex-col gap-1.5">
                  <Label>Tipo de Conta <span className="text-destructive">*</span></Label>
                  <Select value={accountType} onValueChange={setAccountType}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {Object.entries(ACCOUNT_TYPE_LABELS).map(([k, v]) => (
                        <SelectItem key={k} value={k}>{v}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="flex flex-col gap-1.5">
                  <Label>Agência <span className="text-destructive">*</span></Label>
                  <Input
                    placeholder="0001"
                    value={agencia}
                    onChange={(e) => setAgencia(e.target.value)}
                  />
                </div>
                <div className="flex flex-col gap-1.5">
                  <Label>Conta <span className="text-destructive">*</span></Label>
                  <Input
                    placeholder="12345-6"
                    value={conta}
                    onChange={(e) => setConta(e.target.value)}
                  />
                </div>
              </div>
            </>
          ) : (
            <div className="flex flex-col gap-1.5">
              <Label>Nome do Caixa <span className="text-destructive">*</span></Label>
              <Input
                placeholder="Ex.: Caixa Central, Fundo de Caixa..."
                value={nome}
                onChange={(e) => setNome(e.target.value)}
                autoFocus
              />
            </div>
          )}

          {/* Conta Contábil */}
          <div className="flex flex-col gap-1.5">
            <Label>
              Conta Contábil{' '}
              <span className="text-xs text-muted-foreground">(opcional)</span>
            </Label>
            {loadingContas ? (
              <div className="flex items-center gap-2 text-sm text-muted-foreground py-2">
                <Loader2 className="size-4 animate-spin" /> Carregando plano de contas...
              </div>
            ) : (
              <Popover open={contaPopoverOpen} onOpenChange={setContaPopoverOpen}>
                <PopoverTrigger asChild>
                  <button
                    type="button"
                    role="combobox"
                    aria-expanded={contaPopoverOpen}
                    className="flex h-8.5 w-full items-center justify-between rounded-md border border-input bg-background px-3 text-[0.8125rem] shadow-xs shadow-black/5 transition-shadow hover:bg-accent/50 focus-visible:border-ring focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    {contaContabilId ? (
                      <span className="truncate">
                        {(() => { const c = contasContabeis.find((c) => String(c.id) === contaContabilId); return c ? `${c.code} — ${c.name}` : ''; })()}
                      </span>
                    ) : (
                      <span className="text-muted-foreground">Selecione a conta contábil...</span>
                    )}
                    <ChevronsUpDown className="ml-2 size-4 shrink-0 opacity-50" />
                  </button>
                </PopoverTrigger>
                <PopoverContent className="w-(--radix-popover-trigger-width) p-0" align="start">
                  <Command>
                    <CommandInput placeholder="Buscar conta contábil..." />
                    <CommandList>
                      <CommandEmpty>Nenhuma conta encontrada.</CommandEmpty>
                      <CommandGroup>
                        {contasContabeis.map((c) => (
                          <CommandItem
                            key={c.id}
                            value={`${c.code} ${c.name}`}
                            onSelect={() => { setContaContabilId(String(c.id)); setContaPopoverOpen(false); }}
                          >
                            {c.code} — {c.name}
                          </CommandItem>
                        ))}
                      </CommandGroup>
                    </CommandList>
                  </Command>
                </PopoverContent>
              </Popover>
            )}
          </div>

          {/* Descrição */}
          <div className="flex flex-col gap-1.5">
            <Label>
              Descrição{' '}
              <span className="text-xs text-muted-foreground">(opcional)</span>
            </Label>
            <Textarea
              placeholder="Observações..."
              rows={2}
              value={descricao}
              onChange={(e) => setDescricao(e.target.value)}
            />
          </div>
        </SheetBody>

        <SheetFooter className="px-6 py-4 border-t border-border bg-muted/20 flex-row justify-between">
          <Button type="button" variant="outline" size="sm" onClick={handleClose} disabled={saving}>
            Cancelar
          </Button>
          <Button type="button" size="sm" onClick={handleSubmit} disabled={saving || !isValid}>
            {saving
              ? <><Loader2 className="size-4 animate-spin mr-1" />Salvando...</>
              : 'Salvar alterações'}
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}

// ── EntidadesTable ────────────────────────────────────────────────────────────

function EntidadesTable({
  data,
  loading,
  refetch,
}: {
  data: IEntidade[];
  loading: boolean;
  refetch: () => void;
}) {
  const navigate = useNavigate();
  const { hasAdminRole, hasGlobalRole } = useAppData();
  const canEdit = hasAdminRole || hasGlobalRole;

  const [sorting, setSorting] = useState<SortingState>([{ id: 'nome', desc: false }]);
  const [search, setSearch] = useState('');
  const [deleteTarget, setDeleteTarget] = useState<IEntidade | null>(null);
  const [deleteOpen, setDeleteOpen] = useState(false);
  const [editTarget, setEditTarget] = useState<IEntidade | null>(null);
  const [editOpen, setEditOpen] = useState(false);
  const [saldoTarget, setSaldoTarget] = useState<IEntidade | null>(null);
  const [saldoOpen, setSaldoOpen] = useState(false);

  const filtered = useMemo(
    () =>
      data.filter((e) =>
        e.nome.toLowerCase().includes(search.toLowerCase()) ||
        (e.banco_nome ?? '').toLowerCase().includes(search.toLowerCase()),
      ),
    [data, search],
  );

  const columns = useMemo<ColumnDef<IEntidade>[]>(
    () => [
      {
        id: 'logo',
        header: '',
        cell: ({ row }) => {
          const e = row.original;
          if (e.tipo === 'banco' && e.logo_url) {
            return (
              <img
                src={e.logo_url}
                alt={e.banco_nome ?? e.nome}
                className="size-8 object-contain rounded"
              />
            );
          }
          return (
            <div className="size-8 rounded bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
              <Coins className="size-4 text-amber-600" />
            </div>
          );
        },
        enableSorting: false,
        size: 52,
      },
      {
        id: 'nome',
        accessorKey: 'nome',
        header: ({ column }) => <DataGridColumnHeader title="Nome" column={column} />,
        cell: ({ row }) => {
          const e = row.original;
          return (
            <div className="flex flex-col min-w-0">
              <span className="font-medium truncate">{e.nome}</span>
              {e.banco_nome && e.banco_nome !== e.nome && (
                <span className="text-[0.7rem] text-muted-foreground truncate">{e.banco_nome}</span>
              )}
            </div>
          );
        },
        enableSorting: true,
        size: 260,
      },
      {
        id: 'tipo',
        accessorKey: 'tipo',
        header: ({ column }) => <DataGridColumnHeader title="Tipo" column={column} />,
        cell: ({ row }) => {
          const e = row.original;
          return (
            <div className="flex flex-col gap-0.5">
              <Badge variant={e.tipo === 'banco' ? 'secondary' : 'warning'} appearance="outline" size="sm">
                {e.tipo === 'banco' ? <Building2 className="size-3 mr-1" /> : <Coins className="size-3 mr-1" />}
                {e.account_label}
              </Badge>
            </div>
          );
        },
        enableSorting: true,
        size: 160,
      },
      {
        id: 'agencia_conta',
        header: ({ column }) => <DataGridColumnHeader title="Ag. / Conta" column={column} />,
        cell: ({ row }) => {
          const e = row.original;
          if (!e.agencia && !e.conta) return <span className="text-muted-foreground">—</span>;
          return (
            <div className="flex flex-col text-sm tabular-nums">
              {e.agencia && <span>Ag. {e.agencia}</span>}
              {e.conta && <span>Cc. {e.conta}</span>}
            </div>
          );
        },
        enableSorting: false,
        size: 130,
      },
      {
        id: 'saldo_inicial',
        accessorKey: 'saldo_inicial',
        header: ({ column }) => <DataGridColumnHeader title="Saldo Inicial" column={column} />,
        cell: ({ row }) => {
          const e = row.original;
          const negativo = e.saldo_inicial < 0;
          return (
            <span className={cn('tabular-nums text-sm', negativo ? 'text-destructive' : 'text-muted-foreground')}>
              {fmtCurrency(e.saldo_inicial)}
            </span>
          );
        },
        enableSorting: true,
        size: 150,
      },
      {
        id: 'saldo_atual',
        accessorKey: 'saldo_atual',
        header: ({ column }) => <DataGridColumnHeader title="Saldo Atual" column={column} />,
        cell: ({ row }) => {
          const e = row.original;
          return (
            <div className="flex items-center gap-1.5">
              <Banknote className={cn('size-3.5', e.saldo_negativo ? 'text-destructive' : 'text-success')} />
              <span className={cn('font-semibold tabular-nums', e.saldo_negativo ? 'text-destructive' : 'text-success')}>
                {fmtCurrency(e.saldo_atual)}
              </span>
            </div>
          );
        },
        enableSorting: true,
        size: 150,
      },
      {
        id: 'status_conciliacao',
        accessorKey: 'status_conciliacao',
        header: ({ column }) => <DataGridColumnHeader title="Conciliação" column={column} />,
        cell: ({ row }) => {
          const e = row.original;
          const status = e.status_conciliacao;
          const variant = STATUS_VARIANT[status] ?? 'secondary';
          const label = STATUS_LABEL[status] ?? status;
          return (
            <div className="flex items-center gap-1.5">
              <Badge variant={variant} appearance="outline" size="sm">{label}</Badge>
              {e.pendencias_conciliacao > 0 && (
                <span className="text-[0.7rem] text-warning font-medium flex items-center gap-0.5">
                  <CircleAlert className="size-3" />
                  {e.pendencias_conciliacao}
                </span>
              )}
            </div>
          );
        },
        enableSorting: false,
        size: 160,
      },
      {
        id: 'actions',
        header: '',
        cell: ({ row }) => {
          const e = row.original;
          return (
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button
                  size="sm"
                  variant="outline"
                  className="h-7 gap-1.5 px-2.5 text-xs border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800"
                >
                  Ações
                  <ChevronDown className="size-3.5" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent side="bottom" align="end">
                <DropdownMenuItem
                  onClick={() => navigate(`/financeiro/banco/entidade/${e.id}`)}
                >
                  <ExternalLink className="size-4 text-muted-foreground" />
                  Ver detalhes
                </DropdownMenuItem>
                {canEdit && (
                  <DropdownMenuItem
                    onClick={() => { setEditTarget(e); setEditOpen(true); }}
                  >
                    <Pencil className="size-4 text-muted-foreground" />
                    Editar
                  </DropdownMenuItem>
                )}
                {canEdit && (
                  <DropdownMenuItem
                    onClick={() => { setSaldoTarget(e); setSaldoOpen(true); }}
                  >
                    <DollarSign className="size-4 text-muted-foreground" />
                    Valor inicial
                  </DropdownMenuItem>
                )}
                {canEdit && (
                  <DropdownMenuItem
                    variant="destructive"
                    onClick={() => { setDeleteTarget(e); setDeleteOpen(true); }}
                  >
                    <Trash2 className="size-4" />
                    Excluir
                  </DropdownMenuItem>
                )}
              </DropdownMenuContent>
            </DropdownMenu>
          );
        },
        enableSorting: false,
        size: 60,
      },
    ],
    [canEdit, navigate],
  );

  const table = useReactTable({
    columns,
    data: filtered,
    state: { sorting },
    onSortingChange: setSorting,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
  });

  return (
    <DataGrid
      table={table}
      recordCount={filtered.length}
      isLoading={loading}
      tableLayout={{ width: 'auto', cellBorder: true }}
    >
      <Card>
        {/* Barra de busca + refresh */}
        <div className="flex items-center gap-3 px-4 py-3 border-b border-border">
          <Input
            placeholder="Buscar entidade..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="h-8 max-w-xs text-sm"
          />
          <Button
            variant="outline"
            size="sm"
            className={cn(financeiroToolbarSoftBlueClass, 'h-8 min-w-8 px-2.5')}
            onClick={refetch}
            disabled={loading}
            aria-label="Atualizar"
          >
            <RefreshCw className={`size-4 ${loading ? 'animate-spin' : ''}`} />
          </Button>
          <span className="text-xs text-muted-foreground ml-auto">
            {filtered.length} entidade{filtered.length !== 1 ? 's' : ''}
          </span>
        </div>

        <CardTable>
          <ScrollArea>
            <DataGridTable />
            <ScrollBar orientation="horizontal" />
          </ScrollArea>
        </CardTable>
        <CardFooter />
      </Card>

      <DeleteEntidadeDialog
        entidade={deleteTarget}
        open={deleteOpen}
        onOpenChange={setDeleteOpen}
        onDeleted={refetch}
      />

      <EditEntidadeSheet
        entidade={editTarget}
        open={editOpen}
        onOpenChange={setEditOpen}
        onUpdated={refetch}
      />

      <AtualizarSaldoDialog
        entidade={saldoTarget}
        open={saldoOpen}
        onOpenChange={setSaldoOpen}
        onUpdated={refetch}
      />
    </DataGrid>
  );
}

// ── EntidadesFinanceirasPage ──────────────────────────────────────────────────

export function EntidadesFinanceirasPage() {
  const { entidades, totalSaldo, loading, error, refetch } = useEntidades();
  const { hasAdminRole, hasGlobalRole } = useAppData();
  const canCreate = hasAdminRole || hasGlobalRole;
  const [dialogOpen, setDialogOpen] = useState(false);

  const bancos = entidades.filter((e) => e.tipo === 'banco');
  const caixas = entidades.filter((e) => e.tipo === 'caixa');

  return (
    <div className="container flex flex-col gap-5">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Entidades Financeiras</ToolbarPageTitle>
          <FinanceiroBreadcrumb currentLabel="Entidades Financeiras" />
          <p className="text-sm text-muted-foreground">
            {bancos.length} banco{bancos.length !== 1 ? 's' : ''} · {caixas.length} caixa{caixas.length !== 1 ? 's' : ''}
            {' · '}
            Saldo total:{' '}
            <span className={cn('font-semibold', totalSaldo < 0 ? 'text-destructive' : 'text-success')}>
              {fmtCurrency(totalSaldo)}
            </span>
          </p>
        </ToolbarHeading>
        {canCreate && (
          <ToolbarActions>
            <Button
              size="sm"
              className="h-9 gap-2"
              onClick={() => setDialogOpen(true)}
            >
              <Plus className="size-4" />
              Nova Entidade
            </Button>
          </ToolbarActions>
        )}
      </Toolbar>

      {error && (
        <div className="px-4 py-2 text-sm text-destructive bg-destructive/10 rounded-lg border border-destructive/30">
          {error}
        </div>
      )}

      <EntidadesTable data={entidades} loading={loading} refetch={refetch} />

      <NovaEntidadeDialog
        open={dialogOpen}
        onOpenChange={setDialogOpen}
        onCreated={refetch}
      />
    </div>
  );
}

