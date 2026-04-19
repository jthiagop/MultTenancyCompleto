import { Navigate, Route, Routes } from 'react-router';
import { AuthBrandedLayout } from '@/auth/layouts/branded';
import { AuthClassicLayout } from '@/auth/layouts/classic';
import { CallbackPage } from '@/auth/pages/callback-page';
import { ChangePasswordPage } from '@/auth/pages/change-password-page';
import { CheckEmail } from '@/auth/pages/extended/check-email';
import { ForceChangePasswordPage } from '@/auth/pages/force-change-password-page';
import { ResetPasswordChanged } from '@/auth/pages/extended/reset-password-changed';
import { ResetPasswordCheckEmail } from '@/auth/pages/extended/reset-password-check-email';
import { TwoFactorAuth } from '@/auth/pages/extended/tfa';
import { ResetPasswordPage } from '@/auth/pages/reset-password-page';
import { SignInPage } from '@/auth/pages/signin-page';
import { SignUpPage } from '@/auth/pages/signup-page';

export function AuthRoutingSetup() {
  return (
    <Routes>
      {/* /login mantém o URL na barra (alias visual do mesmo formulário que /auth/signin) */}
      <Route path="login" element={<AuthBrandedLayout />}>
        <Route index element={<SignInPage />} />
      </Route>
      <Route path="app/login" element={<AuthBrandedLayout />}>
        <Route index element={<SignInPage />} />
      </Route>
      <Route path="auth" element={<AuthBrandedLayout />}>
        <Route index element={<Navigate to="signin" replace />} />
        <Route path="signin" element={<SignInPage />} />
        <Route path="signup" element={<SignUpPage />} />
        <Route path="change-password" element={<ChangePasswordPage />} />
        <Route path="reset-password/check-email" element={<ResetPasswordCheckEmail />} />
        <Route path="reset-password/changed" element={<ResetPasswordChanged />} />
        <Route path="reset-password" element={<ResetPasswordPage />} />
        <Route path="check-email" element={<CheckEmail />} />
      </Route>

      <Route path="auth/classic" element={<AuthClassicLayout />}>
        <Route index element={<Navigate to="signin" replace />} />
        <Route path="signin" element={<SignInPage />} />
        <Route path="signup" element={<SignUpPage />} />
        <Route path="change-password" element={<ChangePasswordPage />} />
        <Route path="reset-password/check-email" element={<ResetPasswordCheckEmail />} />
        <Route path="reset-password/changed" element={<ResetPasswordChanged />} />
        <Route path="reset-password" element={<ResetPasswordPage />} />
        <Route path="check-email" element={<CheckEmail />} />
      </Route>

      <Route path="first-access" element={<AuthBrandedLayout />}>
        <Route index element={<ForceChangePasswordPage />} />
      </Route>

      <Route path="auth/callback" element={<CallbackPage />} />
      <Route path="auth/2fa" element={<TwoFactorAuth />} />

      <Route path="*" element={<Navigate to="/auth/signin" replace />} />
    </Routes>
  );
}
