import { useEffect, useState } from 'react';
import { Clock3, Plus, Save, Trash2 } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { notify } from '@/lib/notify';
import { cn } from '@/lib/utils';
import { useAppData } from '@/hooks/useAppData';

const DIAS_SEMANA = [
  { value: 'domingo', label: 'Domingo' },
  { value: 'segunda', label: 'Segunda' },
  { value: 'terca', label: 'Terça' },
  { value: 'quarta', label: 'Quarta' },
  { value: 'quinta', label: 'Quinta' },
  { value: 'sexta', label: 'Sexta' },
  { value: 'sabado', label: 'Sábado' },
] as const;

type DiaSemana = (typeof DIAS_SEMANA)[number]['value'];

interface HorarioItem {
  id: string;
  value: string;
}

interface DaySchedule {
  id: string;
  dia_semana: DiaSemana | '';
  horarios: HorarioItem[];
}

function parseTime(value: string) {
  const match = /^(\d{2}):(\d{2})$/.exec(value.trim());
  if (!match) return null;
  const hour = Number(match[1]);
  const minute = Number(match[2]);
  if (!Number.isInteger(hour) || !Number.isInteger(minute)) return null;
  if (hour < 0 || hour > 23 || minute < 0 || minute > 59) return null;
  return { hour, minute };
}

function isValidTime(value: string) {
  return parseTime(value) !== null;
}

function formatTime(hour: number, minute: number) {
  return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
}

interface TimePopoverInputProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  className?: string;
}

function TimePopoverInput({ value, onChange, placeholder = '00:00', className }: TimePopoverInputProps) {
  const parsed = parseTime(value) ?? { hour: 0, minute: 0 };
  const [open, setOpen] = useState(false);
  const [draftHour, setDraftHour] = useState(parsed.hour);
  const [draftMinute, setDraftMinute] = useState(parsed.minute);

  function handleOpenChange(next: boolean) {
    if (next) {
      const current = parseTime(value) ?? { hour: 0, minute: 0 };
      setDraftHour(current.hour);
      setDraftMinute(current.minute);
    }
    setOpen(next);
  }

  const display = isValidTime(value) ? value : '';

  return (
    <Popover open={open} onOpenChange={handleOpenChange}>
      <PopoverTrigger asChild>
        <Button
          type="button"
          variant="outline"
          className={cn('w-40 justify-start font-normal', !display && 'text-muted-foreground', className)}
        >
          <Clock3 className="size-4" />
          {display || placeholder}
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-[220px] p-3 z-[70]" align="start">
        <div className="space-y-3">
          <div className="grid grid-cols-2 gap-2">
            <div>
              <p className="text-xs text-muted-foreground mb-1">Hora</p>
              <Select value={String(draftHour)} onValueChange={(v) => setDraftHour(Number(v))}>
                <SelectTrigger className="h-8">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent className="z-[75] max-h-64">
                  {Array.from({ length: 24 }, (_, h) => (
                    <SelectItem key={h} value={String(h)}>
                      {String(h).padStart(2, '0')}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div>
              <p className="text-xs text-muted-foreground mb-1">Minuto</p>
              <Select value={String(draftMinute)} onValueChange={(v) => setDraftMinute(Number(v))}>
                <SelectTrigger className="h-8">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent className="z-[75] max-h-64">
                  {Array.from({ length: 60 }, (_, m) => (
                    <SelectItem key={m} value={String(m)}>
                      {String(m).padStart(2, '0')}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>

          <div className="flex items-center justify-between">
            <Button type="button" size="sm" variant="ghost" onClick={() => onChange('')}>
              Limpar
            </Button>
            <Button
              type="button"
              size="sm"
              onClick={() => {
                onChange(formatTime(draftHour, draftMinute));
                setOpen(false);
              }}
            >
              Aplicar
            </Button>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  );
}

function makeHorarioId() {
  return Math.random().toString(36).slice(2);
}

function createDay(): DaySchedule {
  return {
    id: Math.random().toString(36).slice(2),
    dia_semana: '',
    horarios: [{ id: makeHorarioId(), value: '' }],
  };
}

export function MassTimesTab() {
  const { csrfToken, companyId } = useAppData();
  const [submitting, setSubmitting] = useState(false);
  const [loading, setLoading] = useState(true);
  const [intervaloPadrao, setIntervaloPadrao] = useState('01:30');
  const [days, setDays] = useState<DaySchedule[]>([createDay()]);
  const usedDays = days.map((d) => d.dia_semana).filter((v): v is DiaSemana => v !== '');

  useEffect(() => {
    setLoading(true);
    fetch('/api/cadastros/company/active', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((res) => (res.ok ? res.json() : null))
      .then((json) => {
        if (!json?.data) return;
        const { horarios_missas, intervalo_padrao } = json.data as {
          horarios_missas: Record<string, string[]>;
          intervalo_padrao: string;
        };
        if (intervalo_padrao) setIntervaloPadrao(intervalo_padrao);
        const entries = Object.entries(horarios_missas ?? {});
        if (entries.length > 0) {
          setDays(
            entries.map(([dia, horarios]) => ({
              id: makeHorarioId(),
              dia_semana: dia as DiaSemana,
              horarios: (horarios as string[]).map((h) => ({ id: makeHorarioId(), value: h })),
            }))
          );
        }
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  function addDay() {
    if (days.length >= DIAS_SEMANA.length) {
      notify.warning('Limite atingido', 'Você já adicionou todos os dias da semana.');
      return;
    }
    setDays((prev) => [...prev, createDay()]);
  }

  function removeDay(dayId: string) {
    setDays((prev) => prev.filter((d) => d.id !== dayId));
  }

  function updateDay(dayId: string, dia_semana: DiaSemana | '') {
    if (!dia_semana) {
      setDays((prev) => prev.map((d) => (d.id === dayId ? { ...d, dia_semana } : d)));
      return;
    }

    const duplicated = days.some((d) => d.id !== dayId && d.dia_semana === dia_semana);
    if (duplicated) {
      notify.warning('Dia já selecionado', 'Cada dia da semana pode ser adicionado apenas uma vez.');
      return;
    }

    setDays((prev) => prev.map((d) => (d.id === dayId ? { ...d, dia_semana } : d)));
  }

  function addHorario(dayId: string) {
    setDays((prev) =>
      prev.map((d) =>
        d.id === dayId ? { ...d, horarios: [...d.horarios, { id: makeHorarioId(), value: '' }] } : d
      )
    );
  }

  function removeHorario(dayId: string, horarioId: string) {
    setDays((prev) =>
      prev.map((d) => {
        if (d.id !== dayId) return d;
        const next = d.horarios.filter((h) => h.id !== horarioId);
        return { ...d, horarios: next.length > 0 ? next : [{ id: makeHorarioId(), value: '' }] };
      })
    );
  }

  function updateHorario(dayId: string, horarioId: string, value: string) {
    setDays((prev) =>
      prev.map((d) => {
        if (d.id !== dayId) return d;
        return { ...d, horarios: d.horarios.map((h) => (h.id === horarioId ? { ...h, value } : h)) };
      })
    );
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();

    if (!companyId) {
      notify.error('Organismo não selecionado', 'Defina uma empresa ativa para salvar os horários.');
      return;
    }

    if (!csrfToken) {
      notify.reload();
      return;
    }

    if (!isValidTime(intervaloPadrao)) {
      notify.warning('Intervalo inválido', 'Use um horário entre 00:00 e 23:59.');
      return;
    }

    const validDays = days
      .map((d) => ({
        dia_semana: d.dia_semana,
        horarios: d.horarios.map((h) => h.value.trim()).filter((h) => h.length > 0 && isValidTime(h)),
      }))
      .filter((d) => d.dia_semana && d.horarios.length > 0);

    const usedOnSubmit = validDays.map((d) => d.dia_semana);
    const hasDuplicatedDay = new Set(usedOnSubmit).size !== usedOnSubmit.length;
    if (hasDuplicatedDay) {
      notify.warning('Dias duplicados', 'Não é permitido repetir domingo ou qualquer outro dia.');
      return;
    }

    const hasInvalidHorario = days.some((d) => d.horarios.some((h) => h.value.trim().length > 0 && !isValidTime(h.value)));
    if (hasInvalidHorario) {
      notify.warning('Horário inválido', 'Cada horário deve estar entre 00:00 e 23:59.');
      return;
    }

    setSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('_method', 'PUT');
      fd.append('updating_horarios_missas', '1');
      fd.append('intervalo_padrao', intervaloPadrao);

      if (validDays.length === 0) {
        fd.append('dias', '');
      } else {
        validDays.forEach((day, dayIndex) => {
          fd.append(`dias[${dayIndex}][dia_semana]`, day.dia_semana);
          day.horarios.forEach((horario, horarioIndex) => {
            fd.append(`dias[${dayIndex}][horarios][${horarioIndex}][horario]`, horario);
          });
        });
      }

      const res = await fetch('/company', {
        method: 'POST',
        headers: {
          Accept: 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: fd,
      });

      if (!res.ok) {
        notify.error('Falha ao salvar', 'Não foi possível salvar os horários de missas.');
        return;
      }

      notify.success('Horários salvos!', 'Os horários de missas foram atualizados com sucesso.');
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <Clock3 className="size-4 text-primary" />
          Horários de Missas
        </CardTitle>
        <CardDescription>Gerencie os dias e horários de celebração da fraternidade.</CardDescription>
      </CardHeader>

      <CardContent>
        {loading ? (
          <div className="flex items-center justify-center py-10 text-muted-foreground text-sm">
            Carregando horários...
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="space-y-3">
              {days.map((day) => (
                <div key={day.id} className="rounded-lg border border-dashed border-border p-4 space-y-3">
                  <div className="flex items-start gap-3">
                    <div className="w-52">
                      <Select value={day.dia_semana} onValueChange={(v) => updateDay(day.id, v as DiaSemana)}>
                        <SelectTrigger>
                          <SelectValue placeholder="Selecione o dia" />
                        </SelectTrigger>
                        <SelectContent>
                          {DIAS_SEMANA.map((option) => (
                            <SelectItem
                              key={option.value}
                              value={option.value}
                              disabled={usedDays.includes(option.value) && day.dia_semana !== option.value}
                            >
                              {option.label}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>

                    <div className="flex-1 space-y-2">
                      {day.horarios.map((horario) => (
                        <div key={horario.id} className="flex items-center gap-2">
                          <TimePopoverInput
                            value={horario.value}
                            onChange={(v) => updateHorario(day.id, horario.id, v)}
                            placeholder="00:00"
                            className="w-40"
                          />
                          <Button type="button" variant="outline" mode="icon" onClick={() => removeHorario(day.id, horario.id)}>
                            <Trash2 className="size-4" />
                          </Button>
                        </div>
                      ))}

                      <Button type="button" size="sm" variant="outline" onClick={() => addHorario(day.id)}>
                        <Plus className="size-4" /> Adicionar horário
                      </Button>
                    </div>

                    <Button type="button" variant="outline" mode="icon" onClick={() => removeDay(day.id)}>
                      <Trash2 className="size-4" />
                    </Button>
                  </div>
                </div>
              ))}
            </div>

            <div className="flex items-center justify-between border-t pt-4">
              <Button type="button" variant="outline" onClick={addDay}>
                <Plus className="size-4" /> Adicionar Dia
              </Button>

              <div className="flex items-center gap-4">
                <div className="flex items-center gap-2">
                  <span className="text-sm text-muted-foreground">Intervalo:</span>
                  <TimePopoverInput
                    value={intervaloPadrao}
                    onChange={setIntervaloPadrao}
                    placeholder="00:00"
                    className="w-32"
                  />
                </div>

                <Button type="submit" disabled={submitting}>
                  <Save className="size-4" /> {submitting ? 'Salvando...' : 'Salvar Horários'}
                </Button>
              </div>
            </div>
          </form>
        )}
      </CardContent>
    </Card>
  );
}
