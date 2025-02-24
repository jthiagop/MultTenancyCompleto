<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Financeiro\BankStatement;
use App\Services\OfxService;
use Auth;
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
            ], [
                'file.required' => 'Você deve selecionar um arquivo OFX.',
                'file.max'      => 'O arquivo ultrapassa o tamanho máximo de 20MB.',
            ]);

            // Chama o Service para processar o OFX
            $this->ofxService->processOfx($request->file('file'));

            // Busca lançamentos não conciliados
        $lancamentosNaoConciliados = BankStatement::where('reconciled', 0)->get();

        return redirect()
            ->route('banco.list', ['tab' => 'conciliacao'])
            ->with('success', 'Extrato OFX importado com sucesso!')
            ->with('lancamentosNaoConciliados', $lancamentosNaoConciliados);

        } catch (\Exception $e) {
            return redirect()
                ->route('banco.list', ['tab' => 'conciliacao'])
                ->with('error', 'Ocorreu um erro ao importar o arquivo: ' . $e->getMessage());
        }
    }

}
