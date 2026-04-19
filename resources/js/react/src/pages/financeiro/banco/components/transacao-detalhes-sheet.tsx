import { useCallback, useEffect, useState } from 'react';
import type { LucideIcon } from 'lucide-react';
import {
  ExternalLink,
  FileArchive,
  FileAudio,
  FileSpreadsheet,
  FileText,
  FileVideo,
  GitFork,
  ImageIcon,
  Layers,
  Link2,
  Loader2,
  Paperclip,
  Pencil,
  Receipt,
} from 'lucide-react';
import { notify } from '@/lib/notify';
import {
  Sheet,
  SheetBody,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import { fmtCurrency, SITUACAO_VARIANT, type OnEditTransacao } from '@/pages/financeiro/components/transacao-table-shared';
import type { TipoLancamento } from '@/pages/financeiro/banco/components/lancamento-drawer';
import {
  buildGerarReciboContext,
  GerarReciboDialog,
} from '@/pages/financeiro/banco/components/gerar-recibo-dialog';
import { cn } from '@/lib/utils';
import { Item, ItemContent, ItemDescription, ItemGroup, ItemMedia, ItemTitle } from '@/components/ui/item';

// ── Tipos (espelham o JSON de `BancoController::getDetalhes`) ───────────────────

interface TransacaoDetalheAnexo {
  id: number;
  nome: string;
  url: string;
  forma_anexo?: string;
  tipo_anexo?: string | null;
  extensao?: string | null;
  /** API pode enviar número (bytes) ou string formatada */
  tamanho?: string | number | null;
  descricao?: string | null;
}

function extensaoDoAnexo(a: TransacaoDetalheAnexo): string {
  const raw = (a.extensao ?? '').toLowerCase().replace(/^\./, '');
  if (raw) return raw;
  const nome = a.nome ?? '';
  const i = nome.lastIndexOf('.');
  if (i >= 0 && i < nome.length - 1) return nome.slice(i + 1).toLowerCase();
  return '';
}

/** Ícone e cor por tipo de arquivo / link (sem Next/Image — Lucide no `ItemMedia`). */
function iconeAnexo(a: TransacaoDetalheAnexo): { Icon: LucideIcon; iconClass: string } {
  const forma = (a.forma_anexo ?? '').toLowerCase();
  if (forma === 'link') {
    return { Icon: Link2, iconClass: 'text-sky-600 dark:text-sky-400' };
  }

  const e = extensaoDoAnexo(a);
  const img = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp', 'ico', 'heic'];
  if (img.includes(e)) return { Icon: ImageIcon, iconClass: 'text-violet-600 dark:text-violet-400' };
  if (e === 'pdf') return { Icon: FileText, iconClass: 'text-red-600 dark:text-red-400' };
  const plan = ['xls', 'xlsx', 'csv', 'ods'];
  if (plan.includes(e)) return { Icon: FileSpreadsheet, iconClass: 'text-emerald-600 dark:text-emerald-400' };
  const arch = ['zip', 'rar', '7z', 'tar', 'gz'];
  if (arch.includes(e)) return { Icon: FileArchive, iconClass: 'text-amber-600 dark:text-amber-400' };
  const vid = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
  if (vid.includes(e)) return { Icon: FileVideo, iconClass: 'text-fuchsia-600 dark:text-fuchsia-400' };
  const aud = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
  if (aud.includes(e)) return { Icon: FileAudio, iconClass: 'text-orange-600 dark:text-orange-400' };
  if (e === 'doc' || e === 'docx' || e === 'odt' || e === 'rtf' || e === 'txt' || e === 'md') {
    return { Icon: FileText, iconClass: 'text-blue-600 dark:text-blue-400' };
  }

  return { Icon: Paperclip, iconClass: 'text-muted-foreground' };
}

function strAnexoMeta(v: string | number | null | undefined): string {
  if (v == null) return '';
  return String(v).trim();
}

function metaLinhaAnexo(a: TransacaoDetalheAnexo): string | null {
  const desc = strAnexoMeta(a.descricao);
  const tipo = strAnexoMeta(a.tipo_anexo);
  const tam = strAnexoMeta(a.tamanho);
  const parts = [tipo, tam].filter(Boolean);
  const base = parts.length ? parts.join(' · ') : null;
  if (desc && base) return `${base} — ${desc}`;
  if (desc) return desc;
  return base;
}

interface TransacaoDetalheParcela {
  numero_parcela: number;
  total_parcelas: number;
  data_vencimento: string;
  valor: number;
  situacao: string | null;
  valor_pago: number;
  descricao?: string | null;
  entidade_nome: string;
}

interface TransacaoDetalheParcelaInfo {
  numero_parcela: number;
  total_parcelas: number;
  parent_id: number;
  parent_descricao: string | null;
}

interface TransacaoDetalheRateio {
  filial_nome: string | null;
  centro_custo: string | null;
  categoria: string | null;
  valor: number;
  percentual: number | null;
}

interface TransacaoDetalheRateioGerado {
  id: number;
  descricao: string;
  valor: number;
  situacao: string | null;
  filial_nome: string | null;
  data_vencimento_formatada: string | null;
}

export interface TransacaoDetalhesApi {
  id: number;
  descricao: string;
  tipo: string;
  valor: number;
  situacao: string;
  agendado: boolean;
  data_competencia_formatada: string | null;
  data_vencimento_formatada: string | null;
  data_pagamento_formatada: string | null;
  valor_pago: number | null;
  juros: number | null;
  multa: number | null;
  desconto: number | null;
  lancamento_padrao: string | null;
  tipo_documento: string | null;
  numero_documento: string | null;
  comprovacao_fiscal: string;
  origem: string | null;
  entidade_financeira: string | null;
  centro_custo: string | null;
  parceiro_nome: string | null;
  historico_complementar: string | null;
  created_by_name: string | null;
  updated_by_name: string | null;
  created_at_formatado: string;
  updated_at_formatado: string;
  anexos: TransacaoDetalheAnexo[];
  parcela_info: TransacaoDetalheParcelaInfo | null;
  is_parcelado: boolean;
  parent_id: number | null;
  parcelas: TransacaoDetalheParcela[];
  rateios?: TransacaoDetalheRateio[];
  rateios_gerados?: TransacaoDetalheRateioGerado[];
  recibo?: {
    id: number;
    nome: string | null;
    cpf_cnpj: string | null;
    referente: string | null;
    address?: {
      cep: string | null;
      rua: string | null;
      numero: string | null;
      bairro: string | null;
      complemento: string | null;
      cidade: string | null;
      uf: string | null;
    } | null;
  } | null;
  parceiro?: {
    id: number;
    nome: string | null;
    nome_fantasia: string | null;
    cpf_cnpj: string | null;
    address?: {
      cep: string | null;
      rua: string | null;
      numero: string | null;
      bairro: string | null;
      complemento: string | null;
      cidade: string | null;
      uf: string | null;
    } | null;
  } | null;
}

function SectionTitle({ children, icon: Icon }: { children: React.ReactNode; icon?: React.ComponentType<{ className?: string }> }) {
  return (
    <h3 className="text-sm font-semibold text-foreground mb-3 flex items-center gap-2">
      {Icon && <Icon className="size-4 text-muted-foreground shrink-0" />}
      {children}
    </h3>
  );
}

function Field({ label, value, className }: { label: string; value: React.ReactNode; className?: string }) {
  return (
    <div className={cn('space-y-0.5', className)}>
      <div className="text-[0.7rem] font-medium uppercase tracking-wide text-muted-foreground">{label}</div>
      <div className="text-sm text-foreground break-words">{value ?? '—'}</div>
    </div>
  );
}

function DashSep() {
  return <div className="border-t border-dashed border-border my-5" />;
}

export interface TransacaoDetalhesSheetProps {
  open: boolean;
  transacaoId: string | null;
  onClose: () => void;
  onEdit?: OnEditTransacao;
}

export function TransacaoDetalhesSheet({ open, transacaoId, onClose, onEdit }: TransacaoDetalhesSheetProps) {
  const [loading, setLoading] = useState(false);
  const [d, setD] = useState<TransacaoDetalhesApi | null>(null);
  const [reciboDialogOpen, setReciboDialogOpen] = useState(false);

  const load = useCallback(async () => {
    if (!transacaoId) return;
    setLoading(true);
    try {
      const res = await fetch(`/financeiro/transacao/${transacaoId}/detalhes`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('Falha ao carregar');
      const json = (await res.json()) as TransacaoDetalhesApi;
      setD(json);
    } catch {
      notify.error('Erro', 'Não foi possível carregar os detalhes do lançamento.');
      onClose();
    } finally {
      setLoading(false);
    }
  }, [transacaoId, onClose]);

  useEffect(() => {
    if (open && transacaoId) {
      void load();
    }
    if (!open) {
      setD(null);
    }
  }, [open, transacaoId, load]);

  const isEntrada = d?.tipo === 'entrada';
  const tipoLancamento: TipoLancamento = isEntrada ? 'receita' : 'despesa';
  const rateios = d?.rateios ?? [];
  const rateiosGerados = d?.rateios_gerados ?? [];

  function handleEditar() {
    if (!transacaoId || !onEdit) return;
    onEdit(transacaoId, { tipo: tipoLancamento });
    onClose();
  }

  function handleAbrirPdfRecibo() {
    if (!d?.recibo) return;
    window.open(`/relatorios/recibo/imprimir/${d.recibo.id}`, '_blank', 'noopener,noreferrer');
    notify.success('Recibo', 'Abrindo o PDF…');
  }

  return (
    <Sheet open={open} onOpenChange={(v) => !v && onClose()}>
      <SheetContent className="sm:max-w-xl w-full flex flex-col p-0 gap-0" side="right" aria-describedby={undefined}>
        <SheetHeader className="px-6 pt-6 pb-2 shrink-0 border-b border-border">
          <SheetTitle className="text-start">Detalhes da transação</SheetTitle>
        </SheetHeader>

        <SheetBody className="flex-1 min-h-0 px-0 py-0">
          {loading && (
            <div className="flex items-center justify-center gap-2 py-16 text-muted-foreground">
              <Loader2 className="size-5 animate-spin" />
              <span className="text-sm">Carregando…</span>
            </div>
          )}

          {!loading && d && (
            <ScrollArea className="h-[calc(100vh-8rem)]">
              <div className="px-6 pb-4 space-y-1">
                <div className="flex flex-wrap items-start gap-2 pt-2">
                  <Badge variant="secondary" className={cn(isEntrada && 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400')}>
                    {isEntrada ? 'Receita' : 'Despesa'}
                  </Badge>
                  {d.agendado && <Badge variant="outline">Agendado</Badge>}
                  <Badge variant={SITUACAO_VARIANT[d.situacao] ?? 'secondary'} className="capitalize">
                    {d.situacao?.replace(/_/g, ' ') ?? d.situacao}
                  </Badge>
                </div>

                <div className="mt-3">
                  <p className="text-base font-semibold leading-snug">{d.descricao || '—'}</p>
                  <p className="text-xs text-muted-foreground mt-1">#{d.id}</p>
                </div>

                <p className="text-2xl font-bold tabular-nums mt-2">{fmtCurrency(d.valor)}</p>
                {d.valor_pago != null && d.valor_pago > 0 && (
                  <p className="text-sm text-muted-foreground">
                    Valor pago: <span className="font-medium text-foreground tabular-nums">{fmtCurrency(d.valor_pago)}</span>
                  </p>
                )}

                <DashSep />

                <SectionTitle>Informações principais</SectionTitle>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Field label="Competência" value={d.data_competencia_formatada} />
                  <Field label="Vencimento" value={d.data_vencimento_formatada} />
                  <Field label="Pagamento" value={d.data_pagamento_formatada} />
                  <Field label="Categoria" value={d.lancamento_padrao} />
                </div>
                {(d.juros != null && d.juros > 0) ||
                (d.multa != null && d.multa > 0) ||
                (d.desconto != null && d.desconto > 0) ? (
                  <div className="grid grid-cols-3 gap-3 mt-3">
                    <Field label="Juros" value={d.juros != null && d.juros > 0 ? fmtCurrency(d.juros) : '—'} />
                    <Field label="Multa" value={d.multa != null && d.multa > 0 ? fmtCurrency(d.multa) : '—'} />
                    <Field label="Desconto" value={d.desconto != null && d.desconto > 0 ? fmtCurrency(d.desconto) : '—'} />
                  </div>
                ) : null}

                <DashSep />

                <SectionTitle>Financeiro</SectionTitle>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Field label="Entidade" value={d.entidade_financeira} />
                  <Field label="Origem" value={d.origem} />
                  <Field label="Centro de custo" value={d.centro_custo} />
                  <Field label={isEntrada ? 'Cliente' : 'Fornecedor'} value={d.parceiro_nome} />
                </div>

                <DashSep />

                <SectionTitle>Documento</SectionTitle>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Field label="Forma / tipo documento" value={d.tipo_documento} />
                  <Field label="Número" value={d.numero_documento} />
                  <Field label="Comprovação fiscal" value={d.comprovacao_fiscal} />
                </div>

                {d.historico_complementar ? (
                  <>
                    <DashSep />
                    <SectionTitle>Histórico complementar</SectionTitle>
                    <p className="text-sm bg-muted/50 rounded-md p-3 whitespace-pre-wrap">{d.historico_complementar}</p>
                  </>
                ) : null}

                <DashSep />

                <SectionTitle icon={Paperclip}>
                  Anexos
                  {d.anexos?.length ? (
                    <span className="text-muted-foreground font-normal">({d.anexos.length})</span>
                  ) : null}
                </SectionTitle>
                {!d.anexos?.length && <p className="text-sm text-muted-foreground">Nenhum anexo.</p>}
                {!!d.anexos?.length && (
                  <ItemGroup className="gap-3">
                    {d.anexos.map((a) => {
                      const { Icon, iconClass } = iconeAnexo(a);
                      const meta = metaLinhaAnexo(a);
                      const titulo = a.nome || `Anexo #${a.id}`;
                      return (
                        <Item key={a.id} variant="outline" asChild role="listitem">
                          <a
                            href={a.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="group no-underline hover:bg-muted/60"
                          >
                            <ItemMedia variant="icon">
                              <Icon className={cn('size-5', iconClass)} aria-hidden />
                            </ItemMedia>
                            <ItemContent>
                              <ItemTitle className="line-clamp-2 break-words text-foreground group-hover:text-primary">
                                {titulo}
                              </ItemTitle>
                              {meta ? (
                                <ItemDescription className="line-clamp-2">{meta}</ItemDescription>
                              ) : (
                                <ItemDescription className="text-primary/80">Abrir em nova aba</ItemDescription>
                              )}
                            </ItemContent>
                          </a>
                        </Item>
                      );
                    })}
                  </ItemGroup>
                )}

                {d.parcela_info ? (
                  <>
                    <DashSep />
                    <SectionTitle icon={Layers}>Parcelamento</SectionTitle>
                    <div className="rounded-lg border border-primary/30 bg-primary/5 px-3 py-2.5 text-sm">
                      Parcela <strong>{d.parcela_info.numero_parcela}</strong> de{' '}
                      <strong>{d.parcela_info.total_parcelas}</strong>
                      {d.parcela_info.parent_descricao ? (
                        <>
                          {' '}
                          · Lançamento pai: <span className="font-medium">{d.parcela_info.parent_descricao}</span>
                        </>
                      ) : null}
                    </div>
                  </>
                ) : null}

                {d.is_parcelado && d.parcelas?.length ? (
                  <>
                    <DashSep />
                    <SectionTitle icon={Layers}>Parcelas</SectionTitle>
                    <div className="rounded-md border border-border overflow-x-auto">
                      <table className="w-full text-xs">
                        <thead>
                          <tr className="border-b border-border bg-muted/40 text-left">
                            <th className="p-2 font-medium">#</th>
                            <th className="p-2 font-medium">Venc.</th>
                            <th className="p-2 font-medium text-end">Valor</th>
                            <th className="p-2 font-medium">Situação</th>
                            <th className="p-2 font-medium hidden sm:table-cell">Conta</th>
                          </tr>
                        </thead>
                        <tbody>
                          {d.parcelas.map((p, idx) => (
                            <tr key={`${p.numero_parcela}-${idx}`} className="border-b border-border/80 last:border-0">
                              <td className="p-2 tabular-nums">
                                {p.numero_parcela}/{p.total_parcelas}
                              </td>
                              <td className="p-2 whitespace-nowrap">{p.data_vencimento}</td>
                              <td className="p-2 text-end tabular-nums">{fmtCurrency(p.valor)}</td>
                              <td className="p-2 capitalize">{p.situacao?.replace(/_/g, ' ') ?? '—'}</td>
                              <td className="p-2 hidden sm:table-cell text-muted-foreground">{p.entidade_nome}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </>
                ) : null}

                {rateios.length > 0 ? (
                  <>
                    <DashSep />
                    <SectionTitle icon={GitFork}>Rateio (definição)</SectionTitle>
                    <div className="rounded-md border border-border overflow-x-auto">
                      <table className="w-full text-xs">
                        <thead>
                          <tr className="border-b border-border bg-muted/40 text-left">
                            <th className="p-2 font-medium">Filial</th>
                            <th className="p-2 font-medium">Categoria</th>
                            <th className="p-2 font-medium hidden sm:table-cell">Centro</th>
                            <th className="p-2 font-medium text-end">Valor</th>
                            <th className="p-2 font-medium text-end hidden sm:table-cell">%</th>
                          </tr>
                        </thead>
                        <tbody>
                          {rateios.map((r, idx) => (
                            <tr key={idx} className="border-b border-border/80 last:border-0">
                              <td className="p-2">{r.filial_nome ?? '—'}</td>
                              <td className="p-2">{r.categoria ?? '—'}</td>
                              <td className="p-2 hidden sm:table-cell text-muted-foreground">{r.centro_custo ?? '—'}</td>
                              <td className="p-2 text-end tabular-nums">{fmtCurrency(r.valor)}</td>
                              <td className="p-2 text-end hidden sm:table-cell tabular-nums">
                                {r.percentual != null ? `${r.percentual}%` : '—'}
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </>
                ) : null}

                {rateiosGerados.length > 0 ? (
                  <>
                    <DashSep />
                    <SectionTitle icon={GitFork}>Lançamentos gerados pelo rateio</SectionTitle>
                    <div className="rounded-md border border-border overflow-x-auto">
                      <table className="w-full text-xs">
                        <thead>
                          <tr className="border-b border-border bg-muted/40 text-left">
                            <th className="p-2 font-medium">Filial</th>
                            <th className="p-2 font-medium">Descrição</th>
                            <th className="p-2 font-medium">Venc.</th>
                            <th className="p-2 font-medium text-end">Valor</th>
                            <th className="p-2 font-medium">Situação</th>
                          </tr>
                        </thead>
                        <tbody>
                          {rateiosGerados.map((r) => (
                            <tr key={r.id} className="border-b border-border/80 last:border-0">
                              <td className="p-2">{r.filial_nome ?? '—'}</td>
                              <td className="p-2 max-w-[140px] truncate" title={r.descricao}>
                                {r.descricao}
                              </td>
                              <td className="p-2 whitespace-nowrap">{r.data_vencimento_formatada ?? '—'}</td>
                              <td className="p-2 text-end tabular-nums">{fmtCurrency(r.valor)}</td>
                              <td className="p-2 capitalize">{r.situacao?.replace(/_/g, ' ') ?? '—'}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </>
                ) : null}

                <DashSep />

                <SectionTitle>Auditoria</SectionTitle>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                  <div>
                    <div className="text-[0.7rem] font-medium uppercase text-muted-foreground">Criado por</div>
                    <div>{d.created_by_name ?? '—'}</div>
                    <div className="text-xs text-muted-foreground mt-0.5">{d.created_at_formatado}</div>
                  </div>
                  <div>
                    <div className="text-[0.7rem] font-medium uppercase text-muted-foreground">Atualizado por</div>
                    <div>{d.updated_by_name ?? '—'}</div>
                    <div className="text-xs text-muted-foreground mt-0.5">{d.updated_at_formatado}</div>
                  </div>
                </div>
              </div>
            </ScrollArea>
          )}
        </SheetBody>

        {!loading && d && (
          <SheetFooter className="px-6 py-4 border-t border-border shrink-0 flex flex-col sm:flex-row flex-wrap gap-2">
            {onEdit && (
              <Button type="button" variant="outline" className="w-full sm:w-auto" onClick={handleEditar}>
                <Pencil className="size-4" />
                Editar lançamento
              </Button>
            )}
            {d.recibo ? (
              <Button type="button" className="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white border-0" onClick={handleAbrirPdfRecibo}>
                <ExternalLink className="size-4" />
                Abrir recibo
              </Button>
            ) : (
              <Button
                type="button"
                variant="secondary"
                className="w-full sm:w-auto"
                onClick={() => setReciboDialogOpen(true)}
              >
                <Receipt className="size-4" />
                Gerar recibo
              </Button>
            )}
          </SheetFooter>
        )}
      </SheetContent>

      {transacaoId && d && !d.recibo && (
        <GerarReciboDialog
          open={reciboDialogOpen}
          onOpenChange={setReciboDialogOpen}
          transacaoId={transacaoId}
          context={buildGerarReciboContext(d)}
          onGerado={() => void load()}
        />
      )}
    </Sheet>
  );
}
