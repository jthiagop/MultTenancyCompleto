import { useEffect, useState } from 'react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input, InputGroup } from '@/components/ui/input';
import { MaskedInput } from '@/components/common/masked-input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { Loader2, Search } from 'lucide-react';
import { notify } from '@/lib/notify';
import { AddressCard, AddressValue, EMPTY_ADDRESS } from '@/components/common/address-card';


export interface ParceiroCreatedPayload {
  id: string;
  nome: string;
  natureza: string;
}

interface ParceiroQuickCreateSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  /** Alinhado ao fluxo do lançamento: receita → cliente; despesa → fornecedor */
  context: 'receita' | 'despesa';
  onCreated: (p: ParceiroCreatedPayload) => void;
}

function QuickCreateActionBar({
  submitting,
  onCancel,
}: {
  submitting: boolean;
  onCancel: () => void;
}) {
  return (
    <div className="flex items-center gap-2.5 shrink-0">
      <Button type="button" variant="outline" onClick={onCancel} disabled={submitting}>
        Cancelar
      </Button>
      <Button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white border-0" disabled={submitting}>
        {submitting ? 'Salvando…' : 'Salvar'}
      </Button>
    </div>
  );
}

/**
 * Cadastro rápido de parceiro (espelha o drawer Blade `drawer_fornecedor.blade.php`):
 * POST JSON em `financeiro.fornecedores.store` → `/financeiro/parceiros`.
 */
export function ParceiroQuickCreateSheet({
  open,
  onOpenChange,
  context,
  onCreated,
}: ParceiroQuickCreateSheetProps) {
  const { csrfToken } = useAppData();

  const [tipoPessoa, setTipoPessoa] = useState<'pf' | 'pj' | ''>('');
  const [cpf, setCpf] = useState('');
  const [cnpj, setCnpj] = useState('');
  const [nomeCompleto, setNomeCompleto] = useState('');
  const [nomeFantasia, setNomeFantasia] = useState('');
  const [telefone, setTelefone] = useState('');
  const [email, setEmail] = useState('');
  const [chkFornecedor, setChkFornecedor] = useState(context === 'despesa');
  const [chkCliente, setChkCliente] = useState(context === 'receita');
  const [address, setAddress] = useState<AddressValue>(EMPTY_ADDRESS);
  const [submitting, setSubmitting] = useState(false);
  const [consultandoCnpj, setConsultandoCnpj] = useState(false);

  useEffect(() => {
    if (!open) return;
    setTipoPessoa('');
    setCpf('');
    setCnpj('');
    setNomeCompleto('');
    setNomeFantasia('');
    setTelefone('');
    setEmail('');
    setChkFornecedor(context === 'despesa');
    setChkCliente(context === 'receita');
    setAddress(EMPTY_ADDRESS);
  }, [open, context]);

  const title = context === 'receita' ? 'Novo Cliente' : 'Novo Fornecedor';
  const contextHint =
    context === 'receita' ? 'Cadastro vinculado ao fluxo de receita' : 'Cadastro vinculado ao fluxo de despesa';

  function resolveNatureza(): 'fornecedor' | 'cliente' | 'ambos' | '' {
    if (chkFornecedor && chkCliente) return 'ambos';
    if (chkFornecedor) return 'fornecedor';
    if (chkCliente) return 'cliente';
    return '';
  }

  async function handleConsultarCnpj() {
    const cnpjLimpo = cnpj.replace(/\D/g, '');
    if (cnpjLimpo.length !== 14) {
      notify.error('CNPJ inválido', 'Informe um CNPJ com 14 dígitos numéricos.');
      return;
    }
    setConsultandoCnpj(true);
    try {
      const res = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpjLimpo}`);
      if (!res.ok) {
        notify.error('CNPJ não encontrado', 'Verifique o número e tente novamente.');
        return;
      }
      const data = await res.json() as {
        razao_social?: string;
        nome_fantasia?: string;
        logradouro?: string;
        municipio?: string;
        bairro?: string;
        numero?: string;
        uf?: string;
        cep?: string;
        ddd_telefone_1?: string;
        email?: string;
      };
      if (data.razao_social)  setNomeFantasia(data.razao_social);
      if (data.nome_fantasia) setNomeCompleto(data.nome_fantasia);
      setAddress((prev) => ({
        ...prev,
        logradouro: data.logradouro ?? prev.logradouro,
        cidade: data.municipio ?? prev.cidade,
        bairro: data.bairro ?? prev.bairro,
        numero: data.numero ?? prev.numero,
        uf: data.uf ?? prev.uf,
        cep: data.cep ? data.cep.replace(/\D/g, '').replace(/^(\d{5})(\d{3})$/, '$1-$2') : prev.cep,
      }));
      if (data.ddd_telefone_1) setTelefone(data.ddd_telefone_1.trim());
      if (data.email)         setEmail(data.email.toLowerCase());
      notify.success('Dados importados!', 'Campos preenchidos automaticamente a partir da Receita Federal.');
    } catch {
      notify.error('Falha na consulta', 'Não foi possível buscar o CNPJ. Verifique sua conexão.');
    } finally {
      setConsultandoCnpj(false);
    }
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const natureza = resolveNatureza();
    if (!natureza) {
      notify.warning('Campo obrigatório', 'Selecione pelo menos uma natureza: Fornecedor ou Cliente.');
      return;
    }
    if (!tipoPessoa) {
      notify.warning('Campo obrigatório', 'Selecione o tipo de pessoa (Física ou Jurídica).');
      return;
    }
    if (tipoPessoa === 'pf' && !nomeCompleto.trim()) {
      notify.warning('Campo obrigatório', 'Informe o nome completo.');
      return;
    }
    if (tipoPessoa === 'pj' && !nomeFantasia.trim()) {
      notify.warning('Campo obrigatório', 'Informe o nome fantasia.');
      return;
    }
    if (tipoPessoa === 'pf' && !cpf.trim()) {
      notify.warning('Campo obrigatório', 'Informe o CPF.');
      return;
    }
    if (tipoPessoa === 'pj' && !cnpj.trim()) {
      notify.warning('Campo obrigatório', 'Informe o CNPJ.');
      return;
    }

    const payload: Record<string, unknown> = {
      tipo: tipoPessoa,
      natureza,
      telefone: telefone || null,
      email: email || null,
      is_fornecedor: chkFornecedor,
      is_cliente: chkCliente,
    };

    if (tipoPessoa === 'pf') {
      payload.nome_completo = nomeCompleto.trim();
      payload.cpf = cpf.trim();
    } else {
      payload.nome_fantasia = nomeFantasia.trim();
      payload.cnpj = cnpj.trim();
    }

    if (address.cep || address.logradouro || address.cidade) {
      payload.cep = address.cep || null;
      payload.address1 = address.logradouro || null;
      payload.city = address.cidade || null;
      payload.bairro = address.bairro || null;
      payload.numero = address.numero || null;
      payload.country = address.uf || null;
    }

      if (!csrfToken) {
      notify.reload();
      return;
    }

    setSubmitting(true);
    try {
      const res = await fetch('/financeiro/parceiros', {
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

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        data?: { id?: number; nome?: string; natureza?: string };
        errors?: Record<string, string[]>;
      };

      if (res.ok && result.success && result.data?.id != null && result.data.nome) {
        notify.success('Parceiro cadastrado!', `${result.data.nome} foi adicionado com sucesso.`);
        onCreated({
          id: String(result.data.id),
          nome: result.data.nome,
          natureza: result.data.natureza ?? natureza,
        });
        onOpenChange(false);
      } else {
        notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
        if (result.errors) {
          notify.validationErrors(result.errors);
        }
      }
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        overlayClassName="z-[55]"
        className={cn(
          'z-[60] gap-0 lg:w-[640px] sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '[&_[data-slot=sheet-close]]:top-4.5 [&_[data-slot=sheet-close]]:end-5',
        )}
      >
        <SheetHeader className="border-b py-3.5 px-5 border-border space-y-0">
          <SheetTitle className="font-medium">{title}</SheetTitle>
        </SheetHeader>

        <form id="parceiro-quick-create-form" onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
          <SheetBody className="grow p-0 flex flex-col min-h-0">
            <div className="flex justify-between gap-2 flex-wrap border-b border-border p-5 pt-1 items-center">
              <span className="text-xs text-muted-foreground font-medium">{contextHint}</span>
              <QuickCreateActionBar submitting={submitting} onCancel={() => onOpenChange(false)} />
            </div>

            <ScrollArea className="h-[calc(100dvh-16.5rem)] ps-5 pe-4 me-1 pb-5">
              <div className="space-y-5 mt-5.5">
                <Card className="rounded-md">
                  <CardHeader className="min-h-[38px] bg-accent/50 py-2">
                    <CardTitle className="text-2sm">Identificação</CardTitle>
                  </CardHeader>
                  <CardContent className="pt-4">
                    <div className="grid grid-cols-12 gap-3">
                      <div className="col-span-12 sm:col-span-4 space-y-2">
                        <Label className="text-xs">
                          Tipo <span className="text-destructive">*</span>
                        </Label>
                        <select
                          value={tipoPessoa}
                          onChange={(e) => setTipoPessoa(e.target.value as 'pf' | 'pj' | '')}
                          className={cn(
                            'flex w-full bg-background border border-input rounded-md shadow-xs shadow-black/5',
                            'h-8.5 px-3 text-[0.8125rem] text-foreground',
                            'focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 focus-visible:border-ring',
                            'disabled:cursor-not-allowed disabled:opacity-60',
                            !tipoPessoa && 'text-muted-foreground/80',
                          )}
                        >
                          <option value="" disabled>Escolha…</option>
                          <option value="pf">Pessoa Física</option>
                          <option value="pj">Pessoa Jurídica</option>
                        </select>
                      </div>

                      {tipoPessoa === 'pf' && (
                        <div className="col-span-12 sm:col-span-8 space-y-2">
                          <Label className="text-xs">
                            CPF <span className="text-destructive">*</span>
                          </Label>
                          <MaskedInput
                            maskType="cpf"
                            value={cpf}
                            onMaskedChange={setCpf}
                            placeholder="000.000.000-00"
                          />
                        </div>
                      )}
                      {tipoPessoa === 'pj' && (
                        <div className="col-span-12 sm:col-span-8 space-y-2">
                          <Label className="text-xs">
                            CNPJ <span className="text-destructive">*</span>
                          </Label>
                          <InputGroup>
                            <MaskedInput
                              maskType="cnpj"
                              value={cnpj}
                              onMaskedChange={setCnpj}
                              placeholder="00.000.000/0000-00"
                            />
                            <Button
                              type="button"
                              className="bg-blue-600 hover:bg-blue-700 text-white border-0"
                              onClick={handleConsultarCnpj}
                              disabled={consultandoCnpj || !cnpj.trim()}
                              data-slot="button"
                            >
                              {consultandoCnpj
                                ? <Loader2 className="size-3.5 animate-spin" />
                                : <Search className="size-3.5" />}
                              Buscar
                            </Button>
                          </InputGroup>
                        </div>
                      )}
                    </div>

                    {tipoPessoa === 'pj' && (
                      <div className="space-y-2 mt-4">
                        <Label className="text-xs">
                          Nome fantasia <span className="text-destructive">*</span>
                        </Label>
                        <Input
                          value={nomeFantasia}
                          onChange={(e) => setNomeFantasia(e.target.value)}
                          placeholder="Razão social / fantasia"
                        />
                      </div>
                    )}
                    {tipoPessoa === 'pf' && (
                      <div className="space-y-2 mt-4">
                        <Label className="text-xs">
                          Nome completo <span className="text-destructive">*</span>
                        </Label>
                        <Input
                          value={nomeCompleto}
                          onChange={(e) => setNomeCompleto(e.target.value)}
                          placeholder="Nome completo"
                        />
                      </div>
                    )}
                  </CardContent>
                </Card>

                <Card className="rounded-md">
                  <CardHeader className="min-h-[38px] bg-accent/50 py-2">
                    <CardTitle className="text-2sm">Contato e natureza</CardTitle>
                  </CardHeader>
                  <CardContent className="pt-4">
                    <div className="grid grid-cols-12 gap-3">
                      <div className="col-span-12 sm:col-span-4 space-y-2">
                        <Label className="text-xs">Telefone</Label>
                        <MaskedInput
                          maskType="telefone"
                          value={telefone}
                          onMaskedChange={setTelefone}
                          placeholder="(00) 00000-0000"
                        />
                      </div>
                      <div className="col-span-12 sm:col-span-8 space-y-2">
                        <Label className="text-xs">E-mail</Label>
                        <Input
                          type="email"
                          value={email}
                          onChange={(e) => setEmail(e.target.value)}
                          placeholder="email@exemplo.com"
                        />
                      </div>
                    </div>

                    <div className="space-y-2 mt-4">
                      <Label className="text-xs">Natureza</Label>
                      <div className="flex flex-wrap gap-6">
                        <label className="flex items-center gap-2 text-sm cursor-pointer">
                          <Checkbox checked={chkFornecedor} onCheckedChange={(v) => setChkFornecedor(v === true)} />
                          Fornecedor
                        </label>
                        <label className="flex items-center gap-2 text-sm cursor-pointer">
                          <Checkbox checked={chkCliente} onCheckedChange={(v) => setChkCliente(v === true)} />
                          Cliente
                        </label>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <AddressCard
                  value={address}
                  onChange={setAddress}
                  collapsible
                  defaultOpen
                  disabled={submitting}
                />
              </div>
            </ScrollArea>
          </SheetBody>

          <SheetFooter className="flex-row border-t justify-between items-center p-5 border-border gap-2 sm:space-x-0">
            <span className="text-xs text-muted-foreground font-medium max-sm:hidden">{contextHint}</span>
            <QuickCreateActionBar submitting={submitting} onCancel={() => onOpenChange(false)} />
          </SheetFooter>
        </form>
      </SheetContent>
    </Sheet>
  );
}
