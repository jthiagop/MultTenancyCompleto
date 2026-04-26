import { useCallback, useEffect, useRef, useState } from 'react';
import { Loader2, Copy, Check, ArrowLeft } from 'lucide-react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { notify } from '@/lib/notify';

const QR_URL = '/integracoes/whatsapp-grupo/qrcode';
const STATUS_PATH = '/integracoes/whatsapp-grupo/status';

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

/**
 * Sheet de cadastro de um número avulso para o "Grupo WhatsApp" da empresa.
 *
 * Reaproveita o mesmo fluxo do {@link WhatsappIntegracaoSheet} (QR via wa.me +
 * UUID + polling), com duas diferenças:
 *  1. Antes de gerar o QR, pede um nome (rótulo do contato — ex.: "Tesoureiro").
 *  2. O backend grava em `whatsapp_auth_requests` com `kind='company_contact'`
 *     e `user_id=NULL`. Vide WhatsAppIntegrationController::getGrupoQRCode.
 */
export function WhatsappGrupoSheet({ open, onOpenChange, onSuccess }: Props) {
  const [step, setStep] = useState<'nome' | 'qr'>('nome');
  const [nome, setNome] = useState('');
  const [submittingNome, setSubmittingNome] = useState(false);

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
      const data = (await res.json()) as { success?: boolean; status?: string };

      if (data.success && data.status === 'active') {
        cleanup();
        setSuccess(true);
        notify.success('Grupo WhatsApp', 'Contato vinculado com sucesso.');
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
        console.warn('Erro ao verificar status do grupo:', e.message);
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
    async (nomeContato: string) => {
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
        const csrfEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
        const res = await fetch(QR_URL, {
          method: 'POST',
          headers: {
            ...JSON_HEADERS,
            'X-CSRF-TOKEN': csrfEl?.content ?? '',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ nome: nomeContato }),
          signal: abortRef.current.signal,
        });

        if (!res.ok) {
          const errBody = (await res.json().catch(() => ({}))) as { error?: string };
          throw new Error(errBody.error ?? `Servidor retornou status: ${res.status}`);
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
        if (e instanceof Error && e.name === 'AbortError') return;
        const msg = e instanceof Error ? e.message : 'Erro desconhecido';
        setError(msg);
      } finally {
        setLoading(false);
      }
    },
    [checkStatus, cleanup, startCountdown],
  );

  const generateQRCodeRef = useRef(generateQRCode);
  generateQRCodeRef.current = generateQRCode;

  // Reseta tudo ao fechar.
  useEffect(() => {
    if (!open) {
      cleanup();
      setStep('nome');
      setNome('');
      setSubmittingNome(false);
      setLoading(false);
      setError(null);
      setQrBase64(null);
      setCode(null);
      setWaLink(null);
      setExpired(false);
      setSuccess(false);
      setCopied(false);
      setCountdown('10:00');
    }
  }, [open, cleanup]);

  const handleSubmitNome = useCallback(async () => {
    const trimmed = nome.trim();
    if (trimmed.length < 2) {
      notify.error('Grupo WhatsApp', 'Informe um nome com pelo menos 2 caracteres.');
      return;
    }
    setSubmittingNome(true);
    setStep('qr');
    await generateQRCodeRef.current(trimmed);
    setSubmittingNome(false);
  }, [nome]);

  const handleVoltar = useCallback(() => {
    cleanup();
    setStep('nome');
    setError(null);
    setQrBase64(null);
    setCode(null);
    setWaLink(null);
    setExpired(false);
    setCountdown('10:00');
  }, [cleanup]);

  const copyCode = useCallback(async () => {
    const c = code?.trim();
    if (!c) return;
    try {
      await navigator.clipboard.writeText(c);
      setCopied(true);
      window.setTimeout(() => setCopied(false), 2000);
    } catch {
      notify.error('Grupo WhatsApp', 'Não foi possível copiar o código.');
    }
  }, [code]);

  const displayCode = code ?? (loading ? 'Aguardando geração do código…' : '—');

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="w-full sm:max-w-md flex flex-col gap-0 p-0">
        <SheetHeader className="px-6 pt-6 pb-2 border-b border-border">
          <SheetTitle className="text-start">
            {step === 'nome' ? 'Adicionar contato ao Grupo WhatsApp' : 'Vincular contato'}
          </SheetTitle>
        </SheetHeader>

        <SheetBody className="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          {step === 'nome' ? (
            <div className="space-y-4">
              <p className="text-sm text-muted-foreground">
                O <strong>Grupo WhatsApp</strong> permite cadastrar números adicionais da empresa que vão receber, em
                paralelo aos usuários, as notificações financeiras (lançamentos vencendo, rateios, etc.).
              </p>

              <div className="space-y-2">
                <label htmlFor="grupo-contato-nome" className="text-sm font-medium text-foreground">
                  Nome do contato
                </label>
                <Input
                  id="grupo-contato-nome"
                  autoFocus
                  type="text"
                  maxLength={120}
                  value={nome}
                  onChange={(e) => setNome(e.target.value)}
                  onKeyDown={(e) => {
                    if (e.key === 'Enter' && !submittingNome) {
                      e.preventDefault();
                      void handleSubmitNome();
                    }
                  }}
                  placeholder="Ex.: Tesoureiro, Pe. João, Diretor…"
                />
                <p className="text-xs text-muted-foreground">
                  Apenas um rótulo para identificar este contato na lista do grupo.
                </p>
              </div>
            </div>
          ) : (
            <>
              <div className="flex items-center gap-2 text-xs text-muted-foreground">
                <Button type="button" size="sm" variant="ghost" className="gap-1.5 -ml-2" onClick={handleVoltar}>
                  <ArrowLeft className="size-3.5" />
                  Voltar
                </Button>
                <span className="truncate">
                  Vinculando <strong className="text-foreground">{nome.trim()}</strong>
                </span>
              </div>

              <p className="text-sm text-muted-foreground">
                Peça à pessoa para escanear este QR code ou clicar no link e enviar a mensagem com o código de
                vinculação pelo WhatsApp dela.
              </p>

              <div className="flex flex-col items-center gap-4">
                {success ? (
                  <div className="w-full rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                    Contato adicionado ao Grupo WhatsApp com sucesso.
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

              <div className="space-y-3">
                <h4 className="text-sm font-semibold text-foreground">Como concluir a vinculação?</h4>

                <div className="rounded-md bg-muted/60 p-3 space-y-2">
                  <div className="flex items-center justify-between gap-2">
                    <code className="text-xs break-all text-foreground">{displayCode}</code>
                    {code ? (
                      <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        className="size-8 shrink-0"
                        onClick={copyCode}
                      >
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
                      Este código expirou. Gere um novo abaixo.
                      <Button
                        type="button"
                        size="sm"
                        className="mt-2 w-full"
                        onClick={() => void generateQRCodeRef.current(nome.trim())}
                      >
                        Gerar novo código
                      </Button>
                    </div>
                  ) : null}
                </div>

                <p className="text-xs text-muted-foreground">
                  Quando o número escanear o QR e enviar a mensagem, ele aparecerá automaticamente na lista de contatos
                  do Grupo WhatsApp.
                </p>
              </div>
            </>
          )}
        </SheetBody>

        <SheetFooter className="px-6 py-4 border-t border-border sm:justify-end gap-2">
          {step === 'nome' ? (
            <>
              <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                Cancelar
              </Button>
              <Button type="button" disabled={submittingNome || nome.trim().length < 2} onClick={() => void handleSubmitNome()}>
                {submittingNome ? <Loader2 className="size-4 animate-spin mr-2" /> : null}
                Continuar
              </Button>
            </>
          ) : (
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Fechar
            </Button>
          )}
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
