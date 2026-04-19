import { useEffect, useState } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { AlertCircle, Check, MoveLeft } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { Link } from 'react-router';
import { useAuthAppData } from '@/auth/hooks/use-auth-app-data';
import { submitLaravelPostForm } from '@/auth/lib/laravel-post-form';
import {
  getResetRequestSchema,
  type ResetRequestSchemaType,
} from '@/auth/forms/reset-password-schema';
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

export function ResetPasswordPage() {
  const { csrfToken, urls, validationErrors, flashStatus } = useAuthAppData();
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  useEffect(() => {
    const emailErr = validationErrors?.email?.[0];
    if (emailErr) setError(emailErr);
    if (flashStatus) setSuccessMessage(flashStatus);
  }, [validationErrors, flashStatus]);

  const form = useForm<ResetRequestSchemaType>({
    resolver: zodResolver(getResetRequestSchema()),
    defaultValues: { email: '' },
  });

  function onSubmit(values: ResetRequestSchemaType) {
    setError(null);
    submitLaravelPostForm(urls.passwordEmail, {
      _token: csrfToken,
      email: values.email,
    });
  }

  return (
    <div className="max-w-md mx-auto">
      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-5">
          <div className="text-center space-y-2">
            <h1 className="text-2xl font-bold tracking-tight">Recuperar senha</h1>
            <p className="text-sm text-muted-foreground">
              Informe seu e-mail para receber o link de redefinição
            </p>
          </div>

          {error && (
            <Alert variant="destructive" appearance="light" close onClose={() => setError(null)}>
              <AlertIcon>
                <AlertCircle className="h-4 w-4" />
              </AlertIcon>
              <AlertTitle>{error}</AlertTitle>
            </Alert>
          )}

          {successMessage && (
            <Alert appearance="light">
              <AlertIcon>
                <Check className="h-4 w-4 text-green-500" />
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
                  <Input
                    placeholder="seu@email.com"
                    type="email"
                    autoComplete="email"
                    {...field}
                  />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />

          <Button type="submit" className="w-full">
            Enviar link
          </Button>

          <div className="text-center text-sm">
            <Link
              to="/auth/signin"
              className="inline-flex items-center gap-2 text-sm font-semibold text-accent-foreground hover:underline hover:underline-offset-2"
            >
              <MoveLeft className="size-3.5 opacity-70" /> Voltar ao login
            </Link>
          </div>
        </form>
      </Form>
    </div>
  );
}
