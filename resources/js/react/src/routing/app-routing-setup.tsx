import { Route, Routes, Navigate } from 'react-router';
import { Layout1 } from '@/components/layouts/layout-1';
import { Layout1Page } from '@/pages/dashboard/page';
import { FinanceiroPage } from '@/pages/financeiro/page';
import { BancoPage } from '@/pages/financeiro/banco/page';
import { EntidadePage } from '@/pages/financeiro/banco/entidade/page';
import { EntidadesFinanceirasPage } from '@/pages/financeiro/entidade/page';
import { DominusIAPage } from '@/pages/financeiro/ia/page';
import { ContabilidadePage } from '@/pages/contabilidade/page';
import { NotificationsPage } from '@/pages/notifications/page';
import { CemiterioPage } from '@/pages/cemiterio/page';
import { UsuariosPage } from '@/pages/cadastros/usuarios/page';
import { FraternidadePage } from '@/pages/fraternidade/page';
import { LoginCustomizationPage } from '@/pages/confs/login/page';

export function AppRoutingSetup() {
  return (
    <Routes>
      <Route element={<Layout1 />}>
        <Route path="/dashboard" element={<Layout1Page />} />
        <Route path="/dashboard/dark-sidebar" element={<Layout1Page />} />

        {/* Módulo Financeiro */}
        <Route path="/financeiro" element={<FinanceiroPage />} />
        <Route path="/financeiro/banco" element={<BancoPage />} />
        <Route path="/financeiro/banco/entidade/:id" element={<EntidadePage />} />
        <Route path="/financeiro/entidades" element={<EntidadesFinanceirasPage />} />
        <Route path="/financeiro/ia" element={<DominusIAPage />} />

        {/* Módulo Contabilidade */}
        <Route path="/contabilidade" element={<ContabilidadePage />} />

        {/* Notificações */}
        <Route path="/notifications" element={<NotificationsPage />} />

        {/* Fraternidade */}
        <Route path="/fraternidade" element={<FraternidadePage />} />

        {/* Módulo Cemitério */}
        <Route path="/cemiterio" element={<CemiterioPage />} />

        {/* Módulo Cadastros */}
        <Route path="/cadastros/usuarios" element={<UsuariosPage />} />

        {/* Configurações — Personalizar Tela de Login */}
        <Route path="/confs/login" element={<LoginCustomizationPage />} />
      </Route>
      <Route path="*" element={<Navigate to="/dashboard" replace />} />
    </Routes>
  );
}
