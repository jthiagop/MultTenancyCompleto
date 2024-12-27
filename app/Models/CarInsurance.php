<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarInsurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'car_insurance'; // Nome da tabela no banco de dados

    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'ano',
        'chassi',
        'renavam',
        'cor',
        'combustivel',
        'capacidade_passageiros',
        'capacidade_carga',
        'status',
        'paroquia',
        'cidade',
        'foranias',
        'seguradora',
        'vencimento',
        'valor_seguro',
        'franquia',
        'cobertura',
        'numero_apolice',
        'data_renovacao',
        'responsavel',
        'telefone',
        'cpf',
        'documentos',
        'cnpj_diocese',
        'tem_apolice',
        'historico_sinistros',
        'km_atual',
        'ultima_edicao_por',
        'endosso',
        'informacoes',
        'observacao',
        'codigo_rastreamento',
        'vendido',
        'data_venda',
        'valor_venda',
        'company_id',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];

    protected $dates = ['deleted_at', 'vencimento', 'data_renovacao', 'data_venda'];

    public function seguros()
{
    return $this->hasMany(Seguro::class);
}

public function sinistros()
{
    return $this->hasMany(Sinistro::class);
}

public function documentos()
{
    return $this->hasMany(Documento::class);
}

public function vendas()
{
    return $this->hasOne(Venda::class);
}

}
