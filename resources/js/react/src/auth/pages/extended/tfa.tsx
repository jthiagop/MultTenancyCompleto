import { useState } from 'react';
import { MoveLeft } from 'lucide-react';
import { Link } from 'react-router';
import { toAbsoluteUrl } from '@/lib/helpers';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

/** Tela de exemplo 2FA (UI Metronic) — integração com backend a definir */
export function TwoFactorAuth() {
  const [codeInputs, setCodeInputs] = useState(Array(6).fill(''));

  const handleInputChange = (index: number, value: string) => {
    if (value.length > 1) return;
    const updatedInputs = [...codeInputs];
    updatedInputs[index] = value;
    setCodeInputs(updatedInputs);
  };

  return (
    <div className="flex flex-col gap-5 max-w-md mx-auto w-full p-6 min-h-[50vh] justify-center">
      <img
        src={toAbsoluteUrl('/media/illustrations/34.svg')}
        className="dark:hidden h-20 mb-2 mx-auto"
        alt=""
      />
      <img
        src={toAbsoluteUrl('/media/illustrations/34-dark.svg')}
        className="light:hidden h-20 mb-2 mx-auto"
        alt=""
      />

      <div className="text-center mb-2">
        <h3 className="text-lg font-medium text-mono mb-5">Verificação em duas etapas</h3>
        <div className="flex flex-col">
          <span className="text-sm text-secondary-foreground mb-1.5">
            Digite o código enviado ao seu dispositivo
          </span>
        </div>
      </div>

      <div className="flex flex-wrap justify-center gap-1.5">
        {codeInputs.map((value, index) => (
          <Input
            key={index}
            type="text"
            inputMode="numeric"
            maxLength={1}
            className="size-10 shrink-0 px-0 text-center"
            value={value}
            onChange={(e) => handleInputChange(index, e.target.value)}
          />
        ))}
      </div>

      <div className="flex items-center justify-center mb-2">
        <span className="text-sm text-secondary-foreground me-1.5">Não recebeu o código?</span>
        <Link to="/auth/classic/signin" className="font-semibold text-foreground hover:text-primary text-sm">
          Reenviar
        </Link>
      </div>

      <Button className="w-full" type="button">
        Continuar
      </Button>

      <Link
        to="/auth/signin"
        className="gap-2.5 flex items-center justify-center text-sm font-semibold text-foreground hover:text-primary"
      >
        <MoveLeft className="size-3.5 opacity-70" />
        Voltar ao login
      </Link>
    </div>
  );
}
