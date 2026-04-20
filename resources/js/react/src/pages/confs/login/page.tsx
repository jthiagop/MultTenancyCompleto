import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Navigate } from 'react-router';
import { Image as ImageIcon, Loader2, Pencil, Trash2, Upload, Check, X } from 'lucide-react';
import { Container } from '@/components/common/container';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from '@/components/ui/carousel';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { cn } from '@/lib/utils';
import { notify } from '@/lib/notify';
import { useAppData } from '@/hooks/useAppData';

/** Payload retornado por TelaDeLoginController::transformImage(). */
interface ActiveImage {
  id: number;
  descricao: string;
  localidade: string;
  status: string;
  imagem_caminho: string;
  imagem_url: string;
  created_at: string | null;
}

/** Payload retornado por TelaDeLoginController::loadPredefinedImages(). */
interface PredefinedImage {
  path: string;
  name: string;
  url: string;
}

interface ListResponse {
  active_images: ActiveImage[];
  predefined: PredefinedImage[];
}

interface SaveResponse {
  success?: boolean;
  message?: string;
  data?: ActiveImage;
  errors?: Record<string, string[]>;
  id?: number;
}

// URL canônica gerada por route('telaLogin.index') no Laravel.
// A rota resource `telaLogin` está registrada na raiz do domínio tenant
// (ver routes/tenant.php, Route::middleware(['role:admin|global'])).
const BASE_URL = '/telaLogin';

/** Headers padrão pra dizer ao Laravel que queremos JSON. */
function makeHeaders(csrfToken: string, extra: HeadersInit = {}): HeadersInit {
  return {
    Accept: 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    'X-Requested-With': 'XMLHttpRequest',
    ...extra,
  };
}

export function LoginCustomizationPage() {
  const { csrfToken, hasAdminRole, hasGlobalRole } = useAppData();
  const canAccess = Boolean(hasAdminRole || hasGlobalRole);

  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [activeImages, setActiveImages] = useState<ActiveImage[]>([]);
  const [predefined, setPredefined] = useState<PredefinedImage[]>([]);

  const [uploadFile, setUploadFile] = useState<File | null>(null);
  const [uploadPreview, setUploadPreview] = useState<string | null>(null);
  const [selectedPredefined, setSelectedPredefined] = useState<string | null>(null);
  const [descricao, setDescricao] = useState('');
  const [localidade, setLocalidade] = useState('');
  const [formErrors, setFormErrors] = useState<Record<string, string>>({});

  const [editing, setEditing] = useState<ActiveImage | null>(null);
  const [editDescricao, setEditDescricao] = useState('');
  const [editLocalidade, setEditLocalidade] = useState('');
  const [editErrors, setEditErrors] = useState<Record<string, string>>({});
  const [editSubmitting, setEditSubmitting] = useState(false);

  const [toDelete, setToDelete] = useState<ActiveImage | null>(null);
  const [deleting, setDeleting] = useState(false);

  const fileInputRef = useRef<HTMLInputElement>(null);

  const fetchList = useCallback(async () => {
    try {
      const res = await fetch(BASE_URL, {
        method: 'GET',
        headers: makeHeaders(csrfToken),
        credentials: 'same-origin',
      });

      if (res.status === 401 || res.status === 419) {
        notify.reload();
        return;
      }

      if (res.status === 403) {
        notify.error('Sem permissão', 'Você não tem acesso para gerenciar a tela de login.');
        return;
      }

      const contentType = res.headers.get('content-type') ?? '';
      if (!res.ok || !contentType.includes('application/json')) {
        notify.error(
          'Erro ao carregar',
          `Não foi possível carregar as imagens atuais (HTTP ${res.status}).`,
        );
        return;
      }

      const data = (await res.json()) as ListResponse;
      setActiveImages(data.active_images ?? []);
      setPredefined(data.predefined ?? []);
    } catch (err) {
      console.error('[LoginCustomizationPage] fetchList failed:', err);
      notify.networkError();
    } finally {
      setLoading(false);
    }
  }, [csrfToken]);

  useEffect(() => {
    if (!canAccess) return;
    fetchList();
  }, [canAccess, fetchList]);

  if (!canAccess) {
    return <Navigate to="/dashboard" replace />;
  }

  function handleFileChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setUploadFile(file);
    setSelectedPredefined(null);
    const reader = new FileReader();
    reader.onload = (ev) => setUploadPreview((ev.target?.result as string) ?? null);
    reader.readAsDataURL(file);
    e.target.value = '';
  }

  function clearUpload() {
    setUploadFile(null);
    setUploadPreview(null);
  }

  function selectPredefined(path: string) {
    setSelectedPredefined((prev) => (prev === path ? null : path));
    if (uploadFile) clearUpload();
  }

  function resetForm() {
    setUploadFile(null);
    setUploadPreview(null);
    setSelectedPredefined(null);
    setDescricao('');
    setLocalidade('');
    setFormErrors({});
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setFormErrors({});

    const errs: Record<string, string> = {};
    if (!descricao.trim()) errs.descricao = 'Informe o nome do convento.';
    if (!localidade.trim()) errs.localidade = 'Informe a localidade.';
    if (!uploadFile && !selectedPredefined) {
      errs.backgroundImage = 'Envie uma imagem ou selecione uma das predefinidas.';
    }
    if (Object.keys(errs).length > 0) {
      setFormErrors(errs);
      return;
    }

    if (!csrfToken) {
      notify.reload();
      return;
    }

    setSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('descricao', descricao.trim());
      fd.append('localidade', localidade.trim());
      if (uploadFile) {
        fd.append('backgroundImage', uploadFile);
      } else if (selectedPredefined) {
        fd.append('selectedImage', selectedPredefined);
      }

      const res = await fetch(BASE_URL, {
        method: 'POST',
        headers: makeHeaders(csrfToken),
        credentials: 'same-origin',
        body: fd,
      });

      const result = (await res.json().catch(() => ({}))) as SaveResponse;

      if (res.ok && result.success !== false && result.data) {
        notify.success('Imagem adicionada!', result.message ?? 'A imagem foi salva com sucesso.');
        setActiveImages((prev) => [result.data as ActiveImage, ...prev]);
        resetForm();
        return;
      }

      if (result.errors) {
        const mapped: Record<string, string> = {};
        Object.entries(result.errors).forEach(([field, msgs]) => {
          if (msgs[0]) mapped[field] = msgs[0];
        });
        setFormErrors(mapped);
        notify.validationErrors(result.errors);
        return;
      }

      notify.error('Não foi possível salvar', result.message ?? 'Verifique os dados e tente novamente.');
    } catch {
      notify.networkError();
    } finally {
      setSubmitting(false);
    }
  }

  function openEdit(img: ActiveImage) {
    setEditing(img);
    setEditDescricao(img.descricao);
    setEditLocalidade(img.localidade);
    setEditErrors({});
  }

  async function handleUpdate(e: React.FormEvent) {
    e.preventDefault();
    if (!editing) return;

    const errs: Record<string, string> = {};
    if (!editDescricao.trim()) errs.descricao = 'Informe o nome do convento.';
    if (!editLocalidade.trim()) errs.localidade = 'Informe a localidade.';
    if (Object.keys(errs).length > 0) {
      setEditErrors(errs);
      return;
    }

    if (!csrfToken) {
      notify.reload();
      return;
    }

    setEditSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('_method', 'PUT');
      fd.append('descricao', editDescricao.trim());
      fd.append('localidade', editLocalidade.trim());

      const res = await fetch(`${BASE_URL}/${editing.id}`, {
        method: 'POST',
        headers: makeHeaders(csrfToken),
        credentials: 'same-origin',
        body: fd,
      });

      const result = (await res.json().catch(() => ({}))) as SaveResponse;

      if (res.ok && result.success !== false && result.data) {
        notify.success('Atualizado!', result.message ?? 'As informações foram salvas.');
        setActiveImages((prev) =>
          prev.map((img) => (img.id === editing.id ? (result.data as ActiveImage) : img)),
        );
        setEditing(null);
        return;
      }

      if (result.errors) {
        const mapped: Record<string, string> = {};
        Object.entries(result.errors).forEach(([field, msgs]) => {
          if (msgs[0]) mapped[field] = msgs[0];
        });
        setEditErrors(mapped);
        notify.validationErrors(result.errors);
        return;
      }

      notify.error('Não foi possível atualizar', result.message ?? 'Verifique os dados.');
    } catch {
      notify.networkError();
    } finally {
      setEditSubmitting(false);
    }
  }

  async function handleDelete() {
    if (!toDelete) return;
    if (!csrfToken) {
      notify.reload();
      return;
    }

    setDeleting(true);
    try {
      const fd = new FormData();
      fd.append('_method', 'DELETE');

      const res = await fetch(`${BASE_URL}/${toDelete.id}`, {
        method: 'POST',
        headers: makeHeaders(csrfToken),
        credentials: 'same-origin',
        body: fd,
      });

      const result = (await res.json().catch(() => ({}))) as SaveResponse;

      if (res.ok && result.success !== false) {
        notify.success('Removida!', result.message ?? 'A imagem foi removida da galeria.');
        setActiveImages((prev) => prev.filter((img) => img.id !== toDelete.id));
        setToDelete(null);
        return;
      }

      notify.error('Não foi possível remover', result.message ?? 'Tente novamente.');
    } catch {
      notify.networkError();
    } finally {
      setDeleting(false);
    }
  }

  const hasActive = activeImages.length > 0;

  const previewSource = useMemo(() => {
    if (uploadPreview) return uploadPreview;
    if (selectedPredefined) {
      return predefined.find((p) => p.path === selectedPredefined)?.url ?? null;
    }
    return null;
  }, [uploadPreview, selectedPredefined, predefined]);

  return (
    <Container className="py-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-semibold flex items-center gap-2">
            <ImageIcon className="size-6 text-primary" />
            Personalizar Tela de Login
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            Gerencie as imagens de fundo exibidas no carrossel da tela de login.
          </p>
        </div>
      </div>

      {/* Galeria atual */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <ImageIcon className="size-4 text-muted-foreground" />
            Imagens ativas
          </CardTitle>
          <CardDescription>
            Estas imagens aparecem no carrossel da tela de login da sua fraternidade.
          </CardDescription>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="flex items-center justify-center py-16">
              <Loader2 className="size-6 animate-spin text-muted-foreground" />
            </div>
          ) : !hasActive ? (
            <div className="text-center py-10 text-sm text-muted-foreground border border-dashed border-border rounded-md">
              Nenhuma imagem ativa. Use o formulário abaixo para adicionar a primeira.
            </div>
          ) : (
            <Carousel opts={{ align: 'start' }} className="w-full">
              <CarouselContent>
                {activeImages.map((img) => (
                  <CarouselItem key={img.id} className="basis-full md:basis-1/2 lg:basis-1/3">
                    <div className="p-1">
                      <Card className="overflow-hidden">
                        <div className="aspect-video w-full bg-muted overflow-hidden">
                          <img
                            src={img.imagem_url}
                            alt={img.descricao}
                            className="size-full object-cover"
                          />
                        </div>
                        <CardContent className="p-3">
                          <p className="font-semibold truncate text-sm">{img.descricao}</p>
                          <p className="text-xs text-muted-foreground truncate">{img.localidade}</p>
                          <div className="flex gap-2 mt-3">
                            <Button
                              type="button"
                              variant="outline"
                              size="sm"
                              className="flex-1"
                              onClick={() => openEdit(img)}
                            >
                              <Pencil className="size-3.5" />
                              Editar
                            </Button>
                            <Button
                              type="button"
                              variant="outline"
                              size="sm"
                              className="flex-1 text-destructive hover:text-destructive"
                              onClick={() => setToDelete(img)}
                            >
                              <Trash2 className="size-3.5" />
                              Remover
                            </Button>
                          </div>
                        </CardContent>
                      </Card>
                    </div>
                  </CarouselItem>
                ))}
              </CarouselContent>
              <CarouselPrevious />
              <CarouselNext />
            </Carousel>
          )}
        </CardContent>
      </Card>

      {/* Formulário de adição */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Upload className="size-4 text-muted-foreground" />
            Adicionar imagem
          </CardTitle>
          <CardDescription>
            Envie uma imagem do seu dispositivo ou escolha entre as predefinidas abaixo. Formatos aceitos: JPG, PNG, GIF, WEBP. Tamanho máximo: 4 MB.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {/* Coluna esquerda: preview + controles de imagem */}
            <div className="lg:col-span-3 space-y-4">
              <div className="rounded-md border border-border bg-muted/30 aspect-video overflow-hidden flex items-center justify-center">
                {previewSource ? (
                  <img
                    src={previewSource}
                    alt="Pré-visualização"
                    className="size-full object-cover"
                  />
                ) : (
                  <div className="text-center text-sm text-muted-foreground px-4">
                    <ImageIcon className="mx-auto size-8 mb-2 opacity-60" />
                    Pré-visualização da imagem selecionada
                  </div>
                )}
              </div>

              <div className="flex flex-wrap items-center gap-3">
                <input
                  ref={fileInputRef}
                  type="file"
                  accept="image/jpeg,image/png,image/gif,image/webp"
                  className="hidden"
                  onChange={handleFileChange}
                />
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => fileInputRef.current?.click()}
                >
                  <Upload className="size-4" />
                  {uploadFile ? 'Trocar imagem' : 'Enviar imagem'}
                </Button>
                {uploadFile && (
                  <>
                    <span className="text-xs text-muted-foreground truncate max-w-xs">
                      {uploadFile.name}
                    </span>
                    <Button
                      type="button"
                      variant="ghost"
                      size="sm"
                      onClick={clearUpload}
                    >
                      <X className="size-3.5" />
                      Remover
                    </Button>
                  </>
                )}
              </div>

              {formErrors.backgroundImage && (
                <p className="text-xs text-destructive">{formErrors.backgroundImage}</p>
              )}

              <div>
                <p className="text-sm font-medium mb-2">Ou escolha uma predefinida:</p>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  {predefined.map((img) => {
                    const isSelected = selectedPredefined === img.path;
                    return (
                      <button
                        key={img.path}
                        type="button"
                        onClick={() => selectPredefined(img.path)}
                        className={cn(
                          'relative overflow-hidden rounded-md border-2 transition-all aspect-video',
                          isSelected
                            ? 'border-primary ring-2 ring-primary/30'
                            : 'border-border hover:border-primary/60',
                        )}
                      >
                        <img
                          src={img.url}
                          alt={img.name}
                          className="size-full object-cover"
                          onError={(e) => {
                            (e.currentTarget as HTMLImageElement).style.display = 'none';
                          }}
                        />
                        {isSelected && (
                          <span className="absolute top-1 end-1 rounded-full bg-primary text-primary-foreground size-5 flex items-center justify-center">
                            <Check className="size-3" />
                          </span>
                        )}
                        <span className="absolute bottom-0 inset-x-0 bg-black/50 text-white text-[11px] px-1.5 py-0.5 truncate text-left">
                          {img.name}
                        </span>
                      </button>
                    );
                  })}
                </div>
              </div>
            </div>

            {/* Coluna direita: campos de texto */}
            <div className="lg:col-span-2 space-y-4">
              <div>
                <Label htmlFor="descricao" className="text-sm">Nome do Convento</Label>
                <Input
                  id="descricao"
                  value={descricao}
                  onChange={(e) => setDescricao(e.target.value)}
                  placeholder="Ex.: Convento Santo Antônio"
                  className="mt-1"
                  maxLength={255}
                />
                {formErrors.descricao && (
                  <p className="text-xs text-destructive mt-1">{formErrors.descricao}</p>
                )}
              </div>

              <div>
                <Label htmlFor="localidade" className="text-sm">Localidade</Label>
                <Input
                  id="localidade"
                  value={localidade}
                  onChange={(e) => setLocalidade(e.target.value)}
                  placeholder="Ex.: Recife - PE"
                  className="mt-1"
                  maxLength={255}
                />
                {formErrors.localidade && (
                  <p className="text-xs text-destructive mt-1">{formErrors.localidade}</p>
                )}
              </div>

              <div className="pt-2 flex flex-col gap-2">
                <Button type="submit" disabled={submitting}>
                  {submitting ? <Loader2 className="size-4 animate-spin" /> : <Check className="size-4" />}
                  Salvar imagem
                </Button>
                <Button type="button" variant="outline" onClick={resetForm} disabled={submitting}>
                  Limpar
                </Button>
              </div>
            </div>
          </form>
        </CardContent>
      </Card>

      {/* Dialog de edição */}
      <Dialog open={editing !== null} onOpenChange={(open) => !open && setEditing(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Editar imagem</DialogTitle>
            <DialogDescription>
              Atualize o nome do convento e a localidade da imagem selecionada.
            </DialogDescription>
          </DialogHeader>
          {editing && (
            <form onSubmit={handleUpdate} className="space-y-4">
              <div className="aspect-video rounded-md overflow-hidden border border-border bg-muted">
                <img
                  src={editing.imagem_url}
                  alt={editing.descricao}
                  className="size-full object-cover"
                />
              </div>
              <div>
                <Label htmlFor="edit-descricao" className="text-sm">Nome do Convento</Label>
                <Input
                  id="edit-descricao"
                  value={editDescricao}
                  onChange={(e) => setEditDescricao(e.target.value)}
                  className="mt-1"
                  maxLength={255}
                />
                {editErrors.descricao && (
                  <p className="text-xs text-destructive mt-1">{editErrors.descricao}</p>
                )}
              </div>
              <div>
                <Label htmlFor="edit-localidade" className="text-sm">Localidade</Label>
                <Input
                  id="edit-localidade"
                  value={editLocalidade}
                  onChange={(e) => setEditLocalidade(e.target.value)}
                  className="mt-1"
                  maxLength={255}
                />
                {editErrors.localidade && (
                  <p className="text-xs text-destructive mt-1">{editErrors.localidade}</p>
                )}
              </div>
              <DialogFooter>
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => setEditing(null)}
                  disabled={editSubmitting}
                >
                  Cancelar
                </Button>
                <Button type="submit" disabled={editSubmitting}>
                  {editSubmitting && <Loader2 className="size-4 animate-spin" />}
                  Salvar alterações
                </Button>
              </DialogFooter>
            </form>
          )}
        </DialogContent>
      </Dialog>

      {/* AlertDialog de remoção */}
      <AlertDialog open={toDelete !== null} onOpenChange={(open) => !open && setToDelete(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Remover imagem?</AlertDialogTitle>
            <AlertDialogDescription>
              {toDelete ? (
                <>
                  Você tem certeza que quer remover <strong>{toDelete.descricao}</strong> do carrossel
                  da tela de login? A imagem ficará inativa mas pode ser reativada depois no banco de dados.
                </>
              ) : null}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={deleting}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              variant="destructive"
              onClick={(e) => {
                e.preventDefault();
                handleDelete();
              }}
              disabled={deleting}
            >
              {deleting ? <Loader2 className="size-4 animate-spin" /> : <Trash2 className="size-4" />}
              Remover
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Container>
  );
}
