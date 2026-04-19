import { useEffect, useState } from 'react';
import { ApexOptions } from 'apexcharts';
import ApexChart from 'react-apexcharts';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';

const dummyChartData: number[] = [
  58, 64, 52, 45, 42, 38, 45, 53, 56, 65, 75, 85,
];

const categories: string[] = [
  'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
  'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez',
];

const EarningsChart = () => {
  const [chartData, setChartData] = useState<number[]>(dummyChartData);

  useEffect(() => {
    setChartData(dummyChartData);
  }, []);

  const options: ApexOptions = {
    series: [
      {
        name: 'Receitas',
        data: chartData ?? [],
      },
    ],
    chart: {
      height: 250,
      type: 'area',
      toolbar: {
        show: false,
      },
    },
    dataLabels: {
      enabled: false,
    },
    legend: {
      show: false,
    },
    stroke: {
      curve: 'smooth',
      show: true,
      width: 3,
      colors: ['#3b82f6'],
    },
    xaxis: {
      categories: categories,
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
      labels: {
        style: {
          colors: 'var(--color-secondary-foreground)',
          fontSize: '12px',
        },
      },
      crosshairs: {
        position: 'front',
        stroke: {
          color: '#3b82f6',
          width: 1,
          dashArray: 3,
        },
      },
      tooltip: {
        enabled: false,
        formatter: undefined,
        offsetY: 0,
        style: {
          fontSize: '12px',
        },
      },
    },
    yaxis: {
      min: 0,
      max: 100,
      tickAmount: 5,
      axisTicks: {
        show: false,
      },
      labels: {
        style: {
          colors: 'var(--color-secondary-foreground)',
          fontSize: '12px',
        },
        formatter: (value) => `R$${value}K`,
      },
    },
    tooltip: {
      enabled: true,
      custom({ series, seriesIndex, dataPointIndex, w }) {
        const number = parseInt(series[seriesIndex][dataPointIndex]) * 1000;
        const month = w.globals.seriesX[seriesIndex][dataPointIndex];
        const monthName = categories[month];

        const formatter = new Intl.NumberFormat('pt-BR', {
          style: 'currency',
          currency: 'BRL',
        });

        const formattedNumber = formatter.format(number);

        return `
          <div class="flex flex-col gap-2 p-3.5">
            <div class="font-medium text-sm text-secondary-foreground">${monthName}, 2025 Receitas</div>
            <div class="flex items-center gap-1.5">
              <div class="font-semibold text-base text-mono">${formattedNumber}</div>
              <span class="rounded-full border border-green-200 font-medium dark:border-green-850 text-success-700 bg-green-100 dark:bg-green-950/30 text-[11px] leading-none px-1.25 py-1">+24%</span>
            </div>
          </div>
        `;
      },
    },
    markers: {
      size: 0,
      colors: '#ffffff',
      strokeColors: '#3b82f6',
      strokeWidth: 4,
      strokeOpacity: 1,
      strokeDashArray: 0,
      fillOpacity: 1,
      discrete: [],
      shape: 'circle',
      offsetX: 0,
      offsetY: 0,
      onClick: undefined,
      onDblClick: undefined,
      showNullDataPoints: true,
      hover: {
        size: 8,
        sizeOffset: 0,
      },
    },
    fill: {
      gradient: {
        opacityFrom: 0.25,
        opacityTo: 0,
      },
    },
    grid: {
      borderColor: 'var(--color-border)',
      strokeDashArray: 5,
      yaxis: {
        lines: {
          show: true,
        },
      },
      xaxis: {
        lines: {
          show: false,
        },
      },
    },
  };

  return (
    <Card className="h-full">
      <CardHeader>
        <CardTitle>Receitas</CardTitle>
        <div className="flex gap-5">
          <div className="flex items-center gap-2">
            <Label htmlFor="referrals-only" className="text-sm">
              Apenas recorrentes
            </Label>
            <Switch id="referrals-only" defaultChecked size="sm" />
          </div>
          <Select defaultValue="1">
            <SelectTrigger className="w-28">
              <SelectValue placeholder="Selecionar" />
            </SelectTrigger>
            <SelectContent className="w-28">
              <SelectItem value="1">1 mês</SelectItem>
              <SelectItem value="3">3 meses</SelectItem>
              <SelectItem value="6">6 meses</SelectItem>
              <SelectItem value="12">12 meses</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </CardHeader>
      <CardContent className="flex flex-col justify-end items-stretch grow px-3 py-1">
        <ApexChart
          id="financeiro_earnings_chart"
          options={options}
          series={options.series}
          type="area"
          height="250"
        />
      </CardContent>
    </Card>
  );
};

export { EarningsChart };
