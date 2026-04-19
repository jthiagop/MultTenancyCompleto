/**
 * Toast customizado para notificação de cobrança de rateio intercompany.
 *
 * Uso:
 *   import { notify } from '@/lib/notify';
 *   import { toastRateio } from '@/components/ui/toast-rateio';
 *
 *   toastRateio({
 *     filialNome: 'Filial Recife',
 *     filialAvatar: null,
 *     valor: 5000,
 *     descricao: 'Rateio - Conta de Energia',
 *     onVerDetalhes: () => navigate('/financeiro?id=123'),
 *   });
 */
import { toast } from 'sonner';
import { GitFork, X } from 'lucide-react';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Item, ItemActions, ItemContent, ItemDescription, ItemMedia, ItemTitle } from '@/components/ui/item';

interface ToastRateioOptions {
  /** Nome da empresa/filial que originou o rateio */
  filialNome: string;
  /** URL do avatar da filial (opcional) */
  filialAvatar?: string | null;
  /** Valor do rateio */
  valor: number;
  /** Descrição do lançamento */
  descricao?: string;
  /** Callback ao clicar em "Ver detalhes" */
  onVerDetalhes?: () => void;
  /** Duração em ms (padrão: 8000) */
  duration?: number;
}

const fmtCurrency = (v: number) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);

function initials(name: string) {
  return name
    .split(' ')
    .slice(0, 2)
    .map((w) => w[0])
    .join('')
    .toUpperCase();
}

export function toastRateio({
  filialNome,
  filialAvatar,
  valor,
  descricao,
  onVerDetalhes,
  duration = 8000,
}: ToastRateioOptions) {
  return toast.custom(
    (id) => (
      <div className="w-80 rounded-xl border border-border bg-background shadow-lg shadow-black/10 p-3">
        {/* Cabeçalho */}
        <div className="flex items-center justify-between mb-2">
          <span className="flex items-center gap-1.5 text-xs font-semibold text-orange-500">
            <GitFork className="size-3.5" />
            Cobrança de Rateio
          </span>
          <button
            onClick={() => toast.dismiss(id)}
            className="text-muted-foreground hover:text-foreground transition-colors"
            aria-label="Fechar"
          >
            <X className="size-3.5" />
          </button>
        </div>

        {/* Item */}
        <Item className="p-0 gap-2.5">
          <ItemMedia>
            <Avatar className="size-9">
              {filialAvatar && <AvatarImage src={filialAvatar} alt={filialNome} />}
              <AvatarFallback className="text-xs bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300">
                {initials(filialNome)}
              </AvatarFallback>
            </Avatar>
          </ItemMedia>
          <ItemContent className="min-w-0">
            <ItemTitle className="text-sm font-semibold truncate">{filialNome}</ItemTitle>
            <ItemDescription className="text-xs truncate">
              {descricao ?? 'Rateio intercompany'}
            </ItemDescription>
          </ItemContent>
          <ItemActions className="shrink-0">
            <span className="text-sm font-bold text-orange-600 dark:text-orange-400 tabular-nums">
              {fmtCurrency(valor)}
            </span>
          </ItemActions>
        </Item>

        {/* Ação */}
        {onVerDetalhes && (
          <div className="mt-2.5 flex justify-end">
            <Button
              size="sm"
              variant="outline"
              className="h-7 text-xs"
              onClick={() => {
                toast.dismiss(id);
                onVerDetalhes();
              }}
            >
              Ver detalhes
            </Button>
          </div>
        )}
      </div>
    ),
    { duration },
  );
}
