import React from 'react';
import { cn } from '@/lib/utils';

const hexClipPath = 'polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%)';

interface HexIconProps {
  icon: React.ElementType;
  className?: string;
  iconClassName?: string;
}

const HexIcon = ({ icon: Icon, className, iconClassName }: HexIconProps) => {
  return (
    <div
      className={cn('flex items-center justify-center size-12 bg-border/60 shrink-0', className)}
      style={{ clipPath: hexClipPath }}
    >
      <div
        className="flex items-center justify-center size-10.5 bg-accent"
        style={{ clipPath: hexClipPath }}
      >
        <Icon className={cn('size-4.5 text-muted-foreground', iconClassName)} />
      </div>
    </div>
  );
};

export { HexIcon, type HexIconProps };
