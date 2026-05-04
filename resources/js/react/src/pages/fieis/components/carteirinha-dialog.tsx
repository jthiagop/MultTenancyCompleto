/**
 * carteirinha-dialog.tsx
 *
 * Dialog da carteirinha do dizimista.
 * Geração de PDF 100% client-side com @react-pdf/renderer:
 *  - QR code gerado com `qrcode`
 *  - Barcode Code128 gerado com `bwip-js`
 *  - PDF gerado com `@react-pdf/renderer` (sem Browsershot no servidor)
 *
 * Botões:
 *  - Imprimir          → gera PDF duplex (A6×2) como blob URL → iframe → print()
 *  - PDF lado a lado   → gera PDF A4 landscape → download automático
 *  - PDF frente/verso  → gera PDF A6×2 → download automático
 */

import { useEffect, useMemo, useState } from 'react';
import { pdf } from '@react-pdf/renderer';
import {
  Building2,
  Download,
  Layers,
  Loader2,
  Printer,
  RefreshCw,
  UserRound,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import {
  buildCarteirinhaImages,
  buildCarteirinhaExternalImages,
  CarteirinhaSideBySideDoc,
  CarteirinhaDuplexDoc,
  type CarteirinhaData,
  type CarteirinhaImages,
} from './carteirinha-pdf-document';

// ── Tipos locais ─────────────────────────────────────────────────────────────

type ApiResponse = {
  success?: boolean;
  message?: string;
  data?: CarteirinhaData;
};

type ExternalImages = {
  avatarDataUrl: string;
  logoDataUrl: string;
};

export interface CarteirinhaDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  fielId: number | null;
}

const MESES_PT = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

// ── Helpers de geração/download ──────────────────────────────────────────────

async function triggerDownload(blob: Blob, filename: string) {
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  // Pequeno delay antes de revogar para garantir que o download disparou
  setTimeout(() => URL.revokeObjectURL(url), 5000);
}

async function triggerPrint(blob: Blob) {
  const url = URL.createObjectURL(blob);
  const iframe = document.createElement('iframe');
  iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
  iframe.setAttribute('aria-hidden', 'true');
  document.body.appendChild(iframe);
  iframe.src = url;
  iframe.onload = () => {
    try {
      iframe.contentWindow?.focus();
      iframe.contentWindow?.print();
    } finally {
      // Limpa após alguns segundos (tempo suficiente para o diálogo de impressão abrir)
      setTimeout(() => {
        URL.revokeObjectURL(url);
        iframe.remove();
      }, 30_000);
    }
  };
}

// ── Componente principal ─────────────────────────────────────────────────────

export function CarteirinhaDialog({ open, onOpenChange, fielId }: CarteirinhaDialogProps) {
  const [data, setData] = useState<CarteirinhaData | null>(null);
  const [images, setImages] = useState<CarteirinhaImages | null>(null);
  const [extImages, setExtImages] = useState<ExternalImages | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Estado individual por botão — evita bloquear todos ao mesmo tempo
  const [generatingPrint, setGeneratingPrint] = useState(false);
  const [generatingSide, setGeneratingSide] = useState(false);
  const [generatingDuplex, setGeneratingDuplex] = useState(false);

  const isReady = !loading && !error && data !== null && images !== null && extImages !== null;

  // ── Busca dados da carteirinha ─────────────────────────────────────────────
  useEffect(() => {
    if (!open || !fielId) {
      if (!open) {
        setData(null);
        setImages(null);
        setExtImages(null);
        setError(null);
      }
      return;
    }

    const controller = new AbortController();
    let active = true;

    const load = async () => {
      setLoading(true);
      setError(null);
      setData(null);
      setImages(null);
      setExtImages(null);

      try {
        const res = await fetch(`/api/cadastros/fieis/${fielId}/carteirinha`, {
          method: 'GET',
          signal: controller.signal,
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        });
        const payload = (await res.json()) as ApiResponse;
        if (!active) return;
        if (!res.ok || !payload.success || !payload.data) {
          throw new Error(payload.message ?? 'Não foi possível carregar a carteirinha.');
        }

        const cartData = payload.data;
        setData(cartData);

        // Gera QR (PNG) e barcode (PNG) no browser
        const [imgs, ext] = await Promise.all([
          buildCarteirinhaImages(cartData),
          buildCarteirinhaExternalImages(cartData),
        ]);
        if (!active) return;

        setImages(imgs);
        setExtImages(ext);
      } catch (err) {
        if (controller.signal.aborted) return;
        if (!active) return;
        setError(err instanceof Error ? err.message : 'Erro ao carregar carteirinha.');
      } finally {
        if (active) setLoading(false);
      }
    };

    void load();

    return () => {
      active = false;
      controller.abort();
    };
  }, [fielId, open]);

  // ── Ações de PDF ───────────────────────────────────────────────────────────

  const handlePrint = async () => {
    if (!data || !images || !extImages) return;
    setGeneratingPrint(true);
    try {
      const blob = await pdf(
        <CarteirinhaDuplexDoc
          data={data}
          images={images}
          avatarDataUrl={extImages.avatarDataUrl}
          logoDataUrl={extImages.logoDataUrl}
        />,
      ).toBlob();
      await triggerPrint(blob);
    } catch (err) {
      notify.error(
        'Não foi possível gerar o PDF para impressão',
        err instanceof Error ? err.message : 'Erro inesperado.',
      );
    } finally {
      setGeneratingPrint(false);
    }
  };

  const handleDownloadSideBySide = async () => {
    if (!data || !images || !extImages) return;
    setGeneratingSide(true);
    try {
      const blob = await pdf(
        <CarteirinhaSideBySideDoc
          data={data}
          images={images}
          avatarDataUrl={extImages.avatarDataUrl}
          logoDataUrl={extImages.logoDataUrl}
        />,
      ).toBlob();
      await triggerDownload(blob, `carteirinha-${data.codigo}-lado-a-lado.pdf`);
    } catch (err) {
      notify.error(
        'Não foi possível gerar o PDF',
        err instanceof Error ? err.message : 'Erro inesperado.',
      );
    } finally {
      setGeneratingSide(false);
    }
  };

  const handleDownloadDuplex = async () => {
    if (!data || !images || !extImages) return;
    setGeneratingDuplex(true);
    try {
      const blob = await pdf(
        <CarteirinhaDuplexDoc
          data={data}
          images={images}
          avatarDataUrl={extImages.avatarDataUrl}
          logoDataUrl={extImages.logoDataUrl}
        />,
      ).toBlob();
      await triggerDownload(blob, `carteirinha-${data.codigo}-frente-verso.pdf`);
    } catch (err) {
      notify.error(
        'Não foi possível gerar o PDF',
        err instanceof Error ? err.message : 'Erro inesperado.',
      );
    } finally {
      setGeneratingDuplex(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-2xl gap-4 p-5">
        <DialogHeader className="space-y-0">
          <DialogTitle className="text-base">Carteirinha do dizimista</DialogTitle>
        </DialogHeader>

        {loading && <CarteirinhaSkeleton />}

        {error && !loading && (
          <div className="flex items-center justify-between gap-3 rounded-md border border-destructive/40 bg-destructive/10 px-3 py-2 text-sm text-destructive">
            <span>{error}</span>
            <Button
              type="button"
              size="sm"
              variant="outline"
              onClick={() => {
                setError(null);
                setData(null);
                if (fielId) {
                  onOpenChange(false);
                  setTimeout(() => onOpenChange(true), 0);
                }
              }}
              className="gap-1.5"
            >
              <RefreshCw className="size-3.5" />
              Tentar novamente
            </Button>
          </div>
        )}

        {data && images && !loading && !error && (
          <CarteirinhaPreviewBox data={data} images={images} />
        )}

        <DialogFooter className="flex-wrap gap-2 sm:gap-2">
          <DialogClose asChild>
            <Button type="button" variant="outline">
              Fechar
            </Button>
          </DialogClose>
          <Button
            type="button"
            variant="outline"
            onClick={handleDownloadSideBySide}
            disabled={!isReady || generatingSide}
            className="gap-1.5"
          >
            {generatingSide ? (
              <Loader2 className="size-3.5 animate-spin" />
            ) : (
              <Layers className="size-3.5" />
            )}
            PDF lado a lado
          </Button>
          <Button
            type="button"
            variant="outline"
            onClick={handleDownloadDuplex}
            disabled={!isReady || generatingDuplex}
            className="gap-1.5"
          >
            {generatingDuplex ? (
              <Loader2 className="size-3.5 animate-spin" />
            ) : (
              <Download className="size-3.5" />
            )}
            PDF frente/verso
          </Button>
          <Button
            type="button"
            onClick={handlePrint}
            disabled={!isReady || generatingPrint}
            className="gap-1.5"
          >
            {generatingPrint ? (
              <Loader2 className="size-3.5 animate-spin" />
            ) : (
              <Printer className="size-3.5" />
            )}
            Imprimir
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

// ── Skeleton ──────────────────────────────────────────────────────────────────

function CarteirinhaSkeleton() {
  return (
    <div className="rounded-lg border border-border bg-background p-4 space-y-3">
      <div className="flex gap-4">
        <Skeleton className="size-[100px] shrink-0 rounded-md" />
        <div className="flex-1 space-y-3">
          <Skeleton className="h-5 w-3/4" />
          <Skeleton className="h-4 w-1/2" />
          <Skeleton className="h-16 w-full" />
        </div>
        <Skeleton className="size-[100px] shrink-0 rounded-md" />
      </div>
      <Skeleton className="h-3 w-2/3 mx-auto" />
    </div>
  );
}

// ── Preview HTML (somente visual, não é imprimível) ───────────────────────────

function CarteirinhaPreviewBox({
  data,
  images,
}: {
  data: CarteirinhaData;
  images: CarteirinhaImages;
}) {
  const ano = useMemo(() => new Date().getFullYear(), []);

  return (
    <div className="mx-auto w-full max-w-[640px] space-y-4">
      <CarteirinhaFrente data={data} images={images} />
      <CarteirinhaVerso ano={ano} />
    </div>
  );
}

function CarteirinhaFrente({
  data,
  images,
}: {
  data: CarteirinhaData;
  images: CarteirinhaImages;
}) {
  const organismo = data.company?.nome ?? 'Igreja';

  return (
    <div className="overflow-hidden rounded-lg border border-zinc-300 bg-white text-zinc-900">
      {/* Header */}
      <div className="flex items-center justify-between gap-2 border-b border-zinc-200 bg-zinc-900 px-3 py-1.5 text-zinc-50">
        <div className="flex flex-col leading-tight">
          <span className="text-[9px] uppercase tracking-wider opacity-70">
            Carteirinha do Dizimista
          </span>
          <span className="text-xs font-semibold">{organismo}</span>
        </div>
        <span className="font-mono text-sm font-bold tabular-nums">{data.codigo}</span>
      </div>

      {/* Body: foto | conteúdo | logo */}
      <div className="grid grid-cols-[100px_1fr_100px] gap-3 p-3">
        {/* Foto */}
        <FotoBox
          src={data.fiel.avatar_url}
          alt={data.fiel.nome_completo}
          fallback={<UserRound className="size-10 text-zinc-300" />}
          label="Foto"
        />

        {/* Centro */}
        <div className="flex min-w-0 flex-col gap-2">
          <div className="min-w-0">
            <p className="text-[9px] uppercase tracking-wider text-zinc-500">Dizimista</p>
            <p className="truncate text-sm font-semibold leading-tight">
              {data.fiel.nome_completo}
            </p>
          </div>

          <div className="flex items-end gap-2">
            {/* QR — imagem PNG gerada pelo cliente */}
            <img
              src={images.qrDataUrl}
              alt="QR Code da carteirinha"
              className="size-[80px] shrink-0 rounded border border-zinc-200 bg-white p-0.5 object-contain"
            />
            <div className="flex min-w-0 flex-1 flex-col gap-0.5">
              {/* Barcode — imagem PNG gerada pelo cliente */}
              <img
                src={images.barcodeDataUrl}
                alt="Código de barras Code128"
                className="w-full rounded border border-zinc-200 bg-white p-0.5 object-contain"
                style={{ height: 40 }}
              />
              <div className="flex items-center justify-between gap-2 text-[9px] text-zinc-500">
                <span>Code128</span>
                <span className="font-mono font-semibold tabular-nums text-zinc-700">
                  {data.codigo}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Logo */}
        <FotoBox
          src={data.company?.logo_url ?? null}
          alt={organismo}
          fallback={<Building2 className="size-10 text-zinc-300" />}
          label="Logo"
          contain
        />
      </div>

      {/* Footer */}
      <div className="border-t border-zinc-200 bg-zinc-50 px-3 py-1 text-center text-[9px] text-zinc-500">
        Apresente esta carteirinha ao realizar a contribuição do dízimo.
      </div>
    </div>
  );
}

function CarteirinhaVerso({ ano }: { ano: number }) {
  const colunas: [number, number][] = [
    [0, 5],
    [6, 11],
  ];

  return (
    <div className="overflow-hidden rounded-lg border border-zinc-300 bg-white text-zinc-900">
      <div className="flex items-center justify-between gap-2 border-b border-zinc-200 bg-zinc-900 px-3 py-1.5 text-zinc-50">
        <span className="text-xs font-semibold uppercase tracking-wider">
          Controle de Dízimos
        </span>
        <span className="font-mono text-sm font-bold tabular-nums">{ano}</span>
      </div>

      <div className="grid grid-cols-2 gap-3 p-3">
        {colunas.map(([from, to], colIdx) => (
          <table key={colIdx} className="w-full border-collapse text-[10px]">
            <thead>
              <tr className="bg-zinc-100 text-zinc-600">
                <th className="border border-zinc-300 px-1.5 py-1 text-start font-semibold">Mês</th>
                <th className="border border-zinc-300 px-1.5 py-1 font-semibold w-6">✓</th>
                <th className="border border-zinc-300 px-1.5 py-1 text-start font-semibold">Data</th>
                <th className="border border-zinc-300 px-1.5 py-1 text-end font-semibold">Valor</th>
              </tr>
            </thead>
            <tbody>
              {MESES_PT.slice(from, to + 1).map((mes) => (
                <tr key={mes}>
                  <td className="border border-zinc-300 px-1.5 py-1 font-medium">{mes}</td>
                  <td className="border border-zinc-300 px-1.5 py-1 text-center">
                    <span className="inline-block size-3 border border-zinc-400" />
                  </td>
                  <td className="border border-zinc-300 px-1.5 py-1">
                    <span className="block h-3 w-full" />
                  </td>
                  <td className="border border-zinc-300 px-1.5 py-1 text-end">
                    <span className="block h-3 w-full" />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        ))}
      </div>

      <div className="border-t border-zinc-200 bg-zinc-50 px-3 py-1 text-center text-[9px] text-zinc-500">
        Marque o mês contribuído, anote a data do pagamento e o valor.
      </div>
    </div>
  );
}

function FotoBox({
  src,
  alt,
  fallback,
  label,
  contain = false,
}: {
  src: string | null;
  alt: string;
  fallback: React.ReactNode;
  label: string;
  contain?: boolean;
}) {
  return (
    <div className="flex size-[100px] shrink-0 flex-col items-center justify-center overflow-hidden rounded-md border border-zinc-300 bg-zinc-50">
      {src ? (
        <img
          src={src}
          alt={alt}
          loading="eager"
          decoding="sync"
          className={cn('size-full', contain ? 'object-contain p-1' : 'object-cover')}
        />
      ) : (
        <div className="flex flex-1 flex-col items-center justify-center gap-1 text-zinc-400">
          {fallback}
          <span className="text-[9px] uppercase tracking-wider">{label}</span>
        </div>
      )}
    </div>
  );
}
