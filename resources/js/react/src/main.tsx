import './styles/globals.css';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { App } from './App';
import { installFinanceiroFetchInterceptor } from './lib/financeiro-fetch-interceptor';

// Instala o interceptor antes do render — qualquer mutação financeira
// bem-sucedida dispara 'financeiro:saldo-updated' automaticamente.
installFinanceiroFetchInterceptor();

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <App />
  </StrictMode>,
);
