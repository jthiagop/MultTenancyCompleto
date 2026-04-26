import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { Loader2, UserPlus, Users, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DatePicker } from '@/components/ui/date-picker';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { useAppData } from '@/hooks/useAppData';
import { notify } from '@/lib/notify';
import { FielAvatarInput } from '@/pages/fieis/components/fiel-avatar-input';

const fielFormSchema = z.object({
  nome_completo: z
    .string()
    .min(1, 'O nome completo é obrigatório.')
    .max(255, 'Nome muito longo.'),
  data_nascimento: z
    .string()
    .min(1, 'A data de nascimento é obrigatória.')
    .regex(/^\d{4}-\d{2}-\d{2}$/, 'Data inválida.')
    .refine((s) => !Number.isNaN(new Date(`${s}T00:00:00`).getTime()), {
      message: 'Data inválida.',
    }),
  cpf: z.string().max(14, 'CPF inválido.').optional(),
  rg: z.string().max(20, 'RG inválido.').optional(),
  sexo: z.enum(['M', 'F', 'Outro']).optional(),
});

type FielFormValues = z.infer<typeof fielFormSchema>;

const FIEL_FORM_DEFAULTS: FielFormValues = {
  nome_completo: '',
  data_nascimento: '',
  cpf: '',
  rg: '',
  sexo: undefined,
};

export interface CadastroFielSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: () => void;
}

/**
 * Cadastro de fiel — avatar, nome e data (POST `relatorios/fieis`).
 */
export function CadastroFielSheet({ open, onOpenChange, onSaved }: CadastroFielSheetProps) {
  const { csrfToken } = useAppData();
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const form = useForm<FielFormValues>({
    resolver: zodResolver(fielFormSchema),
    defaultValues: FIEL_FORM_DEFAULTS,
  });

  useEffect(() => {
    if (!open) return;
    form.reset(FIEL_FORM_DEFAULTS);
    setAvatarFile(null);
  }, [open, form]);

  function handleClose() {
    onOpenChange(false);
  }

  const handleSubmit = form.handleSubmit(async (data) => {
    if (!csrfToken) {
      notify.reload();
      return;
    }

    setSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('_token', csrfToken);
      fd.append('nome_completo', data.nome_completo.trim());
      fd.append('data_nascimento', data.data_nascimento);
      if (data.cpf?.trim()) fd.append('cpf', data.cpf.trim());
      if (data.rg?.trim()) fd.append('rg', data.rg.trim());
      if (data.sexo) fd.append('sexo', data.sexo);
      if (avatarFile) fd.append('avatar', avatarFile);

      const res = await fetch('/relatorios/fieis', {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: fd,
      });

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
      };

      if (res.ok && result.success !== false) {
        notify.success('Fiel cadastrado!', result.message ?? 'Cadastro concluído com sucesso.');
        onOpenChange(false);
        onSaved?.();
      } else if (result.errors) {
        Object.entries(result.errors).forEach(([field, messages]) => {
          const key = field as keyof FielFormValues;
          if (key === 'nome_completo' || key === 'data_nascimento' || key === 'cpf' || key === 'rg' || key === 'sexo') {
            if (messages[0]) form.setError(key, { message: messages[0] });
          }
        });
        notify.error('Não foi possível salvar', 'Verifique os campos destacados.');
      } else {
        notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
      }
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  });

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        close={false}
        className="flex max-h-[calc(100vh-1.5rem)] w-full max-w-lg flex-col gap-0 rounded-xl border p-0 sm:max-w-3xl inset-y-3 end-3"
        aria-describedby={undefined}
      >
        <SheetHeader className="flex flex-row items-center justify-between px-5 py-3.5 border-b border-border shrink-0 space-y-0">
          <div className="flex items-center gap-2">
            <Users className="size-5 text-primary" aria-hidden />
            <SheetTitle className="text-base font-semibold">Cadastro de fiel</SheetTitle>
          </div>
          <Button
            type="button"
            variant="ghost"
            size="icon"
            onClick={handleClose}
            aria-label="Fechar"
            className="size-8"
            disabled={submitting}
          >
            <X className="size-4" />
          </Button>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
            <SheetBody className="p-0 flex-1 overflow-hidden bg-muted min-h-0">
              <div className="flex h-full min-h-[200px]">
                <div className="w-full flex-1 min-w-0">
                  <ScrollArea className="h-full">
                    <div className="p-5 space-y-4">
                      <Card className="rounded-md">
                        <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                          <CardTitle className="text-2sm flex items-center gap-1.5">
                            <UserPlus className="size-3.5 text-muted-foreground" />
                            Dados do fiel
                          </CardTitle>
                        </CardHeader>
                        <CardContent className="pt-4">
                          <div className="flex items-start gap-2 sm:gap-3">
                            <div className="flex shrink-0 justify-center sm:justify-start">
                              <FielAvatarInput
                                value={avatarFile}
                                onChange={setAvatarFile}
                                disabled={submitting}
                              />
                            </div>
                            <div className="flex-1 grid grid-cols-1 gap-3 sm:grid-cols-6">
                              <FormField
                                control={form.control}
                                name="nome_completo"
                                render={({ field }) => (
                                  <FormItem className="sm:col-span-4">
                                    <FormLabel className="text-xs">
                                      Nome completo <span className="text-destructive">*</span>
                                    </FormLabel>
                                    <FormControl>
                                      <Input {...field} placeholder="Nome completo" autoFocus disabled={submitting} />
                                    </FormControl>
                                    <FormMessage />
                                  </FormItem>
                                )}
                              />
                              <FormField
                                control={form.control}
                                name="data_nascimento"
                                render={({ field }) => (
                                  <FormItem className="sm:col-span-2">
                                    <FormLabel className="text-xs">
                                      Data de nascimento <span className="text-destructive">*</span>
                                    </FormLabel>
                                    <FormControl>
                                      <DatePicker
                                        value={field.value}
                                        onChange={field.onChange}
                                        placeholder="dd/mm/aaaa"
                                        disabled={submitting}
                                        aria-invalid={!!form.formState.errors.data_nascimento}
                                      />
                                    </FormControl>
                                    <FormMessage />
                                  </FormItem>
                                )}
                              />
                              <FormField
                                control={form.control}
                                name="cpf"
                                render={({ field }) => (
                                  <FormItem className="sm:col-span-2">
                                    <FormLabel className="text-xs">CPF</FormLabel>
                                    <FormControl>
                                      <Input {...field} placeholder="000.000.000-00" disabled={submitting} />
                                    </FormControl>
                                    <FormMessage />
                                  </FormItem>
                                )}
                              />
                              <FormField
                                control={form.control}
                                name="rg"
                                render={({ field }) => (
                                  <FormItem className="sm:col-span-2">
                                    <FormLabel className="text-xs">RG</FormLabel>
                                    <FormControl>
                                      <Input {...field} placeholder="RG" disabled={submitting} />
                                    </FormControl>
                                    <FormMessage />
                                  </FormItem>
                                )}
                              />
                              <FormField
                                control={form.control}
                                name="sexo"
                                render={({ field }) => (
                                  <FormItem className="sm:col-span-2">
                                    <FormLabel className="text-xs">Sexo</FormLabel>
                                    <Select
                                      value={field.value}
                                      onValueChange={field.onChange}
                                      disabled={submitting}
                                    >
                                      <FormControl>
                                        <SelectTrigger>
                                          <SelectValue placeholder="Selecione" />
                                        </SelectTrigger>
                                      </FormControl>
                                      <SelectContent>
                                        <SelectItem value="M">Masculino</SelectItem>
                                        <SelectItem value="F">Feminino</SelectItem>
                                        <SelectItem value="Outro">Outro</SelectItem>
                                      </SelectContent>
                                    </Select>
                                    <FormMessage />
                                  </FormItem>
                                )}
                              />
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    </div>
                  </ScrollArea>
                </div>
              </div>
            </SheetBody>

            <SheetFooter className="flex-row justify-between border-t border-border px-5 py-3.5 shrink-0 gap-2 sm:space-x-0">
              <Button type="button" variant="ghost" onClick={handleClose} disabled={submitting}>
                Cancelar
              </Button>
              <Button
                type="submit"
                className="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white border-0"
                disabled={submitting}
              >
                {submitting ? (
                  <>
                    <Loader2 className="size-4 animate-spin shrink-0" />
                    Salvando…
                  </>
                ) : (
                  'Salvar'
                )}
              </Button>
            </SheetFooter>
          </form>
        </Form>
      </SheetContent>
    </Sheet>
  );
}
