<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Financeiro\Recorrencia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecorrenciaController extends Controller
{
    /**
     * Lista todas as configurações de recorrência disponíveis
     */
    public function index()
    {
        $companyId = session('active_company_id');
        
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Company ID não encontrado'
            ], 400);
        }

        $recorrencias = Recorrencia::reutilizaveis()
            ->get()
            ->map(function ($recorrencia) {
                return [
                    'id' => $recorrencia->id,
                    'nome' => $recorrencia->nome ?? $this->gerarNomeAutomatico($recorrencia),
                    'intervalo_repeticao' => $recorrencia->intervalo_repeticao,
                    'frequencia' => $recorrencia->frequencia,
                    'total_ocorrencias' => $recorrencia->total_ocorrencias,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $recorrencias
        ]);
    }

    /**
     * Retorna uma configuração específica
     */
    public function show($id)
    {
        $recorrencia = Recorrencia::forActiveCompany()
            ->find($id);

        if (!$recorrencia) {
            return response()->json([
                'success' => false,
                'message' => 'Configuração de recorrência não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $recorrencia->id,
                'nome' => $recorrencia->nome ?? $this->gerarNomeAutomatico($recorrencia),
                'intervalo_repeticao' => $recorrencia->intervalo_repeticao,
                'frequencia' => $recorrencia->frequencia,
                'total_ocorrencias' => $recorrencia->total_ocorrencias,
            ]
        ]);
    }

    /**
     * Cria uma nova configuração de recorrência
     * Verifica se já existe uma configuração idêntica antes de criar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'intervalo_repeticao' => 'required|integer|min:1',
            'frequencia' => 'required|in:diario,semanal,mensal,anual',
            'apos_ocorrencias' => 'required|integer|min:1|max:366',
            'nome' => 'nullable|string|max:255',
        ]);

        $companyId = session('active_company_id');
        
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Company ID não encontrado'
            ], 400);
        }

        // Verifica se já existe uma configuração idêntica
        $recorrenciaExistente = Recorrencia::where('company_id', $companyId)
            ->where('intervalo_repeticao', $validated['intervalo_repeticao'])
            ->where('frequencia', $validated['frequencia'])
            ->where('total_ocorrencias', $validated['apos_ocorrencias'])
            ->where('ativo', true)
            ->first();

        if ($recorrenciaExistente) {
            // Retorna a configuração existente ao invés de criar nova
            return response()->json([
                'success' => true,
                'message' => 'Configuração de recorrência já existe',
                'data' => [
                    'id' => $recorrenciaExistente->id,
                    'nome' => $recorrenciaExistente->nome ?? $this->gerarNomeAutomatico($recorrenciaExistente),
                    'intervalo_repeticao' => $recorrenciaExistente->intervalo_repeticao,
                    'frequencia' => $recorrenciaExistente->frequencia,
                    'total_ocorrencias' => $recorrenciaExistente->total_ocorrencias,
                ]
            ], 200);
        }

        // Gera nome automático se não fornecido
        $nome = $validated['nome'] ?? $this->gerarNomeAutomatico($validated);

        $recorrencia = Recorrencia::create([
            'company_id' => $companyId,
            'nome' => $nome,
            'intervalo_repeticao' => $validated['intervalo_repeticao'],
            'frequencia' => $validated['frequencia'],
            'total_ocorrencias' => $validated['apos_ocorrencias'],
            'ocorrencias_geradas' => 0,
            'data_proxima_geracao' => Carbon::today(), // Será atualizado quando usado
            'data_inicio' => Carbon::today(), // Será atualizado quando usado
            'data_fim' => null, // Será calculado quando usado
            'ativo' => true,
            'created_by' => Auth::id(),
            'created_by_name' => Auth::user()->name ?? 'Sistema',
            'updated_by' => Auth::id(),
            'updated_by_name' => Auth::user()->name ?? 'Sistema',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Configuração de recorrência criada com sucesso',
            'data' => [
                'id' => $recorrencia->id,
                'nome' => $recorrencia->nome,
                'intervalo_repeticao' => $recorrencia->intervalo_repeticao,
                'frequencia' => $recorrencia->frequencia,
                'total_ocorrencias' => $recorrencia->total_ocorrencias,
            ]
        ], 201);
    }

    /**
     * Gera nome automático para a configuração
     */
    private function gerarNomeAutomatico($dados)
    {
        $frequenciaText = [
            'diario' => 'Dia(s)',
            'semanal' => 'Semana(s)',
            'mensal' => 'Mês(es)',
            'anual' => 'Ano(s)'
        ];

        $intervalo = is_array($dados) ? $dados['intervalo_repeticao'] : $dados->intervalo_repeticao;
        $frequencia = is_array($dados) ? $dados['frequencia'] : $dados->frequencia;
        $total = is_array($dados) ? $dados['apos_ocorrencias'] : $dados->total_ocorrencias;

        return "A cada {$intervalo} " . ($frequenciaText[$frequencia] ?? $frequencia) . " - Após {$total} ocorrências";
    }
}
