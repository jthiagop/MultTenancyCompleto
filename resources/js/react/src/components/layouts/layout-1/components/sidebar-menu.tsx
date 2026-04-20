'use client';

import { JSX, useCallback, useMemo } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { MENU_SIDEBAR } from '@/config/layout-1.config';
import { MenuConfig, MenuItem } from '@/config/types';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import {
  AccordionMenu,
  AccordionMenuClassNames,
  AccordionMenuGroup,
  AccordionMenuItem,
  AccordionMenuLabel,
  AccordionMenuSub,
  AccordionMenuSubContent,
  AccordionMenuSubTrigger,
} from '@/components/ui/accordion-menu';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';

export function SidebarMenu() {
  const { pathname, search } = useLocation();
  const { modules, canUsersIndex, canCompanyIndex, canFinanceiroIndex, canNotafiscalIndex } = useAppData();

  // Substitui os children do primeiro item (Dashboards) pelos módulos do usuário
  // e filtra o item "Cadastros" de acordo com as permissões
  const menuWithModules = useMemo<MenuConfig>(() => {
    let base: MenuConfig = MENU_SIDEBAR as MenuConfig;

    // 1. Substituir filhos de Dashboards pelos módulos do usuário
    if (modules && modules.length > 0) {
      const moduleItems: MenuItem[] = modules.map((m) => ({
        title: m.name,
        path: `/${m.key}`,
      }));
      base = base.map((item, i) =>
        i === 0 ? { ...item, children: moduleItems } : item,
      );
    }

    // 2. Filtrar filhos do item "Financeiro" por permissão
    return base
      .map((item: MenuItem): MenuItem | null => {
        // Esconde o item "Financeiro" inteiro se não tem permissão
        if (item.title === 'Financeiro') {
          if (!canFinanceiroIndex) return null;
          const filteredChildren = (item.children ?? []).filter((child) => {
            if (child.title === 'Nota Fiscal') return !!canNotafiscalIndex;
            return true;
          });
          return { ...item, children: filteredChildren };
        }
        // 3. Filtrar filhos do item "Cadastros" por permissão
        if (item.title === 'Cadastros' && item.children) {
          const filteredChildren = item.children.filter((child) => {
            if (child.path === '/cadastros/organismos') return !!canCompanyIndex;
            if (child.path === '/cadastros/usuarios')   return !!canUsersIndex;
            if (child.path === '/cadastros/modulos')    return !!canCompanyIndex;
            if (child.path === '/cadastros/permissoes') return !!canCompanyIndex;
            return true;
          });
          // Se não há filhos visíveis, remove o item inteiro
          if (filteredChildren.length === 0) return null;
          return { ...item, children: filteredChildren };
        }
        // Itens marcados como hidden são omitidos
        if (item.hidden) return null;
        // Heading "Menu" — removido se o usuário não tem acesso ao Financeiro
        if (item.heading === 'Menu' && !canFinanceiroIndex) return null;
        // Heading "Cadastros" — removido se o usuário não tem nenhuma das permissões
        if (item.heading === 'Cadastros' && !canUsersIndex && !canCompanyIndex) return null;
        return item;
      })
      .filter((item): item is MenuItem => item !== null);
  }, [modules, canUsersIndex, canCompanyIndex, canFinanceiroIndex, canNotafiscalIndex]);

  // Memoize matchPath to prevent unnecessary re-renders
  const matchPath = useCallback(
    (path: string): boolean => {
      const [itemPathname, itemQuery] = path.split('?');

      // Se o item tem query string, o match exige pathname + query idênticos
      if (itemQuery) {
        const itemParams = new URLSearchParams(itemQuery);
        const currentParams = new URLSearchParams(search);
        return (
          itemPathname === pathname &&
          itemParams.get('tab') === currentParams.get('tab')
        );
      }

      // Sem query string: comparação normal por pathname
      return (
        itemPathname === pathname ||
        (itemPathname.length > 1 && pathname.startsWith(itemPathname) && itemPathname !== '/layout-1')
      );
    },
    [pathname, search],
  );

  // Global classNames for consistent styling
  const classNames: AccordionMenuClassNames = {
    root: 'lg:ps-1 space-y-3',
    group: 'gap-px',
    label:
      'uppercase text-xs font-medium text-muted-foreground/70 pt-2.25 pb-px',
    separator: '',
    item: 'h-8 hover:bg-transparent text-accent-foreground hover:text-primary data-[selected=true]:text-primary data-[selected=true]:bg-muted data-[selected=true]:font-medium',
    sub: '',
    subTrigger:
      'h-8 hover:bg-transparent text-accent-foreground hover:text-primary data-[selected=true]:text-primary data-[selected=true]:bg-muted data-[selected=true]:font-medium',
    subContent: 'py-0',
    indicator: '',
  };

  const buildMenu = (items: MenuConfig): JSX.Element[] => {
    return items.map((item: MenuItem, index: number) => {
      if (item.heading) {
        return buildMenuHeading(item, index);
      } else if (item.disabled) {
        return buildMenuItemRootDisabled(item, index);
      } else {
        return buildMenuItemRoot(item, index);
      }
    });
  };

  const buildMenuItemRoot = (item: MenuItem, index: number): JSX.Element => {
    if (item.children) {
      return (
        <AccordionMenuSub key={index} value={item.path || `root-${index}`}>
          <AccordionMenuSubTrigger className="text-sm font-medium">
            {item.icon && <item.icon data-slot="accordion-menu-icon" />}
            <span data-slot="accordion-menu-title">{item.title}</span>
          </AccordionMenuSubTrigger>
          <AccordionMenuSubContent
            type="single"
            collapsible
            parentValue={item.path || `root-${index}`}
            className="ps-6 relative before:absolute before:start-4 before:top-0 before:bottom-0 before:border-s before:border-border"
          >
            <AccordionMenuGroup>
              {buildMenuItemChildren(item.children, 1)}
            </AccordionMenuGroup>
          </AccordionMenuSubContent>
        </AccordionMenuSub>
      );
    } else {
      return (
        <AccordionMenuItem
          key={index}
          value={item.path || ''}
          className="text-sm font-medium"
        >
          <Link
            to={item.path || '#'}
            className="flex items-center justify-between grow gap-2"
          >
            {item.icon && <item.icon data-slot="accordion-menu-icon" />}
            <span data-slot="accordion-menu-title">{item.title}</span>
          </Link>
        </AccordionMenuItem>
      );
    }
  };

  const buildMenuItemRootDisabled = (
    item: MenuItem,
    index: number,
  ): JSX.Element => {
    return (
      <AccordionMenuItem
        key={index}
        value={`disabled-${index}`}
        className="text-sm font-medium"
      >
        {item.icon && <item.icon data-slot="accordion-menu-icon" />}
        <span data-slot="accordion-menu-title">{item.title}</span>
        {item.disabled && (
          <Badge variant="secondary" size="sm" className="ms-auto me-[-10px]">
            Soon
          </Badge>
        )}
      </AccordionMenuItem>
    );
  };

  const buildMenuItemChildren = (
    items: MenuConfig,
    level: number = 0,
  ): JSX.Element[] => {
    return items.map((item: MenuItem, index: number) => {
      if (item.disabled) {
        return buildMenuItemChildDisabled(item, index, level);
      } else {
        return buildMenuItemChild(item, index, level);
      }
    });
  };

  const buildMenuItemChild = (
    item: MenuItem,
    index: number,
    level: number = 0,
  ): JSX.Element => {
    if (item.children) {
      return (
        <AccordionMenuSub
          key={index}
          value={item.path || `child-${level}-${index}`}
        >
          <AccordionMenuSubTrigger className="text-[13px]">
            {item.collapse ? (
              <span className="text-muted-foreground">
                <span className="hidden [[data-state=open]>span>&]:inline">
                  {item.collapseTitle}
                </span>
                <span className="inline [[data-state=open]>span>&]:hidden">
                  {item.expandTitle}
                </span>
              </span>
            ) : (
              item.title
            )}
          </AccordionMenuSubTrigger>
          <AccordionMenuSubContent
            type="single"
            collapsible
            parentValue={item.path || `child-${level}-${index}`}
            className={cn(
              'ps-4',
              !item.collapse && 'relative',
              !item.collapse && (level > 0 ? '' : ''),
            )}
          >
            <AccordionMenuGroup>
              {buildMenuItemChildren(
                item.children,
                item.collapse ? level : level + 1,
              )}
            </AccordionMenuGroup>
          </AccordionMenuSubContent>
        </AccordionMenuSub>
      );
    } else {
      return (
        <AccordionMenuItem
          key={index}
          value={item.path || ''}
          className="text-[13px]"
        >
          <Link to={item.path || '#'}>{item.title}</Link>
        </AccordionMenuItem>
      );
    }
  };

  const buildMenuItemChildDisabled = (
    item: MenuItem,
    index: number,
    level: number = 0,
  ): JSX.Element => {
    return (
      <AccordionMenuItem
        key={index}
        value={`disabled-child-${level}-${index}`}
        className="text-[13px]"
      >
        <span data-slot="accordion-menu-title">{item.title}</span>
        {item.disabled && (
          <Badge variant="secondary" size="sm" className="ms-auto me-[-10px]">
            Soon
          </Badge>
        )}
      </AccordionMenuItem>
    );
  };

  const buildMenuHeading = (item: MenuItem, index: number): JSX.Element => {
    return <AccordionMenuLabel key={index}>{item.heading}</AccordionMenuLabel>;
  };

  return (
    <ScrollArea className="flex grow shrink-0 py-5 px-5 lg:h-[calc(100vh-5.5rem)]">
      <AccordionMenu
        selectedValue={pathname}
        matchPath={matchPath}
        type="single"
        collapsible
        classNames={classNames}
      >
        {buildMenu(menuWithModules)}
      </AccordionMenu>
    </ScrollArea>
  );
}
