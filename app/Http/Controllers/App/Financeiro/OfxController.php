<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Financeiro\BankStatement;
use App\Services\OfxService;
use Illuminate\Support\Facades\Auth;
use Endeken\OFX\OFX;

class OfxController extends Controller
{
    protected $ofxService;

    public function __construct(OfxService $ofxService)
    {
        $this->ofxService = $ofxService;
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:21048',
                'usar_horarios_missa' => 'nullable|boolean',
            ], [
                'file.required' => 'Você deve selecionar um arquivo OFX.',
                'file.max'      => 'O arquivo ultrapassa o tamanho máximo de 20MB.',
            ]);

            $file = $request->file('file');
            $fileContents = file_get_contents($file->getRealPath());
            $fileHash = md5($fileContents);
            $fileName = $file->getClientOriginalName(); // Get the original file name here

            // Verifica se já foi importado (ANTES de processar)
            if (BankStatement::where('file_hash', $fileHash)->exists()) {
                return redirect()
                    ->route('banco.list', ['tab' => 'overview'])
                    ->with('warning', 'Este arquivo OFX já foi importado anteriormente.');
            }

            // Verifica se o usuário escolheu usar horários de missa
            $usarHorariosMissas = $request->has('usar_horarios_missa') && $request->input('usar_horarios_missa') == '1';

            // Processa OFX e extrai dados (retorna array com quantidade de transações e entidades importadas)
            $resultado = $this->ofxService->processOfx($file, $usarHorariosMissas, $fileHash, $fileName);
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

        } catch (\Exception $e) {
            return redirect()
                ->route('banco.list', ['tab' => 'overview'])
                ->with('error', 'Erro ao importar o arquivo: ' . $e->getMessage());
        }
    }


}
