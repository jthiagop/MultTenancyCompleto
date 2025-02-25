<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransacaoFinanceiraRequest;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\Financeiro\ModulosAnexo;
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
use Yajra\DataTables\Facades\DataTables;

class TransacaoFinanceiraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

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
        // Recupera a companhia associada ao usuário autenticado
        $subsidiary = User::getCompany();

        if (!$subsidiary) {
            return redirect()->back()->with('error', 'Companhia não encontrada.');
        }

        // Validação dos dados é automática com StoreTransacaoFinanceiraRequest, não é necessário duplicar validações aqui

        // Processa os dados validados
        $validatedData = $request->validated();

        // Converte o valor e a data para os formatos adequados
        $validatedData['data_competencia'] = Carbon::createFromFormat('d-m-Y', $validatedData['data_competencia'])->format('Y-m-d');
        $validatedData['valor'] = str_replace(',', '.', str_replace('.', '', $validatedData['valor']));

        // Adiciona informações padrão
        $validatedData['company_id'] = $subsidiary->company_id;
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;
        $validatedData['updated_by'] = Auth::id();
        $validatedData['updated_by_name'] = Auth::user()->name;

        // Cria o lançamento na tabela 'movimentacoes'
        $movimentacao = Movimentacao::create([
            'entidade_id' => $validatedData['entidade_id'],
            'tipo' => $validatedData['tipo'],
            'valor' => $validatedData['valor'],
            'data' => $validatedData['data_competencia'],
            'descricao' => $validatedData['descricao'],
            'company_id' => $validatedData['company_id'],
            'created_by' => $validatedData['created_by'],
            'created_by_name' => $validatedData['created_by_name'],
            'updated_by' => $validatedData['updated_by'],
            'updated_by_name' => $validatedData['updated_by_name'],
        ]);

        // Cria o registro no caixa
        $validatedData['movimentacao_id'] = $movimentacao->id;
        $caixa = TransacaoFinanceira::create($validatedData);

        // Verifica e processa lançamentos padrão
        $this->processarLancamentoPadrao($validatedData);

        // Processa anexos, se existirem
        $this->processarAnexos($request, $caixa);

        // Adiciona mensagem de sucesso
        Flasher::addSuccess('Lançamento criado com sucesso!');
        return redirect()->back()->with('message', 'Lançamento criado com sucesso!');
    }

    /**
     * Processa lançamentos padrão.
     */
    private function processarLancamentoPadrao(array $validatedData)
    {
        $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $validatedData['origem'] = 'Banco';
            $validatedData['tipo'] = 'entrada';

            // Cria outra movimentação para "Deposito Bancário"
            $movimentacaoBanco = Movimentacao::create([
                'entidade_id' => $validatedData['entidade_banco_id'],
                'tipo' => $validatedData['tipo'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'company_id' => $validatedData['company_id'],
                'created_by' => $validatedData['created_by'],
                'created_by_name' => $validatedData['created_by_name'],
                'updated_by' => $validatedData['updated_by'],
                'updated_by_name' => $validatedData['updated_by_name'],
            ]);

            // Cria o lançamento no banco
            $validatedData['movimentacao_id'] = $movimentacaoBanco->id;
            Banco::create($validatedData);
        }
    }

    /**
     * Processa os anexos enviados.
     */
    private function processarAnexos(Request $request, TransacaoFinanceira $caixa)
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $anexoName = time() . '_' . $file->getClientOriginalName();
                $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

                ModulosAnexo::create([
                    'anexavel_id'   => $caixa->id,                   // ID da transacao_financeira
                    'anexavel_type' => TransacaoFinanceira::class,   // caminho da classe do Model
                    'nome_arquivo'  => $anexoName,
                    'caminho_arquivo' => $anexoPath,
                    'tamanho_arquivo' => $file->getSize(),
                    'tipo_arquivo'  => $file->getMimeType() ?? '',  // se quiser
                    'created_by'    => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    // etc., se tiver mais campos
                ]);
            }
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


    /**
     * Retorna os dados em formato JSON para DataTables.
     */
    public function getData(Request $request)
    {
        // Monta a query base. Se precisar de Eager Loading, faça ->with('entidadeFinanceira','lancamentoPadrao',...)
        $query = TransacaoFinanceira::with([
            'entidadeFinanceira',
            'lancamentoPadrao'
        ]);

        // Transforma em DataTable
        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                // Exemplo: se quiser alguma coluna de ação
                return '<a href="#" class="btn btn-sm btn-primary">Editar</a>';
            })
            ->addColumn('entidade_nome', function ($row) {
                // Exemplo para acessar $row->entidadeFinanceira->nome de forma segura
                return optional($row->entidadeFinanceira)->nome ?? '-';
            })
            ->editColumn('comprovacao_fiscal', function ($row) {
                // Renderiza o ícone igual ao Blade
                if ($row->comprovacao_fiscal === 1) {
                    return '<i class="fas fa-check-circle text-success" title="Tem comprovação Fiscal"></i>';
                } else {
                    return '<i class="bi bi-x-circle-fill text-danger" title="Não tem comprovação fiscal"></i>';
                }
            })
            ->editColumn('lancamentoPadrao.description', function ($row) {
                return optional($row->lancamentoPadrao)->description ?? '-';
            })
            ->editColumn('lancamentoPadrao.category', function ($row) {
                return optional($row->lancamentoPadrao)->category ?? '-';
            })
            ->editColumn('data_competencia', function ($row) {
                return optional($row->data_competencia)->format('d M, Y') ?? '-';
            })
            ->editColumn('tipo', function ($row) {
                // Similar à logic de badge
                if ($row->tipo === 'entrada') {
                    return '<span class="badge badge-light-success">Entrada</span>';
                } else {
                    return '<span class="badge badge-light-danger">Saída</span>';
                }
            })
            ->editColumn('valor', function ($row) {
                // Formata valor
                return 'R$ ' . number_format($row->valor, 2, ',', '.');
            })
            ->rawColumns(['comprovacao_fiscal', 'tipo', 'action']) // Indica quais colunas podem ter HTML
            ->make(true);
    }

    public function grafico(Request $request)
    {
        // Obtém o mês e ano selecionado ou usa o mês atual como padrão
        $mesSelecionado = $request->input('mes', Carbon::now()->month);
        $anoSelecionado = $request->input('ano', Carbon::now()->year);

        // Obtém a quantidade de dias no mês selecionado
        $diasNoMes = Carbon::create($anoSelecionado, $mesSelecionado, 1)->daysInMonth;

        // Inicializa arrays para armazenar os dados do gráfico
        $dias = [];
        $recebimentos = [];
        $pagamentos = [];
        $transfEntrada = [];
        $transfSaida = [];
        $saldo = [];

        // Busca todas as transações do mês selecionado
        $transacoes = TransacaoFinanceira::whereYear('data_competencia', $anoSelecionado)
            ->whereMonth('data_competencia', $mesSelecionado)
            ->orderBy('data_competencia')
            ->get();

        // Variável para armazenar o saldo acumulado
        $saldoAcumulado = 0;

        // Loop para preencher os dados do gráfico para cada dia do mês
        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $dataLoop = Carbon::create($anoSelecionado, $mesSelecionado, $dia)->format('Y-m-d');

            // Filtra as transações do dia
            $transacoesDia = $transacoes->filter(fn($t) => $t->data_competencia->format('Y-m-d') === $dataLoop);

            // Calcula os totais de cada tipo de transação no dia
            $valorRecebimentos = $transacoesDia->where('tipo', 'entrada')->sum('valor');
            $valorPagamentos = $transacoesDia->where('tipo', 'saida')->sum('valor');
            $valorTransfEnt = $transacoesDia->where('tipo', 'transfer_in')->sum('valor');
            $valorTransfSai = $transacoesDia->where('tipo', 'transfer_out')->sum('valor');

            // Atualiza o saldo acumulado
            $saldoAcumulado += ($valorRecebimentos + $valorTransfEnt) - ($valorPagamentos + $valorTransfSai);

            // Adiciona os valores ao array
            $dias[] = $dia;
            $recebimentos[] = (float) $valorRecebimentos;
            $pagamentos[] = (float) $valorPagamentos;
            $transfEntrada[] = (float) $valorTransfEnt;
            $transfSaida[] = (float) $valorTransfSai;
            $saldo[] = (float) $saldoAcumulado;
        }

        // Retorna para a view com os dados
        return view('financeiro.graficos.mensal', compact(
            'dias',
            'recebimentos',
            'pagamentos',
            'transfEntrada',
            'transfSaida',
            'saldo',
            'mesSelecionado',
            'anoSelecionado'
        ));
    }
}
