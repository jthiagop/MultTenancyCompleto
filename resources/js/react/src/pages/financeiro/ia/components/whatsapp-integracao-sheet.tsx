import { useCallback, useEffect, useRef, useState } from 'react';
import { Loader2, Copy, Check } from 'lucide-react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { notify } from '@/lib/notify';

const QR_URL = '/whatsapp/instance/qrcode';
const STATUS_PATH = '/whatsapp/instance/status';

const JSON_HEADERS = {
  Accept: 'application/json',
  'X-Requested-With': 'XMLHttpRequest',
} as const;

function statusUrl(code: string) {
  return `${STATUS_PATH}/${encodeURIComponent(code)}`;
}

type Props = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSuccess?: () => void;
};

export function WhatsappIntegracaoSheet({ open, onOpenChange, onSuccess }: Props) {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [qrBase64, setQrBase64] = useState<string | null>(null);
  const [code, setCode] = useState<string | null>(null);
  const [waLink, setWaLink] = useState<string | null>(null);
  const [expired, setExpired] = useState(false);
  const [success, setSuccess] = useState(false);
  const [countdown, setCountdown] = useState('10:00');
  const [showTimer, setShowTimer] = useState(false);
  const [copied, setCopied] = useState(false);

  const countdownIntervalRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const statusIntervalRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const abortRef = useRef<AbortController | null>(null);
  const expirationMsRef = useRef<number | null>(null);
  const isGeneratingRef = useRef(false);
  const currentCodeRef = useRef<string | null>(null);

  const stopTimers = useCallback(() => {
    if (countdownIntervalRef.current) {
      clearInterval(countdownIntervalRef.current);
      countdownIntervalRef.current = null;
    }
    if (statusIntervalRef.current) {
      clearInterval(statusIntervalRef.current);
      statusIntervalRef.current = null;
    }
    setShowTimer(false);
  }, []);

  const cleanup = useCallback(() => {
    stopTimers();
    if (abortRef.current) {
      abortRef.current.abort();
      abortRef.current = null;
    }
    isGeneratingRef.current = false;
    currentCodeRef.current = null;
  }, [stopTimers]);

  const checkStatus = useCallback(async () => {
    const c = currentCodeRef.current;
    if (!c) return;

    try {
      const res = await fetch(statusUrl(c), {
        headers: JSON_HEADERS,
        signal: abortRef.current?.signal,
      });
      if (!res.ok) return;
      const data = (await res.json()) as {
        success?: boolean;
        status?: string;
      };

      if (data.success && data.status === 'active') {
        cleanup();
        setSuccess(true);
        notify.success('Integrações', 'WhatsApp vinculado com sucesso.');
        onSuccess?.();
        setTimeout(() => {
          setSuccess(false);
          onOpenChange(false);
        }, 1200);
        return;
      }

      if (data.status === 'expired') {
        stopTimers();
        setExpired(true);
      }
    } catch (e) {
      if (e instanceof Error && e.name !== 'AbortError') {
        console.warn('Erro ao verificar status:', e.message);
      }
    }
  }, [cleanup, onOpenChange, onSuccess, stopTimers]);

  const startCountdown = useCallback(
    (minutes = 10) => {
      stopTimers();
      const totalMs = minutes * 60 * 1000;
      expirationMsRef.current = Date.now() + totalMs;
      setShowTimer(true);
      setExpired(false);

      countdownIntervalRef.current = setInterval(() => {
        const end = expirationMsRef.current;
        if (!end) return;
        const remaining = Math.max(0, end - Date.now());
        const mm = String(Math.floor(remaining / 60000)).padStart(2, '0');
        const ss = String(Math.floor((remaining % 60000) / 1000)).padStart(2, '0');
        setCountdown(`${mm}:${ss}`);
        if (remaining === 0) {
          stopTimers();
          setExpired(true);
        }
      }, 1000);
    },
    [stopTimers],
  );

  const generateQRCode = useCallback(
    async (force = false) => {
      if (isGeneratingRef.current && !force) return;
      isGeneratingRef.current = true;

      cleanup();
      abortRef.current = new AbortController();

      setLoading(true);
      setError(null);
      setQrBase64(null);
      setCode(null);
      setWaLink(null);
      setExpired(false);
      setSuccess(false);
      setCopied(false);

      try {
        const res = await fetch(QR_URL, {
          headers: JSON_HEADERS,
          signal: abortRef.current.signal,
        });

        if (!res.ok) {
          throw new Error(`Servidor retornou status: ${res.status}`);
        }

        const qrData = (await res.json()) as {
          success?: boolean;
          base64?: string;
          code?: string;
          link?: string;
          error?: string;
        };

        if (qrData.success && qrData.base64 && qrData.code) {
          setQrBase64(qrData.base64);
          setCode(qrData.code);
          currentCodeRef.current = qrData.code;
          if (qrData.link) setWaLink(qrData.link);
          startCountdown(10);
          statusIntervalRef.current = setInterval(() => {
            void checkStatus();
          }, 3000);
        } else {
          throw new Error(qrData.error ?? 'Não foi possível gerar o QR Code.');
        }
      } catch (e) {
        if (e instanceof Error && e.name === 'AbortError') {
          return;
        }
        const msg = e instanceof Error ? e.message : 'Erro desconhecido';
        setError(msg);
      } finally {
        setLoading(false);
        isGeneratingRef.current = false;
      }
    },
    [checkStatus, cleanup, startCountdown],
  );

  const generateQRCodeRef = useRef(generateQRCode);
  generateQRCodeRef.current = generateQRCode;

  useEffect(() => {
    if (!open) {
      cleanup();
      setLoading(false);
      setError(null);
      setQrBase64(null);
      setCode(null);
      setWaLink(null);
      setExpired(false);
      setSuccess(false);
      setCopied(false);
      setCountdown('10:00');
      return;
    }

    const t = window.setTimeout(() => {
      void generateQRCodeRef.current();
    }, 100);

    return () => {
      window.clearTimeout(t);
    };
  }, [open, cleanup]);

  const copyCode = useCallback(async () => {
    const c = code?.trim();
    if (!c) return;
    try {
      await navigator.clipboard.writeText(c);
      setCopied(true);
      window.setTimeout(() => setCopied(false), 2000);
    } catch {
      notify.error('Integrações', 'Não foi possível copiar o código.');
    }
  }, [code]);

  const displayCode = code ?? (loading ? 'Aguardando geração do código…' : '—');

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="w-full sm:max-w-md flex flex-col gap-0 p-0">
        <SheetHeader className="px-6 pt-6 pb-2 border-b border-border">
          <SheetTitle className="text-start">Recebimento via WhatsApp</SheetTitle>
        </SheetHeader>

        <SheetBody className="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          <p className="text-sm text-muted-foreground">
            Escaneie o QR code ou clique no botão para iniciar a conversa no WhatsApp. Em seguida, envie o código de
            vinculação ou os documentos diretamente por lá.
          </p>

          <div className="flex flex-col items-center gap-4">
            {success ? (
              <div className="w-full rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                Integração realizada com sucesso.
              </div>
            ) : loading && !qrBase64 ? (
              <div className="flex flex-col items-center justify-center py-10 gap-3 text-muted-foreground">
                <Loader2 className="size-8 animate-spin text-primary" />
                <p className="text-sm">Gerando QR Code de vinculação…</p>
              </div>
            ) : error ? (
              <div className="w-full rounded-lg border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive">
                {error}
              </div>
            ) : qrBase64 ? (
              <div className="relative inline-block">
                <div className="rounded-lg border bg-white p-3 shadow-sm">
                  <img src={qrBase64} alt="QR Code WhatsApp" className="size-[200px]" />
                </div>
                <div
                  className="pointer-events-none absolute left-1/2 top-1/2 flex size-[35px] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white p-2 shadow-sm"
                  aria-hidden
                >
                  <img
                    src="/tenancy/assets/media/app/mini-logo.svg"
                    alt=""
                    className="size-[19px] object-contain"
                  />
                </div>
              </div>
            ) : null}

            {waLink && !success ? (
              <Button variant="secondary" size="sm" asChild>
                <a href={waLink} target="_blank" rel="noopener noreferrer">
                  Acessar WhatsApp
                </a>
              </Button>
            ) : null}
          </div>

          <div className="space-y-4">
            <h4 className="text-sm font-semibold text-foreground">Como receber documentos por WhatsApp?</h4>

            <div className="flex gap-3">
              <span className="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
                1
              </span>
              <div className="min-w-0 flex-1 space-y-2">
                <p className="text-sm text-muted-foreground">
                  Acesse o WhatsApp pelo QR code ou link e envie o código
                </p>
                <div className="rounded-md bg-muted/60 p-3 space-y-2">
                  <div className="flex items-center justify-between gap-2">
                    <code className="text-xs break-all text-foreground">{displayCode}</code>
                    {code ? (
                      <Button type="button" variant="ghost" size="icon" className="size-8 shrink-0" onClick={copyCode}>
                        {copied ? <Check className="size-4 text-green-600" /> : <Copy className="size-4" />}
                      </Button>
                    ) : null}
                  </div>
                  {showTimer && !expired ? (
                    <p className="text-xs text-muted-foreground">
                      Código válido por: <strong>{countdown}</strong>
                    </p>
                  ) : null}
                  {expired ? (
                    <div className="rounded-md border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-xs text-amber-900 dark:text-amber-200">
                      Este código expirou. Gere um novo código abaixo.
                      <Button type="button" size="sm" className="mt-2 w-full" onClick={() => void generateQRCode(true)}>
                        Gerar novo código
                      </Button>
                    </div>
                  ) : null}
                </div>
              </div>
            </div>

            <div className="flex gap-3">
              <span className="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
                2
              </span>
              <p className="text-sm text-muted-foreground pt-1">
                Após a validação, seu número será vinculado ao seu usuário.
              </p>
            </div>

            <div className="flex gap-3">
              <span className="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
                3
              </span>
              <p className="text-sm text-muted-foreground pt-1">
                Pronto! Você pode enviar documentos em PDF ou imagem para o Sistema Dominus.
              </p>
            </div>
          </div>
        </SheetBody>

        <SheetFooter className="px-6 py-4 border-t border-border sm:justify-end">
          <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
            Cancelar
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
