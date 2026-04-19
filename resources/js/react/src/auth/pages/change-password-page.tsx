import { useEffect, useMemo, useState } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { AlertCircle, Eye, EyeOff } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { Link, useSearchParams } from 'react-router';
import { useAuthAppData } from '@/auth/hooks/use-auth-app-data';
import { submitLaravelPostForm } from '@/auth/lib/laravel-post-form';
import {
  getNewPasswordSchema,
  type NewPasswordSchemaType,
} from '@/auth/forms/reset-password-schema';
import { Alert, AlertIcon, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
  Form,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';

/**
 * Nova senha após link do Laravel (token + e-mail na query).
 * O e-mail padrão do framework costuma apontar para a rota Blade reset-password/{token};
 * use esta URL apenas se configurar o link para /app/auth/change-password?token=...&email=...
 */
export function ChangePasswordPage() {
  const [searchParams] = useSearchParams();
  const { csrfToken, urls, validationErrors } = useAuthAppData();
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [confirmPasswordVisible, setConfirmPasswordVisible] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const token = useMemo(
    () =>
      searchParams.get('token') ||
      searchParams.get('code') ||
      searchParams.get('token_hash') ||
      '',
    [searchParams],
  );
  const email = searchParams.get('email') || '';

  const hasToken = Boolean(token && email);

  useEffect(() => {
    const pwdErr = validationErrors?.password?.[0] || validationErrors?.email?.[0];
    if (pwdErr) setError(pwdErr);
  }, [validationErrors]);

  const form = useForm<NewPasswordSchemaType>({
    resolver: zodResolver(getNewPasswordSchema()),
    defaultValues: { password: '', confirmPassword: '' },
  });

  function onSubmit(values: NewPasswordSchemaType) {
    setError(null);
    submitLaravelPostForm(urls.passwordStore, {
      _token: csrfToken,
      token,
      email,
      password: values.password,
      password_confirmation: values.confirmPassword,
    });
  }

  if (!hasToken) {
    return (
      <div className="max-w-md mx-auto space-y-5">
        <div className="text-center space-y-2">
          <h1 className="text-2xl font-bold tracking-tight">Nova senha</h1>
          <p className="text-sm text-muted-foreground">
            É necessário um link válido com token e e-mail para definir a nova senha.
          </p>
        </div>

        <div className="bg-muted/50 p-4 rounded-lg border border-border text-sm text-muted-foreground space-y-2">
          <p>Abra o link enviado por e-mail. Se o sistema abrir a página tradicional do Dominus, conclua o processo lá.</p>
        </div>

        <Button asChild variant="outline" className="w-full">
          <Link to="/auth/reset-password">Solicitar novo link</Link>
        </Button>

        <div className="text-center text-sm">
          <Link to="/auth/signin" className="text-primary hover:underline">
            Voltar ao login
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-md mx-auto">
      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
          <div className="text-center space-y-2">
            <h1 className="text-2xl font-bold tracking-tight">Definir nova senha</h1>
            <p className="text-muted-foreground text-sm">Crie uma senha forte para sua conta</p>
          </div>

          {error && (
            <Alert variant="destructive" appearance="light" close onClose={() => setError(null)}>
              <AlertIcon>
                <AlertCircle className="h-4 w-4" />
              </AlertIcon>
              <AlertTitle>{error}</AlertTitle>
            </Alert>
          )}

          <FormField
            control={form.control}
            name="password"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Nova senha</FormLabel>
                <div className="relative">
                  <Input
                    placeholder="Nova senha"
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
                    {passwordVisible ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                  </Button>
                </div>
                <FormMessage />
              </FormItem>
            )}
          />

          <FormField
            control={form.control}
            name="confirmPassword"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Confirmar senha</FormLabel>
                <div className="relative">
                  <Input
                    placeholder="Repita a senha"
                    type={confirmPasswordVisible ? 'text' : 'password'}
                    autoComplete="new-password"
                    {...field}
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    mode="icon"
                    onClick={() => setConfirmPasswordVisible(!confirmPasswordVisible)}
                    className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                  >
                    {confirmPasswordVisible ? (
                      <EyeOff className="h-4 w-4" />
                    ) : (
                      <Eye className="h-4 w-4" />
                    )}
                  </Button>
                </div>
                <FormMessage />
              </FormItem>
            )}
          />

          <Button type="submit" className="w-full">
            Salvar senha
          </Button>

          <div className="text-center text-sm">
            <Link to="/auth/signin" className="text-primary hover:underline">
              Voltar ao login
            </Link>
          </div>
        </form>
      </Form>
    </div>
  );
}
