<x-tenant-app-layout>
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page bg-->
        <div class="page-bg d-flex flex-column flex-center flex-column-fluid">
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-center text-center p-10">
                <!--begin::Wrapper-->
                <div class="card card-flush w-md-650px py-5">
                    <div class="card-body py-15 py-lg-20">
                        <!--begin::Title-->
                        <h1 class="fw-bolder fs-2hx text-gray-900 mb-4">Alteração de Senha Obrigatória</h1>
                        <!--end::Title-->
                        
                        <!--begin::Text-->
                        <div class="fw-semibold fs-6 text-gray-500 mb-7">
                            Por motivos de segurança, você deve alterar sua senha antes de continuar.
                        </div>
                        <!--end::Text-->
                        
                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" method="POST" action="{{ route('password.change') }}">
                            @csrf
                            
                            <!--begin::Input group-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-bold text-gray-900 fs-6 mb-2">Senha Atual *</label>
                                <input class="form-control form-control-lg form-control-solid" 
                                    type="password" 
                                    name="current_password" 
                                    autocomplete="current-password" 
                                    placeholder="Digite sua senha atual" />
                                @error('current_password')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-bold text-gray-900 fs-6 mb-2">Nova Senha *</label>
                                <input class="form-control form-control-lg form-control-solid" 
                                    type="password" 
                                    name="password" 
                                    autocomplete="new-password" 
                                    placeholder="Digite sua nova senha" />
                                @error('password')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-bold text-gray-900 fs-6 mb-2">Confirmar Nova Senha *</label>
                                <input class="form-control form-control-lg form-control-solid" 
                                    type="password" 
                                    name="password_confirmation" 
                                    autocomplete="new-password" 
                                    placeholder="Confirme sua nova senha" />
                                @error('password_confirmation')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!--end::Input group-->
                            
                            <!-- Política de senha -->
                            <div class="alert alert-light-primary d-flex align-items-center p-5 mb-8">
                                <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-primary">Política de Senha</h4>
                                    <span>As senhas devem ter entre 8 e 256 caracteres e usar uma combinação de pelo menos três dos seguintes itens: letras maiúsculas, letras minúsculas, números e símbolos.</span>
                                </div>
                            </div>
                            
                            <!--begin::Actions-->
                            <div class="d-flex flex-center">
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Alterar Senha</span>
                                    <span class="indicator-progress">Aguarde...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page bg-->
    </div>
</x-tenant-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    const passwordInput = form.querySelector('input[name="password"]');
    const confirmPasswordInput = form.querySelector('input[name="password_confirmation"]');
    
    // Função para validar complexidade da senha
    function validatePasswordComplexity(password) {
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumbers = /[0-9]/.test(password);
        const hasSymbols = /[^A-Za-z0-9]/.test(password);
        
        const complexityCount = hasUppercase + hasLowercase + hasNumbers + hasSymbols;
        
        return {
            isValid: complexityCount >= 3,
            count: complexityCount,
            hasUppercase,
            hasLowercase,
            hasNumbers,
            hasSymbols
        };
    }
    
    // Validação em tempo real da senha
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        if (password.length > 0) {
            const validation = validatePasswordComplexity(password);
            if (!validation.isValid) {
                this.classList.add('is-invalid');
                // Criar ou atualizar mensagem de erro
                let errorDiv = this.parentNode.querySelector('.password-complexity-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'password-complexity-error fv-plugins-message-container invalid-feedback';
                    this.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = 'A senha deve conter pelo menos 3 dos seguintes: letras maiúsculas, minúsculas, números e símbolos.';
            } else {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.password-complexity-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        }
    });
    
    // Validação em tempo real da confirmação
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword.length > 0 && password !== confirmPassword) {
            this.classList.add('is-invalid');
            // Criar ou atualizar mensagem de erro
            let errorDiv = this.parentNode.querySelector('.password-confirm-error');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'password-confirm-error fv-plugins-message-container invalid-feedback';
                this.parentNode.appendChild(errorDiv);
            }
            errorDiv.textContent = 'As senhas não conferem.';
        } else if (confirmPassword.length > 0 && password === confirmPassword) {
            this.classList.remove('is-invalid');
            const errorDiv = this.parentNode.querySelector('.password-confirm-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    });
    
    // Validação do formulário
    form.addEventListener('submit', function(e) {
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        
        // Validar complexidade
        const passwordValidation = validatePasswordComplexity(password);
        if (!passwordValidation.isValid) {
            e.preventDefault();
            passwordInput.classList.add('is-invalid');
            return false;
        }
        
        // Validar confirmação
        if (password !== confirmPassword) {
            e.preventDefault();
            confirmPasswordInput.classList.add('is-invalid');
            return false;
        }
        
        // Mostrar loading
        submitBtn.setAttribute('data-kt-indicator', 'on');
    });
});
</script>
