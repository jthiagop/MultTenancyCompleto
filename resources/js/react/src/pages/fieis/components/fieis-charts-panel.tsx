import { useEffect, useMemo, useState } from 'react';
import { Eye, EyeOff, PieChart as PieChartIcon } from 'lucide-react';
import { Label, Pie, PieChart, Sector } from 'recharts';
import type { PieSectorShapeProps } from 'recharts/types/polar/Pie';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartTooltip, ChartTooltipContent, type ChartConfig } from '@/components/ui/chart';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';

const CHARTS_VISIBILITY_KEY = 'fieis:charts-panel:visible';

function readInitialVisibility(): boolean {
  if (typeof window === 'undefined') return true;
  try {
    const stored = window.localStorage.getItem(CHARTS_VISIBILITY_KEY);
    if (stored === null) return true;
    return stored === '1';
  } catch {
    return true;
  }
}

function persistVisibility(visible: boolean) {
  if (typeof window === 'undefined') return;
  try {
    window.localStorage.setItem(CHARTS_VISIBILITY_KEY, visible ? '1' : '0');
  } catch {
    // silencioso: storage indisponível (modo privado, p.ex.)
  }
}

type ChartSlice = {
  key: string;
  label: string;
  value: number;
  fill: string;
  extraInfo?: string;
};

type FieisChartsApiResponse = {
  success?: boolean;
  data?: {
    faixas_etarias?: {
      labels?: string[];
      values?: number[];
    };
    estados_civis?: {
      labels?: string[];
      values?: number[];
    };
  };
};

type InteractivePieCardProps = {
  id: string;
  title: string;
  description?: string;
  selectLabel: string;
  centerCaption: string;
  data: ChartSlice[];
};

const SLICE_COLORS = [
  'var(--chart-1)',
  'var(--chart-2)',
  'var(--chart-3)',
  'var(--chart-4)',
  'var(--chart-5)',
] as const;

function toSafeKey(label: string, idx: number) {
  const normalized = label
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
  return normalized || `item-${idx + 1}`;
}

function formatDateBR(date: Date): string {
  return new Intl.DateTimeFormat('pt-BR').format(date);
}

function yearsAgo(base: Date, years: number): Date {
  const copy = new Date(base);
  copy.setFullYear(copy.getFullYear() - years);
  return copy;
}

function getFaixaEtariaBaseDateInfo(baseDate: Date): Record<string, string> {
  const today = formatDateBR(baseDate);
  const y17 = formatDateBR(yearsAgo(baseDate, 17));
  const y18 = formatDateBR(yearsAgo(baseDate, 18));
  const y29 = formatDateBR(yearsAgo(baseDate, 29));
  const y30 = formatDateBR(yearsAgo(baseDate, 30));
  const y44 = formatDateBR(yearsAgo(baseDate, 44));
  const y45 = formatDateBR(yearsAgo(baseDate, 45));
  const y59 = formatDateBR(yearsAgo(baseDate, 59));
  const y60 = formatDateBR(yearsAgo(baseDate, 60));
  const y74 = formatDateBR(yearsAgo(baseDate, 74));
  const y75 = formatDateBR(yearsAgo(baseDate, 75));

  return {
    '0-17': `Nascimento entre ${y17} e ${today}`,
    '18-29': `Nascimento entre ${y29} e ${y18}`,
    '30-44': `Nascimento entre ${y44} e ${y30}`,
    '45-59': `Nascimento entre ${y59} e ${y45}`,
    '60-74': `Nascimento entre ${y74} e ${y60}`,
    '75+': `Nascimento até ${y75}`,
  };
}

function useInteractivePieSelection(data: ChartSlice[]) {
  const [activeKey, setActiveKey] = useState<string>(data[0]?.key ?? '');

  useEffect(() => {
    if (!data.length) {
      setActiveKey('');
      return;
    }
    if (!data.some((item) => item.key === activeKey)) {
      setActiveKey(data[0].key);
    }
  }, [activeKey, data]);

  const activeIndex = useMemo(
    () => Math.max(0, data.findIndex((item) => item.key === activeKey)),
    [activeKey, data],
  );

  return { activeKey, setActiveKey, activeIndex };
}

function InteractivePieCard({ id, title, description, selectLabel, centerCaption, data }: InteractivePieCardProps) {
  const total = useMemo(() => data.reduce((acc, item) => acc + item.value, 0), [data]);
  const { activeKey, setActiveKey, activeIndex } = useInteractivePieSelection(data);
  const activeItem = data[activeIndex];

  const chartConfig = useMemo<ChartConfig>(() => {
    return data.reduce(
      (acc, item) => {
        acc[item.key] = {
          label: item.label,
          color: item.fill,
        };
        return acc;
      },
      {
        quantidade: {
          label: centerCaption,
        },
      } as ChartConfig,
    );
  }, [centerCaption, data]);

  const renderPieShape = ({ index, outerRadius = 0, ...props }: PieSectorShapeProps) => {
    if (index === activeIndex) {
      return (
        <g>
          <Sector {...props} outerRadius={outerRadius + 8} />
          <Sector {...props} outerRadius={outerRadius + 22} innerRadius={outerRadius + 10} />
        </g>
      );
    }

    return <Sector {...props} outerRadius={outerRadius} />;
  };

  return (
    <Card data-chart={id} className="min-h-[360px]">
      <CardHeader className="gap-2 pb-2">
        <div className="space-y-1">
          <CardTitle>{title}</CardTitle>
          {description ? <CardDescription>{description}</CardDescription> : null}
        </div>
        <Select value={activeKey} onValueChange={setActiveKey}>
          <SelectTrigger className="h-8 w-full sm:w-[240px]" aria-label={selectLabel}>
            <SelectValue placeholder={selectLabel} />
          </SelectTrigger>
          <SelectContent align="end">
            {data.map((item) => (
              <SelectItem key={item.key} value={item.key}>
                <div className="flex items-center gap-2">
                  <span className="size-2.5 rounded-full" style={{ backgroundColor: item.fill }} />
                  <span>{item.label}</span>
                </div>
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </CardHeader>

      <CardContent className="grid gap-4 pt-2 lg:grid-cols-[minmax(0,1fr)_220px]">
        <ChartContainer id={id} config={chartConfig} className="mx-auto aspect-square w-full max-w-[320px]">
          <PieChart>
            <ChartTooltip
              cursor={false}
              content={
                <ChartTooltipContent
                  formatter={(value, _name, item) => {
                    const numericValue = Number(value ?? 0);
                    const pct = total > 0 ? (numericValue / total) * 100 : 0;
                    const extraInfo = typeof item.payload?.extraInfo === 'string' ? item.payload.extraInfo : '';
                    return (
                      <div className="space-y-0.5">
                        <div className="text-xs font-medium">
                          {numericValue} ({pct.toFixed(2)}%)
                        </div>
                        {extraInfo ? <div className="text-[11px] text-muted-foreground">{extraInfo}</div> : null}
                      </div>
                    );
                  }}
                />
              }
            />
            <Pie
              data={data}
              dataKey="value"
              nameKey="label"
              innerRadius={68}
              strokeWidth={4}
              shape={renderPieShape}
              activeIndex={activeIndex}
            >
              <Label
                content={({ viewBox }) => {
                  if (!viewBox || !('cx' in viewBox) || !('cy' in viewBox)) {
                    return null;
                  }

                  return (
                    <text x={viewBox.cx} y={viewBox.cy} textAnchor="middle" dominantBaseline="middle">
                      <tspan x={viewBox.cx} y={viewBox.cy} className="fill-foreground text-2xl font-bold">
                        {activeItem?.value?.toLocaleString('pt-BR') ?? 0}
                      </tspan>
                      <tspan x={viewBox.cx} y={(viewBox.cy || 0) + 20} className="fill-muted-foreground text-xs">
                        {centerCaption}
                      </tspan>
                    </text>
                  );
                }}
              />
            </Pie>
          </PieChart>
        </ChartContainer>

        <div className="space-y-2">
          {data.map((item) => {
            const pct = total > 0 ? (item.value / total) * 100 : 0;
            return (
              <button
                key={item.key}
                type="button"
                onClick={() => setActiveKey(item.key)}
                className="flex w-full items-start justify-between gap-2 rounded-md border px-3 py-2 text-left hover:bg-muted/50"
              >
                <div className="min-w-0">
                  <div className="flex items-center gap-2">
                    <span className="size-2.5 rounded-full shrink-0" style={{ backgroundColor: item.fill }} />
                    <span className="truncate text-sm font-medium">{item.label}</span>
                  </div>
                  {item.extraInfo ? <p className="mt-1 text-xs text-muted-foreground">{item.extraInfo}</p> : null}
                </div>
                <span className="shrink-0 text-xs font-semibold tabular-nums">{pct.toFixed(2)}%</span>
              </button>
            );
          })}
        </div>
      </CardContent>
    </Card>
  );
}

function buildSlices(labels: string[], values: number[], extras?: Record<string, string>): ChartSlice[] {
  return labels.map((label, index) => ({
    key: toSafeKey(label, index),
    label,
    value: Number(values[index] ?? 0),
    fill: SLICE_COLORS[index % SLICE_COLORS.length],
    extraInfo: extras?.[label],
  }));
}

export function FieisChartsPanel({ refreshKey }: { refreshKey?: number }) {
  const [visible, setVisible] = useState<boolean>(readInitialVisibility);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [faixaEtaria, setFaixaEtaria] = useState<ChartSlice[]>([]);
  const [estadoCivil, setEstadoCivil] = useState<ChartSlice[]>([]);

  function toggleVisible() {
    setVisible((v) => {
      const next = !v;
      persistVisibility(next);
      return next;
    });
  }

  useEffect(() => {
    if (!visible) return;

    const controller = new AbortController();
    let isMounted = true;

    const load = async () => {
      setLoading(true);
      setError(null);

      try {
        const response = await fetch('/relatorios/fieis/charts/data', {
          method: 'GET',
          signal: controller.signal,
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (!response.ok) {
          throw new Error(`Falha ao carregar gráficos (${response.status}).`);
        }

        const payload = (await response.json()) as FieisChartsApiResponse;
        const faixas = payload.data?.faixas_etarias;
        const estados = payload.data?.estados_civis;
        const faixaBaseDates = getFaixaEtariaBaseDateInfo(new Date());

        if (!isMounted) return;

        setFaixaEtaria(buildSlices(faixas?.labels ?? [], faixas?.values ?? [], faixaBaseDates));
        setEstadoCivil(buildSlices(estados?.labels ?? [], estados?.values ?? []));
      } catch (err) {
        if (controller.signal.aborted) return;
        const message = err instanceof Error ? err.message : 'Erro ao carregar gráficos de fiéis.';
        if (isMounted) setError(message);
      } finally {
        if (isMounted) setLoading(false);
      }
    };

    void load();

    return () => {
      isMounted = false;
      controller.abort();
    };
  }, [refreshKey, visible]);

  return (
    <section aria-label="Indicadores de fiéis" className="space-y-3">
      <div className="flex items-center justify-between gap-2">
        <div className="flex items-center gap-2">
          <PieChartIcon className="size-4 text-muted-foreground" aria-hidden />
          <h2 className="text-sm font-semibold text-foreground">Indicadores de fiéis</h2>
          {!visible && (
            <span className="text-xs text-muted-foreground">(ocultos)</span>
          )}
        </div>
        <Button
          type="button"
          variant="ghost"
          size="sm"
          onClick={toggleVisible}
          className="h-8 gap-1.5 text-xs text-muted-foreground hover:text-foreground"
          aria-pressed={visible}
          aria-label={visible ? 'Ocultar gráficos' : 'Mostrar gráficos'}
          title={visible ? 'Ocultar gráficos' : 'Mostrar gráficos'}
        >
          {visible ? <EyeOff className="size-3.5" /> : <Eye className="size-3.5" />}
          {visible ? 'Ocultar' : 'Mostrar'}
        </Button>
      </div>

      {visible && (
        loading ? (
          <div className="grid gap-4 lg:grid-cols-2">
            <Skeleton className="h-[360px] w-full rounded-xl" />
            <Skeleton className="h-[360px] w-full rounded-xl" />
          </div>
        ) : error ? (
          <Card>
            <CardHeader>
              <CardTitle>Gráficos de fiéis</CardTitle>
              <CardDescription>{error}</CardDescription>
            </CardHeader>
          </Card>
        ) : (
          <div className="grid gap-4 lg:grid-cols-2">
            <InteractivePieCard
              id="fieis-faixa-etaria-chart"
              title="Fiéis por faixa etária"
              description="Faixas calculadas com base na data de nascimento."
              selectLabel="Selecionar faixa etária"
              centerCaption="fiéis"
              data={faixaEtaria}
            />

            <InteractivePieCard
              id="fieis-estado-civil-chart"
              title="Fiéis por estado civil"
              description="Distribuição conforme cadastro complementar."
              selectLabel="Selecionar estado civil"
              centerCaption="fiéis"
              data={estadoCivil}
            />
          </div>
        )
      )}
    </section>
  );
}
