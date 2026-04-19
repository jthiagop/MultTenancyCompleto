import { useEffect, useState } from 'react';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { DatePicker } from '@/components/ui/date-picker';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { Loader2 } from 'lucide-react';
import { notify } from '@/lib/notify';

interface FormState {
  codigo_sepultura: string;
  localizacao: string;
  tipo: string;
  tamanho: string;
  data_aquisicao: string;
  status: 'Disponível' | 'Ocupada' | 'Reservada' | 'Manutenção';
}

const EMPTY_FORM: FormState = {
  codigo_sepultura: '',
  localizacao: '',
  tipo: '',
  tamanho: '',
  data_aquisicao: '',
  status: 'Disponível',
};

interface TumuloFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: () => void;
  editingId?: number | null;
}

export function TumuloFormSheet({ open, onOpenChange, onSaved, editingId }: TumuloFormSheetProps) {
  const { csrfToken } = useAppData();
  const isEditing = !!editingId;
  const [form, setForm] = useState<FormState>(EMPTY_FORM);
  const [submitting, setSubmitting] = useState(false);
  const [loadingData, setLoadingData] = useState(false);

  useEffect(() => {
    if (!open) return;
    if (editingId) {
      setLoadingData(true);
      fetch(`/cemiterio/tumulos/${editingId}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then((r) => r.json())
        .then((json) => {
          if (!json.success) return;
          const d = json.data;
          setForm({
            codigo_sepultura: d.codigo_sepultura ?? '',
            localizacao:      d.localizacao ?? '',
            tipo:             d.tipo ?? '',
            tamanho:          d.tamanho ?? '',
            data_aquisicao:   d.data_aquisicao ?? '',
            status:           d.status ?? 'Disponível',
          });
        })
        .catch(() => notify.error('Erro', 'Não foi possível carregar os dados.'))
        .finally(() => setLoadingData(false));
    } else {
      setForm(EMPTY_FORM);
    }
  }, [open, editingId]);

  function set<K extends keyof FormState>(field: K, value: FormState[K]) {
    setForm((prev) => ({ ...prev, [field]: value }));
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();

    if (!form.codigo_sepultura.trim()) {
      notify.warning('Campo obrigatório', 'Informe o código da sepultura.');
      return;
    }
    if (!csrfToken) {
      notify.reload();
      return;
    }

    setSubmitting(true);
    try {
      const payload = {
        codigo_sepultura: form.codigo_sepultura.trim(),
        localizacao:      form.localizacao.trim() || null,
        tipo:             form.tipo.trim() || null,
        tamanho:          form.tamanho.trim() || null,
        data_aquisicao:   form.data_aquisicao || null,
        status:           form.status,
        ...(isEditing ? { _method: 'PUT' } : {}),
      };

      const url = isEditing ? `/cemiterio/tumulos/${editingId}` : '/cemiterio/tumulos';

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });

      if (!res.headers.get('content-type')?.includes('application/json')) {
        notify.error('Erro inesperado', 'Resposta inválida do servidor.');
        return;
      }

      const result = await res.json();

      if (res.ok && result.success) {
        notify.success(
          isEditing ? 'Túmulo atualizado!' : 'Túmulo cadastrado!',
          `${form.codigo_sepultura.trim()} foi ${isEditing ? 'atualizado' : 'cadastrado'} com sucesso.`,
        );
        onOpenChange(false);
        onSaved?.();
      } else {
        notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
        if (result.errors) {
          notify.validationErrors(result.errors);
        }
      }
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        overlayClassName="z-[55]"
        className={cn(
          'z-60 gap-0 lg:w-160 sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '**:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5',
        )}
      >
        <SheetHeader className="border-b py-3.5 px-5 border-border space-y-0">
          <SheetTitle className="font-medium">
            {isEditing ? 'Editar Túmulo' : 'Novo Túmulo'}
          </SheetTitle>
        </SheetHeader>

        <form id="tumulo-form" onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
          <SheetBody className="grow p-0 flex flex-col min-h-0">
            {loadingData ? (
              <div className="flex flex-1 items-center justify-center py-20">
                <Loader2 className="size-6 animate-spin text-muted-foreground" />
              </div>
            ) : (
              <>
                {/* Action bar */}
                <div className="flex justify-end gap-2 border-b border-border p-5">
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => onOpenChange(false)}
                    disabled={submitting}
                  >
                    Cancelar
                  </Button>
                  <Button
                    type="submit"
                    className="bg-blue-600 hover:bg-blue-700 text-white border-0"
                    disabled={submitting}
                  >
                    {submitting ? (
                      <>
                        <Loader2 className="size-4 animate-spin" />
                        Salvando…
                      </>
                    ) : (
                      'Salvar'
                    )}
                  </Button>
                </div>

                <ScrollArea className="flex-1 px-5 py-5">
                  <div className="space-y-5">

                    {/* Identificação */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm">Identificação</CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4">
                        <div className="grid grid-cols-12 gap-3">

                          <div className="col-span-12 sm:col-span-5 space-y-2">
                            <Label className="text-xs">
                              Código <span className="text-destructive">*</span>
                            </Label>
                            <Input
                              value={form.codigo_sepultura}
                              onChange={(e) => set('codigo_sepultura', e.target.value)}
                              placeholder="Ex: A-01"
                              autoFocus
                            />
                          </div>

                          <div className="col-span-12 sm:col-span-7 space-y-2">
                            <Label className="text-xs">Localização</Label>
                            <Input
                              value={form.localizacao}
                              onChange={(e) => set('localizacao', e.target.value)}
                              placeholder="Ex: Quadra A, Rua 1, Nº 5"
                            />
                          </div>

                          <div className="col-span-12 sm:col-span-5 space-y-2">
                            <Label className="text-xs">Tipo</Label>
                            <select
                              value={form.tipo}
                              onChange={(e) => set('tipo', e.target.value)}
                              className={cn(
                                'flex w-full bg-background border border-input rounded-md shadow-xs shadow-black/5',
                                'h-8.5 px-3 text-2sm text-foreground',
                                'focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 focus-visible:border-ring',
                                !form.tipo && 'text-muted-foreground/80',
                              )}
                            >
                              <option value="">Selecione o tipo</option>
                              <option value="Gaveta">Gaveta</option>
                              <option value="Carneiro">Carneiro</option>
                              <option value="Jazigo">Jazigo</option>
                              <option value="Túmulo">Túmulo</option>
                              <option value="Cova">Cova</option>
                              <option value="Cripta">Cripta</option>
                              <option value="Columbário">Columbário</option>
                              <option value="Mausoléu">Mausoléu</option>
                            </select>
                          </div>

                          <div className="col-span-12 sm:col-span-4 space-y-2">
                            <Label className="text-xs">Tamanho</Label>
                            <Input
                              value={form.tamanho}
                              onChange={(e) => set('tamanho', e.target.value)}
                              placeholder="Ex: 2x1"
                            />
                          </div>

                          <div className="col-span-12 sm:col-span-3 space-y-2">
                            <Label className="text-xs">Status</Label>
                            <Select
                              value={form.status}
                              onValueChange={(v) => set('status', v as FormState['status'])}
                            >
                              <SelectTrigger>
                                <SelectValue />
                              </SelectTrigger>
                              <SelectContent>
                                <SelectItem value="Disponível">Disponível</SelectItem>
                                <SelectItem value="Ocupada">Ocupada</SelectItem>
                                <SelectItem value="Reservada">Reservada</SelectItem>
                                <SelectItem value="Manutenção">Manutenção</SelectItem>
                              </SelectContent>
                            </Select>
                          </div>

                        </div>
                      </CardContent>
                    </Card>

                    {/* Aquisição */}
                    <Card className="rounded-md">
                      <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                        <CardTitle className="text-2sm">Aquisição</CardTitle>
                      </CardHeader>
                      <CardContent className="pt-4">
                        <div className="grid grid-cols-12 gap-3">

                          <div className="col-span-12 sm:col-span-5 space-y-2">
                            <Label className="text-xs">Data de Aquisição</Label>
                            <DatePicker
                              value={form.data_aquisicao}
                              onChange={(iso) => set('data_aquisicao', iso)}
                              placeholder="DD/MM/AAAA"
                              className="w-full"
                            />
                          </div>

                        </div>
                      </CardContent>
                    </Card>

                  </div>
                </ScrollArea>
              </>
            )}
          </SheetBody>

          <SheetFooter className="flex-row justify-end border-t border-border px-5 py-4 gap-2">
            <Button
              type="button"
              variant="outline"
              onClick={() => onOpenChange(false)}
              disabled={submitting}
            >
              Cancelar
            </Button>
            <Button
              type="submit"
              className="bg-blue-600 hover:bg-blue-700 text-white border-0"
              disabled={submitting}
            >
              {submitting ? (
                <>
                  <Loader2 className="size-4 animate-spin" />
                  Salvando…
                </>
              ) : (
                isEditing ? 'Salvar Alterações' : 'Salvar Túmulo'
              )}
            </Button>
          </SheetFooter>
        </form>
      </SheetContent>
    </Sheet>
  );
}
