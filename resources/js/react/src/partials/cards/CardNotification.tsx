import React, { ReactNode } from 'react';
import { Pencil } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { HexIcon } from './HexIcon';

interface CardNotificationProps {
  icon: React.ElementType;
  title: string;
  description: string;
  button?: boolean;
  actions: ReactNode;
}

const CardNotification = ({
  icon: Icon,
  title,
  description,
  button,
  actions,
}: CardNotificationProps) => {
  return (
    <div className="flex items-center justify-between gap-4 px-5 py-4 border-b last:border-b-0">
      <div className="flex items-center gap-4">
        <HexIcon icon={Icon} />
        <div className="flex flex-col gap-0.5">
          <span className="text-sm font-medium">{title}</span>
          <span className="text-xs text-muted-foreground">{description}</span>
        </div>
      </div>
      <div className="flex items-center gap-2 shrink-0">
        {button && (
          <Button
            variant="ghost"
            size="icon"
            className="size-7 text-muted-foreground hover:text-foreground"
          >
            <Pencil className="size-3.5" strokeWidth={1.5} />
          </Button>
        )}
        {actions}
      </div>
    </div>
  );
};

export { CardNotification, type CardNotificationProps };
