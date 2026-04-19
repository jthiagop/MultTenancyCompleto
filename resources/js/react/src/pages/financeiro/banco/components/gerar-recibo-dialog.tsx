import { useCallback, useEffect, useState } from 'react';
import {
  ArrowDownLeft,
  ArrowUpRight,
  CalendarDays,
  FileSignature,
  Loader2,
  Sparkles,
  Wallet,
} from 'lucide-react';
import { notify } from '@/lib/notify';
import { useAppData } from '@/hooks/useAppData';
import {
  Dialog,
  DialogBody,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { MaskedInput } from '@/components/common/masked-input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { cn } from '@/lib/utils';
import { fmtCurrency } from '@/pages/financeiro/components/transacao-table-shared';

/** Endereço como retornado em detalhes da transação / check-documento */
export interface ReciboEnderecoInput {
  cep?: string | null;
  rua?: string | null;
  numero?: string | null;
  bairro?: string | null;
  cidade?: string | null;
  uf?: string | null;
  complemento?: string | null;
}

export interface GerarReciboParceiroContext {
  nome?: string | null;
  nome_fantasia?: string | null;
  cpf_cnpj?: string | null;
  address?: ReciboEnderecoInput | null;
}

/** Dados mínimos da transação para pré-preencher o modal (espelha Blade `abrirModalReciboAjax`) */
export interface GerarReciboContext {
  id: number;
  tipo: string;
  descricao: string | null;
  historico_complementar?: string | null;
  valor: number;
  data_competencia_formatada: string | null;
  parceiro?: GerarReciboParceiroContext | null;
}

/** Monta o contexto a partir do JSON de `GET /financeiro/transacao/{id}/detalhes`. */
export function buildGerarReciboContext(d: {
  id: number;
  tipo: string;
  descricao: string | null;
  historico_complementar?: string | null;
  valor: number;
  data_competencia_formatada: string | null;
  parceiro?: GerarReciboParceiroContext | null;
}): GerarReciboContext {
  return {
    id: d.id,
    tipo: d.tipo,
    descricao: d.descricao,
    historico_complementar: d.historico_complementar,
    valor: d.valor,
    data_competencia_formatada: d.data_competencia_formatada,
    parceiro: d.parceiro
      ? {
          nome: d.parceiro.nome,
          nome_fantasia: d.parceiro.nome_fantasia,
          cpf_cnpj: d.parceiro.cpf_cnpj,
          address: d.parceiro.address ?? null,
        }
      : null,
  };
}

function buildReferente(descricao: string | null, historico: string | null | undefined): string {
  let r = descricao?.trim() ?? '';
  if (historico?.trim()) {
    r += (r ? ' — ' : '') + historico.trim();
  }
  return r;
}

function digitsOnly(s: string): string {
  return s.replace(/\D/g, '');
}

/** Máscara CPF/CNPJ alinhada ao script do Blade */
function formatCpfCnpjInput(raw: string): string {
  const v = digitsOnly(raw).slice(0, 14);
  if (v.length <= 11) {
    let x = v;
    x = x.replace(/(\d{3})(\d)/, '$1.$2');
    x = x.replace(/(\d{3})(\d)/, '$1.$2');
    x = x.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return x;
  }
  let x = v;
  x = x.replace(/^(\d{2})(\d)/, '$1.$2');
  x = x.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
  x = x.replace(/\.(\d{3})(\d)/, '.$1/$2');
  x = x.replace(/(\d{4})(\d)/, '$1-$2');
  return x;
}

function mapAddressToForm(addr: ReciboEnderecoInput | null | undefined) {
  if (!addr) {
    return { cep: '', logradouro: '', numero: '', bairro: '', localidade: '', uf: '', complemento: '' };
  }
  return {
    cep: addr.cep ?? '',
    logradouro: addr.rua ?? '',
    numero: addr.numero ?? '',
    bairro: addr.bairro ?? '',
    localidade: addr.cidade ?? '',
    uf: addr.uf ?? '',
    complemento: addr.complemento ?? '',
  };
}

export interface GerarReciboDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  transacaoId: string;
  context: GerarReciboContext | null;
  onGerado?: () => void;
}

export function GerarReciboDialog({
  open,
  onOpenChange,
  transacaoId,
  context,
  onGerado,
}: GerarReciboDialogProps) {
  const { csrfToken } = useAppData();

  const [nome, setNome] = useState('');
  const [cpfCnpj, setCpfCnpj] = useState('');
  const [referente, setReferente] = useState('');
  const [cep, setCep] = useState('');
  const [logradouro, setLogradouro] = useState('');
  const [numero, setNumero] = useState('');
  const [bairro, setBairro] = useState('');
  const [localidade, setLocalidade] = useState('');
  const [uf, setUf] = useState('');
  const [complemento, setComplemento] = useState('');

  const [showParceiroHint, setShowParceiroHint] = useState(false);
  const [parceiroHintNome, setParceiroHintNome] = useState('');
  const [docFoundHint, setDocFoundHint] = useState<string | null>(null);

  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);

  const applyPrefill = useCallback(() => {
    if (!context) return;

    const referenteAuto = buildReferente(context.descricao, context.historico_complementar);
    const isEntrada = context.tipo === 'entrada';

    setFieldErrors({});
    setDocFoundHint(null);
    setShowParceiroHint(false);
    setParceiroHintNome('');

    if (context.parceiro) {
      const p = context.parceiro;
      const nomeP = p.nome || p.nome_fantasia || '';
      setNome(nomeP);
      setCpfCnpj(formatCpfCnpjInput(p.cpf_cnpj ?? ''));
      setReferente(referenteAuto);
      const a = mapAddressToForm(p.address ?? null);
      setCep(a.cep);
      setLogradouro(a.logradouro);
      setNumero(a.numero);
      setBairro(a.bairro);
      setLocalidade(a.localidade);
      setUf(a.uf);
      setComplemento(a.complemento);
      setShowParceiroHint(true);
      setParceiroHintNome(nomeP);
    } else {
      setNome('');
      setCpfCnpj('');
      setReferente(referenteAuto);
      setCep('');
      setLogradouro('');
      setNumero('');
      setBairro('');
      setLocalidade('');
      setUf('');
      setComplemento('');
    }
  }, [context]);

  useEffect(() => {
    if (open && context) {
      applyPrefill();
    }
  }, [open, context, applyPrefill]);

  const isEntrada = context?.tipo === 'entrada';
  const valorFormatado =
    context != null
      ? context.valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
      : '0,00';

  async function handleCpfCnpjBlur() {
    setDocFoundHint(null);
    const limpo = digitsOnly(cpfCnpj);
    if (limpo.length < 11) return;

    const tipo = limpo.length <= 11 ? 'cpf' : 'cnpj';

    try {
      const res = await fetch('/financeiro/parceiros/check-documento', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken ?? '',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ tipo, valor: limpo }),
      });
      const data = (await res.json()) as {
        exists?: boolean;
        parceiro?: {
          nome?: string;
          nome_fantasia?: string;
          address?: ReciboEnderecoInput | null;
        };
      };
      if (data.exists && data.parceiro) {
        const nomeExibir = data.parceiro.nome || data.parceiro.nome_fantasia || '';
        setNome(nomeExibir);
        if (data.parceiro.address) {
          const a = mapAddressToForm(data.parceiro.address);
          setCep(a.cep);
          setLogradouro(a.logradouro);
          setNumero(a.numero);
          setBairro(a.bairro);
          setLocalidade(a.localidade);
          setUf(a.uf);
          setComplemento(a.complemento);
        }
        setDocFoundHint(`Dados preenchidos de: ${nomeExibir}`);
      }
    } catch {
      /* silencioso, como no Blade */
    }
  }

  function handleCpfCnpjChange(masked: string) {
    setCpfCnpj(formatCpfCnpjInput(masked));
    setDocFoundHint(null);
    const k = fieldErrors.cpf_cnpj;
    if (k) setFieldErrors((e) => ({ ...e, cpf_cnpj: '' }));
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!context) return;

    setFieldErrors({});
    setSubmitting(true);

    const fd = new FormData();
    fd.append('_token', csrfToken ?? '');
    fd.append('redirect_to_print', 'true');
    fd.append('tipo_transacao', isEntrada ? 'Recebimento' : 'Pagamento');
    fd.append('transacao_id', String(context.id));
    fd.append('data_emissao', context.data_competencia_formatada ?? '');
    fd.append('valor', valorFormatado);
    fd.append('nome', nome);
    fd.append('cpf_cnpj', cpfCnpj);
    fd.append('referente', referente);
    fd.append('cep', cep);
    fd.append('logradouro', logradouro);
    fd.append('numero', numero);
    fd.append('bairro', bairro);
    fd.append('localidade', localidade);
    fd.append('uf', uf);
    fd.append('complemento', complemento);

    try {
      const res = await fetch(`/relatorios/recibos/gerar/${transacaoId}`, {
        method: 'POST',
        body: fd,
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      const json = (await res.json().catch(() => ({}))) as {
        success?: boolean;
        pdf_url?: string;
        message?: string;
        errors?: Record<string, string[]>;
      };

      if (res.status === 422 && json.errors) {
        const next: Record<string, string> = {};
        for (const [k, v] of Object.entries(json.errors)) {
          if (Array.isArray(v) && v[0]) next[k] = v[0];
        }
        setFieldErrors(next);
        notify.error('Validação', 'Corrija os campos destacados.');
        return;
      }

      if (res.status === 409) {
        notify.error('Recibo', json.message ?? 'Já existe um recibo para esta transação.');
        return;
      }

      if (!res.ok) {
        notify.error('Erro', json.message ?? 'Não foi possível gerar o recibo.');
        return;
      }

      if (json.success && json.pdf_url) {
        notify.success('Recibo gerado', 'Abrindo o PDF…');
        window.open(json.pdf_url, '_blank', 'noopener,noreferrer');
        onOpenChange(false);
        onGerado?.();
      } else {
        notify.error('Erro', 'Resposta inesperada do servidor.');
      }
    } catch {
      notify.error('Rede', 'Verifique sua conexão e tente novamente.');
    } finally {
      setSubmitting(false);
    }
  }

  if (!context) return null;

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-[min(100vw-1.5rem,42rem)]  flex flex-col" showCloseButton>
        <DialogHeader>
          <div className="flex flex-wrap items-center gap-2">
            <DialogTitle className="text-start">Gerar recibo</DialogTitle>
            <Badge variant="secondary">#{context.id}</Badge>
            <Badge variant="warning" appearance="light">
              Novo
            </Badge>
          </div>
        </DialogHeader>

        <form onSubmit={handleSubmit} className="flex flex-col min-h-0 flex-1">
          <DialogBody className="px-6 py-4 min-h-0">
            <ScrollArea className="max-h-[min(60vh,28rem)] pr-3 -mr-1">
              <div className="space-y-5">
                <div className="flex flex-wrap items-center gap-3 rounded-lg border border-primary/25 bg-primary/5 px-4 py-3 text-sm">
                  <div className="flex items-center gap-2 shrink-0">
                    {isEntrada ? (
                      <ArrowDownLeft className="size-4 text-emerald-600" />
                    ) : (
                      <ArrowUpRight className="size-4 text-rose-600" />
                    )}
                    <span className="font-semibold text-foreground">
                      {isEntrada ? 'Recebimento' : 'Pagamento'}
                    </span>
                  </div>
                  <div className="flex items-center gap-1.5 text-muted-foreground">
                    <CalendarDays className="size-3.5" />
                    <span>{context.data_competencia_formatada ?? '—'}</span>
                  </div>
                  <div className="flex items-center gap-1.5 font-semibold text-primary">
                    <Wallet className="size-3.5" />
                    <span className="tabular-nums">{fmtCurrency(context.valor)}</span>
                  </div>
                  <div className="flex min-w-0 flex-1 items-start gap-1.5 text-muted-foreground">
                    <span className="truncate" title={context.descricao ?? ''}>
                      {context.descricao ?? '—'}
                    </span>
                  </div>
                </div>

                {showParceiroHint && parceiroHintNome && (
                  <div className="flex gap-3 rounded-lg border border-sky-500/30 bg-sky-500/10 px-3 py-2.5 text-sm">
                    <Sparkles className="size-5 shrink-0 text-sky-600" />
                    <div>
                      <p className="font-medium text-foreground">Dados do parceiro preenchidos</p>
                      <p className="text-xs text-muted-foreground mt-0.5">
                        Preenchido com dados de <strong>{parceiroHintNome}</strong>. Revise antes de emitir.
                      </p>
                    </div>
                  </div>
                )}

                <p className="text-xs text-muted-foreground">
                  Campos com <span className="text-destructive">*</span> são obrigatórios.
                </p>

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-12">
                  <div className="sm:col-span-8 space-y-1.5">
                    <Label htmlFor="recibo-nome">
                      Nome <span className="text-destructive">*</span>
                    </Label>
                    <Input
                      id="recibo-nome"
                      name="nome"
                      value={nome}
                      onChange={(e) => {
                        setNome(e.target.value);
                        if (fieldErrors.nome) setFieldErrors((er) => ({ ...er, nome: '' }));
                      }}
                      placeholder="Nome da empresa ou pessoa"
                      className={cn(fieldErrors.nome && 'border-destructive')}
                    />
                    {fieldErrors.nome && <p className="text-xs text-destructive">{fieldErrors.nome}</p>}
                  </div>
                  <div className="sm:col-span-4 space-y-1.5">
                    <Label htmlFor="recibo-doc">
                      CPF/CNPJ <span className="text-destructive">*</span>
                    </Label>
                    <Input
                      id="recibo-doc"
                      name="cpf_cnpj"
                      value={cpfCnpj}
                      onChange={(e) => handleCpfCnpjChange(e.target.value)}
                      onBlur={handleCpfCnpjBlur}
                      placeholder="Documento"
                      className={cn(
                        fieldErrors.cpf_cnpj && 'border-destructive',
                        docFoundHint && !fieldErrors.cpf_cnpj && 'border-emerald-500/50',
                      )}
                    />
                    {fieldErrors.cpf_cnpj && <p className="text-xs text-destructive">{fieldErrors.cpf_cnpj}</p>}
                    {docFoundHint && !fieldErrors.cpf_cnpj && (
                      <p className="text-xs text-sky-700 dark:text-sky-400">{docFoundHint}</p>
                    )}
                  </div>
                </div>

                <div className="grid grid-cols-12 gap-3">
                  <div className="col-span-12 sm:col-span-4 space-y-1.5">
                    <Label htmlFor="recibo-cep">CEP</Label>
                    <MaskedInput
                      id="recibo-cep"
                      name="cep"
                      maskType="cep"
                      value={cep}
                      onMaskedChange={setCep}
                    />
                  </div>
                  <div className="col-span-12 sm:col-span-8 space-y-1.5">
                    <Label htmlFor="recibo-logradouro">Rua</Label>
                    <Input
                      id="recibo-logradouro"
                      name="logradouro"
                      value={logradouro}
                      onChange={(e) => setLogradouro(e.target.value)}
                      placeholder="Logradouro"
                    />
                  </div>
                  <div className="col-span-12 sm:col-span-3 space-y-1.5">
                    <Label htmlFor="recibo-numero">Nº</Label>
                    <Input
                      id="recibo-numero"
                      name="numero"
                      value={numero}
                      onChange={(e) => setNumero(e.target.value)}
                    />
                  </div>
                  <div className="col-span-12 sm:col-span-5 space-y-1.5">
                    <Label htmlFor="recibo-bairro">Bairro</Label>
                    <Input
                      id="recibo-bairro"
                      name="bairro"
                      value={bairro}
                      onChange={(e) => setBairro(e.target.value)}
                    />
                  </div>
                  <div className="col-span-12 sm:col-span-4 space-y-1.5">
                    <Label htmlFor="recibo-localidade">Cidade</Label>
                    <Input
                      id="recibo-localidade"
                      name="localidade"
                      value={localidade}
                      onChange={(e) => setLocalidade(e.target.value)}
                    />
                  </div>
                  <div className="col-span-12 sm:col-span-3 space-y-1.5">
                    <Label htmlFor="recibo-uf">UF</Label>
                    <Input
                      id="recibo-uf"
                      name="uf"
                      value={uf}
                      onChange={(e) => setUf(e.target.value.toUpperCase().slice(0, 2))}
                      maxLength={2}
                    />
                  </div>
                  <div className="col-span-12 space-y-1.5">
                    <Label htmlFor="recibo-complemento">Complemento</Label>
                    <Input
                      id="recibo-complemento"
                      name="complemento"
                      value={complemento}
                      onChange={(e) => setComplemento(e.target.value)}
                    />
                  </div>
                </div>

                <div className="space-y-1.5">
                  <Label htmlFor="recibo-referente">
                    Referente <span className="text-destructive">*</span>
                  </Label>
                  <Textarea
                    id="recibo-referente"
                    name="referente"
                    value={referente}
                    onChange={(e) => {
                      setReferente(e.target.value);
                      if (fieldErrors.referente) setFieldErrors((er) => ({ ...er, referente: '' }));
                    }}
                    placeholder="Descrição do serviço prestado ou referência do pagamento"
                    rows={3}
                    className={cn(fieldErrors.referente && 'border-destructive')}
                  />
                  {fieldErrors.referente && <p className="text-xs text-destructive">{fieldErrors.referente}</p>}
                </div>

                {(fieldErrors.valor || fieldErrors.data_emissao) && (
                  <p className="text-xs text-destructive">
                    {[fieldErrors.data_emissao, fieldErrors.valor].filter(Boolean).join(' ')}
                  </p>
                )}
              </div>
            </ScrollArea>
          </DialogBody>

          <DialogFooter className="mt-0">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={submitting}>
              Cancelar
            </Button>
            <Button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white border-0" disabled={submitting}>
              {submitting ? <Loader2 className="size-4 animate-spin" /> : <FileSignature className="size-4" />}
              Emitir recibo
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
