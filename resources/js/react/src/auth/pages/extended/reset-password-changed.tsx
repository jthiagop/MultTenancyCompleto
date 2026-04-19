import { Link } from 'react-router';
import { toAbsoluteUrl } from '@/lib/helpers';
import { Button } from '@/components/ui/button';

export function ResetPasswordChanged() {
  return (
    <div className="px-1 py-2">
      <div className="flex justify-center mb-5">
        <img
          src={toAbsoluteUrl('/media/illustrations/32.svg')}
          className="dark:hidden max-h-[180px]"
          alt=""
        />
        <img
          src={toAbsoluteUrl('/media/illustrations/32-dark.svg')}
          className="light:hidden max-h-[180px]"
          alt=""
        />
      </div>

      <h3 className="text-lg font-medium text-mono text-center mb-4">Senha alterada</h3>
      <div className="text-sm text-center text-secondary-foreground mb-7.5">
        Sua senha foi atualizada com sucesso.
      </div>

      <Button asChild className="w-full">
        <Link to="/auth/signin">Entrar</Link>
      </Button>
    </div>
  );
}
