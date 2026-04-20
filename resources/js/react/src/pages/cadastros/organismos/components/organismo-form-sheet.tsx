import { useEffect, useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import {
  Building2,
  Camera,
  Loader2,
  MapPin,
  Save,
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
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

// ── Máscaras utilitárias ──────────────────────────────────────────────────────

function maskCNPJ(v: string): string {
  const digits = v.replace(/\D/g, '').slice(0, 14);
  return digits
    .replace(/^(\d{2})(\d)/, '$1.$2')
    .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
    .replace(/\.(\d{3})(\d)/, '.$1/$2')
    .replace(/(\d{4})(\d)/, '$1-$2');
}

function maskCEP(v: string): string {
  const digits = v.replace(/\D/g, '').slice(0, 8);
  if (digits.length <= 5) return digits;
  return digits.slice(0, 5) + '-' + digits.slice(5);
}

// ── Schema ────────────────────────────────────────────────────────────────────

const organismoSchema = z.object({
  name:         z.string().min(1, 'O nome é obrigatório.').max(255, 'Nome muito longo.'),
  razao_social: z.string().max(255, 'Muito longo.').optional().or(z.literal('')),
  cnpj:         z.string().min(14, 'Informe um CNPJ válido.').max(20, 'CNPJ muito longo.'),
  email:        z.string().email('E-mail inválido.').optional().or(z.literal('')),
  status:       z.enum(['active', 'inactive']),
  type:         z.enum(['matriz', 'filial']),
  cep:          z.string().max(20).optional().or(z.literal('')),
  rua:          z.string().max(255).optional().or(z.literal('')),
  numero:       z.string().max(20).optional().or(z.literal('')),
  complemento:  z.string().max(255).optional().or(z.literal('')),
  bairro:       z.string().max(120).optional().or(z.literal('')),
  cidade:       z.string().max(120).optional().or(z.literal('')),
  uf:           z.string().max(2).optional().or(z.literal('')),
});

type OrganismoFormValues = z.infer<typeof organismoSchema>;

const DEFAULTS: OrganismoFormValues = {
  name: '',
  razao_social: '',
  cnpj: '',
  email: '',
  status: 'active',
  type: 'filial',
  cep: '',
  rua: '',
  numero: '',
  complemento: '',
  bairro: '',
  cidade: '',
  uf: '',
};

export interface OrganismoFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: () => void;
  editingId?: number | null;
}

// ── Componente principal ──────────────────────────────────────────────────────

export function OrganismoFormSheet({
  open,
  onOpenChange,
  onSaved,
  editingId,
}: OrganismoFormSheetProps) {
  const { csrfToken } = useAppData();
  const isEditing = !!editingId;

  const form = useForm<OrganismoFormValues>({
    resolver: zodResolver(organismoSchema),
    defaultValues: DEFAULTS,
  });
  const { formState: { isSubmitting } } = form;

  const [loadingData, setLoadingData] = useState(false);
  const [avatarPreview, setAvatarPreview] = useState('');
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const avatarInputRef = useRef<HTMLInputElement>(null);

  function handleAvatarChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setAvatarFile(file);
    const reader = new FileReader();
    reader.onload = (ev) => setAvatarPreview((ev.target?.result as string) ?? '');
    reader.readAsDataURL(file);
    e.target.value = '';
  }

  // ── Carrega dados ao editar ─────────────────────────────────────────────────
  useEffect(() => {
    if (!open) return;

    if (editingId) {
      setLoadingData(true);
      fetch(`/api/cadastros/organismos/${editingId}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      })
        .then(async (r) => {
          if (!r.ok) throw new Error(`HTTP ${r.status}`);
          return r.json();
        })
        .then((json) => {
          const d = json.data ?? {};
          const rawStatus = String(d.status ?? '').toLowerCase();
          const rawType   = String(d.type   ?? '').toLowerCase();
          form.reset({
            name:         d.name ?? '',
            razao_social: d.razao_social ?? '',
            cnpj:         d.cnpj ? maskCNPJ(String(d.cnpj)) : '',
            email:        d.email ?? '',
            status:       rawStatus === 'inactive' || rawStatus === 'inativo' ? 'inactive' : 'active',
            type:         rawType === 'matriz' ? 'matriz' : 'filial',
            cep:          d.address?.cep ? maskCEP(String(d.address.cep)) : '',
            rua:          d.address?.rua ?? '',
            numero:       d.address?.numero ?? '',
            complemento:  d.address?.complemento ?? '',
            bairro:       d.address?.bairro ?? '',
            cidade:       d.address?.cidade ?? '',
            uf:           d.address?.uf ?? '',
          });
          const hasRealAvatar = d.avatar_url && !String(d.avatar_url).includes('blank');
          setAvatarPreview(hasRealAvatar ? d.avatar_url : '');
          setAvatarFile(null);
        })
        .catch(() => notify.error('Erro', 'Não foi possível carregar os dados do organismo.'))
        .finally(() => setLoadingData(false));
    } else {
      form.reset(DEFAULTS);
      setAvatarPreview('');
      setAvatarFile(null);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, editingId]);

  // ── Submissão ───────────────────────────────────────────────────────────────
  const handleSubmit = form.handleSubmit(async (data) => {
    if (!csrfToken) {
      notify.reload();
      return;
    }

    try {
      const fd = new FormData();
      fd.append('name', data.name.trim());
      if (data.razao_social) fd.append('razao_social', data.razao_social.trim());
      fd.append('cnpj', data.cnpj.trim());
      fd.append('status', data.status);
      fd.append('type', data.type);
      if (data.email)       fd.append('email', data.email.trim());
      if (data.cep)         fd.append('cep', data.cep.trim());
      if (data.rua)         fd.append('rua', data.rua.trim());
      if (data.numero)      fd.append('numero', data.numero.trim());
      if (data.complemento) fd.append('complemento', data.complemento.trim());
      if (data.bairro)      fd.append('bairro', data.bairro.trim());
      if (data.cidade)      fd.append('cidade', data.cidade.trim());
      if (data.uf)          fd.append('uf', data.uf.trim().toUpperCase());
      if (avatarFile)       fd.append('avatar', avatarFile);

      const url = isEditing
        ? `/api/cadastros/organismos/${editingId}`
        : '/api/cadastros/organismos';
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

      const result = (await res.json().catch(() => ({}))) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
      };

      if (res.ok && result.success !== false) {
        notify.success(
          isEditing ? 'Organismo atualizado!' : 'Organismo cadastrado!',
          `${data.name.trim()} foi ${isEditing ? 'atualizado' : 'cadastrado'} com sucesso.`,
        );
        onOpenChange(false);
        onSaved?.();
        return;
      }

      if (result.errors) {
        Object.entries(result.errors).forEach(([field, messages]) => {
          const key = field as keyof OrganismoFormValues;
          if (messages?.[0]) form.setError(key, { message: messages[0] });
        });
        return;
      }

      notify.error(
        'Não foi possível salvar',
        result.message ?? 'Verifique os dados e tente novamente.',
      );
    } catch {
      notify.networkError();
    }
  });

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        overlayClassName="z-[55]"
        className={cn(
          'z-60 gap-0 lg:w-180 sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '**:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5',
        )}
      >
        <SheetHeader className="border-b py-3.5 px-5 border-border space-y-0">
          <SheetTitle className="font-medium flex items-center gap-2">
            <Building2 className="size-4 text-muted-foreground" />
            {isEditing ? 'Editar Organismo' : 'Novo Organismo'}
          </SheetTitle>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
            <SheetBody className="grow p-0 flex flex-col min-h-0">
              {loadingData ? (
                <div className="flex flex-1 items-center justify-center py-20">
                  <Loader2 className="size-6 animate-spin text-muted-foreground" />
                </div>
              ) : (
                <ScrollArea className="flex-1 px-5 py-5">
                  <div className="space-y-5">

                    {/* Dados do Organismo */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <Building2 className="size-3.5 text-muted-foreground" />
                          Dados do Organismo
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4">
                        <div className="flex gap-5">

                          {/* Logo */}
                          <div className="flex flex-col items-center gap-2 shrink-0">
                            <input
                              ref={avatarInputRef}
                              type="file"
                              accept="image/png,image/jpeg,image/webp"
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
                                    alt="Logo do organismo"
                                    className="size-full object-cover"
                                  />
                                ) : (
                                  <Building2 className="size-8 text-muted-foreground" />
                                )}
                              </div>
                              <div className="absolute bottom-0 left-0 right-0 h-5 bg-black/25 flex items-center justify-center rounded-b-full pointer-events-none">
                                <Camera className="size-3 text-white" />
                              </div>
                              {avatarPreview && (
                                <button
                                  type="button"
                                  onClick={(e) => {
                                    e.stopPropagation();
                                    setAvatarPreview('');
                                    setAvatarFile(null);
                                  }}
                                  className="absolute -top-0.5 -end-0.5 z-10 size-5 flex items-center justify-center rounded-full border border-border bg-background shadow-sm"
                                >
                                  <X className="size-3 text-muted-foreground hover:text-foreground" />
                                </button>
                              )}
                            </div>
                            <span className="text-xs text-muted-foreground">Logo</span>
                          </div>

                          {/* Campos principais */}
                          <div className="flex-1 grid grid-cols-2 gap-4">
                            <FormField
                              control={form.control}
                              name="name"
                              render={({ field }) => (
                                <FormItem className="col-span-2">
                                  <FormLabel className="text-xs">
                                    Nome <span className="text-destructive">*</span>
                                  </FormLabel>
                                  <FormControl>
                                    <Input
                                      {...field}
                                      placeholder="Nome do organismo"
                                      autoComplete="off"
                                    />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />

                            <FormField
                              control={form.control}
                              name="cnpj"
                              render={({ field }) => (
                                <FormItem>
                                  <FormLabel className="text-xs">
                                    CNPJ <span className="text-destructive">*</span>
                                  </FormLabel>
                                  <FormControl>
                                    <Input
                                      value={field.value ?? ''}
                                      onChange={(e) => field.onChange(maskCNPJ(e.target.value))}
                                      placeholder="00.000.000/0000-00"
                                      inputMode="numeric"
                                      autoComplete="off"
                                      className="font-mono"
                                    />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />

                            <FormField
                              control={form.control}
                              name="email"
                              render={({ field }) => (
                                <FormItem>
                                  <FormLabel className="text-xs">
                                    E-mail <span className="text-muted-foreground font-normal">(opcional)</span>
                                  </FormLabel>
                                  <FormControl>
                                    <Input
                                      {...field}
                                      type="email"
                                      placeholder="contato@organismo.com.br"
                                      autoComplete="off"
                                    />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />

                            <FormField
                              control={form.control}
                              name="razao_social"
                              render={({ field }) => (
                                <FormItem className="col-span-2">
                                  <FormLabel className="text-xs">
                                    Razão Social <span className="text-muted-foreground font-normal">(opcional)</span>
                                  </FormLabel>
                                  <FormControl>
                                    <Input
                                      {...field}
                                      placeholder="Razão social como consta no CNPJ"
                                      autoComplete="off"
                                    />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />
                          </div>
                        </div>
                      </CardContent>
                    </Card>

                    {/* Endereço */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <MapPin className="size-3.5 text-muted-foreground" />
                          Endereço
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4">
                        <div className="grid grid-cols-6 gap-4">
                          <FormField
                            control={form.control}
                            name="cep"
                            render={({ field }) => (
                              <FormItem className="col-span-2">
                                <FormLabel className="text-xs">CEP</FormLabel>
                                <FormControl>
                                  <Input
                                    value={field.value ?? ''}
                                    onChange={(e) => field.onChange(maskCEP(e.target.value))}
                                    placeholder="00000-000"
                                    inputMode="numeric"
                                    autoComplete="off"
                                    className="font-mono"
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={form.control}
                            name="rua"
                            render={({ field }) => (
                              <FormItem className="col-span-4">
                                <FormLabel className="text-xs">
                                  Rua / Logradouro
                                </FormLabel>
                                <FormControl>
                                  <Input
                                    {...field}
                                    placeholder="Av. Brasil, Rua das Flores..."
                                    autoComplete="off"
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={form.control}
                            name="numero"
                            render={({ field }) => (
                              <FormItem className="col-span-2">
                                <FormLabel className="text-xs">Número</FormLabel>
                                <FormControl>
                                  <Input
                                    {...field}
                                    placeholder="123"
                                    autoComplete="off"
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={form.control}
                            name="complemento"
                            render={({ field }) => (
                              <FormItem className="col-span-4">
                                <FormLabel className="text-xs">Complemento</FormLabel>
                                <FormControl>
                                  <Input
                                    {...field}
                                    placeholder="Sala, bloco, etc."
                                    autoComplete="off"
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={form.control}
                            name="bairro"
                            render={({ field }) => (
                              <FormItem className="col-span-2">
                                <FormLabel className="text-xs">Bairro</FormLabel>
                                <FormControl>
                                  <Input {...field} autoComplete="off" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={form.control}
                            name="cidade"
                            render={({ field }) => (
                              <FormItem className="col-span-3">
                                <FormLabel className="text-xs">Cidade</FormLabel>
                                <FormControl>
                                  <Input {...field} autoComplete="off" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={form.control}
                            name="uf"
                            render={({ field }) => (
                              <FormItem className="col-span-1">
                                <FormLabel className="text-xs">UF</FormLabel>
                                <FormControl>
                                  <Input
                                    value={field.value ?? ''}
                                    onChange={(e) =>
                                      field.onChange(
                                        e.target.value.replace(/[^a-zA-Z]/g, '').slice(0, 2).toUpperCase(),
                                      )
                                    }
                                    placeholder="SP"
                                    maxLength={2}
                                    autoComplete="off"
                                    className="uppercase"
                                  />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                </ScrollArea>
              )}
            </SheetBody>

            <SheetFooter className="border-t border-border px-5 py-3">
              <div className="flex w-full items-center justify-between gap-2">
                {/* Status + tipo (à esquerda) */}
                <div className="flex items-center gap-2">
                  <FormField
                    control={form.control}
                    name="status"
                    render={({ field }) => (
                      <Select value={field.value} onValueChange={field.onChange}>
                        <SelectTrigger className="w-36 h-8 text-xs">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent className="z-[70]">
                          <SelectItem value="active">
                            <span className="flex items-center gap-1.5">
                              <span className="size-1.5 rounded-full bg-green-500 shrink-0" />
                              Ativado
                            </span>
                          </SelectItem>
                          <SelectItem value="inactive">
                            <span className="flex items-center gap-1.5">
                              <span className="size-1.5 rounded-full bg-muted-foreground shrink-0" />
                              Desativado
                            </span>
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="type"
                    render={({ field }) => (
                      <Select value={field.value} onValueChange={field.onChange}>
                        <SelectTrigger className="w-32 h-8 text-xs">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent className="z-[70]">
                          <SelectItem value="matriz">
                            <span className="flex items-center gap-1.5">
                              <span className="size-1.5 rounded-full bg-blue-500 shrink-0" />
                              Matriz
                            </span>
                          </SelectItem>
                          <SelectItem value="filial">
                            <span className="flex items-center gap-1.5">
                              <span className="size-1.5 rounded-full bg-sky-400 shrink-0" />
                              Filial
                            </span>
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    )}
                  />
                </div>

                {/* Ações (à direita) */}
                <div className="flex items-center gap-2">
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => onOpenChange(false)}
                    disabled={isSubmitting}
                  >
                    <X className="size-4" />
                    Cancelar
                  </Button>
                  <Button
                    type="submit"
                    className="bg-blue-600 hover:bg-blue-700 text-white border-0"
                    disabled={isSubmitting || loadingData}
                  >
                    {isSubmitting ? (
                      <><Loader2 className="size-4 animate-spin" />Salvando…</>
                    ) : (
                      <><Save className="size-4" />{isEditing ? 'Salvar alterações' : 'Cadastrar organismo'}</>
                    )}
                  </Button>
                </div>
              </div>
            </SheetFooter>
          </form>
        </Form>
      </SheetContent>
    </Sheet>
  );
}
