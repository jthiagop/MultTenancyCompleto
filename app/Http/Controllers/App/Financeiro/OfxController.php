<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Financeiro\BankStatement;
use App\Models\PdfGeneration;
use App\Services\OfxService;
use App\Services\OfxExportService;
use App\Jobs\GenerateOfxJob;

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

            // Verificar magic bytes: OFX deve começar com OFXHEADER: (SGML) ou <OFX (XML)
            if (!preg_match('/^\s*(OFXHEADER:|<\?OFX|<OFX\b)/i', $fileContents)) {
                $msg = 'O arquivo não é um OFX válido. Verifique se o arquivo correto foi selecionado.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'type' => 'error', 'message' => $msg], 422);
                }
                return redirect()->back()->withErrors(['file' => $msg]);
            }

            $fileHash = hash('sha256', $fileContents);
            $fileName = $file->getClientOriginalName();

            // Verifica se já foi importado (ANTES de processar)
            if (BankStatement::where('file_hash', $fileHash)->exists()) {
                $msg = 'Este arquivo OFX já foi importado anteriormente.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'type' => 'warning', 'message' => $msg], 422);
                }
                return redirect()->route('banco.list', ['tab' => 'overview'])->with('warning', $msg);
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
                $msg = 'Nenhuma transação nova foi importada. Todas as transações deste arquivo já existem no sistema.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'type' => 'warning', 'message' => $msg], 422);
                }
                return redirect()->route('banco.list', ['tab' => 'overview'])->with('warning', $msg);
            }

            $mensagemSucesso = "Extrato OFX importado com sucesso! {$totalTransacoes} transação(ões) importada(s).";
            if ($usarHorariosMissas) {
                $mensagemSucesso .= ' A conciliação com horários de missa foi processada.';
            }

            if ($request->wantsJson() || $request->ajax()) {
                $entidadeId = count($entidades) === 1 ? $entidades[0]['id'] : null;
                return response()->json([
                    'success'     => true,
                    'message'     => $mensagemSucesso,
                    'entidade_id' => $entidadeId,
                ]);
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
            $raw = $e->getMessage();

            // O parser lança "Failed to parse OFX: array(...LibXMLError...)" com var_export bruto.
            // Detectar esse padrão e substituir por mensagem amigável.
            if (str_starts_with($raw, 'Failed to parse OFX:') || str_contains($raw, 'LibXMLError')) {
                $errorMsg = "O arquivo OFX está em um formato não suportado ou está corrompido.\n"
                    . "Verifique se o arquivo exportado pelo seu banco está no formato OFX 1.x (SGML) ou OFX 2.x (XML) e tente novamente.";
            } else {
                $errorMsg = $raw;
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return redirect()
                ->route('banco.list', ['tab' => 'overview'])
                ->with('error', $errorMsg);
        }
    }

    /**
     * Exporta OFX de forma assíncrona via fila (React).
     *
     * POST /relatorios/ofx/exportar-async
     */
    public function exportarAsync(Request $request)
    {
        try {
            $entidadeId  = $request->input('entidade_id');
            $dataInicial = $request->input('data_inicial');
            $dataFinal   = $request->input('data_final');
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
                'type'       => 'ofx',
                'user_id'    => Auth::id(),
                'company_id' => $companyId,
                'status'     => 'pending',
                'parameters' => [
                    'data_inicial' => $dataInicial,
                    'data_final'   => $dataFinal,
                    'entidade_id'  => $entidadeId,
                ],
            ]);

            GenerateOfxJob::dispatch(
                $dataInicial,
                $dataFinal,
                (int) $entidadeId,
                $companyId,
                Auth::id(),
                $tenantId,
                $pdfGen->id
            );

            return response()->json([
                'success' => true,
                'pdf_id'  => $pdfGen->id,
                'message' => 'OFX sendo gerado em background. Aguarde a notificação.',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao despachar job de OFX', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar geração do OFX: ' . $e->getMessage(),
            ], 500);
        }
    }


}
