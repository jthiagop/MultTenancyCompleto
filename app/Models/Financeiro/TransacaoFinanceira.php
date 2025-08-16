<?php

namespace App\Models\Financeiro;

use App\Models\Anexo;
use App\Models\EntidadeFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TransacaoFinanceira extends Model
{
    /** @use HasFactory<\Database\Factories\TransacaoFinanceiraFactory> */
    protected $table = 'transacoes_financeiras';

        use HasFactory, SoftDeletes;

        protected $fillable = [
            'company_id',
            'data_competencia',
            'entidade_id',
            'tipo',
            'valor',
            'descricao',
            'lancamento_padrao_id',
            'movimentacao_id',
            'cost_center_id',
            'tipo_documento',
            'numero_documento',
            'origem',
            'historico_complementar',
            'comprovacao_fiscal',
            'created_by',
            'updated_by',
            'created_by_name',
            'updated_by_name',
        ];

        // Tabela Pivot
        public function bankStatements()
        {
            return $this->belongsToMany(
                BankStatement::class,
                'bank_statement_transacao',
                'transacao_financeira_id',
                'bank_statement_id'
            )
            ->withPivot('valor_conciliado', 'status_conciliacao')
            ->withTimestamps();
        }

        public function recibo()
        {
            return $this->hasOne(Recibo::class, 'transacao_id');
        }

        public function costCenter()
        {
            return $this->belongsTo(CostCenter::class, 'cost_center_id');
        }

        public function transacoesFinanceiras()
        {
            return $this->hasMany(TransacaoFinanceira::class, 'entidade_id');
        }

        public function lancamentoPadrao()
        {
            return $this->belongsTo(LancamentoPadrao::class, 'lancamento_padrao_id');
        }

        public function entidadeFinanceira()
        {
            return $this->belongsTo(EntidadeFinanceira::class, 'entidade_id');
        }

        public function movimentacao()
        {
            return $this->belongsTo(Movimentacao::class, 'movimentacao_id');
        }

        public function createdBy()
        {
            return $this->belongsTo(User::class, 'created_by');
        }

        public function updatedBy()
        {
            return $this->belongsTo(User::class, 'updated_by');
        }

        public function modulos_anexos()
        {
            return $this->hasMany(ModulosAnexo::class, 'anexavel_id');
        }

        static public function getChartSaida()
{
    $userId = Auth::user()->id; // Recupere o ID do usuário logado
    $currentYear = Carbon::now()->year;
    $currentMonth = Carbon::now()->month;

    // Consulta principal na tabela transacoes_financeiras
    $saidas = DB::table('transacoes_financeiras')
        ->join('company_user', 'transacoes_financeiras.company_id', '=', 'company_user.company_id')
        ->where('company_user.user_id', $userId)
        ->where('transacoes_financeiras.tipo', 'S') // Filtra apenas as saídas (S para saída, E para entrada)
        ->whereYear('transacoes_financeiras.data_competencia', $currentYear) // Filtra pelo ano vigente
        ->whereMonth('transacoes_financeiras.data_competencia', $currentMonth) // Filtra pelo mês vigente
        ->whereNull('transacoes_financeiras.deleted_at') // Ignora registros excluídos (Soft Delete)
        ->select('transacoes_financeiras.*', 'transacoes_financeiras.origem')
        ->get();

    // Separar entre Banco e Caixa
    $bancoSaidas = $saidas->where('origem', 'Banco')->sum('valor');
    $caixaSaidas = $saidas->where('origem', 'Caixa')->sum('valor');

    // Retornar os valores em um array associativo
    return [
        'banco' => $bancoSaidas,
        'caixa' => $caixaSaidas,
        'total' => $bancoSaidas + $caixaSaidas
    ];
}
    }
