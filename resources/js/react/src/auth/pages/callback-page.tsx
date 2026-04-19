import { useEffect } from 'react';
import { useNavigate } from 'react-router';

/**
 * Placeholder para fluxo OAuth futuro (ex.: Socialite).
 * Sem provedor configurado, apenas redireciona ao login React.
 */
export function CallbackPage() {
  const navigate = useNavigate();

  useEffect(() => {
    const t = setTimeout(() => {
      navigate('/auth/signin?error=oauth_unavailable', { replace: true });
    }, 800);
    return () => clearTimeout(t);
  }, [navigate]);

  return (
    <div className="flex flex-col items-center justify-center min-h-screen p-4 text-center">
      <p className="text-muted-foreground text-sm">Processando…</p>
    </div>
  );
}
