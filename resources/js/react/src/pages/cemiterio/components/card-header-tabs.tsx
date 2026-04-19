import { CardHeader } from '@/components/ui/card';
import { TabsList, TabsTrigger } from '@/components/ui/tabs';

export function CardHeaderTabs() {
  return (
    <CardHeader className="flex-col items-stretch justify-start gap-0 border-0 pt-4 pb-0 min-h-0 px-0">
      <TabsList variant="line" className="w-full justify-start gap-6 px-5">
        <TabsTrigger value="tumulos">Túmulos</TabsTrigger>
        <TabsTrigger value="difuntos">Difuntos</TabsTrigger>
        <TabsTrigger value="ocupacoes">Ocupações</TabsTrigger>
      </TabsList>
    </CardHeader>
  );
}
