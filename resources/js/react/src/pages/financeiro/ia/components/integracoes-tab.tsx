import { Fragment, useCallback, useEffect, useState } from 'react';
import {
  MessageCircle,
  FileText,
  Mail,
  Trash2,
  Settings,
  Loader2,
  ChevronDown,
  ChevronRight,
  UsersRound,
  Clock,
  Plus,
  Hourglass,
} from 'lucide-react';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { notify } from '@/lib/notify';
import { WhatsappIntegracaoSheet } from './whatsapp-integracao-sheet';
import { WhatsappGrupoSheet } from './whatsapp-grupo-sheet';

/** Linhas retornadas pela API (tipos persistidos no tenant). */
interface IntegracaoApi {
  id: number;
  tipo: string;
  status: string;
  remetente: string | null;
  destinatario: string | null;
  cadastrado_por?: string | null;
  hora_notificacao?: string | null;
}

type DisplayTipo = 'whatsapp' | 'dda' | 'email' | 'grupo_whatsapp';
type DisplayStatus = 'configurado' | 'pendente' | 'nao_configurado';

interface IntegracaoRow {
  id: number | string;
  tipo: DisplayTipo;
  status: DisplayStatus;
  remetente: string | null;
  destinatario: string | null;
  cadastrado_por?: string | null;
  hora_notificacao?: string | null;
  synthetic: boolean;
}

const ROW_ORDER: DisplayTipo[] = ['whatsapp', 'dda', 'email', 'grupo_whatsapp'];

const TIPO_CONFIG: Record<DisplayTipo, { icon: React.ElementType; color: string; label: string }> = {
  whatsapp: { icon: MessageCircle, color: 'text-green-500', label: 'WhatsApp' },
  dda: { icon: FileText, color: 'text-primary', label: 'DDA' },
  email: { icon: Mail, color: 'text-blue-500', label: 'E-mail' },
  grupo_whatsapp: { icon: UsersRound, color: 'text-green-600', label: 'Grupo WhatsApp' },
};

/** Contato individual do "Grupo WhatsApp" (whatsapp_auth_requests kind='company_contact'). */
interface GrupoContatoApi {
  id: number;
  contact_label: string | null;
  wa_id: string | null;
  status: 'pending' | 'active';
  created_at?: string;
}

/** Garante id numérico vindo da API (JSON às vezes entrega string). */
function coerceIntegracaoId(id: unknown): number {
  const n = typeof id === 'number' ? id : parseInt(String(id), 10);
  return Number.isFinite(n) ? n : 0;
}

function apiToDisplayRow(r: IntegracaoApi): IntegracaoRow {
  const tipo = r.tipo as DisplayTipo;
  const statusNorm = String(r.status ?? '').toLowerCase().trim();
  const nonWhatsapp = tipo !== 'whatsapp';
  const status: DisplayStatus =
    statusNorm === 'configurado'
      ? 'configurado'
      : nonWhatsapp && statusNorm === 'pendente'
        ? 'nao_configurado'
        : 'pendente';

  return {
    id: coerceIntegracaoId(r.id),
    tipo,
    status,
    remetente: r.remetente,
    destinatario: r.destinatario,
    cadastrado_por: r.cadastrado_por ?? null,
    hora_notificacao: r.hora_notificacao ?? null,
    synthetic: false,
  };
}

function mergeIntegracaoRows(api: IntegracaoApi[]): IntegracaoRow[] {
  const byTipo = new Map<DisplayTipo, IntegracaoApi>();
  for (const r of api) {
    if (r.tipo === 'whatsapp' || r.tipo === 'dda' || r.tipo === 'email') {
      byTipo.set(r.tipo as DisplayTipo, r);
    }
  }

  return ROW_ORDER.map((tipo) => {
    const found = byTipo.get(tipo);
    if (found) return apiToDisplayRow(found);
    return {
      id: `synthetic-${tipo}`,
      tipo,
      status: 'nao_configurado',
      remetente: null,
      destinatario: null,
      synthetic: true,
    };
  });
}

function SituationBadge({ status }: { status: DisplayStatus }) {
  if (status === 'configurado') {
    return (
      <Badge variant="outline" className="bg-green-500/10 text-green-600 border-green-500/20 text-xs">
        Configurado
      </Badge>
    );
  }
  if (status === 'pendente') {
    return (
      <Badge variant="outline" className="bg-yellow-500/10 text-yellow-600 border-yellow-500/20 text-xs">
        Pendente
      </Badge>
    );
  }
  return (
    <Badge variant="outline" className="bg-muted text-muted-foreground border-border text-xs">
      Não configurado
    </Badge>
  );
}

export function IntegracoesTab() {
  const [rows, setRows] = useState<IntegracaoRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [loadError, setLoadError] = useState(false);
  const [whatsappSheetOpen, setWhatsappSheetOpen] = useState(false);
  const [deleteTarget, setDeleteTarget] = useState<{ rowId: number; tipoLabel: string } | null>(null);
  const [deleteInProgress, setDeleteInProgress] = useState(false);
  const [horarioDialogOpen, setHorarioDialogOpen] = useState(false);
  const [horarioSelecionado, setHorarioSelecionado] = useState('08:00');
  const [horarioSaving, setHorarioSaving] = useState(false);

  // Grupo WhatsApp (kind='company_contact') — múltiplos números por empresa.
  const [grupoSheetOpen, setGrupoSheetOpen] = useState(false);
  const [grupoContatos, setGrupoContatos] = useState<GrupoContatoApi[]>([]);
  const [grupoExpandido, setGrupoExpandido] = useState(false);
  const [grupoDeleteTarget, setGrupoDeleteTarget] = useState<GrupoContatoApi | null>(null);
  const [grupoDeleteInProgress, setGrupoDeleteInProgress] = useState(false);

  const loadContatosGrupo = useCallback(async () => {
    try {
      const res = await fetch('/integracoes/whatsapp-grupo', { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = (await res.json()) as { success?: boolean; data?: GrupoContatoApi[] };
      if (data.success && Array.isArray(data.data)) {
        setGrupoContatos(data.data);
      }
    } catch {
      // Silencioso: a aba ainda funciona, e a linha "Grupo WhatsApp" cai para o estado vazio.
      setGrupoContatos([]);
    }
  }, []);

  const loadIntegracoes = useCallback(async () => {
    setLoading(true);
    setLoadError(false);
    try {
      const res = await fetch('/integracoes', {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = (await res.json()) as {
        success?: boolean;
        data?: IntegracaoApi[];
        error?: string;
      };

      if (!data.success || !Array.isArray(data.data)) {
        throw new Error(data.error ?? 'Resposta inválida');
      }

      setRows(mergeIntegracaoRows(data.data));
    } catch {
      notify.error('Integrações', 'Não foi possível carregar a lista de integrações.');
      setRows([]);
      setLoadError(true);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void loadIntegracoes();
    void loadContatosGrupo();
  }, [loadIntegracoes, loadContatosGrupo]);

  const openWhatsappSetup = useCallback(() => {
    setWhatsappSheetOpen(true);
  }, []);

  const openGrupoSetup = useCallback(() => {
    setGrupoSheetOpen(true);
  }, []);

  const handleGrupoSuccess = useCallback(() => {
    void loadContatosGrupo();
    setGrupoExpandido(true);
  }, [loadContatosGrupo]);

  const executeDeleteGrupoContato = useCallback(async () => {
    if (!grupoDeleteTarget) return;
    setGrupoDeleteInProgress(true);
    try {
      const csrfEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
      const res = await fetch(`/integracoes/whatsapp-grupo/${grupoDeleteTarget.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfEl?.content ?? '',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });
      const data = (await res.json()) as { success?: boolean; error?: string };
      if (res.ok && data.success) {
        notify.success('Grupo WhatsApp', 'Contato removido.');
        setGrupoDeleteTarget(null);
        await loadContatosGrupo();
      } else {
        notify.error('Erro', data.error ?? 'Erro ao remover contato.');
      }
    } catch {
      notify.error('Grupo WhatsApp', 'Erro ao remover contato.');
    } finally {
      setGrupoDeleteInProgress(false);
    }
  }, [grupoDeleteTarget, loadContatosGrupo]);

  const handleConfigure = useCallback(
    (integ: IntegracaoRow) => {
      if (integ.tipo === 'whatsapp' && (integ.status === 'pendente' || integ.status === 'nao_configurado')) {
        openWhatsappSetup();
        return;
      }
      notify.info('Integrações', 'Esta integração estará disponível em breve.');
    },
    [openWhatsappSetup],
  );

  const openDeleteConfirm = useCallback((integ: IntegracaoRow) => {
    const rowId = typeof integ.id === 'number' ? integ.id : coerceIntegracaoId(integ.id);
    if (!rowId || integ.synthetic) return;
    setDeleteTarget({ rowId, tipoLabel: TIPO_CONFIG[integ.tipo].label });
  }, []);

  const openHorarioDialog = useCallback((integ: IntegracaoRow) => {
    setHorarioSelecionado(integ.hora_notificacao ?? '08:00');
    setHorarioDialogOpen(true);
  }, []);

  const saveHorario = useCallback(async () => {
    setHorarioSaving(true);
    try {
      const csrfEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
      const res = await fetch('/integracoes/whatsapp/horario', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfEl?.content ?? '',
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ hora: horarioSelecionado }),
      });
      const data = (await res.json()) as { success?: boolean; error?: string; hora_notificacao?: string };
      if (res.ok && data.success) {
        notify.success('Horário atualizado', `Notificações serão enviadas às ${data.hora_notificacao ?? horarioSelecionado}.`);
        setHorarioDialogOpen(false);
        void loadIntegracoes();
      } else {
        notify.error('Erro', data.error ?? 'Não foi possível salvar o horário.');
      }
    } catch {
      notify.error('Erro ao salvar horário de notificação.');
    } finally {
      setHorarioSaving(false);
    }
  }, [horarioSelecionado, loadIntegracoes]);

  const executeDelete = useCallback(async () => {
    if (!deleteTarget) return;
    const { rowId } = deleteTarget;
    setDeleteInProgress(true);
    try {
      const csrfEl = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
      const res = await fetch(`/integracoes/${rowId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfEl?.content ?? '',
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });
      const data = (await res.json()) as { success?: boolean; error?: string };
      if (res.ok && data.success) {
        notify.success('Integração excluída');
        setDeleteTarget(null);
        void loadIntegracoes();
      } else {
        notify.error('Erro', data.error ?? 'Erro ao excluir');
      }
    } catch {
      notify.error('Erro ao excluir integração');
    } finally {
      setDeleteInProgress(false);
    }
  }, [deleteTarget, loadIntegracoes]);

  return (
    <Card>
      <div className="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-border">
        <h2 className="text-base font-semibold">Integrações</h2>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button size="sm" className="gap-1.5">
              Nova integração
              <ChevronDown className="size-4 opacity-70" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuItem onSelect={openWhatsappSetup}>WhatsApp</DropdownMenuItem>
            <DropdownMenuItem
              onSelect={() => notify.info('Integrações', 'Integração DDA em breve.')}
            >
              DDA
            </DropdownMenuItem>
            <DropdownMenuItem
              onSelect={() => notify.info('Integrações', 'Integração E-mail em breve.')}
            >
              E-mail
            </DropdownMenuItem>
            <DropdownMenuItem onSelect={openGrupoSetup}>Grupo WhatsApp</DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>

      <div className="p-0">
        {loading ? (
          <div className="flex items-center justify-center py-16">
            <Loader2 className="size-6 animate-spin text-muted-foreground" />
          </div>
        ) : loadError && rows.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-16 gap-3 text-muted-foreground">
            <p className="font-medium">Não foi possível carregar as integrações.</p>
            <Button size="sm" variant="outline" onClick={() => void loadIntegracoes()}>
              Tentar novamente
            </Button>
          </div>
        ) : (
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead className="w-[160px]">Integração</TableHead>
                <TableHead className="w-[130px]">Situação</TableHead>
                <TableHead>Remetente</TableHead>
                <TableHead>Destinatário</TableHead>
                <TableHead className="text-right w-[120px]">Ações</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {rows.map((integ) => {
                const cfg = TIPO_CONFIG[integ.tipo];
                const Icon = cfg.icon;

                if (integ.tipo === 'grupo_whatsapp') {
                  // Linha sintética: nunca há registro em `integracoes_api`. O
                  // estado/quantidade vem de `grupoContatos` (whatsapp_auth_requests).
                  const ativos = grupoContatos.filter((c) => c.status === 'active');
                  const pendentes = grupoContatos.filter((c) => c.status === 'pending');
                  const totalAtivos = ativos.length;
                  const totalPendentes = pendentes.length;
                  const grupoStatus: DisplayStatus =
                    totalAtivos > 0 ? 'configurado' : totalPendentes > 0 ? 'pendente' : 'nao_configurado';

                  return (
                    <Fragment key={String(integ.id)}>
                      <TableRow>
                        <TableCell>
                          <span className="flex items-center gap-2 font-medium">
                            {totalAtivos + totalPendentes > 0 ? (
                              <button
                                type="button"
                                onClick={() => setGrupoExpandido((s) => !s)}
                                className="rounded hover:bg-muted/60 p-0.5 -ml-0.5"
                                aria-label={grupoExpandido ? 'Recolher contatos' : 'Expandir contatos'}
                              >
                                {grupoExpandido ? (
                                  <ChevronDown className="size-3.5" />
                                ) : (
                                  <ChevronRight className="size-3.5" />
                                )}
                              </button>
                            ) : (
                              <span className="inline-block size-3.5 -ml-0.5" aria-hidden />
                            )}
                            <Icon className={`size-4 ${cfg.color}`} />
                            {cfg.label}
                          </span>
                        </TableCell>
                        <TableCell>
                          <SituationBadge status={grupoStatus} />
                        </TableCell>
                        <TableCell className="text-muted-foreground">
                          {totalAtivos === 0 && totalPendentes === 0 ? (
                            <span>—</span>
                          ) : (
                            <div className="flex flex-wrap items-center gap-2 text-xs">
                              <span className="font-medium text-foreground">
                                {totalAtivos} {totalAtivos === 1 ? 'contato' : 'contatos'}
                              </span>
                              {totalPendentes > 0 ? (
                                <span className="text-muted-foreground">
                                  · <Hourglass className="inline size-3 mr-0.5" />
                                  {totalPendentes} pendente{totalPendentes > 1 ? 's' : ''}
                                </span>
                              ) : null}
                            </div>
                          )}
                        </TableCell>
                        <TableCell className="text-muted-foreground">Empresa ativa</TableCell>
                        <TableCell className="text-right">
                          <div className="flex justify-end items-center gap-1 flex-wrap">
                            <Button
                              size="sm"
                              variant="outline"
                              className="gap-1.5 h-7 text-xs"
                              onClick={openGrupoSetup}
                            >
                              <Plus className="size-3" />
                              Adicionar contato
                            </Button>
                          </div>
                        </TableCell>
                      </TableRow>

                      {grupoExpandido && (totalAtivos + totalPendentes > 0) ? (
                        grupoContatos.map((contato) => (
                          <TableRow key={`grupo-contato-${contato.id}`} className="bg-muted/30">
                            <TableCell className="pl-12 text-sm">
                              <span className="text-muted-foreground">↳</span>{' '}
                              <span className="font-medium">{contato.contact_label ?? 'Contato'}</span>
                            </TableCell>
                            <TableCell>
                              <SituationBadge
                                status={contato.status === 'active' ? 'configurado' : 'pendente'}
                              />
                            </TableCell>
                            <TableCell className="text-muted-foreground text-xs">
                              {contato.wa_id ? (
                                <span className="font-mono">{contato.wa_id}</span>
                              ) : (
                                <span className="italic">aguardando QR…</span>
                              )}
                            </TableCell>
                            <TableCell className="text-muted-foreground text-xs">—</TableCell>
                            <TableCell className="text-right">
                              <Button
                                type="button"
                                size="icon"
                                variant="ghost"
                                className="size-7 shrink-0 text-destructive hover:bg-destructive/10 hover:text-destructive"
                                aria-label="Remover contato do grupo"
                                title="Remover contato do grupo"
                                onClick={() => setGrupoDeleteTarget(contato)}
                              >
                                <Trash2 className="size-3.5" />
                              </Button>
                            </TableCell>
                          </TableRow>
                        ))
                      ) : null}
                    </Fragment>
                  );
                }

                return (
                  <TableRow key={String(integ.id)}>
                    <TableCell>
                      <span className="flex items-center gap-2 font-medium">
                        <Icon className={`size-4 ${cfg.color}`} />
                        {cfg.label}
                      </span>
                    </TableCell>
                    <TableCell>
                      <SituationBadge status={integ.status} />
                    </TableCell>
                    <TableCell className="text-muted-foreground">
                      <div>
                        <span>{integ.remetente ?? '—'}</span>
                        {integ.cadastrado_por ? (
                          <div className="text-xs text-muted-foreground/80 mt-0.5">{integ.cadastrado_por}</div>
                        ) : null}
                      </div>
                    </TableCell>
                    <TableCell className="text-muted-foreground">{integ.destinatario ?? '—'}</TableCell>
                    <TableCell className="text-right">
                      {(() => {
                        const rowId =
                          typeof integ.id === 'number' ? integ.id : coerceIntegracaoId(integ.id);
                        const hasApiId = !integ.synthetic && rowId > 0;
                        const canExcluir =
                          hasApiId &&
                          (integ.status === 'configurado' ||
                            (integ.tipo === 'whatsapp' && integ.status === 'pendente'));
                        const canConfigurar =
                          integ.status === 'nao_configurado' ||
                          (integ.tipo === 'whatsapp' && integ.status === 'pendente');
                        const canHorario =
                          integ.tipo === 'whatsapp' && integ.status === 'configurado';
                        return (
                          <div className="flex justify-end items-center gap-1 flex-wrap">
                            {canConfigurar ? (
                              <Button
                                size="sm"
                                variant="outline"
                                className="gap-1.5 h-7 text-xs"
                                onClick={() => handleConfigure(integ)}
                              >
                                <Settings className="size-3" />
                                Configurar
                              </Button>
                            ) : null}
                            {canHorario ? (
                              <Button
                                type="button"
                                size="icon"
                                variant="ghost"
                                className="size-7 shrink-0 text-muted-foreground hover:bg-muted"
                                aria-label="Configurar horário de notificação"
                                title={`Horário de notificação: ${integ.hora_notificacao ?? '08:00'}`}
                                onClick={() => openHorarioDialog(integ)}
                              >
                                <Clock className="size-3.5" />
                              </Button>
                            ) : null}
                            {canExcluir ? (
                              <Button
                                type="button"
                                size="icon"
                                variant="ghost"
                                className="size-7 shrink-0 text-destructive hover:bg-destructive/10 hover:text-destructive"
                                aria-label="Excluir integração"
                                title="Excluir integração"
                                onClick={() => openDeleteConfirm(integ)}
                              >
                                <Trash2 className="size-3.5" />
                              </Button>
                            ) : null}
                          </div>
                        );
                      })()}
                    </TableCell>
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>
        )}
      </div>

      <AlertDialog
        open={deleteTarget !== null}
        onOpenChange={(open) => {
          if (!open && !deleteInProgress) setDeleteTarget(null);
        }}
      >
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Excluir integração?</AlertDialogTitle>
            <AlertDialogDescription>
              Tem certeza que deseja excluir a integração{' '}
              <span className="font-medium text-foreground">{deleteTarget?.tipoLabel}</span>? Esta ação não pode ser
              desfeita.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={deleteInProgress}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              className="bg-destructive hover:bg-destructive/90 text-white"
              disabled={deleteInProgress}
              onClick={(e) => {
                e.preventDefault();
                void executeDelete();
              }}
            >
              {deleteInProgress ? (
                <>
                  <Loader2 className="size-4 animate-spin inline-block mr-2 align-middle" />
                  Excluindo…
                </>
              ) : (
                'Excluir'
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      <Dialog open={horarioDialogOpen} onOpenChange={(open) => { if (!horarioSaving) setHorarioDialogOpen(open); }}>
        <DialogContent className="sm:max-w-xs">
          <DialogHeader>
            <DialogTitle>Horário de notificação WhatsApp</DialogTitle>
            <DialogDescription>
              Escolha o horário em que as notificações de contas vencendo serão enviadas diariamente.
            </DialogDescription>
          </DialogHeader>
          <div className="py-2">
            <Select value={horarioSelecionado} onValueChange={setHorarioSelecionado}>
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Selecionar horário" />
              </SelectTrigger>
              <SelectContent>
                {Array.from({ length: 17 }, (_, i) => {
                  const h = String(i + 6).padStart(2, '0');
                  return `${h}:00`;
                }).map((h) => (
                  <SelectItem key={h} value={h}>{h}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <DialogFooter>
            <Button variant="outline" size="sm" disabled={horarioSaving} onClick={() => setHorarioDialogOpen(false)}>
              Cancelar
            </Button>
            <Button size="sm" disabled={horarioSaving} onClick={() => void saveHorario()}>
              {horarioSaving ? <Loader2 className="size-4 animate-spin mr-2" /> : null}
              Salvar
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <WhatsappIntegracaoSheet
        open={whatsappSheetOpen}
        onOpenChange={setWhatsappSheetOpen}
        onSuccess={() => void loadIntegracoes()}
      />

      <WhatsappGrupoSheet
        open={grupoSheetOpen}
        onOpenChange={setGrupoSheetOpen}
        onSuccess={handleGrupoSuccess}
      />

      <AlertDialog
        open={grupoDeleteTarget !== null}
        onOpenChange={(open) => {
          if (!open && !grupoDeleteInProgress) setGrupoDeleteTarget(null);
        }}
      >
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Remover contato do Grupo WhatsApp?</AlertDialogTitle>
            <AlertDialogDescription>
              Tem certeza que deseja remover{' '}
              <span className="font-medium text-foreground">
                {grupoDeleteTarget?.contact_label ?? 'este contato'}
              </span>
              {grupoDeleteTarget?.wa_id ? (
                <>
                  {' '}(<span className="font-mono">{grupoDeleteTarget.wa_id}</span>)
                </>
              ) : null}
              ? A pessoa não receberá mais notificações financeiras nesta empresa.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={grupoDeleteInProgress}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              className="bg-destructive hover:bg-destructive/90 text-white"
              disabled={grupoDeleteInProgress}
              onClick={(e) => {
                e.preventDefault();
                void executeDeleteGrupoContato();
              }}
            >
              {grupoDeleteInProgress ? (
                <>
                  <Loader2 className="size-4 animate-spin inline-block mr-2 align-middle" />
                  Removendo…
                </>
              ) : (
                'Remover'
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Card>
  );
}
