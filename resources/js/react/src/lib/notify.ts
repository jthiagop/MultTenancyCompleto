/**
 * Wrapper centralizado para o Sonner toast.
 *
 * Uso:
 *   import { notify } from '@/lib/notify';
 *
 *   notify.success('Salvo!', 'O lançamento foi registrado.');
 *   notify.error('Erro', 'Verifique sua conexão.', () => retry());
 *   notify.warning('Atenção', 'Campos obrigatórios em branco.');
 *   notify.info('Dica', 'Use Ctrl+S para salvar.');
 *   notify.reload();   // erro de sessão com botão "Recarregar"
 */

import { toast } from 'sonner';
import { createElement } from 'react';
import type React from 'react';
import { FileChartColumn } from 'lucide-react';

interface ActionOpts {
  label: string | React.ReactNode;
  onClick: () => void;
}

interface NotifyOpts {
  description?: string;
  action?: ActionOpts;
  duration?: number;
}

function success(title: string, description?: string, opts?: Omit<NotifyOpts, 'description'>) {
  return toast.success(title, { description, ...opts });
}

function error(title: string, description?: string, onRetry?: (() => void) | NotifyOpts) {
  const extra: NotifyOpts =
    typeof onRetry === 'function'
      ? { action: { label: 'Tentar novamente', onClick: onRetry } }
      : (onRetry ?? {});
  return toast.error(title, { description, ...extra });
}

function warning(title: string, description?: string, opts?: Omit<NotifyOpts, 'description'>) {
  return toast.warning(title, { description, ...opts });
}

function info(title: string, description?: string, opts?: Omit<NotifyOpts, 'description'>) {
  return toast.info(title, { description, ...opts });
}

/** IDs de toasts de loading vinculados a geração de PDF/relatório. */
const _pendingPdfToasts = new Set<string | number>();

function loading(title: string, description?: string, opts?: Omit<NotifyOpts, 'description'>) {
  const toastId = toast.loading(title, { description, ...opts });
  return toastId;
}

/**
 * Exibe loading que só desaparece quando `dismissPdfLoading()` é chamado
 * (tipicamente pelo hook de notificações ao receber um `relatorio_gerado`).
 */
function pdfLoading(title: string, description?: string) {
  const toastId = toast.loading(title, { description, duration: Infinity });
  _pendingPdfToasts.add(toastId);
  return toastId;
}

/** Descarta todos os toasts de loading de PDF pendentes. */
function dismissPdfLoading() {
  _pendingPdfToasts.forEach((id) => toast.dismiss(id));
  _pendingPdfToasts.clear();
}

/**
 * Toast para PDF/relatório gerado com sucesso.
 * Ícone de arquivo, título, descrição com metadados e botão "Baixar".
 */
function pdfReady(opts: {
  title: string;
  fileSize?: string | null;
  fileType?: string | null;
  expiresIn?: string | null;
  downloadUrl?: string | null;
}) {
  const parts = [opts.fileType ?? 'PDF', opts.fileSize, opts.expiresIn ? `Expira em ${opts.expiresIn}` : null].filter(Boolean);
  const desc = parts.join(' · ');

  return toast.success(opts.title, {
    icon: createElement(FileChartColumn, { className: 'size-5 text-red-500 shrink-0' }),
    description: desc || undefined,
    duration: 12000,
    ...(opts.downloadUrl
      ? { action: { label: 'Baixar', onClick: () => window.open(opts.downloadUrl!, '_blank') } }
      : {}),
  });
}

/** Atalho para erros de sessão/CSRF com botão de recarregar a página. */
function reload(title = 'Sessão expirada', description = 'Recarregue a página para continuar.') {
  return toast.error(title, {
    description,
    action: { label: 'Recarregar', onClick: () => window.location.reload() },
    duration: Infinity,
  });
}

/** Atalho para erros de rede com botão de tentar novamente. */
function networkError(onRetry?: () => void) {
  return error(
    'Erro de conexão',
    'Não foi possível se comunicar com o servidor.',
    onRetry,
  );
}

/** Exibe uma lista de erros de validação agrupados em um único toast. */
function validationErrors(errors: Record<string, string[]>) {
  const msgs = Object.values(errors).flat();
  return toast.error('Dados inválidos', {
    description: msgs.join(' · '),
  });
}

export const notify = {
  success,
  error,
  warning,
  info,
  loading,
  pdfLoading,
  dismissPdfLoading,
  pdfReady,
  reload,
  networkError,
  validationErrors,
  /** Exibe um toast com JSX completamente customizado. */
  custom: toast.custom,
  /** Fecha um toast pelo ID. */
  dismiss: toast.dismiss,
};
