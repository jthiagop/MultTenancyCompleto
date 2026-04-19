import {
  Toolbar,
  ToolbarDescription,
  ToolbarHeading,
  ToolbarPageTitle,
} from '@/components/layouts/layout-1/components/toolbar';
import { Channels, DoNotDisturb, OtherNotifications } from './components';

export function NotificationsPage() {
  return (
    <div className="container-fluid">
      <Toolbar>
        <ToolbarHeading>
          <ToolbarPageTitle>Notificações</ToolbarPageTitle>
          <ToolbarDescription>Gerencie seus canais e preferências de notificação</ToolbarDescription>
        </ToolbarHeading>
      </Toolbar>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7">
        <div className="lg:col-span-2 flex flex-col gap-5 lg:gap-7">
          <Channels />
          <OtherNotifications />
        </div>
        <div className="lg:col-span-1">
          <DoNotDisturb />
        </div>
      </div>
    </div>
  );
}
