<x-app-layout>
    {{-- ==========================================================
         TELA DE GESTÃO DE TENANTS (banco central)
         Reescrita 2026-05-03:
           - Removido HTML legado de "Customers" do template Metronic
             (modal de export, filtro Mastercard/Visa, atributos type=
             duplicados, :value pseudo-componente que não funcionava).
           - Submissão do formulário agora via fetch + JSON; controller
             responde 201/422/500 com mensagens claras.
           - Loading visível, validação inline, redirect só após sucesso.
           - Listagem com avatar gerado, contador, ações (deletar/copiar
             código mobile) e empty state.
         O controller (App\Http\Controllers\TenantController) trata o
         pipeline de criação de DB + migrations atomicamente: se algo
         falhar, o tenant órfão é removido automaticamente.
       ========================================================== --}}

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('components.toolbar')

            {{-- Flash messages globais --}}
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center mb-5">
                    <i class="fas fa-check-circle me-3 fs-3 text-success"></i>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center mb-5">
                    <i class="fas fa-exclamation-triangle me-3 fs-3 text-danger"></i>
                    <div class="flex-grow-1">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                {{-- Header --}}
                <div class="card-header border-0 pt-6 align-items-center">
                    <div class="card-title d-flex flex-column">
                        <h2 class="fw-bold mb-1">Tenants</h2>
                        <span class="text-muted fs-7">
                            {{ count($tenants) }} {{ count($tenants) === 1 ? 'tenant cadastrado' : 'tenants cadastrados' }}
                        </span>
                    </div>

                    <div class="card-toolbar">
                        <div class="position-relative me-3">
                            <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" id="tenant_search"
                                class="form-control form-control-solid w-250px ps-10"
                                placeholder="Buscar por nome, e-mail ou domínio…" />
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_tenant">
                            <i class="fas fa-plus me-2"></i>Novo Tenant
                        </button>
                    </div>
                </div>

                {{-- Tabela --}}
                <div class="card-body pt-3">
                    @if (count($tenants) === 0)
                        <div class="text-center py-15">
                            <div class="symbol symbol-150px mx-auto mb-7">
                                <div class="symbol-label bg-light-primary">
                                    <i class="fas fa-building text-primary fs-2x"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-3">Nenhum tenant cadastrado</h3>
                            <p class="text-muted mb-7">Crie o primeiro tenant para começar.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_add_tenant">
                                <i class="fas fa-plus me-2"></i>Criar Tenant
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0" id="kt_tenants_table">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-200px">Tenant</th>
                                        <th class="min-w-200px">E-mail</th>
                                        <th class="min-w-150px">Domínio</th>
                                        <th class="min-w-100px">ID</th>
                                        <th class="min-w-150px">Criado em</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-700">
                                    @foreach ($tenants as $tenant)
                                        <tr data-tenant-id="{{ $tenant->id }}"
                                            data-search-blob="{{ strtolower($tenant->name . ' ' . $tenant->email . ' ' . $tenant->domains->pluck('domain')->implode(' ')) }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-40px me-3">
                                                        <div class="symbol-label bg-light-primary text-primary fw-bold fs-6">
                                                            {{ strtoupper(mb_substr($tenant->name, 0, 2)) }}
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-gray-900 fw-bold mb-0">{{ $tenant->name }}</span>
                                                        @if ($tenant->app_access_code)
                                                            <span class="text-muted fs-8">App: {{ $tenant->app_access_code }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $tenant->email }}" class="text-gray-700 text-hover-primary">
                                                    {{ $tenant->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @forelse ($tenant->domains as $domain)
                                                    <a href="//{{ $domain->domain }}" target="_blank" rel="noopener"
                                                        class="badge badge-light-success me-1">
                                                        <i class="fas fa-external-link-alt me-1 fs-9"></i>{{ $domain->domain }}
                                                    </a>
                                                @empty
                                                    <span class="badge badge-light-warning">
                                                        <i class="fas fa-exclamation-triangle me-1 fs-9"></i>Sem domínio
                                                    </span>
                                                @endforelse
                                            </td>
                                            <td>
                                                <code class="text-muted fs-8" title="{{ $tenant->id }}">
                                                    {{ Str::limit($tenant->id, 8, '…') }}
                                                </code>
                                            </td>
                                            <td>
                                                <span class="text-gray-700">{{ $tenant->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button type="button" class="btn btn-icon btn-sm btn-light-primary"
                                                        title="Gerar código mobile"
                                                        onclick="openAppCodeModal('{{ $tenant->id }}')">
                                                        <i class="fas fa-mobile-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-icon btn-sm btn-light-danger"
                                                        title="Remover tenant"
                                                        onclick="deleteTenant('{{ $tenant->id }}', '{{ addslashes($tenant->name) }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ==================================================
                 Modal: Novo Tenant (submit via fetch + JSON)
               ================================================== --}}
            <div class="modal fade" id="kt_modal_add_tenant" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <div class="modal-content">
                        <form id="kt_modal_add_tenant_form" novalidate>
                            @csrf
                            <div class="modal-header">
                                <h2 class="fw-bold mb-0">Novo Tenant</h2>
                                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Fechar">
                                    <i class="fas fa-times fs-2"></i>
                                </button>
                            </div>

                            <div class="modal-body py-8 px-lg-10">
                                {{-- Banner de erro genérico --}}
                                <div id="form_global_error" class="alert alert-danger d-none mb-5" role="alert"></div>

                                {{-- Seção 1: organização --}}
                                <div class="separator separator-content my-5">
                                    <span class="text-muted fs-7 fw-semibold text-uppercase">Organização</span>
                                </div>

                                <div class="mb-6">
                                    <label class="required form-label fw-semibold">Nome da empresa</label>
                                    <input type="text" class="form-control form-control-solid"
                                        name="name" required autocomplete="organization"
                                        placeholder="Ex.: Província Nossa Senhora da Penha" />
                                    <div class="form-text">Será o nome da organização (Company) dentro do tenant.</div>
                                    <div class="invalid-feedback" data-field-error="name"></div>
                                </div>

                                {{-- Seção 2: administrador --}}
                                <div class="separator separator-content my-5">
                                    <span class="text-muted fs-7 fw-semibold text-uppercase">Administrador inicial</span>
                                </div>

                                <div class="mb-6">
                                    <label class="required form-label fw-semibold">Nome do administrador</label>
                                    <input type="text" class="form-control form-control-solid"
                                        name="user_name" required autocomplete="name"
                                        placeholder="Ex.: João da Silva" />
                                    <div class="form-text">Nome da pessoa que receberá o primeiro acesso.</div>
                                    <div class="invalid-feedback" data-field-error="user_name"></div>
                                </div>

                                <div class="mb-6">
                                    <label class="required form-label fw-semibold">E-mail do administrador</label>
                                    <input type="email" class="form-control form-control-solid"
                                        name="email" required autocomplete="email"
                                        placeholder="admin@dominio.com.br" />
                                    <div class="form-text">Será o login do primeiro usuário.</div>
                                    <div class="invalid-feedback" data-field-error="email"></div>
                                </div>

                                {{-- Domínio --}}
                                <div class="mb-6">
                                    <label class="required form-label fw-semibold">Subdomínio</label>
                                    <div class="input-group input-group-solid">
                                        <input type="text" class="form-control form-control-solid"
                                            name="domain_name" id="domain_name_input" required
                                            autocomplete="off" placeholder="recife"
                                            pattern="^[a-z0-9]([a-z0-9-]*[a-z0-9])?$" minlength="3" maxlength="63"
                                            inputmode="url" />
                                        <span class="input-group-text">.{{ config('app.domain') }}</span>
                                    </div>
                                    <div class="form-text">
                                        Apenas letras minúsculas, números e hifens (3 a 63 caracteres).
                                    </div>
                                    <div class="invalid-feedback" data-field-error="domain_name"></div>
                                </div>

                                {{-- Seção 3: credenciais --}}
                                <div class="separator separator-content my-5">
                                    <span class="text-muted fs-7 fw-semibold text-uppercase">Senha de acesso</span>
                                </div>

                                <div class="mb-6">
                                    <label class="required form-label fw-semibold">Senha</label>
                                    <input type="password" class="form-control form-control-solid"
                                        name="password" required autocomplete="new-password" minlength="8" />
                                    <div class="invalid-feedback" data-field-error="password"></div>
                                </div>

                                {{-- Confirmar senha --}}
                                <div class="mb-2">
                                    <label class="required form-label fw-semibold">Confirmar senha</label>
                                    <input type="password" class="form-control form-control-solid"
                                        name="password_confirmation" required autocomplete="new-password" />
                                    <div class="invalid-feedback" data-field-error="password_confirmation"></div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="kt_modal_add_tenant_submit">
                                    <span class="indicator-label">Criar Tenant</span>
                                    <span class="indicator-progress">
                                        Criando…
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ==================================================
                 Modal: Código de acesso mobile
               ================================================== --}}
            <div class="modal fade" id="kt_modal_app_code" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-500px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="fw-bold mb-0">Código de acesso mobile</h2>
                            <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Fechar">
                                <i class="fas fa-times fs-2"></i>
                            </button>
                        </div>
                        <div class="modal-body text-center py-8">
                            <p class="text-muted mb-4">Use este código no aplicativo mobile:</p>
                            <div class="bg-light-primary rounded py-5 mb-4">
                                <span id="display_app_code" class="fs-1 fw-bold text-primary tracking-wider">---</span>
                            </div>
                            <p class="text-muted fs-7">Compartilhe com os usuários autorizados.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary" id="btn_copy_code">
                                <i class="fas fa-copy me-2"></i>
                                <span class="btn-copy-label">Copiar código</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
(function () {
    'use strict';

    // ── Helpers ──────────────────────────────────────────────────────────
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function clearFieldErrors(form) {
        form.querySelectorAll('[data-field-error]').forEach((el) => {
            el.textContent = '';
            const input = form.querySelector(`[name="${el.dataset.fieldError}"]`);
            input?.classList.remove('is-invalid');
        });
        form.querySelector('#form_global_error')?.classList.add('d-none');
    }

    function setFieldError(form, field, message) {
        const slot = form.querySelector(`[data-field-error="${field}"]`);
        const input = form.querySelector(`[name="${field}"]`);
        if (slot) slot.textContent = Array.isArray(message) ? message[0] : message;
        if (input) input.classList.add('is-invalid');
    }

    function setGlobalError(form, message) {
        const banner = form.querySelector('#form_global_error');
        if (!banner) return;
        banner.textContent = message;
        banner.classList.remove('d-none');
    }

    function setLoading(button, loading) {
        if (!button) return;
        button.disabled = loading;
        button.dataset.ktIndicator = loading ? 'on' : '';
    }

    // ── Busca client-side simples ────────────────────────────────────────
    const searchInput = document.getElementById('tenant_search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const q = e.target.value.trim().toLowerCase();
            document.querySelectorAll('#kt_tenants_table tbody tr').forEach((row) => {
                const blob = row.dataset.searchBlob || '';
                row.style.display = !q || blob.includes(q) ? '' : 'none';
            });
        });
    }

    // ── Auto-normalização do subdomínio ──────────────────────────────────
    const domainInput = document.getElementById('domain_name_input');
    if (domainInput) {
        domainInput.addEventListener('input', (e) => {
            const before = e.target.value;
            const after = before.toLowerCase().replace(/[^a-z0-9-]/g, '');
            if (before !== after) {
                const pos = e.target.selectionStart;
                e.target.value = after;
                e.target.setSelectionRange(pos - (before.length - after.length), pos - (before.length - after.length));
            }
        });
    }

    // ── Submit do form de novo tenant ────────────────────────────────────
    const form = document.getElementById('kt_modal_add_tenant_form');
    const submitBtn = document.getElementById('kt_modal_add_tenant_submit');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearFieldErrors(form);
            setLoading(submitBtn, true);

            try {
                const formData = new FormData(form);
                const res = await fetch('{{ route('tenants.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                const data = await res.json().catch(() => ({}));

                if (res.status === 422 && data.errors) {
                    // Validação Laravel (objeto { campo: ['mensagem'] })
                    Object.entries(data.errors).forEach(([field, msgs]) => setFieldError(form, field, msgs));
                    return;
                }

                if (!res.ok || data.success === false) {
                    setGlobalError(form, data.message || 'Não foi possível criar o tenant.');
                    return;
                }

                // Sucesso — recarrega a lista
                window.location.href = '{{ route('tenants.index') }}';
            } catch (err) {
                console.error(err);
                setGlobalError(form, 'Erro de rede. Verifique sua conexão e tente novamente.');
            } finally {
                setLoading(submitBtn, false);
            }
        });
    }

    // ── Deletar tenant ───────────────────────────────────────────────────
    window.deleteTenant = async function (tenantId, tenantName) {
        if (!confirm(`Remover o tenant "${tenantName}" e seu banco de dados?\n\nEsta ação é IRREVERSÍVEL.`)) {
            return;
        }

        try {
            const res = await fetch(`/tenants/${tenantId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success) {
                document.querySelector(`tr[data-tenant-id="${tenantId}"]`)?.remove();
                // Se ficou vazio, recarrega para mostrar empty-state
                if (!document.querySelector('#kt_tenants_table tbody tr')) {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Erro ao remover tenant.');
            }
        } catch (err) {
            console.error(err);
            alert('Erro de rede ao remover tenant.');
        }
    };

    // ── Modal: código de acesso mobile ──────────────────────────────────
    let currentAppCode = '';

    window.openAppCodeModal = async function (tenantId) {
        const modalEl = document.getElementById('kt_modal_app_code');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const display = document.getElementById('display_app_code');
        const copyBtn = document.getElementById('btn_copy_code');

        display.textContent = 'Gerando…';
        display.classList.remove('text-primary', 'text-danger');
        display.classList.add('text-muted');
        copyBtn.disabled = true;
        currentAppCode = '';
        modal.show();

        try {
            const res = await fetch(`/tenants/${tenantId}/generate-code`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: '{}',
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Erro ao gerar código');

            currentAppCode = data.code;
            display.textContent = data.code;
            display.classList.remove('text-muted');
            display.classList.add('text-primary');
            copyBtn.disabled = false;
        } catch (err) {
            display.textContent = 'Erro';
            display.classList.remove('text-muted');
            display.classList.add('text-danger');
            console.error(err);
        }
    };

    document.getElementById('btn_copy_code')?.addEventListener('click', async () => {
        if (!currentAppCode) return;
        const btn = document.getElementById('btn_copy_code');
        const label = btn.querySelector('.btn-copy-label');
        const originalLabel = label.textContent;

        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(currentAppCode);
            } else {
                // Fallback execCommand
                const ta = document.createElement('textarea');
                ta.value = currentAppCode;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            }
            label.textContent = 'Copiado!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            setTimeout(() => {
                label.textContent = originalLabel;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 1800);
        } catch (err) {
            alert('Não foi possível copiar. Código: ' + currentAppCode);
        }
    });
})();
</script>
