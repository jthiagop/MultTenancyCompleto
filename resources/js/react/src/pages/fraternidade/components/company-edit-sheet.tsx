import { useEffect, useRef, useState } from 'react';
import { z } from 'zod';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Building2, Camera, Check, Loader2, MapPin, UserRound, X } from 'lucide-react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import { MaskedInput } from '@/components/common/masked-input';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import { useAppData, type AppCompany } from '@/hooks/useAppData';

type EditableCompany = AppCompany & {
  details?: string | null;
  status?: 'active' | 'inactive' | string | null;
};

const companySchema = z.object({
  name: z.string().min(1, 'Nome do organismo é obrigatório.').max(255, 'Nome muito longo.'),
  razao_social: z.string().max(255, 'Razão social muito longa.').optional().or(z.literal('')),
  cnpj: z.string().min(1, 'CNPJ é obrigatório.').max(18, 'CNPJ inválido.'),
  email: z.string().email('Informe um e-mail válido.').optional().or(z.literal('')),
  details: z.string().optional().or(z.literal('')),
  cep: z.string().max(10, 'CEP inválido.').optional().or(z.literal('')),
  rua: z.string().max(255, 'Rua muito longa.').optional().or(z.literal('')),
  numero: z.string().max(20, 'Número inválido.').optional().or(z.literal('')),
  bairro: z.string().max(255, 'Bairro muito longo.').optional().or(z.literal('')),
  cidade: z.string().max(255, 'Cidade muito longa.').optional().or(z.literal('')),
  uf: z.string().max(2, 'UF inválida.').optional().or(z.literal('')),
  status: z.enum(['active', 'inactive']).default('active'),
});

type CompanyFormValues = z.infer<typeof companySchema>;

interface CompanyEditSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: (company: EditableCompany) => void;
}

const DEFAULTS: CompanyFormValues = {
  name: '',
  razao_social: '',
  cnpj: '',
  email: '',
  details: '',
  cep: '',
  rua: '',
  numero: '',
  bairro: '',
  cidade: '',
  uf: '',
  status: 'active',
};

function onlyDigits(v?: string | null) {
  return (v ?? '').replace(/\D/g, '');
}

export function CompanyEditSheet({ open, onOpenChange, onSaved }: CompanyEditSheetProps) {
  const { csrfToken, hasAdminRole } = useAppData();
  const [loadingData, setLoadingData] = useState(false);
  const [currentStatus, setCurrentStatus] = useState<'active' | 'inactive'>('active');
  const [avatarPreview, setAvatarPreview] = useState('');
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [avatarRemoved, setAvatarRemoved] = useState(false);
  const [loadingCepLookup, setLoadingCepLookup] = useState(false);
  const [loadingCnpjLookup, setLoadingCnpjLookup] = useState(false);
  const avatarInputRef = useRef<HTMLInputElement>(null);

  const form = useForm<CompanyFormValues>({
    resolver: zodResolver(companySchema),
    defaultValues: DEFAULTS,
  });

  const { isSubmitting } = form.formState;

  function handleAvatarChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setAvatarFile(file);
    setAvatarRemoved(false);
    const reader = new FileReader();
    reader.onload = (ev) => setAvatarPreview((ev.target?.result as string) ?? '');
    reader.readAsDataURL(file);
    e.target.value = '';
  }

  async function handleCepLookup(rawCep: string) {
    const cep = onlyDigits(rawCep);
    if (cep.length !== 8) return;

    setLoadingCepLookup(true);
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

      form.setValue('rua', data.logradouro ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('bairro', data.bairro ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('cidade', data.localidade ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('uf', (data.uf ?? '').toUpperCase(), { shouldDirty: true, shouldValidate: true });
    } catch {
      notify.networkError();
    } finally {
      setLoadingCepLookup(false);
    }
  }

  async function handleCnpjLookup(rawCnpj: string) {
    const cnpj = onlyDigits(rawCnpj);
    if (cnpj.length !== 14) return;

    setLoadingCnpjLookup(true);
    try {
      const res = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);
      const data = (await res.json()) as {
        razao_social?: string;
        nome_fantasia?: string;
        email?: string;
        cep?: string;
        logradouro?: string;
        numero?: string;
        bairro?: string;
        municipio?: string;
        uf?: string;
      };

      if (!res.ok) {
        notify.warning('CNPJ não encontrado', 'Não foi possível consultar os dados deste CNPJ.');
        return;
      }

      form.setValue('razao_social', data.razao_social ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('name', data.nome_fantasia || data.razao_social || '', { shouldDirty: true, shouldValidate: true });

      if (data.email) {
        form.setValue('email', data.email, { shouldDirty: true, shouldValidate: true });
      }

      if (data.cep) {
        const cepDigits = onlyDigits(data.cep);
        const cepMasked = cepDigits.replace(/(\d{5})(\d{0,3})/, (_m, a, b) => (b ? `${a}-${b}` : a));
        form.setValue('cep', cepMasked, { shouldDirty: true, shouldValidate: true });
      }

      form.setValue('rua', data.logradouro ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('numero', data.numero ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('bairro', data.bairro ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('cidade', data.municipio ?? '', { shouldDirty: true, shouldValidate: true });
      form.setValue('uf', (data.uf ?? '').toUpperCase(), { shouldDirty: true, shouldValidate: true });
    } catch {
      notify.networkError();
    } finally {
      setLoadingCnpjLookup(false);
    }
  }

  useEffect(() => {
    if (!open) return;

    setLoadingData(true);
    fetch('/api/cadastros/company/active', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json())
      .then((json) => {
        const d = (json?.data ?? {}) as EditableCompany;
        form.reset({
          name: d.name ?? '',
          razao_social: d.razao_social ?? '',
          cnpj: d.cnpj ?? '',
          email: d.email ?? '',
          details: d.details ?? '',
          cep: d.address?.cep ?? '',
          rua: d.address?.rua ?? '',
          numero: d.address?.numero ?? '',
          bairro: d.address?.bairro ?? '',
          cidade: d.address?.cidade ?? '',
          uf: d.address?.uf ?? '',
          status: d.status === 'inactive' ? 'inactive' : 'active',
        });
        setAvatarPreview(d.avatar_url ?? '');
        setAvatarFile(null);
        setAvatarRemoved(false);
        setCurrentStatus(d.status === 'inactive' ? 'inactive' : 'active');
      })
      .catch(() => notify.error('Erro', 'Não foi possível carregar os dados do organismo.'))
      .finally(() => setLoadingData(false));
  }, [open]);

  const handleSubmit = form.handleSubmit(async (data) => {
    if (!csrfToken) {
      notify.reload();
      return;
    }

    try {
      const res = await fetch('/api/cadastros/company/active', {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: (() => {
          const fd = new FormData();
          fd.append('_method', 'PUT');
          fd.append('name', data.name);
          fd.append('razao_social', data.razao_social ?? '');
          fd.append('cnpj', data.cnpj);
          fd.append('email', data.email ?? '');
          fd.append('details', data.details ?? '');
          fd.append('cep', data.cep ?? '');
          fd.append('rua', data.rua ?? '');
          fd.append('numero', data.numero ?? '');
          fd.append('bairro', data.bairro ?? '');
          fd.append('cidade', data.cidade ?? '');
          fd.append('uf', data.uf ?? '');
          if (hasAdminRole) {
            fd.append('status', data.status);
          }
          if (avatarFile) {
            fd.append('avatar', avatarFile);
          }
          if (avatarRemoved && !avatarFile) {
            fd.append('avatar_remove', '1');
          }
          return fd;
        })(),
      });

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
        data?: EditableCompany;
      };

      if (res.ok && result.success !== false && result.data) {
        notify.success('Organismo atualizado!', result.message ?? 'As informações foram salvas com sucesso.');
        onSaved?.(result.data);
        onOpenChange(false);
        return;
      }

      if (result.errors) {
        Object.entries(result.errors).forEach(([field, messages]) => {
          const key = field as keyof CompanyFormValues;
          if (messages[0]) {
            form.setError(key, { message: messages[0] });
          }
        });
        return;
      }

      notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
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
            Editar Organismo
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
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <Building2 className="size-3.5 text-muted-foreground" />
                          Dados do Organismo
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4">
                        <div className="flex gap-5">
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
                                  <img src={avatarPreview} alt="Avatar do organismo" className="size-full object-cover" />
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
                                  onClick={(e) => {
                                    e.stopPropagation();
                                    setAvatarPreview('');
                                    setAvatarFile(null);
                                    setAvatarRemoved(true);
                                  }}
                                  className="absolute -top-0.5 -end-0.5 z-10 size-5 flex items-center justify-center rounded-full border border-border bg-background shadow-sm"
                                >
                                  <X className="size-3 text-muted-foreground hover:text-foreground" />
                                </button>
                              )}
                            </div>
                            <span className="text-xs text-muted-foreground">Foto</span>
                          </div>

                          <div className="flex-1 grid grid-cols-2 gap-4">
                            <FormField
                              control={form.control}
                              name="razao_social"
                              render={({ field }) => (
                                <FormItem>
                                  <FormLabel className="text-xs">Razão Social</FormLabel>
                                  <FormControl>
                                    <Input {...field} placeholder="Razão social" />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />

                            <FormField
                              control={form.control}
                              name="name"
                              render={({ field }) => (
                                <FormItem>
                                  <FormLabel className="text-xs">Nome do Organismo</FormLabel>
                                  <FormControl>
                                    <Input {...field} placeholder="Nome do organismo" />
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
                                  <FormLabel className="text-xs flex items-center gap-1.5">
                                    CNPJ
                                    {loadingCnpjLookup && <Loader2 className="size-3 animate-spin text-muted-foreground" />}
                                  </FormLabel>
                                  <FormControl>
                                    <MaskedInput
                                      value={field.value}
                                      onMaskedChange={field.onChange}
                                      onBlur={(e) => {
                                        field.onBlur();
                                        void handleCnpjLookup(e.currentTarget.value);
                                      }}
                                      maskType="cnpj"
                                      placeholder="00.000.000/0000-00"
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
                                  <FormLabel className="text-xs">E-mail</FormLabel>
                                  <FormControl>
                                    <Input {...field} type="email" placeholder="email@organismo.com" />
                                  </FormControl>
                                  <FormMessage />
                                </FormItem>
                              )}
                            />
                          </div>
                        </div>
                      </CardContent>
                    </Card>

                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm flex items-center gap-1.5">
                          <MapPin className="size-3.5 text-muted-foreground" />
                          Endereço
                        </CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4 space-y-4">
                        <div className="grid grid-cols-4 gap-4">
                          <FormField
                            control={form.control}
                            name="cep"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs flex items-center gap-1.5">
                                  CEP
                                  {loadingCepLookup && <Loader2 className="size-3 animate-spin text-muted-foreground" />}
                                </FormLabel>
                                <FormControl>
                                  <MaskedInput
                                    value={field.value}
                                    onMaskedChange={field.onChange}
                                    onBlur={(e) => {
                                      field.onBlur();
                                      void handleCepLookup(e.currentTarget.value);
                                    }}
                                    maskType="cep"
                                    placeholder="00000-000"
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
                              <FormItem className="col-span-2">
                                <FormLabel className="text-xs">Rua</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Rua" />
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
                                  <Input {...field} placeholder="123" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                          <FormField
                            control={form.control}
                            name="bairro"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">Bairro</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Bairro" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />

                          <FormField
                            control={form.control}
                            name="cidade"
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
                                <FormLabel className="text-xs">UF</FormLabel>
                                <FormControl>
                                  <Input {...field} maxLength={2} placeholder="UF" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>

                        <FormField
                          control={form.control}
                          name="details"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel className="text-xs">Detalhes</FormLabel>
                              <FormControl>
                                <Textarea {...field} rows={3} placeholder="Detalhes do organismo" />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </CardContent>
                    </Card>
                  </div>
                </ScrollArea>
              )}
            </SheetBody>

            <SheetFooter className="border-t border-border px-5 py-3">
              <div className="flex w-full items-center justify-between gap-2">
                {hasAdminRole ? (
                  <FormField
                    control={form.control}
                    name="status"
                    render={({ field }) => (
                      <Select value={field.value} onValueChange={(v) => field.onChange(v as 'active' | 'inactive')}>
                        <SelectTrigger className="w-36 h-8 text-xs">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent className="z-[70]">
                          <SelectItem value="active">
                            <span className="flex items-center gap-1.5">
                              <span className="size-1.5 rounded-full bg-green-500 shrink-0" />
                              Ativa
                            </span>
                          </SelectItem>
                          <SelectItem value="inactive">
                            <span className="flex items-center gap-1.5">
                              <span className="size-1.5 rounded-full bg-muted-foreground shrink-0" />
                              Inativa
                            </span>
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    )}
                  />
                ) : (
                  <Badge variant={currentStatus === 'active' ? 'success' : 'secondary'} appearance="light" size="sm">
                    {currentStatus === 'active' ? 'Ativa' : 'Inativa'}
                  </Badge>
                )}

                <div className="flex items-center gap-2">
                  <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={isSubmitting}>
                    <X className="size-4" />
                    Cancelar
                  </Button>
                  <Button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white border-0" disabled={isSubmitting || loadingData}>
                    {isSubmitting ? (
                      <>
                        <Loader2 className="size-4 animate-spin" />
                        Salvando...
                      </>
                    ) : (
                      <>
                        <Check className="size-4" />
                        Salvar Alteracoes
                      </>
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
