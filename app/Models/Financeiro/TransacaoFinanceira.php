<?php

namespace App\Models\Financeiro;

use App\Models\EntidadeFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TransacaoFinanceira extends Model
{
    /** @use HasFactory<\Database\Factories\TransacaoFinanceiraFactory> */

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
            'centro',
            'tipo_documento',
            'numero_documento',
            'origem',
            'historico_complementar',
            'comprovacao_fiscal',
            'created_by',
            'updated_by',
        ];

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
    }
