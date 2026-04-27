import { useEffect, useRef, useState } from 'react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { Loader2 } from 'lucide-react';
import { notify } from '@/lib/notify';

export interface CentroCustoCreatedPayload {
  id: string;
  code: string;
  name: string;
}

interface CentroCustoQuickCreateSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onCreated: (c: CentroCustoCreatedPayload) => void;
}

function ActionBar({
  submitting,
  onCancel,
}: {
  submitting: boolean;
  onCancel: () => void;
}) {
  return (
    <div className="flex items-center gap-2.5 shrink-0">
      <Button type="button" variant="outline" onClick={onCancel} disabled={submitting}>
        Cancelar
      </Button>
      <Button
        type="submit"
        className="bg-blue-600 hover:bg-blue-700 text-white border-0"
        disabled={submitting}
      >
        {submitting ? (
          <>
            <Loader2 className="size-3.5 animate-spin" />
            Salvando…
          </>
        ) : (
          'Salvar'
        )}
      </Button>
    </div>
  );
}

/**
 * Cadastro rápido de Centro de Custo (espelha o drawer Blade
 * `drawer_centro_custo.blade.php`):
 *   POST JSON em `costCenter.storeAjax` → `/costCenter/store-ajax`
 *
 * Validações inline:
 *  - Código: obrigatório + numérico + checagem de duplicidade (debounce 400 ms)
 *  - Nome: obrigatório
 */
export function CentroCustoQuickCreateSheet({
  open,
  onOpenChange,
  onCreated,
}: CentroCustoQuickCreateSheetProps) {
  const { csrfToken } = useAppData();

  const [code, setCode] = useState('');
  const [name, setName] = useState('');
  const [codeError, setCodeError] = useState('');
  const [codeValid, setCodeValid] = useState(false);
  const [nameError, setNameError] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [checkingCode, setCheckingCode] = useState(false);
  const codeDuplicado = useRef(false);
  const checkTimeout = useRef<ReturnType<typeof setTimeout> | null>(null);

  useEffect(() => {
    if (!open) return;
    setCode('');
    setName('');
    setCodeError('');
    setCodeValid(false);
    setNameError('');
    codeDuplicado.current = false;
  }, [open]);

  function handleCodeChange(val: string) {
    setCode(val);
    setCodeError('');
    setCodeValid(false);
    codeDuplicado.current = false;
  }

  function handleCodeBlur() {
    const trimmed = code.trim();
    if (!trimmed) return;

    if (checkTimeout.current) clearTimeout(checkTimeout.current);

    checkTimeout.current = setTimeout(async () => {
      setCheckingCode(true);
      try {
        const res = await fetch('/costCenter/check-code', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken ?? '',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
          body: JSON.stringify({ code: trimmed }),
        });
        const data = (await res.json()) as { exists: boolean; message?: string };
        if (data.exists) {
          codeDuplicado.current = true;
          setCodeError(data.message ?? 'Este código já está em uso.');
          setCodeValid(false);
        } else {
          codeDuplicado.current = false;
          setCodeValid(true);
        }
      } catch {
        // silently ignore — o submit vai capturar o erro do servidor
      } finally {
        setCheckingCode(false);
      }
    }, 400);
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();

    let hasError = false;

    if (!code.trim()) {
      setCodeError('O código é obrigatório.');
      hasError = true;
    } else if (!/^\d+$/.test(code.trim())) {
      setCodeError('O código deve ser numérico.');
      hasError = true;
    } else if (codeDuplicado.current) {
      setCodeError('Este código já está em uso.');
      hasError = true;
    }

    if (!name.trim()) {
      setNameError('O nome é obrigatório.');
      hasError = true;
    }

    if (hasError) return;

    if (!csrfToken) {
      notify.reload();
      return;
    }

    setSubmitting(true);
    try {
      const res = await fetch('/costCenter/store-ajax', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ code: code.trim(), name: name.trim() }),
      });

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        data?: { id?: number; code?: string; name?: string };
        errors?: Record<string, string[]>;
      };

      if (res.ok && result.success && result.data?.id != null) {
        const created = result.data;
        notify.success(
          'Centro de Custo criado!',
          `${created.code} – ${created.name} foi adicionado com sucesso.`,
        );
        onCreated({
          id: String(created.id),
          code: String(created.code ?? code.trim()),
          name: String(created.name ?? name.trim()),
        });
        onOpenChange(false);
      } else {
        notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
        if (result.errors) {
          if (result.errors.code?.[0]) setCodeError(result.errors.code[0]);
          if (result.errors.name?.[0]) setNameError(result.errors.name[0]);
        }
      }
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  }

  const contextHint = 'Cadastro rápido de Centro de Custo';

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        overlayClassName="z-[55]"
        className={cn(
          'z-[60] gap-0 lg:w-[480px] sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '[&_[data-slot=sheet-close]]:top-4.5 [&_[data-slot=sheet-close]]:end-5',
        )}
      >
        <SheetHeader className="border-b py-3.5 px-5 border-border space-y-0">
          <SheetTitle className="font-medium">Novo Centro de Custo</SheetTitle>
        </SheetHeader>

        <form id="centro-custo-quick-create-form" onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
          <SheetBody className="grow p-0 flex flex-col min-h-0">
            <div className="flex justify-between gap-2 flex-wrap border-b border-border p-5 pt-1 items-center">
              <span className="text-xs text-muted-foreground font-medium">{contextHint}</span>
              <ActionBar submitting={submitting} onCancel={() => onOpenChange(false)} />
            </div>

            <ScrollArea className="h-[calc(100dvh-14rem)] ps-5 pe-4 me-1 pb-5">
              <div className="space-y-5 mt-5.5">
                <Card className="rounded-md">
                  <CardHeader className="min-h-[38px] bg-accent/50 py-2">
                    <CardTitle className="text-2sm">Identificação</CardTitle>
                  </CardHeader>
                  <CardContent className="pt-4 space-y-4">
                    <p className="text-xs text-muted-foreground">
                      Preencha o código e o nome para criar o centro de custo. O código deve ser único e numérico.
                    </p>

                    {/* Código */}
                    <div className="space-y-1.5">
                      <Label className="text-xs">
                        Código <span className="text-destructive">*</span>
                      </Label>
                      <div className="relative">
                        <Input
                          type="number"
                          min={1}
                          value={code}
                          onChange={(e) => handleCodeChange(e.target.value)}
                          onBlur={handleCodeBlur}
                          placeholder="Ex.: 10"
                          className={cn(
                            codeError && 'border-destructive focus-visible:ring-destructive/30',
                            codeValid && !codeError && 'border-green-500 focus-visible:ring-green-500/30',
                          )}
                        />
                        {checkingCode && (
                          <Loader2 className="absolute right-3 top-1/2 -translate-y-1/2 size-3.5 animate-spin text-muted-foreground" />
                        )}
                      </div>
                      {codeError && (
                        <p className="text-xs text-destructive">{codeError}</p>
                      )}
                    </div>

                    {/* Nome */}
                    <div className="space-y-1.5">
                      <Label className="text-xs">
                        Nome <span className="text-destructive">*</span>
                      </Label>
                      <Input
                        value={name}
                        onChange={(e) => { setName(e.target.value); setNameError(''); }}
                        placeholder="Ex.: Administrativo, Marketing, TI…"
                        className={cn(
                          nameError && 'border-destructive focus-visible:ring-destructive/30',
                        )}
                      />
                      {nameError && (
                        <p className="text-xs text-destructive">{nameError}</p>
                      )}
                    </div>
                  </CardContent>
                </Card>
              </div>
            </ScrollArea>
          </SheetBody>

          <SheetFooter className="flex-row border-t justify-between items-center p-5 border-border gap-2 sm:space-x-0">
            <span className="text-xs text-muted-foreground font-medium max-sm:hidden">{contextHint}</span>
            <ActionBar submitting={submitting} onCancel={() => onOpenChange(false)} />
          </SheetFooter>
        </form>
      </SheetContent>
    </Sheet>
  );
}
