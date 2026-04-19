<?php

namespace App\Http\Controllers\App\Contabilidade;

use App\Http\Controllers\Controller;
use App\Services\LoteContabilExportService;
use App\Models\PdfGeneration;
use App\Jobs\GenerateLoteContabilJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoteContabilController extends Controller
{
    protected LoteContabilExportService $exportService;

    public function __construct(LoteContabilExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Exporta as movimentações contábeis em formato TXT ou CSV (download direto).
     *
     * GET /relatorios/lote-contabil/exportar?entidade_id=X&data_inicial=dd/mm/YYYY&data_final=dd/mm/YYYY&campo_data=data&formato=txt
     */
    public function exportar(Request $request)
    {
        $request->validate([
            'entidade_id'  => 'required|integer',
            'data_inicial' => 'required|string',
            'data_final'   => 'required|string',
            'campo_data'   => 'required|in:data,data_competencia',
            'formato'      => 'required|in:txt,csv',
        ], [
            'entidade_id.required'  => 'Selecione uma conta financeira.',
            'data_inicial.required' => 'O período inicial é obrigatório.',
            'data_final.required'   => 'O período final é obrigatório.',
            'campo_data.required'   => 'Selecione o regime de data.',
            'formato.required'      => 'Selecione o formato de exportação.',
        ]);

        try {
            $resultado = $this->exportService->gerar(
                (int) $request->entidade_id,
                $request->data_inicial,
                $request->data_final,
                $request->campo_data,
                $request->formato
            );

            $contentType = $request->formato === 'csv' ? 'text/csv' : 'text/plain';

            return response($resultado['conteudo'])
                ->header('Content-Type', "{$contentType}; charset=UTF-8")
                ->header('Content-Disposition', "attachment; filename=\"{$resultado['nome_arquivo']}\"")
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('X-Total-Lancamentos', $resultado['total'])
                ->header('X-Total-Ignoradas', $resultado['ignoradas']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Exporta lote contábil de forma assíncrona via fila (React).
     *
     * POST /relatorios/lote-contabil/exportar-async
     */
    public function exportarAsync(Request $request)
    {
        try {
            $entidadeId  = $request->input('entidade_id');
            $dataInicial = $request->input('data_inicial');
            $dataFinal   = $request->input('data_final');
            $campoData   = $request->input('campo_data', 'data');
            $formato     = $request->input('formato', 'txt');
            $companyId   = session('active_company_id');
            $tenantId    = tenant('id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empresa não selecionada.',
                ], 400);
            }

            if (!$entidadeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecione uma conta financeira.',
                ], 400);
            }

            $pdfGen = PdfGeneration::create([
                'type'       => 'lote_contabil',
                'user_id'    => Auth::id(),
                'company_id' => $companyId,
                'status'     => 'pending',
                'parameters' => [
                    'data_inicial' => $dataInicial,
                    'data_final'   => $dataFinal,
                    'entidade_id'  => $entidadeId,
                    'campo_data'   => $campoData,
                    'formato'      => $formato,
                ],
            ]);

            GenerateLoteContabilJob::dispatch(
                $dataInicial,
                $dataFinal,
                (int) $entidadeId,
                $formato,
                $campoData,
                $companyId,
                Auth::id(),
                $tenantId,
                $pdfGen->id
            );

            return response()->json([
                'success' => true,
                'pdf_id'  => $pdfGen->id,
                'message' => 'Arquivo sendo gerado em background. Aguarde a notificação.',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao despachar job de Lote Contábil', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar geração: ' . $e->getMessage(),
            ], 500);
        }
    }
}
