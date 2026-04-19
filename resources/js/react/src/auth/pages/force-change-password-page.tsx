import { useEffect, useState } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { AlertCircle, Eye, EyeOff, KeyRound, ShieldAlert } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { useNavigate } from 'react-router';
import { Alert, AlertIcon, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';

const forceChangeSchema = z
  .object({
    current_password: z.string().min(1, 'Informe sua senha atual.'),
    password: z.string().min(8, 'A nova senha deve ter no mínimo 8 caracteres.').max(256),
    password_confirmation: z.string(),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: 'As senhas não conferem.',
    path: ['password_confirmation'],
  });

type ForceChangeValues = z.infer<typeof forceChangeSchema>;

export function ForceChangePasswordPage() {
  const navigate = useNavigate();
  const [error, setError] = useState<string | null>(null);
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [confirmVisible, setConfirmVisible] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [loading, setLoading] = useState(true);

  const form = useForm<ForceChangeValues>({
    resolver: zodResolver(forceChangeSchema),
    defaultValues: { current_password: '', password: '', password_confirmation: '' },
  });

  // Verifica se o usuário está autenticado e precisa trocar a senha
  useEffect(() => {
    fetch('/sanctum/csrf-cookie', { credentials: 'include' })
      .then(() =>
        fetch('/api/auth/status', {
          credentials: 'same-origin',
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        }),
      )
      .then(async (r) => {
        if (!r.ok || !r.headers.get('content-type')?.includes('application/json')) {
          navigate('/auth/signin', { replace: true });
          return;
        }
        const data = await r.json();
        if (data.authenticated && data.must_change_password) {
          setLoading(false);
        } else if (data.authenticated) {
          navigate('/auth/signin?pwd_reset=success', { replace: true });
        } else {
          navigate('/auth/signin', { replace: true });
        }
      })
      .catch(() => {
        navigate('/auth/signin', { replace: true });
      });
  }, [navigate]);

  /** Lê o XSRF-TOKEN do cookie (já encriptado pelo Laravel) */
  function getXsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
  }

  async function onSubmit(values: ForceChangeValues) {
    setError(null);
    setSubmitting(true);
    const xsrf = getXsrfToken();
    try {
      const res = await fetch('/password/change', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-XSRF-TOKEN': xsrf,
          'X-React-Web': '1',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'include',
        body: JSON.stringify({
          current_password: values.current_password,
          password: values.password,
          password_confirmation: values.password_confirmation,
        }),
      });

      const json = await res.json().catch(() => ({}));

      if (!res.ok) {
        const errs = json.errors as Record<string, string[]> | undefined;
        if (errs?.current_password?.[0]) {
          form.setError('current_password', { message: errs.current_password[0] });
        } else if (errs?.password?.[0]) {
          form.setError('password', { message: errs.password[0] });
        } else {
          setError(json.message ?? 'Erro ao alterar a senha. Tente novamente.');
        }
        return;
      }

      navigate('/auth/signin?pwd_reset=success', { replace: true });
    } catch {
      setError('Erro de conexão. Tente novamente.');
    } finally {
      setSubmitting(false);
    }
  }

  if (loading) {
    return (
      <div className="flex w-full items-center justify-center py-12">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
      </div>
    );
  }

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="block w-full space-y-5">
        <div className="text-center space-y-1 pb-2">
          <div className="flex justify-center mb-3">
            <div className="flex size-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
              <KeyRound className="size-5 text-amber-600 dark:text-amber-400" />
            </div>
          </div>
          <h1 className="text-2xl font-semibold tracking-tight">Altere sua senha</h1>
          <p className="text-sm text-muted-foreground">
            Por segurança, defina uma nova senha antes de continuar.
          </p>
        </div>

        <div className="flex items-start gap-2.5 rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-3 dark:border-amber-800 dark:bg-amber-950/20">
          <ShieldAlert className="mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-400" />
          <p className="text-xs text-amber-700 dark:text-amber-300">
            A senha deve ter pelo menos 3 dos seguintes: letras maiúsculas, minúsculas, números e símbolos.
          </p>
        </div>

        {error && (
          <Alert variant="destructive" appearance="light" close onClose={() => setError(null)}>
            <AlertIcon><AlertCircle /></AlertIcon>
            <AlertTitle>{error}</AlertTitle>
          </Alert>
        )}

        <FormField
          control={form.control}
          name="current_password"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Senha atual</FormLabel>
              <FormControl>
                <Input
                  placeholder="Digite sua senha atual"
                  type="password"
                  autoComplete="current-password"
                  autoFocus
                  {...field}
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="password"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Nova senha</FormLabel>
              <div className="relative">
                <Input
                  placeholder="Mínimo 8 caracteres"
                  type={passwordVisible ? 'text' : 'password'}
                  autoComplete="new-password"
                  {...field}
                />
                <Button
                  type="button"
                  variant="ghost"
                  mode="icon"
                  onClick={() => setPasswordVisible(!passwordVisible)}
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                >
                  {passwordVisible ? (
                    <EyeOff className="text-muted-foreground" />
                  ) : (
                    <Eye className="text-muted-foreground" />
                  )}
                </Button>
              </div>
              <FormMessage />
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="password_confirmation"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Confirmar nova senha</FormLabel>
              <div className="relative">
                <Input
                  placeholder="Repita a nova senha"
                  type={confirmVisible ? 'text' : 'password'}
                  autoComplete="new-password"
                  {...field}
                />
                <Button
                  type="button"
                  variant="ghost"
                  mode="icon"
                  onClick={() => setConfirmVisible(!confirmVisible)}
                  className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                >
                  {confirmVisible ? (
                    <EyeOff className="text-muted-foreground" />
                  ) : (
                    <Eye className="text-muted-foreground" />
                  )}
                </Button>
              </div>
              <FormMessage />
            </FormItem>
          )}
        />

        <Button type="submit" className="w-full" disabled={submitting}>
          {submitting ? 'Salvando...' : 'Salvar nova senha'}
        </Button>
      </form>
    </Form>
  );
}
