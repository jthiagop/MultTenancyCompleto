import { createContext, useCallback, useContext, useEffect, useRef, useState } from 'react';
import { LogIn, LogOut } from 'lucide-react';
import { toAbsoluteUrl } from '@/lib/helpers';
import {
  Dialog,
  DialogBody,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

const SessionExpiredContext = createContext<{ markExpired: () => void }>({
  markExpired: () => {},
});

export function useSessionExpired() {
  return useContext(SessionExpiredContext);
}

export function SessionExpiredProvider({ children }: { children: React.ReactNode }) {
  const [expired, setExpired] = useState(false);
  const patchedRef = useRef(false);

  const markExpired = useCallback(() => setExpired(true), []);

  useEffect(() => {
    if (patchedRef.current) return;
    patchedRef.current = true;

    const originalFetch = window.fetch;
    window.fetch = async (...args: Parameters<typeof fetch>) => {
      const response = await originalFetch(...args);
      if (response.status === 401) {
        setExpired(true);
      }
      return response;
    };
  }, []);

  return (
    <SessionExpiredContext.Provider value={{ markExpired }}>
      {children}
      <SessionExpiredDialog open={expired} />
    </SessionExpiredContext.Provider>
  );
}

function SessionExpiredDialog({ open }: { open: boolean }) {
  return (
    <Dialog open={open}>
      <DialogContent
        className="w-full max-w-[440px] max-h-[95%]"
        showCloseButton={false}
        onPointerDownOutside={(e) => e.preventDefault()}
        onEscapeKeyDown={(e) => e.preventDefault()}
      >
        <DialogHeader className="justify-end border-0 pt-5">
          <DialogTitle />
          <DialogDescription />
        </DialogHeader>
        <DialogBody className="flex flex-col items-center pt-0 pb-10">
          <div className="mb-8">
            <img
              src={toAbsoluteUrl('/media/illustrations/23.svg')}
              className="dark:hidden max-h-[140px]"
              alt="Sessão expirada"
            />
            <img
              src={toAbsoluteUrl('/media/illustrations/23-dark.svg')}
              className="light:hidden max-h-[140px]"
              alt="Sessão expirada"
            />
          </div>

          <div className="flex items-center gap-2 mb-3">
            <LogOut className="size-5 text-destructive" />
            <h3 className="text-lg font-semibold text-foreground">
              Sessão Expirada
            </h3>
          </div>

          <p className="text-sm text-center text-muted-foreground mb-7 max-w-xs">
            Sua sessão expirou por inatividade.
            <br />
            Faça login novamente para continuar.
          </p>

          <Button
            variant="primary"
            size="lg"
            className="gap-2 min-w-[200px]"
            onClick={() => { window.location.href = '/login'; }}
          >
            <LogIn className="size-4" />
            Ir para Login
          </Button>
        </DialogBody>
      </DialogContent>
    </Dialog>
  );
}
