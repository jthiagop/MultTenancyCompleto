import { useEffect, useState } from 'react';
import {
  Bell,
  LayoutGrid,
  Menu,
  MessageCircleMore,
  Search,
  SquareChevronRight,
} from 'lucide-react';
import { useLocation } from 'react-router';
import { Link } from 'react-router-dom';
import { toAbsoluteUrl } from '@/lib/helpers';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { useIsMobile } from '@/hooks/use-mobile';
import { useScrollPosition } from '@/hooks/use-scroll-position';
import { Button } from '@/components/ui/button';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetHeader,
  SheetTrigger,
} from '@/components/ui/sheet';
import { SearchDialog } from '@/components/layouts/layout-1/shared/dialogs/search/search-dialog';
import { AppsDropdownMenu } from '@/components/layouts/layout-1/shared/topbar/apps-dropdown-menu';
import { ChatSheet } from '@/components/layouts/layout-1/shared/topbar/chat-sheet';
import { NotificationsSheet } from '@/components/layouts/layout-1/shared/topbar/notifications-sheet';
import { UserDropdownMenu } from '@/components/layouts/layout-1/shared/topbar/user-dropdown-menu';
import { MegaMenu } from './mega-menu';
import { MegaMenuMobile } from './mega-menu-mobile';
import { SidebarMenu } from './sidebar-menu';

export function Header() {
  const [isSidebarSheetOpen, setIsSidebarSheetOpen] = useState(false);
  const [isMegaMenuSheetOpen, setIsMegaMenuSheetOpen] = useState(false);

  const { pathname } = useLocation();
  const mobileMode = useIsMobile();

  const scrollPosition = useScrollPosition();
  const headerSticky: boolean = scrollPosition > 0;
  const { user, companyId, companies } = useAppData();
  const activeCompany = companies.find((c) => c.id === companyId);
  const initials = user.name
    .split(' ')
    .slice(0, 2)
    .map((n) => n[0])
    .join('')
    .toUpperCase();
  const companyInitials = activeCompany?.name
    ?.split(' ')
    .slice(0, 2)
    .map((n) => n[0])
    .join('')
    .toUpperCase() ?? '';

  // Close sheet when route changes
  useEffect(() => {
    setIsSidebarSheetOpen(false);
    setIsMegaMenuSheetOpen(false);
  }, [pathname]);

  return (
    <header
      className={cn(
        'header fixed top-0 z-10 start-0 flex items-stretch shrink-0 border-b border-transparent bg-background end-0 pe-[var(--removed-body-scroll-bar-size,0px)]',
        headerSticky && 'border-b border-border',
      )}
    >
      <div className="container-fluid flex justify-between items-stretch lg:gap-4">
        {/* HeaderLogo */}
        <div className="flex lg:hidden items-center gap-2.5">
          <Link to="/" className="shrink-0">
            <img
              src={toAbsoluteUrl('/media/app/mini-logo.svg')}
              className="h-[25px] w-full"
              alt="mini-logo"
            />
          </Link>
          <div className="flex items-center">
            {mobileMode && (
              <Sheet
                open={isSidebarSheetOpen}
                onOpenChange={setIsSidebarSheetOpen}
              >
                <SheetTrigger asChild>
                  <Button variant="ghost" mode="icon">
                    <Menu className="text-muted-foreground/70" />
                  </Button>
                </SheetTrigger>
                <SheetContent
                  className="p-0 gap-0 w-[275px]"
                  side="left"
                  close={false}
                  aria-describedby={undefined}
                >
                  <SheetHeader className="p-0 space-y-0" />
                  <SheetBody className="p-0 overflow-y-auto">
                    <SidebarMenu />
                  </SheetBody>
                </SheetContent>
              </Sheet>
            )}
            {mobileMode && (
              <Sheet
                open={isMegaMenuSheetOpen}
                onOpenChange={setIsMegaMenuSheetOpen}
              >
                <SheetTrigger asChild>
                  <Button variant="ghost" mode="icon">
                    <SquareChevronRight className="text-muted-foreground/70" />
                  </Button>
                </SheetTrigger>
                <SheetContent
                  className="p-0 gap-0 w-[275px]"
                  side="left"
                  close={false}
                  aria-describedby={undefined}
                >
                  <SheetHeader className="p-0 space-y-0" />
                  <SheetBody className="p-0 overflow-y-auto">
                    <MegaMenuMobile />
                  </SheetBody>
                </SheetContent>
              </Sheet>
            )}
          </div>
        </div>

        {/* Mega Menu */}
        {!mobileMode && (
          <MegaMenu />
        )}

        {/* HeaderTopbar */}
        <div className="flex items-center gap-3">
          {!mobileMode && (
            <SearchDialog
              trigger={
                <Button
                  variant="ghost"
                  mode="icon"
                  shape="circle"
                  className="size-9 hover:bg-primary/10 hover:[&_svg]:text-primary"
                >
                  <Search className="size-4.5!" />
                </Button>
              }
            />
          )}
          <NotificationsSheet
            trigger={
              <Button
                variant="ghost"
                mode="icon"
                shape="circle"
                className="size-9 hover:bg-primary/10 hover:[&_svg]:text-primary"
              >
                <Bell className="size-4.5!" />
              </Button>
            }
          />
          <ChatSheet
            trigger={
              <Button
                variant="ghost"
                mode="icon"
                shape="circle"
                className="size-9 hover:bg-primary/10 hover:[&_svg]:text-primary"
              >
                <MessageCircleMore className="size-4.5!" />
              </Button>
            }
          />
          <AppsDropdownMenu
            trigger={
              <Button
                variant="ghost"
                mode="icon"
                shape="circle"
                className="size-9 hover:bg-primary/10 hover:[&_svg]:text-primary"
              >
                <LayoutGrid className="size-4.5!" />
              </Button>
            }
          />
          <UserDropdownMenu
            trigger={
              <div className="flex items-center -space-x-2 cursor-pointer">
                {activeCompany && (
                  <span className="relative z-0 flex shrink-0 size-8 rounded-full border-2 border-red-900 overflow-hidden bg-muted">
                    {activeCompany.avatar_url ? (
                      <img src={activeCompany.avatar_url} alt={activeCompany.name} className="size-full rounded-full object-cover" />
                    ) : (
                      <span className="flex size-full items-center justify-center rounded-full bg-muted text-[10px] font-semibold text-muted-foreground">
                        {companyInitials}
                      </span>
                    )}
                  </span>
                )}
                <span className="relative z-10 flex shrink-0 size-9 rounded-full border-2 border-green-500 overflow-hidden bg-background">
                  {user.avatar_url ? (
                    <img src={user.avatar_url} alt={user.name} className="size-full rounded-full object-cover" />
                  ) : (
                    <span className="flex size-full items-center justify-center rounded-full bg-primary/10 text-primary text-sm font-semibold">
                      {initials}
                    </span>
                  )}
                </span>
              </div>
            }
          />
        </div>
      </div>
    </header>
  );
}
