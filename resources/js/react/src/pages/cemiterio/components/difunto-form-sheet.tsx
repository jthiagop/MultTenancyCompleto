import { useEffect, useRef, useState } from 'react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ButtonGroup } from '@/components/ui/button-group';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardToolbar } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { MaskedInput } from '@/components/common/masked-input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { DatePicker } from '@/components/ui/date-picker';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { useAppData } from '@/hooks/useAppData';
import { cn } from '@/lib/utils';
import { BookOpen, Camera, Check, ChevronDown, ClipboardPenLine, Copy, FileText, Loader2, MapPin, Phone, Plus, PlusCircle, Search, Trash2, UserRound, Users, X } from 'lucide-react';
import { notify } from '@/lib/notify';
import { DifuntoFormImageUpload } from './difunto-form-image-upload';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

export interface ResponsavelEntry {
  id: string;
  nome: string;
  telefone: string;
  cep: string;
  logradouro: string;
  numero: string;
  bairro: string;
  cidade: string;
  uf: string;
}

const EMPTY_RESPONSAVEL: Omit<ResponsavelEntry, 'id'> = {
  nome: '',
  telefone: '',
  cep: '',
  logradouro: '',
  numero: '',
  bairro: '',
  cidade: '',
  uf: '',
};

const BR_UF = [
  'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
  'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
  'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
] as const;

interface ResponsavelFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  editingItem?: ResponsavelEntry;
  onSave: (item: ResponsavelEntry) => void;
}

function ResponsavelFormSheet({ open, onOpenChange, editingItem, onSave }: ResponsavelFormSheetProps) {
  const isEditing = !!editingItem;
  const [form, setForm] = useState<Omit<ResponsavelEntry, 'id'>>(EMPTY_RESPONSAVEL);
  const [consultandoCep, setConsultandoCep] = useState(false);

  useEffect(() => {
    if (open) {
      if (editingItem) {
        const { id: _id, ...rest } = editingItem;
        setForm(rest);
      } else {
        setForm(EMPTY_RESPONSAVEL);
      }
    }
  }, [open, editingItem]);

  function setR<K extends keyof typeof EMPTY_RESPONSAVEL>(field: K, value: string) {
    setForm((prev) => ({ ...prev, [field]: value }));
  }

  async function consultarCep(cepValue: string) {
    const cepLimpo = cepValue.replace(/\D/g, '');
    if (cepLimpo.length !== 8) return;
    setConsultandoCep(true);
    try {
      const res = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
      if (!res.ok) {
        notify.error('Erro na consulta', 'Não foi possível consultar o CEP. Verifique sua conexão.');
        return;
      }
      const data = (await res.json()) as {
        logradouro?: string;
        bairro?: string;
        localidade?: string;
        uf?: string;
        erro?: boolean;
      };
      if (data.erro) {
        notify.warning('CEP não encontrado', 'O CEP informado não existe. Verifique e tente novamente.');
        return;
      }
      if (data.logradouro) setR('logradouro', data.logradouro);
      if (data.bairro) setR('bairro', data.bairro);
      if (data.localidade) setR('cidade', data.localidade);
      if (data.uf) setR('uf', data.uf);
    } catch {
      notify.error('Erro na consulta', 'Não foi possível consultar o CEP. Verifique sua conexão.');
    } finally {
      setConsultandoCep(false);
    }
  }

  function handleSave(e: React.FormEvent) {
    e.preventDefault();
    if (!form.nome.trim()) return;
    onSave({
      id: editingItem?.id ?? `${Date.now()}-${Math.random()}`,
      ...form,
      nome: form.nome.trim(),
    });
    onOpenChange(false);
  }

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent
        side="right"
        overlayClassName="z-[65]"
        className={cn(
          'z-70 gap-0 lg:w-130 sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '**:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5',
        )}
      >
        <SheetHeader className="border-b py-3.5 px-5 border-border space-y-0">
          <SheetTitle className="font-medium">
            {isEditing ? 'Editar Responsável' : 'Novo Responsável'}
          </SheetTitle>
        </SheetHeader>

        <form onSubmit={handleSave} className="flex flex-1 min-h-0 flex-col">
          <SheetBody className="grow p-0 flex flex-col min-h-0">
            <ScrollArea className="h-[calc(100dvh-13rem)] ps-5 pe-4 me-1 pb-5">
              <div className="space-y-5 mt-5.5">

                <Card className="rounded-md">
                  <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                    <CardTitle className="text-2sm flex items-center gap-1.5">
                      <UserRound className="size-3.5 text-muted-foreground" />
                      Identificação
                    </CardTitle>
                  </CardHeader>
                  <CardContent className="pt-4">
                    <div className="grid grid-cols-12 gap-3">
                      <div className="col-span-12 sm:col-span-8 space-y-2">
                        <Label className="text-xs">
                          Nome <span className="text-destructive">*</span>
                        </Label>
                        <Input
                          value={form.nome}
                          onChange={(e) => setR('nome', e.target.value)}
                          placeholder="Nome completo do responsável"
                          autoFocus
                        />
                      </div>
                      <div className="col-span-12 sm:col-span-4 space-y-2">
                        <Label className="text-xs">Telefone</Label>
                        <MaskedInput
                          maskType="telefone"
                          value={form.telefone}
                          onMaskedChange={(v) => setR('telefone', v)}
                          placeholder="(00) 00000-0000"
                        />
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <Card className="rounded-md">
                  <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                    <CardTitle className="text-2sm flex items-center gap-1.5">
                      <MapPin className="size-3.5 text-muted-foreground" />
                      Endereço
                    </CardTitle>
                  </CardHeader>
                  <CardContent className="pt-4">
                    <div className="grid grid-cols-12 gap-3">
                      <div className="col-span-12 sm:col-span-5 space-y-2">
                        <Label className="text-xs">CEP</Label>
                        <div className="flex gap-2 items-center">
                          <MaskedInput
                            maskType="cep"
                            value={form.cep}
                            onMaskedChange={(v) => { setR('cep', v); void consultarCep(v); }}
                            placeholder="00000-000"
                          />
                          {consultandoCep && (
                            <Loader2 className="size-4 animate-spin shrink-0 text-muted-foreground" />
                          )}
                        </div>
                      </div>
                      <div className="col-span-12 sm:col-span-7 space-y-2">
                        <Label className="text-xs">Logradouro</Label>
                        <Input
                          value={form.logradouro}
                          onChange={(e) => setR('logradouro', e.target.value)}
                          placeholder="Rua, Av., etc."
                        />
                      </div>
                      <div className="col-span-12 sm:col-span-3 space-y-2">
                        <Label className="text-xs">Número</Label>
                        <Input
                          value={form.numero}
                          onChange={(e) => setR('numero', e.target.value)}
                          placeholder="Nº"
                        />
                      </div>
                      <div className="col-span-12 sm:col-span-4 space-y-2">
                        <Label className="text-xs">Bairro</Label>
                        <Input
                          value={form.bairro}
                          onChange={(e) => setR('bairro', e.target.value)}
                          placeholder="Bairro"
                        />
                      </div>
                      <div className="col-span-12 sm:col-span-3 space-y-2">
                        <Label className="text-xs">Cidade</Label>
                        <Input
                          value={form.cidade}
                          onChange={(e) => setR('cidade', e.target.value)}
                          placeholder="Cidade"
                        />
                      </div>
                      <div className="col-span-12 sm:col-span-2 space-y-2">
                        <Label className="text-xs">UF</Label>
                        <select
                          value={form.uf}
                          onChange={(e) => setR('uf', e.target.value)}
                          className={cn(
                            'flex w-full bg-background border border-input rounded-md shadow-xs shadow-black/5',
                            'h-8.5 px-3 text-2sm text-foreground',
                            'focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 focus-visible:border-ring',
                            !form.uf && 'text-muted-foreground/80',
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
                </Card>

              </div>
            </ScrollArea>
          </SheetBody>

          <SheetFooter className="flex-row justify-end border-t border-border px-5 py-3 gap-2">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancelar
            </Button>
            <Button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white border-0">
              {isEditing ? 'Atualizar Responsável' : 'Adicionar Responsável'}
            </Button>
          </SheetFooter>
        </form>
      </SheetContent>
    </Sheet>
  );
}

interface SepulturaOption {
  id: number;
  codigo_sepultura: string;
  localizacao: string | null;
  tipo: string | null;
  status: string;
}

interface SepulturaSelectProps {
  value: number | null;
  label: string;
  csrfToken: string;
  onSelect: (id: number, label: string) => void;
  onClear: () => void;
}

function SepulturaSelect({ value, label, csrfToken, onSelect, onClear }: SepulturaSelectProps) {
  const [query, setQuery] = useState('');
  const [options, setOptions] = useState<SepulturaOption[]>([]);
  const [searching, setSearching] = useState(false);
  const [open, setOpen] = useState(false);
  const [showCreate, setShowCreate] = useState(false);
  const [creating, setCreating] = useState(false);
  const [createForm, setCreateForm] = useState({ codigo_sepultura: '', localizacao: '', tipo: '' });
  const containerRef = useRef<HTMLDivElement>(null);
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  useEffect(() => {
    if (debounceRef.current) clearTimeout(debounceRef.current);
    if (!open) return;
    debounceRef.current = setTimeout(async () => {
      setSearching(true);
      try {
        const res = await fetch(`/cemiterio/sepulturas/search?q=${encodeURIComponent(query)}`, {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        setOptions(json.data ?? []);
      } catch {
        // ignore
      } finally {
        setSearching(false);
      }
    }, 300);
    return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
  }, [query, open]);

  useEffect(() => {
    function handler(e: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false);
        setShowCreate(false);
      }
    }
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);

  async function handleCreate() {
    if (!createForm.codigo_sepultura.trim()) return;
    setCreating(true);
    try {
      const res = await fetch('/cemiterio/sepulturas/quick', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(createForm),
      });
      const json = await res.json();
      if (json.success) {
        const s = json.data as SepulturaOption;
        onSelect(s.id, s.codigo_sepultura);
        setOpen(false);
        setShowCreate(false);
        setQuery('');
        setCreateForm({ codigo_sepultura: '', localizacao: '', tipo: '' });
        notify.success('Sepultura cadastrada!', `${s.codigo_sepultura} foi cadastrada e vinculada.`);
      } else {
        notify.error('Erro', json.message ?? 'Não foi possível criar a sepultura.');
      }
    } catch {
      notify.networkError();
    } finally {
      setCreating(false);
    }
  }

  if (value) {
    return (
      <div className="flex items-center gap-2 h-9 px-3 border border-border rounded-md bg-accent/50">
        <Check className="size-3.5 text-green-600 shrink-0" />
        <span className="text-sm flex-1 truncate">{label}</span>
        <button type="button" onClick={onClear} className="shrink-0">
          <X className="size-3.5 text-muted-foreground hover:text-foreground" />
        </button>
      </div>
    );
  }

  return (
    <div ref={containerRef} className="relative">
      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground pointer-events-none" />
        <Input
          className="pl-8"
          placeholder="Buscar por código, localização ou tipo..."
          value={query}
          onFocus={() => setOpen(true)}
          onChange={(e) => { setQuery(e.target.value); setOpen(true); setShowCreate(false); }}
        />
      </div>
      {open && (
        <div className="absolute z-50 w-full mt-1 bg-popover border border-border rounded-md shadow-lg max-h-60 overflow-y-auto">
          {searching ? (
            <div className="flex items-center justify-center py-4">
              <Loader2 className="size-4 animate-spin text-muted-foreground" />
            </div>
          ) : options.length > 0 ? (
            <>
              {options.map((s) => (
                <button
                  key={s.id}
                  type="button"
                  className="w-full text-left px-3 py-2 text-sm hover:bg-accent flex items-start gap-2"
                  onClick={() => { onSelect(s.id, s.codigo_sepultura); setOpen(false); setQuery(''); }}
                >
                  <div className="flex-1 min-w-0">
                    <div className="font-medium">{s.codigo_sepultura}</div>
                    {(s.localizacao || s.tipo) && (
                      <div className="text-xs text-muted-foreground truncate">
                        {[s.tipo, s.localizacao].filter(Boolean).join(' · ')}
                      </div>
                    )}
                  </div>
                  <span className={cn('text-xs px-1.5 py-0.5 rounded-sm border shrink-0 mt-0.5', {
                    'border-green-500/40 text-green-700 bg-green-50 dark:bg-green-950': s.status === 'Disponível',
                    'border-red-500/40 text-red-700 bg-red-50 dark:bg-red-950': s.status === 'Ocupada',
                    'border-amber-500/40 text-amber-700 bg-amber-50 dark:bg-amber-950': s.status === 'Reservada',
                    'border-gray-500/40 text-gray-600 bg-gray-50 dark:bg-gray-900': s.status === 'Manutenção',
                  })}>
                    {s.status}
                  </span>
                </button>
              ))}
              <div className="border-t border-border" />
            </>
          ) : query.trim() !== '' ? (
            <div className="px-3 py-2 text-sm text-muted-foreground">Nenhuma sepultura encontrada.</div>
          ) : null}
          {!showCreate ? (
            <button
              type="button"
              className="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-accent flex items-center gap-2"
              onClick={() => setShowCreate(true)}
            >
              <PlusCircle className="size-3.5" />
              Cadastrar nova sepultura
            </button>
          ) : (
            <div className="p-3 space-y-2 border-t border-border">
              <p className="text-xs font-medium text-muted-foreground">Nova Sepultura</p>
              <Input
                placeholder="Código * (Ex: A-01)"
                value={createForm.codigo_sepultura}
                onChange={(e) => setCreateForm((p) => ({ ...p, codigo_sepultura: e.target.value }))}
                autoFocus
              />
              <Input
                placeholder="Localização (Ex: Quadra 1, Rua 2)"
                value={createForm.localizacao}
                onChange={(e) => setCreateForm((p) => ({ ...p, localizacao: e.target.value }))}
              />
              <select
                value={createForm.tipo}
                onChange={(e) => setCreateForm((p) => ({ ...p, tipo: e.target.value }))}
                className={cn(
                  'flex w-full bg-background border border-input rounded-md shadow-xs shadow-black/5',
                  'h-8.5 px-3 text-2sm text-foreground',
                  'focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/30 focus-visible:border-ring',
                  !createForm.tipo && 'text-muted-foreground/80',
                )}
              >
                <option value="">Tipo de sepultura</option>
                <option value="Gaveta">Gaveta</option>
                <option value="Carneiro">Carneiro</option>
                <option value="Jazigo">Jazigo</option>
                <option value="Túmulo">Túmulo</option>
                <option value="Cova">Cova</option>
                <option value="Cripta">Cripta</option>
                <option value="Columbário">Columbário</option>
                <option value="Mausoléu">Mausoléu</option>
              </select>
              <div className="flex gap-2 pt-1">
                <Button
                  type="button"
                  size="sm"
                  className="bg-blue-600 hover:bg-blue-700 text-white border-0"
                  disabled={!createForm.codigo_sepultura.trim() || creating}
                  onClick={handleCreate}
                >
                  {creating ? <Loader2 className="size-3.5 animate-spin" /> : 'Cadastrar'}
                </Button>
                <Button
                  type="button"
                  size="sm"
                  variant="outline"
                  onClick={() => setShowCreate(false)}
                >
                  Cancelar
                </Button>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

interface DifuntoFormSheetProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved?: () => void;
  editingId?: number | null;
  cloneFromId?: number | null;
}

interface FormState {
  nome_completo: string;
  cpf: string;
  data_nascimento: string;
  data_falecimento: string;
  data_sepultamento: string;
  causa_mortis: string;
  tumulo_codigo: string;
  sepultura_id: number | null;
  sepultura_label: string;
  relacionamento: string;
  informacoes_atestado_obito: string;
  livro_sepultamento: string;
  folha_sepultamento: string;
  numero_sepultamento: string;
  observacoes: string;
}

function todayIso(): string {
  return new Date().toISOString().slice(0, 10);
}

const EMPTY_FORM: FormState = {
  nome_completo: '',
  cpf: '',
  data_nascimento: todayIso(),
  data_falecimento: todayIso(),
  data_sepultamento: todayIso(),
  causa_mortis: '',
  tumulo_codigo: '',
  sepultura_id: null,
  sepultura_label: '',
  relacionamento: '',
  informacoes_atestado_obito: '',
  livro_sepultamento: '',
  folha_sepultamento: '',
  numero_sepultamento: '',
  observacoes: '',
};

export function DifuntoFormSheet({ open, onOpenChange, onSaved, editingId, cloneFromId }: DifuntoFormSheetProps) {
  const { csrfToken } = useAppData();
  const isEditing = !!editingId;
  const [form, setForm] = useState<FormState>(EMPTY_FORM);
  const [submitting, setSubmitting] = useState(false);
  const [loadingData, setLoadingData] = useState(false);
  const [responsaveis, setResponsaveis] = useState<ResponsavelEntry[]>([]);
  const [responsavelSheetOpen, setResponsavelSheetOpen] = useState(false);
  const [editingResponsavel, setEditingResponsavel] = useState<ResponsavelEntry | undefined>(undefined);
  const [avatarPreview, setAvatarPreview] = useState('');
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const avatarInputRef = useRef<HTMLInputElement>(null);
  const saveModeRef = useRef<'close' | 'clone' | 'clear'>('close');
  const [initialImages, setInitialImages] = useState<{ id: string; url: string }[]>([]);
  const [keepImageUrls, setKeepImageUrls] = useState<string[]>([]);
  const [newImageFiles, setNewImageFiles] = useState<File[]>([]);

  useEffect(() => {
    if (!open) return;
    if (editingId) {
      setLoadingData(true);
      fetch(`/cemiterio/difuntos/${editingId}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then((r) => r.json())
        .then((json) => {
          if (!json.success) return;
          const d = json.data;
          setForm({
            nome_completo:              d.nome_completo,
            cpf:                        d.cpf,
            data_nascimento:            d.data_nascimento,
            data_falecimento:           d.data_falecimento,
            data_sepultamento:          d.data_sepultamento,
            causa_mortis:               d.causa_mortis,
            tumulo_codigo:              d.tumulo_codigo,
            sepultura_id:               d.sepultura_id ?? null,
            sepultura_label:            d.sepultura_label ?? '',
            relacionamento:             d.relacionamento ?? '',
            informacoes_atestado_obito: d.informacoes_atestado_obito ?? '',
            livro_sepultamento:         d.livro_sepultamento ?? '',
            folha_sepultamento:         d.folha_sepultamento ?? '',
            numero_sepultamento:        d.numero_sepultamento ?? '',
            observacoes:                d.observacoes,
          });
          setAvatarPreview(d.avatar ?? '');
          setAvatarFile(null);
          setInitialImages(d.imagens ?? []);
          setKeepImageUrls((d.imagens ?? []).map((i: { id: string }) => i.id));
          setNewImageFiles([]);
          setResponsaveis(d.responsaveis ?? []);
        })
        .catch(() => notify.error('Erro', 'Não foi possível carregar os dados.'))
        .finally(() => setLoadingData(false));
    } else if (cloneFromId) {
      setLoadingData(true);
      fetch(`/cemiterio/difuntos/${cloneFromId}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then((r) => r.json())
        .then((json) => {
          if (!json.success) return;
          const d = json.data;
          setForm({
            nome_completo:              d.nome_completo + ' (Cópia)',
            cpf:                        '',
            data_nascimento:            d.data_nascimento,
            data_falecimento:           d.data_falecimento,
            data_sepultamento:          d.data_sepultamento,
            causa_mortis:               d.causa_mortis,
            tumulo_codigo:              '',
            sepultura_id:               null,
            sepultura_label:            '',
            relacionamento:             d.relacionamento ?? '',
            informacoes_atestado_obito: d.informacoes_atestado_obito ?? '',
            livro_sepultamento:         '',
            folha_sepultamento:         '',
            numero_sepultamento:        '',
            observacoes:                d.observacoes,
          });
          setAvatarPreview('');
          setAvatarFile(null);
          setInitialImages([]);
          setKeepImageUrls([]);
          setNewImageFiles([]);
          setResponsaveis(d.responsaveis ?? []);
        })
        .catch(() => notify.error('Erro', 'Não foi possível carregar os dados para clonar.'))
        .finally(() => setLoadingData(false));
    } else {
      setForm(EMPTY_FORM);
      setAvatarPreview('');
      setAvatarFile(null);
      setInitialImages([]);
      setKeepImageUrls([]);
      setNewImageFiles([]);
      setResponsaveis([]);
    }
  }, [open, editingId, cloneFromId]);

  function set<K extends keyof FormState>(field: K, value: FormState[K]) {
    setForm((prev) => ({ ...prev, [field]: value }));
  }

  function handleAvatarChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setAvatarFile(file);
    const reader = new FileReader();
    reader.onload = (ev) => setAvatarPreview(ev.target?.result as string);
    reader.readAsDataURL(file);
    e.target.value = '';
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();

    if (!form.nome_completo.trim()) {
      notify.warning('Campo obrigatório', 'Informe o nome completo do difunto.');
      return;
    }
    if (!form.data_falecimento) {
      notify.warning('Campo obrigatório', 'Informe a data de falecimento.');
      return;
    }
    if (!csrfToken) {
      notify.reload();
      return;
    }

    setSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('nome_completo', form.nome_completo.trim());
      if (form.cpf.trim()) fd.append('cpf', form.cpf.trim());
      if (form.data_nascimento) fd.append('data_nascimento', form.data_nascimento);
      fd.append('data_falecimento', form.data_falecimento);
      if (form.data_sepultamento) fd.append('data_sepultamento', form.data_sepultamento);
      if (form.causa_mortis.trim()) fd.append('causa_mortis', form.causa_mortis.trim());
      if (form.tumulo_codigo.trim()) fd.append('tumulo_codigo', form.tumulo_codigo.trim());
      if (form.sepultura_id) fd.append('sepultura_id', String(form.sepultura_id));
      if (form.relacionamento.trim()) fd.append('relacionamento', form.relacionamento.trim());
      if (form.informacoes_atestado_obito.trim()) fd.append('informacoes_atestado_obito', form.informacoes_atestado_obito.trim());
      if (form.livro_sepultamento.trim()) fd.append('livro_sepultamento', form.livro_sepultamento.trim());
      if (form.folha_sepultamento.trim()) fd.append('folha_sepultamento', form.folha_sepultamento.trim());
      if (form.numero_sepultamento.trim()) fd.append('numero_sepultamento', form.numero_sepultamento.trim());
      if (form.observacoes.trim()) fd.append('observacoes', form.observacoes.trim());
      if (avatarFile) fd.append('avatar', avatarFile);
      if (isEditing) fd.append('keep_imagens', JSON.stringify(keepImageUrls));
      newImageFiles.forEach((file) => fd.append('novas_imagens[]', file));
      if (responsaveis.length > 0) fd.append('responsaveis', JSON.stringify(responsaveis));

      const url    = isEditing ? `/cemiterio/difuntos/${editingId}` : '/cemiterio/difuntos';
      const method = isEditing ? 'POST' : 'POST';
      // Laravel não aceita PUT com FormData — usa _method spoofing
      if (isEditing) fd.append('_method', 'PUT');

      const res = await fetch(url, {
        method,
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: fd,
      });

      if (!res.headers.get('content-type')?.includes('application/json')) {
        notify.error('Erro inesperado', 'Resposta inválida do servidor.');
        return;
      }

      const result = (await res.json()) as {
        success?: boolean;
        message?: string;
        errors?: Record<string, string[]>;
      };

      if (res.ok && result.success) {
        notify.success(
          isEditing ? 'Difunto atualizado!' : 'Difunto cadastrado!',
          `${form.nome_completo.trim()} foi ${isEditing ? 'atualizado' : 'adicionado'} com sucesso.`,
        );
        const mode = saveModeRef.current;
        if (mode === 'clone') {
          // mantém o formulário preenchido para criar registro similar
          onSaved?.();
        } else if (mode === 'clear') {
          // limpa o formulário para novo registro em branco
          setForm(EMPTY_FORM);
          setAvatarPreview('');
          setAvatarFile(null);
          setInitialImages([]);
          setKeepImageUrls([]);
          setNewImageFiles([]);
          setResponsaveis([]);
          onSaved?.();
        } else {
          // mode === 'close'
          onOpenChange(false);
          onSaved?.();
        }
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
          'z-60 gap-0 lg:w-295 sm:max-w-none inset-5 start-auto h-auto rounded-lg border p-0 flex flex-col',
          '**:data-[slot=sheet-close]:top-4.5 **:data-[slot=sheet-close]:end-5',
        )}
      >
        <SheetHeader className="border-b py-3.5 px-5 border-border space-y-0">
          <SheetTitle className="font-medium flex items-center gap-2">
            <UserRound className="size-4 text-muted-foreground" />
            {isEditing ? 'Editar Difunto' : 'Novo Difunto'}
          </SheetTitle>
        </SheetHeader>

        <form id="difunto-form" onSubmit={handleSubmit} className="flex flex-1 min-h-0 flex-col">
          <SheetBody className="grow p-0 flex flex-col min-h-0">
            {loadingData ? (
              <div className="flex flex-1 items-center justify-center py-20">
                <Loader2 className="size-6 animate-spin text-muted-foreground" />
              </div>
            ) : (<>
            {/* Action bar */}
            <div className="flex justify-between gap-2 flex-wrap border-b border-border p-5">
              <div />
              <div className="flex items-center gap-2.5">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => onOpenChange(false)}
                  disabled={submitting}
                >
                  <X className="size-4" />
                  Cancelar
                </Button>
                <ButtonGroup>
                  <Button
                    type="submit"
                    className="bg-blue-600 hover:bg-blue-700 text-white border-0 rounded-e-none"
                    disabled={submitting}
                    onClick={() => { saveModeRef.current = 'close'; }}
                  >
                    {submitting ? (
                      <><Loader2 className="size-4 animate-spin" />Salvando…</>
                    ) : (
                      <><Check className="size-4" />Salvar</>
                    )}
                  </Button>
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button
                        type="button"
                        className="bg-blue-600 hover:bg-blue-700 text-white border-0 border-l border-l-blue-500 px-2 rounded-s-none"
                        disabled={submitting}
                      >
                        <ChevronDown className="size-3.5" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" className="z-65">
                      <DropdownMenuItem
                        onClick={() => {
                          saveModeRef.current = 'clone';
                          (document.getElementById('difunto-form') as HTMLFormElement)?.requestSubmit();
                        }}
                      >
                        <Copy className="size-3.5 me-2" />
                        Salvar e Clonar
                      </DropdownMenuItem>
                      <DropdownMenuItem
                        onClick={() => {
                          saveModeRef.current = 'clear';
                          (document.getElementById('difunto-form') as HTMLFormElement)?.requestSubmit();
                        }}
                      >
                        <Plus className="size-3.5 me-2" />
                        Salvar e Limpar
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </ButtonGroup>
              </div>
            </div>

            {/* Two-column scroll area */}
            <ScrollArea
              className="flex flex-col h-[calc(100dvh-15.2rem)] mx-1.5"
              viewportClassName="[&>div]:h-full [&>div>div]:h-full"
            >
              <div className="flex flex-wrap lg:flex-nowrap px-3.5 grow">
                {/* Left column — form fields */}
                <div className="grow lg:border-e border-border lg:pe-5 space-y-5 py-5">

                  {/* Dados Pessoais */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <UserRound className="size-3.5 text-muted-foreground" />
                        Dados Pessoais
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                      <div className="flex gap-5">

                        {/* Avatar */}
                        <div className="flex flex-col items-center gap-2 shrink-0">
                          <input
                            ref={avatarInputRef}
                            type="file"
                            accept="image/*"
                            className="hidden"
                            onChange={handleAvatarChange}
                          />
                          <div
                            className="relative size-16 cursor-pointer"
                            onClick={() => avatarInputRef.current?.click()}
                          >
                            <div className="size-16 rounded-full overflow-hidden border-2 border-border bg-accent flex items-center justify-center">
                              {avatarPreview ? (
                                <img src={avatarPreview} alt="Avatar do difunto" className="size-full object-cover" />
                              ) : (
                                <UserRound className="size-8 text-muted-foreground" />
                              )}
                            </div>
                            <div className="absolute bottom-0 left-0 right-0 h-5 bg-black/25 flex items-center justify-center rounded-b-full pointer-events-none">
                              <Camera className="size-3 text-white" />
                            </div>
                            {avatarPreview && (
                              <button
                                type="button"
                                onClick={(e) => { e.stopPropagation(); setAvatarPreview(''); setAvatarFile(null); }}
                                className="absolute -top-0.5 -end-0.5 z-10 size-5 flex items-center justify-center rounded-full border border-border bg-background shadow-sm"
                              >
                                <X className="size-3 text-muted-foreground hover:text-foreground" />
                              </button>
                            )}
                          </div>
                          <span className="text-xs text-muted-foreground">Foto</span>
                        </div>

                        {/* Fields */}
                        <div className="grid grid-cols-12 gap-3 flex-1">

                        <div className="col-span-12 sm:col-span-8 space-y-2">
                          <Label className="text-xs">
                            Nome Completo <span className="text-destructive">*</span>
                          </Label>
                          <Input
                            value={form.nome_completo}
                            onChange={(e) => set('nome_completo', e.target.value)}
                            placeholder="Nome completo do difunto"
                            autoFocus
                          />
                        </div>

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">CPF</Label>
                          <MaskedInput
                            maskType="cpf"
                            value={form.cpf}
                            onMaskedChange={(v) => set('cpf', v)}
                            placeholder="000.000.000-00"
                          />
                        </div>

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">Data de Nascimento</Label>
                          <DatePicker
                            value={form.data_nascimento}
                            onChange={(iso) => set('data_nascimento', iso)}
                            placeholder="DD/MM/AAAA"
                            className="w-full"
                          />
                        </div>

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">
                            Data de Falecimento <span className="text-destructive">*</span>
                          </Label>
                          <DatePicker
                            value={form.data_falecimento}
                            onChange={(iso) => set('data_falecimento', iso)}
                            placeholder="DD/MM/AAAA"
                            className="w-full"
                          />
                        </div>

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">Data de Sepultamento</Label>
                          <DatePicker
                            value={form.data_sepultamento}
                            onChange={(iso) => set('data_sepultamento', iso)}
                            placeholder="DD/MM/AAAA"
                            className="w-full"
                          />
                        </div>

                        <div className="col-span-12 space-y-2">
                          <Label className="text-xs">Causa Mortis</Label>
                          <Input
                            value={form.causa_mortis}
                            onChange={(e) => set('causa_mortis', e.target.value)}
                            placeholder="Ex: Insuficiência cardíaca"
                          />
                        </div>

                        </div>
                      </div>
                    </CardContent>
                  </Card>

                  {/* Responsáveis */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <Users className="size-3.5 text-muted-foreground" />
                        Responsáveis
                      </CardTitle>
                      <CardToolbar>
                        <Button
                          type="button"
                          size="sm"
                          variant="outline"
                          onClick={() => { setEditingResponsavel(undefined); setResponsavelSheetOpen(true); }}
                        >
                          <Plus className="size-3.5" />
                          Adicionar
                        </Button>
                      </CardToolbar>
                    </CardHeader>
                    <CardContent className="p-0">
                      {responsaveis.length === 0 ? (
                        <div className="flex flex-col items-start gap-2 p-5">
                          <p className="text-sm text-muted-foreground">Nenhum responsável adicionado.</p>
                          <Button
                            type="button"
                            size="sm"
                            variant="outline"
                            onClick={() => { setEditingResponsavel(undefined); setResponsavelSheetOpen(true); }}
                          >
                            <Plus className="size-3.5" />
                            Adicionar Responsável
                          </Button>
                        </div>
                      ) : (
                        <Table>
                          <TableHeader>
                            <TableRow className="text-2sm border-border/60">
                              <TableHead className="h-8.5 ps-5 border-e border-border/60">Nome</TableHead>
                              <TableHead className="h-8.5 border-e border-border/60">
                                <span className="inline-flex items-center gap-1">
                                  <Phone className="size-3.5 text-muted-foreground" />
                                  Telefone
                                </span>
                              </TableHead>
                              <TableHead className="h-8.5 border-e border-border/60">Endereço</TableHead>
                              <TableHead className="h-8.5 w-20">Ações</TableHead>
                            </TableRow>
                          </TableHeader>
                          <TableBody>
                            {responsaveis.map((r, idx) => (
                              <TableRow key={r.id} className={cn('text-2sm border-0', idx % 2 === 0 && 'bg-accent/50')}>
                                <TableCell className="py-1.5 ps-5 border-e border-border/60 font-medium">{r.nome}</TableCell>
                                <TableCell className="py-1.5 border-e border-border/60 text-muted-foreground">{r.telefone || '—'}</TableCell>
                                <TableCell className="py-1.5 border-e border-border/60 text-muted-foreground text-xs">
                                  {[r.logradouro, r.numero, r.bairro, r.cidade, r.uf].filter(Boolean).join(', ') || '—'}
                                </TableCell>
                                <TableCell className="py-1 text-center">
                                  <div className="flex items-center justify-center gap-0.5">
                                    <Button
                                      type="button"
                                      variant="ghost"
                                      size="sm"
                                      onClick={() => { setEditingResponsavel(r); setResponsavelSheetOpen(true); }}
                                    >
                                      <ClipboardPenLine className="size-3.5" />
                                    </Button>
                                    <Button
                                      type="button"
                                      variant="ghost"
                                      size="sm"
                                      onClick={() => setResponsaveis((prev) => prev.filter((x) => x.id !== r.id))}
                                    >
                                      <Trash2 className="size-3.5" />
                                    </Button>
                                  </div>
                                </TableCell>
                              </TableRow>
                            ))}
                          </TableBody>
                        </Table>
                      )}
                    </CardContent>
                  </Card>

                  <ResponsavelFormSheet
                    open={responsavelSheetOpen}
                    onOpenChange={setResponsavelSheetOpen}
                    editingItem={editingResponsavel}
                    onSave={(item) =>
                      setResponsaveis((prev) =>
                        editingResponsavel ? prev.map((x) => (x.id === item.id ? item : x)) : [...prev, item],
                      )
                    }
                  />

                  {/* Sepultamento */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <MapPin className="size-3.5 text-muted-foreground" />
                        Sepultamento
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                      <div className="grid grid-cols-12 gap-3">

                        <div className="col-span-12 space-y-2">
                          <Label className="text-xs">Sepultura</Label>
                          <SepulturaSelect
                            value={form.sepultura_id}
                            label={form.sepultura_label}
                            csrfToken={csrfToken ?? ''}
                            onSelect={(id, label) => setForm((p) => ({ ...p, sepultura_id: id, sepultura_label: label }))}
                            onClear={() => setForm((p) => ({ ...p, sepultura_id: null, sepultura_label: '' }))}
                          />
                        </div>

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">Código do Túmulo</Label>
                          <Input
                            value={form.tumulo_codigo}
                            onChange={(e) => set('tumulo_codigo', e.target.value)}
                            placeholder="Ex: A-01"
                          />
                        </div>

                        <div className="col-span-12 space-y-2">
                          <Label className="text-xs">Observações</Label>
                          <Textarea
                            value={form.observacoes}
                            onChange={(e) => set('observacoes', e.target.value)}
                            placeholder="Observações adicionais"
                            rows={3}
                            className="resize-none"
                          />
                        </div>

                      </div>
                    </CardContent>
                  </Card>

                  {/* Registro Civil */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <FileText className="size-3.5 text-muted-foreground" />
                        Registro Civil
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                      <div className="grid grid-cols-12 gap-3">

                        <div className="col-span-12 sm:col-span-6 space-y-2">
                          <Label className="text-xs">Relacionamento / Parentesco</Label>
                          <Input
                            value={form.relacionamento}
                            onChange={(e) => set('relacionamento', e.target.value)}
                            placeholder="Ex: Cônjuge, Filho, Pai"
                          />
                        </div>

                        <div className="col-span-12 space-y-2">
                          <Label className="text-xs">Informações do Atestado de Óbito</Label>
                          <Textarea
                            value={form.informacoes_atestado_obito}
                            onChange={(e) => set('informacoes_atestado_obito', e.target.value)}
                            placeholder="Informações relevantes do atestado de óbito"
                            rows={3}
                            className="resize-none"
                          />
                        </div>

                      </div>
                    </CardContent>
                  </Card>

                  {/* Livro de Sepultamento */}
                  <Card className="rounded-md">
                    <CardHeader className="min-h-9.5 bg-accent/50 py-2">
                      <CardTitle className="text-2sm flex items-center gap-1.5">
                        <BookOpen className="size-3.5 text-muted-foreground" />
                        Livro de Sepultamento
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                      <div className="grid grid-cols-12 gap-3">

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">Livro</Label>
                          <Input
                            value={form.livro_sepultamento}
                            onChange={(e) => set('livro_sepultamento', e.target.value)}
                            placeholder="Nº do livro"
                          />
                        </div>

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">Folha</Label>
                          <Input
                            value={form.folha_sepultamento}
                            onChange={(e) => set('folha_sepultamento', e.target.value)}
                            placeholder="Nº da folha"
                          />
                        </div>

                        <div className="col-span-12 sm:col-span-4 space-y-2">
                          <Label className="text-xs">Número</Label>
                          <Input
                            value={form.numero_sepultamento}
                            onChange={(e) => set('numero_sepultamento', e.target.value)}
                            placeholder="Nº do registro"
                          />
                        </div>

                      </div>
                    </CardContent>
                  </Card>

                </div>

                {/* Right column — image upload */}
                <div className="w-full lg:w-95 shrink-0 lg:mt-5 space-y-5 lg:ps-5">
                  <DifuntoFormImageUpload
                    initialImages={initialImages}
                    onStateChange={(kept, files) => {
                      setKeepImageUrls(kept);
                      setNewImageFiles(files);
                    }}
                  />
                  <Separator />
                </div>

              </div>
            </ScrollArea>
          </>)}
          </SheetBody>

          <SheetFooter className="flex-row justify-between items-center border-t border-border px-5 py-4 gap-2">
            <div />
            <div className="flex items-center gap-2">
              <Button
                type="button"
                variant="outline"
                onClick={() => onOpenChange(false)}
                disabled={submitting}
              >
                <X className="size-4" />
                Cancelar
              </Button>
              <ButtonGroup>
                <Button
                  type="submit"
                  className="bg-blue-600 hover:bg-blue-700 text-white border-0 rounded-e-none"
                  disabled={submitting}
                  onClick={() => { saveModeRef.current = 'close'; }}
                >
                  {submitting ? (
                    <><Loader2 className="size-4 animate-spin" />Salvando…</>
                  ) : (
                    <><Check className="size-4" />{isEditing ? 'Salvar Alterações' : 'Salvar Difunto'}</>
                  )}
                </Button>
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button
                      type="button"
                      className="bg-blue-600 hover:bg-blue-700 text-white border-0 border-l border-l-blue-500 px-2 rounded-s-none"
                      disabled={submitting}
                    >
                      <ChevronDown className="size-3.5" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end" className="z-65">
                    <DropdownMenuItem
                      onClick={() => {
                        saveModeRef.current = 'clone';
                        (document.getElementById('difunto-form') as HTMLFormElement)?.requestSubmit();
                      }}
                    >
                      <Copy className="size-3.5 me-2" />
                      Salvar e Clonar
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      onClick={() => {
                        saveModeRef.current = 'clear';
                        (document.getElementById('difunto-form') as HTMLFormElement)?.requestSubmit();
                      }}
                    >
                      <Plus className="size-3.5 me-2" />
                      Salvar e Limpar
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </ButtonGroup>
            </div>
          </SheetFooter>
        </form>
      </SheetContent>
    </Sheet>
  );
}
