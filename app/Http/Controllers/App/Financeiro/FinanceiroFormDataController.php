<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\CostCenter;
use App\Models\FormasPagamento;
use App\Models\LancamentoPadrao;
use App\Models\Parceiro;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class FinanceiroFormDataController extends Controller
{
    /**
     * Retorna todos os dados necessários para popular os selects do formulário de lançamento.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function formData(Request $request): JsonResponse
    {
        try {
            return response()->json($this->buildFormDataPayload($request));
        } catch (Throwable $e) {
            $tipoQuery = $request->query('tipo', 'despesa');
            $tenantId  = rescue(static fn () => tenant()?->id, null, report: false);

            Log::error('financeiro.api.form-data: falha ao montar payload', [
                'tipo'                => $tipoQuery,
                'active_company_id'   => session('active_company_id'),
                'tenant_id'           => $tenantId,
                'exception'           => $e::class,
                'message'             => $e->getMessage(),
                'file'                => $e->getFile(),
                'line'                => $e->getLine(),
            ]);

            report($e);

            return response()->json([
                'message' => 'Erro ao carregar dados do formulário. Detalhes foram registrados no log do servidor.',
                'debug'   => config('app.debug') ? [
                    'exception' => $e::class,
                    'message'   => $e->getMessage(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ] : null,
            ], 500);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFormDataPayload(Request $request): array
    {
        $tipo      = $request->query('tipo', 'despesa'); // 'receita', 'despesa' ou 'all' (extrato / filtros)
        $companyId = session('active_company_id');

        // Parceiros: natureza conforme tipo, ou todos da empresa quando tipo=all
        if ($tipo === 'all') {
            $parceiros = Parceiro::forActiveCompany()
                ->orderBy('nome')
                ->get(['id', 'nome', 'natureza', 'cnpj', 'cpf']);
        } else {
            $natureza = $tipo === 'receita' ? 'cliente' : 'fornecedor';
            $parceiros = Parceiro::forActiveCompany()
                ->natureza($natureza)
                ->orderBy('nome')
                ->get(['id', 'nome', 'natureza', 'cnpj', 'cpf']);
        }

        // Entidades financeiras (bancos + caixas) — sem company na sessão, lista vazia
        $entidades = collect();
        if ($companyId !== null) {
            // Coluna real em `banks` é `logo_path`; `logo_url` é accessor no modelo Bank.
            $entidadesCol = EntidadeFinanceira::where('company_id', $companyId)
                ->with('bank:id,name,logo_path')
                ->orderBy('nome')
                ->get();
            $saldos = EntidadeFinanceira::saldosCalculadosPorEntidadeIds($entidadesCol->pluck('id')->all());
            // saldo_atual no JSON = saldo das movimentações (calculateBalance), não só o cache na entidade.
            $entidades = $entidadesCol->map(fn ($e) => [
                'id'           => (string) $e->id,
                'nome'         => $e->nome,
                'label'        => $e->tipo === 'banco'
                    ? (($e->agencia ? $e->agencia . ' - ' : '') . ($e->conta ?? $e->nome))
                    : $e->nome,
                'tipo'         => $e->tipo,
                'account_type' => $e->tipo === 'banco' ? ($e->account_type ?? null) : null,
                'logo'         => $e->bank?->logo_url,
                'saldo_atual'  => round((float) ($saldos[$e->id] ?? 0), 2),
            ]);
        }

        // Categorias (lançamentos padrão) — lancamento_padraos.type: entrada | saida | ambos (migração 2025_11_24_140039)
        $categoriasBase = fn () => LancamentoPadrao::where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->orWhereNull('company_id');
        });

        try {
            $q = $categoriasBase();
            if ($tipo === 'all') {
                $q->whereIn('type', ['entrada', 'saida', 'ambos']);
            } elseif ($tipo === 'receita') {
                $q->whereIn('type', ['entrada', 'ambos']);
            } else {
                $q->whereIn('type', ['saida', 'ambos']);
            }
            $categorias = $q->orderBy('description')->get(['id', 'description', 'type']);
        } catch (QueryException $e) {
            // Banco sem ENUM/value 'ambos' ou outro SQL legado (migração ainda não aplicada no tenant)
            $q = $categoriasBase();
            if ($tipo === 'all') {
                $q->whereIn('type', ['entrada', 'saida']);
            } elseif ($tipo === 'receita') {
                $q->where('type', 'entrada');
            } else {
                $q->where('type', 'saida');
            }
            $categorias = $q->orderBy('description')->get(['id', 'description', 'type']);
        }

        // Centros de custo ativos
        $centrosCusto = collect();
        if ($companyId !== null) {
            $centrosCusto = CostCenter::where('company_id', $companyId)
                ->where('status', 1)
                ->orderBy('code')
                ->get(['id', 'code', 'name']);
        }

        // Formas de pagamento ativas
        $formasPagamento = FormasPagamento::where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'codigo', 'nome']);

        // Filiais para rateio (matriz + filhas). Sem company_id na sessão: vazio (evita OR solto com NULL).
        $filiais = collect();
        if ($companyId !== null) {
            $filiais = Company::where(function ($q) use ($companyId) {
                $q->where('id', $companyId)->orWhere('parent_id', $companyId);
            })
                ->select('id', 'name', 'type', 'avatar')
                ->orderBy('name')
                ->get()
                ->map(function ($c) {
                    $avatar = $c->avatar;
                    $avatarPath = is_string($avatar) && $avatar !== ''
                        ? '/file/' . ltrim(preg_replace('#^public/#', '', $avatar), '/')
                        : null;

                    return [
                        'id'         => (string) $c->id,
                        'name'       => $c->name,
                        'type'       => $c->type,
                        'avatar_url' => $avatarPath,
                    ];
                });
        }

        return [
            'parceiros'       => $parceiros,
            'entidades'       => $entidades,
            'categorias'      => $categorias,
            'centrosCusto'    => $centrosCusto,
            'formasPagamento' => $formasPagamento,
            'filiais'         => $filiais,
        ];
    }
}
