'use client';

import * as React from 'react';
import { useTheme } from 'next-themes';
import { Toaster as Sonner } from 'sonner';
import { CircleCheck, CircleX, TriangleAlert, Info, LoaderCircle } from 'lucide-react';

type ToasterProps = React.ComponentProps<typeof Sonner>;

const Toaster = ({ ...props }: ToasterProps) => {
  const { theme = 'system' } = useTheme();

  return (
    <Sonner
      theme={theme as ToasterProps['theme']}
      position="bottom-right"
      richColors
      closeButton
      duration={6000}
      offset={{ bottom: 'max(0.75rem, env(safe-area-inset-bottom))', right: '1rem' }}
      className="group toaster"
      // z-index acima do overlay do Sheet/Dialog (z-50 = 50 no Tailwind).
      // pointer-events: all garante interatividade mesmo quando Radix bloqueia o body.
      style={{ zIndex: 99999, pointerEvents: 'all' }}
      icons={{
        success: <CircleCheck className="size-5 text-green-500" />,
        error:   <CircleX className="size-5 text-red-500" />,
        warning: <TriangleAlert className="size-5 text-amber-500" />,
        info:    <Info className="size-5 text-blue-500" />,
        loading: <LoaderCircle className="size-5 text-blue-500 animate-spin" />,
      }}
      toastOptions={{
        classNames: {
          toast: [
            'group toast',
            'group-[.toaster]:bg-background',
            'group-[.toaster]:text-foreground!',
            'group-[.toaster]:border-border',
            'group-[.toaster]:shadow-lg',
            'group-[.toaster]:rounded-xl',
            'has-[[role=alert]]:border-0!',
            'has-[[role=alert]]:shadow-none!',
            'has-[[role=alert]]:bg-transparent!',
          ].join(' '),
          title:       'group-[.toast]:text-sm group-[.toast]:font-semibold',
          description: 'group-[.toast]:text-xs group-[.toast]:text-muted-foreground group-[.toast]:mt-0.5',
          actionButton:
            'group-[.toast]:rounded-md! group-[.toast]:bg-primary group-[.toast]:text-primary-foreground! group-[.toast]:text-xs!',
          cancelButton:
            'group-[.toast]:rounded-md! group-[.toast]:bg-muted group-[.toast]:text-muted-foreground! group-[.toast]:text-xs!',
          closeButton:
            'group-[.toast]:border-border group-[.toast]:bg-background group-[.toast]:text-muted-foreground',
        },
      }}
      {...props}
    />
  );
};

export { Toaster };
