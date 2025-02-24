<?php

namespace App\Services;

use App\Models\Movimentacao;
use App\Models\LancamentoPadrao;
use App\Models\Banco;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransacaoFinanceiraService
{
    /**
     * Cria uma nova transação financeira.
     */
    public function criarTransacao(Request $request)
    {
        $subsidiary = Auth::user()->company;
        if (!$subsidiary) {
            throw new \Exception("Companhia não encontrada.");
        }

        $validatedData = $this->formatarDados($request->validated(), $subsidiary);

        $movimentacao = $this->criarMovimentacao($validatedData);
        $validatedData['movimentacao_id'] = $movimentacao->id;

        $transacao = TransacaoFinanceira::create($validatedData);

        $this->processarLancamentoPadrao($validatedData);
        $this->processarAnexos($request, $transacao);

        return $transacao;
    }

    /**
     * Formata os dados antes de salvar.
     */
    private function formatarDados(array $validatedData, $subsidiary)
    {
        return [
            'data_competencia'  => Carbon::createFromFormat('d-m-Y', $validatedData['data_competencia'])->format('Y-m-d'),
            'valor'             => str_replace(',', '.', str_replace('.', '', $validatedData['valor'])),
            'company_id'        => $subsidiary->company_id,
            'created_by'        => Auth::id(),
            'created_by_name'   => Auth::user()->name,
            'updated_by'        => Auth::id(),
            'updated_by_name'   => Auth::user()->name,
        ] + $validatedData;
    }

    /**
     * Cria uma movimentação financeira.
     */
    private function criarMovimentacao(array $validatedData)
    {
        return Movimentacao::create([
            'entidade_id'      => $validatedData['entidade_id'],
            'tipo'             => $validatedData['tipo'],
            'valor'            => $validatedData['valor'],
            'data'             => $validatedData['data_competencia'],
            'descricao'        => $validatedData['descricao'],
            'company_id'       => $validatedData['company_id'],
            'created_by'       => $validatedData['created_by'],
            'created_by_name'  => $validatedData['created_by_name'],
            'updated_by'       => $validatedData['updated_by'],
            'updated_by_name'  => $validatedData['updated_by_name'],
        ]);
    }

    /**
     * Processa lançamentos padrão.
     */
    private function processarLancamentoPadrao(array $validatedData)
    {
        $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);

        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $validatedData['origem'] = 'Banco';
            $validatedData['tipo']   = 'entrada';

            $movimentacaoBanco = Movimentacao::create([
                'entidade_id'      => $validatedData['entidade_banco_id'],
                'tipo'             => $validatedData['tipo'],
                'valor'            => $validatedData['valor'],
                'descricao'        => $validatedData['descricao'],
                'company_id'       => $validatedData['company_id'],
                'created_by'       => $validatedData['created_by'],
                'created_by_name'  => $validatedData['created_by_name'],
                'updated_by'       => $validatedData['updated_by'],
                'updated_by_name'  => $validatedData['updated_by_name'],
            ]);

            $validatedData['movimentacao_id'] = $movimentacaoBanco->id;
            Banco::create($validatedData);
        }
    }

    /**
     * Processa os anexos da transação.
     */
    private function processarAnexos(Request $request, TransacaoFinanceira $transacao)
    {
        if (!$request->hasFile('files')) {
            return;
        }

        foreach ($request->file('files') as $file) {
            $anexoName = time() . '_' . $file->getClientOriginalName();
            $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

            ModulosAnexo::create([
                'anexavel_id'     => $transacao->id,
                'anexavel_type'   => TransacaoFinanceira::class,
                'nome_arquivo'    => $file->getClientOriginalName(),
                'caminho_arquivo' => $anexoPath,
                'tamanho_arquivo' => $file->getSize(),
                'tipo_arquivo'    => $file->getMimeType() ?? '',
                'created_by'      => Auth::id(),
                'created_by_name' => Auth::user()->name,
            ]);
        }
    }
}
