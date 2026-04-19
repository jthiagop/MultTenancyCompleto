import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { BellOff, Bell } from 'lucide-react';

export function DoNotDisturb() {
  const [isPaused, setIsPaused] = useState(false);

  return (
    <Card>
      <CardHeader className="pb-2">
        <div className="flex items-center gap-2">
          <BellOff className="size-4 text-primary" />
          <CardTitle className="text-base">Não Perturbe</CardTitle>
        </div>
        <p className="text-sm text-muted-foreground">
          Pause temporariamente todas as notificações.
        </p>
      </CardHeader>
      <CardContent className="pt-2 flex flex-col gap-4">
        <div className="flex items-center gap-3 rounded-lg border p-3 bg-muted/40">
          {isPaused ? (
            <BellOff className="size-8 text-destructive shrink-0" />
          ) : (
            <Bell className="size-8 text-primary shrink-0" />
          )}
          <div>
            <p className="text-sm font-medium">
              {isPaused ? 'Notificações pausadas' : 'Notificações ativas'}
            </p>
            <p className="text-xs text-muted-foreground">
              {isPaused
                ? 'Você não receberá alertas enquanto estiver no modo não perturbe.'
                : 'Você está recebendo todas as notificações configuradas.'}
            </p>
          </div>
        </div>
        <Button
          variant={isPaused ? 'outline' : 'secondary'}
          className="w-full"
          onClick={() => setIsPaused((prev) => !prev)}
        >
          {isPaused ? (
            <>
              <Bell className="size-4 mr-2" />
              Retomar Notificações
            </>
          ) : (
            <>
              <BellOff className="size-4 mr-2" />
              Pausar Notificações
            </>
          )}
        </Button>
      </CardContent>
    </Card>
  );
}
