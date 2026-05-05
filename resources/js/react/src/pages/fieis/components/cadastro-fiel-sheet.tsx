import { useCallback, useEffect, useMemo, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import {
  Check,
  ClipboardCopy,
  HeartHandshake,
  IdCard,
  Loader2,
  Mail,
  MessageSquareText,
  Phone,
  Plus,
  Trash2,
  UserPlus,
  Users,
  X,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DatePicker } from '@/components/ui/date-picker';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { MaskedInput } from '@/components/common/masked-input';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import { FielAvatarInput } from '@/pages/fieis/components/fiel-avatar-input';
import { CarteirinhaDialog } from '@/pages/fieis/components/carteirinha-dialog';
import { AddressCard, AddressValue, EMPTY_ADDRESS } from '@/components/common/address-card';

// ── Constantes ──────────────────────────────────────────────────────────────

const ESTADO_CIVIL_OPTIONS = [
  'Solteiro(a)',
  'Casado(a)',
  'União Estável',
  'Separado(a)',
  'Divorciado(a)',
  'Viúvo(a)',
  'Outro',
] as const;

const UF_OPTIONS = [
  'AC','AL','AM','AP','BA','CE','DF','ES','GO','MA',
  'MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN',
  'RO','RR','RS','SC','SE','SP','TO',
] as const;

const STATUS_OPTIONS = [
  { value: 'Ativo', label: 'Ativo', dot: 'bg-green-500' },
  { value: 'Inativo', label: 'Inativo', dot: 'bg-muted-foreground' },
  { value: 'Falecido', label: 'Falecido', dot: 'bg-amber-500' },
  { value: 'Mudou-se', label: 'Mudou-se', dot: 'bg-blue-500' },
] as const;

type StatusValue = (typeof STATUS_OPTIONS)[number]['value'];

const MAX_PHONES = 3;

// ── Schema ──────────────────────────────────────────────────────────────────

const phoneSchema = z.object({
  numero: z.string().max(20, 'Telefone inválido.').optional().or(z.literal('')),
  is_whatsapp: z.boolean(),
});

const fielFormSchema = z.object({
  nome_completo: z
    .string()
    .min(1, 'O nome completo é obrigatório.')
    .max(255, 'Nome muito longo.'),
  data_nascimento: z
    .string()
    .min(1, 'A data de nascimento é obrigatória.')
    .regex(/^\d{4}-\d{2}-\d{2}$/, 'Data inválida.')
    .refine((s) => !Number.isNaN(new Date(`${s}T00:00:00`).getTime()), {
      message: 'Data inválida.',
    }),
  cpf: z.string().max(14, 'CPF inválido.').optional().or(z.literal('')),
  rg: z.string().max(20, 'RG inválido.').optional().or(z.literal('')),
  sexo: z.enum(['M', 'F', 'Outro']).optional(),

  // contatos
  email: z
    .string()
    .max(255, 'E-mail muito longo.')
    .optional()
    .or(z.literal(''))
    .refine((v) => !v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), { message: 'E-mail inválido.' }),
  phones: z.array(phoneSchema).max(MAX_PHONES),

  // complementares
  profissao: z.string().max(150, 'Profissão muito longa.').optional().or(z.literal('')),
  estado_civil: z.string().optional().or(z.literal('')),
  nacionalidade: z.string().max(100).optional().or(z.literal('')),
  natural: z.string().max(150, 'Naturalidade muito longa.').optional().or(z.literal('')),
  uf_natural: z.string().max(2).optional().or(z.literal('')),
  titulo_eleitor: z.string().max(20).optional().or(z.literal('')),
  zona: z.string().max(10).optional().or(z.literal('')),
  secao: z.string().max(10).optional().or(z.literal('')),
  observacoes: z.string().max(2000, 'Observações muito longas.').optional().or(z.literal('')),

  // dízimo
  dizimista: z.boolean(),
  codigo_dizimista: z.string().max(50, 'Código muito longo.').optional().or(z.literal('')),

  // status (ciclo de vida do fiel)
  status: z.enum(['Ativo', 'Inativo', 'Falecido', 'Mudou-se']),
});

type FielFormValues = z.infer<typeof fielFormSchema>;

const FIEL_FORM_DEFAULTS: FielFormValues = {
  nome_completo: '',
  data_nascimento: '',
  cpf: '',
  rg: '',
  sexo: undefined,
  email: '',
  phones: [{ numero: '', is_whatsapp: true }],
  profissao: '',
  estado_civil: '',
  nacionalidade: '',
  natural: '',
  uf_natural: '',
  titulo_eleitor: '',
  zona: '',
  secao: '',
  observacoes: '',
  dizimista: false,
  codigo_dizimista: '',
  status: 'Ativo',
};

export interface CadastroFielSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: () => void;
  /** Quando informado, abre em modo edição carregando dados do fiel. */
  editingId?: number | null;
  /**
   * Quando true, exige CPF preenchido e envia `require_cpf=1` ao backend
   * para validar unicidade dentro da company ativa. Útil em fluxos de
   * "quick create" (ex.: a partir do drawer de Dízimo/Doação).
   */
  requireCpf?: boolean;
  /**
   * Callback opcional disparado após criação bem-sucedida, recebendo os
   * dados básicos do fiel recém-cadastrado para que o componente pai
   * possa selecioná-lo automaticamente em outro formulário.
   */
  onCreated?: (fiel: { id: number; nome_completo: string; avatar_url: string | null }) => void;
}

// ── Componente ──────────────────────────────────────────────────────────────

/**
 * Cadastro/Edição de fiel — formulário completo.
 *
 * - Modo criação: `editingId` ausente/null → POST `/relatorios/fieis`
 * - Modo edição:  `editingId` definido    → GET `/api/cadastros/fieis/{id}` + POST `/api/cadastros/fieis/{id}`
 */
export function CadastroFielSheet({
  open,
  onOpenChange,
  onSaved,
  editingId,
  requireCpf = false,
  onCreated,
}: CadastroFielSheetProps) {
  const { csrfToken } = useAppData();
  const isEditing = !!editingId;

  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [existingAvatarUrl, setExistingAvatarUrl] = useState<string | null>(null);
  const [address, setAddress] = useState<AddressValue>(EMPTY_ADDRESS);
  const [submitting, setSubmitting] = useState(false);
  const [loadingData, setLoadingData] = useState(false);
  const [carteirinhaOpen, setCarteirinhaOpen] = useState(false);

  const form = useForm<FielFormValues>({
    resolver: zodResolver(fielFormSchema),
    defaultValues: FIEL_FORM_DEFAULTS,
  });

  const phones = form.watch('phones');
  const dizimista = form.watch('dizimista');

  const resetForm = useCallback(() => {
    form.reset(FIEL_FORM_DEFAULTS);
    setAvatarFile(null);
    setExistingAvatarUrl(null);
    setAddress(EMPTY_ADDRESS);
  }, [form]);

  // Ao abrir em modo edição, carrega os dados do fiel
  useEffect(() => {
    if (!open) return;
    if (!isEditing || !editingId) {
      resetForm();
      return;
    }

    setLoadingData(true);
    fetch(`/api/cadastros/fieis/${editingId}`, {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json())
      .then((res: { success?: boolean; data?: Record<string, unknown> }) => {
        if (!res.success || !res.data) {
          notify.error('Erro', 'Não foi possível carregar os dados do fiel.');
          return;
        }
        const d = res.data;
        form.reset({
          nome_completo:    String(d.nome_completo ?? ''),
          data_nascimento:  String(d.data_nascimento ?? ''),
          cpf:              String(d.cpf ?? ''),
          rg:               String(d.rg ?? ''),
          sexo:             (d.sexo as 'M' | 'F' | 'Outro' | undefined) ?? undefined,
          email:            String(d.email ?? ''),
          phones:           Array.isArray(d.phones) && d.phones.length > 0
            ? (d.phones as { numero: string; is_whatsapp: boolean }[])
            : [{ numero: '', is_whatsapp: true }],
          profissao:        String(d.profissao ?? ''),
          estado_civil:     String(d.estado_civil ?? ''),
          nacionalidade:    String(d.nacionalidade ?? ''),
          natural:          String(d.natural ?? ''),
          uf_natural:       String(d.uf_natural ?? ''),
          titulo_eleitor:   String(d.titulo_eleitor ?? ''),
          zona:             String(d.zona ?? ''),
          secao:            String(d.secao ?? ''),
          observacoes:      String(d.observacoes ?? ''),
          dizimista:        Boolean(d.dizimista),
          codigo_dizimista: String(d.codigo_dizimista ?? ''),
          status:           (d.status as StatusValue) ?? 'Ativo',
        });

        const addr = d.address as Record<string, string> | undefined;
        setAddress({
          cep:        addr?.cep ?? '',
          logradouro: addr?.logradouro ?? '',
          numero:     addr?.numero ?? '',
          bairro:     addr?.bairro ?? '',
          cidade:     addr?.cidade ?? '',
          uf:         addr?.uf ?? '',
        });

        setExistingAvatarUrl(d.avatar_url ? String(d.avatar_url) : null);
      })
      .catch(() => notify.networkError())
      .finally(() => setLoadingData(false));
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, editingId]);

  function handleClose() {
    if (!submitting) onOpenChange(false);
  }

  function addPhone() {
    const current = form.getValues('phones');
    if (current.length >= MAX_PHONES) return;
    form.setValue('phones', [...current, { numero: '', is_whatsapp: false }], { shouldDirty: true });
  }

  function removePhone(index: number) {
    const current = form.getValues('phones');
    if (current.length <= 1) {
      form.setValue('phones', [{ numero: '', is_whatsapp: true }], { shouldDirty: true });
      return;
    }
    form.setValue(
      'phones',
      current.filter((_, i) => i !== index),
      { shouldDirty: true },
    );
  }

  async function copyCodigo() {
    const v = form.getValues('codigo_dizimista');
    if (!v) return;
    try {
      await navigator.clipboard.writeText(v);
      notify.success('Copiado!', 'Código do dizimista copiado para a área de transferência.');
    } catch {
      notify.error('Não foi possível copiar', 'Tente novamente.');
    }
  }

  const handleSubmit = form.handleSubmit(async (data) => {
    if (!csrfToken) {
      notify.reload();
      return;
    }

    // Validação extra de CPF quando o sheet foi aberto com `requireCpf=true`
    // (ex.: quick-create a partir do drawer de Dízimo). A unicidade é
    // validada no backend.
    if (requireCpf && !isEditing) {
      const cpfDigits = (data.cpf ?? '').replace(/\D/g, '');
      if (cpfDigits.length < 11) {
        form.setError('cpf', { message: 'O CPF é obrigatório para este cadastro rápido.' });
        return;
      }
    }

    setSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('_token', csrfToken);
      if (requireCpf && !isEditing) fd.append('require_cpf', '1');

      // Identificação
      fd.append('nome_completo', data.nome_completo.trim());
      fd.append('data_nascimento', data.data_nascimento);
      if (data.cpf?.trim()) fd.append('cpf', data.cpf.trim());
      if (data.rg?.trim()) fd.append('rg', data.rg.trim());
      if (data.sexo) fd.append('sexo', data.sexo);
      if (avatarFile) fd.append('avatar', avatarFile);

      // Endereço (mapeamento legado: logradouro→endereco, uf→estado)
      if (address.cep.trim()) fd.append('cep', address.cep.trim());
      if (address.logradouro.trim()) {
        fd.append('endereco', address.logradouro.trim());
        fd.append('logradouro', address.logradouro.trim());
      }
      if (address.numero.trim()) fd.append('numero', address.numero.trim());
      if (address.bairro.trim()) fd.append('bairro', address.bairro.trim());
      if (address.cidade.trim()) fd.append('cidade', address.cidade.trim());
      if (address.uf) {
        fd.append('estado', address.uf);
        fd.append('uf', address.uf);
      }

      // Contatos: phones[] + email
      const validPhones = data.phones
        .map((p) => ({ ...p, numero: (p.numero ?? '').trim() }))
        .filter((p) => p.numero.length > 0);
      validPhones.forEach((p, i) => {
        fd.append(`phones[${i}][numero]`, p.numero);
        fd.append(`phones[${i}][is_whatsapp]`, p.is_whatsapp ? '1' : '0');
      });
      // Retrocompat com campos legados do Blade
      if (validPhones[0]) fd.append('telefone', validPhones[0].numero);
      if (validPhones[1]) fd.append('telefone_secundario', validPhones[1].numero);
      if (data.email?.trim()) fd.append('email', data.email.trim());

      // Dados complementares
      if (data.profissao?.trim()) fd.append('profissao', data.profissao.trim());
      if (data.estado_civil) fd.append('estado_civil', data.estado_civil);
      if (data.nacionalidade?.trim()) fd.append('nacionalidade', data.nacionalidade.trim());
      if (data.natural?.trim()) fd.append('natural', data.natural.trim());
      if (data.uf_natural?.trim()) fd.append('uf_natural', data.uf_natural.trim());
      if (data.titulo_eleitor?.trim()) fd.append('titulo_eleitor', data.titulo_eleitor.trim());
      if (data.zona?.trim()) fd.append('zona', data.zona.trim());
      if (data.secao?.trim()) fd.append('secao', data.secao.trim());
      if (data.observacoes?.trim()) fd.append('observacoes', data.observacoes.trim());

      // Dízimo
      fd.append('dizimista', data.dizimista ? '1' : '0');
      if (data.codigo_dizimista?.trim()) fd.append('codigo_dizimista', data.codigo_dizimista.trim());

      // Status
      fd.append('status', data.status);

      // ── Requisição: criar (POST) ou atualizar (POST com _method=PUT) ──────
      const url = isEditing
        ? `/api/cadastros/fieis/${editingId}`
        : '/relatorios/fieis';

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: fd,
      });

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
        fiel?: { id: number; nome_completo: string; avatar?: string | null };
      };

      if (res.ok && result.success !== false) {
        notify.success(
          isEditing ? 'Fiel atualizado!' : 'Fiel cadastrado!',
          result.message ?? (isEditing ? 'Dados atualizados com sucesso.' : 'Cadastro concluído com sucesso.'),
        );
        onOpenChange(false);
        onSaved?.();
        if (!isEditing && result.fiel?.id && onCreated) {
          onCreated({
            id: result.fiel.id,
            nome_completo: result.fiel.nome_completo,
            avatar_url: result.fiel.avatar
              ? '/file/' + String(result.fiel.avatar).replace(/^public\//, '')
              : null,
          });
        }
        return;
      }

      if (result.errors) {
        Object.entries(result.errors).forEach(([field, messages]) => {
          const key = field as keyof FielFormValues;
          if (key in FIEL_FORM_DEFAULTS && messages[0]) {
            form.setError(key, { message: messages[0] });
          }
        });
        notify.error('Não foi possível salvar', 'Verifique os campos destacados.');
      } else {
        notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
      }
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  });

  const canAddPhone = phones.length < MAX_PHONES;
  const codigoLength = form.watch('codigo_dizimista')?.length ?? 0;

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" close={false} className="gap-0 p-0" aria-describedby={undefined}>
        <SheetHeader className="flex flex-row items-center justify-between px-5 py-3.5 border-b border-border shrink-0 space-y-0">
          <div className="flex items-center gap-2">
            <Users className="size-5 text-primary" aria-hidden />
            <SheetTitle className="text-base font-semibold">
              {isEditing ? 'Editar fiel' : 'Cadastro de fiel'}
            </SheetTitle>
            {loadingData && <Loader2 className="size-4 animate-spin text-muted-foreground" />}
          </div>
          <Button
            type="button"
            variant="ghost"
            size="icon"
            onClick={handleClose}
            aria-label="Fechar"
            className="size-8"
            disabled={submitting}
          >
            <X className="size-4" />
          </Button>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
            <SheetBody className="p-0 flex-1 overflow-hidden bg-muted min-h-0">
              <ScrollArea className="h-full">
                <div className="p-5 space-y-4">

                  {/* ── Card 1: Dados do fiel ────────────────────────────── */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <UserPlus className="size-3.5 text-muted-foreground" />
                        Dados do fiel
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                      <div className="flex items-start gap-2 sm:gap-3">
                        <div className="flex shrink-0 justify-center sm:justify-start">
                          <FielAvatarInput
                            value={avatarFile}
                            onChange={setAvatarFile}
                            disabled={submitting || loadingData}
                            existingUrl={existingAvatarUrl}
                          />
                        </div>
                        <div className="flex-1 grid grid-cols-1 gap-3 sm:grid-cols-6">
                          <FormField
                            control={form.control}
                            name="nome_completo"
                            render={({ field }) => (
                              <FormItem className="sm:col-span-4">
                                <FormLabel className="text-xs">
                                  Nome completo <span className="text-destructive">*</span>
                                </FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Nome completo" autoFocus disabled={submitting} />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="data_nascimento"
                            render={({ field }) => (
                              <FormItem className="sm:col-span-2">
                                <FormLabel className="text-xs">
                                  Data de nascimento <span className="text-destructive">*</span>
                                </FormLabel>
                                <FormControl>
                                  <DatePicker
                                    value={field.value}
                                    onChange={field.onChange}
                                    placeholder="dd/mm/aaaa"
                                    disabled={submitting}
                                    aria-invalid={!!form.formState.errors.data_nascimento}
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="cpf"
                            render={({ field }) => (
                              <FormItem className="sm:col-span-2">
                                <FormLabel className="text-xs">CPF</FormLabel>
                                <FormControl>
                                  <MaskedInput
                                    maskType="cpf"
                                    value={field.value ?? ''}
                                    onMaskedChange={field.onChange}
                                    placeholder="000.000.000-00"
                                    disabled={submitting}
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="rg"
                            render={({ field }) => (
                              <FormItem className="sm:col-span-2">
                                <FormLabel className="text-xs">RG</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="RG" disabled={submitting} />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="sexo"
                            render={({ field }) => (
                              <FormItem className="sm:col-span-2">
                                <FormLabel className="text-xs">Sexo</FormLabel>
                                <Select
                                  value={field.value}
                                  onValueChange={field.onChange}
                                  disabled={submitting}
                                >
                                  <FormControl>
                                    <SelectTrigger>
                                      <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                  </FormControl>
                                  <SelectContent>
                                    <SelectItem value="M">Masculino</SelectItem>
                                    <SelectItem value="F">Feminino</SelectItem>
                                    <SelectItem value="Outro">Outro</SelectItem>
                                  </SelectContent>
                                </Select>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      </div>
                    </CardContent>
                  </Card>

                  {/* ── Card 2: Endereço (componente reutilizável) ───────── */}
                  <AddressCard
                    value={address}
                    onChange={setAddress}
                    disabled={submitting}
                  />

                  {/* ── Card 3: Contatos ─────────────────────────────────── */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <Phone className="size-3.5 text-muted-foreground" />
                        Contatos
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-3">
                      {/* Telefones */}
                      <div className="space-y-2">
                        <Label className="text-xs">
                          Telefones <span className="text-muted-foreground font-normal">(até {MAX_PHONES})</span>
                        </Label>
                        <div className="space-y-2">
                          {phones.map((_phone, index) => (
                            <PhoneRow
                              key={index}
                              index={index}
                              form={form}
                              disabled={submitting}
                              canRemove={phones.length > 1}
                              onRemove={() => removePhone(index)}
                            />
                          ))}
                          {canAddPhone && (
                            <Button
                              type="button"
                              variant="outline"
                              size="sm"
                              onClick={addPhone}
                              disabled={submitting}
                              className="w-full sm:w-auto"
                            >
                              <Plus className="size-3.5" />
                              Adicionar telefone
                            </Button>
                          )}
                        </div>
                      </div>

                      {/* E-mail */}
                      <FormField
                        control={form.control}
                        name="email"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="text-xs">E-mail</FormLabel>
                            <FormControl>
                              <div className="relative">
                                <Mail className="absolute start-2.5 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
                                <Input
                                  {...field}
                                  type="email"
                                  inputMode="email"
                                  autoComplete="email"
                                  placeholder="email@exemplo.com"
                                  className="ps-8"
                                  disabled={submitting}
                                />
                              </div>
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </CardContent>
                  </Card>

                  {/* ── Card 4: Dados complementares ────────────────────── */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <MessageSquareText className="size-3.5 text-muted-foreground" />
                        Dados complementares
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-3">

                      {/* Linha 1: Profissão + Estado Civil */}
                      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <FormField
                          control={form.control}
                          name="profissao"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel className="text-xs">Profissão</FormLabel>
                              <FormControl>
                                <Input
                                  {...field}
                                  placeholder="Ex.: Engenheiro, Professora…"
                                  disabled={submitting}
                                />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={form.control}
                          name="estado_civil"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel className="text-xs">Estado civil</FormLabel>
                              <Select
                                value={field.value || ''}
                                onValueChange={field.onChange}
                                disabled={submitting}
                              >
                                <FormControl>
                                  <SelectTrigger>
                                    <SelectValue placeholder="Selecione" />
                                  </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                  {ESTADO_CIVIL_OPTIONS.map((opt) => (
                                    <SelectItem key={opt} value={opt}>
                                      {opt}
                                    </SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      {/* Linha 2: Nacionalidade + Naturalidade + UF */}
                      <div className="grid grid-cols-1 gap-3 sm:grid-cols-6">
                        <FormField
                          control={form.control}
                          name="nacionalidade"
                          render={({ field }) => (
                            <FormItem className="sm:col-span-2">
                              <FormLabel className="text-xs">Nacionalidade</FormLabel>
                              <FormControl>
                                <Input {...field} placeholder="Ex.: Brasileira" disabled={submitting} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={form.control}
                          name="natural"
                          render={({ field }) => (
                            <FormItem className="sm:col-span-3">
                              <FormLabel className="text-xs">Naturalidade (cidade)</FormLabel>
                              <FormControl>
                                <Input {...field} placeholder="Ex.: São Paulo" disabled={submitting} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={form.control}
                          name="uf_natural"
                          render={({ field }) => (
                            <FormItem className="sm:col-span-1">
                              <FormLabel className="text-xs">UF</FormLabel>
                              <Select
                                value={field.value || ''}
                                onValueChange={field.onChange}
                                disabled={submitting}
                              >
                                <FormControl>
                                  <SelectTrigger>
                                    <SelectValue placeholder="UF" />
                                  </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                  {UF_OPTIONS.map((uf) => (
                                    <SelectItem key={uf} value={uf}>{uf}</SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      {/* Linha 3: Título de Eleitor + Zona + Seção */}
                      <div className="grid grid-cols-1 gap-3 sm:grid-cols-6">
                        <FormField
                          control={form.control}
                          name="titulo_eleitor"
                          render={({ field }) => (
                            <FormItem className="sm:col-span-4">
                              <FormLabel className="text-xs">Título de eleitor</FormLabel>
                              <FormControl>
                                <Input
                                  {...field}
                                  placeholder="Número do título"
                                  disabled={submitting}
                                />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={form.control}
                          name="zona"
                          render={({ field }) => (
                            <FormItem className="sm:col-span-1">
                              <FormLabel className="text-xs">Zona</FormLabel>
                              <FormControl>
                                <Input {...field} placeholder="Ex.: 001" disabled={submitting} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={form.control}
                          name="secao"
                          render={({ field }) => (
                            <FormItem className="sm:col-span-1">
                              <FormLabel className="text-xs">Seção</FormLabel>
                              <FormControl>
                                <Input {...field} placeholder="Ex.: 001" disabled={submitting} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      {/* Observações */}
                      <FormField
                        control={form.control}
                        name="observacoes"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="text-xs">Observações</FormLabel>
                            <FormControl>
                              <Textarea
                                {...field}
                                rows={3}
                                placeholder="Informações adicionais relevantes…"
                                disabled={submitting}
                                className="resize-none"
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </CardContent>
                  </Card>

                  {/* ── Card 5: Dízimo & Carteirinha ────────────────────── */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <HeartHandshake className="size-3.5 text-muted-foreground" />
                        Dízimo & Carteirinha
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-3">
                      <FormField
                        control={form.control}
                        name="dizimista"
                        render={({ field }) => (
                          <FormItem className="flex items-center justify-between rounded-md border border-border bg-accent/20 px-3 py-2">
                            <div>
                              <Label className="text-sm font-medium">É dizimista?</Label>
                              <p className="text-xs text-muted-foreground mt-0.5">
                                Marque para registrar este fiel como dizimista ativo.
                              </p>
                            </div>
                            <FormControl>
                              <Switch
                                checked={field.value}
                                onCheckedChange={field.onChange}
                                disabled={submitting}
                              />
                            </FormControl>
                          </FormItem>
                        )}
                      />

                      {dizimista && (
                        <div className="space-y-3 pt-1">
                          <div className="grid grid-cols-12 gap-3">
                            <FormField
                              control={form.control}
                              name="codigo_dizimista"
                              render={({ field }) => (
                                <FormItem className="col-span-12 sm:col-span-7">
                                  <FormLabel className="text-xs">Código do dizimista</FormLabel>
                                  <FormControl>
                                    <div className="relative">
                                      <IdCard className="absolute start-2.5 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
                                      <Input
                                        {...field}
                                        placeholder="Ex.: D-0001 (deixe em branco para gerar automaticamente)"
                                        className="ps-8 pe-9"
                                        disabled={submitting}
                                      />
                                      {codigoLength > 0 && (
                                        <button
                                          type="button"
                                          onClick={copyCodigo}
                                          title="Copiar código"
                                          className="absolute end-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                        >
                                          <ClipboardCopy className="size-3.5" />
                                        </button>
                                      )}
                                    </div>
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />

                            <div className="col-span-12 sm:col-span-5 flex flex-col justify-end">
                              <Button
                                type="button"
                                variant="outline"
                                disabled={!isEditing}
                                onClick={() => {
                                  if (!editingId) return;
                                  setCarteirinhaOpen(true);
                                }}
                                title={isEditing ? 'Abrir carteirinha do dizimista' : 'Disponível após o cadastro ser salvo'}
                                className="gap-1.5"
                              >
                                <IdCard className="size-3.5" />
                                Ver carteirinha
                              </Button>
                              <span className="text-[11px] text-muted-foreground mt-1.5 leading-tight">
                                {isEditing
                                  ? 'Visualize, imprima ou baixe o PDF da carteirinha.'
                                  : 'A carteirinha (com QR Code) será gerada após o cadastro ser salvo.'}
                              </span>
                            </div>
                          </div>
                        </div>
                      )}
                    </CardContent>
                  </Card>

                </div>
              </ScrollArea>
            </SheetBody>

            {/* ── Footer: situação + ações ─────────────────────────────── */}
            <SheetFooter className="border-t border-border px-5 py-3 shrink-0 sm:space-x-0">
              <div className="flex w-full items-center justify-between gap-2">
                <FormField
                  control={form.control}
                  name="status"
                  render={({ field }) => (
                    <Select
                      value={field.value}
                      onValueChange={(v) => field.onChange(v as StatusValue)}
                      disabled={submitting}
                    >
                      <SelectTrigger className="w-36 h-8 text-xs">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent className="z-70">
                        {STATUS_OPTIONS.map((opt) => (
                          <SelectItem key={opt.value} value={opt.value}>
                            <span className="flex items-center gap-1.5">
                              <span className={cn('size-1.5 rounded-full shrink-0', opt.dot)} />
                              {opt.label}
                            </span>
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  )}
                />

                <div className="flex items-center gap-2">
                  <Button type="button" variant="outline" onClick={handleClose} disabled={submitting}>
                    <X className="size-4" />
                    Cancelar
                  </Button>
                  <Button
                    type="submit"
                    className="bg-blue-600 hover:bg-blue-700 text-white border-0"
                    disabled={submitting}
                  >
                    {submitting ? (
                      <>
                        <Loader2 className="size-4 animate-spin shrink-0" />
                        Salvando…
                      </>
                    ) : (
                      <>
                        <Check className="size-4" />
                        {isEditing ? 'Salvar alterações' : 'Cadastrar Fiel'}
                      </>
                    )}
                  </Button>
                </div>
              </div>
            </SheetFooter>
          </form>
        </Form>
      </SheetContent>

      <CarteirinhaDialog
        open={carteirinhaOpen}
        onOpenChange={setCarteirinhaOpen}
        fielId={editingId ?? null}
      />
    </Sheet>
  );
}

// ── Sub-componente: linha de telefone ───────────────────────────────────────

interface PhoneRowProps {
  index: number;
  form: ReturnType<typeof useForm<FielFormValues>>;
  disabled: boolean;
  canRemove: boolean;
  onRemove: () => void;
}

function PhoneRow({ index, form, disabled, canRemove, onRemove }: PhoneRowProps) {
  const placeholder = useMemo(() => '(00) 00000-0000', []);

  return (
    <div className="flex items-center gap-2">
      <div className="flex-1 min-w-0">
        <FormField
          control={form.control}
          name={`phones.${index}.numero` as const}
          render={({ field }) => (
            <FormItem className="space-y-0">
              <FormControl>
                <MaskedInput
                  maskType="telefone"
                  value={field.value ?? ''}
                  onMaskedChange={field.onChange}
                  placeholder={placeholder}
                  disabled={disabled}
                />
              </FormControl>
              <FormMessage className="mt-1" />
            </FormItem>
          )}
        />
      </div>

      <FormField
        control={form.control}
        name={`phones.${index}.is_whatsapp` as const}
        render={({ field }) => (
          <label
            className={cn(
              'flex items-center gap-1.5 rounded-md border border-border bg-background px-2.5 h-8.5 text-xs font-medium cursor-pointer select-none',
              'hover:bg-accent transition-colors shrink-0',
              field.value && 'border-green-500/40 bg-green-50 text-green-700 dark:bg-green-950 dark:text-green-300',
              disabled && 'cursor-not-allowed opacity-60',
            )}
          >
            <input
              type="checkbox"
              className="sr-only"
              checked={field.value}
              onChange={(e) => field.onChange(e.target.checked)}
              disabled={disabled}
            />
            <span
              className={cn(
                'size-1.5 rounded-full shrink-0',
                field.value ? 'bg-green-500' : 'bg-muted-foreground/40',
              )}
            />
            WhatsApp
          </label>
        )}
      />

      <Button
        type="button"
        variant="ghost"
        size="icon"
        className="size-8 shrink-0 text-muted-foreground hover:text-destructive"
        onClick={onRemove}
        disabled={disabled || !canRemove}
        aria-label="Remover telefone"
        title={canRemove ? 'Remover telefone' : 'Pelo menos um telefone deve permanecer'}
      >
        <Trash2 className="size-3.5" />
      </Button>
    </div>
  );
}
