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

            // Processa OFX e extrai dados
            $ofxData = $this->ofxService->processOfx($file);
            $totalValue = $ofxData['total_value'] ?? 0;
            $transactionCount = $ofxData['transaction_count'] ?? 0;

            // Salva no banco
            BankStatement::create([
                'bank_account_id' => Auth::user()->bank_account_id, // Exemplo de associação
                'file_name' => $file->getClientOriginalName(),
                'file_hash' => $fileHash,
                'total_value' => $totalValue,
                'transaction_count' => $transactionCount,
                'imported_by' => Auth::id(),
                'reconciled' => 0,

            ]);

            return redirect()
                ->route('banco.list', ['tab' => 'overview'])
                ->with('success', 'Extrato OFX importado com sucesso!');

        } catch (\Exception $e) {
            return redirect()
                ->route('banco.list', ['tab' => 'overview'])
                ->with('error', 'Erro ao importar o arquivo: ' . $e->getMessage());
        }
    }


}
