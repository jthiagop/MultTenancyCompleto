import { Filter } from 'lucide-react';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

const TIPOS_DOCUMENTO = [
  { value: 'NF-e', label: 'NF-e' },
  { value: 'NFC-e', label: 'NFC-e' },
  { value: 'CUPOM', label: 'Cupom Fiscal' },
  { value: 'BOLETO', label: 'Boleto' },
  { value: 'RECIBO', label: 'Recibo' },
  { value: 'FATURA_CARTAO', label: 'Fatura de Cartão' },
  { value: 'COMPROVANTE', label: 'Comprovante' },
  { value: 'OUTRO', label: 'Outro' },
];

interface DocumentFilterProps {
  value: string;
  onChange: (value: string) => void;
}

export function DocumentFilter({ value, onChange }: DocumentFilterProps) {
  return (
    <div>
      <label className="flex items-center gap-1.5 text-xs font-semibold text-muted-foreground mb-1.5">
        <Filter className="size-3" />
        Filtrar por tipo
      </label>
      <Select value={value} onValueChange={(v) => onChange(v === '__all__' ? '' : v)}>
        <SelectTrigger className="h-9 text-sm">
          <SelectValue placeholder="Todos os tipos" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="__all__">Todos os tipos</SelectItem>
          {TIPOS_DOCUMENTO.map((t) => (
            <SelectItem key={t.value} value={t.value}>
              {t.label}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
    </div>
  );
}
