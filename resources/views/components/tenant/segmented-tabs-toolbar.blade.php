{{-- resources/views/components/tenant/segmented-tabs-toolbar.blade.php --}}
@props([
    'id' => 'segmented-tabs',
    'tabs' => [],
    'active' => null,
])

<style>
    /* =========================
       SEGMENTED TABS (Light/Dark)
       Usa variáveis do Bootstrap 5.3 (data-bs-theme)
       ========================= */

    .segmented-shell {
        border: 1px solid var(--bs-border-color-translucent);
        border-radius: .5rem;
        overflow: hidden;
        background: var(--bs-body-bg);
    }

    .segmented-tab-header {
        background: var(--bs-tertiary-bg);
        border-bottom: 1px solid var(--bs-border-color-translucent);
    }

    .segmented-tab-header .nav {
        margin: 0;
    }

    .segmented-tab-header .nav-link {
        border: 0 !important;
        border-radius: 0 !important;
        width: 100%;
        padding: .85rem .5rem;
        text-align: center;

        display: flex;
        flex-direction: column;
        gap: .2rem;
        align-items: center;
        justify-content: center;

        color: var(--bs-body-color);
        opacity: .75;
        background: transparent;
        position: relative;

        transition: background .15s ease, opacity .15s ease;
        min-height: 64px;
    }

    /* separador vertical entre tabs */
    .segmented-tab-header .nav-item:not(:last-child) .nav-link::after {
        content: '';
        position: absolute;
        right: 0;
        top: 14px;
        bottom: 14px;
        width: 1px;
        background: var(--bs-border-color-translucent);
    }

    /* hover sutil + linha de cor */
    .segmented-tab-header .nav-link:hover {
        background: var(--bs-secondary-bg);
        opacity: .95;
    }

    .segmented-tab-header .nav-link:hover::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 3px;
        width: 100%;
        background: var(--seg-accent, var(--bs-primary));
        opacity: 0.5; /* Opacidade reduzida para indicar que é hover, não active */
    }

    /* ativo: destaque clean */
    .segmented-tab-header .nav-link.active {
        background: var(--bs-secondary-bg);
        opacity: 1;
    }

    /* “linha” no topo do tab ativo */
    .segmented-tab-header .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 3px;
        width: 100%;
        background: var(--seg-accent, var(--bs-primary));
    }

    .segmented-tab-label {
        font-size: .85rem;
        font-weight: 600;
        line-height: 1.1;
    }

    .segmented-tab-count {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1;
    }

    /* Toolbar */
    .segmented-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .6rem .75rem;
        background: var(--bs-tertiary-bg);
        border-bottom: 1px solid var(--bs-border-color-translucent);
        flex-wrap: wrap;
    }

    .segmented-toolbar-left,
    .segmented-toolbar-right {
        display: flex;
        gap: .5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .segmented-toolbar .btn.btn-sm {
        border-radius: .45rem;
    }

    /* (Opcional) reforço de bordas no dark, se o tema ficar suave demais */
    [data-bs-theme="dark"] .segmented-shell,
    [data-bs-theme="dark"] .segmented-tab-header,
    [data-bs-theme="dark"] .segmented-toolbar {
        border-color: rgba(255, 255, 255, .14);
    }

    [data-bs-theme="dark"] .segmented-tab-header .nav-item:not(:last-child) .nav-link::after {
        background: rgba(255, 255, 255, .14);
    }
</style>

<div class="segmented-shell">
    <!-- Tabs -->
    <div class="segmented-tab-header">
        <ul class="nav nav-justified" id="{{ $id }}-tabs" role="tablist">
            @foreach ($tabs as $tab)
                @php
                    $key = $tab['key'];
                    $paneId = $tab['paneId'] ?? ($id . '-pane-' . $key);

                    // Lógica Automática de Cores baseada na chave (se não for passado explicitamente)
                    // Padrão: Azul (Primary) | received/entrada: Verde (Success) | paid/saida: Vermelho (Danger)
                    $defaultColors = match($key) {
                        'received', 'recebimentos', 'entrada', 'credit' => ['class' => 'text-success', 'accent' => 'var(--bs-success)'],
                        'paid', 'pagamentos', 'saida', 'debit' => ['class' => 'text-danger', 'accent' => 'var(--bs-danger)'],
                        default => ['class' => 'text-primary', 'accent' => null] // null usa o CSS padrão (primary)
                    };

                    $countClass = $tab['countClass'] ?? $defaultColors['class'];
                    $accent = $tab['accent'] ?? $defaultColors['accent'];

                    $isActive = ($active === $key);
                @endphp
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link {{ $isActive ? 'active' : '' }}"
                        style="{{ $accent ? "--seg-accent: {$accent};" : '' }}"
                        id="{{ $id }}-tab-{{ $key }}"
                        data-bs-toggle="tab"
                        data-bs-target="#{{ $paneId }}"
                        type="button"
                        role="tab"
                        aria-controls="{{ $paneId }}"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                    >
                        <span class="segmented-tab-label">{{ $tab['label'] }}</span>
                        <span class="segmented-tab-count {{ $countClass }}">{{ $tab['count'] ?? 0 }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Toolbar -->
    <div class="segmented-toolbar">
        <div class="segmented-toolbar-left">
            {{ $actionsLeft ?? '' }}
        </div>
        <div class="segmented-toolbar-right">
            {{ $actionsRight ?? '' }}
        </div>
    </div>

    <!-- Tab Panes -->
    <div class="tab-content" id="{{ $id }}-content">
        {{ $panes ?? '' }}
    </div>
</div>
