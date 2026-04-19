<?php

namespace App\Http\Controllers\App\Cemiterio;

use App\Http\Controllers\Controller;
use App\Models\Cemiterio\Sepultura;
use App\Models\Cemiterio\Sepultado;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Movimentacao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CobrancaController extends Controller
{
    public function index(Request $request)
    {
        $companyId = User::getCompany()->company_id;

        $page     = max(1, (int) $request->query('page', 1));
        $perPage  = min(100, max(1, (int) $request->query('per_page', 20)));
        $search   = trim($request->query('search', ''));
        $situacao = $request->query('situacao', '');
        $de       = $request->query('de', '');
        $ate      = $request->query('ate', '');
        $sortBy   = $request->query('sort_by', 'data_vencimento');
        $sortDir  = in_array(strtolower($request->query('sort_dir', 'asc')), ['asc', 'desc'])
                    ? strtolower($request->query('sort_dir', 'asc'))
                    : 'asc';

        $query = TransacaoFinanceira::with(['sepultura', 'parceiro',
                'sepultado' => fn($sq) => $sq->select('id', 'nome', 'cpf', 'sepultura_id')])
            ->where('company_id', $companyId)
            ->where(fn($q) => $q->whereNotNull('sepultura_id')->orWhereNotNull('sepultado_id'))
            ->where('origem', 'cemiterio');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                  ->orWhereHas('sepultura', fn($sq) => $sq->where('codigo_sepultura', 'like', "%{$search}%"))
                  ->orWhereHas('parceiro', fn($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($situacao !== '') {
            if ($situacao === 'em_atraso') {
                $query->where('situacao', 'atrasado');
            } elseif ($situacao === 'a_receber') {
                $query->where('situacao', 'em_aberto');
            } elseif ($situacao === 'recebido') {
                $query->where('situacao', 'recebido');
            } elseif ($situacao === 'cancelado') {
                $query->where('situacao', 'desconsiderado');
            } else {
                $query->where('situacao', $situacao);
            }
        }

        if ($de !== '') {
            $query->where('data_vencimento', '>=', Carbon::parse($de)->startOfDay());
        }

        if ($ate !== '') {
            $query->where('data_vencimento', '<=', Carbon::parse($ate)->endOfDay());
        }

        $total = $query->count();

        $cobrancas = $query
            ->orderBy($sortBy, $sortDir)
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $data = $cobrancas->map(fn($t) => [
            'id'              => $t->id,
            'sepultura_id'    => $t->sepultura_id,
            'sepultura_codigo' => $t->sepultura?->codigo_sepultura ?? '',
            'sepultado_id'    => $t->sepultado_id,
            'sepultado_nome'  => $t->sepultado?->nome ?? '',
            'tipo_cobranca'   => $t->sepultura_id ? 'tumulo' : 'difunto',
            'titulo'          => $t->sepultura_id
                ? ($t->sepultura?->codigo_sepultura ?? '—')
                : ($t->sepultado?->nome ?? '—'),
            'responsavel'     => $t->parceiro?->name ?? '',
            'descricao'       => $t->descricao ?? '',
            'data_vencimento' => $t->data_vencimento?->toDateString() ?? '',
            'data_pagamento'  => $t->data_pagamento?->toDateString() ?? '',
            'valor'           => (float) $t->valor,
            'valor_pago'      => (float) ($t->valor_pago ?? 0),
            'situacao'        => $t->situacao?->value ?? '',
            'situacao_label'  => $t->situacao?->label() ?? '',
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'total'   => $total,
        ]);
    }

    public function store(Request $request)
    {
        $companyId = User::getCompany()->company_id;

        $validator = Validator::make($request->all(), [
            'items'                    => 'required|array|min:1',
            'items.*.type'             => 'required|in:sepultura,sepultado',
            'items.*.id'               => 'required|integer',
            'data_vencimento'          => 'required|date',
            'valor'                    => 'required|numeric|min:0.01',
            'descricao'                => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user  = Auth::user();
        $criados = [];

        foreach ($request->input('items') as $item) {
            $sepulturaId  = null;
            $sepultadoId  = null;
            $descricaoBase = $request->input('descricao');

            if ($item['type'] === 'sepultura') {
                $sepultura = Sepultura::where('company_id', $companyId)->findOrFail($item['id']);
                $sepulturaId   = $sepultura->id;
                $descricaoBase = $descricaoBase ?? 'Taxa de sepultura - ' . $sepultura->codigo_sepultura;
            } else {
                $sepultado = \App\Models\Cemiterio\Sepultado::where('company_id', $companyId)->findOrFail($item['id']);
                $sepultadoId   = $sepultado->id;
                $sepulturaId   = $sepultado->sepultura_id; // pode ser null
                $descricaoBase = $descricaoBase ?? 'Taxa — ' . $sepultado->nome;
            }

            $transacao = TransacaoFinanceira::create([
                'company_id'       => $companyId,
                'sepultura_id'     => $sepulturaId,
                'sepultado_id'     => $sepultadoId,
                'tipo'             => 'entrada',
                'valor'            => $request->input('valor'),
                'data_competencia' => $request->input('data_vencimento'),
                'data_vencimento'  => $request->input('data_vencimento'),
                'descricao'        => $descricaoBase,
                'situacao'         => 'em_aberto',
                'origem'           => 'cemiterio',
                'created_by'       => $user->id,
                'created_by_name'  => $user->name,
                'updated_by'       => $user->id,
                'updated_by_name'  => $user->name,
            ]);

            $criados[] = $transacao->id;
        }

        $count = count($criados);
        return response()->json([
            'success' => true,
            'message' => $count === 1 ? 'Cobrança lançada com sucesso!' : "{$count} cobranças lançadas com sucesso!",
            'data'    => ['ids' => $criados],
        ], 201);
    }

    public function pagar(Request $request, int $id)
    {
        $companyId = User::getCompany()->company_id;

        $validator = Validator::make($request->all(), [
            'entidade_id'    => 'required|integer',
            'data_pagamento' => 'required|date',
            'valor_pago'     => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $transacao = TransacaoFinanceira::where('company_id', $companyId)
            ->where(fn($q) => $q->whereNotNull('sepultura_id')->orWhereNotNull('sepultado_id'))
            ->where('origem', 'cemiterio')
            ->findOrFail($id);

        $user = Auth::user();

        DB::transaction(function () use ($transacao, $request, $companyId, $user) {
            $movimentacao = Movimentacao::create([
                'company_id'       => $companyId,
                'entidade_id'      => $request->input('entidade_id'),
                'tipo'             => 'entrada',
                'valor'            => $request->input('valor_pago'),
                'data'             => $request->input('data_pagamento'),
                'descricao'        => $transacao->descricao,
                'created_by'       => $user->id,
                'created_by_name'  => $user->name,
                'updated_by'       => $user->id,
                'updated_by_name'  => $user->name,
            ]);

            $transacao->update([
                'entidade_id'    => $request->input('entidade_id'),
                'movimentacao_id' => $movimentacao->id,
                'data_pagamento' => $request->input('data_pagamento'),
                'valor_pago'     => $request->input('valor_pago'),
                'situacao'       => 'recebido',
                'updated_by'     => $user->id,
                'updated_by_name' => $user->name,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Cobrança marcada como recebida!',
        ]);
    }
}
