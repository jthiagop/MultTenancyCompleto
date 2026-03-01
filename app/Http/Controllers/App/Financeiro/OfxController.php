<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Financeiro\BankStatement;
use App\Services\OfxService;
use App\Services\OfxExportService;

class OfxController extends Controller
{
    protected $ofxService;
    protected $ofxExportService;

    public function __construct(OfxService $ofxService, OfxExportService $ofxExportService)
    {
        $this->ofxService = $ofxService;
        $this->ofxExportService = $ofxExportService;
    }

    /**
     * Exporta as transações financeiras em formato OFX (download direto).
     *
     * GET /relatorios/ofx/exportar?entidade_id=X&data_inicial=dd/mm/YYYY&data_final=dd/mm/YYYY
     */
    public function exportar(Request $request)
    {
        $request->validate([
            'entidade_id'  => 'required|integer',
            'data_inicial' => 'required|string',
            'data_final'   => 'required|string',
        ], [
            'entidade_id.required'  => 'Selecione uma conta financeira.',
            'data_inicial.required' => 'O período inicial é obrigatório.',
            'data_final.required'   => 'O período final é obrigatório.',
        ]);

        try {
            $resultado = $this->ofxExportService->gerarOfx(
                (int) $request->entidade_id,
                $request->data_inicial,
                $request->data_final
            );

            return response($resultado['conteudo'])
                ->header('Content-Type', 'application/x-ofx')
                ->header('Content-Disposition', "attachment; filename=\"{$resultado['nome_arquivo']}\"")
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:21048|extensions:ofx',
                'usar_horarios_missa' => 'nullable|boolean',
            ], [
                'file.required' => 'Você deve selecionar um arquivo OFX.',
                'file.max'      => 'O arquivo ultrapassa o tamanho máximo de 20MB.',
                'file.extensions' => 'O arquivo deve ter a extensão .ofx.',
            ]);

            $file = $request->file('file');
            $fileContents = file_get_contents($file->getRealPath());
            $fileHash = hash('sha256', $fileContents);
            $fileName = $file->getClientOriginalName();

            // Verifica se já foi importado (ANTES de processar)
            if (BankStatement::where('file_hash', $fileHash)->exists()) {
                return redirect()
                    ->route('banco.list', ['tab' => 'overview'])
                    ->with('warning', 'Este arquivo OFX já foi importado anteriormente.');
            }

            // Verifica se o usuário escolheu usar horários de missa
            $usarHorariosMissas = $request->has('usar_horarios_missa') && $request->input('usar_horarios_missa') == '1';

            $companyId = session('active_company_id');

            // Processa OFX e extrai dados (retorna array com quantidade de transações e entidades importadas)
            $resultado = $this->ofxService->processOfx($file, $usarHorariosMissas, $fileHash, $fileName, $companyId);
            $totalTransacoes = $resultado['totalTransacoes'];
            $entidades = $resultado['entidades'];
            
            // Verifica se alguma transação foi realmente importada
            if ($totalTransacoes === 0) {
                return redirect()
                    ->route('banco.list', ['tab' => 'overview'])
                    ->with('warning', 'Nenhuma transação nova foi importada. Todas as transações deste arquivo já existem no sistema.');
            }

            $mensagemSucesso = "Extrato OFX importado com sucesso! {$totalTransacoes} transação(ões) importada(s).";
            if ($usarHorariosMissas) {
                $mensagemSucesso .= ' A conciliação com horários de missa foi processada.';
            }

            // Se houver apenas 1 entidade importada, redireciona para o show dela
            if (count($entidades) === 1) {
                return redirect()
                    ->route('entidades.show', ['entidade' => $entidades[0]['id']])
                    ->with('success', $mensagemSucesso);
            }

            // Se houver múltiplas entidades, redireciona para a lista
            return redirect()
                ->route('banco.list', ['tab' => 'overview'])
                ->with('success', $mensagemSucesso);

        } catch (\Throwable $e) {
            return redirect()
                ->route('banco.list', ['tab' => 'overview'])
                ->with('error', 'Erro ao importar o arquivo: ' . $e->getMessage());
        }
    }


}
