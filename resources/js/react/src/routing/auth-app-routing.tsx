import { useEffect, useState } from 'react';
import { useLocation } from 'react-router';
import { useLoadingBar } from 'react-top-loading-bar';
import { AuthRoutingSetup } from './auth-routing-setup';

export function AuthAppRouting() {
  const { start, complete } = useLoadingBar({
    color: 'var(--color-primary)',
    shadow: false,
    waitingTime: 400,
    transitionTime: 200,
    height: 2,
  });

  const [firstLoad, setFirstLoad] = useState(true);
  const location = useLocation();

  useEffect(() => {
    if (firstLoad) {
      setFirstLoad(false);
    }
  });

  useEffect(() => {
    if (!firstLoad) {
      start('static');
      const timer = setTimeout(() => complete(), 100);
      return () => clearTimeout(timer);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [location]);

  return <AuthRoutingSetup />;
}
