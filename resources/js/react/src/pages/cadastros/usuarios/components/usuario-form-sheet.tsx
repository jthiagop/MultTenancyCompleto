import { useEffect, useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { Building2, Camera, Check, ChevronDown, Eye, EyeOff, Loader2, ShieldCheck, UserPlus, UserRound, X } from 'lucide-react';
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
import { Checkbox } from '@/components/ui/checkbox';
import { Item, ItemContent, ItemDescription, ItemGroup, ItemMedia, ItemTitle } from '@/components/ui/item';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

// ── Schema de validação ──────────────────────────────────────────────────────

const userFormSchema = z
  .object({
    name: z.string().min(1, 'O nome é obrigatório.').max(255, 'Nome muito longo.'),
    email: z
      .string()
      .min(1, 'O e-mail é obrigatório.')
      .email('Informe um e-mail válido.'),
    password: z
      .string()
      .refine((v) => v.length === 0 || v.length >= 8, 'A senha deve ter no mínimo 8 caracteres.'),
    password_confirmation: z.string(),
    must_change_password: z.boolean(),
    active: z.boolean(),
  })
  .refine((d) => !d.password || d.password === d.password_confirmation, {
    message: 'A confirmação não confere.',
    path: ['password_confirmation'],
  });

type UserFormValues = z.infer<typeof userFormSchema>;

const USER_FORM_DEFAULTS: UserFormValues = {
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  must_change_password: true,
  active: true,
};

interface ICompany {
  id: number;
  name: string;
  avatar_url: string | null;
  city: string | null;
  state: string | null;
}

interface IPermission {
  id: number;
  name: string;
  action: string;
  action_label: string;
  color: 'blue' | 'green' | 'amber' | 'red' | 'purple' | 'gray';
}

interface IPermissionModule {
  key: string;
  name: string;
  icon_url: string | null;
  permissions: IPermission[];
}

const ACTION_STYLES: Record<string, string> = {
  blue:   'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800',
  green:  'bg-green-50 text-green-700 border-green-200 dark:bg-green-950 dark:text-green-300 dark:border-green-800',
  amber:  'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800',
  red:    'bg-red-50 text-red-700 border-red-200 dark:bg-red-950 dark:text-red-300 dark:border-red-800',
  purple: 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950 dark:text-purple-300 dark:border-purple-800',
  gray:   'bg-muted text-muted-foreground border-border',
};

export interface UsuarioFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: () => void;
  editingId?: number | null;
}

// ── Campo de senha com toggle visibilidade ────────────────────────────────────

function PasswordInput({
  value,
  onChange,
  placeholder,
  id,
  'aria-invalid': ariaInvalid,
  'aria-describedby': ariaDescribedby,
}: {
  value: string;
  onChange: (v: string) => void;
  placeholder?: string;
  id?: string;
  'aria-invalid'?: boolean | 'true' | 'false';
  'aria-describedby'?: string;
}) {
  const [show, setShow] = useState(false);
  return (
    <div className="relative">
      <Input
        id={id}
        type={show ? 'text' : 'password'}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        placeholder={placeholder}
        className="pe-9"
        autoComplete="new-password"
        aria-invalid={ariaInvalid}
        aria-describedby={ariaDescribedby}
      />
      <button
        type="button"
        tabIndex={-1}
        onClick={() => setShow((s) => !s)}
        className="absolute end-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
      >
        {show ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
      </button>
    </div>
  );
}

// ── Componente principal ─────────────────────────────────────────────────────

export function UsuarioFormSheet({ open, onOpenChange, onSaved, editingId }: UsuarioFormSheetProps) {
  const { csrfToken } = useAppData();
  const isEditing = !!editingId;

  const form = useForm<UserFormValues>({
    resolver: zodResolver(userFormSchema),
    defaultValues: USER_FORM_DEFAULTS,
  });
  const { formState: { isSubmitting } } = form;

  const [loadingData, setLoadingData] = useState(false);
  const [avatarPreview, setAvatarPreview] = useState('');
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [companies, setCompanies] = useState<ICompany[]>([]);
  const [loadingCompanies, setLoadingCompanies] = useState(false);
  const [selectedCompanyIds, setSelectedCompanyIds] = useState<number[]>([]);
  const [permModules, setPermModules] = useState<IPermissionModule[]>([]);
  const [loadingPerms, setLoadingPerms] = useState(false);
  const [selectedPermIds, setSelectedPermIds] = useState<number[]>([]);
  const [openModules, setOpenModules] = useState<Set<string>>(new Set());

  const avatarInputRef = useRef<HTMLInputElement>(null);

  function handleAvatarChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setAvatarFile(file);
    const reader = new FileReader();
    reader.onload = (ev) => setAvatarPreview(ev.target?.result as string);
    reader.readAsDataURL(file);
    e.target.value = '';
  }

  // ── Carrega lista de organismos e permissões ────────────────────────────────
  useEffect(() => {
    if (!open) return;
    setLoadingCompanies(true);
    fetch('/api/cadastros/companies', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json())
      .then((json) => setCompanies(json.data ?? []))
      .catch(() => {})
      .finally(() => setLoadingCompanies(false));

    setLoadingPerms(true);
    fetch('/api/cadastros/permissions', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((r) => r.json())
      .then((json) => {
        const mods: IPermissionModule[] = json.modules ?? [];
        setPermModules(mods);
        // Abre o primeiro módulo por padrão
        if (mods.length > 0) setOpenModules(new Set([mods[0].key]));
      })
      .catch(() => {})
      .finally(() => setLoadingPerms(false));
  }, [open]);

  // ── Carrega dados ao editar ─────────────────────────────────────────────────
  useEffect(() => {
    if (!open) return;

    if (editingId) {
      setLoadingData(true);
      fetch(`/api/cadastros/usuarios/${editingId}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      })
        .then((r) => r.json())
        .then((json) => {
          const d = json.data ?? json;
          if (!d.id) throw new Error();
          form.reset({
            name: d.name ?? '',
            email: d.email ?? '',
            password: '',
            password_confirmation: '',
            must_change_password: d.force_password_change ?? d.must_change_password ?? false,
            active: d.active ?? true,
          });
          // Ignora o avatar padrão (blank.png)
          const hasRealAvatar = d.avatar_url && !d.avatar_url.includes('blank');
          setAvatarPreview(hasRealAvatar ? d.avatar_url : '');
          setAvatarFile(null);
          setSelectedCompanyIds(Array.isArray(d.company_ids) ? d.company_ids : []);
          setSelectedPermIds(Array.isArray(d.permission_ids) ? d.permission_ids : []);
        })
        .catch(() => notify.error('Erro', 'Não foi possível carregar os dados do usuário.'))
        .finally(() => setLoadingData(false));
    } else {
      form.reset(USER_FORM_DEFAULTS);
      setAvatarPreview('');
      setAvatarFile(null);
      setSelectedCompanyIds([]);
      setSelectedPermIds([]);
    }
  }, [open, editingId]);

  // ── Submissão ───────────────────────────────────────────────────────────────
  const handleSubmit = form.handleSubmit(async (data) => {
    // Senha obrigatória apenas na criação — zod não sabe se é edição ou não
    if (!isEditing && !data.password) {
      form.setError('password', { message: 'A senha é obrigatória.' });
      return;
    }
    if (!csrfToken) {
      notify.reload();
      return;
    }

    try {
      const fd = new FormData();
      fd.append('name', data.name.trim());
      fd.append('email', data.email.trim());
      fd.append('active', data.active ? '1' : '0');
      fd.append('must_change_password', data.must_change_password ? '1' : '0');
      if (data.password) {
        fd.append('password', data.password);
        fd.append('password_confirmation', data.password_confirmation);
      }
      if (avatarFile) fd.append('avatar', avatarFile);
      selectedCompanyIds.forEach((id) => fd.append('filiais[]', String(id)));
      // Sinaliza ao backend que as permissões devem ser sincronizadas (mesmo vazio = remover todas)
      fd.append('sync_permissions', '1');
      selectedPermIds.forEach((id) => fd.append('permissions[]', String(id)));

      const url = isEditing ? `/api/cadastros/usuarios/${editingId}` : '/api/cadastros/usuarios';
      // Laravel não aceita PUT com FormData — usa _method spoofing
      if (isEditing) fd.append('_method', 'PUT');
      const method = 'POST';

      const res = await fetch(url, {
        method,
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

      if (res.ok && (result.success !== false)) {
        notify.success(
          isEditing ? 'Usuário atualizado!' : 'Usuário cadastrado!',
          `${data.name.trim()} foi ${isEditing ? 'atualizado' : 'cadastrado'} com sucesso.`,
        );
        onOpenChange(false);
        onSaved?.();
      } else {
        // Erros do servidor: exibir inline nos campos correspondentes
        if (result.errors) {
          Object.entries(result.errors).forEach(([field, messages]) => {
            const key = field as keyof UserFormValues;
            if (messages[0]) form.setError(key, { message: messages[0] });
          });
        } else {
          notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
        }
      }
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
            <UserPlus className="size-4 text-muted-foreground" />
            {isEditing ? 'Editar Usuário' : 'Novo Usuário'}
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

                  {/* Dados do Usuário */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <UserPlus className="size-3.5 text-muted-foreground" />
                        Dados do Usuário
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
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
                                <img src={avatarPreview} alt="Avatar do usuário" className="size-full object-cover" />
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
                                onClick={(e) => { e.stopPropagation(); setAvatarPreview(''); setAvatarFile(null); }}
                                className="absolute -top-0.5 -end-0.5 z-10 size-5 flex items-center justify-center rounded-full border border-border bg-background shadow-sm"
                              >
                                <X className="size-3 text-muted-foreground hover:text-foreground" />
                              </button>
                            )}
                          </div>
                          <span className="text-xs text-muted-foreground">Foto</span>
                        </div>

                        {/* Campos */}
                        <div className="flex-1 grid grid-cols-2 gap-4">
                          <FormField
                            control={form.control}
                            name="name"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel className="text-xs">
                                  Nome <span className="text-destructive">*</span>
                                </FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="Nome completo" autoFocus />
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
                                  E-mail <span className="text-destructive">*</span>
                                </FormLabel>
                                <FormControl>
                                  <Input {...field} type="email" placeholder="email@exemplo.com" autoComplete="off" />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>

                      </div>
                    </CardContent>
                  </Card>

                  {/* Senha */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm">
                        {isEditing ? 'Alterar Senha' : 'Senha'}
                        {isEditing && (
                          <span className="ms-1.5 text-xs font-normal text-muted-foreground">
                            (deixe em branco para manter a atual)
                          </span>
                        )}
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4 space-y-4">

                      <div className="grid grid-cols-2 gap-4">
                        <FormField
                          control={form.control}
                          name="password"
                          render={({ field, fieldState }) => (
                            <FormItem>
                              <FormLabel className="text-xs">
                                Senha {!isEditing && <span className="text-destructive">*</span>}
                              </FormLabel>
                              <PasswordInput
                                id="u-password"
                                value={field.value}
                                onChange={field.onChange}
                                placeholder="Mínimo 8 caracteres"
                                aria-invalid={!!fieldState.error}
                              />
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={form.control}
                          name="password_confirmation"
                          render={({ field, fieldState }) => (
                            <FormItem>
                              <FormLabel className="text-xs">
                                Confirmar Senha {!isEditing && <span className="text-destructive">*</span>}
                              </FormLabel>
                              <PasswordInput
                                id="u-password-confirm"
                                value={field.value}
                                onChange={field.onChange}
                                placeholder="Repita a senha"
                                aria-invalid={!!fieldState.error}
                              />
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      <FormField
                        control={form.control}
                        name="must_change_password"
                        render={({ field }) => (
                          <div className="flex items-center gap-2.5 pt-1">
                            <Checkbox
                              id="u-force-pw"
                              checked={field.value}
                              onCheckedChange={(checked) => field.onChange(checked === true)}
                            />
                            <Label htmlFor="u-force-pw" className="text-sm cursor-pointer">
                              Usuário deve trocar a senha no primeiro acesso
                            </Label>
                          </div>
                        )}
                      />

                    </CardContent>
                  </Card>

                  {/* Organismos com Acesso */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <Building2 className="size-3.5 text-muted-foreground" />
                        Organismos com Acesso
                        {selectedCompanyIds.length > 0 && (
                          <span className="ms-1.5 text-xs font-normal text-muted-foreground">
                            ({selectedCompanyIds.length} selecionado{selectedCompanyIds.length !== 1 ? 's' : ''})
                          </span>
                        )}
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                      {loadingCompanies ? (
                        <div className="flex items-center justify-center py-6">
                          <Loader2 className="size-5 animate-spin text-muted-foreground" />
                        </div>
                      ) : companies.length === 0 ? (
                        <p className="text-sm text-muted-foreground text-center py-4">
                          Nenhum organismo encontrado.
                        </p>
                      ) : (
                        <ItemGroup className="grid grid-cols-2">
                          {companies.map((company) => {
                            const isSelected = selectedCompanyIds.includes(company.id);
                            const initials = company.name.slice(0, 2).toUpperCase();
                            const location = company.city
                              ? `${company.city}${company.state ? ` - ${company.state}` : ''}`
                              : 'Organismo';
                            return (
                              <Item
                                key={company.id}
                                variant="outline"
                                className={cn(
                                  'cursor-pointer select-none transition-colors',
                                  isSelected && 'border-primary/40 bg-primary/5',
                                )}
                                onClick={() =>
                                  setSelectedCompanyIds((prev) =>
                                    isSelected
                                      ? prev.filter((id) => id !== company.id)
                                      : [...prev, company.id],
                                  )
                                }
                              >
                                <ItemMedia variant="image" className="size-9">
                                  {company.avatar_url ? (
                                    <img
                                      src={company.avatar_url}
                                      alt={company.name}
                                      className="size-full rounded-full object-cover"
                                    />
                                  ) : (
                                    <div className="size-9 rounded-full bg-primary/10 flex items-center justify-center text-xs font-semibold text-primary">
                                      {initials}
                                    </div>
                                  )}
                                </ItemMedia>
                                <ItemContent>
                                  <ItemTitle>{company.name}</ItemTitle>
                                  <ItemDescription>{location}</ItemDescription>
                                </ItemContent>
                                {/* Toggle visual simples — evita o loop de setRef do Radix Switch */}
                                <div
                                  role="switch"
                                  aria-checked={isSelected}
                                  onClick={(e) => e.stopPropagation()}
                                  className={cn(
                                    'relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors',
                                    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
                                    isSelected ? 'bg-primary' : 'bg-input',
                                  )}
                                >
                                  <span
                                    className={cn(
                                      'pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform',
                                      isSelected ? 'translate-x-4' : 'translate-x-0',
                                    )}
                                  />
                                </div>
                              </Item>
                            );
                          })}
                        </ItemGroup>
                      )}
                    </CardContent>
                  </Card>

                  {/* Permissões por Módulo */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <ShieldCheck className="size-3.5 text-muted-foreground" />
                        Permissões por Módulo
                        {selectedPermIds.length > 0 && (
                          <span className="ms-1.5 text-xs font-normal text-muted-foreground">
                            ({selectedPermIds.length} selecionada{selectedPermIds.length !== 1 ? 's' : ''})
                          </span>
                        )}
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-0 px-0">
                      {loadingPerms ? (
                        <div className="flex items-center justify-center py-6">
                          <Loader2 className="size-5 animate-spin text-muted-foreground" />
                        </div>
                      ) : permModules.length === 0 ? (
                        <p className="text-sm text-muted-foreground text-center py-4 px-5">
                          Nenhuma permissão encontrada.
                        </p>
                      ) : (
                        <div>
                          {permModules.map((mod, idx) => {
                            const isOpen = openModules.has(mod.key);
                            const selectedInModule = mod.permissions.filter((p) => selectedPermIds.includes(p.id));
                            const allSelected = selectedInModule.length === mod.permissions.length;
                            const someSelected = selectedInModule.length > 0 && !allSelected;

                            function toggleModule() {
                              setOpenModules((prev) => {
                                const next = new Set(prev);
                                if (next.has(mod.key)) next.delete(mod.key);
                                else next.add(mod.key);
                                return next;
                              });
                            }

                            function toggleAll(e: React.MouseEvent) {
                              e.stopPropagation();
                              setSelectedPermIds((prev) =>
                                allSelected
                                  ? prev.filter((id) => !mod.permissions.some((p) => p.id === id))
                                  : [...new Set([...prev, ...mod.permissions.map((p) => p.id)])],
                              );
                            }

                            return (
                              <div
                                key={mod.key}
                                className={cn('border-b border-border last:border-b-0', idx === 0 && 'border-t border-border')}
                              >
                                {/* Cabeçalho do módulo */}
                                <div
                                  className="flex items-center gap-3 px-5 py-3 cursor-pointer hover:bg-accent/30 transition-colors select-none"
                                  onClick={toggleModule}
                                >
                                  <ChevronDown
                                    className={cn(
                                      'size-3.5 text-muted-foreground transition-transform shrink-0',
                                      isOpen && 'rotate-180',
                                    )}
                                  />
                                  {mod.icon_url ? (
                                    <img src={mod.icon_url} alt={mod.name} className="size-5 shrink-0 object-contain" />
                                  ) : (
                                    <ShieldCheck className="size-5 shrink-0 text-muted-foreground" />
                                  )}
                                  <span className="flex-1 text-sm font-medium">{mod.name}</span>
                                  {/* Contador */}
                                  <span
                                    className={cn(
                                      'text-xs px-1.5 py-0.5 rounded-full font-medium min-w-[1.5rem] text-center',
                                      allSelected && 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                      someSelected && 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
                                      !someSelected && !allSelected && 'bg-muted text-muted-foreground',
                                    )}
                                  >
                                    {selectedInModule.length}/{mod.permissions.length}
                                  </span>
                                  {/* Toggle Todos */}
                                  <button
                                    type="button"
                                    onClick={toggleAll}
                                    className={cn(
                                      'text-xs px-2 py-0.5 rounded border transition-colors shrink-0',
                                      allSelected
                                        ? 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100 dark:border-green-700 dark:bg-green-950 dark:text-green-300'
                                        : 'border-border bg-background text-muted-foreground hover:bg-accent hover:text-foreground',
                                    )}
                                  >
                                    {allSelected ? 'Desmarcar' : 'Todos'}
                                  </button>
                                </div>

                                {/* Permissões expandidas */}
                                {isOpen && (
                                  <div className="px-5 pb-4 pt-1">
                                    <div className="grid grid-cols-3 gap-2">
                                      {mod.permissions.map((perm) => {
                                        const checked = selectedPermIds.includes(perm.id);
                                        return (
                                          <button
                                            key={perm.id}
                                            type="button"
                                            onClick={() =>
                                              setSelectedPermIds((prev) =>
                                                checked
                                                  ? prev.filter((id) => id !== perm.id)
                                                  : [...prev, perm.id],
                                              )
                                            }
                                            className={cn(
                                              'flex items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors text-left',
                                              checked
                                                ? ACTION_STYLES[perm.color]
                                                : 'border-border bg-background text-muted-foreground hover:bg-accent hover:text-foreground',
                                            )}
                                          >
                                            <span
                                              className={cn(
                                                'size-1.5 rounded-full shrink-0',
                                                checked ? 'bg-current' : 'bg-muted-foreground/40',
                                              )}
                                            />
                                            {perm.action_label}
                                          </button>
                                        );
                                      })}
                                    </div>
                                  </div>
                                )}
                              </div>
                            );
                          })}
                        </div>
                      )}
                    </CardContent>
                  </Card>

                </div>
              </ScrollArea>
            )}
          </SheetBody>

          <SheetFooter className="border-t border-border px-5 py-3">
            <div className="flex w-full items-center justify-between gap-2">
            {/* Seletor de status */}
            <FormField
              control={form.control}
              name="active"
              render={({ field }) => (
                <Select
                  value={field.value ? 'active' : 'inactive'}
                  onValueChange={(v) => field.onChange(v === 'active')}
                >
                  <SelectTrigger className="w-36 h-8 text-xs">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent className="z-[70]">
                    <SelectItem value="active">
                      <span className="flex items-center gap-1.5">
                        <span className="size-1.5 rounded-full bg-green-500 shrink-0" />
                        Ativo
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
                  <><Check className="size-4" />{isEditing ? 'Salvar Alterções' : 'Cadastrar Usuário'}</>
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
