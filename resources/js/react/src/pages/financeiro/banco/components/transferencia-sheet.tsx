import { useCallback, useEffect, useMemo, useState } from 'react';
import { ArrowLeft, Loader2 } from 'lucide-react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { DatePicker } from '@/components/ui/date-picker';
import { CurrencyInput } from '@/components/common/masked-input';
import { Input } from '@/components/ui/input';
import { SearchSelect } from '@/components/common/search-select';
import { useFormSelectData } from '@/hooks/useFormSelectData';
import { useAppData } from '@/hooks/useAppData';
import { notify } from '@/lib/notify';
import { todayIso } from './useLancamentoForm';
import { entidadeBadges } from './entidade-badges';

function isoToBrDate(iso: string): string {
  if (!iso || iso.length < 10) return '';
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
}

function pickFirstErr(errors: Record<string, string[] | string> | undefined, key: string): string | undefined {
  if (!errors?.[key]) return undefined;
  const v = errors[key];
  return Array.isArray(v) ? v[0] : v;
}

type Props = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSuccess?: () => void;
};

export function TransferenciaSheet({ open, onOpenChange, onSuccess }: Props) {
  const { data, loading } = useFormSelectData(null);
  const { csrfToken, companyId, companies } = useAppData();

  const [origemId, setOrigemId] = useState('');
  const [destinoId, setDestinoId] = useState('');
  const [dataIso, setDataIso] = useState('');
  const [valor, setValor] = useState('');
  const [descricao, setDescricao] = useState('');
  const [descricaoTouched, setDescricaoTouched] = useState(false);
  const [saving, setSaving] = useState(false);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

  const activeCompany = useMemo(
    () => companies.find((c) => c.id === companyId) ?? companies[0],
    [companies, companyId],
  );

  const entidadeOptions = useMemo(
    () =>
      data.entidades.map((e) => ({
        value: e.id,
        label: e.nome ?? e.label,
        badges: entidadeBadges(
          e.tipo,
          e.account_type,
          e.tipo === 'banco' && e.label !== e.nome ? e.label : undefined,
        ),
        icon:
          e.logo ??
          (e.tipo === 'caixa'
            ? '/tenancy/assets/media/svg/bancos/fraternidadecaixa.svg'
            : '/tenancy/assets/media/svg/bancos/default.svg'),
      })),
    [data.entidades],
  );

  const nomeOrigem = useMemo(
    () => entidadeOptions.find((o) => o.value === origemId)?.label ?? '',
    [entidadeOptions, origemId],
  );
  const nomeDestino = useMemo(
    () => entidadeOptions.find((o) => o.value === destinoId)?.label ?? '',
    [entidadeOptions, destinoId],
  );

  const saldoOrigemFormatado = useMemo(() => {
    if (!origemId) return null;
    const ent = data.entidades.find((e) => e.id === origemId);
    if (!ent) return null;
    const v = typeof ent.saldo_atual === 'number' ? ent.saldo_atual : 0;
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);
  }, [origemId, data.entidades]);

  useEffect(() => {
    if (!open) return;
    setOrigemId('');
    setDestinoId('');
    setDataIso(todayIso());
    setValor('');
    setDescricao('');
    setDescricaoTouched(false);
    setFieldErrors({});
  }, [open]);

  useEffect(() => {
    if (descricaoTouched) return;
    if (nomeOrigem && nomeDestino) {
      setDescricao(`Origem: ${nomeOrigem} / Destino: ${nomeDestino}`);
    } else {
      setDescricao('');
    }
  }, [nomeOrigem, nomeDestino, descricaoTouched]);

  const accountsMustDiffer =
    origemId && destinoId && origemId === destinoId ? 'As contas devem ser diferentes' : '';

  const clearField = useCallback((key: string) => {
    setFieldErrors((prev) => {
      const next = { ...prev };
      delete next[key];
      return next;
    });
  }, []);

  const handleSubmit = useCallback(async () => {
    const next: Record<string, string> = {};
    if (!origemId) next.entidade_origem_id = 'Selecione a conta de origem.';
    if (!destinoId) next.entidade_destino_id = 'Selecione a conta de destino.';
    if (origemId && destinoId && origemId === destinoId) {
      next.entidade_origem_id = 'As contas devem ser diferentes';
      next.entidade_destino_id = 'As contas devem ser diferentes';
    }
    if (!descricao.trim()) next.descricao = 'Informe a descrição.';
    if (!dataIso) next.data = 'Informe a data da transferência.';
    if (!valor.trim()) next.valor = 'Informe o valor.';
    if (Object.keys(next).length) {
      setFieldErrors(next);
      return;
    }

    setSaving(true);
    setFieldErrors({});
    try {
      const res = await fetch('/transferencia', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          entidade_origem_id: origemId,
          entidade_destino_id: destinoId,
          descricao: descricao.trim(),
          data: isoToBrDate(dataIso),
          valor: valor.trim(),
        }),
      });
      const json = (await res.json()) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[] | string>;
      };

      if (res.ok && json.success) {
        notify.success('Transferência', json.message ?? 'Transferência realizada com sucesso!');
        onOpenChange(false);
        onSuccess?.();
        return;
      }

      if (res.status === 422 && json.errors) {
        const fe: Record<string, string> = {};
        for (const key of Object.keys(json.errors)) {
          const msg = pickFirstErr(json.errors, key);
          if (msg) fe[key] = msg;
        }
        setFieldErrors(fe);
        if (!Object.keys(fe).length && json.message) {
          notify.error('Transferência', json.message);
        }
        return;
      }

      notify.error('Transferência', json.message ?? 'Não foi possível concluir a transferência.');
    } catch {
      notify.error('Transferência', 'Erro de conexão. Tente novamente.');
    } finally {
      setSaving(false);
    }
  }, [
    origemId,
    destinoId,
    descricao,
    dataIso,
    valor,
    csrfToken,
    onOpenChange,
    onSuccess,
  ]);

  const nowLabel = useMemo(() => {
    try {
      return new Intl.DateTimeFormat('pt-BR', {
        dateStyle: 'short',
        timeStyle: 'medium',
      }).format(new Date());
    } catch {
      return '';
    }
  }, [open]);

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="w-full sm:max-w-md flex flex-col gap-0 p-0" aria-describedby={undefined}>
        <SheetHeader className="px-5 py-4 border-b border-border space-y-0">
          <div className="flex items-center gap-2">
            <Button
              type="button"
              variant="ghost"
              size="icon"
              className="size-8 shrink-0 text-primary"
              onClick={() => onOpenChange(false)}
              aria-label="Voltar"
            >
              <ArrowLeft className="size-5" />
            </Button>
            <SheetTitle className="text-base font-semibold text-start leading-tight">
              Nova transferência entre contas
            </SheetTitle>
          </div>
        </SheetHeader>

        <SheetBody className="flex-1 overflow-y-auto px-5 py-4 space-y-5">
          <div className="rounded-lg border border-border bg-muted/30 px-4 py-3 text-sm">
            <div className="flex justify-between gap-3 items-start">
              <div>
                <p className="font-medium text-foreground">{activeCompany?.name ?? 'Empresa'}</p>
                <p className="text-xs text-muted-foreground mt-1">Última atualização: {nowLabel}</p>
              </div>
              <div className="text-end shrink-0">
                <p className="text-xs text-muted-foreground">Saldo disponível</p>
                <p className="text-sm font-semibold tabular-nums">
                  {saldoOrigemFormatado ?? '—'}
                </p>
              </div>
            </div>
          </div>

          <div className="space-y-2">
            <Label className="text-xs">
              Conta de origem <span className="text-destructive">*</span>
            </Label>
            <SearchSelect
              popoverModal={false}
              options={entidadeOptions}
              value={origemId}
              onValueChange={(v) => {
                setOrigemId(v);
                clearField('entidade_origem_id');
              }}
              placeholder="Selecione a conta de origem"
              disabled={loading}
            />
            {(accountsMustDiffer || fieldErrors.entidade_origem_id) && (
              <p className="text-xs text-destructive">{fieldErrors.entidade_origem_id ?? accountsMustDiffer}</p>
            )}
          </div>

          <div className="space-y-2">
            <Label className="text-xs">
              Conta de destino <span className="text-destructive">*</span>
            </Label>
            <SearchSelect
              popoverModal={false}
              options={entidadeOptions}
              value={destinoId}
              onValueChange={(v) => {
                setDestinoId(v);
                clearField('entidade_destino_id');
              }}
              placeholder="Selecione a conta de destino"
              disabled={loading}
            />
            {(accountsMustDiffer || fieldErrors.entidade_destino_id) && (
              <p className="text-xs text-destructive">{fieldErrors.entidade_destino_id ?? accountsMustDiffer}</p>
            )}
          </div>

          <div className="space-y-2">
            <Label className="text-xs">
              Descrição <span className="text-destructive">*</span>
            </Label>
            <Input
              value={descricao}
              onChange={(e) => {
                setDescricaoTouched(true);
                setDescricao(e.target.value);
                clearField('descricao');
              }}
              placeholder="Ex.: Origem: Conta A / Destino: Conta B"
              disabled={loading}
              className={fieldErrors.descricao ? 'border-destructive' : ''}
            />
            {fieldErrors.descricao && <p className="text-xs text-destructive">{fieldErrors.descricao}</p>}
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div className="space-y-2">
              <Label className="text-xs">
                Data da transferência <span className="text-destructive">*</span>
              </Label>
              <DatePicker value={dataIso} onChange={(iso) => { setDataIso(iso); clearField('data'); }} disabled={loading} />
              {fieldErrors.data && <p className="text-xs text-destructive">{fieldErrors.data}</p>}
            </div>

            <div className="space-y-2">
              <Label className="text-xs">
                Valor <span className="text-destructive">*</span>
              </Label>
              <CurrencyInput
                value={valor}
                onMaskedChange={(v) => {
                  setValor(v);
                  clearField('valor');
                }}
                disabled={loading}
                className={fieldErrors.valor ? 'border-destructive' : undefined}
              />
              {fieldErrors.valor && <p className="text-xs text-destructive">{fieldErrors.valor}</p>}
            </div>
          </div>
        </SheetBody>

        <SheetFooter className="px-5 py-4 border-t border-border flex-row justify-between gap-2 sm:justify-between">
          <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={saving}>
            Cancelar
          </Button>
          <Button
            type="button"
            className="inline-flex gap-2"
            onClick={() => void handleSubmit()}
            disabled={saving || loading}
          >

            {saving ? (
              <>
                <Loader2 className="size-4 animate-spin shrink-0" />
                Transferindo…
              </>
            ) : (
              'Transferir'
            )}
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
