import { useEffect, useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import {
  Camera,
  ChevronDown,
  Loader2,
  UserRound,
  Users,
  X,
} from 'lucide-react';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { MaskedInput } from '@/components/common/masked-input';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { DatePicker } from '@/components/ui/date-picker';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

// ── Tipos ─────────────────────────────────────────────────────────────────────

interface IFormationStage {
  id: number;
  name: string;
  slug: string;
  sort_order: number;
}

interface ICompanyOption {
  id: number;
  name: string;
}

interface IReligiousRole {
  id: number;
  name: string;
  slug: string;
}

interface IFormData {
  formation_stages: IFormationStage[];
  companies: ICompanyOption[];
  religious_roles: IReligiousRole[];
}

// ── Zod Schema ────────────────────────────────────────────────────────────────

const membroFormSchema = z.object({
  nome: z.string().min(1, 'O nome é obrigatório.').max(255, 'Nome muito longo.'),
  current_stage_id: z.string().min(1, 'A etapa de formação é obrigatória.'),
  data_nascimento: z.string().min(1, 'A data de nascimento é obrigatória.'),
  funcao: z.string().max(50).optional().or(z.literal('')),
  provincia: z.string().max(50).optional().or(z.literal('')),
  cpf: z.string().max(20).optional().or(z.literal('')),
  religious_role_id: z.string().optional().or(z.literal('')),
  cep: z.string().max(10).optional().or(z.literal('')),
  bairro: z.string().max(255).optional().or(z.literal('')),
  logradouro: z.string().max(255).optional().or(z.literal('')),
  numero: z.string().max(20).optional().or(z.literal('')),
  localidade: z.string().max(255).optional().or(z.literal('')),
  uf: z.string().max(2).optional().or(z.literal('')),
  observacoes: z.string().optional().or(z.literal('')),
  disponivel_todas_casas: z.boolean(),
});

type MembroFormValues = z.infer<typeof membroFormSchema>;

const DEFAULTS: MembroFormValues = {
  nome: '',
  current_stage_id: '',
  data_nascimento: '',
  funcao: '',
  provincia: '',
  cpf: '',
  religious_role_id: '',
  cep: '',
  bairro: '',
  logradouro: '',
  numero: '',
  localidade: '',
  uf: '',
  observacoes: '',
  disponivel_todas_casas: true,
};

function onlyDigits(v?: string | null) {
  return (v ?? '').replace(/\D/g, '');
}

// Slugs das etapas que permitem atribuição de função religiosa (votos perpétuos)
const VOTOS_PERPETUOS_SLUGS = ['votos-perpetuos', 'votos_perpetuos', 'perpetua', 'perpetuo'];

function isVotosPerpetuos(slug: string): boolean {
  return VOTOS_PERPETUOS_SLUGS.some((s) => slug.toLowerCase().includes(s.replace('-', '')));
}

// ── Tipo de estágio do formulário ──────────────────────────────────────────────

interface StageFormValues {
  start_date: string;
  end_date: string;
  company_id: string;
  is_current: boolean;
}

// ── Props ─────────────────────────────────────────────────────────────────────

export interface MembroFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: () => void;
  editingId?: number | null;
}

// ── Estados/UF ────────────────────────────────────────────────────────────────

const ESTADOS_BR = [
  { value: 'AC', label: 'Acre' },
  { value: 'AL', label: 'Alagoas' },
  { value: 'AP', label: 'Amapá' },
  { value: 'AM', label: 'Amazonas' },
  { value: 'BA', label: 'Bahia' },
  { value: 'CE', label: 'Ceará' },
  { value: 'DF', label: 'Distrito Federal' },
  { value: 'ES', label: 'Espírito Santo' },
  { value: 'GO', label: 'Goiás' },
  { value: 'MA', label: 'Maranhão' },
  { value: 'MT', label: 'Mato Grosso' },
  { value: 'MS', label: 'Mato Grosso do Sul' },
  { value: 'MG', label: 'Minas Gerais' },
  { value: 'PA', label: 'Pará' },
  { value: 'PB', label: 'Paraíba' },
  { value: 'PR', label: 'Paraná' },
  { value: 'PE', label: 'Pernambuco' },
  { value: 'PI', label: 'Piauí' },
  { value: 'RJ', label: 'Rio de Janeiro' },
  { value: 'RN', label: 'Rio Grande do Norte' },
  { value: 'RS', label: 'Rio Grande do Sul' },
  { value: 'RO', label: 'Rondônia' },
  { value: 'RR', label: 'Roraima' },
  { value: 'SC', label: 'Santa Catarina' },
  { value: 'SP', label: 'São Paulo' },
  { value: 'SE', label: 'Sergipe' },
  { value: 'TO', label: 'Tocantins' },
  { value: 'EX', label: 'Estrangeiro' },
];

// ── Componente principal ──────────────────────────────────────────────────────

export function MembroFormSheet({
  open,
  onOpenChange,
  onSaved,
  editingId,
}: MembroFormSheetProps) {
  const { csrfToken } = useAppData();
  const isEditing = !!editingId;

  const form = useForm<MembroFormValues>({
    resolver: zodResolver(membroFormSchema),
    defaultValues: DEFAULTS,
  });
  const {
    formState: { isSubmitting },
  } = form;

  // Avatar
  const avatarInputRef = useRef<HTMLInputElement>(null);
  const [avatarPreview, setAvatarPreview] = useState('');
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [avatarRemove, setAvatarRemove] = useState(false);

  // CEP lookup
  const [loadingCep, setLoadingCep] = useState(false);

  // Dados de lookup
  const [loadingData, setLoadingData] = useState(false);
  const [formData, setFormData] = useState<IFormData>({
    formation_stages: [],
    companies: [],
    religious_roles: [],
  });

  // Etapas formativas — mapa por slug
  const [stageForms, setStageForms] = useState<Record<string, StageFormValues>>({});

  // Stage selecionada (para decidir quais blocos exibir e se mostra função religiosa)
  const watchedStageId = form.watch('current_stage_id');
  const selectedStage = formData.formation_stages?.find((s) => String(s.id) === watchedStageId);
  const showReligiousRole = selectedStage ? isVotosPerpetuos(selectedStage.slug) : false;

  // Etapas visíveis = todas até a ordem da etapa atual (inclusive)
  const visibleStages = selectedStage
    ? formData.formation_stages.filter((s) => s.sort_order <= selectedStage.sort_order)
    : [];

  // ── Avatar ─────────────────────────────────────────────────────────────────

  function handleAvatarChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setAvatarFile(file);
    setAvatarRemove(false);
    const reader = new FileReader();
    reader.onload = (ev) => setAvatarPreview(ev.target?.result as string);
    reader.readAsDataURL(file);
    e.target.value = '';
  }

  function handleAvatarRemove(e: React.MouseEvent) {
    e.stopPropagation();
    setAvatarPreview('');
    setAvatarFile(null);
    setAvatarRemove(true);
  }

  // ── Busca de CEP via ViaCEP ─────────────────────────────────────────────────

  async function handleCepLookup(rawCep: string) {
    const cep = onlyDigits(rawCep);
    if (cep.length !== 8) return;

    setLoadingCep(true);
    try {
      const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
      const data = (await res.json()) as {
        erro?: boolean;
        logradouro?: string;
        bairro?: string;
        localidade?: string;
        uf?: string;
      };

      if (!res.ok || data.erro) {
        notify.warning('CEP não encontrado', 'Confira o CEP informado.');
        return;
      }

      form.setValue('logradouro', data.logradouro ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('bairro', data.bairro ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('localidade', data.localidade ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('uf', (data.uf ?? '').toUpperCase(), { shouldDirty: true, shouldValidate: true });
    } catch {
      notify.networkError();
    } finally {
      setLoadingCep(false);
    }
  }

  // ── Carrega dados de lookup ao abrir ───────────────────────────────────────

  useEffect(() => {
    if (!open) return;

    fetch('/api/secretary/form-data', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then((json) => {
        if (json.formation_stages) setFormData(json);
      })
      .catch(() => {});
  }, [open]);

  // ── Carrega dados ao editar ───────────────────────────────────────────────

  useEffect(() => {
    if (!open) return;

    if (editingId) {
      setLoadingData(true);
      fetch(`/api/secretary/membros/${editingId}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      })
        .then((r) => r.json())
        .then((json) => {
          const d = json.member ?? json;
          if (!d.id) throw new Error('Dados inválidos');

          form.reset({
            nome: d.nome ?? '',
            current_stage_id: d.current_stage_id ? String(d.current_stage_id) : '',
            data_nascimento: d.data_nascimento ?? '',
            funcao: d.funcao ?? '',
            provincia: d.provincia ?? '',
            cpf: d.cpf ?? '',
            religious_role_id: d.religious_role_id ? String(d.religious_role_id) : '',
            cep: d.endereco?.cep ?? '',
            bairro: d.endereco?.bairro ?? '',
            logradouro: d.endereco?.rua ?? '',
            numero: d.endereco?.numero ?? '',
            localidade: d.endereco?.cidade ?? '',
            uf: d.endereco?.uf ?? '',
            observacoes: d.observacoes ?? '',
            disponivel_todas_casas: d.disponivel_todas_casas ?? true,
          });

          const hasRealAvatar =
            d.avatar_url && !d.avatar_url.includes('blank');
          setAvatarPreview(hasRealAvatar ? d.avatar_url : '');
          setAvatarFile(null);
          setAvatarRemove(false);

          // Preenche stageForms com os períodos carregados
          if (Array.isArray(d.formation_periods)) {
            const stageMap: Record<string, StageFormValues> = {};
            for (const period of d.formation_periods) {
              // Precisamos encontrar o slug pelo formation_stage_id
              // O slug será resolvido quando formData já estiver carregado
              stageMap[String(period.formation_stage_id)] = {
                start_date: period.start_date ?? '',
                end_date: period.end_date ?? '',
                company_id: period.company_id ? String(period.company_id) : '',
                is_current: !!period.is_current,
              };
            }
            setStageForms(stageMap);
          }
        })
        .catch(() =>
          notify.error('Erro', 'Não foi possível carregar os dados do membro.'),
        )
        .finally(() => setLoadingData(false));
    } else {
      form.reset(DEFAULTS);
      setAvatarPreview('');
      setAvatarFile(null);
      setAvatarRemove(false);
      setStageForms({});
    }
  }, [open, editingId]);

  // ── Helpers de stageForms ─────────────────────────────────────────────────

  function getStageForm(stageSlug: string): StageFormValues {
    return (
      stageForms[stageSlug] ?? {
        start_date: '',
        end_date: '',
        company_id: '',
        is_current: false,
      }
    );
  }

  function setStageField<K extends keyof StageFormValues>(
    stageSlug: string,
    field: K,
    value: StageFormValues[K],
  ) {
    setStageForms((prev) => ({
      ...prev,
      [stageSlug]: {
        ...getStageForm(stageSlug),
        [field]: value,
      },
    }));
  }

  // ── Submissão ─────────────────────────────────────────────────────────────

  type SaveMode = 'default' | 'clone' | 'clear';

  async function submitForm(data: MembroFormValues, mode: SaveMode = 'default') {
    if (!csrfToken) {
      notify.reload();
      return;
    }

    try {
      const fd = new FormData();
      fd.append('nome', data.nome.trim());
      fd.append('current_stage_id', data.current_stage_id);
      // Converte ISO (YYYY-MM-DD) para formato brasileiro esperado pelo backend (d/m/Y)
      if (data.data_nascimento) {
        const [y, m, d] = data.data_nascimento.split('-');
        fd.append('data_nascimento', `${d}/${m}/${y}`);
      }
      if (data.funcao) fd.append('funcao', data.funcao);
      if (data.provincia) fd.append('provincia', data.provincia);
      if (data.cpf) fd.append('cpf', data.cpf);
      if (data.religious_role_id) fd.append('religious_role_id', data.religious_role_id);
      if (data.cep) fd.append('cep', data.cep);
      if (data.bairro) fd.append('bairro', data.bairro);
      if (data.logradouro) fd.append('logradouro', data.logradouro);
      if (data.numero) fd.append('numero', data.numero);
      if (data.localidade) fd.append('localidade', data.localidade);
      if (data.uf) fd.append('uf', data.uf);
      if (data.observacoes) fd.append('observacoes', data.observacoes);
      fd.append('disponivel_todas_casas', data.disponivel_todas_casas ? '1' : '0');

      if (avatarFile) {
        fd.append('avatar', avatarFile);
      } else if (avatarRemove && isEditing) {
        fd.append('avatar_remove', '1');
      }

      // Serializa os períodos de formação
      const stagesPayload: Record<
        string,
        { start_date: string; end_date: string; company_id: string; is_current: number }
      > = {};
      for (const stage of visibleStages) {
        const sf = getStageForm(stage.slug);
        if (sf.start_date || sf.company_id) {
          stagesPayload[stage.slug] = {
            start_date: sf.start_date,
            end_date: sf.end_date,
            company_id: sf.company_id,
            is_current: sf.is_current ? 1 : 0,
          };
        }
      }
      fd.append('stages_json', JSON.stringify(stagesPayload));

      const url = isEditing
        ? `/api/secretary/membros/${editingId}`
        : '/api/secretary/membros';
      if (isEditing) fd.append('_method', 'PUT');

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
      };

      if (res.ok && result.success !== false) {
        notify.success(
          isEditing ? 'Membro atualizado!' : 'Membro cadastrado!',
          result.message ?? `${data.nome.trim()} foi salvo com sucesso.`,
        );

        if (mode === 'clear') {
          form.reset(DEFAULTS);
          setAvatarPreview('');
          setAvatarFile(null);
          setAvatarRemove(false);
          setStageForms({});
        } else if (mode === 'clone') {
          // Mantém os dados, mas fecha e avisa
          onOpenChange(false);
        } else {
          onOpenChange(false);
        }

        onSaved?.();
      } else {
        if (result.errors) {
          Object.entries(result.errors).forEach(([field, messages]) => {
            const key = field as keyof MembroFormValues;
            if (messages[0]) form.setError(key, { message: messages[0] });
          });
        } else {
          notify.error(
            'Não foi possível salvar',
            result.message ?? 'Verifique os dados e tente novamente.',
          );
        }
      }
    } catch {
      notify.networkError();
    }
  }

  const handleSubmit = form.handleSubmit((data) => submitForm(data, 'default'));

  // ── Render ────────────────────────────────────────────────────────────────

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        overlayClassName="z-[55]"
        className={cn(
          'z-60 gap-0 lg:w-200 sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '**:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5',
        )}
      >
        <SheetHeader className="border-b px-5 py-4 shrink-0">
          <SheetTitle className="text-base font-semibold">
            {isEditing ? 'Editar Membro' : 'Novo Membro'}
          </SheetTitle>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
            <SheetBody className="grow p-0 flex flex-col min-h-0">
              {loadingData ? (
                <div className="flex flex-1 items-center justify-center">
                  <Loader2 className="size-6 animate-spin text-muted-foreground" />
                </div>
              ) : (
                <ScrollArea className="flex-1 px-5 py-5">
                  <div className="space-y-5">

                    {/* ── Dados do Membro ─────────────────────────────── */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <Users className="size-3.5 text-muted-foreground" />
                          Dados do Membro
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4 space-y-4">

                        {/* Avatar + Nome */}
                        <div className="flex gap-5">

                          {/* Avatar */}
                          <div className="flex flex-col items-center gap-2 shrink-0">
                            <input
                              ref={avatarInputRef}
                              type="file"
                              accept="image/*"
                              className="hidden"
                              onChange={handleAvatarChange}
                            />
                            <div
                              className="relative size-16 cursor-pointer"
                              onClick={() => avatarInputRef.current?.click()}
                            >
                              <div className="size-16 rounded-full overflow-hidden border-2 border-border bg-accent flex items-center justify-center">
                                {avatarPreview ? (
                                  <img
                                    src={avatarPreview}
                                    alt="Foto do membro"
                                    className="size-full object-cover"
                                  />
                                ) : (
                                  <UserRound className="size-8 text-muted-foreground" />
                                )}
                              </div>
                              <div className="absolute bottom-0 left-0 right-0 h-5 bg-black/25 flex items-center justify-center rounded-b-full pointer-events-none">
                                <Camera className="size-3 text-white" />
                              </div>
                              {avatarPreview && (
                                <button
                                  type="button"
                                  onClick={handleAvatarRemove}
                                  className="absolute -top-0.5 -end-0.5 z-10 size-5 flex items-center justify-center rounded-full border border-border bg-background shadow-sm"
                                >
                                  <X className="size-3 text-muted-foreground hover:text-foreground" />
                                </button>
                              )}
                            </div>
                            <span className="text-xs text-muted-foreground">Foto</span>
                          </div>

                          {/* Nome + Etapa + Data Nasc. */}
                          <div className="flex-1 space-y-3">
                            <FormField
                              control={form.control}
                              name="nome"
                              render={({ field }) => (
                                <FormItem>
                                  <FormLabel className="text-xs">
                                    Nome Completo <span className="text-destructive">*</span>
                                  </FormLabel>
                                  <FormControl>
                                    <Input
                                      {...field}
                                      placeholder="Digite o nome do membro"
                                      autoFocus
                                    />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />

                            <div className="grid grid-cols-2 gap-3">
                              <FormField
                                control={form.control}
                                name="current_stage_id"
                                render={({ field }) => (
                                  <FormItem>
                                    <FormLabel className="text-xs">
                                      Etapa de Formação <span className="text-destructive">*</span>
                                    </FormLabel>
                                    <Select
                                      value={field.value}
                                      onValueChange={field.onChange}
                                    >
                                      <FormControl>
                                        <SelectTrigger>
                                          <SelectValue placeholder="Selecione a etapa" />
                                        </SelectTrigger>
                                      </FormControl>
                                      <SelectContent className="z-[70]">
                                        {formData.formation_stages.map((stage) => (
                                          <SelectItem key={stage.id} value={String(stage.id)}>
                                            {stage.name}
                                          </SelectItem>
                                        ))}
                                      </SelectContent>
                                    </Select>
                                    <FormMessage />
                                  </FormItem>
                                )}
                              />

                              <FormField
                                control={form.control}
                                name="data_nascimento"
                                render={({ field }) => (
                                  <FormItem>
                                    <FormLabel className="text-xs">
                                      Data de Nascimento <span className="text-destructive">*</span>
                                    </FormLabel>
                                    <FormControl>
                                      <DatePicker
                                        value={field.value}
                                        onChange={field.onChange}
                                        placeholder="dd/mm/aaaa"
                                        align="start"
                                      />
                                    </FormControl>
                                    <FormMessage />
                                  </FormItem>
                                )}
                              />
                            </div>
                          </div>
                        </div>

                        {/* ID Ordem / Província / CPF */}
                        <div className="grid grid-cols-3 gap-3">
                          <FormField
                            control={form.control}
                            name="funcao"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">ID da Ordem</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Código" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="provincia"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">ID da Província</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Código" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="cpf"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">CPF</FormLabel>
                                <FormControl>
                                  <MaskedInput
                                    maskType="cpf"
                                    value={field.value}
                                    onMaskedChange={field.onChange}
                                    placeholder="000.000.000-00"
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>

                      </CardContent>
                    </Card>

                    {/* ── Etapas Formativas ───────────────────────────── */}
                    {visibleStages.length > 0 && (
                      <Card className="rounded-md">
                        <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                          <CardTitle className="text-2sm">Etapas Formativas</CardTitle>
                        </CardHeader>
                        <CardContent className="pt-4 space-y-5">
                          {visibleStages.map((stage, idx) => {
                            const sf = getStageForm(stage.slug);
                            return (
                              <div key={stage.id}>
                                {idx > 0 && <Separator className="mb-5" />}

                                {/* Header da etapa */}
                                <div className="flex items-center justify-between mb-3">
                                  <div className="flex items-center gap-2">
                                    <span className="inline-flex items-center justify-center size-5 rounded-full bg-primary text-primary-foreground text-xs font-semibold">
                                      {stage.sort_order}
                                    </span>
                                    <span className="text-sm font-semibold">{stage.name}</span>
                                  </div>
                                  <div className="flex items-center gap-2">
                                    <Label
                                      htmlFor={`is_current_${stage.slug}`}
                                      className="text-xs text-muted-foreground cursor-pointer"
                                    >
                                      Período Atual
                                    </Label>
                                    <Switch
                                      id={`is_current_${stage.slug}`}
                                      checked={sf.is_current}
                                      onCheckedChange={(v) =>
                                        setStageField(stage.slug, 'is_current', v)
                                      }
                                      size="sm"
                                    />
                                  </div>
                                </div>

                                {/* Campos da etapa */}
                                <div className="grid grid-cols-3 gap-3">
                                  <div>
                                    <Label className="text-xs">Data Inicial</Label>
                                    <DatePicker
                                      value={sf.start_date}
                                      onChange={(v) =>
                                        setStageField(stage.slug, 'start_date', v)
                                      }
                                      placeholder="dd/mm/aaaa"
                                      align="start"
                                    />
                                  </div>
                                  <div>
                                    <Label className="text-xs">Data Final</Label>
                                    <DatePicker
                                      value={sf.end_date}
                                      onChange={(v) =>
                                        setStageField(stage.slug, 'end_date', v)
                                      }
                                      placeholder="dd/mm/aaaa"
                                      align="start"
                                    />
                                  </div>
                                  <div>
                                    <Label className="text-xs">Local</Label>
                                    <Select
                                      value={sf.company_id}
                                      onValueChange={(v) =>
                                        setStageField(stage.slug, 'company_id', v)
                                      }
                                    >
                                      <SelectTrigger>
                                        <SelectValue placeholder="Selecione..." />
                                      </SelectTrigger>
                                      <SelectContent className="z-[70]">
                                        {formData.companies.map((c) => (
                                          <SelectItem key={c.id} value={String(c.id)}>
                                            {c.name}
                                          </SelectItem>
                                        ))}
                                      </SelectContent>
                                    </Select>
                                  </div>
                                </div>
                              </div>
                            );
                          })}

                          {/* Função Religiosa (apenas para Votos Perpétuos) */}
                          {showReligiousRole && formData.religious_roles.length > 0 && (
                            <>
                              <Separator />
                              <div>
                                <div className="mb-3">
                                  <p className="text-sm font-semibold">Função na Ordem</p>
                                  <p className="text-xs text-muted-foreground">
                                    Disponível apenas para religiosos com Votos Perpétuos
                                  </p>
                                </div>
                                <FormField
                                  control={form.control}
                                  name="religious_role_id"
                                  render={({ field }) => (
                                    <FormItem>
                                      <FormControl>
                                        <RadioGroup
                                          value={field.value}
                                          onValueChange={field.onChange}
                                          className="flex flex-wrap gap-4"
                                        >
                                          {formData.religious_roles.map((role) => (
                                            <div
                                              key={role.id}
                                              className="flex items-center gap-2"
                                            >
                                              <RadioGroupItem
                                                value={String(role.id)}
                                                id={`role_${role.id}`}
                                              />
                                              <Label
                                                htmlFor={`role_${role.id}`}
                                                className="text-sm cursor-pointer"
                                              >
                                                {role.name}
                                              </Label>
                                            </div>
                                          ))}
                                        </RadioGroup>
                                      </FormControl>
                                      <FormMessage />
                                    </FormItem>
                                  )}
                                />
                              </div>
                            </>
                          )}
                        </CardContent>
                      </Card>
                    )}

                    {/* ── Endereço de Origem ──────────────────────────── */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm">Endereço de Origem</CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4 space-y-3">
                        <div className="grid grid-cols-4 gap-3">
                          <FormField
                            control={form.control}
                            name="cep"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">CEP</FormLabel>
                                <div className="relative">
                                  <FormControl>
                                    <MaskedInput
                                      maskType="cep"
                                      value={field.value}
                                      onMaskedChange={(v) => {
                                        field.onChange(v);
                                        handleCepLookup(v);
                                      }}
                                      placeholder="00000-000"
                                      className="pe-8"
                                    />
                                  </FormControl>
                                  {loadingCep && (
                                    <Loader2 className="absolute end-2.5 top-1/2 -translate-y-1/2 size-3.5 animate-spin text-muted-foreground" />
                                  )}
                                </div>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="bairro"
                            render={({ field }) => (
                              <FormItem className="col-span-3">
                                <FormLabel className="text-xs">Bairro</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Bairro" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>

                        <div className="grid grid-cols-4 gap-3">
                          <FormField
                            control={form.control}
                            name="logradouro"
                            render={({ field }) => (
                              <FormItem className="col-span-3">
                                <FormLabel className="text-xs">Rua</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Logradouro" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="numero"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">Número</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Nº" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>

                        <div className="grid grid-cols-2 gap-3">
                          <FormField
                            control={form.control}
                            name="localidade"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">Cidade</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Cidade" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="uf"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">Estado</FormLabel>
                                <Select value={field.value} onValueChange={field.onChange}>
                                  <FormControl>
                                    <SelectTrigger>
                                      <SelectValue placeholder="UF" />
                                    </SelectTrigger>
                                  </FormControl>
                                  <SelectContent className="z-[70]">
                                    {ESTADOS_BR.map((uf) => (
                                      <SelectItem key={uf.value} value={uf.value}>
                                        {uf.label}
                                      </SelectItem>
                                    ))}
                                  </SelectContent>
                                </Select>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      </CardContent>
                    </Card>

                    {/* ── Observações ─────────────────────────────────── */}
                    <FormField
                      control={form.control}
                      name="observacoes"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel className="text-xs">Observações</FormLabel>
                          <FormControl>
                            <Textarea
                              {...field}
                              placeholder="Observações sobre o membro"
                              rows={3}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    {/* ── Disponibilidade ─────────────────────────────── */}
                    <FormField
                      control={form.control}
                      name="disponivel_todas_casas"
                      render={({ field }) => (
                        <FormItem>
                          <div className="flex items-center justify-between rounded-lg border p-3 gap-3">
                            <div>
                              <Label className="text-sm font-medium cursor-pointer">
                                Disponível em Todas as Casas?
                              </Label>
                              <p className="text-xs text-muted-foreground mt-0.5">
                                As informações básicas serão exibidas somente nas casas onde o
                                frade passou.
                              </p>
                            </div>
                            <Switch
                              checked={field.value}
                              onCheckedChange={field.onChange}
                            />
                          </div>
                        </FormItem>
                      )}
                    />

                  </div>
                </ScrollArea>
              )}
            </SheetBody>

            {/* ── Footer ───────────────────────────────────────────── */}
            <SheetFooter className="border-t px-5 py-3 shrink-0 flex items-center justify-end gap-2">
              <Button
                type="button"
                variant="outline"
                onClick={() => onOpenChange(false)}
                disabled={isSubmitting}
              >
                Cancelar
              </Button>

              <div className="flex">
                <Button
                  type="submit"
                  disabled={isSubmitting}
                  className="rounded-e-none border-e-0"
                >
                  {isSubmitting ? (
                    <>
                      <Loader2 className="size-4 animate-spin" />
                      Salvando...
                    </>
                  ) : (
                    'Salvar'
                  )}
                </Button>
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button
                      type="button"
                      disabled={isSubmitting}
                      className="rounded-s-none px-2"
                      aria-label="Mais opções de salvamento"
                    >
                      <ChevronDown className="size-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem
                      onClick={form.handleSubmit((d) => submitForm(d, 'clone'))}
                    >
                      Salvar e Clonar
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      onClick={form.handleSubmit((d) => submitForm(d, 'clear'))}
                    >
                      Salvar e Limpar
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </SheetFooter>
          </form>
        </Form>
      </SheetContent>
    </Sheet>
  );
}
