/**
 * AddressCard — card de endereço reutilizável.
 *
 * Funciona com `useState` simples ou com `react-hook-form` via `Controller`:
 *
 * ### Exemplo com useState
 * ```tsx
 * const [addr, setAddr] = useState<AddressValue>(EMPTY_ADDRESS);
 * <AddressCard value={addr} onChange={setAddr} />
 * ```
 *
 * ### Exemplo com react-hook-form
 * ```tsx
 * <Controller
 *   control={control}
 *   name="endereco"
 *   render={({ field }) => (
 *     <AddressCard value={field.value} onChange={field.onChange} />
 *   )}
 * />
 * ```
 *
 * ### Collapsível (padrão do cadastro de parceiro)
 * ```tsx
 * <AddressCard value={addr} onChange={setAddr} collapsible defaultOpen={false} />
 * ```
 */

import { useState } from 'react';
import { Loader2, MapPin, Minus, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardToolbar } from '@/components/ui/card';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { MaskedInput } from '@/components/common/masked-input';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';

// ── Constantes ─────────────────────────────────────────────────────────────

export const BR_UF = [
  'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
  'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
  'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
] as const;

export type BrUf = (typeof BR_UF)[number];

// ── Tipos ───────────────────────────────────────────────────────────────────

export interface AddressValue {
  cep: string;
  logradouro: string;
  numero: string;
  bairro: string;
  cidade: string;
  uf: string;
}

export const EMPTY_ADDRESS: AddressValue = {
  cep: '',
  logradouro: '',
  numero: '',
  bairro: '',
  cidade: '',
  uf: '',
};

export interface AddressCardProps {
  value: AddressValue;
  onChange: (v: AddressValue) => void;

  /**
   * Título exibido no cabeçalho do card.
   * @default "Endereço"
   */
  title?: string;

  /**
   * Consulta automática de CEP via ViaCEP ao digitar 8 dígitos.
   * @default true
   */
  withCepLookup?: boolean;

  /**
   * Torna o card collapsível (padrão parceiro).
   * @default false
   */
  collapsible?: boolean;

  /**
   * Estado inicial quando `collapsible` for true.
   * @default true
   */
  defaultOpen?: boolean;

  disabled?: boolean;

  className?: string;
}

// ── Componente ──────────────────────────────────────────────────────────────

export function AddressCard({
  value,
  onChange,
  title = 'Endereço',
  withCepLookup = true,
  collapsible = false,
  defaultOpen = true,
  disabled = false,
  className,
}: AddressCardProps) {
  const [loadingCep, setLoadingCep] = useState(false);
  const [open, setOpen] = useState(defaultOpen);

  function set<K extends keyof AddressValue>(field: K, val: string) {
    onChange({ ...value, [field]: val });
  }

  async function handleCepChange(rawCep: string) {
    set('cep', rawCep);
    if (!withCepLookup) return;

    const digits = rawCep.replace(/\D/g, '');
    if (digits.length !== 8) return;

    setLoadingCep(true);
    try {
      const res = await fetch(`https://viacep.com.br/ws/${digits}/json/`);
      if (!res.ok) {
        notify.error('Erro na consulta', 'Não foi possível consultar o CEP.');
        return;
      }
      const data = (await res.json()) as {
        erro?: boolean;
        logradouro?: string;
        bairro?: string;
        localidade?: string;
        uf?: string;
      };

      if (data.erro) {
        notify.warning('CEP não encontrado', 'Verifique o CEP e tente novamente.');
        return;
      }

      onChange({
        ...value,
        cep: rawCep,
        logradouro: data.logradouro ?? value.logradouro,
        bairro: data.bairro ?? value.bairro,
        cidade: data.localidade ?? value.cidade,
        uf: data.uf?.toUpperCase() ?? value.uf,
      });
    } catch {
      notify.error('Erro na consulta', 'Não foi possível consultar o CEP. Verifique sua conexão.');
    } finally {
      setLoadingCep(false);
    }
  }

  const content = (
    <CardContent className="pt-4">
      <div className="grid grid-cols-12 gap-3">
        {/* CEP */}
        <div className="col-span-12 sm:col-span-3 space-y-2">
          <Label className="text-xs">CEP</Label>
          <div className="flex items-center gap-2">
            <MaskedInput
              maskType="cep"
              value={value.cep}
              onMaskedChange={handleCepChange}
              placeholder="00000-000"
              disabled={disabled || loadingCep}
            />
            {loadingCep && (
              <Loader2 className="size-4 shrink-0 animate-spin text-muted-foreground" />
            )}
          </div>
        </div>

        {/* Logradouro */}
        <div className="col-span-12 sm:col-span-9 space-y-2">
          <Label className="text-xs">Logradouro</Label>
          <Input
            value={value.logradouro}
            onChange={(e) => set('logradouro', e.target.value)}
            placeholder="Rua, Av., etc."
            disabled={disabled}
          />
        </div>

        {/* Número */}
        <div className="col-span-12 sm:col-span-2 space-y-2">
          <Label className="text-xs">Número</Label>
          <Input
            value={value.numero}
            onChange={(e) => set('numero', e.target.value)}
            placeholder="Nº"
            disabled={disabled}
          />
        </div>

        {/* Bairro */}
        <div className="col-span-12 sm:col-span-4 space-y-2">
          <Label className="text-xs">Bairro</Label>
          <Input
            value={value.bairro}
            onChange={(e) => set('bairro', e.target.value)}
            placeholder="Bairro"
            disabled={disabled}
          />
        </div>

        {/* Cidade */}
        <div className="col-span-12 sm:col-span-4 space-y-2">
          <Label className="text-xs">Cidade</Label>
          <Input
            value={value.cidade}
            onChange={(e) => set('cidade', e.target.value)}
            placeholder="Cidade"
            disabled={disabled}
          />
        </div>

        {/* UF */}
        <div className="col-span-12 sm:col-span-2 space-y-2">
          <Label className="text-xs">UF</Label>
          <select
            value={value.uf}
            onChange={(e) => set('uf', e.target.value)}
            disabled={disabled}
            className={cn(
              'flex w-full bg-background border border-input rounded-md shadow-xs shadow-black/5',
              'h-8.5 px-3 text-[0.8125rem] text-foreground',
              'focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 focus-visible:border-ring',
              'disabled:cursor-not-allowed disabled:opacity-60',
              !value.uf && 'text-muted-foreground/80',
            )}
          >
            <option value="">UF</option>
            {BR_UF.map((uf) => (
              <option key={uf} value={uf}>{uf}</option>
            ))}
          </select>
        </div>
      </div>
    </CardContent>
  );

  if (collapsible) {
    return (
      <Collapsible open={open} onOpenChange={setOpen} className={className}>
        <Card className="rounded-md">
          <CardHeader className="min-h-9.5 bg-accent/50 py-2">
            <CardTitle className="text-2sm flex items-center gap-1.5">
              <MapPin className="size-3.5 text-muted-foreground" />
              {title}
            </CardTitle>
            <CardToolbar>
              <CollapsibleTrigger asChild>
                <Button variant="dim" mode="icon" size="sm" type="button" disabled={disabled}>
                  {open ? <Minus className="size-4" /> : <Plus className="size-4" />}
                </Button>
              </CollapsibleTrigger>
            </CardToolbar>
          </CardHeader>
          <CollapsibleContent>{content}</CollapsibleContent>
        </Card>
      </Collapsible>
    );
  }

  return (
    <Card className={cn('rounded-md', className)}>
      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
        <CardTitle className="text-2sm flex items-center gap-1.5">
          <MapPin className="size-3.5 text-muted-foreground" />
          {title}
        </CardTitle>
      </CardHeader>
      {content}
    </Card>
  );
}
