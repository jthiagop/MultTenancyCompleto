import { useEffect, useState } from 'react';
import { Link } from 'react-router';
import { toAbsoluteUrl } from '@/lib/helpers';
import { Button } from '@/components/ui/button';

export function ResetPasswordCheckEmail() {
  const [email, setEmail] = useState<string | null>(null);

  useEffect(() => {
    setEmail(new URLSearchParams(window.location.search).get('email'));
  }, []);

  return (
    <div className="w-full">
      <div className="flex justify-center py-10">
        <img
          src={toAbsoluteUrl('/media/illustrations/30.svg')}
          className="dark:hidden max-h-[130px]"
          alt=""
        />
        <img
          src={toAbsoluteUrl('/media/illustrations/30-dark.svg')}
          className="light:hidden max-h-[130px]"
          alt=""
        />
      </div>

      <h3 className="text-lg font-medium text-mono text-center mb-3">Verifique seu e-mail</h3>
      <div className="text-sm text-center text-secondary-foreground mb-7.5">
        Abra o link enviado para{' '}
        <span className="text-foreground font-medium">{email ?? 'seu e-mail'}</span>
        <br />
        para redefinir a senha.
      </div>

      <Button asChild variant="outline" className="w-full mb-5">
        <Link to="/auth/reset-password/changed">Continuar</Link>
      </Button>

      <div className="flex items-center justify-center gap-1">
        <span className="text-xs text-secondary-foreground">Não recebeu?</span>
        <Link to="/auth/reset-password" className="text-xs font-medium text-primary hover:underline">
          Solicitar de novo
        </Link>
      </div>
    </div>
  );
}
