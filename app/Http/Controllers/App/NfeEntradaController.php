<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\DfeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NfeEntradaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companyId = session('active_company_id') ?? Auth::user()->company_id;

        $documentos = DfeDocument::where('company_id', $companyId)
                                 ->orderBy('data_emissao', 'desc')
                                 ->paginate(15);

        return view('app.nfe_entrada.index', compact('documentos'));
    }

    /**
     * Filter documents by date range (AJAX)
     */
    public function filtrar(Request $request)
    {
        try {
            $request->validate([
                'data_inicial' => 'required|date_format:d/m/Y',
                'data_final' => 'required|date_format:d/m/Y',
            ]);

            $companyId = session('active_company_id') ?? Auth::user()->company_id;

            // Parse dates from Brazilian format
            $dataInicial = \Carbon\Carbon::createFromFormat('d/m/Y', $request->data_inicial)->startOfDay();
            $dataFinal = \Carbon\Carbon::createFromFormat('d/m/Y', $request->data_final)->endOfDay();

            // Validate date range
            if ($dataInicial->gt($dataFinal)) {
                return response()->json([
                    'success' => false,
                    'message' => 'A data inicial não pode ser maior que a data final.'
                ], 422);
            }

            // Query documents
            $documentos = DfeDocument::where('company_id', $companyId)
                ->whereBetween('data_emissao', [$dataInicial, $dataFinal])
                ->orderBy('data_emissao', 'desc')
                ->get();

            // Format for frontend
            $documentosFormatados = $documentos->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'data_emissao' => $doc->data_emissao ? $doc->data_emissao->format('d/m/Y H:i') : '-',
                    'chave_acesso' => $doc->chave_acesso,
                    'chave_resumida' => substr($doc->chave_acesso, 0, 4) . '...' . substr($doc->chave_acesso, -4),
                    'emitente_nome' => $doc->emitente_nome ?? 'Sem Nome',
                    'emitente_cnpj' => $doc->emitente_cnpj,
                    'valor_total' => number_format($doc->valor_total, 2, ',', '.'),
                    'valor_total_raw' => $doc->valor_total,
                    'status_sistema' => $doc->status_sistema,
                    'status_label' => $this->getStatusLabel($doc->status_sistema),
                    'tp_amb' => $doc->tp_amb,
                    'ambiente_label' => $doc->tp_amb == 1 ? 'Produção' : 'Homologação',
                ];
            });

            return response()->json([
                'success' => true,
                'documentos' => $documentosFormatados,
                'total' => $documentos->count(),
                'periodo' => [
                    'inicial' => $dataInicial->format('d/m/Y'),
                    'final' => $dataFinal->format('d/m/Y'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao filtrar documentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status label for display
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'novo' => ['text' => 'Novo (Resumo)', 'class' => 'badge-light-warning'],
            'downloaded' => ['text' => 'XML Baixado', 'class' => 'badge-light-success'],
        ];

        return $labels[$status] ?? ['text' => ucfirst($status), 'class' => 'badge-light-primary'];
    }
}
