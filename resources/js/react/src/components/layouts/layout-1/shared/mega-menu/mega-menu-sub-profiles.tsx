import type React from 'react';
import { Link } from 'react-router-dom';
import { IdCard, KeyRound, LayoutGrid, Users } from 'lucide-react';
import type { MenuConfig } from '@/config/types';
import { useAppData } from '@/hooks/useAppData';
import { MegaMenuFooter } from './components';

const MegaMenuSubProfiles = ({ items: _items }: { items: MenuConfig }) => {
  const { canUsersIndex, canCompanyIndex } = useAppData();

  const cadastrosItems = [
    canUsersIndex   && { title: 'Usuários',   path: '/cadastros/usuarios',   icon: Users },
    canCompanyIndex && { title: 'Módulos',    path: '/cadastros/modulos',    icon: LayoutGrid },
    canCompanyIndex && { title: 'Permissões', path: '/cadastros/permissoes', icon: KeyRound },
  ].filter(Boolean) as Array<{ title: string; path: string; icon: React.ElementType }>;

  if (cadastrosItems.length === 0) return null;

  return (
    <div className="w-full gap-0 lg:w-[320px]">
      <div className="pt-4 pb-2 lg:p-6">
        <div className="flex items-center gap-2 mb-4 px-1">
          <IdCard className="size-4 text-muted-foreground" />
          <h3 className="text-sm font-semibold text-foreground">Cadastros</h3>
        </div>
        <div className="space-y-0.5">
          {cadastrosItems.map((item) => (
            <Link
              key={item.path}
              to={item.path}
              className="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm text-secondary-foreground hover:bg-accent hover:text-accent-foreground transition-colors"
            >
              <item.icon className="size-4 shrink-0 text-muted-foreground" />
              {item.title}
            </Link>
          ))}
        </div>
      </div>
      <MegaMenuFooter />
    </div>
  );
};

export { MegaMenuSubProfiles };

