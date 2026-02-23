<?php

namespace App\Http\Controllers\App\Contabilidade;

use App\Http\Controllers\Controller;
use App\Services\LoteContabilExportService;
use Illuminate\Http\Request;

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
}
