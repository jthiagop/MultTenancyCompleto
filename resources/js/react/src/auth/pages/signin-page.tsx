import { useEffect, useState } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { AlertCircle, Check, Eye, EyeOff } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { Link, useNavigate, useSearchParams } from 'react-router';
import { useAuthAppData } from '@/auth/hooks/use-auth-app-data';
import { getSigninSchema, type SigninSchemaType } from '@/auth/forms/signin-schema';
import { Alert, AlertIcon, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';

/**
 * Redireciona para uma URL do Laravel (painel).
 * No Vite dev a origem é diferente (porta 5174), então usamos a URL do backend.
 * Em produção (mesmo origin) funciona com path relativo normalmente.
 */
function redirectToPanel(path = '/app/') {
  if (import.meta.env.DEV && import.meta.env.VITE_LARAVEL_URL) {
    window.location.href = `${import.meta.env.VITE_LARAVEL_URL}${path}`;
  } else if (import.meta.env.DEV) {
    // Fallback dev: monta URL com hostname atual na porta 8000
    window.location.href = `${window.location.protocol}//${window.location.hostname}:8000${path}`;
  } else {
    window.location.href = path;
  }
}

export function SignInPage() {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const { csrfToken: csrfFromBlade, urls, flashStatus, validationErrors } = useAuthAppData();
  const [csrfToken, setCsrfToken] = useState(csrfFromBlade);
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  // Sem Blade (dev via Vite direto), o token vem do cookie após GET /sanctum/csrf-cookie.
  useEffect(() => {
    if (csrfFromBlade) return;
    fetch('/sanctum/csrf-cookie', { credentials: 'include' })
      .then(() => {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        if (match) setCsrfToken(decodeURIComponent(match[1]));
      })
      .catch(() => {});
  }, [csrfFromBlade]);

  // Ao montar: se o usuário já está autenticado e precisa trocar a senha,
  // redirecionar para a página dedicada. Não redireciona ao painel no mount
  // para evitar que a sessão do Blade (mesma domain, porta diferente) force
  // a saída da SPA React em dev.
  useEffect(() => {
    fetch('/api/auth/status', {
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then(async (r) => {
        if (!r.ok || !r.headers.get('content-type')?.includes('application/json')) return;
        const data = await r.json();
        if (data.authenticated && data.must_change_password) {
          navigate('/first-access', { replace: true });
        }
      })
      .catch(() => {});
  }, [navigate]);

  useEffect(() => {
    const pwdReset = searchParams.get('pwd_reset');
    if (pwdReset === 'success') {
      setSuccessMessage('Senha alterada com sucesso. Entre com a nova senha.');
    }
    if (searchParams.get('error') === 'oauth_unavailable') {
      setError('Login social não está disponível. Use e-mail e senha.');
    }
    const emailErr = validationErrors?.email?.[0];
    if (emailErr) setError(emailErr);
    if (flashStatus) setSuccessMessage(flashStatus);
  }, [searchParams, validationErrors, flashStatus]);

  const form = useForm<SigninSchemaType>({
    resolver: zodResolver(getSigninSchema()),
    defaultValues: {
      email: '',
      password: '',
      rememberMe: true,
    },
  });

  // ── Submit do login ────────────────────────────────────────────────────────
  async function onSubmit(values: SigninSchemaType) {
    if (!csrfToken) {
      setError('Carregando token de segurança, aguarde um momento e tente novamente.');
      return;
    }
    setError(null);
    form.clearErrors();
    setIsSubmitting(true);
    try {
      const res = await fetch(urls.login, {
        method: 'POST',
        redirect: 'manual',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-React-Web': '1',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          email: values.email,
          password: values.password,
          ...(values.rememberMe ? { remember: '1' } : {}),
        }),
      });

      // opaqueredirect = servidor emitiu um 302 (guest middleware ou redirect Blade).
      if (res.type === 'opaqueredirect') {
        try {
          const statusRes = await fetch('/api/auth/status', {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          });
          const statusData = await statusRes.json().catch(() => ({}));
          if (statusData.authenticated && statusData.must_change_password) {
            navigate('/first-access', { replace: true });
            return;
          }
        } catch {
          // fallback
        }
        // Em produção, o middleware guest redireciona antes do React.
        // No Vite dev, ficamos no login; o painel é servido pelo Laravel diretamente.
        redirectToPanel('/app/dashboard');
        return;
      }

      const data = (await res.json().catch(() => null)) ?? {};

      if (res.status === 422 && data.error === 'PASSWORD_CHANGE_REQUIRED') {
        navigate('/first-access', { replace: true });
        return;
      }

      if (!res.ok) {
        const errs = data.errors as Record<string, string[]> | undefined;
        if (errs?.email?.[0]) {
          form.setError('email', { message: errs.email[0] });
        } else if (errs?.password?.[0]) {
          form.setError('password', { message: errs.password[0] });
        } else {
          setError((data.message as string | undefined) ?? 'E-mail ou senha incorretos.');
        }
        return;
      }

      const redirect = (data.redirect as string | undefined) ?? '/app/dashboard';
      redirectToPanel(redirect);
    } catch {
      setError('Erro de conexão. Tente novamente.');
    } finally {
      setIsSubmitting(false);
    }
  }

  // ── Render: Login ──────────────────────────────────────────────────────────
  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="block w-full space-y-5">
        <div className="text-center space-y-1 pb-3">
          <h1 className="text-2xl font-semibold tracking-tight">Entrar</h1>
          <p className="text-sm text-muted-foreground">Use seu e-mail e senha para acessar o sistema.</p>
        </div>

        {error && (
          <Alert variant="destructive" appearance="light" close onClose={() => setError(null)}>
            <AlertIcon>
              <AlertCircle />
            </AlertIcon>
            <AlertTitle>{error}</AlertTitle>
          </Alert>
        )}

        {successMessage && (
          <Alert appearance="light" close onClose={() => setSuccessMessage(null)}>
            <AlertIcon>
              <Check />
            </AlertIcon>
            <AlertTitle>{successMessage}</AlertTitle>
          </Alert>
        )}

        <FormField
          control={form.control}
          name="email"
          render={({ field }) => (
            <FormItem>
              <FormLabel>E-mail</FormLabel>
              <FormControl>
                <Input type="email" autoComplete="email" placeholder="seu@email.com" {...field} />
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
              <div className="flex justify-between items-center gap-2.5">
                <FormLabel>Senha</FormLabel>
              </div>
              <div className="relative">
                <Input
                  placeholder="Sua senha"
                  type={passwordVisible ? 'text' : 'password'}
                  autoComplete="current-password"
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
          name="rememberMe"
          render={({ field }) => (
            <FormItem className="flex flex-col space-y-2">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <FormControl>
                    <Checkbox checked={field.value} onCheckedChange={field.onChange} />
                  </FormControl>
                  <FormLabel className="text-sm font-normal cursor-pointer">Lembrar-me</FormLabel>
                </div>
                <Link to="/auth/reset-password" className="text-sm font-semibold text-foreground hover:text-primary">
                  Esqueceu a senha?
                </Link>
              </div>
            </FormItem>
          )}
        />

        <Button type="submit" className="w-full" disabled={isSubmitting}>
          <span className="flex items-center gap-2 justify-center">
            Entrar
          </span>
        </Button>

        <div className="text-center text-sm text-muted-foreground">
          Não tem conta?{' '}
          <Link to="/auth/signup" className="text-sm font-semibold text-foreground hover:text-primary">
            Cadastre-se
          </Link>
        </div>
      </form>
    </Form>
  );
}
