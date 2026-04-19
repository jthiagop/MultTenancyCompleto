import { createElement, StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import components from './react-components/index';

function mountAll(root: Element | Document = document) {
    root.querySelectorAll<HTMLElement>('[data-react-component]').forEach((el) => {
        const name = el.dataset.reactComponent!;
        const Component = components[name];

        if (!Component) {
            console.warn(`[react-bridge] Componente não encontrado: "${name}"`);
            return;
        }

        let props: Record<string, unknown> = {};
        try {
            props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
        } catch {
            console.error(`[react-bridge] Props inválidas em "${name}":`, el.dataset.props);
        }

        createRoot(el).render(
            <StrictMode>
                {/* eslint-disable-next-line @typescript-eslint/no-explicit-any */}
                {createElement(Component as any, props)}
            </StrictMode>,
        );
    });
}

// Monta ao carregar a página
mountAll();

// Expõe para uso em conteúdo carregado via AJAX
(window as Window & { __mountReactComponents?: (root?: Element) => void }).__mountReactComponents = mountAll;
