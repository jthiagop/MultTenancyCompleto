import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { Check, Copy, Eye, EyeOff, KeyRound, Loader2, ShieldAlert } from 'lucide-react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Checkbox } from '@/components/ui/checkbox';
import { useAppData } from '@/hooks/useAppData';
import { notify } from '@/lib/notify';
import { cn } from '@/lib/utils';

// ── Schema ──────────────────────────────────────────────────────────────────

const resetPasswordSchema = z
  .object({
    automatic_password: z.boolean(),
    password: z.string(),
    password_confirmation: z.string(),
    require_change: z.boolean(),
  })
  .superRefine((d, ctx) => {
    if (!d.automatic_password) {
      if (d.password.length < 8) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          message: 'A senha deve ter no mínimo 8 caracteres.',
          path: ['password'],
        });
      }
      if (d.password !== d.password_confirmation) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          message: 'A confirmação não confere.',
          path: ['password_confirmation'],
        });
      }
    }
  });

type ResetPasswordValues = z.infer<typeof resetPasswordSchema>;

const DEFAULTS: ResetPasswordValues = {
  automatic_password: true,
  password: '',
  password_confirmation: '',
  require_change: true,
};

// ── PasswordInput ────────────────────────────────────────────────────────────

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

// ── Props ────────────────────────────────────────────────────────────────────

export interface ResetPasswordSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  userId: number | null;
  userName?: string;
  userEmail?: string;
}

// ── Componente principal ─────────────────────────────────────────────────────

export function ResetPasswordSheet({
  open,
  onOpenChange,
  userId,
  userName,
  userEmail,
}: ResetPasswordSheetProps) {
  const { csrfToken } = useAppData();

  const form = useForm<ResetPasswordValues>({
    resolver: zodResolver(resetPasswordSchema),
    defaultValues: DEFAULTS,
  });

  const {
    formState: { isSubmitting },
    watch,
  } = form;

  const automaticPassword = watch('automatic_password');

  const [generatedPassword, setGeneratedPassword] = useState<string | null>(null);
  const [copied, setCopied] = useState(false);

  // Reseta o form e estado local toda vez que o Sheet abre
  useEffect(() => {
    if (open) {
      form.reset(DEFAULTS);
      setGeneratedPassword(null);
      setCopied(false);
    }
  }, [open, form]);

  async function onSubmit(values: ResetPasswordValues) {
    if (!userId) return;

    const fd = new FormData();
    fd.append('_token', csrfToken);
    fd.append('automatic_password', values.automatic_password ? '1' : '0');
    fd.append('require_change', values.require_change ? '1' : '0');

    if (!values.automatic_password) {
      fd.append('password', values.password);
      fd.append('password_confirmation', values.password_confirmation);
    }

    try {
      const res = await fetch(`/api/cadastros/usuarios/${userId}/reset-password`, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
        body: fd,
      });

      const result = await res.json();

      if (!res.ok || !result.success) {
        if (result.errors) {
          Object.entries(result.errors as Record<string, string[]>).forEach(([field, messages]) => {
            form.setError(field as keyof ResetPasswordValues, { message: messages[0] });
          });
        } else {
          notify.error(result.message ?? 'Erro ao redefinir a senha.');
        }
        return;
      }

      if (values.automatic_password && result.generated_password) {
        setGeneratedPassword(result.generated_password);
      } else {
        notify.success(result.message ?? 'Senha redefinida com sucesso!');
        onOpenChange(false);
      }
    } catch {
      notify.error('Erro de conexão. Tente novamente.');
    }
  }

  function handleCopy() {
    if (!generatedPassword) return;
    navigator.clipboard.writeText(generatedPassword).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  }

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent className="w-full sm:max-w-[480px] flex flex-col p-0">
        <SheetHeader className="border-b border-border px-5 py-4">
          <div className="flex items-center gap-3">
            <div className="flex size-9 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
              <KeyRound className="size-4 text-amber-600 dark:text-amber-400" />
            </div>
            <div className="min-w-0">
              <SheetTitle className="text-base font-semibold">Redefinir Senha</SheetTitle>
              {(userName || userEmail) && (
                <p className="mt-0.5 truncate text-xs text-muted-foreground">
                  {userName}
                  {userName && userEmail && ' · '}
                  {userEmail}
                </p>
              )}
            </div>
          </div>
        </SheetHeader>

        <SheetBody className="flex-1 overflow-y-auto px-5 py-5">
          {/* Senha gerada com sucesso */}
          {generatedPassword ? (
            <div className="flex flex-col gap-4">
              <div className="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950/30">
                <div className="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-green-600">
                  <Check className="size-3 text-white" />
                </div>
                <div>
                  <p className="text-sm font-semibold text-green-800 dark:text-green-300">
                    Senha redefinida com sucesso!
                  </p>
                  <p className="mt-1 text-xs text-green-700 dark:text-green-400">
                    Copie a senha gerada abaixo e compartilhe com o usuário de forma segura.
                  </p>
                </div>
              </div>

              <div>
                <p className="mb-1.5 text-xs font-medium text-muted-foreground uppercase tracking-wide">
                  Senha gerada
                </p>
                <div className="flex items-center gap-2 rounded-lg border border-border bg-muted/50 px-3 py-2.5">
                  <code className="flex-1 select-all font-mono text-sm font-semibold tracking-wider text-foreground">
                    {generatedPassword}
                  </code>
                  <button
                    type="button"
                    onClick={handleCopy}
                    className={cn(
                      'flex shrink-0 items-center gap-1.5 rounded px-2 py-1 text-xs font-medium transition-colors',
                      copied
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                        : 'bg-background text-muted-foreground hover:text-foreground hover:bg-accent border border-border',
                    )}
                  >
                    {copied ? (
                      <>
                        <Check className="size-3" /> Copiado
                      </>
                    ) : (
                      <>
                        <Copy className="size-3" /> Copiar
                      </>
                    )}
                  </button>
                </div>
              </div>

              <div className="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-950/20">
                <ShieldAlert className="mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-400" />
                <p className="text-xs text-amber-700 dark:text-amber-300">
                  Esta senha não será exibida novamente. Guarde-a em local seguro antes de fechar.
                </p>
              </div>
            </div>
          ) : (
            <Form {...form}>
              <form id="reset-password-form" onSubmit={form.handleSubmit(onSubmit)} className="flex flex-col gap-5">
                {/* Checkbox senha automática */}
                <FormField
                  control={form.control}
                  name="automatic_password"
                  render={({ field }) => (
                    <FormItem className="flex items-start gap-3 rounded-lg border border-border bg-muted/30 px-4 py-3">
                      <FormControl>
                        <Checkbox
                          checked={field.value}
                          onCheckedChange={field.onChange}
                          className="mt-0.5"
                        />
                      </FormControl>
                      <div className="leading-none">
                        <FormLabel className="cursor-pointer text-sm font-medium">
                          Criar uma senha automaticamente
                        </FormLabel>
                        <p className="mt-1 text-xs text-muted-foreground">
                          O sistema gerará uma senha segura e a exibirá após a redefinição.
                        </p>
                      </div>
                    </FormItem>
                  )}
                />

                {/* Campos manuais — visíveis apenas quando automático está desmarcado */}
                {!automaticPassword && (
                  <div className="flex flex-col gap-4">
                    <FormField
                      control={form.control}
                      name="password"
                      render={({ field, fieldState }) => (
                        <FormItem>
                          <FormLabel>Nova Senha</FormLabel>
                          <FormControl>
                            <PasswordInput
                              id="reset-password"
                              value={field.value}
                              onChange={field.onChange}
                              placeholder="Mínimo 8 caracteres"
                              aria-invalid={fieldState.invalid ? 'true' : 'false'}
                              aria-describedby={fieldState.error ? 'reset-password-error' : undefined}
                            />
                          </FormControl>
                          <FormMessage id="reset-password-error" />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="password_confirmation"
                      render={({ field, fieldState }) => (
                        <FormItem>
                          <FormLabel>Confirmar Nova Senha</FormLabel>
                          <FormControl>
                            <PasswordInput
                              id="reset-password-confirmation"
                              value={field.value}
                              onChange={field.onChange}
                              placeholder="Repita a nova senha"
                              aria-invalid={fieldState.invalid ? 'true' : 'false'}
                              aria-describedby={fieldState.error ? 'reset-password-confirmation-error' : undefined}
                            />
                          </FormControl>
                          <FormMessage id="reset-password-confirmation-error" />
                        </FormItem>
                      )}
                    />
                  </div>
                )}

                {/* Checkbox exigir troca */}
                <FormField
                  control={form.control}
                  name="require_change"
                  render={({ field }) => (
                    <FormItem className="flex items-start gap-3 rounded-lg border border-border bg-muted/30 px-4 py-3">
                      <FormControl>
                        <Checkbox
                          checked={field.value}
                          onCheckedChange={field.onChange}
                          className="mt-0.5"
                        />
                      </FormControl>
                      <div className="leading-none">
                        <FormLabel className="cursor-pointer text-sm font-medium">
                          Exigir alteração no próximo login
                        </FormLabel>
                        <p className="mt-1 text-xs text-muted-foreground">
                          O usuário será obrigado a definir uma nova senha ao entrar.
                        </p>
                      </div>
                    </FormItem>
                  )}
                />
              </form>
            </Form>
          )}
        </SheetBody>

        <SheetFooter className="border-t border-border px-5 py-3">
          {generatedPassword ? (
            <Button
              type="button"
              className="w-full"
              onClick={() => onOpenChange(false)}
            >
              Fechar
            </Button>
          ) : (
            <div className="flex w-full gap-2">
              <Button
                type="button"
                variant="outline"
                className="flex-1"
                onClick={() => onOpenChange(false)}
                disabled={isSubmitting}
              >
                Cancelar
              </Button>
              <Button
                type="submit"
                form="reset-password-form"
                className="flex-1 bg-amber-600 hover:bg-amber-700 text-white border-0"
                disabled={isSubmitting}
              >
                {isSubmitting ? (
                  <>
                    <Loader2 className="size-4 animate-spin" />
                    Redefinindo...
                  </>
                ) : (
                  <>
                    <KeyRound className="size-4" />
                    Redefinir Senha
                  </>
                )}
              </Button>
            </div>
          )}
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
