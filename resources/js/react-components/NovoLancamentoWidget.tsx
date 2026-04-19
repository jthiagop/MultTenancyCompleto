import { useState, useRef, useEffect } from 'react';
import * as DropdownMenuPrimitive from '@radix-ui/react-dropdown-menu';
import * as DialogPrimitive from '@radix-ui/react-dialog';
import { PlusCircle, ChevronDown, X } from 'lucide-react';

type TipoLancamento = 'receita' | 'despesa';

const TITULO: Record<TipoLancamento, string> = {
  receita: 'Nova Receita',
  despesa: 'Nova Despesa',
};

// ── Dropdown ──────────────────────────────────────────────────────────────────

function NovoLancamentoDropdown({ onSelect }: { onSelect: (t: TipoLancamento) => void }) {
  return (
    <DropdownMenuPrimitive.Root>
      <DropdownMenuPrimitive.Trigger asChild>
        <button className="btn btn-sm btn-primary d-inline-flex align-items-center gap-2">
          <PlusCircle size={15} />
          Novo Lançamento
          <ChevronDown size={13} className="opacity-75" />
        </button>
      </DropdownMenuPrimitive.Trigger>

      <DropdownMenuPrimitive.Portal>
        <DropdownMenuPrimitive.Content
          align="end"
          sideOffset={4}
          className="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
          style={{ zIndex: 9999 }}
        >
          <div className="menu-item px-3">
            <div className="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
              O que deseja criar?
            </div>
          </div>

          <DropdownMenuPrimitive.Item asChild>
            <div className="menu-item px-3">
              <button
                className="menu-link px-3 w-100 border-0 bg-transparent text-start"
                onClick={() => onSelect('receita')}
              >
                <i className="fa-regular fa-circle-up text-success me-2 fs-6" />
                Nova Receita
              </button>
            </div>
          </DropdownMenuPrimitive.Item>

          <DropdownMenuPrimitive.Item asChild>
            <div className="menu-item px-3">
              <button
                className="menu-link px-3 w-100 border-0 bg-transparent text-start"
                onClick={() => onSelect('despesa')}
              >
                <i className="fa-regular fa-circle-down text-danger me-2 fs-6" />
                Nova Despesa
              </button>
            </div>
          </DropdownMenuPrimitive.Item>

        </DropdownMenuPrimitive.Content>
      </DropdownMenuPrimitive.Portal>
    </DropdownMenuPrimitive.Root>
  );
}

// ── Sheet / Drawer lateral ────────────────────────────────────────────────────

function LancamentoSheet({
  open,
  tipo,
  onClose,
}: {
  open: boolean;
  tipo: TipoLancamento | null;
  onClose: () => void;
}) {
  return (
    <DialogPrimitive.Root open={open} onOpenChange={(v) => !v && onClose()}>
      <DialogPrimitive.Portal>
        <DialogPrimitive.Overlay
          style={{
            position: 'fixed', inset: 0, zIndex: 10000,
            background: 'rgba(0,0,0,0.3)', backdropFilter: 'blur(4px)',
          }}
        />
        <DialogPrimitive.Content
          style={{
            position: 'fixed', top: 0, right: 0, bottom: 0,
            width: '100%', maxWidth: '480px', zIndex: 10001,
            background: '#fff', display: 'flex', flexDirection: 'column',
            boxShadow: '-4px 0 24px rgba(0,0,0,.12)',
          }}
        >
          {/* Header */}
          <div
            className="d-flex align-items-center justify-content-between px-5 py-4"
            style={{ borderBottom: '1px solid #e9ecef' }}
          >
            <div className="d-flex align-items-center gap-2">
              {tipo === 'receita' && <i className="fa-regular fa-circle-up text-success fs-4" />}
              {tipo === 'despesa' && <i className="fa-regular fa-circle-down text-danger fs-4" />}
              <DialogPrimitive.Title className="fw-bold fs-5 mb-0">
                {tipo ? TITULO[tipo] : ''}
              </DialogPrimitive.Title>
            </div>
            <DialogPrimitive.Close asChild>
              <button
                className="btn btn-sm btn-icon btn-light"
                aria-label="Fechar"
                onClick={onClose}
              >
                <X size={16} />
              </button>
            </DialogPrimitive.Close>
          </div>

          {/* Conteúdo */}
          <div className="flex-grow-1 overflow-auto px-5 py-6">
            <p className="text-muted fs-7">
              Formulário de {tipo ? TITULO[tipo].toLowerCase() : ''} será implementado aqui.
            </p>
          </div>

          {/* Footer */}
          <div
            className="d-flex justify-content-end gap-2 px-5 py-4"
            style={{ borderTop: '1px solid #e9ecef' }}
          >
            <button className="btn btn-sm btn-light" onClick={onClose}>
              Cancelar
            </button>
            <button className="btn btn-sm btn-primary">
              Salvar
            </button>
          </div>
        </DialogPrimitive.Content>
    </DialogPrimitive.Portal>
    </DialogPrimitive.Root>
  );
}

// ── Widget raiz ───────────────────────────────────────────────────────────────

export function NovoLancamentoWidget() {
  const [drawerTipo, setDrawerTipo] = useState<TipoLancamento | null>(null);

  return (
    <>
      <NovoLancamentoDropdown onSelect={(tipo) => setDrawerTipo(tipo)} />
      <LancamentoSheet
        open={drawerTipo !== null}
        tipo={drawerTipo}
        onClose={() => setDrawerTipo(null)}
      />
    </>
  );
}
