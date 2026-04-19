import { ReactNode, useState } from 'react';
import { Building2, Loader2 } from 'lucide-react';
import { useAppData } from '@/hooks/useAppData';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';

/**
 * Seletor de "Minhas Empresas" no topbar React.
 * Espelha o submenu do `userMenu.blade.php` (linhas 37-52).
 *
 * Fluxo (sem redirect chain que quebraria a sessão):
 *  1. Clique → fetch POST /session/switch-company-react com X-CSRF-TOKEN
 *  2. SessionController::switchCompanyReact() → session(['active_company_id' => id]) + save()
 *  3. Resposta JSON {ok: true} → window.location.reload()
 *  4. ReactAppController recarrega __APP_DATA__ com a nova empresa ativa
 */
export function AppsDropdownMenu({ trigger }: { trigger: ReactNode }) {
  const { companies, companyId, csrfToken } = useAppData();
  const [switching, setSwitching] = useState<number | null>(null);

  async function handleSwitchCompany(id: number) {
    if (id === companyId || switching !== null) return;
    setSwitching(id);
    try {
      const res = await fetch('/app/session/switch-company', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ company_id: id }),
      });
      if (res.ok) {
        window.location.reload();
      }
    } catch {
      setSwitching(null);
    }
  }

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>{trigger}</DropdownMenuTrigger>
      <DropdownMenuContent className="w-[280px] p-0" side="bottom" align="end">
        {/* Header */}
        <div className="flex items-center justify-between gap-2.5 text-xs text-secondary-foreground font-medium px-5 py-3 border-b border-border">
          <span>Minhas Empresas</span>
          <span className="text-[11px] text-muted-foreground">{companies.length} empresa{companies.length !== 1 ? 's' : ''}</span>
        </div>

        {/* Lista de empresas */}
        <div className="flex flex-col divide-y divide-border overflow-y-auto max-h-[360px]">
          {companies.length === 0 && (
            <div className="px-5 py-6 text-center text-xs text-muted-foreground">
              Nenhuma empresa associada.
            </div>
          )}
          {companies.map((company) => {
            const isActive  = company.id === companyId;
            const isLoading = switching === company.id;
            return (
              <button
                key={company.id}
                type="button"
                disabled={isActive || switching !== null}
                onClick={() => handleSwitchCompany(company.id)}
                className={cn(
                  'flex items-center gap-3 w-full px-4 py-3 text-left transition-colors',
                  isActive
                    ? 'cursor-default'
                    : switching !== null
                      ? 'opacity-50 cursor-not-allowed'
                      : 'hover:bg-accent/40 cursor-pointer',
                )}
              >
                {/* Avatar com borda verde pulsante quando ativa */}
                <span className={cn(
                  'relative shrink-0 size-10 rounded-lg flex items-center justify-center overflow-hidden bg-background',
                  isActive
                    ? 'ring-2 ring-green-500 ring-offset-1 ring-offset-background'
                    : 'border border-border',
                )}>
                  {isActive && (
                    <span className="absolute inset-0 rounded-lg ring-2 ring-green-500 animate-ping opacity-40" />
                  )}
                  {company.avatar_url ? (
                    <img src={company.avatar_url} alt={company.name} className="size-full object-cover" />
                  ) : (
                    <Building2 className="size-4 text-muted-foreground" />
                  )}
                </span>

                {/* Nome + Razão Social + CNPJ */}
                <div className="flex flex-col flex-1 min-w-0 gap-0.5">
                  <span className={cn(
                    'text-sm font-semibold truncate leading-tight',
                    isActive ? 'text-foreground' : 'text-secondary-foreground',
                  )}>
                    {company.name}
                  </span>
                  {company.razao_social && (
                    <span className="text-[11px] text-muted-foreground truncate leading-tight">
                      {company.razao_social}
                    </span>
                  )}
                  {company.cnpj && (
                    <span className="text-[11px] font-mono text-muted-foreground/80 leading-tight">
                      {company.cnpj}
                    </span>
                  )}
                </div>

                {/* Indicadores à direita */}
                <div className="shrink-0 flex flex-col items-end gap-1">
                  {isLoading && <Loader2 className="size-4 animate-spin text-muted-foreground" />}
                  {isActive && !isLoading && (
                    <span className="flex items-center gap-1 text-[10px] font-semibold text-green-600 dark:text-green-500">
                      <span className="size-1.5 rounded-full bg-green-500 animate-pulse" />
                      Ativa
                    </span>
                  )}
                </div>
              </button>
            );
          })}
        </div>

        {/* Footer */}
        {companies.length > 1 && (
          <div className="p-4 border-t border-border">
            <p className="text-[11px] text-muted-foreground text-center">
              Clique em uma empresa para alternar
            </p>
          </div>
        )}
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
