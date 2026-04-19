import type { SelectHTMLAttributes } from 'react';
import { forwardRef, useCallback, useEffect, useMemo, useRef } from 'react';
import { cn } from '@/lib/utils';

type KtSelectInstance = {
  dispose?: () => void;
  destroy?: () => void;
};

type KtSelectCtor = new (element: HTMLElement) => KtSelectInstance;

type WindowWithKtSelect = Window & {
  KTSelect?: KtSelectCtor & { getInstance?: (el: HTMLElement) => KtSelectInstance | undefined };
};

const DEFAULT_OPTIONS_LIST_CLASS = 'kt-scrollable overflow-auto max-h-[250px]';

export type KtNativeSelectProps = SelectHTMLAttributes<HTMLSelectElement> & {
  /** `data-kt-select-placeholder` (Metronic / KTUI) */
  placeholder?: string;
  /** Mescla em `data-kt-select-config` (ex.: lista rolável) */
  optionsListClassName?: string;
  /** `data-kt-select-enable-search="true"` */
  enableSearch?: boolean;
};

/**
 * `<select>` no padrão Metronic v9 (KTUI): `kt-select`, `data-kt-select`, placeholder e config JSON.
 * Se `window.KTSelect` existir (ex.: `ktui.min.js` na página), inicializa e faz dispose no unmount
 * e quando `value` muda, para manter o estado alinhado ao React.
 */
export const KtNativeSelect = forwardRef<HTMLSelectElement, KtNativeSelectProps>(function KtNativeSelect(
  {
    placeholder,
    optionsListClassName = DEFAULT_OPTIONS_LIST_CLASS,
    enableSearch,
    className,
    value,
    ...rest
  },
  ref,
) {
  const innerRef = useRef<HTMLSelectElement>(null);

  const setRefs = useCallback(
    (node: HTMLSelectElement | null) => {
      innerRef.current = node;
      if (typeof ref === 'function') ref(node);
      else if (ref) ref.current = node;
    },
    [ref],
  );

  const configJson = useMemo(
    () => JSON.stringify({ optionsClass: optionsListClassName }),
    [optionsListClassName],
  );

  useEffect(() => {
    const el = innerRef.current;
    if (!el) return;

    const w = window as WindowWithKtSelect;
    const Ctor = w.KTSelect;
    if (!Ctor) return;

    const existing = Ctor.getInstance?.(el);
    if (existing) {
      existing.dispose?.();
      existing.destroy?.();
    }

    let inst: KtSelectInstance | undefined;
    try {
      inst = new Ctor(el);
    } catch {
      /* elemento já tratado ou API diferente */
    }

    return () => {
      inst?.dispose?.();
      inst?.destroy?.();
      const left = Ctor.getInstance?.(el);
      left?.dispose?.();
      left?.destroy?.();
    };
  }, [value]);

  return (
    <select
      ref={setRefs}
      className={cn('kt-select w-full', className)}
      data-kt-select="true"
      data-kt-select-placeholder={placeholder}
      data-kt-select-config={configJson}
      {...(enableSearch ? { 'data-kt-select-enable-search': 'true' as const } : {})}
      value={value}
      {...rest}
    />
  );
});
