import { Link } from 'react-router';
import { toAbsoluteUrl } from '@/lib/helpers';
import { Button } from '@/components/ui/button';

export function CheckEmail() {
  return (
    <>
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
        Clique no link enviado para confirmar sua conta.
      </div>

      <Button asChild className="w-full mb-5">
        <Link to="/">Voltar ao início</Link>
      </Button>

      <div className="flex items-center justify-center gap-1">
        <span className="text-sm text-secondary-foreground">Não recebeu?</span>
        <Link to="/auth/signin" className="text-sm font-semibold text-foreground hover:text-primary">
          Reenviar
        </Link>
      </div>
    </>
  );
}
