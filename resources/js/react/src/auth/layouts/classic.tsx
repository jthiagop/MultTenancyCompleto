import { Link, Outlet } from 'react-router';
import { toAbsoluteUrl } from '@/lib/helpers';
import { Card, CardContent } from '@/components/ui/card';

export function AuthClassicLayout() {
  return (
    <div className="flex flex-col items-center justify-center grow min-h-screen bg-gradient-to-b from-muted/60 to-background">
      <div className="m-5">
        <Link to="/">
          <img
            src={toAbsoluteUrl('/media/app/mini-logo.svg')}
            className="h-[35px] max-w-none dark:hidden"
            alt=""
          />
          <img
            src={toAbsoluteUrl('/media/app/mini-logo-white.svg')}
            className="h-[35px] max-w-none hidden dark:block"
            alt=""
          />
        </Link>
      </div>
      <Card className="w-full max-w-[400px] mx-4">
        <CardContent className="p-6">
          <Outlet />
        </CardContent>
      </Card>
    </div>
  );
}
