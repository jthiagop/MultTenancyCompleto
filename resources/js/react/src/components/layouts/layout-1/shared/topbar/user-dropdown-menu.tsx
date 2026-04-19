import { ReactNode, useState } from 'react';
import {
  Bell,
  Building2,
  Check,
  IdCard,
  Loader2,
  LogOut,
  Moon,
  Settings,
  Sidebar,
  UserCircle,
} from 'lucide-react';
import { useTheme } from 'next-themes';
import { Link } from 'react-router';
import { useAppData } from '@/hooks/useAppData';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Switch } from '@/components/ui/switch';
import { cn } from '@/lib/utils';
import { Label } from 'react-aria-components';
import { useLayout } from '@/components/layouts/layout-1/components/context';

/** POST em /app/logout (rota dedicada React), aguarda JSON e navega para /auth/signin */
async function submitLaravelLogout(logoutUrl: string, csrfToken: string) {
  try {
    await fetch(logoutUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    });
  } finally {
    window.location.href = '/app/auth/signin';
  }
}

export function UserDropdownMenu({ trigger }: { trigger: ReactNode }) {
  const { theme, setTheme } = useTheme();
  const { sidebarTheme, setSidebarTheme } = useLayout();
  const { user, csrfToken, logoutUrl, companyId, companies } = useAppData();
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
  const initials = user.name
    .split(' ')
    .slice(0, 2)
    .map((n) => n[0])
    .join('')
    .toUpperCase();

  const handleThemeToggle = (checked: boolean) => {
    setTheme(checked ? 'dark' : 'light');
  };

  const handleSidebarThemeToggle = (checked: boolean) => {
    setSidebarTheme(checked ? 'dark' : 'light');
  };

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>{trigger}</DropdownMenuTrigger>
      <DropdownMenuContent className="w-64" side="bottom" align="end">
        {/* Header */}
        <div className="flex items-center justify-between p-3">
          <div className="flex items-center gap-2">
            {user.avatar_url ? (
              <img
                className="size-9 rounded-full border-2 border-green-500 object-cover"
                src={user.avatar_url}
                alt={user.name}
              />
            ) : (
              <span className="size-9 rounded-full border-2 border-green-500 bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold">
                {initials}
              </span>
            )}
            <div className="flex flex-col">
              <Link
                to="#"
                className="text-sm text-mono hover:text-primary font-semibold"
              >
                {user.name}
              </Link>
              <a
                href={`mailto:${user.email}`}
                className="text-xs text-muted-foreground hover:text-primary"
              >
                {user.email}
              </a>
            </div>
          </div>
          <Badge variant="primary" appearance="light" size="sm">
            Pro
          </Badge>
        </div>

        <DropdownMenuSeparator />

        {/* Minhas Empresas — espelha o submenu "Minhas Empresas" do userMenu.blade.php */}
        {companies.length > 0 && (
          <DropdownMenuSub>
            <DropdownMenuSubTrigger className="flex items-center gap-2">
              <Building2 className="size-4" />
              Minhas Empresas
            </DropdownMenuSubTrigger>
            <DropdownMenuSubContent className="w-64 p-1">
              {companies.map((company) => {
                const isActive = company.id === companyId;
                const isLoading = switching === company.id;
                return (
                  <DropdownMenuItem
                    key={company.id}
                    disabled={isActive || switching !== null}
                    className={cn(
                      'flex items-center gap-2.5 px-3 py-2 rounded-md',
                      isActive ? 'bg-accent text-accent-foreground font-medium cursor-default' : 'cursor-pointer',
                    )}
                    onSelect={(e) => {
                      e.preventDefault();
                      handleSwitchCompany(company.id);
                    }}
                  >
                    <span className="shrink-0 size-6 rounded flex items-center justify-center overflow-hidden border border-border bg-accent">
                      {company.avatar_url ? (
                        <img src={company.avatar_url} alt={company.name} className="size-full object-cover" />
                      ) : (
                        <Building2 className="size-3.5 text-muted-foreground" />
                      )}
                    </span>
                    <span className="flex-1 min-w-0">
                      <span className="block truncate text-sm">{company.name}</span>
                      {company.cnpj && (
                        <span className="block truncate text-[11px] text-muted-foreground font-mono">
                          {company.cnpj}
                        </span>
                      )}
                    </span>
                    {isLoading && <Loader2 className="size-3.5 animate-spin text-muted-foreground shrink-0" />}
                    {isActive && !isLoading && <Check className="size-3.5 text-primary shrink-0" />}
                  </DropdownMenuItem>
                );
              })}
            </DropdownMenuSubContent>
          </DropdownMenuSub>
        )}

        {/* Menu Items */}
        <DropdownMenuItem asChild>
          <Link
            to="/fraternidade"
            className="flex items-center gap-2"
          >
            <IdCard />
            Minha Fraternidade
          </Link>
        </DropdownMenuItem>
        <DropdownMenuItem asChild>
          <Link
            to="#"
            className="flex items-center gap-2"
          >
            <UserCircle />
            Meu Perfil
          </Link>
        </DropdownMenuItem>
        <DropdownMenuSub>
          <DropdownMenuSubTrigger className="flex items-center gap-2">
            <Settings className="size-4" />
            Configurações
          </DropdownMenuSubTrigger>
          <DropdownMenuSubContent className="w-56 p-1" alignOffset={-4}>
            <DropdownMenuItem
              className="flex items-center gap-2 cursor-default focus:bg-accent"
              onSelect={(e) => e.preventDefault()}
            >
              <Sidebar className="size-4 shrink-0" />
              <div className="flex min-w-0 flex-1 items-center justify-between gap-2">
                <Label htmlFor="sidebar-theme-toggle" className="cursor-pointer text-sm font-normal">
                  Sidebar escura
                </Label>
                <Switch
                  id="sidebar-theme-toggle"
                  size="sm"
                  checked={sidebarTheme === 'dark'}
                  onCheckedChange={handleSidebarThemeToggle}
                />
              </div>
            </DropdownMenuItem>
                    <DropdownMenuItem asChild>
          <Link to="/notifications" className="flex items-center gap-2">
            <Bell />
            Notificações
          </Link>
        </DropdownMenuItem>
          </DropdownMenuSubContent>
        </DropdownMenuSub>



        <DropdownMenuSeparator />

        {/* Footer */}
        <DropdownMenuItem
          className="flex items-center gap-2"
          onSelect={(event) => event.preventDefault()}
        >
          <Moon />
          <div className="flex items-center gap-2 justify-between grow">
            <Label htmlFor="theme-toggle" className="text-xs cursor-pointer">
              Tema
              <span className="text-xs font-normal">
                {theme === 'dark' ? ' - Escuro' : ' - Claro'}
              </span>
            </Label>
            <Switch
              size="sm"
              checked={theme === 'dark'}
              onCheckedChange={handleThemeToggle}
            />
          </div>
        </DropdownMenuItem>
        <div className="p-2 mt-1">
          <Button
            type="button"
            variant="outline"
            size="sm"
            className="w-full"
            onClick={() => {
              if (csrfToken && logoutUrl) {
                submitLaravelLogout(logoutUrl, csrfToken);
              }
            }}
          >
            <LogOut />
            Sair
          </Button>
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
