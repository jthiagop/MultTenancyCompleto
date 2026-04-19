import { Fragment } from 'react';
import { Link } from 'react-router';
import { toAbsoluteUrl } from '@/lib/helpers';
import { Card, CardContent } from '@/components/ui/card';
import { useAppData } from '@/hooks/useAppData';

export function ModulesGrid() {
  const { modules } = useAppData();

  if (!modules || modules.length === 0) {
    return null;
  }

  return (
    <Fragment>
      <style>
        {`
          .module-card-bg {
            background-image: url('${toAbsoluteUrl('/media/images/2600x1600/bg-3.png')}');
          }
          .dark .module-card-bg {
            background-image: url('${toAbsoluteUrl('/media/images/2600x1600/bg-3-dark.png')}');
          }
        `}
      </style>

      {modules.map((module) => (
        <Link key={module.key} to={`/${module.key}`} className="no-underline">
          <Card className="h-full hover:shadow-md transition-shadow">
            <CardContent className="p-0 flex flex-col justify-between gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat module-card-bg">
              <div className="mt-4 ms-5">
                {module.icon ? (
                  <img src={module.icon} className="size-12 object-contain" alt={module.name} />
                ) : module.icon_class ? (
                  <i className={`${module.icon_class} text-4xl leading-none`} />
                ) : (
                  <span className="size-12 block" />
                )}
              </div>
              <div className="flex flex-col gap-1 pb-4 px-5">
                <span className="text-lg font-semibold text-mono leading-tight">
                  {module.name}
                </span>
                {module.description && (
                  <span className="text-sm font-normal text-muted-foreground line-clamp-2">
                    {module.description}
                  </span>
                )}
              </div>
            </CardContent>
          </Card>
        </Link>
      ))}
    </Fragment>
  );
}
