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

            // Verifica se já foi importado
            if (BankStatement::where('file_hash', $fileHash)->exists()) {
                return redirect()
                    ->route('banco.list', ['tab' => 'overview'])
                    ->with('warning', 'Este arquivo OFX já foi importado anteriormente.');
            }

            // Verifica se o usuário escolheu usar horários de missa
            $usarHorariosMissas = $request->has('usar_horarios_missa') && $request->input('usar_horarios_missa') == '1';

            // Processa OFX e extrai dados (passa a escolha do usuário sobre horários de missa)
            $this->ofxService->processOfx($file, $usarHorariosMissas);
            
            // O processOfx já salva as transações individualmente via BankStatement::storeTransaction
            // Não precisamos criar um registro adicional aqui

            $mensagemSucesso = 'Extrato OFX importado com sucesso!';
            if ($usarHorariosMissas) {
                $mensagemSucesso .= ' A conciliação com horários de missa foi processada.';
            }

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
