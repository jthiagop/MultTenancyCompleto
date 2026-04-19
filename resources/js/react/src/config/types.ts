import { type LucideIcon } from 'lucide-react';

export interface MenuItem {
  title?: string;
  desc?: string;
  img?: string;
  icon?: LucideIcon;
  path?: string;
  rootPath?: string;
  childrenIndex?: number;
  heading?: string;
  children?: MenuConfig;
  disabled?: boolean;
  collapse?: boolean;
  collapseTitle?: string;
  expandTitle?: string;
  badge?: string;
  separator?: boolean;
  /** Oculta o item do menu sem removê-lo do config. */
  hidden?: boolean;
}

export type MenuConfig = MenuItem[];
