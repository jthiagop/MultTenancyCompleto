<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransacaoFinanceiraRequest;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Flasher;
use Illuminate\Http\Request;
use Log;
use Validator;

class TransacaoFinanceiraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransacaoFinanceiraRequest $request)
    {
            dd($request);
        try {
            $subsidiaryId = User::getCompany();

            // Converte a data para o formato correto
            $validatedData = $request->validated();
            $validatedData['data_competencia'] = Carbon::createFromFormat('d-m-Y', $validatedData['data_competencia'])->format('Y-m-d');
            $validatedData['company_id'] = $subsidiaryId->company_id;
            $validatedData['origem'] = 'CX';
            $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));
            $validatedData['created_by'] = Auth::id();
            $validatedData['created_by_name'] = Auth::user()->name;
            $validatedData['updated_by'] = Auth::id();
            $validatedData['updated_by_name'] = Auth::user()->name;

            // Cria o lançamento na tabela 'movimentacoes'
            try {
                $movimentacao = Movimentacao::create([
                    'entidade_id' => $validatedData['entidade_id'],
                    'tipo' => $validatedData['tipo'],
                    'valor' => $validatedData['valor'],
                    'data' => $validatedData['data_competencia'],
                    'descricao' => $validatedData['descricao'],
                    'company_id' => $subsidiaryId->company_id,
                    'created_by' => Auth::id(), // Pegando apenas o ID do usuário
                    'created_by_name' => Auth::user()->name, // Nome do usuário
                    'updated_by' => Auth::id(), // Pegando apenas o ID do usuário
                    'updated_by_name' => Auth::user()->name, // Nome do usuário
                ]);

                // Retorno de sucesso
                return redirect()->back()->with('message', 'Movimentação criada com sucesso!');
            } catch (\Exception $e) {
                // Log de erro
                Log::error('Erro ao criar movimentação: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Erro ao criar a movimentação.');
            }


            // Cria o lançamento na tabela 'caixa'
            $validatedData['movimentacao_id'] = $movimentacao->id; // Vincula a movimentação
            dd($validatedData, $movimentacao);
            // Cria o registro no caixa
            $caixa = TransacaoFinanceira::create($validatedData);

            // Verifica e processa lançamentos padrão
            $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
            if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
                $validatedData['origem'] = 'BC';
                $validatedData['tipo'] = 'entrada';
                $validatedData['comprovacao_fiscal'] = $validatedData['comprovacao_fiscal'];

                // Cria o lançamento na tabela 'movimentacoes'
                try {
                    $movimentacao = Movimentacao::create([
                        'entidade_id' => $validatedData['entidade_banco_id'],
                        'tipo' => $validatedData['tipo'],
                        'valor' => $validatedData['valor'],
                        'descricao' => $validatedData['descricao'],
                        'company_id' => $subsidiaryId->company_id,
                        'created_by' => Auth::id(),
                        'created_by_name' => Auth::user()->name,
                        'updated_by' => Auth::id(),
                        'updated_by_name' => Auth::user()->name,
                    ]);
                } catch (\Exception $e) {
                    // Log de erro
                    dd('Erro ao criar movimentação:', $e->getMessage(), $e->getTrace());
                    Log::error('Erro ao criar movimentação: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Erro ao criar a movimentação.');
                }

                // Cria o lançamento na tabela 'caixa'
                $validatedData['movimentacao_id'] = $movimentacao->id; // Vincula a movimentação

                $banco = Banco::create($validatedData);

                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $anexo) {
                        $anexoName = time() . '_' . $anexo->getClientOriginalName();
                        $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                        Anexo::create([
                            'banco_id' => $banco->id,
                            'nome_arquivo' => $anexoName,
                            'caminho_arquivo' => $anexoPath,
                            'size' => $anexo->getSize(), // Tamanho do arquivo
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                        ]);
                    }
                }
            }


            // Verifica se há arquivos anexos
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $anexo) {
                    $anexoName = time() . '_' . $anexo->getClientOriginalName();
                    $anexoPath = $anexo->storeAs('anexos', $anexoName, 'public');

                    Anexo::create([
                        'caixa_id' => $caixa->id,
                        'nome_arquivo' => $anexoName,
                        'caminho_arquivo' => $anexoPath,
                        'size' => $anexo->getSize(), // Tamanho do arquivo
                        'created_by' => Auth::user()->id(),
                        'updated_by' => Auth::user()->id(),
                    ]);
                }
            }
        // Mensagem de sucesso
        flash()->success('O livro foi salvo com sucesso!');

            // Exibe a mensagem diretamente usando o Flasher e redireciona
            return redirect()->back();
        } catch (\Exception $e) {
            // Adiciona mensagem de erro com detalhes da exceção
            Flasher::addError('Ocorreu um erro ao processar o lançamento: ' . $e->getMessage());

            // Retorna com os dados antigos e exibe as mensagens de erro
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransacaoFinanceira $transacaoFinanceira)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransacaoFinanceira $transacaoFinanceira)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransacaoFinanceira $transacaoFinanceira)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransacaoFinanceira $transacaoFinanceira)
    {
        //
    }
}
